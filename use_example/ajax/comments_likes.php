<?php
define("PULL_AJAX_INIT", true);
define("PUBLIC_AJAX_MODE", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);

// Отключаем вывод ошибок
error_reporting(0);
ini_set('display_errors', 0);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Устанавливаем временную зону для PHP
date_default_timezone_set('Europe/Moscow');

// Очищаем буферы
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

// Функция для логирования (только в файл, не в вывод)
function addDebugLog($message)
{
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/like_debug.log';
    $log = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    file_put_contents($logFile, $log, FILE_APPEND);
}




// ========== ФУНКЦИИ ДЛЯ ОБРАБОТКИ ИЗОБРАЖЕНИЙ В КОММЕНТАРИЯХ ==========



/**
 * Сохранение base64 изображения для комментария
 * @param string $dataUrl Base64 data URL
 * @param string $commentId ID комментария
 * @return string|false Локальный путь или false
 */
function saveBase64ImageForComment($dataUrl, $commentId)
{
    // Создаем директорию
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/images/comments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Разбираем data URL
    if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $type)) {
        $imageData = substr($dataUrl, strpos($dataUrl, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return false;
        }

        $imageData = base64_decode($imageData);
        if ($imageData === false) {
            return false;
        }
    } else {
        return false;
    }

    // Генерируем уникальное имя
    $filename = 'comment_' . $commentId . '_' . time() . '_' . md5($dataUrl) . '.' . $type;
    $localPath = $uploadDir . $filename;
    $webPath = '/upload/images/comments/' . $filename;

    // Сохраняем файл
    if (file_put_contents($localPath, $imageData)) {
        return $webPath;
    }

    return false;
}




/**
 * Обработка изображений в комментарии
 * @param string $content HTML контент
 * @param string $commentId ID комментария
 * @return string Обработанный контент
 */
function processImagesInComment($content, $commentId)
{
    if (empty($content)) return $content;

    $content = preg_replace('/<b\b[^>]*>/i', '<span style="font-weight:bold;">', $content);
    $content = preg_replace('/<\/b>/i', '</span>', $content);

    $pattern = '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i';

    $content = preg_replace_callback($pattern, function ($matches) use ($commentId) {
        $originalUrl = $matches[1];

        // Если уже локальный путь - пропускаем
        if (strpos($originalUrl, '/upload/') === 0) {
            return $matches[0];
        }

        // Если это base64 - сохраняем на сервер
        if (strpos($originalUrl, 'data:image') === 0) {
            $localPath = saveBase64ImageForComment($originalUrl, $commentId);
            if ($localPath) {
                return str_replace($originalUrl, $localPath, $matches[0]);
            }
            return $matches[0];
        }

        // Если это внешний URL - скачиваем
        $localPath = downloadImageForComment($originalUrl, $commentId);
        if ($localPath) {
            return str_replace($originalUrl, $localPath, $matches[0]);
        }

        return $matches[0];
    }, $content);

    return $content;
}

/**
 * Скачивание изображения для комментария
 * @param string $url URL изображения
 * @param string $commentId ID комментария
 * @return string|false Локальный путь или false
 */
function downloadImageForComment($url, $commentId)
{
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/images/comments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $parsedUrl = parse_url($url);
    $path = $parsedUrl['path'] ?? '';
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    if (empty($extension)) {
        $extension = 'jpg';
    }

    $filename = 'comment_' . $commentId . '_' . time() . '_' . md5($url) . '.' . $extension;
    $localPath = $uploadDir . $filename;
    $webPath = '/upload/images/comments/' . $filename;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $imageData !== false) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);

        if (strpos($mimeType, 'image/') === 0) {
            file_put_contents($localPath, $imageData);
            return $webPath;
        }
    }

    return false;
}

// ========== КОНЕЦ ФУНКЦИЙ ДЛЯ ОБРАБОТКИ ИЗОБРАЖЕНИЙ ==========

header('Content-Type: application/json; charset=' . LANG_CHARSET);

$response = ['success' => false, 'error' => 'Неизвестная ошибка'];

try {
    if (!Loader::includeModule('pull')) {
        throw new Exception('PULL_MODULE_IS_NOT_INSTALLED');
    }

    if (intval($GLOBALS['USER']->GetID()) <= 0) {
        throw new Exception('AUTHORIZE_ERROR');
    }

    if (!check_bitrix_sessid()) {
        throw new Exception('SESSION_ERROR');
    }

    Loader::includeModule('iblock');

    $request = $_POST;
    $action = $request['action'] ?? '';
    $elementId = intval($request['element_id'] ?? 0);
    $postId = intval($request['post_id'] ?? 0);
    $userId = $GLOBALS['USER']->GetID();
    $iblockId = intval($request['iblock_id'] ?? 0);

    // Уникальный ключ для поста (iblock_id + post_id)
    $uniqueKey = $iblockId . '_' . ($postId ?: $elementId);

    $connection = Application::getConnection();

    // Устанавливаем временную зону для MySQL сессии
    /* $connection->queryExecute("SET time_zone = '+03:00'"); */

    // Функция для получения JSON значения свойства
    function getPropertyJson($elementId, $propCode, $iblockId)
    {
        if (!$elementId || !$iblockId) return [];

        $rs = CIBlockElement::GetProperty($iblockId, $elementId, [], ['CODE' => $propCode]);
        while ($ar = $rs->Fetch()) {
            if (!empty($ar['VALUE'])) {
                $decoded = json_decode($ar['VALUE'], true);
                if (is_array($decoded)) {
                    return $decoded;
                }
                return [];
            }
        }
        return [];
    }

    // Функция для сохранения JSON значения свойства
    function setPropertyJson($elementId, $propCode, $value, $iblockId)
    {
        if (!$elementId || !$iblockId) return false;

        $jsonValue = json_encode($value, JSON_UNESCAPED_UNICODE);

        // Очищаем старое значение
        CIBlockElement::SetPropertyValuesEx($elementId, $iblockId, [$propCode => false]);

        // Устанавливаем новое значение
        CIBlockElement::SetPropertyValuesEx($elementId, $iblockId, [$propCode => $jsonValue]);

        return true;
    }

    if ($action === 'get_comments') {
        $targetId = $postId ? $postId : $elementId;

        if (!$targetId) {
            throw new Exception('Не передан ID поста');
        }

        if (!$iblockId) {
            throw new Exception('Не передан ID инфоблока');
        }

        // Используем уникальный ключ для таблицы
        $tableExists = $connection->isTableExists('comments');

        if ($tableExists) {
            $sql = "SELECT id, iblock_id, id_post, author_id, author_name, avatar, content, 
                    DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') as created_at 
                    FROM comments 
                    WHERE iblock_id = " . intval($iblockId) . " AND id_post = " . intval($targetId) . " AND status = 1 
                    ORDER BY created_at ASC";
            $result = $connection->query($sql);
            $comments = [];
            while ($row = $result->fetch()) {
                $comments[] = $row;
            }
            $response = ['success' => true, 'comments' => $comments];
        } else {
            $comments = getPropertyJson($targetId, 'COMMENTS', $iblockId);
            if (!is_array($comments)) {
                $comments = [];
            }
            usort($comments, function ($a, $b) {
                $dateA = $a['date'] ?? $a['created_at'] ?? '';
                $dateB = $b['date'] ?? $b['created_at'] ?? '';
                return strtotime($dateA) - strtotime($dateB);
            });
            $response = ['success' => true, 'comments' => $comments];
        }
    } elseif ($action === 'add_comment') {
        $targetId = $postId ? $postId : $elementId;

        if (!$targetId) {
            throw new Exception('Не передан ID поста');
        }

        if (!$iblockId) {
            throw new Exception('Не передан ID инфоблока');
        }

        $content = trim($request['text'] ?? '');
        if (empty($content) || $content === '<br>') {
            throw new Exception('Комментарий не может быть пустым');
        }


        // ========== ОБРАБОТКА ИЗОБРАЖЕНИЙ В КОММЕНТАРИИ ==========
        $tempId = time() . '_' . $userId;
        $content = processImagesInComment($content, $tempId);
        // ========== КОНЕЦ ОБРАБОТКИ ==========

        $user = CUser::GetByID($userId)->Fetch();
        $authorName = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
        if (empty($authorName)) {
            $authorName = $user['LOGIN'];
        }

        $avatar = '';
        if ($user['PERSONAL_PHOTO']) {
            $avatarFile = CFile::ResizeImageGet($user['PERSONAL_PHOTO'], ['width' => 32, 'height' => 32], BX_RESIZE_IMAGE_EXACT, true);
            $avatar = $avatarFile['src'];
        }

        $content = strip_tags($content, '<p><br><b><strong><i><em><u><ul><ol><li><a><img><span><div>');

        $tableExists = $connection->isTableExists('comments');
        $newId = time();
        $createdAt = date('d.m.Y H:i');



        // ========== ОБРАБОТКА ПРИКРЕПЛЕННЫХ ФАЙЛОВ В КОММЕНТАРИИ ==========

        // Создаем директорию для файлов комментариев
        $commentUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/comments/' . $targetId . '/';
        if (!is_dir($commentUploadDir)) {
            mkdir($commentUploadDir, 0777, true);
        }

        $uploadedFiles = array();

        // Обработка загруженных файлов
        if (isset($_FILES['COMMENT_FILE']) && is_array($_FILES['COMMENT_FILE']['name'])) {
            // Проверяем количество файлов
            $filesCount = count($_FILES['COMMENT_FILE']['name']);
            if ($filesCount > 10) {
                throw new Exception('Максимум 10 файлов на комментарий');
            }

            for ($i = 0; $i < $filesCount; $i++) {
                if ($_FILES['COMMENT_FILE']['error'][$i] == 0 && !empty($_FILES['COMMENT_FILE']['name'][$i])) {
                    $fileName = $_FILES['COMMENT_FILE']['name'][$i];
                    $fileSize = $_FILES['COMMENT_FILE']['size'][$i];
                    $fileTmp = $_FILES['COMMENT_FILE']['tmp_name'][$i];
                    $fileType = $_FILES['COMMENT_FILE']['type'][$i];

                    // Проверяем расширение
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExtensions)) {
                        throw new Exception("Файл \"{$fileName}\" имеет неподдерживаемое расширение");
                    }

                    // Проверяем размер (20 MB)
                    if ($fileSize > 20 * 1024 * 1024) {
                        throw new Exception("Файл \"{$fileName}\" превышает максимальный размер (20 МБ)");
                    }

                    // Генерируем уникальное имя
                    $newFileName = time() . '_' . md5($fileName . $userId) . '.' . $ext;
                    $filePath = $commentUploadDir . $newFileName;
                    $webPath = '/upload/comments/' . $targetId . '/' . $newFileName;

                    // Перемещаем файл
                    if (move_uploaded_file($fileTmp, $filePath)) {
                        // Убираем автоматическую вставку изображений в контент!
                        // Изображения больше НЕ добавляются автоматически в текст комментария
                        // Пользователь сам вставляет их через кнопку "В текст"

                        $uploadedFiles[] = array(
                            'name' => $fileName,
                            'size' => $fileSize,
                            'path' => $webPath,
                            'type' => $fileType
                        );
                    }
                }
            }
        }

        // Сохраняем информацию о файлах в комментарий (опционально)
        $fileData = !empty($uploadedFiles) ? json_encode($uploadedFiles, JSON_UNESCAPED_UNICODE) : '';

        // ========== КОНЕЦ ОБРАБОТКИ ФАЙЛОВ ==========




        if ($tableExists) {
            $sql = "INSERT INTO comments (iblock_id, id_post, author_id, author_name, avatar, content, created_at, status) 
                    VALUES (" . intval($iblockId) . ", " . intval($targetId) . ", " . intval($userId) . ", 
                    '" . $connection->getSqlHelper()->forSql($authorName) . "', 
                    '" . $connection->getSqlHelper()->forSql($avatar) . "', 
                    '" . $connection->getSqlHelper()->forSql($content) . "', 
                    NOW(), 1)";
            $connection->queryExecute($sql);
            $newId = $connection->getInsertedId();
        } else {
            $comments = getPropertyJson($targetId, 'COMMENTS', $iblockId);
            if (!is_array($comments)) {
                $comments = [];
            }

            $newComment = [
                'id' => uniqid(),
                'iblock_id' => $iblockId,
                'id_post' => $targetId,
                'author_id' => $userId,
                'author_name' => $authorName,
                'avatar' => $avatar,
                'content' => $content,
                'created_at' => $createdAt,
                'date' => date('c'),
                'date_formatted' => $createdAt
            ];

            $comments[] = $newComment;
            setPropertyJson($targetId, 'COMMENTS', $comments, $iblockId);
        }

        $response = [
            'success' => true,
            'message' => 'Комментарий добавлен',
            'comment' => [
                'id' => $newId,
                'iblock_id' => $iblockId,
                'id_post' => $targetId,
                'author_id' => $userId,
                'author_name' => $authorName,
                'avatar' => $avatar,
                'content' => $content,
                'created_at' => $createdAt
            ]
        ];

        // Уникальный канал для Push-уведомлений (iblock_id + post_id)
        $pushChannel = 'COMMENTS_' . $iblockId . '_' . $targetId;

        // Отправляем Push-уведомление
        CPullWatch::AddToStack($pushChannel, [
            'module_id' => 'comments',
            'command' => 'new_comment',
            'params' => [
                'iblock_id' => $iblockId,
                'post_id' => $targetId,
                'comment' => [
                    'id' => $newId,
                    'author_id' => $userId,
                    'author_name' => $authorName,
                    'avatar' => $avatar,
                    'content' => $content,
                    'created_at' => $createdAt
                ]
            ]
        ]);
    } elseif ($action === 'delete_comment') {
        $commentId = intval($request['comment_id'] ?? 0);
        $targetId = $postId ? $postId : $elementId;

        if (!$commentId) {
            throw new Exception('Не передан ID комментария');
        }

        if (!$GLOBALS['USER']->IsAdmin()) {
            throw new Exception('Доступ запрещен');
        }

        $tableExists = $connection->isTableExists('comments');

        if ($tableExists) {
            $sql = "UPDATE comments SET status = 0 WHERE id = " . intval($commentId);
            $connection->queryExecute($sql);
            $response = ['success' => true, 'message' => 'Комментарий удален'];

            // Уникальный канал для Push-уведомлений
            $pushChannel = 'COMMENTS_' . $iblockId . '_' . $targetId;

            // Отправляем Push-уведомление об удалении
            CPullWatch::AddToStack($pushChannel, [
                'module_id' => 'comments',
                'command' => 'delete_comment',
                'params' => [
                    'iblock_id' => $iblockId,
                    'post_id' => $targetId,
                    'comment_id' => $commentId
                ]
            ]);

            addDebugLog("Push delete_comment sent to channel: {$pushChannel}, comment_id: {$commentId}");
        } else {
            $comments = getPropertyJson($targetId, 'COMMENTS', $iblockId);
            if (is_array($comments)) {
                $comments = array_filter($comments, function ($comment) use ($commentId) {
                    return $comment['id'] != $commentId;
                });
                $comments = array_values($comments);
                setPropertyJson($targetId, 'COMMENTS', $comments, $iblockId);
            }
            $response = ['success' => true, 'message' => 'Комментарий удален'];
        }
    } elseif ($action == 'like') {
        if (!$elementId) {
            throw new Exception('Не передан ID элемента');
        }

        if (!$iblockId) {
            throw new Exception('Не передан ID инфоблока');
        }

        // Получаем текущие лайки
        $likes = getPropertyJson($elementId, 'LIKES', $iblockId);
        if (!is_array($likes)) {
            $likes = [];
        }

        // Проверяем, есть ли уже лайк от этого пользователя
        $userLikedIndex = array_search($userId, $likes);
        $wasLiked = ($userLikedIndex !== false);

        if ($wasLiked) {
            // Удаляем лайк
            unset($likes[$userLikedIndex]);
            $likes = array_values($likes);
            $actionText = 'unliked';
            $userLiked = false;
        } else {
            // Добавляем лайк
            $likes[] = $userId;
            $actionText = 'liked';
            $userLiked = true;
        }

        // Сохраняем обновленный массив лайков
        $saveResult = setPropertyJson($elementId, 'LIKES', $likes, $iblockId);

        $response = [
            'success' => true,
            'action' => $actionText,
            'count' => count($likes),
            'user_liked' => $userLiked
        ];

        // Уникальный канал для лайков
        $pushChannel = 'LIKES_' . $iblockId . '_' . $elementId;

        // Отправляем Push-уведомление об обновлении лайков
        CPullWatch::AddToStack($pushChannel, [
            'module_id' => 'comments',
            'command' => 'update_likes',
            'params' => [
                'iblock_id' => $iblockId,
                'post_id' => $elementId,
                'element_id' => $elementId,
                'count' => count($likes),
                'action' => $actionText,
                'user_liked' => $userLiked
            ]
        ]);
    } elseif ($action == 'get_likes') {
        if (!$elementId) {
            throw new Exception('Не передан ID элемента');
        }

        if (!$iblockId) {
            throw new Exception('Не передан ID инфоблока');
        }

        $likes = getPropertyJson($elementId, 'LIKES', $iblockId);
        if (!is_array($likes)) {
            $likes = [];
        }
        $response = [
            'success' => true,
            'count' => count($likes),
            'liked' => in_array($userId, $likes)
        ];
    } elseif ($action === 'subscribe') {
        $targetId = $postId ? $postId : $elementId;

        if (!$targetId) {
            throw new Exception('Не передан ID поста');
        }

        if (!$iblockId) {
            throw new Exception('Не передан ID инфоблока');
        }

        // Уникальный канал для подписки
        $pushChannel = 'COMMENTS_' . $iblockId . '_' . $targetId;

        // Подписываем пользователя на события
        CPullWatch::Add($userId, $pushChannel);
        $response = ['success' => true, 'message' => 'Subscribed to ' . $pushChannel];
    } elseif ($action === 'subscribe_likes') {
        $targetId = $postId ?: $elementId;

        if (!$targetId) {
            throw new Exception('Не передан ID поста');
        }

        if (!$iblockId) {
            throw new Exception('Не передан ID инфоблока');
        }

        $likesChannel = 'LIKES_' . $iblockId . '_' . $targetId;

        // Подписываем пользователя на канал лайков
        CPullWatch::Add($userId, $likesChannel);

        $response = [
            'success' => true,
            'message' => 'Subscribed to likes channel',
            'channel' => $likesChannel
        ];
    } else {
        throw new Exception('Неизвестное действие: ' . $action);
    }
} catch (Exception $e) {
    $response = ['success' => false, 'error' => $e->getMessage()];
}

// Очищаем буфер и выводим только JSON
ob_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
die();

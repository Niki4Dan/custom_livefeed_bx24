<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();





// ==================== ДОБАВЛЯЕМ ДАННЫЕ О ПОЛЬЗОВАТЕЛЯХ (АВАТАР, ФИО) ====================
$userIds = array();
foreach ($arResult["ITEMS"] as $arItem) {
    if ($arItem["CREATED_BY"] > 0) {
        $userIds[] = $arItem["CREATED_BY"];
    }

    // ДОБАВЛЯЕМ: получаем ID получателей из свойства RECIPIENTS
    if (isset($arItem['PROPERTIES']['RECIPIENTS']['VALUE']) && is_array($arItem['PROPERTIES']['RECIPIENTS']['VALUE'])) {
        foreach ($arItem['PROPERTIES']['RECIPIENTS']['VALUE'] as $recipientId) {
            if ($recipientId > 0) {
                $userIds[] = intval($recipientId);
            }
        }
    }
}

$arResult["USERS"] = array();
if (!empty($userIds)) {
    $userIds = array_unique($userIds);
    $userRes = CUser::GetList('ID', 'ASC', array('ID' => implode('|', $userIds)));
    while ($user = $userRes->Fetch()) {
        $user['FULL_NAME'] = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
        if (empty($user['FULL_NAME'])) {
            $user['FULL_NAME'] = 'Пользователь';
        }

        if ($user['PERSONAL_PHOTO'] > 0) {
            $avatarFile = CFile::ResizeImageGet(
                $user['PERSONAL_PHOTO'],
                array('width' => 44, 'height' => 44),
                BX_RESIZE_IMAGE_EXACT,
                true
            );
            $user['AVATAR'] = $avatarFile['src'];
        } else {
            $user['AVATAR'] = '';
        }

        $user['PROFILE_URL'] = '/company/personal/user/' . $user['ID'] . '/';
        $arResult["USERS"][$user['ID']] = $user;
    }
} 


// Функция склонения числительных
function declensionNum($num, $forms)
{
    $num = abs($num) % 100;
    $num1 = $num % 10;
    if ($num > 10 && $num < 20) return $forms[2];
    if ($num1 > 1 && $num1 < 5) return $forms[1];
    if ($num1 == 1) return $forms[0];
    return $forms[2];
}

// Переименованная функция форматирования даты (было formatDate, стало formatDateCustom)
function formatDateCustom($timestamp)
{
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'Только что';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' ' . declensionNum($minutes, array('минуту', 'минуты', 'минут')) . ' назад';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' ' . declensionNum($hours, array('час', 'часа', 'часов')) . ' назад';
    } elseif ($diff < 172800) {
        return 'Вчера';
    } else {
        return date('d.m.Y', $timestamp);
    }
}

foreach ($arResult["ITEMS"] as &$arItem) {
    $userId = $arItem["CREATED_BY"];
    if ($userId > 0 && isset($arResult["USERS"][$userId])) {
        $arItem["USER"] = $arResult["USERS"][$userId];
    } else {
        $arItem["USER"] = array(
            'ID' => 0,
            'FULL_NAME' => 'Аноним',
            'AVATAR' => '',
            'PROFILE_URL' => '#',
        );
    }

    // ========== ФОРМАТИРОВАНИЕ ДАТЫ ==========
    // Сначала проверяем DATE_ACTIVE_FROM (дата начала активности)
    if (!empty($arItem["DATE_ACTIVE_FROM"])) {
        $timestamp = MakeTimeStamp($arItem["DATE_ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS");
        if (!$timestamp || $timestamp <= 0) {
            $timestamp = MakeTimeStamp($arItem["DATE_ACTIVE_FROM"]);
        }
        if ($timestamp && $timestamp > 0) {
            $arItem["DATE_FORMATTED"] = formatDateCustom($timestamp);
        } else {
            $arItem["DATE_FORMATTED"] = $arItem["DATE_ACTIVE_FROM"];
        }
    }
    // Если DATE_ACTIVE_FROM нет, используем DATE_CREATE
    elseif (!empty($arItem["DATE_CREATE"])) {
        $timestamp = MakeTimeStamp($arItem["DATE_CREATE"], "DD.MM.YYYY HH:MI:SS");
        if (!$timestamp || $timestamp <= 0) {
            $timestamp = MakeTimeStamp($arItem["DATE_CREATE"]);
        }
        if ($timestamp && $timestamp > 0) {
            $arItem["DATE_FORMATTED"] = formatDateCustom($timestamp);
        } else {
            $arItem["DATE_FORMATTED"] = $arItem["DATE_CREATE"];
        }
    }
    // Если ничего нет, используем DISPLAY_ACTIVE_FROM
    else {
        $arItem["DATE_FORMATTED"] = $arItem["DISPLAY_ACTIVE_FROM"] ?: '';
    }
    // ========== КОНЕЦ ФОРМАТИРОВАНИЯ ==========
}
unset($arItem);

// Обрабатываем PREVIEW_TEXT для встраиваемых изображений, чтобы включить просмотрщик
foreach ($arResult["ITEMS"] as &$arItem) {
    if (!empty($arItem["PREVIEW_TEXT"])) {
        $arItem["PREVIEW_TEXT"] = preg_replace_callback(
            '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i',
            function ($matches) use ($arItem) {
                $imgTag = $matches[0];
                $imgSrc = $matches[1];
                $postId = $arItem['ID'];

                // Оборачиваем изображение только если это не data URI (base64)
                if (strpos($imgSrc, 'data:image') === 0) {
                    return $imgTag; // Не оборачиваем base64 изображения, они уже встроены
                }
                // Оборачиваем изображение тегом <a> для работы просмотрщика
                return '<a href="' . htmlspecialchars($imgSrc) . '" data-viewer data-viewer-group-by="post-inline-images-' . $postId . '" target="_blank">' . $imgTag . '</a>';
                return '<a href="' . htmlspecialchars($imgSrc) . '" data-viewer data-viewer-group-by="post-inline-images-' . $postId . '">' . $imgTag . '</a>';
            },
            $arItem["PREVIEW_TEXT"]
        );
    }
}
unset($arItem); // Снимаем ссылку после цикла

// ==================== ПОЛУЧАЕМ ЛАЙКИ И КОММЕНТАРИИ ====================
foreach ($arResult["ITEMS"] as &$arItem) {
    // Получаем лайки (JSON строка)
    $likes = array();
    $res = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arItem["ID"], array(), array('CODE' => 'LIKES'));
    while ($prop = $res->Fetch()) {
        if ($prop['VALUE']) {
            $decoded = json_decode($prop['VALUE'], true);
            if (is_array($decoded)) {
                $likes = $decoded;
            }
            break;
        }
    }
    $arItem["LIKES_COUNT"] = count($likes);
    $arItem["USER_LIKED"] = in_array($GLOBALS['USER']->GetID(), $likes);

    // Получаем комментарии (JSON строка)
    $comments = array();
    $res = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arItem["ID"], array(), array('CODE' => 'COMMENTS'));
    while ($prop = $res->Fetch()) {
        if ($prop['VALUE']) {
            $decoded = json_decode($prop['VALUE'], true);
            if (is_array($decoded)) {
                $comments = $decoded;
            }
            break;
        }
    }
    $arItem["COMMENTS_COUNT"] = count($comments);
}
unset($arItem);
?>

<?php
// Подключаем JS-библиотеку для просмотра изображений, если есть элементы с изображениями
CJSCore::Init(array('viewer'));
?>
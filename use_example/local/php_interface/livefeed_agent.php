<?php
// /local/livefeed_agent.php

// Подключаем конфигурационный файл
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/info/conf.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/info/conf.php';
}

// Если переменная не определена в конфиге, устанавливаем значение по умолчанию


// Подключаем модули
if (!CModule::IncludeModule("iblock")) {
    return;
}
if (!CModule::IncludeModule("intranet")) {
    return;
}
if (!CModule::IncludeModule("im")) {
    return;
}

// Включаем отладку
$GLOBALS['NOTIFICATION_DEBUG'] = true;

function debug_log($message, $data = null)
{
    if ($GLOBALS['NOTIFICATION_DEBUG']) {
        $log = date('Y-m-d H:i:s') . " - " . $message;
        if ($data !== null) {
            $log .= ": " . print_r($data, true);
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/notification_debug.log', $log . "\n", FILE_APPEND);
    }
}

/**
 * Функция для получения всех активных пользователей
 */
function getAllActiveUsers()
{
    $users = array();

    $userQuery = CUser::GetList(
        "ID",
        "ASC",
        array("ACTIVE" => "Y"),
        array("FIELDS" => array("ID"))
    );

    while ($user = $userQuery->Fetch()) {
        $users[] = intval($user["ID"]);
    }

    return $users;
}

/**
 * Функция для получения получателей из свойства RECIPIENTS
 */
function getRecipientsFromElement($elementId, $iblockId, $authorId = null)
{
    global $DB;

    debug_log("getRecipientsFromElement called", array("elementId" => $elementId, "iblockId" => $iblockId));

    $recipients = array();
    $excluded = array();

    $elementId = intval($elementId);
    $iblockId = intval($iblockId);

    if ($elementId <= 0 || $iblockId <= 0) {
        return array();
    }

    // Получаем ID свойств через прямой запрос к БД
    $propRecipientsId = 0;
    $propNoRecipientsId = 0;

    $sql = "SELECT ID, CODE FROM b_iblock_property WHERE IBLOCK_ID = " . $iblockId . " AND CODE IN ('RECIPIENTS', 'NORECIPIENTS')";
    $res = $DB->Query($sql);
    while ($row = $res->Fetch()) {
        if ($row["CODE"] == "RECIPIENTS") {
            $propRecipientsId = intval($row["ID"]);
        }
        if ($row["CODE"] == "NORECIPIENTS") {
            $propNoRecipientsId = intval($row["ID"]);
        }
    }

    debug_log("Properties", array("RECIPIENTS_ID" => $propRecipientsId, "NORECIPIENTS_ID" => $propNoRecipientsId));

    // Получаем получателей
    $hasRecipients = false;
    if ($propRecipientsId > 0) {
        $sql = "SELECT VALUE FROM b_iblock_element_property 
                WHERE IBLOCK_PROPERTY_ID = " . $propRecipientsId . " 
                AND IBLOCK_ELEMENT_ID = " . $elementId;
        $res = $DB->Query($sql);
        while ($row = $res->Fetch()) {
            if (!empty($row["VALUE"]) && is_numeric($row["VALUE"])) {
                $recipients[] = intval($row["VALUE"]);
                $hasRecipients = true;
            }
        }
    }

    // Если получатели не указаны - берем всех активных пользователей
    if (!$hasRecipients) {
        $recipients = getAllActiveUsers();
        debug_log("No recipients specified, using all active users: " . count($recipients));
    }

    // Получаем исключенных
    if ($propNoRecipientsId > 0) {
        $sql = "SELECT VALUE FROM b_iblock_element_property 
                WHERE IBLOCK_PROPERTY_ID = " . $propNoRecipientsId . " 
                AND IBLOCK_ELEMENT_ID = " . $elementId;
        $res = $DB->Query($sql);
        while ($row = $res->Fetch()) {
            if (!empty($row["VALUE"]) && is_numeric($row["VALUE"])) {
                $excluded[] = intval($row["VALUE"]);
            }
        }
    }

    // Исключаем пользователей
    $finalRecipients = array_diff($recipients, $excluded);

    // Исключаем автора сообщения
    if ($authorId !== null && $authorId > 0) {
        $finalRecipients = array_diff($finalRecipients, array($authorId));
    }

    debug_log("Final recipients count: " . count($finalRecipients));

    return $finalRecipients;
}

/**
 * Увеличение счетчика для получателей
 */
function incrementDepartmentNewsCounter($recipients)
{
    global $enableNotifications;
    
    // Проверяем включены ли уведомления
    if (!$enableNotifications) {
        debug_log("Notifications are disabled, skipping counter increment");
        return;
    }
    
    if (empty($recipients)) return;

    debug_log("Incrementing counter for users", $recipients);

    foreach ($recipients as $userId) {
        CUserCounter::Increment(
            $userId,
            "department_news_counter",
            SITE_ID,
            true,
            1
        );
    }

    debug_log("Counter incremented for " . count($recipients) . " users");
}

/**
 * Функция отправки уведомления пользователю
 * @param int $userId ID получателя
 * @param string $message Текст сообщения
 * @param string $link Ссылка
 * @param int $fromUserId ID отправителя (автора поста)
 * @return bool
 */
function sendNotificationToUser($userId, $message, $link = '', $fromUserId = 1)
{
    global $enableNotifications;
    
    // Проверяем включены ли уведомления
    if (!$enableNotifications) {
        debug_log("Notifications are disabled, skipping send to user: $userId");
        return false;
    }
    
    $userId = intval($userId);
    if ($userId <= 0) {
        return false;
    }

    if (!CModule::IncludeModule("im")) {
        return false;
    }

    $messageText = $message;
    if (!empty($link)) {
        $messageText .= "<br><a href='" . htmlspecialchars($link) . "'>Перейти к сообщению</a>";
    }

    $messageFields = array(
        "TO_USER_ID" => $userId,
        "FROM_USER_ID" => $fromUserId,
        "NOTIFY_TYPE" => IM_NOTIFY_FROM,
        "NOTIFY_MODULE" => "iblock",
        "NOTIFY_EVENT" => "news_message",
        "NOTIFY_TAG" => "NEWS_MESSAGE_" . time() . "_" . $userId,
        "NOTIFY_MESSAGE" => $messageText,
        "NOTIFY_MESSAGE_OUT" => strip_tags($messageText)
    );

    $result = CIMNotify::Add($messageFields);

    if ($result) {
        debug_log("Notification sent to user: $userId from user: $fromUserId");
    } else {
        debug_log("Failed to send notification to user: $userId");
    }

    return $result > 0;
}

/**
 * Отправка уведомлений всем получателям
 * @param array $recipients Массив получателей
 * @param string $message Текст сообщения
 * @param string $link Ссылка
 * @param int $fromUserId ID отправителя (автора поста)
 * @return int
 */
function sendNotificationsToRecipients($recipients, $message, $link = '', $fromUserId = 1)
{
    global $enableNotifications;
    
    // Проверяем включены ли уведомления
    if (!$enableNotifications) {
        debug_log("Notifications are disabled, skipping sending to recipients");
        return 0;
    }
    
    if (empty($recipients) || !is_array($recipients)) {
        return 0;
    }

    $sentCount = 0;
    foreach ($recipients as $userId) {
        if (sendNotificationToUser($userId, $message, $link, $fromUserId)) {
            $sentCount++;
        }
    }

    return $sentCount;
}

/**
 * Агент для отправки уведомлений (возвращает пустую строку для удаления)
 */
function sendNotificationsForElement($elementId)
{
    global $iblockId, $enableNotifications;

    debug_log("=== sendNotificationsForElement called: elementId=$elementId ===");
    debug_log("Notifications enabled: " . ($enableNotifications ? 'YES' : 'NO'));

    if (!CModule::IncludeModule("iblock")) {
        return "sendNotificationsForElement(" . $elementId . ");";
    }

    $elementId = intval($elementId);
    if ($elementId <= 0) {
        return "";
    }

    // Получаем элемент
    $dbElement = CIBlockElement::GetList(
        array(),
        array("ID" => $elementId),
        false,
        false,
        array("ID", "IBLOCK_ID", "NAME", "CREATED_BY", "DATE_ACTIVE_FROM", "ACTIVE", "DETAIL_PAGE_URL")
    );

    if (!$arElement = $dbElement->Fetch()) {
        debug_log("Element not found: $elementId");
        return "";
    }

    debug_log("Element found", array(
        "NAME" => $arElement["NAME"],
        "ACTIVE" => $arElement["ACTIVE"],
        "DATE_ACTIVE_FROM" => $arElement["DATE_ACTIVE_FROM"],
        "CREATED_BY" => $arElement["CREATED_BY"]
    ));

    // Если элемент не активен - удаляем агент
    if ($arElement["ACTIVE"] != "Y") {
        debug_log("Element not active, removing agent");
        return "";
    }

    // Проверяем, что дата активности наступила или не указана
    $dateActiveFrom = $arElement["DATE_ACTIVE_FROM"];
    $shouldSend = true;

    if (!empty($dateActiveFrom)) {
        $timestamp = MakeTimeStamp($dateActiveFrom, "DD.MM.YYYY HH:MI:SS");
        if (!$timestamp) {
            $timestamp = MakeTimeStamp($dateActiveFrom, "YYYY-MM-DD HH:MI:SS");
        }

        $currentTime = time();

        if ($timestamp > $currentTime) {
            $shouldSend = false;
            debug_log("Future date, agent will run later");
            return "sendNotificationsForElement(" . $elementId . ");";
        }
    }

    if (!$shouldSend) {
        return "sendNotificationsForElement(" . $elementId . ");";
    }

    // Если уведомления отключены - просто логируем и выходим
    if (!$enableNotifications) {
        debug_log("Notifications are disabled, skipping all notifications for element $elementId");
        
        // Логируем пропуск отправки
        file_put_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/upload/notification_skipped.log',
            date('Y-m-d H:i:s') . " - Element ID={$elementId}, Name={$arElement['NAME']} - SKIPPED (notifications disabled)\n",
            FILE_APPEND
        );
        return "";
    }

    // Получаем получателей
    $recipients = getRecipientsFromElement($elementId, $arElement["IBLOCK_ID"], $arElement["CREATED_BY"]);

    if (empty($recipients)) {
        debug_log("No recipients found, removing agent");
        return "";
    }

    // Формируем ссылку
    $link = SITE_DIR . "info/index.php#post-" . $elementId;

    // Формируем сообщение с именем автора
    $authorName = getUserName($arElement["CREATED_BY"]);
    $message = "📢 Новое сообщение: " . $arElement["NAME"];

    // Увеличиваем счетчик
    incrementDepartmentNewsCounter($recipients);

    // Отправляем уведомления от имени автора поста
    $sentCount = sendNotificationsToRecipients($recipients, $message, $link, $arElement["CREATED_BY"]);

    debug_log("Notifications sent: $sentCount of " . count($recipients));

    // Логируем
    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/upload/notification_sent.log',
        date('Y-m-d H:i:s') . " - Element ID={$elementId}, Name={$arElement['NAME']}, Sent={$sentCount}, Total=" . count($recipients) . ", FromUser={$arElement['CREATED_BY']}\n",
        FILE_APPEND
    );

    return "";
}

/**
 * Функция получения имени пользователя по ID
 * @param int $userId ID пользователя
 * @return string
 */
function getUserName($userId)
{
    $userId = intval($userId);
    if ($userId <= 0) {
        return "Система";
    }
    
    $user = CUser::GetByID($userId)->Fetch();
    if ($user) {
        $name = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
        if (empty($name)) {
            $name = $user['LOGIN'];
        }
        return $name;
    }
    
    return "Пользователь";
}

/**
 * Агент для активации элемента при наступлении даты
 */
function activateElementOnDate($elementId)
{
    global $enableNotifications;
    
    debug_log("=== activateElementOnDate called: elementId=$elementId ===");

    if (!CModule::IncludeModule("iblock")) {
        return "activateElementOnDate(" . $elementId . ");";
    }

    $elementId = intval($elementId);
    if ($elementId <= 0) {
        return "";
    }

    // Получаем элемент
    $dbElement = CIBlockElement::GetList(
        array(),
        array("ID" => $elementId),
        false,
        false,
        array("ID", "IBLOCK_ID", "NAME", "CREATED_BY", "DATE_ACTIVE_FROM", "ACTIVE", "DETAIL_PAGE_URL")
    );

    if (!$arElement = $dbElement->Fetch()) {
        debug_log("Element not found: $elementId");
        return "";
    }

    debug_log("Element found", array(
        "NAME" => $arElement["NAME"],
        "ACTIVE" => $arElement["ACTIVE"],
        "DATE_ACTIVE_FROM" => $arElement["DATE_ACTIVE_FROM"]
    ));

    // Проверяем дату
    $shouldActivate = false;
    if (!empty($arElement["DATE_ACTIVE_FROM"])) {
        $timestamp = MakeTimeStamp($arElement["DATE_ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS");
        if (!$timestamp) {
            $timestamp = MakeTimeStamp($arElement["DATE_ACTIVE_FROM"], "YYYY-MM-DD HH:MI:SS");
        }

        $currentTime = time();

        debug_log("Date check", array(
            "timestamp" => $timestamp,
            "current" => $currentTime,
            "diff" => $timestamp - $currentTime
        ));

        if ($timestamp <= $currentTime) {
            $shouldActivate = true;
            debug_log("Date has come, should activate");
        } else {
            debug_log("Date not come yet, agent will run again");
            return "activateElementOnDate(" . $elementId . ");";
        }
    } else {
        // Если даты нет, но элемент не активен - активируем
        if ($arElement["ACTIVE"] != "Y") {
            $shouldActivate = true;
            debug_log("No date, but element inactive, should activate");
        }
    }

    // Активируем элемент
    if ($shouldActivate && $arElement["ACTIVE"] != "Y") {
        $el = new CIBlockElement;
        $result = $el->Update($elementId, array("ACTIVE" => "Y"));

        if ($result) {
            debug_log("Element {$elementId} activated by agent");

            // Отправляем уведомления после активации (если включены)
            if ($enableNotifications) {
                sendNotificationsForElement($elementId);
            } else {
                debug_log("Notifications disabled, skipping send after activation");
            }
        } else {
            debug_log("Failed to activate element {$elementId}: " . $el->LAST_ERROR);
        }
    } elseif ($shouldActivate && $arElement["ACTIVE"] == "Y") {
        debug_log("Element already active, just sending notifications");
        if ($enableNotifications) {
            sendNotificationsForElement($elementId);
        } else {
            debug_log("Notifications disabled, skipping send for already active element");
        }
    }

    return "";
}

/**
 * Обработчик события - после добавления элемента
 */
function onAfterIBlockElementAddHandler(&$arFields)
{
    global $iblockId, $enableNotifications;

    debug_log("=== onAfterIBlockElementAddHandler called ===");
    debug_log("arFields", array(
        "ID" => $arFields["ID"],
        "IBLOCK_ID" => $arFields["IBLOCK_ID"],
        "ACTIVE" => $arFields["ACTIVE"],
        "DATE_ACTIVE_FROM" => $arFields["DATE_ACTIVE_FROM"]
    ));
    debug_log("Notifications enabled: " . ($enableNotifications ? 'YES' : 'NO'));

    if (empty($arFields["ID"]) || empty($arFields["IBLOCK_ID"])) {
        debug_log("Missing ID or IBLOCK_ID");
        return;
    }

    // Проверяем нужный инфоблок (используем ID из конфига)
    if ($arFields["IBLOCK_ID"] != $iblockId) {
        debug_log("Wrong IBLOCK_ID: " . $arFields["IBLOCK_ID"] . ", expected: " . $iblockId);
        return;
    }

    $elementId = $arFields["ID"];
    $dateActiveFrom = $arFields["DATE_ACTIVE_FROM"] ?? '';

    // Удаляем старые агенты
    CAgent::RemoveAgent("sendNotificationsForElement(" . $elementId . ");", "iblock");
    CAgent::RemoveAgent("activateElementOnDate(" . $elementId . ");", "iblock");

    // Если дата активности указана в будущем - создаем агент для активации
    if (!empty($dateActiveFrom)) {
        $timestamp = MakeTimeStamp($dateActiveFrom, "DD.MM.YYYY HH:MI:SS");
        if (!$timestamp) {
            $timestamp = MakeTimeStamp($dateActiveFrom, "YYYY-MM-DD HH:MI:SS");
        }

        $currentTime = time();

        debug_log("Date processing", array(
            "dateActiveFrom" => $dateActiveFrom,
            "timestamp" => $timestamp,
            "currentTime" => $currentTime,
            "diff" => $timestamp - $currentTime
        ));

        if ($timestamp > $currentTime) {
            // Создаем агент для активации в будущем
            $nextExec = ConvertTimeStamp($timestamp, "FULL");
            CAgent::AddAgent(
                "activateElementOnDate(" . $elementId . ");",
                "iblock",
                "N",
                0,
                "",
                "Y",
                $nextExec
            );

            debug_log("Created activation agent for element ID={$elementId} at {$nextExec}");
            return;
        }
    }

    // Если дата не указана или уже наступила, и элемент активен - отправляем сразу
    if ($arFields["ACTIVE"] == "Y") {
        debug_log("Element active, sending notifications immediately for element ID={$elementId}");
        if ($enableNotifications) {
            sendNotificationsForElement($elementId);
        } else {
            debug_log("Notifications disabled, skipping immediate send");
        }
    } else {
        debug_log("Element not active, will be activated later. Element ID={$elementId}");
    }
}

// ========== РЕГИСТРАЦИЯ ОБРАБОТЧИКОВ ==========
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "onAfterIBlockElementAddHandler");

debug_log("=== livefeed_agent.php loaded and event handler registered ===");
debug_log("Using iblock ID: " . $iblockId);
debug_log("Notifications enabled: " . ($enableNotifications ? 'YES' : 'NO'));
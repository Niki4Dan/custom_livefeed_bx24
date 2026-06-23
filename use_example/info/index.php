<?


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/php_errors.log');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("FLOTENK.LIVE");


global $USER;
$userGroups = $USER->GetUserGroupArray();
$userId = $USER->GetID();
require 'conf.php';

$showHtmlEditor = (count(array_intersect($userGroups, $allowedGroups)) > 0) || $USER->IsAdmin();

?>
<style>
    /* Кнопка помощи в тулбаре */
    .help-toolbar-btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin-left: 10px;
        background: transparent;
        border: 1px solid #d0d7de;
        border-radius: 50%;
        color: #ffffff;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        vertical-align: middle;
    }

    .help-toolbar-btn-icon:hover {
        background: #2fc6f6;
        border-color: #2fc6f6;
        color: #ffffff;
        transform: scale(1.05);
    }

    /* Контейнер для кнопки */
    .help-wrapper {
        position: relative;
        display: inline-block;
        vertical-align: middle;
    }

    /* Всплывающая подсказка - справа от кнопки */
    .help-popup-tooltip {
        position: fixed;
        background: white;
        color: black;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 12px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        pointer-events: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        white-space: nowrap;
    }

    .help-popup-tooltip::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 50%;
        transform: translateY(-50%);
        width: 0;
        height: 0;
        border-top: 6px solid transparent;
        border-bottom: 6px solid transparent;
        border-right: 6px solid white;
    }

    .help-popup-tooltip.show {
        opacity: 1;
        visibility: visible;
    }

    @media (max-width: 600px) {
        .help-toolbar-btn-icon {
            width: 28px;
            height: 28px;
            font-size: 14px;
            margin-left: 8px;
        }

        .help-popup-tooltip {
            white-space: normal;
            max-width: 200px;
            font-size: 11px;
            text-align: center;
        }

        .help-popup-tooltip::before {
            display: none;
        }
    }




    /* Стили для меню смайликов - убедитесь, что они есть */
    .b24-smiles-menu {
        position: fixed !important;
        background: #ffffff !important;
        border: 1px solid #e8e8e8 !important;
        border-radius: 12px !important;
        padding: 12px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
        z-index: 999999 !important;
        display: none;
        max-width: 720px !important;
        max-height: 300px !important;
        overflow-y: auto !important;
        min-width: 250px !important;
    }

    .b24-smile-item {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 6px !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        background: #f8fafc !important;
        border: 2px solid transparent !important;
    }

    .b24-smile-item:hover {
        background: #e8f4fd !important;
        border-color: #2fc6f6 !important;
        transform: scale(1.08) !important;
    }

    .b24-smile-item img {
        max-width: 36px !important;
        max-height: 36px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        display: block !important;
    }

    .custom-smiles-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 28px !important;
        height: 28px !important;
        margin: 0 2px !important;
        padding: 0 !important;
        border-radius: 4px !important;
        cursor: pointer !important;
        font-size: 18px !important;
        line-height: 28px !important;
        text-align: center !important;
        background: transparent !important;
        border: none !important;
        transition: all 0.2s ease !important;
    }

    .custom-smiles-btn:hover {
        background: #e8f4fd !important;
    }
</style>
<?php
if ($showHtmlEditor) {
?>
    <script>
        // Ждем полной загрузки DOM
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var pagetitleContainer = document.getElementById('pagetitleContainer');
                console.log('pagetitleContainer found:', pagetitleContainer);

                if (pagetitleContainer) {
                    // Создаем контейнер для кнопки
                    var helpWrapper = document.createElement('div');
                    helpWrapper.className = 'help-wrapper';

                    var helpBtn = document.createElement('a');
                    helpBtn.href = '/info/help.php';
                    helpBtn.target = '_blank';
                    helpBtn.className = 'help-toolbar-btn-icon';
                    helpBtn.innerHTML = '?';
                    helpBtn.setAttribute('data-tooltip', 'helpPopupTooltip');

                    helpWrapper.appendChild(helpBtn);
                    pagetitleContainer.appendChild(helpWrapper);

                    // Создаем подсказку отдельно
                    var tooltip = document.createElement('div');
                    tooltip.className = 'help-popup-tooltip';
                    tooltip.id = 'helpPopupTooltip';
                    tooltip.innerHTML = '💡 Появились вопросы? Полная инструкция по кнопке слева (?)';
                    document.body.appendChild(tooltip);

                    // Функция обновления позиции подсказки (справа от кнопки)
                    function updateTooltipPosition() {
                        var btnRect = helpBtn.getBoundingClientRect();
                        var tooltipRect = tooltip.getBoundingClientRect();

                        // Позиция справа от кнопки
                        var top = btnRect.top + (btnRect.height / 2) - (tooltipRect.height / 2);
                        var left = btnRect.right + 10;

                        // Проверяем, чтобы подсказка не выходила за пределы экрана справа
                        if (left + tooltipRect.width > window.innerWidth - 10) {
                            // Если не помещается справа, показываем слева
                            left = btnRect.left - tooltipRect.width - 10;
                            tooltip.classList.add('tooltip-left');
                            tooltip.classList.remove('tooltip-right');
                        } else {
                            tooltip.classList.remove('tooltip-left');
                            tooltip.classList.add('tooltip-right');
                        }

                        // Проверяем, чтобы подсказка не выходила за пределы экрана сверху/снизу
                        if (top < 10) {
                            top = 10;
                        }
                        if (top + tooltipRect.height > window.innerHeight - 10) {
                            top = window.innerHeight - tooltipRect.height - 10;
                        }

                        tooltip.style.top = top + 'px';
                        tooltip.style.left = left + 'px';

                        // Меняем направление стрелки
                        if (tooltip.classList.contains('tooltip-left')) {
                            tooltip.style.setProperty('--arrow-position', 'right');
                        } else {
                            tooltip.style.setProperty('--arrow-position', 'left');
                        }
                    }

                    // Добавляем стили для стрелки в зависимости от позиции
                    var style = document.createElement('style');
                    style.textContent = `
                .help-popup-tooltip.tooltip-right::before {
                    left: -6px;
                    right: auto;
                    border-right: 6px solid white;
                    border-left: none;
                }
                .help-popup-tooltip.tooltip-left::before {
                    left: auto;
                    right: -6px;
                    border-left: 6px solid white;
                    border-right: none;
                }
            `;
                    document.head.appendChild(style);

                    // Показываем подсказку через 2 секунды
                    setTimeout(function() {
                        updateTooltipPosition();
                        tooltip.classList.add('show');
                        console.log('Tooltip shown');

                        setTimeout(function() {
                            tooltip.classList.remove('show');
                        }, 5000);
                    }, 2000);

                    // При наведении на кнопку показываем подсказку
                    var hideTimeout;

                    helpBtn.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                        updateTooltipPosition();
                        tooltip.classList.add('show');
                    });

                    helpBtn.addEventListener('mouseleave', function() {
                        hideTimeout = setTimeout(function() {
                            tooltip.classList.remove('show');
                        }, 300);
                    });

                    // При наведении на подсказку не скрываем её
                    tooltip.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                        tooltip.classList.add('show');
                    });

                    tooltip.addEventListener('mouseleave', function() {
                        hideTimeout = setTimeout(function() {
                            tooltip.classList.remove('show');
                        }, 300);
                    });

                    // Обновляем позицию при ресайзе окна
                    window.addEventListener('resize', function() {
                        if (tooltip.classList.contains('show')) {
                            updateTooltipPosition();
                        }
                    });

                    // Обновляем позицию при скролле
                    window.addEventListener('scroll', function() {
                        if (tooltip.classList.contains('show')) {
                            updateTooltipPosition();
                        }
                    });
                } else {
                    console.log('pagetitleContainer not found, retrying...');
                    setTimeout(function() {
                        var retryContainer = document.getElementById('pagetitleContainer');
                        if (retryContainer) {
                            var helpWrapper = document.createElement('div');
                            helpWrapper.className = 'help-wrapper';

                            var helpBtn = document.createElement('a');
                            helpBtn.href = '/info/help.php';
                            helpBtn.target = '_blank';
                            helpBtn.className = 'help-toolbar-btn-icon';
                            helpBtn.innerHTML = '?';

                            helpWrapper.appendChild(helpBtn);
                            retryContainer.appendChild(helpWrapper);

                            var tooltip = document.createElement('div');
                            tooltip.className = 'help-popup-tooltip';
                            tooltip.id = 'helpPopupTooltip';
                            tooltip.innerHTML = '💡 Появились вопросы? Полная инструкция по кнопке слева (?)';
                            document.body.appendChild(tooltip);

                            function updateTooltipPosition() {
                                var btnRect = helpBtn.getBoundingClientRect();
                                var tooltipRect = tooltip.getBoundingClientRect();
                                var top = btnRect.top + (btnRect.height / 2) - (tooltipRect.height / 2);
                                var left = btnRect.right + 10;
                                if (left + tooltipRect.width > window.innerWidth - 10) {
                                    left = btnRect.left - tooltipRect.width - 10;
                                }
                                if (top < 10) top = 10;
                                if (top + tooltipRect.height > window.innerHeight - 10) {
                                    top = window.innerHeight - tooltipRect.height - 10;
                                }
                                tooltip.style.top = top + 'px';
                                tooltip.style.left = left + 'px';
                            }

                            setTimeout(function() {
                                updateTooltipPosition();
                                tooltip.classList.add('show');
                                setTimeout(function() {
                                    tooltip.classList.remove('show');
                                }, 5000);
                            }, 2000);
                        }
                    }, 500);
                }
            }, 100);
        });
    </script>
<?php
}
?>



<?php

global $USER;
if ($USER->IsAuthorized()) {
    // Обнуляем счетчик при просмотре страницы
    CUserCounter::Clear($USER->GetID(), "department_news_counter", SITE_ID, false);

    // Логируем обнуление
    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/upload/notification_debug.log',
        date('Y-m-d H:i:s') . " - Counter cleared for user " . $USER->GetID() . "\n",
        FILE_APPEND
    );
}





// Создаем фильтр для получения только доступных постов
$arFeedFilter = array(
    "IBLOCK_ID" => $iblockId,
    "ACTIVE" => "Y",
    "ACTIVE_DATE" => "Y",
);

// Получаем ID всех постов, к которым у пользователя есть доступ
$arAvailablePosts = array();

// Получаем все посты и проверяем доступ
$rsPosts = CIBlockElement::GetList(
    array(),
    array(
        "IBLOCK_ID" => $iblockId,
        "ACTIVE" => "Y",
        "ACTIVE_DATE" => "Y",
    ),
    false,
    false,
    array("ID", "IBLOCK_ID", "CREATED_BY")
);

while ($arPost = $rsPosts->Fetch()) {
    // Получаем получателей поста
    $recipients = array();
    $noRecipients = array();

    $rsProps = CIBlockElement::GetProperty($iblockId, $arPost["ID"]);
    while ($arProp = $rsProps->Fetch()) {
        if ($arProp['CODE'] == 'RECIPIENTS' && !empty($arProp['VALUE'])) {
            $recipients[] = intval($arProp['VALUE']);
        }
        if ($arProp['CODE'] == 'NORECIPIENTS' && !empty($arProp['VALUE'])) {
            $noRecipients[] = intval($arProp['VALUE']);
        }
    }

    // Проверяем доступ
    $hasAccess = false;
    if (empty($recipients)) {
        $hasAccess = true;
    } else {
        $hasAccess = in_array($userId, $recipients);
    }

    if ($hasAccess && !in_array($userId, $noRecipients)) {
        $arAvailablePosts[] = $arPost["ID"];
    }
}

// Если есть доступные посты, добавляем фильтр по ID
if (!empty($arAvailablePosts)) {
    $arFeedFilter["ID"] = $arAvailablePosts;
} else {
    $arFeedFilter["ID"] = -1; // Не показываем ничего
}

// Получаем количество доступных постов для пагинации
$totalAvailable = count($arAvailablePosts);
$pageSize = 20;
$currentPage = intval($_REQUEST["PAGEN_1"]) ?: 1;
$totalPages = ceil($totalAvailable / $pageSize);

// Если текущая страница больше максимальной - показываем последнюю существующую
if ($currentPage > $totalPages && $totalPages > 0) {
    $_REQUEST["PAGEN_1"] = $totalPages;
    LocalRedirect($APPLICATION->GetCurPageParam("PAGEN_1=" . $totalPages, array("PAGEN_1")));
}

// Если нет доступных постов или они на одной странице - скрываем пагинацию
if ($totalAvailable <= $pageSize) {
    $arParams["DISPLAY_BOTTOM_PAGER"] = "N";
    $arParams["PAGER_SHOW_ALWAYS"] = "N";
}






?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM полностью загружен и разобран');
        document.getElementById('air-workarea-content').style.backgroundColor = "transparent"
    });
</script>

<?php if ($showHtmlEditor): ?>
    <?php
    $APPLICATION->IncludeComponent(
        "niki4:iblock.element.add.form",
        "",
        array(
            "IBLOCK_ID" => $iblockId, // ID вашего инфоблока
            "IBLOCK_TYPE" => "news", // Тип инфоблока

            // Какие поля выводить
            "PROPERTY_CODES" => array(
                "NAME",
                "PREVIEW_TEXT",
                "DATE_ACTIVE_FROM",

            ),

            // Обязательные поля
            "PROPERTY_CODES_REQUIRED" => array("NAME", "PREVIEW_TEXT"),

            // Группы пользователей, которые могут добавлять
            "GROUPS" => array(1, 2), // 1 - администраторы, 2 - все авторизованные

            // Страница списка элементов (опционально)
            "LIST_URL" => "",

            // Сообщения об успешном добавлении
            "USER_MESSAGE_ADD" => "Сообщение успешно отправлено!",

            // Включить визуальный редактор для PREVIEW_TEXT
            "PREVIEW_TEXT_USE_HTML_EDITOR" => "Y",

            // Отключить DETAIL_TEXT (нам не нужен)
            "DETAIL_TEXT_USE_HTML_EDITOR" => "N",

            // Дополнительные настройки
            "SEF_MODE" => "N",
            "USE_CAPTCHA" => "N",
            "STATUS_NEW" => "N", // N Новый элемент активен сразу
            "MAX_USER_ENTRIES" => 1000,
        ),
        false
    );
    ?>


<?php endif; ?>
<? $APPLICATION->IncludeComponent(
    "niki4:news.list",
    "livefeed_style",
    [
        "ACTIVE_DATE" => "Y",
        "ADD_SECTIONS_CHAIN" => "Y",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "N",
        "CACHE_TIME" => "600",
        "CACHE_TYPE" => "N",
        "DETAIL_URL" => "",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "DISPLAY_TOP_PAGER" => "N",
        "FIELD_CODE" => [
            0 => "DATE_ACTIVE_FROM",
            1 => "DATE_CREATE",
            2 => "CREATED_BY",
            3 => "",
        ],
        "FILTER_NAME" => "arFeedFilter",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "IBLOCK_ID" => $iblockId,
        "IBLOCK_TYPE" => "news",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "Y",
        "INCLUDE_SUBSECTIONS" => "Y",
        "MESSAGE_404" => "",
        "NEWS_COUNT" => "20",
        "PAGER_BASE_LINK_ENABLE" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_TEMPLATE" => ".default",
        "PAGER_TITLE" => "Новости",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "PREVIEW_TRUNCATE_LEN" => "",
        "PROPERTY_CODE" => [
            0 => "",
            1 => "*",
            2 => "",
        ],
        "SET_BROWSER_TITLE" => "Y",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "Y",
        "SET_META_KEYWORDS" => "Y",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "Y",
        "SHOW_404" => "N",
        "SORT_BY1" => "DATE_ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "STRICT_SECTION_CHECK" => "N",
        "COMPONENT_TEMPLATE" => "livefeed_style",
        "SORT_BY2" => "SORT",
        "SORT_ORDER2" => "ASC"
    ],
    false
);





?>



<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>
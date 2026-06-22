<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Подключаем модуль диска
$diskEnabled = CModule::IncludeModule('disk');

// Функция для получения ссылки на просмотр файла через Битрикс.Docs
function getBitrixDocsViewUrl($fileId, $fileName, $iblockElementId = null)
{
    global $diskEnabled;

    if (!$diskEnabled) {
        return false;
    }

    // Пытаемся найти уже существующий файл в Диске по FILE_ID
    $diskFile = \Bitrix\Disk\File::load(array('FILE_ID' => $fileId));

    if (!$diskFile) {
        // Если файла в Диске нет, пробуем создать его
        $arFile = CFile::GetFileArray($fileId);
        if (!$arFile) {
            return false;
        }

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $officeExts = array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf');

        // Создаем в Диске только офисные документы и PDF
        if (!in_array($ext, $officeExts)) {
            return false;
        }

        // Получаем хранилище для пользователя (администратора)
        $storage = \Bitrix\Disk\Driver::getInstance()->getStorageByUserId(1);
        if (!$storage) {
            // Если нет хранилища у пользователя, берем общее хранилище
            $storages = \Bitrix\Disk\Storage::getList(array('limit' => 1));
            $storage = $storages->fetch();
            if ($storage) {
                $storage = \Bitrix\Disk\Storage::loadById($storage['ID']);
            }
        }

        if ($storage) {
            // Создаем папку для файлов инфоблока, если её нет
            $folderName = "Новости инфоблока";
            $folder = $storage->getChild(array('NAME' => $folderName));
            if (!$folder) {
                $folder = $storage->addFolder(array('NAME' => $folderName));
            }

            if ($folder) {
                // Загружаем файл в Диск
                $fileArray = CFile::MakeFileArray($fileId);
                $diskFile = $folder->addFile(array(
                    'NAME' => $fileName,
                    'FILE' => $fileArray,
                    'CREATED_BY' => 1
                ));
            }
        }
    }

    if ($diskFile) {
        // Получаем ссылку для просмотра через Битрикс.Docs
        $urlManager = \Bitrix\Disk\Driver::getInstance()->getUrlManager();
        return $urlManager->getPathFileDetail($diskFile);
    }

    return false;
}

// Обработка файловых свойств
foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty) {
    if ($arProperty["PROPERTY_TYPE"] == "F") {
        if (is_array($arProperty["VALUE"])) {
            foreach ($arProperty["VALUE"] as $fid => $file_id) {
                $arFile = CFile::GetFileArray($file_id);
                if ($arFile) {
                    $arResult["FILES"][$pid][$fid] = $arFile;
                    $arResult["FILES"][$pid][$fid]["VIEW_URL"] = $arFile["SRC"];
                }
            }
        } else {
            $arFile = CFile::GetFileArray($arProperty["VALUE"]);
            if ($arFile) {
                $arResult["FILES"][$pid] = $arFile;
                $arResult["FILES"][$pid]["VIEW_URL"] = $arFile["SRC"];
            }
        }
    }
}

// Форматирование даты
if ($arResult["ACTIVE_FROM"]) {
    $arResult["FORMATTED_DATE"] = FormatDate("d F Y", MakeTimeStamp($arResult["ACTIVE_FROM"]));
}

// Подготовка предыдущей/следующей новости
$arResult["PREV"] = array();
$arResult["NEXT"] = array();

$rsElement = CIBlockElement::GetList(
    array("SORT" => "ASC", "ID" => "ASC"),
    array(
        "IBLOCK_ID" => $arResult["IBLOCK_ID"],
        "ACTIVE" => "Y",
        "CHECK_PERMISSIONS" => "Y",
    ),
    false,
    false,
    array("ID", "NAME", "DETAIL_PAGE_URL")
);

$arElements = array();
while ($arElement = $rsElement->GetNext()) {
    $arElements[$arElement["ID"]] = $arElement;
}

$arIds = array_keys($arElements);
$currentIndex = array_search($arResult["ID"], $arIds);

if ($currentIndex !== false) {
    if (isset($arIds[$currentIndex - 1])) {
        $arResult["PREV"] = $arElements[$arIds[$currentIndex - 1]];
    }
    if (isset($arIds[$currentIndex + 1])) {
        $arResult["NEXT"] = $arElements[$arIds[$currentIndex + 1]];
    }
}

// Получаем информацию об авторе элемента
$arResult["IS_OWNER"] = false;
$arResult["CAN_EDIT"] = false;
$arResult["CAN_DELETE"] = false;

if (!empty($arResult["ID"])) {
    $rsElement = CIBlockElement::GetList(
        array(),
        array("ID" => $arResult["ID"]),
        false,
        false,
        array("ID", "CREATED_BY", "IBLOCK_ID")
    );

    if ($arElement = $rsElement->GetNext()) {
        $createdBy = $arElement["CREATED_BY"];
        $currentUserId = $GLOBALS['USER']->GetID();

        // Проверяем, является ли текущий пользователь владельцем
        if ($currentUserId > 0 && $createdBy == $currentUserId) {
            $arResult["IS_OWNER"] = true;
        }

        // Проверяем права на редактирование через CIBlockElement
        $iblockId = $arElement["IBLOCK_ID"];
        $arResult["CAN_EDIT"] = CIBlockElementRights::UserHasRightTo($iblockId, $arResult["ID"], "element_edit");
        $arResult["CAN_DELETE"] = CIBlockElementRights::UserHasRightTo($iblockId, $arResult["ID"], "element_delete");

        // Если пользователь не админ, но владелец, даем права на редактирование/удаление
        if ($arResult["IS_OWNER"] && !$arResult["CAN_EDIT"]) {
            $arResult["CAN_EDIT"] = true;
            $arResult["CAN_DELETE"] = true;
        }

        if ($createdBy > 0) {
            $rsUser = CUser::GetByID($createdBy);
            if ($arUser = $rsUser->Fetch()) {
                $authorName = trim($arUser["NAME"] . " " . $arUser["LAST_NAME"]);
                if (empty($authorName)) {
                    $authorName = $arUser["LOGIN"];
                }
                $arResult["AUTHOR_NAME"] = $authorName;
                $arResult["AUTHOR_LOGIN"] = $arUser["LOGIN"];
                $arResult["AUTHOR_ID"] = $createdBy;
                $arResult["AUTHOR_EMAIL"] = $arUser["EMAIL"];

                $arResult["AUTHOR_AVATAR"] = "";
                if (!empty($arUser["PERSONAL_PHOTO"])) {
                    $arFile = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]);
                    if ($arFile) {
                        $arResult["AUTHOR_AVATAR"] = CFile::ResizeImageGet(
                            $arFile,
                            array("width" => 32, "height" => 32),
                            BX_RESIZE_IMAGE_EXACT,
                            true
                        );
                        if ($arResult["AUTHOR_AVATAR"]) {
                            $arResult["AUTHOR_AVATAR"] = $arResult["AUTHOR_AVATAR"]["src"];
                        }
                    }
                }

                if (empty($arResult["AUTHOR_AVATAR"])) {
                    $arResult["AUTHOR_AVATAR"] = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 24 24' fill='none' stroke='%237a8a9a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'%3E%3C/path%3E%3Ccircle cx='12' cy='7' r='4'%3E%3C/circle%3E%3C/svg%3E";
                }

                $arResult["AUTHOR_PROFILE_URL"] = "/bitrix/admin/user_edit.php?ID=" . $createdBy;

                if (CModule::IncludeModule("socialnetwork")) {
                    $arResult["AUTHOR_PROFILE_URL"] = CComponentEngine::MakePathFromTemplate(
                        "/company/personal/user/#user_id#/",
                        array("user_id" => $createdBy)
                    );
                }
            }
        }
    }
}

// Генерируем URL для редактирования и удаления
$arResult["EDIT_URL"] = "";
$arResult["DELETE_URL"] = "";

if ($arResult["CAN_EDIT"] || $arResult["CAN_DELETE"]) {
    // Получаем URL страницы редактирования
    if ($arResult["IBLOCK_ID"] == 16) {
        $redactor_url = '/ofitsialnaya-informatsiya/redaktor-informatsii/redaktor-flotenk/news_edit.php';
    }
    if ($arResult["IBLOCK_ID"] == 17) {
        $redactor_url = '/ofitsialnaya-informatsiya/redaktor-informatsii/redaktor-flotenk-i/news_edit.php';
    }
    $arResult["EDIT_URL"] = $arParams["EDIT_URL"] ?? $redactor_url . "?edit=Y&CODE=" . $arResult["ID"];
    $arResult["DELETE_URL"] = $redactor_url . "?delete=Y&CODE=" . $arResult["ID"] . "&" . bitrix_sessid_get();
}

if (empty($arResult["AUTHOR_NAME"]) && !empty($arResult["PROPERTIES"]["AUTHOR"]["VALUE"])) {
    $arResult["AUTHOR_NAME"] = $arResult["PROPERTIES"]["AUTHOR"]["VALUE"];
    $arResult["AUTHOR_IS_LINK"] = false;
} elseif (empty($arResult["AUTHOR_NAME"]) && !empty($arResult["PROPERTIES"]["NIKI4_AUTHOR"]["VALUE"])) {
    $arResult["AUTHOR_NAME"] = $arResult["PROPERTIES"]["NIKI4_AUTHOR"]["VALUE"];
    $arResult["AUTHOR_IS_LINK"] = false;
} else {
    $arResult["AUTHOR_IS_LINK"] = !empty($arResult["AUTHOR_ID"]);
}

if (!empty($arResult["ACTIVE_FROM"])) {
    $arResult["FORMATTED_DATE_LONG"] = FormatDate("d F Y", MakeTimeStamp($arResult["ACTIVE_FROM"]));
}

// Собираем файлы для удобства с добавлением ссылок для просмотра через Битрикс.Docs
$arResult["FILES_LIST"] = array();
if (!empty($arResult["PROPERTIES"])) {
    foreach ($arResult["PROPERTIES"] as $propertyCode => $arProperty) {
        if ($arProperty["PROPERTY_TYPE"] == "F" && !empty($arProperty["VALUE"])) {
            $fileIds = is_array($arProperty["VALUE"]) ? $arProperty["VALUE"] : array($arProperty["VALUE"]);
            foreach ($fileIds as $fileId) {
                if ($fileId) {
                    $arFile = CFile::GetFileArray($fileId);
                    if ($arFile) {
                        $bitrixDocsUrl = getBitrixDocsViewUrl($fileId, $arFile["ORIGINAL_NAME"], $arResult["ID"]);

                        $arResult["FILES_LIST"][] = array(
                            "FILE" => $arFile,
                            "PROPERTY_NAME" => $arProperty["NAME"],
                            "PROPERTY_CODE" => $propertyCode,
                            "VIEW_URL" => $arFile["SRC"],
                            "BITRIX_DOCS_URL" => $bitrixDocsUrl
                        );
                    }
                }
            }
        }
    }
}

// Также добавляем файлы из FILES
if (!empty($arResult["FILES"])) {
    foreach ($arResult["FILES"] as $propertyCode => $arFiles) {
        if (is_array($arFiles)) {
            foreach ($arFiles as $fileId => $arFile) {
                if ($arFile && !empty($arFile["SRC"])) {
                    $exists = false;
                    foreach ($arResult["FILES_LIST"] as $existingFile) {
                        if ($existingFile["FILE"]["ID"] == $arFile["ID"]) {
                            $exists = true;
                            break;
                        }
                    }

                    if (!$exists) {
                        $bitrixDocsUrl = getBitrixDocsViewUrl($fileId, $arFile["ORIGINAL_NAME"], $arResult["ID"]);
                        $arResult["FILES_LIST"][] = array(
                            "FILE" => $arFile,
                            "PROPERTY_NAME" => $arResult["PROPERTIES"][$propertyCode]["NAME"] ?? "Файл",
                            "PROPERTY_CODE" => $propertyCode,
                            "VIEW_URL" => $arFile["SRC"],
                            "BITRIX_DOCS_URL" => $bitrixDocsUrl
                        );
                    }
                }
            }
        }
    }
}

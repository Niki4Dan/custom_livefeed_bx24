<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// Получаем параметры фильтрации из URL
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

// Функция для получения иконки по расширению файла
function getFileIcon($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️', 'webp' => '🖼️',
        'pdf' => '📕',
        'doc' => '📘', 'docx' => '📘',
        'xls' => '📗', 'xlsx' => '📗',
        'zip' => '🗜️', 'rar' => '🗜️', '7z' => '🗜️',
        'mp3' => '🎵', 'wav' => '🎵',
        'mp4' => '🎬', 'avi' => '🎬', 'mkv' => '🎬',
        'txt' => '📝',
        'ppt' => '📙', 'pptx' => '📙',
    ];
    return $icons[$ext] ?? '📄';
}

// Функция для определения, является ли файл офисным
function isOfficeFile($filename) {
    $officeExts = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $officeExts);
}

function getMicrosoftViewerUrl($fileUrl) {
    $encodedUrl = urlencode($fileUrl);
    return "https://view.officeapps.live.com/op/embed.aspx?src={$encodedUrl}";
}
// Принудительно загружаем свойство NIKI4_FILES, если оно не загружено
if(empty($arParams["PROPERTY_CODE"]) || !in_array("NIKI4_FILES", $arParams["PROPERTY_CODE"])) {
    \Bitrix\Main\Loader::includeModule('iblock');
    $elementIds = array_column($arResult["ITEMS"], 'ID');
    
    if(!empty($elementIds)) {
        $propertyValues = [];
        $res = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $elementIds, [], ["CODE" => "NIKI4_FILES"]);
        while($prop = $res->Fetch()) {
            $propertyValues[$prop["IBLOCK_ELEMENT_ID"]][$prop["CODE"]][] = $prop;
        }
        
        foreach($arResult["ITEMS"] as &$item) {
            if(!isset($item["PROPERTIES"])) {
                $item["PROPERTIES"] = [];
            }
            
            if(isset($propertyValues[$item["ID"]]["NIKI4_FILES"])) {
                $props = $propertyValues[$item["ID"]]["NIKI4_FILES"];
                if(count($props) == 1 && $props[0]["MULTIPLE"] == "N") {
                    $item["PROPERTIES"]["NIKI4_FILES"] = $props[0];
                } else {
                    $item["PROPERTIES"]["NIKI4_FILES"] = [
                        "NAME" => $props[0]["NAME"],
                        "CODE" => "NIKI4_FILES",
                        "PROPERTY_TYPE" => $props[0]["PROPERTY_TYPE"],
                        "MULTIPLE" => "Y",
                        "VALUE" => array_column($props, "VALUE"),
                        "DESCRIPTION" => array_column($props, "DESCRIPTION"),
                    ];
                }
            }
        }
        unset($item);
    }
}

// Собираем все файлы из новостей из свойства NIKI4_FILES
$arResult["ALL_FILES"] = [];

foreach($arResult["ITEMS"] as $key => $arItem) {
    $arResult["ITEMS"][$key]["FILES"] = [];
    
    if(!empty($arItem["PROPERTIES"]["NIKI4_FILES"]["VALUE"])) {
        $fileIds = is_array($arItem["PROPERTIES"]["NIKI4_FILES"]["VALUE"]) 
            ? $arItem["PROPERTIES"]["NIKI4_FILES"]["VALUE"] 
            : [$arItem["PROPERTIES"]["NIKI4_FILES"]["VALUE"]];
        
        foreach($fileIds as $fileId) {
            if(empty($fileId)) continue;
            
            $fileArray = CFile::GetFileArray($fileId);
            if($fileArray) {
                $fileUrl = $fileArray["SRC"];
                if(strpos($fileUrl, 'http') !== 0 && strpos($fileUrl, '//') !== 0) {
                    $protocol = (CMain::IsHTTPS() ? "https://" : "http://");
                    $serverName = $_SERVER["HTTP_HOST"];
                    $fileUrl = $protocol . $serverName . $fileUrl;
                }
                
                $fileArray["FULL_URL"] = $fileUrl;
                $fileArray["IS_OFFICE"] = isOfficeFile($fileArray["ORIGINAL_NAME"]);
                $fileArray["VIEWER_URL"] = $fileArray["IS_OFFICE"] ? getMicrosoftViewerUrl($fileUrl) : $fileUrl;
                $fileArray["PROPERTY_CODE"] = "NIKI4_FILES";
                $fileArray["PROPERTY_NAME"] = $arItem["PROPERTIES"]["NIKI4_FILES"]["NAME"];
                $fileArray["NEWS_ID"] = $arItem["ID"];
                $fileArray["NEWS_NAME"] = $arItem["NAME"];
                $fileArray["NEWS_URL"] = $arItem["DETAIL_PAGE_URL"];
                $fileArray["ICON"] = getFileIcon($fileArray["ORIGINAL_NAME"]);
                
                $arResult["ITEMS"][$key]["FILES"][] = $fileArray;
                $arResult["ALL_FILES"][] = $fileArray;
            }
        }
    }
}

// Фильтрация по поисковому запросу и дате
if(!empty($searchQuery) || !empty($dateFrom) || !empty($dateTo)) {
    $filteredItems = [];
    $searchQueryLower = mb_strtolower($searchQuery, 'UTF-8');
    
    foreach($arResult["ITEMS"] as $arItem) {
        $found = true;
        
        if(!empty($dateFrom) || !empty($dateTo)) {
            $itemDate = strtotime($arItem["ACTIVE_FROM"]);
            if(!empty($dateFrom) && $itemDate < strtotime($dateFrom)) {
                $found = false;
            }
            if(!empty($dateTo) && $found && $itemDate > strtotime($dateTo . " 23:59:59")) {
                $found = false;
            }
        }
        
        if(!empty($searchQuery) && $found) {
            $found = false;
            
            $name = mb_strtolower($arItem["NAME"], 'UTF-8');
            if(mb_strpos($name, $searchQueryLower) !== false) {
                $found = true;
            }
            
            if(!$found) {
                $previewText = mb_strtolower($arItem["PREVIEW_TEXT"], 'UTF-8');
                if(mb_strpos($previewText, $searchQueryLower) !== false) {
                    $found = true;
                }
            }
            
            if(!$found) {
                $detailText = mb_strtolower($arItem["DETAIL_TEXT"], 'UTF-8');
                if(mb_strpos($detailText, $searchQueryLower) !== false) {
                    $found = true;
                }
            }
            
            if(!$found && !empty($arItem["FILES"])) {
                foreach($arItem["FILES"] as $file) {
                    $fileName = mb_strtolower($file["ORIGINAL_NAME"], 'UTF-8');
                    if(mb_strpos($fileName, $searchQueryLower) !== false) {
                        $found = true;
                        break;
                    }
                }
            }
        }
        
        if($found) {
            $filteredItems[] = $arItem;
        }
    }
    
    $arResult["ITEMS"] = $filteredItems;
    
    $filteredFiles = [];
    $filteredItemIds = array_column($filteredItems, 'ID');
    foreach($arResult["ALL_FILES"] as $file) {
        if(in_array($file["NEWS_ID"], $filteredItemIds)) {
            $filteredFiles[] = $file;
        }
    }
    $arResult["ALL_FILES"] = $filteredFiles;
    
    if($arResult["NAV_RESULT"]) {
        $arResult["NAV_RESULT"]->NavRecordCount = count($filteredItems);
        $arResult["NAV_RESULT"]->NavPageCount = ceil($arResult["NAV_RESULT"]->NavRecordCount / $arResult["NAV_RESULT"]->NavPageSize);
        
        if($arResult["NAV_RESULT"]->NavPageNomer > $arResult["NAV_RESULT"]->NavPageCount) {
            $arResult["NAV_RESULT"]->NavPageNomer = $arResult["NAV_RESULT"]->NavPageCount;
            if($arResult["NAV_RESULT"]->NavPageNomer < 1) $arResult["NAV_RESULT"]->NavPageNomer = 1;
        }
        
        $arResult["NAV_STRING"] = $arResult["NAV_RESULT"]->GetPageNavStringEx($dummy, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
    }
}
?>
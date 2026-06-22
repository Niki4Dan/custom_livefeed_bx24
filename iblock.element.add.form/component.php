<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);

if (!CModule::IncludeModule("iblock")) {
	ShowError(GetMessage("CC_BIEAF_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

// Подключаем модули для работы с пользователями и отделами
CModule::IncludeModule("socialnetwork");
CModule::IncludeModule("intranet");

// ========== ФУНКЦИИ ДЛЯ ОБРАБОТКИ ИЗОБРАЖЕНИЙ ==========

/**
 * Обработка изображений в контенте поста
 * @param string $content HTML контент
 * @param int $postId ID поста
 * @return string Обработанный контент
 */
function processImagesInPostContent($content, $postId)
{
	if (empty($content)) return $content;

	// Замена тегов <b> на <span style="font-weight:bold;">
	$content = preg_replace('/<b\b[^>]*>/i', '<span style="font-weight:bold;">', $content);
	$content = preg_replace('/<\/b>/i', '</span>', $content);


	// Регулярное выражение для поиска всех img тегов
	$pattern = '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i';

	$content = preg_replace_callback($pattern, function ($matches) use ($postId) {
		$originalUrl = $matches[1];

		// Пропускаем уже локальные изображения и внешние URL без data:image
		if (strpos($originalUrl, 'data:image') !== 0 && (strpos($originalUrl, 'http') === 0 || strpos($originalUrl, 'https') === 0)) {
			$localPath = downloadImageForPostContent($originalUrl, $postId);
			if ($localPath) {
				return str_replace($originalUrl, $localPath, $matches[0]);
			}
			return $matches[0];
		} elseif (strpos($originalUrl, 'data:image') === 0) {
			// Обрабатываем base64 изображения
			$localPath = saveBase64ImageForPostContent($originalUrl, $postId);
			if ($localPath) {
				return str_replace($originalUrl, $localPath, $matches[0]);
			}
			return $matches[0];
		}

		return $matches[0];
	}, $content);

	return $content;
}

/**
 * Скачивание изображения для поста
 * @param string $url URL изображения
 * @param int $postId ID поста
 * @return string|false Локальный путь или false
 */
function downloadImageForPostContent($url, $postId)
{
	// Создаем директорию
	$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/images/posts/';
	if (!is_dir($uploadDir)) {
		mkdir($uploadDir, 0777, true);
	}

	// Определяем расширение
	$parsedUrl = parse_url($url);
	$path = $parsedUrl['path'] ?? '';
	$extension = pathinfo($path, PATHINFO_EXTENSION);
	if (empty($extension)) {
		$extension = 'jpg';
	}

	// Генерируем уникальное имя
	$filename = 'post_' . $postId . '_' . time() . '_' . md5($url) . '.' . $extension;
	$localPath = $uploadDir . $filename;
	$webPath = '/upload/images/posts/' . $filename;

	// Скачиваем изображение
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$imageData = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($httpCode === 200 && $imageData !== false) {
		// Проверяем MIME тип
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

/**
 * Сохранение base64 изображения для поста
 * @param string $dataUrl Base64 data URL
 * @param int $postId ID поста
 * @return string|false Локальный путь или false
 */
function saveBase64ImageForPostContent($dataUrl, $postId)
{
	// Создаем директорию
	$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/upload/images/posts/';
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
	$filename = 'post_' . $postId . '_' . time() . '_' . md5($dataUrl) . '.' . $type;
	$localPath = $uploadDir . $filename;
	$webPath = '/upload/images/posts/' . $filename;

	// Сохраняем файл
	if (file_put_contents($localPath, $imageData)) {
		return $webPath;
	}

	return false;
}

// ========== КОНЕЦ ФУНКЦИЙ ДЛЯ ОБРАБОТКИ ИЗОБРАЖЕНИЙ ==========

// ========== НАСТРОЙКИ ДЛЯ ФАЙЛОВ ==========
$allowedFileExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar', "odt", "ods", "odp", "odg", "odf", "odb", "ott", "ots", "otp", "otg", "ppt", "pptx");
$maxFileSize = 20 * 1024 * 1024; // 20 MB

function isAllowedFileExtension($filename, $allowed)
{
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	return in_array($ext, $allowed);
}
// ========== КОНЕЦ НАСТРОЕК ДЛЯ ФАЙЛОВ ==========

$arElement = false;

if ($arParams["IBLOCK_ID"] > 0) {
	$arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"]);
	$bWorkflowIncluded = ($arIBlock["WORKFLOW"] == "Y") && CModule::IncludeModule("workflow");
	$bBizproc = ($arIBlock["BIZPROC"] == "Y") && CModule::IncludeModule("bizproc");
} else {
	$arIBlock = false;
	$bWorkflowIncluded = CModule::IncludeModule("workflow");
	$bBizproc = false;
}

$arParams["ID"] = intval($_REQUEST["CODE"]);
$arParams["MAX_FILE_SIZE"] = intval($arParams["MAX_FILE_SIZE"]);
$arParams["PREVIEW_TEXT_USE_HTML_EDITOR"] = $arParams["PREVIEW_TEXT_USE_HTML_EDITOR"] === "Y" && CModule::IncludeModule("fileman");
$arParams["DETAIL_TEXT_USE_HTML_EDITOR"] = $arParams["DETAIL_TEXT_USE_HTML_EDITOR"] === "Y" && CModule::IncludeModule("fileman");
$arParams["RESIZE_IMAGES"] = $arParams["RESIZE_IMAGES"] === "Y";

if (!is_array($arParams["PROPERTY_CODES"])) {
	$arParams["PROPERTY_CODES"] = array();
} else {
	foreach ($arParams["PROPERTY_CODES"] as $i => $k)
		if ($k == '')
			unset($arParams["PROPERTY_CODES"][$i]);
}
$arParams["PROPERTY_CODES_REQUIRED"] = is_array($arParams["PROPERTY_CODES_REQUIRED"]) ? $arParams["PROPERTY_CODES_REQUIRED"] : array();
foreach ($arParams["PROPERTY_CODES_REQUIRED"] as $key => $value)
	if (trim($value) == '')
		unset($arParams["PROPERTY_CODES_REQUIRED"][$key]);

$arParams["USER_MESSAGE_ADD"] = trim($arParams["USER_MESSAGE_ADD"]);
if ($arParams["USER_MESSAGE_ADD"] == '')
	$arParams["USER_MESSAGE_ADD"] = GetMessage("IBLOCK_USER_MESSAGE_ADD_DEFAULT");

$arParams["USER_MESSAGE_EDIT"] = trim($arParams["USER_MESSAGE_EDIT"]);
if ($arParams["USER_MESSAGE_EDIT"] == '')
	$arParams["USER_MESSAGE_EDIT"] = GetMessage("IBLOCK_USER_MESSAGE_EDIT_DEFAULT");

if (!$bWorkflowIncluded) {
	if ($arParams["STATUS_NEW"] != "N" && $arParams["STATUS_NEW"] != "NEW") $arParams["STATUS_NEW"] = "ANY";
}

if (!is_array($arParams["STATUS"])) {
	if ($arParams["STATUS"] === "INACTIVE")
		$arParams["STATUS"] = array("INACTIVE");
	else
		$arParams["STATUS"] = array("ANY");
}

if (!is_array($arParams["GROUPS"]))
	$arParams["GROUPS"] = array();

$arGroups = $USER->GetUserGroupArray();

// check whether current user can have access to add/edit elements
if ($arParams["ID"] == 0) {
	$bAllowAccess = count(array_intersect($arGroups, $arParams["GROUPS"])) > 0 || $USER->IsAdmin();
} else {
	// rights for editing current element will be in element get filter
	$bAllowAccess = $USER->GetID() > 0;
}

$arResult["ERRORS"] = array();

if ($bAllowAccess) {
	// get iblock sections list
	$rsIBlockSectionList = CIBlockSection::GetList(
		array("left_margin" => "asc"),
		array(
			"ACTIVE" => "Y",
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		),
		false,
		array("ID", "NAME", "DEPTH_LEVEL")
	);
	$arResult["SECTION_LIST"] = array();
	while ($arSection = $rsIBlockSectionList->GetNext()) {
		$arSection["NAME"] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]) . $arSection["NAME"];
		$arResult["SECTION_LIST"][$arSection["ID"]] = array(
			"VALUE" => $arSection["NAME"]
		);
	}

	$COL_COUNT = intval($arParams["DEFAULT_INPUT_SIZE"]);
	if ($COL_COUNT < 1)
		$COL_COUNT = 30;
	// customize "virtual" properties
	$arResult["PROPERTY_LIST"] = array();
	$arResult["PROPERTY_LIST_FULL"] = array(
		"NAME" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"COL_COUNT" => $COL_COUNT,
		),

		"TAGS" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"COL_COUNT" => $COL_COUNT,
		),

		"DATE_ACTIVE_FROM" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"USER_TYPE" => "DateTime",
		),

		"DATE_ACTIVE_TO" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"USER_TYPE" => "DateTime",
		),

		"IBLOCK_SECTION" => array(
			"PROPERTY_TYPE" => "L",
			"ROW_COUNT" => "12",
			"MULTIPLE" => $arParams["MAX_LEVELS"] == 1 ? "N" : "Y",
			"ENUM" => $arResult["SECTION_LIST"],
		),

		"PREVIEW_TEXT" => array(
			"PROPERTY_TYPE" => ($arParams["PREVIEW_TEXT_USE_HTML_EDITOR"] ? "HTML" : "T"),
			"MULTIPLE" => "N",
			"ROW_COUNT" => "12",
			"COL_COUNT" => $COL_COUNT,
		),
		"PREVIEW_PICTURE" => array(
			"PROPERTY_TYPE" => "F",
			"FILE_TYPE" => "jpg, gif, bmp, png, jpeg, webp",
			"MULTIPLE" => "N",
		),
		"DETAIL_TEXT" => array(
			"PROPERTY_TYPE" => ($arParams["DETAIL_TEXT_USE_HTML_EDITOR"] ? "HTML" : "T"),
			"MULTIPLE" => "N",
			"ROW_COUNT" => "5",
			"COL_COUNT" => $COL_COUNT,
		),
		"DETAIL_PICTURE" => array(
			"PROPERTY_TYPE" => "F",
			"FILE_TYPE" => "jpg, gif, bmp, png, jpeg, webp",
			"MULTIPLE" => "N",
		),
	);

	// add them to edit-list
	foreach ($arResult["PROPERTY_LIST_FULL"] as $key => $arr) {
		if (in_array($key, $arParams["PROPERTY_CODES"])) $arResult["PROPERTY_LIST"][] = $key;
	}

	// get iblock property list
	$rsIBLockPropertyList = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]));
	while ($arProperty = $rsIBLockPropertyList->GetNext()) {
		// get list of property enum values
		if ($arProperty["PROPERTY_TYPE"] == "L") {
			$rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
			$arProperty["ENUM"] = array();
			while ($arPropertyEnum = $rsPropertyEnum->GetNext()) {
				$arProperty["ENUM"][$arPropertyEnum["ID"]] = $arPropertyEnum;
			}
		}

		if ($arProperty["PROPERTY_TYPE"] == "T") {
			if (empty($arProperty["COL_COUNT"])) $arProperty["COL_COUNT"] = "30";
			if (empty($arProperty["ROW_COUNT"])) $arProperty["ROW_COUNT"] = "5";
		}

		if ($arProperty["USER_TYPE"] <> '') {
			$arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
			if (array_key_exists("GetPublicEditHTML", $arUserType))
				$arProperty["GetPublicEditHTML"] = $arUserType["GetPublicEditHTML"];
			else
				$arProperty["GetPublicEditHTML"] = false;
		} else {
			$arProperty["GetPublicEditHTML"] = false;
		}

		// add property to edit-list
		if (in_array($arProperty["ID"], $arParams["PROPERTY_CODES"]))
			$arResult["PROPERTY_LIST"][] = $arProperty["ID"];

		$arResult["PROPERTY_LIST_FULL"][$arProperty["ID"]] = $arProperty;
	}

	// set starting filter value
	$arFilter = array("IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"], "SHOW_NEW" => "Y");

	// check type of user association to iblock elements and add user association to filter
	if ($arParams["ELEMENT_ASSOC"] == "PROPERTY_ID" && mb_strlen($arParams["ELEMENT_ASSOC_PROPERTY"]) && is_array($arResult["PROPERTY_LIST_FULL"][$arParams["ELEMENT_ASSOC_PROPERTY"]])) {
		if ($USER->GetID())
			$arFilter["PROPERTY_" . $arParams["ELEMENT_ASSOC_PROPERTY"]] = $USER->GetID();
		else
			$arFilter["ID"] = -1;
	} elseif ($USER->GetID()) {
		$arFilter["CREATED_BY"] = $USER->GetID();
	}
	// additional bugcheck. situation can be found when property ELEMENT_ASSOC_PROPERTY does not exists and user is not registered
	else {
		$arFilter["ID"] = -1;
	}

	//check for access to current element
	if ($arParams["ID"] > 0) {
		if (empty($arFilter["ID"])) $arFilter["ID"] = $arParams["ID"];

		// get current iblock element

		$rsIBlockElements = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter);

		if ($arElement = $rsIBlockElements->Fetch()) {
			$bAllowAccess = true;

			if ($bWorkflowIncluded) {
				$LAST_ID = CIBlockElement::WF_GetLast($arElement['ID']);
				if ($LAST_ID != $arElement["ID"]) {
					$rsElement = CIBlockElement::GetByID($LAST_ID);
					$arElement = $rsElement->Fetch();
				}

				if (!in_array($arElement["WF_STATUS_ID"], $arParams["STATUS"])) {
					ShowError(GetMessage("IBLOCK_ADD_ACCESS_DENIED"));
					$bAllowAccess = false;
				}
			} else {
				if (in_array("INACTIVE", $arParams["STATUS"]) === true && $arElement["ACTIVE"] !== "N") {
					ShowError(GetMessage("IBLOCK_ADD_ACCESS_DENIED"));
					$bAllowAccess = false;
				}
			}
		} else {
			ShowError(GetMessage("IBLOCK_ADD_ELEMENT_NOT_FOUND"));
			$bAllowAccess = false;
		}
	} elseif ($arParams["MAX_USER_ENTRIES"] > 0 && $USER->GetID()) {
		$rsIBlockElements = CIBlockElement::GetList(array(), $arFilter, false, false, array('ID'));
		$elements_count = $rsIBlockElements->SelectedRowsCount();
		if ($elements_count >= $arParams["MAX_USER_ENTRIES"]) {
			ShowError(GetMessage("IBLOCK_ADD_MAX_ENTRIES_EXCEEDED"));
			$bHideAuth = true;
			$bAllowAccess = false;
		}
	}
}




// ========== ФУНКЦИЯ ДЛЯ ПОЛУЧЕНИЯ ПОЛЬЗОВАТЕЛЕЙ ГРУППЫ ==========
/**
 * Получение всех пользователей группы (социальной)
 * @param int $groupId ID группы
 * @return array Массив ID пользователей
 */
function getSocialGroupUsers($groupId)
{
	$users = array();

	if (CModule::IncludeModule("socialnetwork")) {
		// Получаем всех пользователей группы
		$dbUsers = CSocNetUserToGroup::GetList(
			array(),
			array(
				"GROUP_ID" => $groupId,
				"USER_ACTIVE" => "Y"
			),
			false,
			false,
			array("USER_ID")
		);

		while ($arUser = $dbUsers->Fetch()) {
			if (!empty($arUser["USER_ID"]) && !in_array($arUser["USER_ID"], $users)) {
				$users[] = intval($arUser["USER_ID"]);
			}
		}

		// Также включаем всех участников группы
		$dbAllUsers = CSocNetUserToGroup::GetList(
			array(),
			array(
				"GROUP_ID" => $groupId,
				"USER_ACTIVE" => "Y"
			),
			false,
			false,
			array("USER_ID")
		);

		while ($arUser = $dbAllUsers->Fetch()) {
			if (!empty($arUser["USER_ID"]) && !in_array($arUser["USER_ID"], $users)) {
				$users[] = intval($arUser["USER_ID"]);
			}
		}
	}

	return $users;
}


/**
 * Функция получения сотрудников отдела (рекурсивно)
 * @param int $departmentId ID отдела
 * @return array Массив ID сотрудников
 */
function getDepartmentUsersRecursive($departmentId)
{
	$users = array();

	if (CModule::IncludeModule("intranet")) {
		// Получаем сотрудников текущего отдела
		$dbUsers = CIntranetUtils::GetDepartmentEmployees($departmentId, true);
		if ($dbUsers) {
			while ($arUser = $dbUsers->Fetch()) {
				if (!empty($arUser["ID"]) && !in_array($arUser["ID"], $users)) {
					$users[] = intval($arUser["ID"]);
				}
			}
		}

		// Получаем дочерние отделы
		$structureIBlockId = COption::GetOptionInt("intranet", "iblock_structure", 0);
		if ($structureIBlockId > 0) {
			$dbSections = CIBlockSection::GetList(
				array("LEFT_MARGIN" => "ASC"),
				array(
					"IBLOCK_ID" => $structureIBlockId,
					"SECTION_ID" => $departmentId,
					"ACTIVE" => "Y"
				),
				false,
				array("ID")
			);

			while ($arSection = $dbSections->Fetch()) {
				$childUsers = getDepartmentUsersRecursive($arSection["ID"]);
				$users = array_merge($users, $childUsers);
			}
		}
	}

	return array_unique($users);
}

if ($bAllowAccess) {
	// process POST data
	if (check_bitrix_sessid() && (!empty($_REQUEST["iblock_submit"]) || !empty($_REQUEST["iblock_apply"]))) {
		$arProperties = $_REQUEST["PROPERTY"];

		$arUpdateValues = array();
		$arUpdatePropertyValues = array();

		// Явно считываем поле NAME из запроса
		if (isset($_REQUEST['NAME'])) {
			$arUpdateValues['NAME'] = $_REQUEST['NAME'];
		}

		// ========== ОБРАБОТКА DATE_ACTIVE_FROM ==========
		if (isset($_REQUEST["DATE_ACTIVE_FROM"]) && !empty($_REQUEST["DATE_ACTIVE_FROM"])) {
			$dateActiveFrom = trim($_REQUEST["DATE_ACTIVE_FROM"]);
			$timestamp = MakeTimeStamp($dateActiveFrom, "DD.MM.YYYY HH:MI:SS");
			if ($timestamp && $timestamp > 0) {
				$arUpdateValues["DATE_ACTIVE_FROM"] = ConvertTimeStamp($timestamp, "FULL");
			} else {
				$arUpdateValues["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
			}
		} else {
			$arUpdateValues["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time() + CTimeZone::GetOffset(), "FULL");
		}
		// ========== КОНЕЦ ОБРАБОТКИ ==========

		// ========== ОБРАБОТКА RECIPIENTS (привязка к пользователям с поддержкой отделов) ==========
		$arRecipients = array();
		$hasOtherRecipients = false;

		if (isset($_REQUEST["PROPERTY"]["RECIPIENTS"])) {
			$recipientsValue = $_REQUEST["PROPERTY"]["RECIPIENTS"];

			if (!is_array($recipientsValue)) {
				$recipientsValue = array($recipientsValue);
			}

			foreach ($recipientsValue as $value) {
				$value = trim($value);
				if (empty($value)) continue;

				// Выбран отдел (префикс DR)
				if (strpos($value, 'DR') === 0) {
					$deptId = intval(substr($value, 2));
					if ($deptId > 0) {
						$deptUsers = getDepartmentUsersRecursive($deptId);
						foreach ($deptUsers as $userId) {
							if (!in_array($userId, $arRecipients)) {
								$arRecipients[] = $userId;
								$hasOtherRecipients = true;
							}
						}
					}
				}
				// Выбрана социальная группа (префикс SG)
				elseif (strpos($value, 'SG') === 0) {
					$groupId = intval(substr($value, 2));
					if ($groupId > 0) {
						$groupUsers = getSocialGroupUsers($groupId);
						foreach ($groupUsers as $userId) {
							if (!in_array($userId, $arRecipients)) {
								$arRecipients[] = $userId;
								$hasOtherRecipients = true;
							}
						}
					}
				}
				// Выбран пользователь (префикс U)
				elseif (strpos($value, 'U') === 0) {
					$userId = intval(substr($value, 1));
					if ($userId > 0 && !in_array($userId, $arRecipients)) {
						$arRecipients[] = $userId;
						$hasOtherRecipients = true;
					}
				}
				// Просто ID пользователя
				elseif (is_numeric($value) && $value > 0) {
					$userId = intval($value);
					if (!in_array($userId, $arRecipients)) {
						$arRecipients[] = $userId;
						$hasOtherRecipients = true;
					}
				}
			}
		}



		// Добавляем автора ТОЛЬКО если есть другие получатели
		if ($hasOtherRecipients) {
			if (!in_array($USER->GetID(), $arRecipients)) {
				$arRecipients[] = $USER->GetID();
			}
		} else {
			// Если получатели не выбраны - очищаем массив
			$arRecipients = array();
		}

		// Ищем ID свойства RECIPIENTS
		$recipientsPropertyId = null;
		foreach ($arResult["PROPERTY_LIST_FULL"] as $propId => $propData) {
			if ($propData["CODE"] == "RECIPIENTS") {
				$recipientsPropertyId = $propId;
				break;
			}
		}

		if ($recipientsPropertyId) {
			if (!empty($arRecipients)) {
				if ($arResult["PROPERTY_LIST_FULL"][$recipientsPropertyId]["MULTIPLE"] == "Y") {
					$arUpdatePropertyValues[$recipientsPropertyId] = array();
					foreach ($arRecipients as $userId) {
						$arUpdatePropertyValues[$recipientsPropertyId][] = $userId;
					}
				} else {
					$arUpdatePropertyValues[$recipientsPropertyId] = $arRecipients[0];
				}
			} else {
				$arUpdatePropertyValues[$recipientsPropertyId] = "";
			}
		}
		// ========== КОНЕЦ ОБРАБОТКИ RECIPIENTS ==========

		// ========== ОБРАБОТКА NORECIPIENTS (исключить из получателей с поддержкой отделов) ==========
		$arNoRecipients = array();
		if (isset($_REQUEST["PROPERTY"]["NORECIPIENTS"])) {
			$norecipientsValue = $_REQUEST["PROPERTY"]["NORECIPIENTS"];

			if (!is_array($norecipientsValue)) {
				$norecipientsValue = array($norecipientsValue);
			}

			foreach ($norecipientsValue as $value) {
				$value = trim($value);
				if (empty($value)) continue;

				// Выбран отдел (префикс DR)
				if (strpos($value, 'DR') === 0) {
					$deptId = intval(substr($value, 2));
					if ($deptId > 0) {
						$deptUsers = getDepartmentUsersRecursive($deptId);
						foreach ($deptUsers as $userId) {
							if (!in_array($userId, $arNoRecipients)) {
								$arNoRecipients[] = $userId;
							}
						}
					}
				}
				// Выбрана социальная группа (префикс SG)
				elseif (strpos($value, 'SG') === 0) {
					$groupId = intval(substr($value, 2));
					if ($groupId > 0) {
						$groupUsers = getSocialGroupUsers($groupId);
						foreach ($groupUsers as $userId) {
							if (!in_array($userId, $arNoRecipients)) {
								$arNoRecipients[] = $userId;
							}
						}
					}
				}
				// Выбран пользователь (префикс U)
				elseif (strpos($value, 'U') === 0) {
					$userId = intval(substr($value, 1));
					if ($userId > 0 && !in_array($userId, $arNoRecipients)) {
						$arNoRecipients[] = $userId;
					}
				}
				// Просто ID пользователя
				elseif (is_numeric($value) && $value > 0) {
					$userId = intval($value);
					if (!in_array($userId, $arNoRecipients)) {
						$arNoRecipients[] = $userId;
					}
				}
			}
		}

		// Исключаем пользователей из списка получателей
		$finalRecipients = array_diff($arRecipients, $arNoRecipients);

		// Ищем ID свойства NORECIPIENTS
		$norecipientsPropertyId = null;
		foreach ($arResult["PROPERTY_LIST_FULL"] as $propId => $propData) {
			if ($propData["CODE"] == "NORECIPIENTS") {
				$norecipientsPropertyId = $propId;
				break;
			}
		}

		if ($norecipientsPropertyId) {
			if (!empty($arNoRecipients)) {
				if ($arResult["PROPERTY_LIST_FULL"][$norecipientsPropertyId]["MULTIPLE"] == "Y") {
					$arUpdatePropertyValues[$norecipientsPropertyId] = array();
					foreach ($arNoRecipients as $userId) {
						$arUpdatePropertyValues[$norecipientsPropertyId][] = $userId;
					}
				} else {
					$arUpdatePropertyValues[$norecipientsPropertyId] = $arNoRecipients[0];
				}
			} else {
				$arUpdatePropertyValues[$norecipientsPropertyId] = "";
			}
		}
		// ========== КОНЕЦ ОБРАБОТКИ NORECIPIENTS ==========

		// ========== ОБРАБОТКА ПРИКРЕПЛЕННЫХ ФАЙЛОВ (MESSAGE_FILE) ==========
		global $DB;
		$arFileValues = array();

		// Ищем ID свойства MESSAGE_FILE
		$filePropertyId = null;
		foreach ($arResult["PROPERTY_LIST_FULL"] as $propId => $propData) {
			if ($propData["CODE"] == "MESSAGE_FILE") {
				$filePropertyId = $propId;
				break;
			}
		}

		// Если свойства нет, создаем его
		if (!$filePropertyId && $arParams["IBLOCK_ID"] > 0) {
			$dbProp = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => "MESSAGE_FILE"));
			if (!$dbProp->Fetch()) {
				$prop = new CIBlockProperty;
				$prop->Add(array(
					"NAME" => "Прикрепленные файлы",
					"ACTIVE" => "Y",
					"SORT" => 500,
					"CODE" => "MESSAGE_FILE",
					"PROPERTY_TYPE" => "F",
					"MULTIPLE" => "Y",
					"FILE_TYPE" => "jpg, jpeg, png, gif, webp, pdf, doc, docx, xls, xlsx, txt, zip, rar",
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				));

				// Повторно ищем свойство
				foreach ($arResult["PROPERTY_LIST_FULL"] as $propId => $propData) {
					if ($propData["CODE"] == "MESSAGE_FILE") {
						$filePropertyId = $propId;
						break;
					}
				}
			}
		}

		// Обработка загруженных файлов
		if (isset($_FILES['MESSAGE_FILE']) && is_array($_FILES['MESSAGE_FILE']['name'])) {
			for ($i = 0; $i < count($_FILES['MESSAGE_FILE']['name']); $i++) {
				if ($_FILES['MESSAGE_FILE']['error'][$i] == 0 && !empty($_FILES['MESSAGE_FILE']['name'][$i])) {
					$fileName = $_FILES['MESSAGE_FILE']['name'][$i];

					// Проверяем расширение
					if (!isAllowedFileExtension($fileName, $allowedFileExtensions)) {
						$arResult["ERRORS"][] = "Файл \"{$fileName}\" имеет неподдерживаемое расширение. Разрешенные форматы: " . implode(', ', $allowedFileExtensions);
						continue;
					}

					// Проверяем размер
					if ($_FILES['MESSAGE_FILE']['size'][$i] > $maxFileSize) {
						$arResult["ERRORS"][] = "Файл \"{$fileName}\" превышает максимальный размер (20 МБ)";
						continue;
					}

					// Сохраняем файл
					$arFile = array(
						'name' => $fileName,
						'size' => $_FILES['MESSAGE_FILE']['size'][$i],
						'tmp_name' => $_FILES['MESSAGE_FILE']['tmp_name'][$i],
						'type' => $_FILES['MESSAGE_FILE']['type'][$i],
						'MODULE_ID' => 'iblock'
					);

					$fid = CFile::SaveFile($arFile, 'iblock/message_files');
					if ($fid > 0) {
						$arFileValues[] = $fid;
					}
				}
			}
		}

		// Добавляем файлы в свойства
		if ($filePropertyId && !empty($arFileValues)) {
			if ($arResult["PROPERTY_LIST_FULL"][$filePropertyId]["MULTIPLE"] == "Y") {
				$arUpdatePropertyValues[$filePropertyId] = $arFileValues;
			} else {
				$arUpdatePropertyValues[$filePropertyId] = $arFileValues[0];
			}
		}
		// ========== КОНЕЦ ОБРАБОТКИ ФАЙЛОВ ==========

		// process properties list
		foreach ($arParams["PROPERTY_CODES"] as $i => $propertyID) {
			// Пропускаем RECIPIENTS, NORECIPIENTS и MESSAGE_FILE, т.к. уже обработали
			$isRecipientsProperty = false;
			$isNoRecipientsProperty = false;
			$isFileProperty = false;

			if (is_numeric($propertyID)) {
				$propCode = $arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"];
				$isRecipientsProperty = ($propCode == "RECIPIENTS");
				$isNoRecipientsProperty = ($propCode == "NORECIPIENTS");
				$isFileProperty = ($propCode == "MESSAGE_FILE");
			} else {
				$isRecipientsProperty = ($propertyID == "RECIPIENTS");
				$isNoRecipientsProperty = ($propertyID == "NORECIPIENTS");
				$isFileProperty = ($propertyID == "MESSAGE_FILE");
			}

			if ($isRecipientsProperty || $isNoRecipientsProperty || $isFileProperty) {
				continue;
			}

			$arPropertyValue = $arProperties[$propertyID];
			// check if property is a real property, or element field
			if (intval($propertyID) > 0) {
				// for non-file properties
				if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] != "F") {
					// for multiple properties
					if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y") {
						$arUpdatePropertyValues[$propertyID] = array();

						if (!is_array($arPropertyValue)) {
							$arUpdatePropertyValues[$propertyID][] = $arPropertyValue;
						} else {
							foreach ($arPropertyValue as $key => $value) {
								if (
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "L" && intval($value) > 0
									||
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] != "L" && !empty($value)
								) {
									$arUpdatePropertyValues[$propertyID][] = $value;
								}
							}
						}
					}
					// for single properties
					else {
						if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] != "L")
							$arUpdatePropertyValues[$propertyID] = $arPropertyValue[0];
						else
							$arUpdatePropertyValues[$propertyID] = $arPropertyValue;
					}
				}
				// for file properties
				else {
					$arUpdatePropertyValues[$propertyID] = array();
					foreach ($arPropertyValue as $key => $value) {
						$arFile = $_FILES["PROPERTY_FILE_" . $propertyID . "_" . $key];
						$arFile["del"] = $_REQUEST["DELETE_FILE"][$propertyID][$key] == "Y" ? "Y" : "";
						$arUpdatePropertyValues[$propertyID][$key] = $arFile;

						if (($arParams["MAX_FILE_SIZE"] > 0) && ($arFile["size"] > $arParams["MAX_FILE_SIZE"]))
							$arResult["ERRORS"][] = GetMessage("IBLOCK_ERROR_FILE_TOO_LARGE");
					}

					if (empty($arUpdatePropertyValues[$propertyID]))
						unset($arUpdatePropertyValues[$propertyID]);
				}
			} else {
				// for "virtual" properties
				if ($propertyID == "IBLOCK_SECTION") {
					if (!is_array($arProperties[$propertyID]))
						$arProperties[$propertyID] = array($arProperties[$propertyID]);
					$arUpdateValues[$propertyID] = $arProperties[$propertyID];

					if ($arParams["LEVEL_LAST"] == "Y" && is_array($arUpdateValues[$propertyID])) {
						foreach ($arUpdateValues[$propertyID] as $section_id) {
							$rsChildren = CIBlockSection::GetList(
								array("SORT" => "ASC"),
								array(
									"IBLOCK_ID" => $arParams["IBLOCK_ID"],
									"SECTION_ID" => $section_id,
								),
								false,
								array("ID")
							);
							if ($rsChildren->SelectedRowsCount() > 0) {
								$arResult["ERRORS"][] = GetMessage("IBLOCK_ADD_LEVEL_LAST_ERROR");
								break;
							}
						}
					}

					if ($arParams["MAX_LEVELS"] > 0 && count($arUpdateValues[$propertyID]) > $arParams["MAX_LEVELS"]) {
						$arResult["ERRORS"][] = str_replace("#MAX_LEVELS#", $arParams["MAX_LEVELS"], GetMessage("IBLOCK_ADD_MAX_LEVELS_EXCEEDED"));
					}
				} elseif ($propertyID == "DATE_ACTIVE_FROM") {
					continue;
				} else {
					if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "F") {
						$arFile = $_FILES["PROPERTY_FILE_" . $propertyID . "_0"];
						$arFile["del"] = $_REQUEST["DELETE_FILE"][$propertyID][0] == "Y" ? "Y" : "";
						$arUpdateValues[$propertyID] = $arFile;
						if ($arParams["MAX_FILE_SIZE"] > 0 && $arFile["size"] > $arParams["MAX_FILE_SIZE"])
							$arResult["ERRORS"][] = GetMessage("IBLOCK_ERROR_FILE_TOO_LARGE");
					} elseif ($arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "HTML") {
						// Получаем ID поста (для нового элемента используем временный ID)
						$tempId = ($arParams["ID"] > 0) ? $arParams["ID"] : time();

						// Обрабатываем изображения в контенте
						$processedContent = processImagesInPostContent($arProperties[$propertyID][0], $tempId);

						if ($propertyID == "DETAIL_TEXT")
							$arUpdateValues["DETAIL_TEXT_TYPE"] = "html";
						if ($propertyID == "PREVIEW_TEXT")
							$arUpdateValues["PREVIEW_TEXT_TYPE"] = "html";
						$arUpdateValues[$propertyID] = $processedContent;
					} else {
						// Получаем ID поста (для нового элемента используем временный ID)
						$tempId = ($arParams["ID"] > 0) ? $arParams["ID"] : time();

						// Обрабатываем изображения в контенте (для текстового режима)
						$processedContent = processImagesInPostContent($arProperties[$propertyID][0], $tempId);

						if ($propertyID == "DETAIL_TEXT")
							$arUpdateValues["DETAIL_TEXT_TYPE"] = "text";
						if ($propertyID == "PREVIEW_TEXT")
							$arUpdateValues["PREVIEW_TEXT_TYPE"] = "text";
						$arUpdateValues[$propertyID] = $processedContent;
					}
				}
			}
		}

		// check required properties
		foreach ($arParams["PROPERTY_CODES_REQUIRED"] as $key => $propertyID) {
			// Проверка для RECIPIENTS
			$isRecipientsRequired = false;
			if (is_numeric($propertyID)) {
				$propCode = $arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"];
				$isRecipientsRequired = ($propCode == "RECIPIENTS");
			} else {
				$isRecipientsRequired = ($propertyID == "RECIPIENTS");
			}

			if ($isRecipientsRequired) {
				if (empty($finalRecipients)) {
					$arResult["ERRORS"][] = str_replace("#PROPERTY_NAME#", "Получатели", GetMessage("IBLOCK_ADD_ERROR_REQUIRED"));
				}
				continue;
			}

			// Проверка для NORECIPIENTS (необязательное, пропускаем)
			$isNoRecipientsProperty = false;
			if (is_numeric($propertyID)) {
				$propCode = $arResult["PROPERTY_LIST_FULL"][$propertyID]["CODE"];
				$isNoRecipientsProperty = ($propCode == "NORECIPIENTS");
			} else {
				$isNoRecipientsProperty = ($propertyID == "NORECIPIENTS");
			}

			if ($isNoRecipientsProperty) {
				continue;
			}

			$bError = false;
			$propertyValue = intval($propertyID) > 0 ? $arUpdatePropertyValues[$propertyID] : $arUpdateValues[$propertyID];

			if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] != "")
				$arUserType = CIBlockProperty::GetUserType($arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"]);
			else
				$arUserType = array();

			//Files check
			if ($arResult["PROPERTY_LIST_FULL"][$propertyID]['PROPERTY_TYPE'] == 'F') {
				//New element
				if ($arParams["ID"] <= 0) {
					$bError = true;
					if (is_array($propertyValue)) {
						if (array_key_exists("tmp_name", $propertyValue) && array_key_exists("size", $propertyValue)) {
							if ($propertyValue['size'] > 0) {
								$bError = false;
							}
						} else {
							foreach ($propertyValue as $arFile) {
								if ($arFile['size'] > 0) {
									$bError = false;
									break;
								}
							}
						}
					}
				}
				//Element field
				elseif (intval($propertyID) <= 0) {
					if ($propertyValue['size'] <= 0) {
						if (intval($arElement[$propertyID]) <= 0 || $propertyValue['del'] == 'Y')
							$bError = true;
					}
				}
				//Element property
				else {
					$dbProperty = CIBlockElement::GetProperty(
						$arElement["IBLOCK_ID"],
						$arParams["ID"],
						"sort",
						"asc",
						array("ID" => $propertyID)
					);

					$bCount = 0;
					while ($arProperty = $dbProperty->Fetch())
						$bCount++;

					foreach ($propertyValue as $arFile) {
						if ($arFile['size'] > 0) {
							$bCount++;
							break;
						} elseif ($arFile['del'] == 'Y') {
							$bCount--;
						}
					}

					$bError = $bCount <= 0;
				}
			} elseif (array_key_exists("GetLength", $arUserType)) {
				$len = 0;
				if (is_array($propertyValue) && !array_key_exists("VALUE", $propertyValue)) {
					foreach ($propertyValue as $value) {
						if (is_array($value) && !array_key_exists("VALUE", $value))
							foreach ($value as $val)
								$len += call_user_func_array($arUserType["GetLength"], array($arResult["PROPERTY_LIST_FULL"][$propertyID], array("VALUE" => $val)));
						elseif (is_array($value) && array_key_exists("VALUE", $value))
							$len += call_user_func_array($arUserType["GetLength"], array($arResult["PROPERTY_LIST_FULL"][$propertyID], $value));
						else
							$len += call_user_func_array($arUserType["GetLength"], array($arResult["PROPERTY_LIST_FULL"][$propertyID], array("VALUE" => $value)));
					}
				} elseif (is_array($propertyValue) && array_key_exists("VALUE", $propertyValue)) {
					$len += call_user_func_array($arUserType["GetLength"], array($arResult["PROPERTY_LIST_FULL"][$propertyID], $propertyValue));
				} else {
					$len += call_user_func_array($arUserType["GetLength"], array($arResult["PROPERTY_LIST_FULL"][$propertyID], array("VALUE" => $propertyValue)));
				}

				if ($len <= 0)
					$bError = true;
			}
			//multiple property
			elseif ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" || $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "L") {
				if (is_array($propertyValue)) {
					$bError = true;
					foreach ($propertyValue as $value) {
						if ($value <> '') {
							$bError = false;
							break;
						}
					}
				} elseif ($propertyValue == '') {
					$bError = true;
				}
			}
			//single
			elseif (is_array($propertyValue) && array_key_exists("VALUE", $propertyValue)) {
				if ($propertyValue["VALUE"] == '')
					$bError = true;
			} elseif (!is_array($propertyValue)) {
				if ($propertyValue == '')
					$bError = true;
			}

			if ($bError) {
				$arResult["ERRORS"][] = str_replace("#PROPERTY_NAME#", intval($propertyID) > 0 ? $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] : (!empty($arParams["CUSTOM_TITLE_" . $propertyID]) ? $arParams["CUSTOM_TITLE_" . $propertyID] : GetMessage("IBLOCK_FIELD_" . $propertyID)), GetMessage("IBLOCK_ADD_ERROR_REQUIRED"));
			}
		}

		// check captcha
		if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0) {
			if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"])) {
				$arResult["ERRORS"][] = GetMessage("IBLOCK_FORM_WRONG_CAPTCHA");
			}
		}

		//---BP---
		if ($bBizproc) {
			$DOCUMENT_TYPE = "iblock_" . $arIBlock["ID"];

			$arDocumentStates = CBPDocument::GetDocumentStates(
				array("iblock", "CIBlockDocument", $DOCUMENT_TYPE),
				($arParams["ID"] > 0) ? array("iblock", "CIBlockDocument", $arParams["ID"]) : null,
				"Y"
			);

			$arCurrentUserGroups = $USER->GetUserGroupArray();
			if (!$arElement || $arElement["CREATED_BY"] == $USER->GetID()) {
				$arCurrentUserGroups[] = "Author";
			}

			if ($arParams["ID"]) {
				$canWrite = CBPDocument::CanUserOperateDocument(
					CBPCanUserOperateOperation::WriteDocument,
					$USER->GetID(),
					array("iblock", "CIBlockDocument", $arParams["ID"]),
					array("AllUserGroups" => $arCurrentUserGroups, "DocumentStates" => $arDocumentStates)
				);
			} else {
				$canWrite = CBPDocument::CanUserOperateDocumentType(
					CBPCanUserOperateOperation::WriteDocument,
					$USER->GetID(),
					array("iblock", "CIBlockDocument", $DOCUMENT_TYPE),
					array("AllUserGroups" => $arCurrentUserGroups, "DocumentStates" => $arDocumentStates)
				);
			}

			if (!$canWrite)
				$arResult["ERRORS"][] = GetMessage("CC_BIEAF_ACCESS_DENIED_STATUS");

			if (empty($arResult["ERRORS"])) {
				$arBizProcParametersValues = array();
				foreach ($arDocumentStates as $arDocumentState) {
					if ($arDocumentState["ID"] == '') {
						$arErrorsTmp = array();

						$arBizProcParametersValues[$arDocumentState["TEMPLATE_ID"]] = CBPDocument::StartWorkflowParametersValidate(
							$arDocumentState["TEMPLATE_ID"],
							$arDocumentState["TEMPLATE_PARAMETERS"],
							array("iblock", "CIBlockDocument", $DOCUMENT_TYPE),
							$arErrorsTmp
						);

						foreach ($arErrorsTmp as $e)
							$arResult["ERRORS"][] = $e["message"];
					}
				}
			}
		}

		if (empty($arResult["ERRORS"])) {
			if ($arParams["ELEMENT_ASSOC"] == "PROPERTY_ID")
				$arUpdatePropertyValues[$arParams["ELEMENT_ASSOC_PROPERTY"]] = $USER->GetID();
			$arUpdateValues["MODIFIED_BY"] = $USER->GetID();

			$arUpdateValues["PROPERTY_VALUES"] = $arUpdatePropertyValues;

			// ========== УСТАНОВКА АКТИВНОСТИ В ЗАВИСИМОСТИ ОТ ДАТЫ ==========
			if (!empty($arUpdateValues["DATE_ACTIVE_FROM"])) {
				// Парсим дату
				$timestamp = MakeTimeStamp($arUpdateValues["DATE_ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS");
				if (!$timestamp) {
					$timestamp = MakeTimeStamp($arUpdateValues["DATE_ACTIVE_FROM"], "YYYY-MM-DD HH:MI:SS");
				}

				$currentTime = time();

				// Если дата в будущем - элемент НЕ активен
				if ($timestamp > $currentTime) {
					$arUpdateValues["ACTIVE"] = "N";
					// Логируем для отладки
					file_put_contents(
						$_SERVER['DOCUMENT_ROOT'] . '/upload/date_debug.log',
						date('Y-m-d H:i:s') . " - Element will be INACTIVE until: " . $arUpdateValues["DATE_ACTIVE_FROM"] . " (timestamp: $timestamp, current: $currentTime)\n",
						FILE_APPEND
					);
				} else {
					$arUpdateValues["ACTIVE"] = "Y";
					file_put_contents(
						$_SERVER['DOCUMENT_ROOT'] . '/upload/date_debug.log',
						date('Y-m-d H:i:s') . " - Element ACTIVE now. Date: " . $arUpdateValues["DATE_ACTIVE_FROM"] . "\n",
						FILE_APPEND
					);
				}
			} else {
				// Если дата не указана - публикуем сразу
				$arUpdateValues["ACTIVE"] = "Y";
				file_put_contents(
					$_SERVER['DOCUMENT_ROOT'] . '/upload/date_debug.log',
					date('Y-m-d H:i:s') . " - No date, element ACTIVE immediately\n",
					FILE_APPEND
				);
			}
			// ========== КОНЕЦ УСТАНОВКИ АКТИВНОСТИ ==========

			if ($bWorkflowIncluded && $arParams["STATUS_NEW"] <> '') {
				$arUpdateValues["WF_STATUS_ID"] = $arParams["STATUS_NEW"];
				// НЕ ПЕРЕЗАПИСЫВАЕМ ACTIVE, ТОЛЬКО ЕСЛИ ОН ЕЩЕ НЕ УСТАНОВЛЕН КАК N
				if (!isset($arUpdateValues["ACTIVE"]) || $arUpdateValues["ACTIVE"] !== "N") {
					$arUpdateValues["ACTIVE"] = "Y";
				}
			} elseif ($bBizproc) {
				if ($arParams["STATUS_NEW"] == "ANY") {
					$arUpdateValues["BP_PUBLISHED"] = "N";
				} elseif ($arParams["STATUS_NEW"] == "N") {
					$arUpdateValues["BP_PUBLISHED"] = "Y";
				} else {
					if ($arParams["ID"] <= 0)
						$arUpdateValues["BP_PUBLISHED"] = "N";
				}
				// НЕ ПЕРЕЗАПИСЫВАЕМ ACTIVE, ТОЛЬКО ЕСЛИ ОН ЕЩЕ НЕ УСТАНОВЛЕН КАК N
				if (!isset($arUpdateValues["ACTIVE"]) || $arUpdateValues["ACTIVE"] !== "N") {
					$arUpdateValues["ACTIVE"] = "Y";
				}
			}

			// update existing element
			$oElement = new CIBlockElement();
			if ($arParams["ID"] > 0) {
				$sAction = "EDIT";

				$bFieldProps = array();
				foreach ($arUpdateValues["PROPERTY_VALUES"] as $prop_id => $v) {
					$bFieldProps[$prop_id] = true;
				}
				$dbPropV = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arParams["ID"], "sort", "asc", array("ACTIVE" => "Y"));
				while ($arPropV = $dbPropV->Fetch()) {
					if (!array_key_exists($arPropV["ID"], $bFieldProps) && $arPropV["PROPERTY_TYPE"] != "F") {
						if ($arPropV["MULTIPLE"] == "Y") {
							if (!array_key_exists($arPropV["ID"], $arUpdateValues["PROPERTY_VALUES"]))
								$arUpdateValues["PROPERTY_VALUES"][$arPropV["ID"]] = array();
							$arUpdateValues["PROPERTY_VALUES"][$arPropV["ID"]][$arPropV["PROPERTY_VALUE_ID"]] = array(
								"VALUE" => $arPropV["VALUE"],
								"DESCRIPTION" => $arPropV["DESCRIPTION"],
							);
						} else {
							$arUpdateValues["PROPERTY_VALUES"][$arPropV["ID"]] = array(
								"VALUE" => $arPropV["VALUE"],
								"DESCRIPTION" => $arPropV["DESCRIPTION"],
							);
						}
					}
				}

				if (!$res = $oElement->Update($arParams["ID"], $arUpdateValues, $bWorkflowIncluded, true, $arParams["RESIZE_IMAGES"])) {
					$arResult["ERRORS"][] = $oElement->LAST_ERROR;
				}
			}
			// add new element
			else {
				$arUpdateValues["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

				$sAction = "ADD";
				if (!$arParams["ID"] = $oElement->Add($arUpdateValues, $bWorkflowIncluded, true, $arParams["RESIZE_IMAGES"])) {
					$arResult["ERRORS"][] = $oElement->LAST_ERROR;
				}
			}
		}

		if ($bBizproc && empty($arResult["ERRORS"])) {
			$arBizProcWorkflowId = array();
			foreach ($arDocumentStates as $arDocumentState) {
				if ($arDocumentState["ID"] == '') {
					$arErrorsTmp = array();

					$arBizProcWorkflowId[$arDocumentState["TEMPLATE_ID"]] = CBPDocument::StartWorkflow(
						$arDocumentState["TEMPLATE_ID"],
						array("iblock", "CIBlockDocument", $arParams["ID"]),
						$arBizProcParametersValues[$arDocumentState["TEMPLATE_ID"]],
						$arErrorsTmp
					);

					foreach ($arErrorsTmp as $e)
						$arResult["ERRORS"][] = $e["message"];
				}
			}
		}

		if ($bBizproc && empty($arResult["ERRORS"])) {
			$arDocumentStates = null;
			CBPDocument::AddDocumentToHistory(array("iblock", "CIBlockDocument", $arParams["ID"]), $arUpdateValues["NAME"], $USER->GetID());
		}

		// redirect to element edit form or to elements list
		if (empty($arResult["ERRORS"])) {
			if (!empty($_REQUEST["iblock_submit"])) {
				if ($arParams["LIST_URL"] <> '') {
					$sRedirectUrl = $arParams["LIST_URL"];
				} else {
					$sRedirectUrl = $APPLICATION->GetCurPageParam("", array("edit", "CODE", "strIMessage"), $get_index_page = false);
				}
			} else {
				$sRedirectUrl = $APPLICATION->GetCurPageParam("edit=Y&CODE=" . $arParams["ID"], array("edit", "CODE", "strIMessage"), $get_index_page = false);
			}

			$sAction = $sAction == "ADD" ? "ADD" : "EDIT";
			$sRedirectUrl .= (mb_strpos($sRedirectUrl, "?") === false ? "?" : "&") . "strIMessage=";
			$sRedirectUrl .= urlencode($arParams["USER_MESSAGE_" . $sAction]);

			LocalRedirect($sRedirectUrl);
			exit();
		}
	}

	//prepare data for form

	$arResult["PROPERTY_REQUIRED"] = is_array($arParams["PROPERTY_CODES_REQUIRED"]) ? $arParams["PROPERTY_CODES_REQUIRED"] : array();

	if ($arParams["ID"] > 0) {
		// $arElement is defined before in elements rights check
		$rsElementSections = CIBlockElement::GetElementGroups($arElement["ID"]);
		$arElement["IBLOCK_SECTION"] = array();
		while ($arSection = $rsElementSections->GetNext()) {
			$arElement["IBLOCK_SECTION"][] = array("VALUE" => $arSection["ID"]);
		}

		$arResult["ELEMENT"] = array();
		foreach ($arElement as $key => $value) {
			$arResult["ELEMENT"]["~" . $key] = $value;
			if (!is_array($value) && !is_object($value))
				$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
			else
				$arResult["ELEMENT"][$key] = $value;
		}

		//Restore HTML if needed
		if (
			$arParams["DETAIL_TEXT_USE_HTML_EDITOR"]
			&& array_key_exists("DETAIL_TEXT", $arResult["ELEMENT"])
			&& mb_strtolower($arResult["ELEMENT"]["DETAIL_TEXT_TYPE"]) == "html"
		)
			$arResult["ELEMENT"]["DETAIL_TEXT"] = $arResult["ELEMENT"]["~DETAIL_TEXT"];

		if (
			$arParams["PREVIEW_TEXT_USE_HTML_EDITOR"]
			&& array_key_exists("PREVIEW_TEXT", $arResult["ELEMENT"])
			&& mb_strtolower($arResult["ELEMENT"]["PREVIEW_TEXT_TYPE"]) == "html"
		)
			$arResult["ELEMENT"]["PREVIEW_TEXT"] = $arResult["ELEMENT"]["~PREVIEW_TEXT"];

		// load element properties
		$rsElementProperties = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arElement["ID"], $by = "sort", $order = "asc");
		$arResult["ELEMENT_PROPERTIES"] = array();
		$arResult["ELEMENT_PROPERTIES_RECIPIENTS"] = array();
		$arResult["ELEMENT_PROPERTIES_NORECIPIENTS"] = array();

		while ($arElementProperty = $rsElementProperties->Fetch()) {
			if (!array_key_exists($arElementProperty["ID"], $arResult["ELEMENT_PROPERTIES"]))
				$arResult["ELEMENT_PROPERTIES"][$arElementProperty["ID"]] = array();

			if (is_array($arElementProperty["VALUE"])) {
				$htmlvalue = array();
				foreach ($arElementProperty["VALUE"] as $k => $v) {
					if (is_array($v)) {
						$htmlvalue[$k] = array();
						foreach ($v as $k1 => $v1)
							$htmlvalue[$k][$k1] = htmlspecialcharsbx($v1);
					} else {
						$htmlvalue[$k] = htmlspecialcharsbx($v);
					}
				}
			} else {
				$htmlvalue = htmlspecialcharsbx($arElementProperty["VALUE"]);
			}

			$arResult["ELEMENT_PROPERTIES"][$arElementProperty["ID"]][] = array(
				"ID" => htmlspecialcharsbx($arElementProperty["ID"]),
				"VALUE" => $htmlvalue,
				"~VALUE" => $arElementProperty["VALUE"],
				"VALUE_ID" => htmlspecialcharsbx($arElementProperty["PROPERTY_VALUE_ID"]),
				"VALUE_ENUM" => htmlspecialcharsbx($arElementProperty["VALUE_ENUM"]),
			);

			// Обработка RECIPIENTS для вывода в форме
			if ($arElementProperty["CODE"] == "RECIPIENTS" && !empty($arElementProperty["VALUE"])) {
				$arResult["ELEMENT_PROPERTIES_RECIPIENTS"][] = $arElementProperty["VALUE"];
			}

			// Обработка NORECIPIENTS для вывода в форме
			if ($arElementProperty["CODE"] == "NORECIPIENTS" && !empty($arElementProperty["VALUE"])) {
				$arResult["ELEMENT_PROPERTIES_NORECIPIENTS"][] = $arElementProperty["VALUE"];
			}
		}

		// process element property files
		$arResult["ELEMENT_FILES"] = array();
		foreach ($arResult["PROPERTY_LIST"] as $propertyID) {
			$arProperty = $arResult["PROPERTY_LIST_FULL"][$propertyID];
			if ($arProperty["PROPERTY_TYPE"] == "F") {
				$arValues = array();
				if (intval($propertyID) > 0) {
					foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arProperty) {
						$arValues[] = $arProperty["VALUE"];
					}
				} else {
					$arValues[] = $arResult["ELEMENT"][$propertyID];
				}

				foreach ($arValues as $value) {
					if ($arFile = CFile::GetFileArray($value)) {
						$arFile["IS_IMAGE"] = CFile::IsImage($arFile["FILE_NAME"], $arFile["CONTENT_TYPE"]);
						$arResult["ELEMENT_FILES"][$value] = $arFile;
					}
				}
			}
		}

		$bShowForm = true;
	} else {
		$bShowForm = true;
		$arResult["ELEMENT_PROPERTIES_RECIPIENTS"] = array();
		$arResult["ELEMENT_PROPERTIES_NORECIPIENTS"] = array();
	}

	if ($bShowForm) {
		// prepare form data if some errors occured
		if (!empty($arResult["ERRORS"])) {
			foreach ($arUpdateValues as $key => $value) {
				if ($key == "IBLOCK_SECTION") {
					$arResult["ELEMENT"][$key] = array();
					if (!is_array($value)) {
						$arResult["ELEMENT"][$key][] = array("VALUE" => htmlspecialcharsbx($value));
					} else {
						foreach ($value as $vkey => $vvalue) {
							$arResult["ELEMENT"][$key][$vkey] = array("VALUE" => htmlspecialcharsbx($vvalue));
						}
					}
				} elseif ($key == "PROPERTY_VALUES") {
					//Skip
				} elseif ($arResult["PROPERTY_LIST_FULL"][$key]["PROPERTY_TYPE"] == "F") {
					//Skip
				} elseif ($arResult["PROPERTY_LIST_FULL"][$key]["PROPERTY_TYPE"] == "HTML") {
					$arResult["ELEMENT"][$key] = $value;
				} else {
					$arResult["ELEMENT"][$key] = htmlspecialcharsbx($value);
				}
			}

			foreach ($arUpdatePropertyValues as $key => $value) {
				if ($arResult["PROPERTY_LIST_FULL"][$key]["PROPERTY_TYPE"] != "F") {
					$arResult["ELEMENT_PROPERTIES"][$key] = array();
					if (!is_array($value)) {
						$value = array(
							array("VALUE" => $value),
						);
					}
					foreach ($value as $vv) {
						if (is_array($vv)) {
							if (array_key_exists("VALUE", $vv))
								$arResult["ELEMENT_PROPERTIES"][$key][] = array(
									"~VALUE" => $vv["VALUE"],
									"VALUE" => !is_array($vv["VALUE"]) ? htmlspecialcharsbx($vv["VALUE"]) : $vv["VALUE"],
								);
							else
								$arResult["ELEMENT_PROPERTIES"][$key][] = array(
									"~VALUE" => $vv,
									"VALUE" => $vv,
								);
						} else {
							$arResult["ELEMENT_PROPERTIES"][$key][] = array(
								"~VALUE" => $vv,
								"VALUE" => htmlspecialcharsbx($vv),
							);
						}
					}
				}
			}

			// Восстанавливаем RECIPIENTS из ошибки
			if (isset($arRecipients) && !empty($arRecipients)) {
				$arResult["ELEMENT_PROPERTIES_RECIPIENTS"] = $arRecipients;
			}

			// Восстанавливаем NORECIPIENTS из ошибки
			if (isset($arNoRecipients) && !empty($arNoRecipients)) {
				$arResult["ELEMENT_PROPERTIES_NORECIPIENTS"] = $arNoRecipients;
			}
		}

		// prepare captcha
		if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0) {
			$arResult["CAPTCHA_CODE"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
		}

		$arResult["MESSAGE"] = '';
		if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_REQUEST["strIMessage"]) && is_string($_REQUEST["strIMessage"]))
			$arResult["MESSAGE"] = htmlspecialcharsbx($_REQUEST["strIMessage"]);

		$this->includeComponentTemplate();
	}
}
if (!$bAllowAccess && !$bHideAuth) {
	$APPLICATION->AuthForm("");
}

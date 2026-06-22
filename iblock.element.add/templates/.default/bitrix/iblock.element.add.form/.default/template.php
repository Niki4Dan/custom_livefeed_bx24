<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);
?>

<style>
	/* Основные стили */
	.element-form-modern {
		max-width: 900px;
		margin: 0 auto;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	}

	/* Сообщения об ошибках */
	.element-form-modern .error-message {
		background: #f8d7da;
		border-left: 4px solid #dc3545;
		color: #721c24;
		padding: 15px 20px;
		border-radius: 8px;
		margin-bottom: 25px;
		font-size: 14px;
		animation: slideDown 0.3s ease;
	}

	.element-form-modern .error-message ul {
		margin: 10px 0 0 20px;
		padding: 0;
	}

	.element-form-modern .error-message li {
		margin-bottom: 5px;
	}

	/* Сообщение об успехе */
	.element-form-modern .success-message {
		background: #d4edda;
		border-left: 4px solid #28a745;
		color: #155724;
		padding: 15px 20px;
		border-radius: 8px;
		margin-bottom: 25px;
		font-size: 14px;
		animation: slideDown 0.3s ease;
	}

	@keyframes slideDown {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Форма */
	.element-form-modern .modern-form {
		background: #ffffff;
		border-radius: 16px;
		box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
		overflow: hidden;
	}

	.element-form-modern .form-fields {
		padding: 30px;
	}

	/* Группы полей */
	.element-form-modern .form-group {
		margin-bottom: 25px;
		display: flex;
		flex-wrap: wrap;
		animation: fadeInUp 0.4s ease;
		animation-fill-mode: both;
	}

	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(20px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.element-form-modern .form-group:hover {
		background: #fafbfc;
		border-radius: 12px;
		transition: background 0.2s ease;
	}

	.element-form-modern .form-label {
		flex: 0 0 200px;
		padding: 12px 15px 0 0;
		font-weight: 600;
		font-size: 14px;
		color: #1a2a3a;
	}

	.element-form-modern .form-label .required-star {
		color: #dc3545;
		margin-left: 4px;
	}

	.element-form-modern .form-control {
		flex: 1;
		min-width: 200px;
	}

	/* Поля ввода */
	.element-form-modern input[type="text"],
	.element-form-modern input[type="password"],
	.element-form-modern input[type="email"],
	.element-form-modern input[type="number"],
	.element-form-modern textarea,
	.element-form-modern select {
		width: 100%;
		padding: 10px 12px;
		border: 1px solid #d0d7de;
		border-radius: 8px;
		font-size: 14px;
		font-family: inherit;
		transition: all 0.2s ease;
		background: #ffffff;
		color: #1a2a3a;
	}

	.element-form-modern input[type="text"]:focus,
	.element-form-modern input[type="password"]:focus,
	.element-form-modern input[type="email"]:focus,
	.element-form-modern input[type="number"]:focus,
	.element-form-modern textarea:focus,
	.element-form-modern select:focus {
		outline: none;
		border-color: #0066cc;
		box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
	}

	.element-form-modern textarea {
		resize: vertical;
		min-height: 80px;
	}

	.element-form-modern select[multiple] {
		min-height: 100px;
		padding: 8px;
	}

	/* Чекбоксы и радио */
	.element-form-modern .checkbox-group,
	.element-form-modern .radio-group {
		display: flex;
		flex-wrap: wrap;
		gap: 15px;
		padding: 8px 0;
	}

	.element-form-modern .checkbox-item,
	.element-form-modern .radio-item {
		display: inline-flex;
		align-items: center;
		cursor: pointer;
		font-size: 14px;
		color: #2c3e4e;
	}

	.element-form-modern .checkbox-item input,
	.element-form-modern .radio-item input {
		margin-right: 8px;
		cursor: pointer;
		width: 16px;
		height: 16px;
	}

	.element-form-modern .checkbox-item:hover,
	.element-form-modern .radio-item:hover {
		color: #0066cc;
	}

	/* Файлы */
	.element-form-modern .file-input {
		padding: 10px 0;
	}

	.element-form-modern .file-input input[type="file"] {
		padding: 8px;
		border: 1px dashed #cbd5e1;
		border-radius: 8px;
		width: 100%;
		cursor: pointer;
		background: #f8fafc;
	}

	.element-form-modern .file-input input[type="file"]:hover {
		background: #f1f5f9;
		border-color: #0066cc;
	}

	.element-form-modern .existing-file {
		margin-top: 10px;
		padding: 10px;
		background: #f8fafc;
		border-radius: 8px;
		font-size: 13px;
	}

	.element-form-modern .existing-file img {
		max-width: 100%;
		height: auto;
		border-radius: 8px;
		margin-top: 10px;
	}

	.element-form-modern .delete-file {
		margin-top: 10px;
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.element-form-modern .delete-file input[type="checkbox"] {
		width: 16px;
		height: 16px;
		cursor: pointer;
	}

	.element-form-modern .delete-file label {
		cursor: pointer;
		color: #dc3545;
		font-size: 13px;
	}

	/* Календарь */
	.element-form-modern .calendar-wrapper {
		display: flex;
		gap: 10px;
		align-items: center;
		flex-wrap: wrap;
	}

	.element-form-modern .date-hint {
		font-size: 12px;
		color: #7a8a9a;
		margin-top: 5px;
	}

	/* HTML редактор */
	.element-form-modern .html-editor {
		border: 1px solid #d0d7de;
		border-radius: 8px;
		overflow: hidden;
	}

	/* Капча */
	.element-form-modern .captcha-wrapper {
		display: flex;
		flex-direction: column;
		gap: 15px;
	}

	.element-form-modern .captcha-image {
		background: #f8fafc;
		padding: 10px;
		border-radius: 8px;
		display: inline-block;
	}

	.element-form-modern .captcha-image img {
		border-radius: 6px;
	}

	/* Кнопки формы */
	.element-form-modern .form-buttons {
		display: flex;
		gap: 15px;
		padding: 20px 30px;
		background: #f8fafc;
		border-top: 1px solid #eef2f5;
		flex-wrap: wrap;
	}

	.element-form-modern .btn-submit,
	.element-form-modern .btn-apply,
	.element-form-modern .btn-cancel {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 10px 24px;
		border-radius: 8px;
		font-size: 14px;
		font-weight: 500;
		text-decoration: none;
		cursor: pointer;
		transition: all 0.2s ease;
		border: none;
		font-family: inherit;
	}

	.element-form-modern .btn-submit {
		background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
		color: #ffffff;
	}

	.element-form-modern .btn-submit:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
	}

	.element-form-modern .btn-apply {
		background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
		color: #ffffff;
	}

	.element-form-modern .btn-apply:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
	}

	.element-form-modern .btn-cancel {
		background: #6c757d;
		color: #ffffff;
	}

	.element-form-modern .btn-cancel:hover {
		background: #5a6268;
		transform: translateY(-2px);
	}

	/* Календарь */
	.element-form-modern .calendar-wrapper {
		display: flex;
		gap: 10px;
		align-items: center;
		flex-wrap: wrap;
		margin-top: 5px;
	}

	.element-form-modern .calendar-wrapper input.calendar-icon {
		cursor: pointer;
		padding: 6px 12px;
		background: #f0f2f5;
		border: 1px solid #d0d7de;
		border-radius: 6px;
		font-size: 12px;
		transition: all 0.2s ease;
	}

	.element-form-modern .calendar-wrapper input.calendar-icon:hover {
		background: #e4e8ec;
		border-color: #0066cc;
	}

	.element-form-modern .date-hint {
		font-size: 12px;
		color: #7a8a9a;
		margin-top: 5px;
	}

	/* Стили для поля даты */
	.element-form-modern input[type="text"][id^="date_"] {
		font-family: monospace;
		letter-spacing: 0.5px;
	}

	/* Адаптивность */
	@media (max-width: 768px) {
		.element-form-modern .form-fields {
			padding: 20px;
		}

		.element-form-modern .form-group {
			flex-direction: column;
		}

		.element-form-modern .form-label {
			flex: auto;
			padding: 0 0 8px 0;
		}

		.element-form-modern .form-control {
			width: 100%;
		}

		.element-form-modern .form-buttons {
			padding: 20px;
		}

		.element-form-modern .btn-submit,
		.element-form-modern .btn-apply,
		.element-form-modern .btn-cancel {
			flex: 1;
			justify-content: center;
		}

		.element-form-modern .checkbox-group,
		.element-form-modern .radio-group {
			flex-direction: column;
			gap: 8px;
		}
	}

	/* Анимации для полей */
	.element-form-modern .form-group {
		opacity: 0;
		animation: fadeInUp 0.4s ease forwards;
	}

	.element-form-modern .form-group:nth-child(1) {
		animation-delay: 0.05s;
	}

	.element-form-modern .form-group:nth-child(2) {
		animation-delay: 0.1s;
	}

	.element-form-modern .form-group:nth-child(3) {
		animation-delay: 0.15s;
	}

	.element-form-modern .form-group:nth-child(4) {
		animation-delay: 0.2s;
	}

	.element-form-modern .form-group:nth-child(5) {
		animation-delay: 0.25s;
	}

	.element-form-modern .form-group:nth-child(6) {
		animation-delay: 0.3s;
	}

	.element-form-modern .form-group:nth-child(7) {
		animation-delay: 0.35s;
	}

	.element-form-modern .form-group:nth-child(8) {
		animation-delay: 0.4s;
	}

	.element-form-modern .form-group:nth-child(9) {
		animation-delay: 0.45s;
	}

	.element-form-modern .form-group:nth-child(10) {
		animation-delay: 0.5s;
	}


/* Специальные стили для полей даты */
.element-form-modern .date-input-wrapper {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex-wrap: nowrap;
    background: #ffffff;
    border: 1px solid #d0d7de;
    border-radius: 8px;
    padding: 0;
    transition: all 0.2s ease;
}

.element-form-modern .date-input-wrapper:hover {
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.05);
}

.element-form-modern .date-input-wrapper:focus-within {
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

.element-form-modern .date-input-field {
    width: auto !important;
    min-width: 180px;
    max-width: 220px;
    border: none !important;
    padding: 10px 12px !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    margin: 0 !important;
}

.element-form-modern .date-input-field:focus {
    box-shadow: none !important;
}

/* Стили для кнопки календаря */
.element-form-modern .calendar-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    margin: 0 4px 0 0 !important;
    padding: 0 !important;
    background: #f8fafc;
    border-left: 1px solid #d0d7de;
    border-radius: 0 8px 8px 0;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.element-form-modern .calendar-icon:hover {
    background: #e8f0fe;
}

.element-form-modern .calendar-icon img,
.element-form-modern .calendar-icon input {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

/* Скрываем стандартное поле ввода календаря, если оно появляется */
.element-form-modern .date-input-wrapper input.calendar-icon-input {
    display: none;
}

/* Стили для подсказки формата даты */
.element-form-modern .date-hint {
    font-size: 12px;
    color: #7a8a9a;
    margin-top: 5px;
    margin-bottom: 0;
    clear: both;
}

/* Адаптация для мобильных устройств */
@media (max-width: 768px) {
    .element-form-modern .date-input-wrapper {
        width: 100%;
    }
    
    .element-form-modern .date-input-field {
        flex: 1;
        min-width: 150px;
    }
}

/* Стили для контейнера form-control */
.element-form-modern .form-control {
    flex: 1;
    min-width: 200px;
}

/* Чтобы календарь не разрывал строку */
.element-form-modern .calendar-wrapper {
    display: inline-block;
}
</style>

<div class="element-form-modern">

	<? if (!empty($arResult["ERRORS"])): ?>
		<div class="error-message">
			<strong>⚠️ <?= GetMessage("IBLOCK_FORM_ERRORS") ?></strong>
			<ul>
				<? foreach ($arResult["ERRORS"] as $error): ?>
					<li><?= $error ?></li>
				<? endforeach ?>
			</ul>
		</div>
	<? endif; ?>

	<? if ($arResult["MESSAGE"] <> ''): ?>
		<div class="success-message">
			✅ <?= $arResult["MESSAGE"] ?>
		</div>
	<? endif; ?>

	<form name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data" class="modern-form">
		<?= bitrix_sessid_post() ?>
		<? if ($arParams["MAX_FILE_SIZE"] > 0): ?>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?= $arParams["MAX_FILE_SIZE"] ?>" />
		<? endif ?>

		<div class="form-fields">
			<? if (is_array($arResult["PROPERTY_LIST"]) && !empty($arResult["PROPERTY_LIST"])): ?>
				<? foreach ($arResult["PROPERTY_LIST"] as $propertyID): ?>
					<div class="form-group">
						<div class="form-label">
							<? if (intval($propertyID) > 0): ?>
								<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"] ?>
							<? else: ?>
								<?= !empty($arParams["CUSTOM_TITLE_" . $propertyID]) ? $arParams["CUSTOM_TITLE_" . $propertyID] : GetMessage("IBLOCK_FIELD_" . $propertyID) ?>
							<? endif ?>
							<? if (in_array($propertyID, $arResult["PROPERTY_REQUIRED"])): ?>
								<span class="required-star">*</span>
							<? endif ?>
						</div>
						<div class="form-control">
							<?
							// Подготовка типа поля
							if (intval($propertyID) > 0) {
								if (
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "T"
									&&
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] == "1"
								)
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "S";
								elseif (
									(
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "S"
										||
										$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] == "N"
									)
									&&
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] > "1"
								)
									$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "T";
							} elseif (($propertyID == "TAGS") && CModule::IncludeModule('search'))
								$arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"] = "TAGS";

							// Количество полей для множественных свойств
							if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y") {
								$inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$propertyID]) : 0;
								$inputNum += $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE_CNT"];
							} else {
								$inputNum = 1;
							}

							if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"])
								$INPUT_TYPE = "USER_TYPE";
							else
								$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$propertyID]["PROPERTY_TYPE"];

							switch ($INPUT_TYPE):
								case "USER_TYPE":
									for ($i = 0; $i < $inputNum; $i++) {
										if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["~VALUE"] : $arResult["ELEMENT"][$propertyID];
											$description = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["DESCRIPTION"] : "";
										} elseif ($i == 0) {
											$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
											$description = "";
										} else {
											$value = "";
											$description = "";
										}
										echo call_user_func_array(
											$arResult["PROPERTY_LIST_FULL"][$propertyID]["GetPublicEditHTML"],
											array(
												$arResult["PROPERTY_LIST_FULL"][$propertyID],
												array(
													"VALUE" => $value,
													"DESCRIPTION" => $description,
												),
												array(
													"VALUE" => "PROPERTY[" . $propertyID . "][" . $i . "][VALUE]",
													"DESCRIPTION" => "PROPERTY[" . $propertyID . "][" . $i . "][DESCRIPTION]",
													"FORM_NAME" => "iblock_add",
												),
											)
										);
									}
									break;

								case "TAGS":
									$APPLICATION->IncludeComponent(
										"bitrix:search.tags.input",
										"",
										array(
											"VALUE" => $arResult["ELEMENT"][$propertyID],
											"NAME" => "PROPERTY[" . $propertyID . "][0]",
											"TEXT" => 'size="' . $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"] . '" class="tags-input"',
										),
										null,
										array("HIDE_ICONS" => "Y")
									);
									break;

								case "HTML":
									$LHE = new CHTMLEditor;
									$LHE->Show(array(
										'name' => "PROPERTY[" . $propertyID . "][0]",
										'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[" . $propertyID . "][0]"),
										'inputName' => "PROPERTY[" . $propertyID . "][0]",
										'content' => $arResult["ELEMENT"][$propertyID],
										'width' => '100%',
										'minBodyWidth' => 350,
										'normalBodyWidth' => 555,
										'height' => '200',
										'bAllowPhp' => false,
										'limitPhpAccess' => false,
										'autoResize' => true,
										'autoResizeOffset' => 40,
										'useFileDialogs' => false,
										'saveOnBlur' => true,
										'showTaskbars' => false,
										'showNodeNavi' => false,
										'askBeforeUnloadPage' => true,
										'bbCode' => false,
										'siteId' => SITE_ID,
										'controlsMap' => array(
											array('id' => 'Bold', 'compact' => true, 'sort' => 80),
											array('id' => 'Italic', 'compact' => true, 'sort' => 90),
											array('id' => 'Underline', 'compact' => true, 'sort' => 100),
											array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
											array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
											array('id' => 'Color', 'compact' => true, 'sort' => 130),
											array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
											array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
											array('separator' => true, 'compact' => false, 'sort' => 145),
											array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
											array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
											array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
											array('separator' => true, 'compact' => false, 'sort' => 200),
											array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
											array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
											array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
											array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
											array('separator' => true, 'compact' => false, 'sort' => 290),
											array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
											array('id' => 'More', 'compact' => true, 'sort' => 400)
										),
									));
									break;

								case "T":
									for ($i = 0; $i < $inputNum; $i++) {
										if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
										} elseif ($i == 0) {
											$value = intval($propertyID) > 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
										} else {
											$value = "";
										}
							?>
										<textarea cols="<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"] ?>" rows="<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] ?>" name="PROPERTY[<?= $propertyID ?>][<?= $i ?>]" placeholder="<?= htmlspecialcharsbx($arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]) ?>"><?= htmlspecialcharsbx($value) ?></textarea>
										<?
									}
									break;

								case "S":
								case "N":
								case "N":
									for ($i = 0; $i < $inputNum; $i++) {
										if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
											$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
										} elseif ($i == 0) {
											$value = intval($propertyID) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$propertyID]["DEFAULT_VALUE"];
										} else {
											$value = "";
										}

										// Определяем, является ли поле датой
										$isDateField = false;
										$isDateTimeField = false;

										// Проверяем стандартные поля дат
										if (intval($propertyID) <= 0) {
											if ($propertyID == "DATE_ACTIVE_FROM" || $propertyID == "DATE_ACTIVE_TO") {
												$isDateTimeField = true;
											} elseif ($propertyID == "DATE_CREATE" || $propertyID == "TIMESTAMP_X") {
												$isDateTimeField = true;
											}
										}

										// Проверяем пользовательское свойство типа DateTime
										if (intval($propertyID) > 0 && $arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "DateTime") {
											$isDateTimeField = true;
										} elseif (intval($propertyID) > 0 && $arResult["PROPERTY_LIST_FULL"][$propertyID]["USER_TYPE"] == "Date") {
											$isDateField = true;
										}

										// Для полей даты используем специальную обертку
										if ($isDateTimeField || $isDateField): ?>
											<div class="date-input-wrapper">
												<input type="text"
													name="PROPERTY[<?= $propertyID ?>][<?= $i ?>]"
													id="date_<?= $propertyID ?>_<?= $i ?>"
													class="date-input-field"
													size="20"
													value="<?= htmlspecialcharsbx($value) ?>"
													placeholder="<?= htmlspecialcharsbx($arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]) ?>" 
													onclick="document.querySelector('.calendar-icon').click();"
													/>
													
												<?
												$APPLICATION->IncludeComponent(
													'bitrix:main.calendar',
													'',
													array(
														'FORM_NAME' => 'iblock_add',
														'INPUT_NAME' => "PROPERTY[" . $propertyID . "][" . $i . "]",
														'INPUT_VALUE' => $value,
														'SHOW_TIME' => $isDateTimeField ? 'Y' : 'N',
														'INPUT_NAME_FINISH' => '',
														'SHOW_INPUT' => 'N', // Не показываем дополнительное поле ввода
													),
													null,
													array('HIDE_ICONS' => 'Y')
												);
												?>
											</div>
											<div class="date-hint">
												📅 <?= GetMessage("IBLOCK_FORM_DATE_FORMAT") ?>
												<?= $isDateTimeField ? FORMAT_DATETIME : FORMAT_DATE ?>
												(значек календаря после даты в поле кликабельный)
											</div>
										<? else: ?>
											<input type="text"
												name="PROPERTY[<?= $propertyID ?>][<?= $i ?>]"
												size="<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"]; ?>"
												value="<?= htmlspecialcharsbx($value) ?>"
												placeholder="<?= htmlspecialcharsbx($arResult["PROPERTY_LIST_FULL"][$propertyID]["NAME"]) ?>" />
										<? endif; ?>
									<?
									}
									break;

								case "F":
									for ($i = 0; $i < $inputNum; $i++) {
										$value = intval($propertyID) > 0 ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE"] : $arResult["ELEMENT"][$propertyID];
										$valueId = $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] ? $arResult["ELEMENT_PROPERTIES"][$propertyID][$i]["VALUE_ID"] : $i;
									?>
										<div class="file-input">
											<input type="hidden" name="PROPERTY[<?= $propertyID ?>][<?= $valueId ?>]" value="<?= $value ?>" />
											<input type="file" size="<?= $arResult["PROPERTY_LIST_FULL"][$propertyID]["COL_COUNT"] ?>" name="PROPERTY_FILE_<?= $propertyID ?>_<?= $valueId ?>" />

											<? if (!empty($value) && is_array($arResult["ELEMENT_FILES"][$value])): ?>
												<div class="existing-file">
													<? if ($arResult["ELEMENT_FILES"][$value]["IS_IMAGE"]): ?>
														<img src="<?= $arResult["ELEMENT_FILES"][$value]["SRC"] ?>" height="150" alt="" />
													<? else: ?>
														📄 <?= GetMessage("IBLOCK_FORM_FILE_NAME") ?>: <?= $arResult["ELEMENT_FILES"][$value]["ORIGINAL_NAME"] ?><br />
														📊 <?= GetMessage("IBLOCK_FORM_FILE_SIZE") ?>: <?= round($arResult["ELEMENT_FILES"][$value]["FILE_SIZE"] / 1024) ?> КБ<br />
														🔗 <a href="<?= $arResult["ELEMENT_FILES"][$value]["SRC"] ?>" target="_blank"><?= GetMessage("IBLOCK_FORM_FILE_DOWNLOAD") ?></a>
													<? endif; ?>
												</div>
												<div class="delete-file">
													<input type="checkbox" name="DELETE_FILE[<?= $propertyID ?>][<?= $valueId ?>]" id="file_delete_<?= $propertyID ?>_<?= $i ?>" value="Y" />
													<label for="file_delete_<?= $propertyID ?>_<?= $i ?>">🗑️ <?= GetMessage("IBLOCK_FORM_FILE_DELETE") ?></label>
												</div>
											<? endif; ?>
										</div>
										<?
									}
									break;

								case "L":
									if ($arResult["PROPERTY_LIST_FULL"][$propertyID]["LIST_TYPE"] == "C")
										$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "checkbox" : "radio";
									else
										$type = $arResult["PROPERTY_LIST_FULL"][$propertyID]["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

									switch ($type):
										case "checkbox":
										case "radio":
										?>
											<div class="<?= $type ?>-group">
												<?
												foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum) {
													$checked = false;
													if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
														if (is_array($arResult["ELEMENT_PROPERTIES"][$propertyID])) {
															foreach ($arResult["ELEMENT_PROPERTIES"][$propertyID] as $arElEnum) {
																if ($arElEnum["VALUE"] == $key) {
																	$checked = true;
																	break;
																}
															}
														}
													} else {
														if ($arEnum["DEF"] == "Y") $checked = true;
													}
												?>
													<label class="<?= $type ?>-item">
														<input type="<?= $type ?>" name="PROPERTY[<?= $propertyID ?>]<?= $type == "checkbox" ? "[" . $key . "]" : "" ?>" value="<?= $key ?>" <?= $checked ? " checked" : "" ?> />
														<?= htmlspecialcharsbx($arEnum["VALUE"]) ?>
													</label>
												<?
												}
												?>
											</div>
										<?
											break;

										case "dropdown":
										case "multiselect":
										?>
											<select name="PROPERTY[<?= $propertyID ?>]<?= $type == "multiselect" ? "[]\" multiple size=\"" . $arResult["PROPERTY_LIST_FULL"][$propertyID]["ROW_COUNT"] . "\"" : "" ?>">
												<option value=""><? echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA") ?></option>
												<?
												if (intval($propertyID) > 0) $sKey = "ELEMENT_PROPERTIES";
												else $sKey = "ELEMENT";

												foreach ($arResult["PROPERTY_LIST_FULL"][$propertyID]["ENUM"] as $key => $arEnum) {
													$checked = false;
													if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
														foreach ($arResult[$sKey][$propertyID] as $elKey => $arElEnum) {
															if ($key == $arElEnum["VALUE"]) {
																$checked = true;
																break;
															}
														}
													} else {
														if ($arEnum["DEF"] == "Y") $checked = true;
													}
												?>
													<option value="<?= $key ?>" <?= $checked ? " selected" : "" ?>><?= htmlspecialcharsbx($arEnum["VALUE"]) ?></option>
												<?
												}
												?>
											</select>
							<?
											break;
									endswitch;
									break;
							endswitch;
							?>
						</div>
					</div>
				<? endforeach; ?>

				<? if ($arParams["USE_CAPTCHA"] == "Y" && $arParams["ID"] <= 0): ?>
					<div class="form-group">
						<div class="form-label">
							<?= GetMessage("IBLOCK_FORM_CAPTCHA_TITLE") ?>
							<span class="required-star">*</span>
						</div>
						<div class="form-control">
							<div class="captcha-wrapper">
								<input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />
								<div class="captcha-image">
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" />
								</div>
								<input type="text" name="captcha_word" maxlength="50" placeholder="<?= GetMessage("IBLOCK_FORM_CAPTCHA_PROMPT") ?>" />
							</div>
						</div>
					</div>
				<? endif ?>
			<? endif; ?>
		</div>

		<div class="form-buttons">
			<input type="submit" name="iblock_submit" class="btn-submit" value="💾 <?= GetMessage("IBLOCK_FORM_SUBMIT") ?>" />
			<? if ($arParams["LIST_URL"] <> ''): ?>
				<input type="submit" name="iblock_apply" class="btn-apply" value="✅ <?= GetMessage("IBLOCK_FORM_APPLY") ?>" />
				<input type="button" name="iblock_cancel" class="btn-cancel" value="✖️ <? echo GetMessage('IBLOCK_FORM_CANCEL'); ?>" onclick="location.href='<? echo CUtil::JSEscape($arParams["LIST_URL"]) ?>';" />
			<? endif ?>
		</div>
	</form>
</div>
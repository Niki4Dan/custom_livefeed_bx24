<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(false);

global $USER;
$currentUser = CUser::GetByID($USER->GetID())->Fetch();

if (!CModule::IncludeModule("intranet")) {
	CModule::IncludeModule("socialnetwork");
}
?>

<style>
	.b24-feed-container {
		margin: 0 auto;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	}

	.b24-feed-form {
		background: #ffffff;
		border-radius: 16px;
		border: 1px solid #e8e8e8;
		margin-bottom: 24px;
		overflow: hidden;
		transition: box-shadow 0.2s ease;
	}

	.b24-feed-form:hover {
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
	}

	.b24-feed-placeholder {
		padding: 20px;
		cursor: text;
		transition: all 0.2s ease;
	}

	.b24-feed-placeholder:hover {
		background: #f8f9fa;
	}

	.b24-feed-placeholder-text {
		font-size: 16px;
		color: #8a99a8;
		font-weight: 400;
		margin: 0;
	}

	.b24-feed-form-wrapper {
		display: none;
		animation: slideDown 0.3s ease;
	}

	.b24-feed-form-wrapper.active {
		display: block;
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

	@keyframes slideUp {
		from {
			opacity: 1;
			transform: translateY(0);
		}

		to {
			opacity: 0;
			transform: translateY(-10px);
		}
	}

	.b24-feed-form-wrapper.closing {
		animation: slideUp 0.3s ease;
	}

	.b24-toast-container {
		position: fixed;
		top: 20px;
		right: 20px;
		z-index: 10000;
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.b24-toast {
		min-width: 300px;
		max-width: 500px;
		padding: 16px 20px;
		border-radius: 12px;
		font-size: 14px;
		animation: slideInRight 0.3s ease;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
		cursor: pointer;
		transition: opacity 0.3s ease;
	}

	.b24-toast:hover {
		opacity: 0.9;
	}

	.b24-toast-success {
		background: #e6f7e6;
		color: #2d8a2d;
		border-left: 4px solid #2d8a2d;
	}

	.b24-toast-error {
		background: #fee;
		color: #d32f2f;
		border-left: 4px solid #d32f2f;
	}

	.b24-toast-info {
		background: #e8f4fd;
		color: #1a2a3a;
		border-left: 4px solid #2fc6f6;
	}

	.b24-toast-title {
		font-weight: 600;
		margin-bottom: 5px;
	}

	.b24-toast-message {
		font-size: 13px;
	}

	.b24-toast-close {
		float: right;
		margin-left: 10px;
		cursor: pointer;
		font-weight: bold;
		color: #999;
	}

	@keyframes slideInRight {
		from {
			opacity: 0;
			transform: translateX(100%);
		}

		to {
			opacity: 1;
			transform: translateX(0);
		}
	}

	@keyframes slideOutRight {
		from {
			opacity: 1;
			transform: translateX(0);
		}

		to {
			opacity: 0;
			transform: translateX(100%);
		}
	}

	.b24-toast.hiding {
		animation: slideOutRight 0.3s ease forwards;
	}

	.b24-form-group {
		margin-bottom: 20px;
	}

	.b24-form-label {
		display: block;
		margin-bottom: 8px;
		font-weight: 500;
		font-size: 13px;
		color: #1a2a3a;
	}

	.b24-form-label .required-star {
		color: #dc3545;
		margin-left: 4px;
	}

	.b24-html-editor {
		border: 1px solid #e8e8e8;
		border-radius: 10px;
		overflow: hidden;
		position: relative;
	}

	.b24-date-wrapper {
		display: flex;
		align-items: center;
		gap: 10px;
		flex-wrap: wrap;
	}

	.b24-date-input {
		flex: 1;
		padding: 10px 12px;
		border: 1px solid #e8e8e8;
		border-radius: 10px;
		font-size: 14px;
		font-family: inherit;
		background: #ffffff;
		color: #1a2a3a;
		cursor: pointer;
	}

	.b24-date-input:focus {
		outline: none;
		border-color: #2fc6f6;
		box-shadow: 0 0 0 3px rgba(47, 198, 246, 0.1);
	}

	.b24-date-hint {
		font-size: 11px;
		color: #828b95;
		margin-top: 5px;
	}

	.b24-user-selector {
		margin-top: 5px;
	}

	.b24-user-selector .main-user-selector {
		border: 1px solid #e8e8e8;
		border-radius: 10px;
		padding: 8px;
		min-height: 42px;
	}

	.b24-user-selector .main-user-selector:hover {
		border-color: #2fc6f6;
	}

	.b24-form-actions {
		display: flex;
		gap: 12px;
		padding: 16px 20px;
		background: #f5f7f8;
		border-top: 1px solid #e8e8e8;
		flex-wrap: wrap;
	}

	.b24-btn {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 8px 20px;
		border-radius: 24px;
		font-size: 13px;
		font-weight: 500;
		text-decoration: none;
		cursor: pointer;
		transition: all 0.2s ease;
		border: none;
		font-family: inherit;
	}

	.b24-btn-submit {
		background: #2fc6f6;
		color: #ffffff;
	}

	.b24-btn-submit:hover {
		background: #1ea5d8;
		transform: translateY(-1px);
	}

	.b24-btn-cancel {
		background: #e8e8e8;
		color: #1a2a3a;
	}

	.b24-btn-discard {
		background: transparent;
		color: #828b95;
		border: 1px solid #e8e8e8;
	}

	.b24-btn-discard:hover {
		background: #f5f7f8;
		color: #1a2a3a;
	}

	.b24-form-fields {
		padding: 20px;
	}

	.main-user-selector-item {
		background: #e8f4fd;
		border-radius: 16px;
		padding: 4px 10px;
		margin: 2px;
		display: inline-flex;
		align-items: center;
		font-size: 13px;
	}

	.main-user-selector-item-avatar {
		width: 20px;
		height: 20px;
		border-radius: 50%;
		margin-right: 6px;
	}

	.main-user-selector-item-name {
		color: #1a2a3a;
	}

	.main-user-selector-item-remove {
		margin-left: 8px;
		cursor: pointer;
		color: #999;
	}

	/* Стили для файлов */
	.b24-files-uploader {
		background: #f8fafc;
		border: 1px dashed #cbd5e1;
		border-radius: 12px;
		padding: 20px;
		transition: all 0.2s ease;
		cursor: pointer;
	}

	.b24-files-uploader:hover {
		border-color: #2fc6f6;
		background: #f0f7fc;
	}

	.b24-files-uploader.drag-over {
		border-color: #2fc6f6;
		background: #e6f4fa;
	}

	.b24-files-upload-content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		text-align: center;
	}

	.b24-files-upload-icon {
		font-size: 32px;
		margin-bottom: 12px;
	}

	.b24-files-upload-text {
		font-size: 14px;
		color: #1e293b;
		margin-bottom: 8px;
	}

	.b24-files-upload-hint {
		font-size: 12px;
		color: #94a3b8;
	}

	.b24-files-upload-btn {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 8px 20px;
		background: #ffffff;
		color: #2fc6f6;
		border: 1px solid #2fc6f6;
		border-radius: 24px;
		font-size: 13px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s ease;
		margin-top: 12px;
	}

	.b24-files-upload-btn:hover {
		background: #2fc6f6;
		color: #ffffff;
	}

	.b24-files-list {
		margin-top: 16px;
		display: flex;
		flex-direction: column;
		gap: 8px;
	}

	.b24-file-item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 10px 12px;
		background: #ffffff;
		border: 1px solid #e2e8f0;
		border-radius: 10px;
		transition: all 0.2s ease;
	}

	.b24-file-item:hover {
		background: #f8fafc;
		border-color: #cbd5e1;
	}

	.b24-file-preview {
		width: 40px;
		height: 40px;
		border-radius: 8px;
		background: #f1f5f9;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 20px;
		flex-shrink: 0;
	}

	.b24-file-preview-img {
		width: 40px;
		height: 40px;
		border-radius: 8px;
		object-fit: cover;
	}

	.b24-file-info {
		flex: 1;
		min-width: 0;
	}

	.b24-file-name {
		font-size: 13px;
		font-weight: 500;
		color: #1e293b;
		margin-bottom: 2px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.b24-file-size {
		font-size: 11px;
		color: #94a3b8;
	}

	.b24-file-actions {
		display: flex;
		gap: 8px;
		flex-shrink: 0;
	}

	.b24-file-insert,
	.b24-file-remove {
		background: none;
		border: none;
		cursor: pointer;
		padding: 6px;
		border-radius: 6px;
		font-size: 14px;
		transition: all 0.2s ease;
	}

	.b24-file-insert {
		color: #2fc6f6;
	}

	.b24-file-insert:hover {
		background: #e6f4fa;
	}

	.b24-file-remove {
		color: #ef4444;
	}

	.b24-file-remove:hover {
		background: #fee2e2;
	}

	.b24-files-grid {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
		margin-top: 16px;
	}

	.b24-file-preview-large {
		display: inline-flex;
		flex-direction: column;
		align-items: center;
		padding: 12px;
		background: #ffffff;
		border: 1px solid #e2e8f0;
		border-radius: 12px;
		margin: 5px;
		width: 120px;
	}

	.b24-file-preview-large img {
		width: 100px;
		height: 100px;
		object-fit: cover;
		border-radius: 8px;
		margin-bottom: 8px;
	}

	.b24-file-preview-large .b24-file-name {
		font-size: 11px;
		text-align: center;
		width: 100%;
		margin-bottom: 6px;
	}

	/* ========== СТИЛИ ДЛЯ РЕДАКТИРОВАНИЯ ИЗОБРАЖЕНИЙ ========== */
	
	/* Стили для изображений внутри редактора */
	.bx-editor-iframe img {
		max-width: 100% !important;
		height: auto !important;
		border-radius: 8px;
		margin: 8px 0;
	}

	/* Контейнер для редактируемого изображения - НЕ ПОПАДАЕТ В СОХРАНЯЕМЫЙ ТЕКСТ */
	.b24-image-editable {
		position: relative;
		display: inline-block;
		margin: 8px 0;
		max-width: 100%;
		line-height: 0;
		cursor: pointer;
	}

	.b24-image-editable img {
		display: block;
		max-width: 100%;
		height: auto;
		transition: width 0.1s ease, height 0.1s ease;
	}

	.b24-image-editable .image-resize-handle {
		position: absolute;
		bottom: -8px;
		right: -8px;
		width: 18px;
		height: 18px;
		background: #2fc6f6;
		border: 2px solid #ffffff;
		border-radius: 50%;
		cursor: nwse-resize;
		display: none;
		box-shadow: 0 1px 6px rgba(47, 198, 246, 0.4);
		transition: transform 0.2s ease;
		z-index: 10;
	}

	.b24-image-editable .image-resize-handle:hover {
		transform: scale(1.15);
	}

	.b24-image-editable:hover .image-resize-handle {
		display: block;
	}

	.b24-image-editable .image-size-tooltip {
		position: absolute;
		top: -28px;
		left: 50%;
		transform: translateX(-50%);
		background: rgba(26, 42, 58, 0.85);
		color: #ffffff;
		padding: 2px 10px;
		border-radius: 4px;
		font-size: 11px;
		white-space: nowrap;
		display: none;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		pointer-events: none;
		backdrop-filter: blur(4px);
	}

	.b24-image-editable:hover .image-size-tooltip {
		display: block;
	}

	/* Стили для меню изображений - отдельный слой, не входит в содержимое */
	.b24-image-menu-overlay {
		position: fixed !important;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		pointer-events: none;
		z-index: 9999;
	}

	.b24-image-menu {
		position: absolute !important;
		background: rgba(0, 0, 0, 0.85) !important;
		backdrop-filter: blur(8px) !important;
		-webkit-backdrop-filter: blur(8px) !important;
		border-radius: 8px !important;
		padding: 4px !important;
		z-index: 10000 !important;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
		border: 1px solid rgba(255, 255, 255, 0.1) !important;
		min-width: 150px !important;
		pointer-events: auto !important;
		display: none;
	}

	.b24-image-menu-btn {
		display: flex !important;
		align-items: center !important;
		background: transparent !important;
		border: none !important;
		color: #ffffff !important;
		padding: 6px 12px !important;
		border-radius: 6px !important;
		font-size: 13px !important;
		cursor: pointer !important;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
		transition: background 0.2s ease !important;
		width: 100% !important;
		white-space: nowrap !important;
	}

	.b24-image-menu-btn:hover {
		background: rgba(47, 198, 246, 0.3) !important;
	}

	/* Анимация для модального окна */
	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: scale(0.95);
		}
		to {
			opacity: 1;
			transform: scale(1);
		}
	}

	/* Стили для ползунка в модальном окне */
	.b24-resize-slider::-webkit-slider-thumb {
		-webkit-appearance: none;
		width: 20px;
		height: 20px;
		background: #2fc6f6;
		border-radius: 50%;
		cursor: pointer;
		box-shadow: 0 2px 8px rgba(47, 198, 246, 0.4);
		transition: transform 0.2s ease;
	}

	.b24-resize-slider::-webkit-slider-thumb:hover {
		transform: scale(1.1);
	}

	.b24-resize-slider::-moz-range-thumb {
		width: 20px;
		height: 20px;
		background: #2fc6f6;
		border: none;
		border-radius: 50%;
		cursor: pointer;
		box-shadow: 0 2px 8px rgba(47, 198, 246, 0.4);
	}

	.b24-resize-slider::-webkit-slider-runnable-track {
		height: 6px;
		background: linear-gradient(to right, #2fc6f6, #1ea5d8);
		border-radius: 3px;
	}

	.b24-resize-slider::-moz-range-track {
		height: 6px;
		background: linear-gradient(to right, #2fc6f6, #1ea5d8);
		border-radius: 3px;
		border: none;
	}

	/* Стили для инпутов в модальном окне */
	.b24-resize-width:focus,
	.b24-resize-height:focus {
		border-color: #2fc6f6 !important;
		box-shadow: 0 0 0 3px rgba(47, 198, 246, 0.1) !important;
	}

	/* Стили для чекбокса */
	.b24-resize-aspect {
		accent-color: #2fc6f6;
	}

	/* Анимация для меню */
	.b24-image-menu {
		animation: menuFadeIn 0.15s ease;
	}

	@keyframes menuFadeIn {
		from {
			opacity: 0;
			transform: translateY(-4px);
		}
		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Стили для iframe редактора - меню поверх iframe */
	.bx-editor-iframe .b24-image-editable {
		position: relative !important;
		display: inline-block !important;
		margin: 8px 0 !important;
		max-width: 100% !important;
	}

	.bx-editor-iframe .b24-image-editable img {
		display: block !important;
		max-width: 100% !important;
		height: auto !important;
	}

	.bx-editor-iframe .b24-image-editable .image-resize-handle {
		position: absolute !important;
		bottom: -8px !important;
		right: -8px !important;
		width: 18px !important;
		height: 18px !important;
		background: #2fc6f6 !important;
		border: 2px solid #ffffff !important;
		border-radius: 50% !important;
		cursor: nwse-resize !important;
		box-shadow: 0 1px 6px rgba(47, 198, 246, 0.4) !important;
		z-index: 1000 !important;
	}

	.bx-editor-iframe .b24-image-editable .image-size-tooltip {
		position: absolute !important;
		top: -28px !important;
		left: 50% !important;
		transform: translateX(-50%) !important;
		background: rgba(26, 42, 58, 0.85) !important;
		color: #ffffff !important;
		padding: 2px 10px !important;
		border-radius: 4px !important;
		font-size: 11px !important;
		white-space: nowrap !important;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
		pointer-events: none !important;
		backdrop-filter: blur(4px) !important;
		z-index: 1000 !important;
	}

	@media (max-width: 600px) {
		.b24-files-grid {
			justify-content: center;
		}

		.b24-files-upload-area {
			flex-direction: column;
			align-items: stretch;
		}

		.b24-files-upload-btn {
			justify-content: center;
		}

		.b24-file-name {
			max-width: 150px;
		}
	}
</style>

<div class="b24-toast-container" id="toastContainer"></div>

<div class="b24-feed-container">
	<div class="b24-feed-form">
		<div class="b24-feed-placeholder" id="feedPlaceholder">
			<div class="b24-feed-placeholder-text">Написать сообщение ...</div>
		</div>

		<div class="b24-feed-form-wrapper" id="formWrapper">
			<form name="iblock_add" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data" id="b24FeedForm">
				<?= bitrix_sessid_post() ?>
				<input type="hidden" name="PROPERTY[NAME][0]" id="postName" value="" />

				<div class="b24-form-fields">
					<div class="b24-form-group">
						<div class="b24-html-editor" id="editorContainer">
							<?php
							$editorId = 'PREVIEW_TEXT';
							$LHE = new CHTMLEditor;
							$LHE->Show(array(
								'name' => "PROPERTY[PREVIEW_TEXT][0]",
								'id' => $editorId,
								'inputName' => "PROPERTY[PREVIEW_TEXT][0]",
								'content' => $arResult["ELEMENT"]["PREVIEW_TEXT"] ?? '',
								'width' => '100%',
								'height' => '200',
								'bAllowPhp' => false,
								'limitPhpAccess' => false,
								'autoResize' => true,
								'useFileDialogs' => false,
								'saveOnBlur' => true,
								'showTaskbars' => false,
								'bbCode' => false,
								'siteId' => SITE_ID,
								'controlsMap' => array(
									array('id' => 'Bold', 'compact' => true, 'sort' => 80),
									array('id' => 'Italic', 'compact' => true, 'sort' => 90),
									array('id' => 'Underline', 'compact' => true, 'sort' => 100),
									array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
									array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
									array('id' => 'Color', 'compact' => true, 'sort' => 130),
									array('separator' => true, 'compact' => false, 'sort' => 145),
									array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
									array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
									array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
									array('separator' => true, 'compact' => false, 'sort' => 200),
									array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
									/* array('id' => 'InsertImage', 'compact' => false, 'sort' => 220), */
									array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
									array('separator' => true, 'compact' => false, 'sort' => 290),
									/* array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310), */
									array('id' => 'Source', 'compact' => false, 'sort' => 320),
									array('id' => 'FontSelector', 'compact' => false, 'sort' => 70),
									array('id' => 'FontSize', 'compact' => false, 'sort' => 75),
									array('separator' => true, 'compact' => false, 'sort' => 78),
								),
							));
							?>
						</div>
					</div>


					<div class="b24-form-group">
						<div class="b24-form-label">📎 Прикрепленные файлы <span style="font-size: 11px; color: #828b95; font-weight: normal;">(опционально)</span></div>

						<div class="b24-files-uploader" id="filesUploader">
							<div class="b24-files-upload-content">
								<div class="b24-files-upload-icon">📎</div>
								<div class="b24-files-upload-text">Перетащите файлы сюда или</div>
								<label class="b24-files-upload-btn" for="messageFiles">📁 Выбрать файлы</label>
								<input type="file" name="MESSAGE_FILE[]" id="messageFiles" multiple style="display: none;" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
								<div class="b24-files-upload-hint" style="margin-top:20px;">Поддерживаются: JPG, PNG, GIF, WEBP, PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR до 20 МБ</div>
							</div>
						</div>

						<div id="uploadedFilesContainer"></div>
					</div>

					<div class="b24-form-group">
						<div class="b24-form-label">📅 Дата начала публикации <span style="font-size: 11px; color: #828b95; font-weight: normal;">(оставьте пустым для немедленной публикации)</span></div>
						<div class="b24-date-wrapper">
							<input type="text" class="b24-date-input" name="DATE_ACTIVE_FROM" id="dateActiveFrom" value="<?= htmlspecialchars($arResult["ELEMENT"]["DATE_ACTIVE_FROM"] ?? '') ?>" placeholder="Выберите дату и время публикации">
						</div>
						<div class="b24-date-hint">ℹ️ Если указать дату в будущем, сообщение будет опубликовано автоматически в указанное время</div>
					</div>

					<div class="b24-form-group">
						<div class="b24-form-label">👥 Получатели <span class="required-star">*</span></div>
						<div class="b24-user-selector">
							<?
							$arCurrentRecipients = array();
							if (!empty($arResult["ELEMENT_PROPERTIES_RECIPIENTS"])) {
								foreach ($arResult["ELEMENT_PROPERTIES_RECIPIENTS"] as $userId) {
									$arCurrentRecipients[] = 'U' . $userId;
								}
							}
							$APPLICATION->IncludeComponent("bitrix:main.user.selector", "", array(
								"ID" => "recipients_selector",
								"API_VERSION" => 3,
								"LIST" => $arCurrentRecipients,
								"INPUT_NAME" => "PROPERTY[RECIPIENTS][]",
								"USE_SYMBOLIC_ID" => "Y",
								"LAZYLOAD" => "Y",
								"SELECTOR_OPTIONS" => array(
									'enableSearch' => 'Y',
									'enableDepartments' => 'Y',
									'enableUsers' => 'Y',
									'departmentSelectDisable' => 'N',
									'returnOnlyUsers' => 'N'
								)
							));
							?>
						</div>
						<div class="b24-date-hint">ℹ️ Выберите сотрудников или целые отделы. При выборе отдела все его сотрудники автоматически станут получателями.</div>
					</div>

					<div class="b24-form-group">
						<div class="b24-form-label">🚫 Исключить из получателей <span style="font-size: 11px; color: #828b95; font-weight: normal;">(опционально)</span></div>
						<div class="b24-user-selector">
							<?
							$arCurrentNoRecipients = array();
							if (!empty($arResult["ELEMENT_PROPERTIES_NORECIPIENTS"])) {
								foreach ($arResult["ELEMENT_PROPERTIES_NORECIPIENTS"] as $userId) {
									$arCurrentNoRecipients[] = 'U' . $userId;
								}
							}
							$APPLICATION->IncludeComponent("bitrix:main.user.selector", "", array(
								"ID" => "norecipients_selector",
								"API_VERSION" => 3,
								"LIST" => $arCurrentNoRecipients,
								"INPUT_NAME" => "PROPERTY[NORECIPIENTS][]",
								"USE_SYMBOLIC_ID" => "Y",
								"LAZYLOAD" => "Y",
								"SELECTOR_OPTIONS" => array(
									'enableSearch' => 'Y',
									'enableDepartments' => 'Y',
									'enableUsers' => 'Y',
									'departmentSelectDisable' => 'N',
									'returnOnlyUsers' => 'N'
								)
							));
							?>
						</div>
						<div class="b24-date-hint">ℹ️ Выберите сотрудников или отделы, которые НЕ должны получать это сообщение (приоритет над получателями).</div>
					</div>


				</div>

				<div class="b24-form-actions">
					<input type="submit" name="iblock_submit" class="b24-btn b24-btn-submit" value="📝 Опубликовать" />
					<button type="button" class="b24-btn b24-btn-discard" id="discardBtn">✖️ Отменить</button>
					<?php if ($arParams["LIST_URL"] <> ''): ?>
						<input type="button" name="iblock_cancel" class="b24-btn b24-btn-cancel" value="🔙 На список" onclick="location.href='<?= CUtil::JSEscape($arParams["LIST_URL"]) ?>';" />
					<?php endif; ?>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Оверлей для меню изображений - вне содержимого редактора -->
<div class="b24-image-menu-overlay" id="imageMenuOverlay"></div>

<script>
	BX.ready(function() {
		var placeholder = document.getElementById('feedPlaceholder');
		var formWrapper = document.getElementById('formWrapper');
		var discardBtn = document.getElementById('discardBtn');
		var form = document.getElementById('b24FeedForm');
		var calendarInitialized = false;

		// Настройки для файлов
		const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
		const maxFileSize = 20 * 1024 * 1024;
		let uploadedFiles = [];

		function isAllowedExtension(filename) {
			const ext = filename.split('.').pop().toLowerCase();
			return allowedExtensions.includes(ext);
		}

		function isImageFile(filename) {
			const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
			const ext = filename.split('.').pop().toLowerCase();
			return imageExtensions.includes(ext);
		}

		function formatFileSize(bytes) {
			if (bytes === 0) return '0 Bytes';
			const k = 1024;
			const sizes = ['Bytes', 'KB', 'MB', 'GB'];
			const i = Math.floor(Math.log(bytes) / Math.log(k));
			return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
		}

		function getFileIcon(fileName) {
			if (fileName.match(/\.(jpg|jpeg|png|gif|webp)$/i)) return '🖼️';
			if (fileName.match(/\.(pdf)$/i)) return '📄';
			if (fileName.match(/\.(doc|docx)$/i)) return '📝';
			if (fileName.match(/\.(xls|xlsx)$/i)) return '📊';
			if (fileName.match(/\.(zip|rar)$/i)) return '📦';
			return '📎';
		}

		function insertImageToEditor(imageUrl) {
			if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
				for (let i in window.BXHtmlEditor.editors) {
					const editor = window.BXHtmlEditor.editors[i];
					if (editor && editor.id === 'PREVIEW_TEXT') {
						editor.InsertHtml(`<img src="${imageUrl}" style="max-width: 100%; height: auto; border-radius: 8px; margin: 8px 0;">`);
						showToast('Изображение вставлено в сообщение', 'success', '✅ Готово');
						// Инициализируем ресайз после вставки
						setTimeout(function() {
							initImageResizeForEditor();
						}, 300);
						break;
					}
				}
			}
		}

		function previewImage(file, index) {
			return new Promise((resolve) => {
				const reader = new FileReader();
				reader.onload = function(e) {
					uploadedFiles[index].previewUrl = e.target.result;
					resolve();
				};
				reader.readAsDataURL(file);
			});
		}

		function renderUploadedFiles() {
			const container = document.getElementById('uploadedFilesContainer');
			if (!container) return;

			if (uploadedFiles.length === 0) {
				container.innerHTML = '';
				return;
			}

			const images = uploadedFiles.filter(f => isImageFile(f.name));
			const otherFiles = uploadedFiles.filter(f => !isImageFile(f.name));

			let html = '';

			if (images.length > 0) {
				html += '<div class="b24-files-grid">';
				images.forEach((file, idx) => {
					const originalIndex = uploadedFiles.findIndex(f => f === file);
					html += `
					<div class="b24-file-preview-large">
						<img src="${file.previewUrl || ''}" alt="${escapeHtml(file.name)}">
						<div class="b24-file-name">${escapeHtml(file.name.substring(0, 20))}${file.name.length > 20 ? '...' : ''}</div>
						<button type="button" class="b24-file-insert" data-index="${originalIndex}">В текст</button>
						<button type="button" class="b24-file-remove" data-index="${originalIndex}">Удалить</button>
					</div>
				`;
				});
				html += '</div>';
			}

			if (otherFiles.length > 0) {
				html += '<div class="b24-files-list">';
				otherFiles.forEach((file) => {
					const originalIndex = uploadedFiles.findIndex(f => f === file);
					const fileIcon = getFileIcon(file.name);
					html += `
					<div class="b24-file-item">
						<div class="b24-file-preview"><span>${fileIcon}</span></div>
						<div class="b24-file-info">
							<div class="b24-file-name">${escapeHtml(file.name)}</div>
							<div class="b24-file-size">${formatFileSize(file.size)}</div>
						</div>
						<div class="b24-file-actions">
							<button type="button" class="b24-file-remove" data-index="${originalIndex}">🗑️</button>
						</div>
					</div>
				`;
				});
				html += '</div>';
			}

			container.innerHTML = html;

			document.querySelectorAll('#uploadedFilesContainer .b24-file-remove').forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					const index = parseInt(this.dataset.index);
					uploadedFiles.splice(index, 1);
					renderUploadedFiles();
					updateFileInput();
				});
			});

			document.querySelectorAll('#uploadedFilesContainer .b24-file-insert').forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					const index = parseInt(this.dataset.index);
					const file = uploadedFiles[index];
					if (file && file.previewUrl) insertImageToEditor(file.previewUrl);
				});
			});
		}

		function updateFileInput() {
			const form = document.getElementById('b24FeedForm');
			if (!form) return;
			const oldInputs = form.querySelectorAll('input[name="MESSAGE_FILE[]"]');
			oldInputs.forEach(input => input.remove());
			for (let i = 0; i < uploadedFiles.length; i++) {
				const input = document.createElement('input');
				input.type = 'file';
				input.name = 'MESSAGE_FILE[]';
				input.style.display = 'none';
				const dataTransfer = new DataTransfer();
				dataTransfer.items.add(uploadedFiles[i].file);
				input.files = dataTransfer.files;
				form.appendChild(input);
			}
		}

		async function handleFileSelect(files) {
			const fileInput = document.getElementById('messageFiles');
			let hasError = false;
			for (let file of files) {
				if (!isAllowedExtension(file.name)) {
					showToast(`Файл "${file.name}" имеет неподдерживаемое расширение`, 'error', '⚠️ Ошибка');
					hasError = true;
					continue;
				}
				if (file.size > maxFileSize) {
					showToast(`Файл "${file.name}" превышает максимальный размер (20 МБ)`, 'error', '⚠️ Ошибка');
					hasError = true;
					continue;
				}
				const index = uploadedFiles.length;
				uploadedFiles.push({
					file: file,
					name: file.name,
					size: file.size,
					previewUrl: null
				});
				if (isImageFile(file.name)) await previewImage(file, index);
			}
			if (!hasError && files.length > 0) showToast(`Загружено файлов: ${files.length}`, 'success', '✅ Готово');
			renderUploadedFiles();
			updateFileInput();
			if (fileInput) fileInput.value = '';
		}

		function initFileUploader() {
			const fileInput = document.getElementById('messageFiles');
			const uploader = document.getElementById('filesUploader');
			const uploadBtn = document.querySelector('.b24-files-upload-btn');

			if (!fileInput) return;

			if (uploadBtn) {
				uploadBtn.addEventListener('click', function(e) {
					e.stopPropagation();
				});
			}

			if (uploader) {
				uploader.addEventListener('click', function(e) {
					if (!e.target.closest('.b24-files-upload-btn')) {
						fileInput.click();
					}
				});

				uploader.addEventListener('dragover', function(e) {
					e.preventDefault();
					uploader.classList.add('drag-over');
				});

				uploader.addEventListener('dragleave', function(e) {
					e.preventDefault();
					uploader.classList.remove('drag-over');
				});

				uploader.addEventListener('drop', function(e) {
					e.preventDefault();
					uploader.classList.remove('drag-over');
					const files = Array.from(e.dataTransfer.files);
					handleFileSelect(files);
				});
			}

			fileInput.addEventListener('change', function(e) {
				handleFileSelect(Array.from(e.target.files));
				fileInput.value = '';
			});
		}

		function escapeHtml(text) {
			if (!text) return '';
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}

		function showToast(message, type = 'info', title = '') {
			var container = document.getElementById('toastContainer');
			if (!container) return;
			var toast = document.createElement('div');
			toast.className = 'b24-toast b24-toast-' + type;
			var titles = {
				'success': '✅ Успех',
				'error': '⚠️ Ошибка',
				'info': 'ℹ️ Информация'
			};
			var toastTitle = title || titles[type] || titles.info;
			toast.innerHTML = '<div class="b24-toast-close">×</div><div class="b24-toast-title">' + toastTitle + '</div><div class="b24-toast-message">' + message + '</div>';
			container.appendChild(toast);
			var closeBtn = toast.querySelector('.b24-toast-close');
			closeBtn.addEventListener('click', function() {
				hideToast(toast);
			});
			toast.addEventListener('click', function(e) {
				if (e.target !== closeBtn) hideToast(toast);
			});
			setTimeout(function() {
				hideToast(toast);
			}, 5000);
		}

		function hideToast(toast) {
			if (toast.classList.contains('hiding')) return;
			toast.classList.add('hiding');
			setTimeout(function() {
				if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
			}, 300);
		}

		function resetForm() {
			if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
				for (var i in window.BXHtmlEditor.editors) {
					var editor = window.BXHtmlEditor.editors[i];
					if (editor && editor.id === 'PREVIEW_TEXT') editor.SetContent('', false);
				}
			}
			var textarea = document.querySelector('textarea[name="PROPERTY[PREVIEW_TEXT][0]"]');
			if (textarea) textarea.value = '';
			var dateInput = document.getElementById('dateActiveFrom');
			if (dateInput) dateInput.value = '';
			uploadedFiles = [];
			renderUploadedFiles();
			updateFileInput();
			if (window.BXMainUserSelector) {
				if (window.BXMainUserSelector.recipients_selector) window.BXMainUserSelector.recipients_selector.clearSelectedItems();
				if (window.BXMainUserSelector.norecipients_selector) window.BXMainUserSelector.norecipients_selector.clearSelectedItems();
			}
			// Удаляем все меню изображений
			hideAllEditorMenus();
		}

		function closeForm(reset = true) {
			if (reset) resetForm();
			formWrapper.classList.add('closing');
			setTimeout(function() {
				formWrapper.classList.remove('active', 'closing');
				placeholder.style.display = 'flex';
			}, 300);
		}

		function openForm() {
			placeholder.style.display = 'none';
			formWrapper.classList.add('active');
			initFileUploader();
			if (!calendarInitialized && BX && BX.calendar) {
				var dateInput = document.getElementById('dateActiveFrom');
				if (dateInput) {
					dateInput.onclick = function() {
						BX.calendar({
							node: this,
							field: this,
							form: 'b24FeedForm',
							bTime: true,
							bHideTime: false,
							zIndex: 1000
						});
					};
					calendarInitialized = true;
				}
			}
			setTimeout(function() {
				if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
					for (var i in window.BXHtmlEditor.editors) {
						var editor = window.BXHtmlEditor.editors[i];
						if (editor && editor.id === 'PREVIEW_TEXT') {
							editor.Focus();
							break;
						}
					}
				}
				// Инициализируем ресайз изображений после открытия формы
				setTimeout(function() {
					initImageResizeForEditor();
				}, 500);
			}, 100);
		}

		<?php if (!empty($arResult["ERRORS"])): ?>
			openForm();
			<?php foreach ($arResult["ERRORS"] as $error): ?>
				showToast('<?= CUtil::JSEscape($error) ?>', 'error');
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ($arResult["MESSAGE"] <> ''): ?>
			showToast('<?= CUtil::JSEscape($arResult["MESSAGE"]) ?>', 'success');
			closeForm(true);
		<?php endif; ?>

		if (!placeholder || !formWrapper) return;

		placeholder.addEventListener('click', function() {
			openForm();
		});

		if (discardBtn) discardBtn.addEventListener('click', function(e) {
			e.preventDefault();
			closeForm(true);
		});

		function createNameFromText(text) {
			if (!text) return 'Запись';
			let plainText = text.replace(/<[^>]*>/g, '').replace(/\[(\/?)[A-Z0-9=\s]+\]/gi, '').trim();
			if (!plainText) return 'Запись';
			let words = plainText.split(/\s+/);
			let name = words.slice(0, 5).join(' ');
			if (name.length > 30) name = name.substring(0, 30) + '...';
			return name;
		}

		if (form) {
			form.addEventListener('submit', function(e) {
				// Очищаем HTML от оберток перед сохранением
				cleanEditorContent();

				var previewText = '';
				if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
					for (var i in window.BXHtmlEditor.editors) {
						var editor = window.BXHtmlEditor.editors[i];
						if (editor && editor.id === 'PREVIEW_TEXT') {
							previewText = editor.GetContent();
							break;
						}
					}
				}
				if (!previewText) {
					var textarea = document.querySelector('textarea[name="PROPERTY[PREVIEW_TEXT][0]"]');
					if (textarea) previewText = textarea.value;
				}
				if (!previewText.trim()) {
					e.preventDefault();
					showToast('Пожалуйста, введите текст сообщения', 'error');
					return false;
				}
				var name = createNameFromText(previewText);
				var nameInput = document.getElementById('postName');
				if (nameInput) nameInput.value = name;
			});
		}
	});

	// ========== ФУНКЦИИ ДЛЯ РЕДАКТИРОВАНИЯ ИЗОБРАЖЕНИЙ В РЕДАКТОРЕ СООБЩЕНИЙ ==========

	// Глобальное состояние для ресайза
	let editorImageResizeState = {
		currentImage: null,
		currentWidth: 0,
		currentHeight: 0,
		aspectRatio: 0,
		modal: null,
		slider: null,
		widthInput: null,
		heightInput: null,
		preview: null,
		sizeDisplay: null
	};

	// Проверяем, находится ли изображение внутри редактора сообщения
	function isInsideMessageEditor(img) {
		let parent = img.parentElement;
		while (parent) {
			if (parent.classList && parent.classList.contains('b24-html-editor')) {
				return true;
			}
			if (parent.tagName === 'IFRAME' && parent.classList && parent.classList.contains('bx-editor-iframe')) {
				return true;
			}
			parent = parent.parentElement;
		}
		
		// Проверяем через iframe
		try {
			const iframes = document.querySelectorAll('.bx-editor-iframe');
			for (let iframe of iframes) {
				try {
					const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
					if (iframeDoc && iframeDoc.contains(img)) {
						return true;
					}
				} catch(e) {}
			}
		} catch(e) {}
		
		return false;
	}

	// Получить iframe редактора
	function getEditorIframe() {
		const iframes = document.querySelectorAll('.bx-editor-iframe');
		for (let iframe of iframes) {
			try {
				const doc = iframe.contentDocument || iframe.contentWindow.document;
				if (doc && doc.body) {
					return iframe;
				}
			} catch(e) {}
		}
		return null;
	}

	// Получить документ внутри iframe
	function getEditorDocument() {
		const iframe = getEditorIframe();
		if (iframe) {
			try {
				return iframe.contentDocument || iframe.contentWindow.document;
			} catch(e) {}
		}
		return null;
	}

	// Создание меню для изображения (в оверлее, а не в содержимом)
	function createEditorImageMenu(img) {
		if (!isInsideMessageEditor(img)) {
			return null;
		}
		
		// Проверяем, есть ли уже меню для этого изображения
		const menuId = 'menu_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5);
		
		const menu = document.createElement('div');
		menu.className = 'b24-image-menu';
		menu.id = menuId;
		menu.dataset.targetImg = img.src || '';
		menu.style.cssText = `
			position: fixed !important;
			background: rgba(0, 0, 0, 0.85) !important;
			backdrop-filter: blur(8px) !important;
			-webkit-backdrop-filter: blur(8px) !important;
			border-radius: 8px !important;
			padding: 4px !important;
			z-index: 10000 !important;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
			border: 1px solid rgba(255, 255, 255, 0.1) !important;
			min-width: 150px !important;
			pointer-events: auto !important;
			display: none;
		`;
		
		const resizeBtn = document.createElement('button');
		resizeBtn.className = 'b24-image-menu-btn';
		resizeBtn.innerHTML = `
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
				<polyline points="15 3 21 3 21 9"></polyline>
				<polyline points="9 21 3 21 3 15"></polyline>
				<line x1="21" y1="3" x2="14" y2="10"></line>
				<line x1="3" y1="21" x2="10" y2="14"></line>
			</svg>
			Изменить размер
		`;
		resizeBtn.style.cssText = `
			display: flex !important;
			align-items: center !important;
			background: transparent !important;
			border: none !important;
			color: #ffffff !important;
			padding: 6px 12px !important;
			border-radius: 6px !important;
			font-size: 13px !important;
			cursor: pointer !important;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
			transition: background 0.2s ease !important;
			width: 100% !important;
			white-space: nowrap !important;
		`;
		resizeBtn.addEventListener('mouseenter', function() {
			this.style.background = 'rgba(47, 198, 246, 0.3)';
		});
		resizeBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});
		
		const resetBtn = document.createElement('button');
		resetBtn.className = 'b24-image-menu-btn';
		resetBtn.innerHTML = `
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
				<path d="M3 12a9 9 0 1 0 9-9m0 0v6m0-6h-6"></path>
			</svg>
			Сбросить
		`;
		resetBtn.style.cssText = `
			display: flex !important;
			align-items: center !important;
			background: transparent !important;
			border: none !important;
			color: #ffffff !important;
			padding: 6px 12px !important;
			border-radius: 6px !important;
			font-size: 13px !important;
			cursor: pointer !important;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
			transition: background 0.2s ease !important;
			width: 100% !important;
			white-space: nowrap !important;
		`;
		resetBtn.addEventListener('mouseenter', function() {
			this.style.background = 'rgba(239, 68, 68, 0.3)';
		});
		resetBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});
		
		menu.appendChild(resizeBtn);
		menu.appendChild(resetBtn);
		
		// Добавляем меню в оверлей
		const overlay = document.getElementById('imageMenuOverlay');
		if (overlay) {
			overlay.appendChild(menu);
		} else {
			document.body.appendChild(menu);
		}
		
		// Сохраняем ссылку на изображение в меню
		menu._targetImg = img;
		
		resizeBtn.addEventListener('click', function(e) {
			e.stopPropagation();
			e.preventDefault();
			const targetImg = menu._targetImg;
			if (targetImg) {
				openEditorResizeModal(targetImg);
			}
			hideAllEditorMenus();
		});
		
		resetBtn.addEventListener('click', function(e) {
			e.stopPropagation();
			e.preventDefault();
			const targetImg = menu._targetImg;
			if (targetImg) {
				resetEditorImageSize(targetImg);
			}
			hideAllEditorMenus();
		});
		
		return menu;
	}

	function hideAllEditorMenus() {
		document.querySelectorAll('.b24-image-menu').forEach(function(menu) {
			menu.style.display = 'none';
		});
	}

	function showEditorImageMenu(img) {
		if (!isInsideMessageEditor(img)) {
			return;
		}
		
		// Находим существующее меню для этого изображения
		let menu = null;
		const menus = document.querySelectorAll('.b24-image-menu');
		for (let m of menus) {
			if (m._targetImg === img) {
				menu = m;
				break;
			}
		}
		
		if (!menu) {
			menu = createEditorImageMenu(img);
			if (!menu) return;
		}
		
		// Позиционируем меню относительно изображения
		const rect = img.getBoundingClientRect();
		menu.style.left = (rect.right - 10) + 'px';
		menu.style.top = (rect.top - 10) + 'px';
		menu.style.display = 'block';
		
		// Скрываем другие меню
		document.querySelectorAll('.b24-image-menu').forEach(function(m) {
			if (m !== menu) {
				m.style.display = 'none';
			}
		});
	}

	function resetEditorImageSize(img) {
		if (img.naturalWidth && img.naturalHeight) {
			img.style.width = img.naturalWidth + 'px';
			img.style.height = img.naturalHeight + 'px';
			showToast('Размер сброшен до оригинального', 'success', '✅ Готово');
		} else {
			img.style.width = '';
			img.style.height = '';
			showToast('Размер сброшен', 'success', '✅ Готово');
		}
	}

	function openEditorResizeModal(img) {
		const width = img.offsetWidth || img.clientWidth || img.naturalWidth || 100;
		const height = img.offsetHeight || img.clientHeight || img.naturalHeight || 100;
		const aspectRatio = width / height;
		
		editorImageResizeState.currentImage = img;
		editorImageResizeState.currentWidth = width;
		editorImageResizeState.currentHeight = height;
		editorImageResizeState.aspectRatio = aspectRatio;
		
		if (editorImageResizeState.modal) {
			updateEditorModalValues();
			editorImageResizeState.modal.style.display = 'flex';
			editorImageResizeState.modal.style.opacity = '1';
			editorImageResizeState.modal.style.transform = 'scale(1)';
			return;
		}
		
		createEditorResizeModal();
	}

	function updateEditorModalValues() {
		const img = editorImageResizeState.currentImage;
		if (!img) return;
		
		const width = img.offsetWidth || img.clientWidth || img.naturalWidth || 100;
		const height = img.offsetHeight || img.clientHeight || img.naturalHeight || 100;
		
		editorImageResizeState.currentWidth = width;
		editorImageResizeState.currentHeight = height;
		editorImageResizeState.aspectRatio = width / height;
		
		if (editorImageResizeState.preview) {
			editorImageResizeState.preview.src = img.src;
			editorImageResizeState.preview.style.width = width + 'px';
			editorImageResizeState.preview.style.height = height + 'px';
			editorImageResizeState.preview.style.maxWidth = '100%';
			editorImageResizeState.preview.style.maxHeight = '300px';
			editorImageResizeState.preview.style.objectFit = 'contain';
		}
		
		if (editorImageResizeState.widthInput) {
			editorImageResizeState.widthInput.value = Math.round(width);
		}
		
		if (editorImageResizeState.heightInput) {
			editorImageResizeState.heightInput.value = Math.round(height);
		}
		
		if (editorImageResizeState.slider) {
			editorImageResizeState.slider.value = 100;
		}
		
		if (editorImageResizeState.sizeDisplay) {
			editorImageResizeState.sizeDisplay.textContent = Math.round(width) + ' × ' + Math.round(height);
		}
	}

	function createEditorResizeModal() {
		const modal = document.createElement('div');
		modal.className = 'b24-resize-modal';
		modal.style.cssText = `
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.6);
			backdrop-filter: blur(4px);
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 99999;
			padding: 20px;
			animation: fadeIn 0.3s ease;
		`;
		
		const content = document.createElement('div');
		content.style.cssText = `
			background: #ffffff;
			border-radius: 16px;
			padding: 32px;
			max-width: 500px;
			width: 100%;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			position: relative;
			max-height: 90vh;
			overflow-y: auto;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
		`;
		
		const header = document.createElement('div');
		header.style.cssText = `
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
		`;
		header.innerHTML = `
			<h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #1a2a3a;">Изменение размера изображения</h3>
			<button class="b24-resize-close" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; padding: 4px 8px; border-radius: 8px; transition: all 0.2s ease; line-height: 1;">×</button>
		`;
		content.appendChild(header);
		
		const previewContainer = document.createElement('div');
		previewContainer.style.cssText = `
			background: #f8fafc;
			border-radius: 12px;
			padding: 16px;
			margin-bottom: 20px;
			border: 1px solid #e2e8f0;
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100px;
			overflow: hidden;
		`;
		const preview = document.createElement('img');
		preview.className = 'b24-resize-preview';
		preview.style.cssText = `
			max-width: 100%;
			max-height: 300px;
			object-fit: contain;
			border-radius: 8px;
		`;
		previewContainer.appendChild(preview);
		content.appendChild(previewContainer);
		
		const sliderSection = document.createElement('div');
		sliderSection.style.cssText = `margin-bottom: 20px;`;
		
		const sliderLabel = document.createElement('div');
		sliderLabel.style.cssText = `
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 8px;
		`;
		sliderLabel.innerHTML = `
			<span style="font-size: 14px; font-weight: 500; color: #1a2a3a;">Размер</span>
			<span class="b24-resize-size-display" style="font-size: 14px; color: #2fc6f6; font-weight: 600;">100 × 100</span>
		`;
		sliderSection.appendChild(sliderLabel);
		
		const slider = document.createElement('input');
		slider.className = 'b24-resize-slider';
		slider.type = 'range';
		slider.min = '10';
		slider.max = '200';
		slider.value = '100';
		slider.style.cssText = `
			width: 100%;
			height: 6px;
			-webkit-appearance: none;
			background: linear-gradient(to right, #2fc6f6, #1ea5d8);
			border-radius: 3px;
			outline: none;
			cursor: pointer;
		`;
		sliderSection.appendChild(slider);
		content.appendChild(sliderSection);
		
		const inputsSection = document.createElement('div');
		inputsSection.style.cssText = `
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 12px;
			margin-bottom: 20px;
		`;
		
		const widthGroup = document.createElement('div');
		widthGroup.innerHTML = `
			<label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 4px;">Ширина (px)</label>
			<input type="number" class="b24-resize-width" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s ease;" min="10">
		`;
		inputsSection.appendChild(widthGroup);
		
		const heightGroup = document.createElement('div');
		heightGroup.innerHTML = `
			<label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 4px;">Высота (px)</label>
			<input type="number" class="b24-resize-height" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s ease;" min="10">
		`;
		inputsSection.appendChild(heightGroup);
		content.appendChild(inputsSection);
		
		const aspectCheckbox = document.createElement('div');
		aspectCheckbox.style.cssText = `
			display: flex;
			align-items: center;
			gap: 8px;
			margin-bottom: 20px;
		`;
		aspectCheckbox.innerHTML = `
			<input type="checkbox" class="b24-resize-aspect" checked id="editorResizeAspect" style="width: 18px; height: 18px; cursor: pointer; accent-color: #2fc6f6;">
			<label for="editorResizeAspect" style="font-size: 14px; color: #1a2a3a; cursor: pointer;">Сохранять пропорции</label>
		`;
		content.appendChild(aspectCheckbox);
		
		const actions = document.createElement('div');
		actions.style.cssText = `
			display: flex;
			gap: 8px;
			justify-content: flex-end;
			border-top: 1px solid #f0f2f4;
			padding-top: 16px;
		`;
		
		const cancelBtn = document.createElement('button');
		cancelBtn.textContent = 'Отмена';
		cancelBtn.style.cssText = `
			padding: 8px 20px;
			background: none;
			border: 1px solid #e2e8f0;
			border-radius: 8px;
			cursor: pointer;
			font-size: 14px;
			color: #64748b;
			transition: all 0.2s ease;
		`;
		
		const applyBtn = document.createElement('button');
		applyBtn.textContent = 'Применить';
		applyBtn.style.cssText = `
			padding: 8px 24px;
			background: #2fc6f6;
			border: none;
			border-radius: 8px;
			cursor: pointer;
			font-size: 14px;
			font-weight: 500;
			color: #ffffff;
			transition: all 0.2s ease;
		`;
		
		actions.appendChild(cancelBtn);
		actions.appendChild(applyBtn);
		content.appendChild(actions);
		
		modal.appendChild(content);
		document.body.appendChild(modal);
		
		editorImageResizeState.modal = modal;
		editorImageResizeState.slider = slider;
		editorImageResizeState.widthInput = widthGroup.querySelector('.b24-resize-width');
		editorImageResizeState.heightInput = heightGroup.querySelector('.b24-resize-height');
		editorImageResizeState.preview = preview;
		editorImageResizeState.sizeDisplay = sliderLabel.querySelector('.b24-resize-size-display');
		
		updateEditorModalValues();
		
		const closeBtn = header.querySelector('.b24-resize-close');
		closeBtn.addEventListener('click', function() {
			closeEditorResizeModal();
		});
		closeBtn.addEventListener('mouseenter', function() {
			this.style.background = '#f1f5f9';
		});
		closeBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});
		
		cancelBtn.addEventListener('click', function() {
			closeEditorResizeModal();
		});
		cancelBtn.addEventListener('mouseenter', function() {
			this.style.background = '#f8fafc';
		});
		cancelBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});
		
		applyBtn.addEventListener('click', function() {
			applyEditorResize();
		});
		applyBtn.addEventListener('mouseenter', function() {
			this.style.background = '#1ea5d8';
			this.style.transform = 'translateY(-1px)';
		});
		applyBtn.addEventListener('mouseleave', function() {
			this.style.background = '#2fc6f6';
			this.style.transform = 'none';
		});
		
		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				closeEditorResizeModal();
			}
		});
		
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && modal.style.display === 'flex') {
				closeEditorResizeModal();
			}
		});
		
		slider.addEventListener('input', function() {
			const percent = parseInt(this.value) / 100;
			const newWidth = Math.round(editorImageResizeState.currentWidth * percent);
			const newHeight = Math.round(editorImageResizeState.currentHeight * percent);
			
			editorImageResizeState.widthInput.value = newWidth;
			editorImageResizeState.heightInput.value = newHeight;
			editorImageResizeState.sizeDisplay.textContent = newWidth + ' × ' + newHeight;
			
			editorImageResizeState.preview.style.width = newWidth + 'px';
			editorImageResizeState.preview.style.height = newHeight + 'px';
		});
		
		editorImageResizeState.widthInput.addEventListener('input', function() {
			const newWidth = parseInt(this.value) || 0;
			const aspectCheckbox = document.querySelector('#editorResizeAspect');
			
			if (aspectCheckbox && aspectCheckbox.checked && newWidth > 0) {
				const newHeight = Math.round(newWidth / editorImageResizeState.aspectRatio);
				editorImageResizeState.heightInput.value = newHeight;
				editorImageResizeState.sizeDisplay.textContent = newWidth + ' × ' + newHeight;
				
				editorImageResizeState.preview.style.width = newWidth + 'px';
				editorImageResizeState.preview.style.height = newHeight + 'px';
			}
		});
		
		editorImageResizeState.heightInput.addEventListener('input', function() {
			const newHeight = parseInt(this.value) || 0;
			const aspectCheckbox = document.querySelector('#editorResizeAspect');
			
			if (aspectCheckbox && aspectCheckbox.checked && newHeight > 0) {
				const newWidth = Math.round(newHeight * editorImageResizeState.aspectRatio);
				editorImageResizeState.widthInput.value = newWidth;
				editorImageResizeState.sizeDisplay.textContent = newWidth + ' × ' + newHeight;
				
				editorImageResizeState.preview.style.width = newWidth + 'px';
				editorImageResizeState.preview.style.height = newHeight + 'px';
			}
		});
	}

	function closeEditorResizeModal() {
		if (editorImageResizeState.modal) {
			editorImageResizeState.modal.style.opacity = '0';
			editorImageResizeState.modal.style.transform = 'scale(0.95)';
			setTimeout(function() {
				editorImageResizeState.modal.style.display = 'none';
				editorImageResizeState.modal.style.opacity = '1';
				editorImageResizeState.modal.style.transform = 'scale(1)';
			}, 200);
		}
	}

	function applyEditorResize() {
		const img = editorImageResizeState.currentImage;
		if (!img) return;
		
		const width = parseInt(editorImageResizeState.widthInput.value) || 0;
		const height = parseInt(editorImageResizeState.heightInput.value) || 0;
		
		if (width < 10 || height < 10) {
			showToast('Размер должен быть не менее 10px', 'error', '⚠️ Ошибка');
			return;
		}
		
		img.style.width = width + 'px';
		img.style.height = height + 'px';
		
		showToast('Размер изображения изменен', 'success', '✅ Готово');
		closeEditorResizeModal();
	}

	// Очистка содержимого редактора от оберток перед сохранением
	function cleanEditorContent() {
		// Получаем содержимое редактора
		let content = '';
		if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
			for (let i in window.BXHtmlEditor.editors) {
				const editor = window.BXHtmlEditor.editors[i];
				if (editor && editor.id === 'PREVIEW_TEXT') {
					content = editor.GetContent();
					break;
				}
			}
		}
		if (!content) {
			const textarea = document.querySelector('textarea[name="PROPERTY[PREVIEW_TEXT][0]"]');
			if (textarea) content = textarea.value;
		}
		
		if (!content) return;
		
		// Создаем временный DOM для очистки
		const tempDiv = document.createElement('div');
		tempDiv.innerHTML = content;
		
		// Удаляем все обертки .b24-image-editable, но сохраняем img
		const wrappers = tempDiv.querySelectorAll('.b24-image-editable');
		wrappers.forEach(function(wrapper) {
			const img = wrapper.querySelector('img');
			if (img) {
				// Заменяем обертку на само изображение
				wrapper.parentNode.replaceChild(img, wrapper);
			} else {
				// Если нет изображения, просто удаляем обертку
				wrapper.parentNode.removeChild(wrapper);
			}
		});
		
		// Очищаем от лишних стилей у изображений
		const images = tempDiv.querySelectorAll('img');
		images.forEach(function(img) {
			// Убираем inline стили, оставляем только width и height если они заданы
			const width = img.style.width;
			const height = img.style.height;
			img.style.cssText = '';
			if (width && width !== 'auto') {
				img.style.width = width;
			}
			if (height && height !== 'auto') {
				img.style.height = height;
			}
			// Убираем атрибуты, которые могли появиться
			img.removeAttribute('data-resizable');
		});
		
		// Получаем очищенное содержимое
		const cleanContent = tempDiv.innerHTML;
		
		// Обновляем содержимое редактора
		if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
			for (let i in window.BXHtmlEditor.editors) {
				const editor = window.BXHtmlEditor.editors[i];
				if (editor && editor.id === 'PREVIEW_TEXT') {
					editor.SetContent(cleanContent, false);
					break;
				}
			}
		}
		const textarea = document.querySelector('textarea[name="PROPERTY[PREVIEW_TEXT][0]"]');
		if (textarea) {
			textarea.value = cleanContent;
		}
	}

	// Обработка изображений в редакторе
	function processEditorImages(images) {
		images.forEach(function(img) {
			if (img.dataset.resizable === 'true' || img.dataset.resizable === 'skipped') {
				return;
			}
			
			if (img.parentElement && img.parentElement.classList.contains('b24-image-editable')) {
				return;
			}
			
			const inEditor = isInsideMessageEditor(img);
			if (!inEditor) {
				img.dataset.resizable = 'skipped';
				return;
			}
			
			const parent = img.parentNode;
			
			// Оборачиваем изображение в контейнер для интерактивности (НЕ сохраняется в БД)
			const container = document.createElement('div');
			container.className = 'b24-image-editable';
			container.style.cssText = `
				position: relative;
				display: inline-block;
				margin: 8px 0;
				max-width: 100%;
				line-height: 0;
				cursor: pointer;
			`;
			
			parent.insertBefore(container, img);
			container.appendChild(img);
			
			img.style.maxWidth = '100%';
			img.style.height = 'auto';
			
			// Обработчик наведения - показываем меню
			let hideTimeout = null;
			
			container.addEventListener('mouseenter', function(e) {
				if (hideTimeout) {
					clearTimeout(hideTimeout);
					hideTimeout = null;
				}
				if (!isInsideMessageEditor(img)) {
					return;
				}
/* 				showEditorImageMenu(img); */
				container.style.outline = '2px dashed #2fc6f6';
				container.style.outlineOffset = '2px';
			});
			
			container.addEventListener('mouseleave', function(e) {
				// Не скрываем сразу, даем время на наведение на меню
				hideTimeout = setTimeout(function() {
					// Проверяем, наведено ли мышь на меню
					const menu = document.querySelector('.b24-image-menu');
					if (menu && menu._targetImg === img) {
						const isHoveringMenu = menu.matches(':hover');
						if (!isHoveringMenu) {
							menu.style.display = 'none';
						} else {
							// Если мышь на меню, проверяем еще раз через 300ms
							hideTimeout = setTimeout(function() {
								if (menu && !menu.matches(':hover')) {
									menu.style.display = 'none';
								}
							}, 300);
						}
					} else {
						// Скрываем все меню
						hideAllEditorMenus();
					}
					container.style.outline = '';
					container.style.outlineOffset = '';
				}, 300);
			});
			
			// Клик по изображению открывает модальное окно
			img.addEventListener('click', function(e) {
				e.stopPropagation();
				openEditorResizeModal(img);
				// Скрываем меню после клика
				setTimeout(function() {
					hideAllEditorMenus();
				}, 100);
			});
			
			img.dataset.resizable = 'true';
			console.log('✅ Изображение в редакторе сообщения обработано');
		});
	}

	function initImageResizeForEditor() {
		console.log('🔄 initImageResizeForEditor вызван');
		
		// Основной документ - ищем изображения внутри редактора
		const editorContainer = document.getElementById('editorContainer');
		if (editorContainer) {
			const mainImages = editorContainer.querySelectorAll('img:not(.b24-image-editable img)');
			console.log('📸 Найдено изображений в редакторе:', mainImages.length);
			processEditorImages(mainImages);
		}
		
		// Iframe редактора
		const editorIframes = document.querySelectorAll('.bx-editor-iframe');
		editorIframes.forEach(function(iframe, index) {
			try {
				const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
				if (iframeDoc && iframeDoc.body) {
					const iframeImages = iframeDoc.querySelectorAll('img:not(.b24-image-editable img)');
					console.log(`📸 Iframe ${index}: найдено ${iframeImages.length} изображений`);
					processEditorImages(iframeImages);
				} else {
					iframe.addEventListener('load', function() {
						setTimeout(function() {
							const doc = iframe.contentDocument || iframe.contentWindow.document;
							if (doc) {
								const images = doc.querySelectorAll('img:not(.b24-image-editable img)');
								processEditorImages(images);
							}
						}, 300);
					});
				}
			} catch (e) {
				console.log(`❌ Ошибка доступа к iframe ${index}:`, e.message);
			}
		});
		
		// Наблюдение за изменениями в iframe
		editorIframes.forEach(function(iframe) {
			try {
				const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
				if (!iframeDoc) return;
				
				const observer = new MutationObserver(function(mutations) {
					let hasNewImages = false;
					mutations.forEach(function(mutation) {
						if (mutation.type === 'childList') {
							mutation.addedNodes.forEach(function(node) {
								if (node.nodeType === Node.ELEMENT_NODE) {
									const images = node.querySelectorAll('img');
									if (images.length > 0) {
										hasNewImages = true;
									}
								}
							});
						}
					});
					if (hasNewImages) {
						console.log('🔄 Обнаружены новые изображения в iframe редактора');
						setTimeout(function() {
							const images = iframeDoc.querySelectorAll('img:not(.b24-image-editable img)');
							processEditorImages(images);
						}, 300);
					}
				});
				
				observer.observe(iframeDoc.body, {
					childList: true,
					subtree: true
				});
			} catch (e) {}
		});
	}

	// Запускаем инициализацию после загрузки
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			setTimeout(function() {
				initImageResizeForEditor();
			}, 1000);
		});
	} else {
		setTimeout(function() {
			initImageResizeForEditor();
		}, 1000);
	}

	// Переинициализация при открытии формы
	document.addEventListener('click', function(e) {
		if (e.target.closest('#feedPlaceholder')) {
			setTimeout(function() {
				initImageResizeForEditor();
			}, 500);
			setTimeout(function() {
				initImageResizeForEditor();
			}, 1500);
		}
	});
</script>
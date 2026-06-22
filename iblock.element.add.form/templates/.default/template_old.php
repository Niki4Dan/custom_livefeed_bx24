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
						<div class="b24-html-editor">
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
						editor.InsertHtml(`<img src="${imageUrl}" style="max-width: 100%; height: auto;">`);
						showToast('Изображение вставлено в сообщение', 'success', '✅ Готово');
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

			// Обработчик для кнопки выбора файлов
			if (uploadBtn) {
				uploadBtn.addEventListener('click', function(e) {
					e.stopPropagation(); // Оставляем, чтобы избежать других конфликтов, но убираем дублирующий вызов
				});
			}

			// Обработчик для области загрузки (только при клике не на кнопку)
			if (uploader) {
				uploader.addEventListener('click', function(e) {
					// Если клик был не по кнопке, открываем диалог
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

			// Обработчик изменения файлов
			fileInput.addEventListener('change', function(e) {
				handleFileSelect(Array.from(e.target.files));
				// Очищаем input, чтобы можно было выбрать те же файлы снова
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
</script>
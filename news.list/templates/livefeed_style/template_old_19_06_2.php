<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

$this->setFrameMode(true);

$pullEnabled = false;
if (Loader::includeModule('pull')) {
	$pullEnabled = true;
}
?>

<style>
	.b24-feed-list {
		margin: 0 auto;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	}

	.b24-feed-post {
		background: #ffffff;
		border-radius: 16px;
		border: 1px solid #e8e8e8;
		margin-bottom: 20px;
		overflow: hidden;
		transition: box-shadow 0.2s ease;
	}

	.b24-feed-post:hover {
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
	}

	.b24-feed-post-header {
		display: flex;
		align-items: flex-start;
		padding: 16px 20px;
		cursor: pointer;
	}

	.b24-feed-post-avatar {
		width: 44px;
		height: 44px;
		border-radius: 50%;
		overflow: hidden;
		margin-right: 12px;
		flex-shrink: 0;
		cursor: pointer;
		transition: opacity 0.2s ease;
		border: 1px solid rgb(0, 147, 85);
	}

	.b24-feed-post-avatar:hover {
		opacity: 0.9;
	}

	.b24-feed-post-avatar img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.b24-feed-post-avatar-default {
		width: 100%;
		height: 100%;
		background: linear-gradient(135deg, #2fc6f6, #1ea5d8);
		border-radius: 50%;
	}

	.b24-feed-post-info {
		flex: 1;
	}

	.b24-feed-post-author {
		font-weight: 600;
		font-size: 14px;
		color: #1a2a3a;
		margin-bottom: 4px;
		cursor: pointer;
		transition: color 0.2s ease;
		display: inline-block;
	}

	.b24-feed-post-author:hover {
		color: #2fc6f6;
		text-decoration: underline;
	}

	.b24-feed-post-date {
		font-size: 12px;
		color: #828b95;
		display: flex;
		align-items: center;
		gap: 4px;
	}

	.b24-feed-post-content {
		padding: 0 20px 16px 72px;
	}

	.b24-feed-post-text {
		font-size: 14px;
		line-height: 1.5;
		color: #1a2a3a;
		word-wrap: break-word;
	}

	.b24-feed-post-text p {
		margin: 0 0 10px 0;
	}

	.b24-feed-post-text p:last-child {
		margin-bottom: 0;
	}

	.b24-feed-post-text img {
		max-width: 100%;
		height: auto;
		border-radius: 12px;
		margin: 10px 0;
	}

	.b24-feed-post-text a {
		color: #2fc6f6;
		text-decoration: none;
	}

	.b24-feed-post-text a:hover {
		text-decoration: underline;
	}

	.b24-feed-post-preview-img {
		margin-top: 16px;
		border-radius: 12px;
		overflow: hidden;
	}

	.b24-feed-post-preview-img img {
		width: 100%;
		max-height: 400px;
		object-fit: cover;
		border-radius: 12px;
	}

	.b24-feed-post-actions {
		display: flex;
		gap: 24px;
		padding: 12px 20px;
		border-top: 1px solid #f0f2f4;
		background: #fafbfc;
		margin-top: 12px;
	}

	.b24-feed-action-btn {
		display: flex;
		align-items: center;
		gap: 8px;
		background: none;
		border: none;
		color: #828b95;
		cursor: pointer;
		font-size: 13px;
		padding: 6px 12px;
		border-radius: 20px;
		transition: all 0.2s ease;
	}

	.b24-feed-action-btn:hover {
		background: #e8e8e8;
		color: #2fc6f6;
	}

	.b24-feed-action-btn.like.active {
		color: #ff5c5c;
	}

	.b24-feed-action-btn.like.active svg {
		fill: #ff5c5c;
		stroke: #ff5c5c;
	}

	.b24-feed-comments {
		display: none;
		padding: 16px 20px;
		border-top: 1px solid #f0f2f4;
		background: #fafbfc;
	}

	.b24-feed-comments.show {
		display: block;
	}

	.b24-feed-comments-list {
		max-height: 500px;
		overflow-y: auto;
		margin-bottom: 16px;
		display: flex;
		flex-direction: column;
		gap: 0;
	}

	.b24-feed-comment-item {
		display: flex;
		gap: 12px;
		padding: 14px 12px;
		margin-bottom: 8px;
		background: #f5f7f9;
		border-radius: 12px;
		border: none;
		transition: background 0.2s ease;
		animation: fadeIn 0.3s ease;
	}

	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.b24-feed-comment-item.new-comment {
		animation: highlight 1s ease;
	}

	@keyframes highlight {
		0% {
			background: #fff3e0;
		}

		100% {
			background: #f5f7f9;
		}
	}

	.b24-feed-comment-item:hover {
		background: #eef2f5;
	}

	.b24-feed-comment-avatar {
		width: 36px;
		height: 36px;
		border-radius: 50%;
		overflow: hidden;
		flex-shrink: 0;
		cursor: pointer;
		transition: opacity 0.2s ease;
		background: linear-gradient(135deg, #2fc6f6, #1ea5d8);
		border: 1px solid rgb(0, 147, 85);
	}

	.b24-feed-comment-avatar:hover {
		opacity: 0.8;
	}

	.b24-feed-comment-avatar img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.b24-feed-comment-content {
		flex: 1;
		min-width: 0;
	}

	.b24-feed-comment-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin-bottom: 4px;
		flex-wrap: wrap;
		gap: 8px;
	}

	.b24-feed-comment-author-wrapper {
		display: flex;
		align-items: center;
		gap: 8px;
		flex-wrap: wrap;
	}

	.b24-feed-comment-author {
		font-weight: 600;
		font-size: 13px;
		color: #1a2a3a;
		cursor: pointer;
		transition: color 0.2s ease;
		display: inline-block;
	}

	.b24-feed-comment-author:hover {
		color: #2fc6f6;
		text-decoration: underline;
	}

	.b24-feed-comment-text {
		font-size: 13px;
		color: #1a2a3a;
		line-height: 1.5;
		word-wrap: break-word;
		overflow-wrap: break-word;
		margin-bottom: 6px;
	}

	.b24-feed-comment-date {
		font-size: 10px;
		color: #828b95;
		display: flex;
		align-items: center;
		gap: 4px;
	}

	.b24-feed-comment-delete {
		opacity: 0;
		background: none;
		border: none;
		cursor: pointer;
		padding: 4px 8px;
		border-radius: 8px;
		transition: all 0.2s ease;
		color: #828b95;
		font-size: 14px;
		flex-shrink: 0;
	}

	.b24-feed-comment-item:hover .b24-feed-comment-delete {
		opacity: 1;
	}

	.b24-feed-comment-delete:hover {
		background: #ffebee;
		color: #f44336;
	}

	.b24-feed-comment-delete.admin-mode {
		opacity: 1;
	}

	.b24-feed-comment-form-wrapper {
		margin-top: 16px;
		padding-top: 16px;
		border-top: 1px solid #e8e8e8;
	}

	.b24-html-editor-wrapper {
		border: 1px solid #e8e8e8;
		border-radius: 12px;
		overflow: hidden;
		background: #ffffff;
		margin-bottom: 12px;
	}

	.b24-feed-comment-buttons {
		display: flex;
		gap: 8px;
		justify-content: flex-end;
	}

	.b24-feed-comment-submit {
		background: #2fc6f6;
		color: #fff;
		border: none;
		padding: 8px 24px;
		border-radius: 24px;
		cursor: pointer;
		font-size: 13px;
		font-weight: 500;
		transition: all 0.2s ease;
	}

	.b24-feed-comment-submit:hover {
		background: #1ea5d8;
		transform: translateY(-1px);
	}

	.b24-feed-comment-submit:disabled {
		opacity: 0.6;
		cursor: not-allowed;
		transform: none;
	}

	.b24-feed-comment-cancel {
		background: none;
		border: 1px solid #e8e8e8;
		color: #828b95;
		padding: 8px 24px;
		border-radius: 24px;
		cursor: pointer;
		font-size: 13px;
		transition: all 0.2s ease;
	}

	.b24-feed-comment-cancel:hover {
		background: #f0f2f4;
		color: #1a2a3a;
	}

	.b24-feed-like-count,
	.b24-feed-comment-count {
		font-size: 12px;
		margin-left: 4px;
	}

	.comment-placeholder {
		text-align: center;
		padding: 32px 20px;
		color: #828b95;
		background: #f5f7f9;
		border-radius: 12px;
		font-size: 13px;
	}

	.b24-feed-pagination {
		text-align: center;
		margin-top: 30px;
	}

	/* Стили для файлов в постах */
	.b24-feed-post-files {
		margin-top: 16px;
		padding: 12px 16px;
		background: #f8fafc;
		border-radius: 12px;
		border: 1px solid #e2e8f0;
	}

	.b24-files-title {
		font-size: 12px;
		color: #64748b;
		margin-bottom: 10px;
		font-weight: 500;
	}

	.b24-files-list {
		display: flex;
		flex-direction: column;
		gap: 8px;
	}

	.b24-post-file-item {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 8px 12px;
		background: #ffffff;
		border: 1px solid #e2e8f0;
		border-radius: 10px;
		transition: all 0.2s ease;
		text-decoration: none;
		color: #1e293b;
	}

	.b24-post-file-item:hover {
		background: #f1f5f9;
		border-color: #cbd5e1;
		transform: translateY(-1px);
		text-decoration: none;
		color: #1e293b;
	}

	.b24-post-file-icon {
		width: 36px;
		height: 36px;
		background: #f1f5f9;
		border-radius: 8px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 20px;
		flex-shrink: 0;
	}

	.b24-post-file-info {
		flex: 1;
		min-width: 0;
	}

	.b24-post-file-name {
		font-size: 13px;
		font-weight: 500;
		color: #1e293b;
		margin-bottom: 2px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	.b24-post-file-size {
		font-size: 11px;
		color: #94a3b8;
	}

	.b24-post-file-download {
		color: #94a3b8;
		font-size: 14px;
		flex-shrink: 0;
		transition: all 0.2s ease;
	}

	.b24-post-file-item:hover .b24-post-file-download {
		color: #2fc6f6;
		transform: translateX(3px);
	}

	.b24-feed-post-images {
		display: flex;
		flex-wrap: wrap;
		gap: 8px;
		margin-top: 12px;
	}

	.b24-feed-post-image {
		position: relative;
		width: calc(33.333% - 6px);
		max-width: 200px;
		border-radius: 12px;
		overflow: hidden;
		cursor: pointer;
		transition: all 0.2s ease;
	}

	.b24-feed-post-image img {
		width: 100%;
		height: 120px;
		object-fit: cover;
		transition: all 0.2s ease;
	}

	.b24-feed-post-image:hover img {
		transform: scale(1.05);
	}

	@media (max-width: 600px) {
		.b24-feed-post-image {
			width: calc(50% - 4px);
		}

		.b24-post-file-name {
			max-width: 150px;
		}
	}


	.b24-feed-action-btn.like {
		transition: all 0.3s ease;
	}

	.b24-feed-action-btn.like.like-animation {
		transform: scale(1.15);
		color: #ff5c5c;
	}

	.b24-feed-action-btn.like.like-animation svg {
		transform: scale(1.1);
	}



	.b24-feed-comments-show-more {
		text-align: center;
		padding: 12px 0 16px;
		margin-bottom: 8px;
	}

	.b24-feed-show-more-btn {
		background: none;
		border: none;
		color: #2fc6f6;
		cursor: pointer;
		font-size: 12px;
		padding: 8px 20px;
		border-radius: 20px;
		transition: all 0.2s ease;
		background: #eef2f5;
	}

	.b24-feed-show-more-btn:hover {
		background: #e2e8ed;
		text-decoration: none;
	}

	.b24-feed-empty {
		text-align: center;
		padding: 60px 20px;
		background: #ffffff;
		border-radius: 16px;
		border: 1px solid #e8e8e8;
	}

	.b24-feed-empty-icon {
		font-size: 48px;
		margin-bottom: 16px;
	}

	.b24-feed-empty-text {
		font-size: 16px;
		font-weight: 500;
		color: #1a2a3a;
		margin-bottom: 8px;
	}

	.b24-feed-empty-hint {
		font-size: 13px;
		color: #828b95;
	}
</style>


<div class="b24-feed-list">
	<?php if ($arParams["DISPLAY_TOP_PAGER"]): ?>
		<div class="b24-feed-pagination">
			<div class="modern-pagination"><?= $arResult["NAV_STRING"] ?></div>
		</div>
	<?php endif; ?>

	<?php if (count($arResult["ITEMS"]) > 0): ?>
		<?php foreach ($arResult["ITEMS"] as $arItem): ?>
			<?php
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

			$user = $arItem["USER"];
			$imageSrc = '';
			if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])) {
				$imageSrc = $arItem["PREVIEW_PICTURE"]["SRC"];
			}

			$displayDate = '';
			if (!empty($arItem["DATE_ACTIVE_FROM"])) {
				$timestamp = MakeTimeStamp($arItem["DATE_ACTIVE_FROM"]);
				$diff = time() - $timestamp;
				if ($diff < 60) $displayDate = 'Только что';
				elseif ($diff < 3600) $displayDate = floor($diff / 60) . ' мин назад';
				elseif ($diff < 86400) $displayDate = floor($diff / 3600) . ' ч назад';
				elseif ($diff < 172800) $displayDate = 'Вчера';
				else $displayDate = date('d.m.Y', $timestamp);
			} else {
				$timestamp = MakeTimeStamp($arItem["DATE_CREATE"]);
				$displayDate = date('d.m.Y', $timestamp);
			}

			if ($pullEnabled) {
				try {
					$pushChannel = 'COMMENTS_' . $arItem['IBLOCK_ID'] . '_' . $arItem['ID'];
					CPullWatch::Add($USER->GetID(), $pushChannel);
				} catch (Exception $e) {
				}
			}

			$connection = Application::getConnection();
			$tableExists = $connection->isTableExists('comments');
			$count_comments = 0;
			if ($tableExists) {
				$sql = "SELECT COUNT(*) as cnt FROM comments WHERE id_post = " . $arItem['ID'] . " AND status = 1";
				$result = $connection->query($sql);
				$row = $result->fetch();
				$count_comments = intval($row['cnt']);
			}
			?>

			<?php
			$recipients = isset($arItem['PROPERTIES']['RECIPIENTS']['VALUE']) && is_array($arItem['PROPERTIES']['RECIPIENTS']['VALUE']) ? $arItem['PROPERTIES']['RECIPIENTS']['VALUE'] : [];
			$norecipients = isset($arItem['PROPERTIES']['NORECIPIENTS']['VALUE']) && is_array($arItem['PROPERTIES']['NORECIPIENTS']['VALUE']) ? $arItem['PROPERTIES']['NORECIPIENTS']['VALUE'] : [];

			if ((empty($recipients) || in_array($USER->GetID(), $recipients)) && !in_array($USER->GetID(), $norecipients)) {
			?>
				<div class="b24-feed-post" id="post-<?= $arItem['ID'] ?>" data-post-id="<?= $arItem['ID'] ?>" data-iblock-id="<?= $arItem['IBLOCK_ID'] ?>">
					<div class="b24-feed-post-header" data-user-id="<?= $user['ID'] ?>">
						<div class="b24-feed-post-avatar" data-user-id="<?= $user['ID'] ?>">
							<?php if ($user['AVATAR']): ?>
								<img src="<?= htmlspecialchars($user['AVATAR']) ?>" alt="<?= htmlspecialchars($user['FULL_NAME']) ?>">
							<?php else: ?>
								<div class="b24-feed-post-avatar-default"></div>
							<?php endif; ?>
						</div>
						<div class="b24-feed-post-info">
							<div class="b24-feed-post-author" data-user-id="<?= $user['ID'] ?>"><?= htmlspecialchars($user['FULL_NAME']) ?></div>
							<div class="b24-feed-post-date"><?= $displayDate ?></div>
						</div>
					</div>

					<div class="b24-feed-post-content">
						<?php if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
							<div class="b24-feed-post-text"><?= $arItem["PREVIEW_TEXT"] ?></div>
						<?php endif; ?>

						<?php if ($imageSrc): ?>
							<div class="b24-feed-post-preview-img">
								<img src="<?= $imageSrc ?>" alt="<?= $arItem["NAME"] ?>">
							</div>
						<?php endif; ?>

						<?php
						$arFiles = array();
						if (isset($arItem['PROPERTIES']['MESSAGE_FILE']['VALUE']) && !empty($arItem['PROPERTIES']['MESSAGE_FILE']['VALUE'])) {
							if (is_array($arItem['PROPERTIES']['MESSAGE_FILE']['VALUE'])) {
								$arFiles = $arItem['PROPERTIES']['MESSAGE_FILE']['VALUE'];
							} else {
								$arFiles = array($arItem['PROPERTIES']['MESSAGE_FILE']['VALUE']);
							}
						}

						$imageFiles = array();
						$otherFiles = array();
						foreach ($arFiles as $fileId) {
							$arFile = CFile::GetFileArray($fileId);
							if ($arFile) {
								$fileExt = strtolower(pathinfo($arFile['FILE_NAME'], PATHINFO_EXTENSION));
								if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
									$imageFiles[] = $arFile;
								} else {
									$otherFiles[] = $arFile;
								}
							}
						}
						?>

						<?php if (!empty($imageFiles)): ?>
							<!-- Блок для прикрепленных изображений был удален по запросу пользователя -->
						<?php endif; ?>

						<?php if (!empty($otherFiles)): ?>
							<div class="b24-feed-post-files">
								<div class="b24-files-title">Прикрепленные файлы</div>
								<div class="b24-files-list">
									<?php foreach ($otherFiles as $arFile): ?>
										<?php
										$fileExt = strtolower(pathinfo($arFile['FILE_NAME'], PATHINFO_EXTENSION));
										$fileIcon = '📎';
										if ($fileExt == 'pdf') $fileIcon = '📄';
										elseif (in_array($fileExt, ['doc', 'docx'])) $fileIcon = '📝';
										elseif (in_array($fileExt, ['xls', 'xlsx'])) $fileIcon = '📊';
										elseif (in_array($fileExt, ['zip', 'rar'])) $fileIcon = '📦';
										?>
										<a href="<?= $arFile['SRC'] ?>" class="b24-post-file-item" download>
											<div class="b24-post-file-icon"><?= $fileIcon ?></div>
											<div class="b24-post-file-info">
												<div class="b24-post-file-name"><?= htmlspecialchars($arFile['ORIGINAL_NAME'] ?: $arFile['FILE_NAME']) ?></div>
												<div class="b24-post-file-size"><?= CFile::FormatSize($arFile['FILE_SIZE']) ?></div>
											</div>
											<div class="b24-post-file-download">⬇️</div>
										</a>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endif; ?>
					</div>

					<div class="b24-feed-post-actions">
						<button class="b24-feed-action-btn like <?= $arItem["USER_LIKED"] ? 'active' : '' ?>" data-id="<?= $arItem['ID'] ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
							</svg>
							<span>Нравится</span>
							<span class="b24-feed-like-count"><?= $arItem["LIKES_COUNT"] ?></span>
						</button>
						<button class="b24-feed-action-btn comment" data-id="<?= $arItem['ID'] ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
								<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
							</svg>
							<span>Комментарии</span>
							<span class="b24-feed-comment-count"><?= $count_comments ?></span>
						</button>
					</div>

					<div class="b24-feed-comments" id="comments-<?= $arItem['ID'] ?>">
						<div class="b24-feed-comments-list"></div>
						<div class="b24-feed-comment-form-wrapper">
							<div class="b24-html-editor-wrapper">
								<?php
								$arSmile = CSmileGallery::getSmilesWithSets(CSmileGallery::GALLERY_DEFAULT);
								$arSmiles = array();
								$i = 0;
								foreach ($arSmile['SMILE'] as $smile) {
									$arSmiles[$i]['name'] = $smile['NAME'];
									$arSmiles[$i]['code'] = $smile['TYPING'];
									$arSmiles[$i]['path'] = $smile['IMAGE'];
									$arSmiles[$i]['width'] = $smile['WIDTH'];
									$arSmiles[$i]['height'] = $smile['HEIGHT'];
									$i++;
								}
								$LHE = new CHTMLEditor;
								$LHE->Show([
									'name' => 'comment_text_' . $arItem['ID'],
									'id' => 'comment_editor_' . $arItem['ID'],
									'inputName' => 'comment_text_' . $arItem['ID'],
									'content' => '',
									'width' => '100%',
									'height' => '200',
									'bAllowPhp' => false,
									'limitPhpAccess' => false,
									'autoResize' => true,
									'useFileDialogs' => true,
									'saveOnBlur' => false,
									'showTaskbars' => false,
									'showNodeNavi' => false,
									'bbCode' => false,
									'siteId' => SITE_ID,
									'toolbarConfig' => ['Bold', 'Italic', 'Underline', 'Strike', 'ForeColor', 'Justify', 'InsertOrderedList', 'InsertUnorderedList', 'CreateLink', 'DeleteLink', 'InsertImage', 'UploadFile', 'Smile'],
									'arSmiles' => $arSmiles,
									'isLight' => true,
									'placeholder' => 'Написать комментарий...',
									'emojies' => true,
									'emojiesPicker' => true,
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
								]);
								?>
							</div>


							<div class="b24-form-group">
								<!-- <div class="b24-form-label">📎 Прикрепленные файлы <span style="font-size: 11px; color: #828b95; font-weight: normal;">(опционально)</span></div> -->
								<style>
									/* Стили для файлов */
									.b24-files-uploader-comments {
										background: #f8fafc;
										border: 1px dashed #cbd5e1;
										border-radius: 12px;
										padding: 20px;
										transition: all 0.2s ease;
										cursor: pointer;
									}

									.b24-files-uploader-comments:hover {
										border-color: #2fc6f6;
										background: #f0f7fc;
									}

									.b24-files-uploader-comments.drag-over {
										border-color: #2fc6f6;
										background: #e6f4fa;
									}

									.b24-files-upload-content-comments {
										display: flex;
										flex-direction: column;
										align-items: center;
										justify-content: center;
										text-align: center;
									}

									.b24-files-upload-icon-comments {
										font-size: 32px;
										margin-bottom: 12px;
									}

									.b24-files-upload-text-comments {
										font-size: 14px;
										color: #1e293b;
										margin-bottom: 8px;
									}

									.b24-files-upload-hint-comments {
										font-size: 12px;
										color: #94a3b8;
									}

									.b24-files-upload-btn-comments {
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

									.b24-files-upload-btn-comments:hover {
										background: #2fc6f6;
										color: #ffffff;
									}
								</style>
								<div class="b24-files-uploader-comments" id="filesUploader">
									<div class="b24-files-upload-content-comments">
										<div class="b24-files-upload-icon-comments">📎</div>
										<div class="b24-files-upload-text-comments">Перетащите файлы сюда или</div>
										<label class="b24-files-upload-btn-comments" for="commentFiles">📁 Выбрать файлы</label>
										<input type="file" name="COMMENTS_FILE[]" id="commentFiles" multiple style="display: none;" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
										<div class="b24-files-upload-hint-comments" style="margin-top:20px;">Поддерживаются: JPG, PNG, GIF, WEBP, PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP, RAR до 20 МБ</div>
									</div>
								</div>

								<div id="uploadedFilesContainer"></div>
							</div>



							<div class="b24-feed-comment-buttons">
								<button class="b24-feed-comment-submit" data-post-id="<?= $arItem['ID'] ?>">📝 Отправить</button>
								<button class="b24-feed-comment-cancel" data-post-id="<?= $arItem['ID'] ?>">✖️ Отмена</button>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php endforeach; ?>
	<?php else: ?>
		<div class="b24-feed-empty">
			<div class="b24-feed-empty-icon">📝</div>
			<div class="b24-feed-empty-text">Нет записей</div>
			<div class="b24-feed-empty-hint">Здесь пока нет сообщений</div>
		</div>
	<?php endif; ?>

	<?php if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
		<div class="b24-feed-pagination">
			<div class="modern-pagination"><?= $arResult["NAV_STRING"] ?></div>
		</div>
	<?php endif; ?>
</div>


<script>
	const debug = false;
	const COMMENTS_PER_PAGE = 5;
	let isLoadingComments = {};
	const commentsCache = {};
	let loadedCount = {};
	const isAdmin = <?= $USER->IsAdmin() ? 'true' : 'false' ?>;
	const currentUserId = <?= $USER->GetID() ?>;

	let debugPanel = null;

	function parseCommentDate(dateStr) {
		if (!dateStr) return new Date(0);
		if (dateStr.includes('.')) {
			const [date, time] = dateStr.split(' ');
			const [day, month, year] = date.split('.');
			return new Date(`${year}-${month}-${day} ${time}`);
		}
		return new Date(dateStr);
	}

	function addDebugLog(message, type = 'info') {
		console.log(`[${type.toUpperCase()}] ${message}`);
		if (debugPanel && isAdmin) {
			const time = new Date().toLocaleTimeString();
			const logDiv = debugPanel.querySelector('.debug-logs');
			if (logDiv) {
				logDiv.innerHTML += `<div style="font-size: 10px; border-top: 1px solid #333; margin-top: 2px; padding-top: 2px;">${time}: ${escapeHtml(message)}</div>`;
				logDiv.scrollTop = logDiv.scrollHeight;
			}
		}
	}

	function createDebugPanel() {
		if (!isAdmin) return;
		if (!debug) return;
		debugPanel = document.createElement('div');
		debugPanel.className = 'pull-debug-panel';
		debugPanel.style.cssText = 'position:fixed;bottom:10px;right:10px;background:#1a2a3a;color:#0f0;padding:8px 12px;border-radius:8px;font-family:monospace;font-size:11px;z-index:99999;max-width:300px;opacity:0.8;cursor:pointer;';
		debugPanel.innerHTML = `
            <strong>🔌 Push&Pull Debug</strong>
            <div style="font-size: 10px; margin-top: 4px;">Enabled: ✅ YES</div>
            <div class="debug-logs" style="font-size: 10px; margin-top: 4px; max-height: 150px; overflow-y: auto;"></div>
        `;
		document.body.appendChild(debugPanel);
		debugPanel.addEventListener('click', function() {
			const logs = this.querySelector('.debug-logs');
			if (logs.style.maxHeight === '300px') {
				logs.style.maxHeight = '150px';
			} else {
				logs.style.maxHeight = '300px';
			}
		});
		addDebugLog('Debug panel created');
	}

	function openUserProfile(userId) {
		if (!userId || userId === '0') return;
		if (typeof BX !== 'undefined' && BX.SidePanel && BX.SidePanel.Instance) {
			BX.SidePanel.Instance.open('/company/personal/user/' + userId + '/', {
				width: '100%',
				leftBoundary: 0,
				resizable: true,
				cacheable: false,
				allowChangeHistory: true
			});
		} else {
			window.location.href = '/company/personal/user/' + userId + '/';
		}
	}

	function escapeHtml(text) {
		if (!text) return '';
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	function initRealtimeComments(postId, iblockId) {
		addDebugLog(`Init realtime for post ${iblockId}_${postId}`);
		const pushChannel = 'COMMENTS_' + iblockId + '_' + postId;

		BX.addCustomEvent("onPullEvent-comments", function(command, params) {
			addDebugLog(`📨 onPullEvent-comments received: ${command}, iblock: ${params.iblock_id}, post: ${params.post_id}`);

			if (command === 'update_likes') {
				const postId = params.element_id || params.post_id;
				const newCount = params.count;
				const postElement = document.querySelector(`.b24-feed-post[data-post-id="${postId}"]`);
				if (postElement) {
					const likeBtn = postElement.querySelector('.b24-feed-action-btn.like');
					if (likeBtn) {
						let countSpan = likeBtn.querySelector('.b24-feed-like-count');
						if (!countSpan) {
							countSpan = document.createElement('span');
							countSpan.className = 'b24-feed-like-count';
							likeBtn.appendChild(countSpan);
						}
						countSpan.textContent = newCount > 0 ? newCount : '';
						// Добавляем анимацию при обновлении
						likeBtn.classList.add('like-animation');
						setTimeout(() => likeBtn.classList.remove('like-animation'), 300);
						addDebugLog(`Updated likes count for post ${postId}: ${newCount}`);
					}
				}
			}

			if (command === 'new_comment' && params.iblock_id == iblockId && params.post_id == postId) {
				addDebugLog(`✅ New comment event for post ${iblockId}_${postId}`);
				addDebugLog(`Current cache before: ${JSON.stringify(commentsCache[postId])}`);

				const newComment = params.comment;

				// Пропускаем свой комментарий (он уже добавлен локально)
				if (newComment.author_id == currentUserId) {
					addDebugLog(`⏭️ Skipping own comment ${newComment.id} (already added locally)`);
					return;
				}

				// Если кеша нет - загружаем комментарии с сервера
				if (!commentsCache[postId]) {
					addDebugLog(`No cache for post ${postId}, loading from server`);
					const commentsDiv = document.getElementById('comments-' + postId);
					if (commentsDiv) {
						loadComments(postId, commentsDiv, iblockId);
					}
					return;
				}

				// Проверяем, нет ли уже такого комментария
				const exists = commentsCache[postId].some(c => c.id == newComment.id);
				if (!exists) {
					commentsCache[postId].push(newComment);
					commentsCache[postId].sort((a, b) => {
						return parseCommentDate(a.created_at) - parseCommentDate(b.created_at);
					});
					addDebugLog(`Added new comment ${newComment.id}, total: ${commentsCache[postId].length}`);
					addDebugLog(`Cache after: ${JSON.stringify(commentsCache[postId].map(c => c.id))}`);
				} else {
					addDebugLog(`Comment ${newComment.id} already exists, skipping`);
					return;
				}

				updateCommentCount(postId);

				const commentsDiv = document.getElementById('comments-' + postId);
				if (commentsDiv && commentsDiv.classList.contains('show')) {
					loadedCount[postId] = commentsCache[postId].length;
					renderComments(postId, commentsDiv, iblockId);
					const listContainer = commentsDiv.querySelector('.b24-feed-comments-list');
					if (listContainer) {
						setTimeout(() => listContainer.scrollTop = listContainer.scrollHeight, 100);
					}
					const newCommentElement = commentsDiv.querySelector('.b24-feed-comment-item:last-child');
					if (newCommentElement) {
						newCommentElement.classList.add('new-comment');
						setTimeout(() => newCommentElement.classList.remove('new-comment'), 1000);
					}
				}
			}

			if (command === 'delete_comment' && params.iblock_id == iblockId && params.post_id == postId) {
				addDebugLog(`🗑 Delete comment event for post ${iblockId}_${postId}, comment_id: ${params.comment_id}`);
				if (commentsCache[postId]) {
					commentsCache[postId] = commentsCache[postId].filter(c => c.id != params.comment_id);
				}
				const commentsDiv = document.getElementById('comments-' + postId);
				if (commentsDiv && commentsDiv.classList.contains('show')) {
					if (loadedCount[postId] && loadedCount[postId] > commentsCache[postId].length) {
						loadedCount[postId] = commentsCache[postId].length;
					}
					renderComments(postId, commentsDiv, iblockId);
				}
				updateCommentCount(postId);
			}
		});

		if (typeof BX.PULL !== 'undefined') {
			BX.PULL.extendWatch(pushChannel);
			addDebugLog(`Extended watch for ${pushChannel}`);
		}
	}

	function subscribeToPost(postId, iblockId) {
		BX.ajax({
			url: '/ajax/comments_likes.php',
			method: 'POST',
			data: {
				'action': 'subscribe',
				'post_id': postId,
				'iblock_id': iblockId,
				'sessid': BX.bitrix_sessid()
			},
			onsuccess: function(response) {
				const data = JSON.parse(response);
				if (data.success) {
					addDebugLog(`Subscribed to COMMENTS_${iblockId}_${postId}`);
				}
			},
			onfailure: function(error) {
				addDebugLog(`Subscribe error: ${error}`, 'error');
			}
		});
	}

	function subscribeToLikes(postId, iblockId) {
		const likesChannel = 'LIKES_' + iblockId + '_' + postId;
		if (typeof BX.PULL !== 'undefined') {
			BX.PULL.extendWatch(likesChannel);
			addDebugLog(`Subscribed to likes channel: ${likesChannel}`);
		}
		BX.ajax({
			url: '/ajax/comments_likes.php',
			method: 'POST',
			data: {
				'action': 'subscribe_likes',
				'post_id': postId,
				'iblock_id': iblockId,
				'sessid': BX.bitrix_sessid()
			},
			onsuccess: function(response) {
				try {
					const data = JSON.parse(response);
					if (data.success) {
						addDebugLog(`Subscribed to likes: ${data.channel}`);
					}
				} catch (e) {}
			}
		});
	}

	function getEditorContent(postId) {
		const editorId = 'comment_editor_' + postId;
		if (typeof BX !== 'undefined' && BX.LHE && BX.LHE.GetEditor) {
			const lhe = BX.LHE.GetEditor(editorId);
			if (lhe && lhe.oEditor && typeof lhe.oEditor.GetContent === 'function') {
				const content = lhe.oEditor.GetContent();
				if (content && content.trim() !== '' && content !== '<br>') {
					return content;
				}
			}
		}
		if (typeof BXHtmlEditor !== 'undefined') {
			const editor = BXHtmlEditor.Get(editorId);
			if (editor && typeof editor.GetContent === 'function') {
				const content = editor.GetContent();
				if (content && content.trim() !== '' && content !== '<br>') {
					return content;
				}
			}
		}
		return '';
	}

	function clearEditorContent(postId) {
		const editorId = 'comment_editor_' + postId;
		if (typeof BX !== 'undefined' && BX.LHE && BX.LHE.GetEditor) {
			const lhe = BX.LHE.GetEditor(editorId);
			if (lhe && lhe.oEditor && typeof lhe.oEditor.SetContent === 'function') {
				lhe.oEditor.SetContent('');
				if (lhe.oEditor.GetCloudUploader && lhe.oEditor.GetCloudUploader().clear) {
					lhe.oEditor.GetCloudUploader().clear();
				}
				return;
			}
		}
		if (typeof BXHtmlEditor !== 'undefined') {
			const editor = BXHtmlEditor.Get(editorId);
			if (editor && typeof editor.SetContent === 'function') {
				editor.SetContent('');
				return;
			}
		}
	}

	function updateCommentCount(postId) {
		const commentCount = commentsCache[postId] ? commentsCache[postId].length : 0;
		const postElement = document.querySelector('.b24-feed-post[data-post-id="' + postId + '"]');
		if (postElement) {
			const commentBtn = postElement.querySelector('.b24-feed-action-btn.comment');
			if (commentBtn) {
				let countSpan = commentBtn.querySelector('.b24-feed-comment-count');
				if (!countSpan) {
					countSpan = document.createElement('span');
					countSpan.className = 'b24-feed-comment-count';
					commentBtn.appendChild(countSpan);
				}
				countSpan.textContent = commentCount;
			}
		}
	}

	function deleteComment(postId, commentId, container, iblockId) {
		if (!confirm('Вы уверены, что хотите удалить этот комментарий?')) return;
		addDebugLog(`Deleting comment ${commentId} from post ${postId}`);
		const deleteBtn = container.querySelector(`.b24-feed-comment-delete[data-comment-id="${commentId}"]`);
		const originalText = deleteBtn ? deleteBtn.innerHTML : '✕';
		if (deleteBtn) {
			deleteBtn.disabled = true;
			deleteBtn.innerHTML = '⏳';
		}
		BX.ajax({
			url: '/ajax/comments_likes.php',
			method: 'POST',
			data: {
				'action': 'delete_comment',
				'post_id': postId,
				'comment_id': commentId,
				'iblock_id': iblockId,
				'sessid': BX.bitrix_sessid()
			},
			onsuccess: function(response) {
				try {
					const data = JSON.parse(response);
					if (data.success) {
						addDebugLog(`Comment ${commentId} deleted successfully`);
						if (commentsCache[postId]) {
							commentsCache[postId] = commentsCache[postId].filter(c => c.id != commentId);
						}
						renderComments(postId, container, iblockId);
						updateCommentCount(postId);
					} else {
						alert(data.message || 'Ошибка при удалении комментария');
					}
				} catch (e) {
					addDebugLog(`Parse error: ${e.message}`, 'error');
					alert('Ошибка обработки ответа сервера');
				}
			},
			onfailure: function(error) {
				addDebugLog(`Delete error: ${error}`, 'error');
				alert('Ошибка при удалении комментария');
			},
			oncomplete: function() {
				if (deleteBtn && deleteBtn.parentNode) {
					deleteBtn.disabled = false;
					deleteBtn.innerHTML = originalText;
				}
			}
		});
	}

	function renderComments(postId, container, iblockId) {
		const listContainer = container.querySelector('.b24-feed-comments-list');
		let allComments = commentsCache[postId] || [];

		allComments = [...allComments].sort((a, b) => {
			return parseCommentDate(a.created_at) - parseCommentDate(b.created_at);
		});

		const currentLoaded = loadedCount[postId] || COMMENTS_PER_PAGE;
		const displayComments = allComments.slice(-currentLoaded);
		const hasMore = allComments.length > currentLoaded;

		if (allComments.length === 0) {
			listContainer.innerHTML = '<div class="comment-placeholder">Нет комментариев. Будьте первым!</div>';
			return;
		}

		let html = '';
		if (hasMore) {
			const hiddenCount = allComments.length - currentLoaded;
			html += `<div class="b24-feed-comments-show-more"><button class="b24-feed-show-more-btn" data-post-id="${postId}">📋 Показать предыдущие комментарии (${hiddenCount})</button></div>`;
		}

		displayComments.forEach(comment => {
			const deleteButton = isAdmin ? `<button class="b24-feed-comment-delete admin-mode" data-comment-id="${comment.id}" data-post-id="${postId}" title="Удалить комментарий">✕</button>` : '';
			html += `
            <div class="b24-feed-comment-item" data-comment-id="${comment.id}">
                <div class="b24-feed-comment-avatar" data-user-id="${comment.author_id}">
                    ${comment.avatar ? `<img src="${escapeHtml(comment.avatar)}" alt="${escapeHtml(comment.author_name)}">` : '<div class="b24-feed-comment-avatar-default"></div>'}
                </div>
                <div class="b24-feed-comment-content">
                    <div class="b24-feed-comment-header">
                        <div class="b24-feed-comment-author-wrapper">
                            <div class="b24-feed-comment-author" data-user-id="${comment.author_id}">${escapeHtml(comment.author_name)}</div>
                            <div class="b24-feed-comment-date">${escapeHtml(comment.created_at)}</div>
                        </div>
                        ${deleteButton}
                    </div>
                    <div class="b24-feed-comment-text">${comment.content}</div>
                </div>
            </div>`;
		});

		listContainer.innerHTML = html;

		listContainer.querySelectorAll('.b24-feed-show-more-btn').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				const pid = this.getAttribute('data-post-id');
				loadedCount[pid] = (loadedCount[pid] || COMMENTS_PER_PAGE) + COMMENTS_PER_PAGE;
				renderComments(pid, container, iblockId);
				const commentsList = container.querySelector('.b24-feed-comments-list');
				if (commentsList) commentsList.scrollTop = 0;
			});
		});

		if (isAdmin) {
			listContainer.querySelectorAll('.b24-feed-comment-delete').forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.stopPropagation();
					const commentId = this.getAttribute('data-comment-id');
					const pid = this.getAttribute('data-post-id');
					deleteComment(pid, commentId, container, iblockId);
				});
			});
		}

		listContainer.querySelectorAll('.b24-feed-comment-avatar, .b24-feed-comment-author').forEach(el => {
			el.addEventListener('click', function(e) {
				e.stopPropagation();
				const userId = this.getAttribute('data-user-id');
				if (userId && userId !== '0') openUserProfile(userId);
			});
		});
	}

	function loadComments(postId, container, iblockId) {
		const listContainer = container.querySelector('.b24-feed-comments-list');
		listContainer.innerHTML = '<div class="comment-placeholder">Загрузка комментариев...</div>';
		addDebugLog(`Loading comments for post ${postId}, iblock ${iblockId}`);

		BX.ajax({
			url: '/ajax/comments_likes.php',
			method: 'POST',
			data: {
				'action': 'get_comments',
				'post_id': postId,
				'iblock_id': iblockId,
				'sessid': BX.bitrix_sessid()
			},
			onsuccess: function(response) {
				try {
					const data = JSON.parse(response);
					addDebugLog(`Raw comments response: ${JSON.stringify(data)}`);

					if (data.success) {
						addDebugLog(`Loaded ${data.comments.length} comments for post ${postId}`);
						addDebugLog(`Comments IDs: ${data.comments.map(c => c.id).join(', ')}`);

						// Полностью заменяем кеш
						commentsCache[postId] = data.comments;
						commentsCache[postId].sort((a, b) => {
							return parseCommentDate(a.created_at) - parseCommentDate(b.created_at);
						});

						addDebugLog(`Cache after load: ${commentsCache[postId].length} comments`);
						loadedCount[postId] = COMMENTS_PER_PAGE;
						renderComments(postId, container, iblockId);
						updateCommentCount(postId);
					} else {
						addDebugLog(`Server error: ${data.error || data.message}`, 'error');
						listContainer.innerHTML = '<div class="comment-placeholder">' + escapeHtml(data.error || data.message || 'Ошибка загрузки комментариев') + '</div>';
					}
				} catch (e) {
					addDebugLog(`JSON parse error: ${e.message}`, 'error');
					listContainer.innerHTML = '<div class="comment-placeholder">Ошибка парсинга ответа сервера</div>';
				}
			},
			onfailure: function(error) {
				addDebugLog(`AJAX failure: ${error}`, 'error');
				listContainer.innerHTML = '<div class="comment-placeholder">Ошибка соединения с сервером</div>';
			}
		});
	}

	function addComment(postId, container, postElement, iblockId) {
		const commentText = getEditorContent(postId);
		if (!commentText || commentText.trim() === '' || commentText === '<br>' || commentText === '&nbsp;') {
			alert('Введите текст комментария');
			return false;
		}

		addDebugLog(`Adding comment to post ${postId}, iblock ${iblockId}, text length: ${commentText.length}`);
		const submitBtn = container.querySelector('.b24-feed-comment-submit');
		const originalText = submitBtn.innerHTML;
		submitBtn.disabled = true;
		submitBtn.innerHTML = '⏳ Отправка...';

		BX.ajax({
			url: '/ajax/comments_likes.php',
			method: 'POST',
			data: {
				'action': 'add_comment',
				'post_id': postId,
				'text': commentText,
				'iblock_id': iblockId,
				'sessid': BX.bitrix_sessid()
			},
			onsuccess: function(response) {
				addDebugLog(`Raw add comment response: ${response.substring(0, 500)}`);

				try {
					const data = JSON.parse(response);
					addDebugLog(`Parsed add comment response: ${JSON.stringify(data)}`);

					if (data.success) {
						addDebugLog(`Comment added successfully to post ${postId}`);
						clearEditorContent(postId);

						// Проверяем структуру ответа
						let newComment = null;
						if (data.comment) {
							newComment = data.comment;
						} else if (data.data && data.data.comment) {
							newComment = data.data.comment;
						} else {
							addDebugLog(`Unexpected response structure, but comment was added`, 'error');
							// Если комментарий добавился, но структура не та, перезагружаем комментарии
							loadComments(postId, container, iblockId);
							submitBtn.disabled = false;
							submitBtn.innerHTML = originalText;
							return;
						}

						// Инициализируем кеш
						if (!commentsCache[postId]) {
							commentsCache[postId] = [];
						}

						// Проверяем, нет ли уже такого комментария
						const exists = commentsCache[postId].some(c => c.id == newComment.id);
						if (!exists) {
							commentsCache[postId].push(newComment);
							commentsCache[postId].sort((a, b) => {
								const dateA = parseCommentDate(a.created_at);
								const dateB = parseCommentDate(b.created_at);
								return dateA - dateB;
							});
							addDebugLog(`Added comment to cache, total: ${commentsCache[postId].length}`);
						}

						// Обновляем отображение
						renderComments(postId, container, iblockId);
						updateCommentCount(postId);

						// Прокрутка к новому комментарию
						const listContainer = container.querySelector('.b24-feed-comments-list');
						if (listContainer) {
							setTimeout(() => {
								listContainer.scrollTop = listContainer.scrollHeight;
							}, 100);
						}

						submitBtn.disabled = false;
						submitBtn.innerHTML = originalText;
					} else {
						addDebugLog(`Server error: ${data.error || data.message}`, 'error');
						alert(data.message || data.error || 'Ошибка при добавлении комментария');
						submitBtn.disabled = false;
						submitBtn.innerHTML = originalText;
					}
				} catch (e) {
					addDebugLog(`Parse error: ${e.message}`, 'error');
					addDebugLog(`Response that failed to parse: ${response}`, 'error');

					// Даже если ошибка парсинга, комментарий мог добавиться
					// Перезагружаем комментарии с сервера
					addDebugLog(`Reloading comments due to parse error`);
					loadComments(postId, container, iblockId);

					alert('Комментарий отправлен, но произошла ошибка обновления');
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalText;
				}
			},
			onfailure: function(error) {
				addDebugLog(`Add comment AJAX error: ${error}`, 'error');
				alert('Ошибка соединения при отправке комментария');
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
			}
		});
		return true;
	}

	function toggleLike(postId, button, iblockId) {
		if (button.getAttribute('data-loading') === 'true') {
			console.log(`Like button for post ${postId} is already loading, skipping`);
			button.setAttribute('data-loading', 'false');
		}
		button.setAttribute('data-loading', 'true');
		console.log(`Toggling like for post ${postId}`);

		const wasActive = button.classList.contains('active');
		let currentCount = 0;
		let countSpan = button.querySelector('.b24-feed-like-count');
		if (countSpan && countSpan.textContent) {
			currentCount = parseInt(countSpan.textContent) || 0;
		}

		if (wasActive) {
			button.classList.remove('active');
			if (countSpan && currentCount > 0) {
				countSpan.textContent = currentCount - 1;
			}
		} else {
			button.classList.add('active');
			if (countSpan) {
				countSpan.textContent = currentCount + 1;
			}
		}

		// Добавляем анимацию при клике
		button.classList.add('like-animation');
		setTimeout(() => {
			button.classList.remove('like-animation');
		}, 300);

		BX.ajax({
			url: '/ajax/comments_likes.php',
			method: 'POST',
			data: {
				'action': 'like',
				'element_id': postId,
				'iblock_id': iblockId,
				'sessid': BX.bitrix_sessid()
			},
			timeout: 10000,
			onsuccess: function(response) {
				try {
					const data = JSON.parse(response);
					if (data.success) {
						if (countSpan) {
							countSpan.textContent = data.count > 0 ? data.count : '';
						}
						if (data.user_liked) {
							button.classList.add('active');
						} else {
							button.classList.remove('active');
						}
						// Еще раз добавляем анимацию для подтверждения
						button.classList.add('like-animation');
						setTimeout(() => {
							button.classList.remove('like-animation');
						}, 300);
					} else {
						if (wasActive) {
							button.classList.add('active');
							if (countSpan) countSpan.textContent = currentCount;
						} else {
							button.classList.remove('active');
							if (countSpan) countSpan.textContent = currentCount;
						}
						alert(data.error || 'Ошибка при выполнении действия');
					}
				} catch (e) {
					if (wasActive) {
						button.classList.add('active');
						if (countSpan) countSpan.textContent = currentCount;
					} else {
						button.classList.remove('active');
						if (countSpan) countSpan.textContent = currentCount;
					}
					alert('Ошибка обработки ответа сервера');
				}
			},
			onfailure: function(error) {
				if (wasActive) {
					button.classList.add('active');
					if (countSpan) countSpan.textContent = currentCount;
				} else {
					button.classList.remove('active');
					if (countSpan) countSpan.textContent = currentCount;
				}
				alert('Ошибка соединения с сервером');
			},
			oncomplete: function() {
				button.setAttribute('data-loading', 'false');
			}
		});
	}

	function loadInitialLikesState(postId, button, iblockId) {
		BX.ajax({
			url: '/ajax/comments_likes.php',
			method: 'POST',
			data: {
				'action': 'get_likes',
				'element_id': postId,
				'iblock_id': iblockId,
				'sessid': BX.bitrix_sessid()
			},
			onsuccess: function(response) {
				try {
					const data = JSON.parse(response);
					if (data.success) {
						let countSpan = button.querySelector('.b24-feed-like-count');
						if (!countSpan) {
							countSpan = document.createElement('span');
							countSpan.className = 'b24-feed-like-count';
							button.appendChild(countSpan);
						}
						countSpan.textContent = data.count > 0 ? data.count : '';
						if (data.liked) {
							button.classList.add('active');
						} else {
							button.classList.remove('active');
						}
						addDebugLog(`Initial likes state loaded for post ${postId}: count=${data.count}, liked=${data.liked}`);
					}
				} catch (e) {
					addDebugLog(`Error loading initial likes: ${e.message}`, 'error');
				}
			},
			onfailure: function(error) {
				addDebugLog(`Failed to load initial likes: ${error}`, 'error');
			}
		});
	}

	if (typeof BX !== 'undefined') {
		BX.ready(function() {
			addDebugLog('BX.ready() called');
			if (typeof BX.PULL !== 'undefined') {
				BX.PULL.start();
				addDebugLog('BX.PULL.start() called');
			}
			if (isAdmin) {
				createDebugPanel();
			}

			document.querySelectorAll('.b24-feed-post').forEach(function(postElement) {
				const postId = postElement.dataset.postId;
				const iblockId = postElement.dataset.iblockId;
				const commentsDiv = postElement.querySelector('.b24-feed-comments');
				subscribeToLikes(postId, iblockId);

				const likeBtn = postElement.querySelector('.b24-feed-action-btn.like');
				if (likeBtn) {
					loadInitialLikesState(postId, likeBtn, iblockId);
					likeBtn.addEventListener('click', function(e) {
						e.stopPropagation();
						toggleLike(postId, likeBtn, iblockId);
					});
				}

				const commentBtn = postElement.querySelector('.b24-feed-action-btn.comment');
				if (commentBtn && commentsDiv) {
					commentBtn.addEventListener('click', function(e) {
						e.stopPropagation();
						const wasHidden = !commentsDiv.classList.contains('show');
						commentsDiv.classList.toggle('show');
						if (wasHidden) {
							if (commentsCache[postId]) {
								renderComments(postId, commentsDiv, iblockId);
							} else {
								loadComments(postId, commentsDiv, iblockId);
							}
						}
					});
				}

				const submitBtn = postElement.querySelector('.b24-feed-comment-submit');
				const cancelBtn = postElement.querySelector('.b24-feed-comment-cancel');

				if (submitBtn && commentsDiv) {
					submitBtn.addEventListener('click', function(e) {
						e.stopPropagation();
						addComment(postId, commentsDiv, postElement, iblockId);
					});
				}

				if (cancelBtn && commentsDiv) {
					cancelBtn.addEventListener('click', function(e) {
						e.stopPropagation();
						clearEditorContent(postId);
						commentsDiv.classList.remove('show');
					});
				}

				initRealtimeComments(postId, iblockId);
				subscribeToPost(postId, iblockId);
			});

			document.querySelectorAll('.b24-feed-post-avatar, .b24-feed-post-author').forEach(function(el) {
				el.addEventListener('click', function(e) {
					e.stopPropagation();
					const userId = this.getAttribute('data-user-id');
					if (userId && userId !== '0') openUserProfile(userId);
				});
			});
		});
	}

	document.addEventListener('DOMContentLoaded', function() {
		const posts = document.querySelectorAll('.b24-feed-post');
		if (posts.length === 0) {
			const emptyBlock = document.createElement('div');
			emptyBlock.className = 'b24-feed-empty';
			emptyBlock.innerHTML = `
                <div class="b24-feed-empty-icon">📝</div>
                <div class="b24-feed-empty-text">Нет записей</div>
                <div class="b24-feed-empty-hint">Здесь пока нет сообщений</div>
            `;
			const feedContainer = document.querySelector('.noelements');
			if (feedContainer) {
				feedContainer.insertBefore(emptyBlock, feedContainer.firstChild);
			}
		}
	});
</script>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
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


// Функция для склонения числительных
function getNumEnding($num, $variants)
{
	$num = abs($num);
	if ($num % 100 >= 11 && $num % 100 <= 19) {
		return $variants[2];
	}
	$lastDigit = $num % 10;
	if ($lastDigit == 1) {
		return $variants[0];
	}
	if ($lastDigit >= 2 && $lastDigit <= 4) {
		return $variants[1];
	}
	return $variants[2];
}


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
		/* line-height: 1.5; */
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
		font-size: 12px;
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


	.b24-comment-image {
		margin-top: 8px;
		border-radius: 8px;
		overflow: hidden;
	}

	.b24-comment-image img {
		max-width: 100%;
		max-height: 300px;
		border-radius: 8px;
		display: block;
		border: 1px solid #e8e8e8;
	}

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


	.b24-file-insert {
		background: none;
		border: none;
		cursor: pointer;
		padding: 6px 12px;
		border-radius: 6px;
		font-size: 12px;
		color: #2fc6f6;
		transition: all 0.2s ease;
		background: #e6f4fa;
	}

	.b24-file-insert:hover {
		background: #2fc6f6;
		color: #ffffff;
		transform: translateY(-1px);
	}

	.b24-file-remove {
		background: none;
		border: none;
		cursor: pointer;
		padding: 6px 12px;
		border-radius: 6px;
		font-size: 12px;
		color: #ef4444;
		transition: all 0.2s ease;
	}

	.b24-file-remove:hover {
		background: #fee2e2;
		transform: translateY(-1px);
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
		transition: all 0.2s ease;
	}

	.b24-file-preview-large:hover {
		border-color: #2fc6f6;
		box-shadow: 0 2px 8px rgba(47, 198, 246, 0.1);
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
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	/* Стили для файлов в комментариях */
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
		transition: all 0.2s ease;
	}

	.b24-file-preview-large:hover {
		border-color: #2fc6f6;
		box-shadow: 0 2px 8px rgba(47, 198, 246, 0.1);
		transform: translateY(-2px);
	}

	.b24-file-preview-large img {
		width: 100px;
		height: 100px;
		object-fit: cover;
		border-radius: 8px;
		margin-bottom: 8px;
		border: 1px solid #f1f5f9;
	}

	.b24-file-preview-large .b24-file-name {
		font-size: 11px;
		text-align: center;
		width: 100%;
		margin-bottom: 8px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		color: #1e293b;
		font-weight: 500;
	}

	.b24-file-insert {
		background: #e6f4fa;
		border: none;
		cursor: pointer;
		padding: 4px 10px;
		border-radius: 6px;
		font-size: 11px;
		color: #2fc6f6;
		transition: all 0.2s ease;
		flex: 1;
		max-width: 70px;
		font-weight: 500;
	}

	.b24-file-insert:hover {
		background: #2fc6f6;
		color: #ffffff;
		transform: translateY(-1px);
	}

	.b24-file-remove {
		background: none;
		border: none;
		cursor: pointer;
		padding: 4px 10px;
		border-radius: 6px;
		font-size: 11px;
		color: #ef4444;
		transition: all 0.2s ease;
		flex: 1;
		max-width: 70px;
		font-weight: 500;
	}

	.b24-file-remove:hover {
		background: #fee2e2;
		transform: translateY(-1px);
	}

	/* Стили для файлов в комментариях */
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
		transition: all 0.2s ease;
	}

	.b24-file-preview-large:hover {
		border-color: #2fc6f6;
		box-shadow: 0 2px 8px rgba(47, 198, 246, 0.1);
		transform: translateY(-2px);
	}

	.b24-file-preview-large img {
		width: 100px;
		height: 100px;
		object-fit: cover;
		border-radius: 8px;
		margin-bottom: 8px;
		border: 1px solid #f1f5f9;
	}

	.b24-file-preview-large .b24-file-name {
		font-size: 11px;
		text-align: center;
		width: 100%;
		margin-bottom: 8px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		color: #1e293b;
		font-weight: 500;
	}

	.b24-file-insert {
		background: #e6f4fa;
		border: none;
		cursor: pointer;
		padding: 4px 10px;
		border-radius: 6px;
		font-size: 11px;
		color: #2fc6f6;
		transition: all 0.2s ease;
		width: 100%;
		font-weight: 500;
	}

	.b24-file-insert:hover {
		background: #2fc6f6;
		color: #ffffff;
		transform: translateY(-1px);
	}

	.b24-file-remove {
		background: none;
		border: none;
		cursor: pointer;
		padding: 4px 10px;
		border-radius: 6px;
		font-size: 11px;
		color: #ef4444;
		transition: all 0.2s ease;
		width: 100%;
		font-weight: 500;
	}

	.b24-file-remove:hover {
		background: #fee2e2;
		transform: translateY(-1px);
	}

	/* Стили для файлового элемента (не изображения) */
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
		transform: translateY(-1px);
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

	.b24-file-info {
		flex: 1;
		min-width: 0;
	}

	.b24-file-actions {
		display: flex;
		gap: 8px;
		flex-shrink: 0;
	}

	.b24-file-actions .b24-file-remove {
		background: none;
		border: none;
		cursor: pointer;
		padding: 6px 10px;
		border-radius: 6px;
		font-size: 12px;
		color: #ef4444;
		transition: all 0.2s ease;
		width: auto;
	}

	.b24-file-actions .b24-file-remove:hover {
		background: #fee2e2;
		transform: translateY(-1px);
	}

	@media (max-width: 600px) {
		.b24-files-grid {
			justify-content: center;
		}

		.b24-file-preview-large {
			width: 100px;
		}

		.b24-file-preview-large img {
			width: 80px;
			height: 80px;
		}
	}

	/* Стили для получателей (как в Битрикс24) */
	.b24-feed-post-recipients {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		font-size: 13px;
		color: #828b95;
		margin-top: 2px;
		text-align: justify;
	}

	.b24-feed-post-arrow {
		color: #828b95;
		font-size: 14px;
		margin: 0 2px;
	}

	.b24-feed-post-recipients-list {
		color: #828b95;
		font-size: 13px;
	}

	.b24-feed-post-recipients-list .b24-recipient-link {
		color: #828b95;
		text-decoration: none;
		transition: color 0.2s ease;
		cursor: pointer;
	}

	.b24-feed-post-recipients-list .b24-recipient-link:hover {
		color: #2fc6f6;
		text-decoration: underline;
	}

	/* Альтернативный стиль - получатели на отдельной строке */
	.b24-feed-post-recipients-block {
		display: flex;
		align-items: center;
		gap: 6px;
		font-size: 12px;
		color: #828b95;
		margin-top: 2px;
		flex-wrap: wrap;
	}

	.b24-feed-post-recipients-block .b24-recipient-label {
		color: #828b95;
	}

	.b24-feed-post-recipients-block .b24-recipient-link {
		color: #828b95;
		text-decoration: none;
		transition: color 0.2s ease;
		cursor: pointer;
	}

	.b24-feed-post-recipients-block .b24-recipient-link:hover {
		color: #2fc6f6;
		text-decoration: underline;
	}

	@media (max-width: 600px) {
		.b24-feed-post-recipients {
			font-size: 12px;
			flex-wrap: wrap;
		}

		.b24-feed-post-recipients-list {
			font-size: 12px;
		}
	}

	/* Стили для редактирования изображений */
	.b24-image-editable {
		position: relative;
		display: inline-block;
		margin: 8px 0;
		max-width: 100%;
	}

	.b24-image-editable img {
		display: block;
		max-width: 100%;
		height: auto;
		transition: width 0.1s ease, height 0.1s ease;
	}

	/* 	.b24-image-editable:hover {
		outline: 2px dashed #2fc6f6;
		outline-offset: 2px;
		border-radius: 4px;
	} */

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


	/* Стили для изображений внутри iframe редактора */
	.bx-editor-iframe img {
		max-width: 100% !important;
		height: auto !important;
		border-radius: 8px;
		margin: 8px 0;
	}

	/* Стили для контейнера редактирования внутри iframe */
	.bx-editor-iframe .b24-image-editable {
		position: relative;
		display: inline-block;
		margin: 8px 0;
		max-width: 100%;
	}

	.bx-editor-iframe .b24-image-editable img {
		display: block;
		max-width: 100% !important;
		height: auto !important;
		transition: width 0.1s ease, height 0.1s ease;
	}

	/* 	.bx-editor-iframe .b24-image-editable:hover {
		outline: 2px dashed #2fc6f6;
		outline-offset: 2px;
		border-radius: 4px;
	} */

	.bx-editor-iframe .image-resize-handle {
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

	.bx-editor-iframe .image-resize-handle:hover {
		transform: scale(1.15);
	}

	.bx-editor-iframe .b24-image-editable:hover .image-resize-handle {
		display: block;
	}

	.bx-editor-iframe .image-size-tooltip {
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

	.bx-editor-iframe .b24-image-editable:hover .image-size-tooltip {
		display: block;
	}

	/* Стили для ручек внутри iframe */
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


	/* Стили для меню изображений */
	.b24-image-menu {
		position: absolute !important;
		top: 8px !important;
		right: 8px !important;
		background: rgba(0, 0, 0, 0.85) !important;
		backdrop-filter: blur(8px) !important;
		-webkit-backdrop-filter: blur(8px) !important;
		border-radius: 8px !important;
		padding: 4px !important;
		z-index: 1000 !important;
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
		border: 1px solid rgba(255, 255, 255, 0.1) !important;
		min-width: 150px !important;
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


	.b24-recipients-visible,
	.b24-recipients-hidden {
		display: inline;
		vertical-align: sub;
	}

	.b24-recipients-hidden {
		display: none;
	}

	.b24-recipients-more {
		color: #2fc6f6;
		cursor: pointer;
		text-decoration: none;
		font-weight: 500;
		transition: color 0.2s ease;
	}

	.b24-recipients-more:hover {
		color: #1ea5d8;
		text-decoration: underline;
	}

	.b24-recipients-hide {
		color: #828b95;
		cursor: pointer;
		text-decoration: none;
		font-weight: 500;
		transition: color 0.2s ease;
	}

	.b24-recipients-hide:hover {
		color: #555;
		text-decoration: underline;
	}

	.b24-recipient-link {
		color: #828b95;
		text-decoration: none;
		transition: color 0.2s ease;
		cursor: pointer;
	}

	.b24-recipient-link:hover {
		color: #2fc6f6;
		text-decoration: underline;
	}

	.b24-feed-post-arrow {
		color: #828b95;
		font-size: 14px;
		margin: 0 2px;
	}


	/* Пагинация в серых тонах */
.b24-feed-pagination {
    text-align: center;
    margin-top: 40px;
}

.b24-feed-pagination .navigation {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    padding: 6px 12px;
    border-radius: 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.b24-feed-pagination .navigation-pages {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.b24-feed-pagination .navigation-title {
    display: none;
}

.b24-feed-pagination .navigation-page-numb,
.b24-feed-pagination .navigation-current-page {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #f4f6f9;
    color: #5a6874;
    border: none;
}

.b24-feed-pagination .navigation-page-numb:hover {
    background: #e8ecf1;
    color: #2c3e50;
    transform: scale(1.02);
}

.b24-feed-pagination .navigation-current-page {
    background: #e8ecf1;
    color: #2c3e50;
    font-weight: 600;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
}

.b24-feed-pagination .navigation-arrows {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 8px;
}

.b24-feed-pagination .navigation-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px 14px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #f4f6f9;
    color: #5a6874;
    border: none;
}

.b24-feed-pagination .navigation-button:hover {
    background: #e8ecf1;
    color: #2c3e50;
}

.b24-feed-pagination .navigation-button.navigation-disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.b24-feed-pagination .navigation-ctrl-before,
.b24-feed-pagination .navigation-ctrl-after {
    display: none;
}

@media (max-width: 600px) {
    .b24-feed-pagination .navigation-page-numb,
    .b24-feed-pagination .navigation-current-page {
        min-width: 34px;
        height: 34px;
        font-size: 13px;
    }
    
    .b24-feed-pagination .navigation-button {
        padding: 6px 12px;
        font-size: 12px;
    }
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
							<div class="b24-feed-post-author" data-user-id="<?= $user['ID'] ?>">
								<?= htmlspecialchars($user['FULL_NAME']) ?>
							</div>

							<!-- ====== ВЫВОД ПОЛУЧАТЕЛЕЙ (как в Битрикс24: автор > получатели) ====== -->
							<?php
							// Получаем список получателей
							$recipientsList = isset($arItem['PROPERTIES']['RECIPIENTS']['VALUE']) && is_array($arItem['PROPERTIES']['RECIPIENTS']['VALUE'])
								? $arItem['PROPERTIES']['RECIPIENTS']['VALUE']
								: [];

							// Получаем список исключенных
							$noRecipientsList = isset($arItem['PROPERTIES']['NORECIPIENTS']['VALUE']) && is_array($arItem['PROPERTIES']['NORECIPIENTS']['VALUE'])
								? $arItem['PROPERTIES']['NORECIPIENTS']['VALUE']
								: [];

							// Исключаем автора из списка получателей для отображения
							$displayRecipients = array_diff($recipientsList, [$user['ID']]);

							// ====== ВАЖНО: Исключаем пользователей из списка NORECIPIENTS ======
							$displayRecipients = array_diff($displayRecipients, $noRecipientsList);

							if (!empty($displayRecipients)):
								$recipientNames = [];
								foreach ($displayRecipients as $recipientId) {
									if (isset($arResult["USERS"][$recipientId])) {
										// Формируем полное ФИО: Фамилия Имя Отчество
										$userData = $arResult["USERS"][$recipientId];
										$fullName = trim(
											($userData['LAST_NAME'] ?? '') . ' ' .
												($userData['NAME'] ?? '') . ' ' .
												($userData['SECOND_NAME'] ?? '')
										);
										// Если ФИО пустое, используем FULL_NAME как запасной вариант
										if (empty($fullName)) {
											$fullName = $userData['FULL_NAME'] ?? 'Пользователь';
										}

										$recipientNames[] = [
											'id' => $recipientId,
											'name' => htmlspecialchars($fullName)
										];
									}
								}

								if (!empty($recipientNames)):
									$totalRecipients = count($recipientNames);
									$visibleCount = 3;
									$hiddenCount = $totalRecipients - $visibleCount;
									$visibleRecipients = array_slice($recipientNames, 0, $visibleCount);
									$hiddenRecipients = array_slice($recipientNames, $visibleCount);
									$postId = $arItem['ID'];
									$uniqueId = 'recipients_' . $postId . '_' . md5(uniqid());
							?>
									<div class="b24-feed-post-recipients" data-post-id="<?= $postId ?>">
										<span class="b24-feed-post-arrow">›</span>
										<span class="b24-feed-post-recipients-list">
											<span class="b24-recipients-visible" id="<?= $uniqueId ?>_visible">
												<?php foreach ($visibleRecipients as $index => $recipient): ?>
													<span class="b24-recipient-link" data-user-id="<?= $recipient['id'] ?>"><?= $recipient['name'] ?></span><?= ($index < count($visibleRecipients) - 1) ? ', ' : '' ?>
												<?php endforeach; ?>

												<?php if ($hiddenCount > 0): ?>
													<span class="b24-recipients-more" data-post-id="<?= $postId ?>" data-target="<?= $uniqueId ?>" style="color: #2fc6f6; cursor: pointer; text-decoration: none; font-weight: 500;">
														и ещё <?= $hiddenCount ?> <?= getNumEnding($hiddenCount, ['получатель', 'получателя', 'получателей']) ?>
													</span>
												<?php endif; ?>
											</span>

											<span class="b24-recipients-hidden" id="<?= $uniqueId ?>_hidden" style="display: none;">
												<?php foreach ($visibleRecipients as $index => $recipient): ?>
													<span class="b24-recipient-link" data-user-id="<?= $recipient['id'] ?>"><?= $recipient['name'] ?></span><?= ($index < count($visibleRecipients) - 1) ? ', ' : '' ?>
												<?php endforeach; ?>

												<?php if (!empty($hiddenRecipients)): ?>
													<?php foreach ($hiddenRecipients as $index => $recipient): ?>
														, <span class="b24-recipient-link" data-user-id="<?= $recipient['id'] ?>"><?= $recipient['name'] ?></span><?= ($index < count($hiddenRecipients) - 1) ? ', ' : '' ?>
													<?php endforeach; ?>
												<?php endif; ?>

												<span class="b24-recipients-hide" data-post-id="<?= $postId ?>" data-target="<?= $uniqueId ?>" style="color: #828b95; cursor: pointer; text-decoration: none; font-weight: 500; margin-left: 4px;">
													(Скрыть)
												</span>
											</span>
										</span>
									</div>
								<?php
								endif;
							endif;

							if (empty($displayRecipients)):
								?>
								<div class="b24-feed-post-recipients">
									<span class="b24-feed-post-arrow">›</span>
									<span class="b24-feed-post-recipients-list">
										Всем
									</span>
								</div>
							<?php endif; ?>
							<!-- ====== КОНЕЦ ВЫВОДА ПОЛУЧАТЕЛЕЙ ====== -->

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
										/* array('id' => 'InsertLink', 'compact' => true, 'sort' => 210), */
										/* array('id' => 'InsertImage', 'compact' => false, 'sort' => 220), */
										/* array('id' => 'InsertTable', 'compact' => false, 'sort' => 250), */
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
								<style>
									.b24-files-uploader-comments {
										position: relative;
										cursor: pointer;
									}

									#commentFiles_<?= $arItem['ID'] ?> {
										position: absolute;
										opacity: 0;
										width: 0.1px;
										height: 0.1px;
										z-index: -1;
									}

									/* Или используйте другой подход - инпут всегда видим, но прозрачный */
									.b24-files-uploader-comments input[type="file"] {
										position: absolute;
										top: 0;
										left: 0;
										width: 100%;
										height: 100%;
										opacity: 0;
										cursor: pointer;
										z-index: 10;
									}
								</style>
								<div class="b24-form-group" style="margin-bottom: 15px !important;">
									<div class="b24-files-uploader-comments" id="commentFilesUploader_<?= $arItem['ID'] ?>">
										<div class="b24-files-upload-content-comments">
											<div class="b24-files-upload-icon-comments">📎</div>
											<div class="b24-files-upload-text-comments">Перетащите файлы сюда или</div>
											<button type="button" class="b24-files-upload-btn-comments" data-post-id="<?= $arItem['ID'] ?>" onclick="openCommentFileDialog(<?= $arItem['ID'] ?>); return false;">
												📁 Выбрать файлы
											</button>
											<input type="file" name="COMMENT_FILE[]" id="commentFiles_<?= $arItem['ID'] ?>" multiple style="display: none;" accept=".jpg,.jpeg,.png,.gif,.webp,">
											<div class="b24-files-upload-hint-comments" style="margin-top:20px;">Поддерживаются: JPG, PNG, GIF, WEBP до 20 МБ</div>
										</div>
									</div>
									<div id="commentFilesContainer_<?= $arItem['ID'] ?>"></div>
								</div>
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



	// ========== ФУНКЦИЯ ДЛЯ ТОСТ-УВЕДОМЛЕНИЙ ==========
	function showToast(message, type = 'info', title = '') {
		// Создаем контейнер если его нет
		let container = document.getElementById('toastContainer');
		if (!container) {
			container = document.createElement('div');
			container.id = 'toastContainer';
			container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000; display: flex; flex-direction: column; gap: 10px;';
			document.body.appendChild(container);
		}

		var toast = document.createElement('div');
		toast.className = 'b24-toast b24-toast-' + type;

		var titles = {
			'success': '✅ Успех',
			'error': '⚠️ Ошибка',
			'info': 'ℹ️ Информация'
		};
		var toastTitle = title || titles[type] || titles.info;

		toast.innerHTML = '<div class="b24-toast-close">×</div><div class="b24-toast-title">' + toastTitle + '</div><div class="b24-toast-message">' + message + '</div>';

		// Стили для тоста
		toast.style.cssText = `
        min-width: 300px;
        max-width: 500px;
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 14px;
        animation: slideInRight 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: opacity 0.3s ease;
        background: ${type === 'success' ? '#e6f7e6' : type === 'error' ? '#fee' : '#e8f4fd'};
        color: ${type === 'success' ? '#2d8a2d' : type === 'error' ? '#d32f2f' : '#1a2a3a'};
        border-left: 4px solid ${type === 'success' ? '#2d8a2d' : type === 'error' ? '#d32f2f' : '#2fc6f6'};
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    `;

		container.appendChild(toast);

		var closeBtn = toast.querySelector('.b24-toast-close');
		closeBtn.style.cssText = 'float: right; margin-left: 10px; cursor: pointer; font-weight: bold; color: #999;';

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
		toast.style.animation = 'slideOutRight 0.3s ease forwards';
		setTimeout(function() {
			if (toast && toast.parentNode) toast.parentNode.removeChild(toast);
		}, 300);
	}

	// Добавляем ключевые кадры для анимаций, если их нет
	(function() {
		if (!document.getElementById('toastStyles')) {
			const style = document.createElement('style');
			style.id = 'toastStyles';
			style.textContent = `
            @keyframes slideInRight {
                from { opacity: 0; transform: translateX(100%); }
                to { opacity: 1; transform: translateX(0); }
            }
            @keyframes slideOutRight {
                from { opacity: 1; transform: translateX(0); }
                to { opacity: 0; transform: translateX(100%); }
            }
            .b24-toast-title {
                font-weight: 600;
                margin-bottom: 5px;
            }
            .b24-toast-message {
                font-size: 13px;
            }
            .b24-toast.hiding {
                animation: slideOutRight 0.3s ease forwards;
            }
        `;
			document.head.appendChild(style);
		}
	})();




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

		setTimeout(function() {
			addFileDebugLog(`Initializing file uploader for post ${postId} from renderComments`);
			initCommentFileUploader(postId);

			// Дополнительно проверяем, что обработчик привязан
			const fileInput = document.getElementById('commentFiles_' + postId);
			if (fileInput) {
				addFileDebugLog(`File input found for post ${postId}, has listeners: ${fileInput.hasAttribute('data-initialized') ? 'yes' : 'no'}`);
			} else {
				addFileDebugLog(`File input NOT found for post ${postId}`, 'error');
			}
		}, 200);

		// Инициализируем изменение размера изображений
		setTimeout(initImageResizeOnContent, 100);
		setTimeout(function() {
			initImageResize();
			// Инициализируем слежение за iframe после открытия комментариев
			setTimeout(observeEditorIframes, 500);
		}, 300);


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

		// Получаем файлы
		const files = commentUploadedFiles[postId] || [];

		// Проверяем, есть ли текст или файлы
		const hasText = commentText && commentText.trim() !== '' && commentText !== '<br>' && commentText !== '&nbsp;';
		if (!hasText && files.length === 0) {
			alert('Введите текст комментария или прикрепите файл');
			return false;
		}

		addDebugLog(`Adding comment to post ${postId}, iblock ${iblockId}, text length: ${commentText.length}, files: ${files.length}`);

		const submitBtn = container.querySelector('.b24-feed-comment-submit');
		const originalText = submitBtn.innerHTML;
		submitBtn.disabled = true;
		submitBtn.innerHTML = '⏳ Отправка...';

		// Создаем FormData для отправки файлов
		const formData = new FormData();
		formData.append('action', 'add_comment');
		formData.append('post_id', postId);
		formData.append('iblock_id', iblockId);
		formData.append('text', commentText); // Текст отправляем как есть, без автоматического добавления файлов
		formData.append('sessid', BX.bitrix_sessid());

		// Добавляем файлы
		files.forEach((fileObj, index) => {
			formData.append('COMMENT_FILE[]', fileObj.file);
		});

		// Отправляем через fetch
		fetch('/ajax/comments_likes.php', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			})
			.then(response => response.json())
			.then(data => {
				addDebugLog(`Add comment response: ${JSON.stringify(data)}`);

				if (data.success) {
					addDebugLog(`Comment added successfully to post ${postId}`);
					clearEditorContent(postId);

					// Очищаем файлы
					commentUploadedFiles[postId] = [];
					renderCommentUploadedFiles(postId);
					updateCommentFileInput(postId);

					// Обновляем кеш
					let newComment = data.comment;
					if (!commentsCache[postId]) {
						commentsCache[postId] = [];
					}

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

					renderComments(postId, container, iblockId);
					updateCommentCount(postId);

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
			})
			.catch(error => {
				addDebugLog(`Add comment error: ${error}`, 'error');
				alert('Ошибка соединения при отправке комментария');
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
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
						// Очищаем файлы
						commentUploadedFiles[postId] = [];
						renderCommentUploadedFiles(postId);
						updateCommentFileInput(postId);
						commentsDiv.classList.remove('show');
					});
				}

				initRealtimeComments(postId, iblockId);
				subscribeToPost(postId, iblockId);
				initCommentFileUploader(postId);
			});

			document.querySelectorAll('.b24-feed-post-avatar, .b24-feed-post-author').forEach(function(el) {
				el.addEventListener('click', function(e) {
					e.stopPropagation();
					const userId = this.getAttribute('data-user-id');
					if (userId && userId !== '0') openUserProfile(userId);
				});
			});


			document.querySelectorAll('.b24-recipient-link').forEach(function(el) {
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




	// ========== ФУНКЦИИ ДЛЯ РАБОТЫ С ФАЙЛАМИ В КОММЕНТАРИЯХ ==========

	// Настройки для файлов комментариев
	const commentAllowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
	const commentMaxFileSize = 20 * 1024 * 1024; // 20 MB
	let commentUploadedFiles = {}; // Хранилище файлов по postId

	function isCommentAllowedExtension(filename) {
		const ext = filename.split('.').pop().toLowerCase();
		return commentAllowedExtensions.includes(ext);
	}

	function isCommentImageFile(filename) {
		const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
		const ext = filename.split('.').pop().toLowerCase();
		return imageExtensions.includes(ext);
	}

	function formatCommentFileSize(bytes) {
		if (bytes === 0) return '0 Bytes';
		const k = 1024;
		const sizes = ['Bytes', 'KB', 'MB', 'GB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));
		return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
	}

	function getCommentFileIcon(fileName) {
		if (fileName.match(/\.(jpg|jpeg|png|gif|webp)$/i)) return '🖼️';
		if (fileName.match(/\.(pdf)$/i)) return '📄';
		if (fileName.match(/\.(doc|docx)$/i)) return '📝';
		if (fileName.match(/\.(xls|xlsx)$/i)) return '📊';
		if (fileName.match(/\.(zip|rar)$/i)) return '📦';
		return '📎';
	}

	function previewCommentImage(file, index, postId) {
		return new Promise((resolve) => {
			const reader = new FileReader();
			reader.onload = function(e) {
				if (!commentUploadedFiles[postId]) {
					commentUploadedFiles[postId] = [];
				}
				commentUploadedFiles[postId][index].previewUrl = e.target.result;
				resolve();
			};
			reader.readAsDataURL(file);
		});
	}

	function renderCommentUploadedFiles(postId) {
		const container = document.getElementById('commentFilesContainer_' + postId);
		if (!container) return;

		const files = commentUploadedFiles[postId] || [];

		if (files.length === 0) {
			container.innerHTML = '';
			return;
		}

		const images = files.filter(f => isCommentImageFile(f.name));
		const otherFiles = files.filter(f => !isCommentImageFile(f.name));

		let html = '';

		if (images.length > 0) {
			html += '<div class="b24-files-grid" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px;">';
			images.forEach((file, idx) => {
				const originalIndex = files.findIndex(f => f === file);
				html += `
                <div class="b24-file-preview-large" style="display: inline-flex; flex-direction: column; align-items: center; padding: 12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin: 5px; width: 120px; transition: all 0.2s ease;">
                    <img src="${file.previewUrl || ''}" alt="${escapeHtml(file.name)}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 8px; border: 1px solid #f1f5f9;">
                    <div class="b24-file-name" style="font-size: 11px; text-align: center; width: 100%; margin-bottom: 8px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #1e293b; font-weight: 500;">${escapeHtml(file.name.substring(0, 20))}${file.name.length > 20 ? '...' : ''}</div>
                    <div style="display: flex; flex-direction: column; gap: 4px; width: 100%; align-items:center;">
                        <button type="button" class="b24-file-insert" data-comment-index="${originalIndex}" data-comment-post="${postId}" style="background: #e6f4fa; border: none; cursor: pointer; padding: 4px 10px; border-radius: 6px; font-size: 11px; color: #2fc6f6; transition: all 0.2s ease; width: 100%; font-weight: 500;">В текст</button>
                        <button type="button" class="b24-file-remove" data-comment-index="${originalIndex}" data-comment-post="${postId}" style="background: none; border: none; cursor: pointer; padding: 4px 10px; border-radius: 6px; font-size: 11px; color: #ef4444; transition: all 0.2s ease; width: 100%; font-weight: 500;">Удалить</button>
                    </div>
                </div>
            `;
			});
			html += '</div>';
		}

		if (otherFiles.length > 0) {
			html += '<div class="b24-files-list" style="display: flex; flex-direction: column; gap: 8px; margin-top: 16px;">';
			otherFiles.forEach((file) => {
				const originalIndex = files.findIndex(f => f === file);
				const fileIcon = getCommentFileIcon(file.name);
				html += `
                <div class="b24-file-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease;">
                    <div class="b24-file-preview" style="width: 40px; height: 40px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><span>${fileIcon}</span></div>
                    <div class="b24-file-info" style="flex: 1; min-width: 0;">
                        <div class="b24-file-name" style="font-size: 13px; font-weight: 500; color: #1e293b; margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(file.name)}</div>
                        <div class="b24-file-size" style="font-size: 11px; color: #94a3b8;">${formatCommentFileSize(file.size)}</div>
                    </div>
                    <div class="b24-file-actions" style="display: flex; gap: 8px; flex-shrink: 0;">
                        <button type="button" class="b24-file-remove" data-comment-index="${originalIndex}" data-comment-post="${postId}" style="background: none; border: none; cursor: pointer; padding: 6px 10px; border-radius: 6px; font-size: 12px; color: #ef4444; transition: all 0.2s ease; background: #fee2e2;">🗑️ Удалить</button>
                    </div>
                </div>
            `;
			});
			html += '</div>';
		}

		container.innerHTML = html;

		// Обработчики для кнопки "В текст"
		container.querySelectorAll('.b24-file-insert').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				const index = parseInt(this.dataset.commentIndex);
				const pid = this.dataset.commentPost;
				const files = commentUploadedFiles[pid] || [];
				const file = files[index];
				if (file && file.previewUrl) {
					insertImageToCommentEditor(pid, file.previewUrl);
					showToast('Изображение вставлено в комментарий', 'success', '✅ Готово');
				}
			});

			btn.addEventListener('mouseenter', function() {
				this.style.background = '#2fc6f6';
				this.style.color = '#ffffff';
				this.style.transform = 'translateY(-1px)';
			});
			btn.addEventListener('mouseleave', function() {
				this.style.background = '#e6f4fa';
				this.style.color = '#2fc6f6';
				this.style.transform = 'none';
			});
		});

		// Обработчики удаления
		container.querySelectorAll('.b24-file-remove').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				const index = parseInt(this.dataset.commentIndex);
				const pid = this.dataset.commentPost;
				if (commentUploadedFiles[pid]) {
					commentUploadedFiles[pid].splice(index, 1);
					renderCommentUploadedFiles(pid);
					updateCommentFileInput(pid);
				}
			});

			btn.addEventListener('mouseenter', function() {
				if (!this.classList.contains('b24-file-insert')) {
					this.style.background = '#fee2e2';
					this.style.transform = 'translateY(-1px)';
				}
			});
			btn.addEventListener('mouseleave', function() {
				if (!this.classList.contains('b24-file-insert')) {
					this.style.background = '';
					this.style.transform = 'none';
				}
			});
		});
	}


	// Функция для вставки изображения в редактор комментария
	function insertImageToCommentEditor(postId, imageUrl) {
		const editorId = 'comment_editor_' + postId;

		// Пытаемся вставить через BX.LHE
		if (typeof BX !== 'undefined' && BX.LHE && BX.LHE.GetEditor) {
			const lhe = BX.LHE.GetEditor(editorId);
			if (lhe && lhe.oEditor && typeof lhe.oEditor.InsertHtml === 'function') {
				// Добавляем класс для идентификации изображения в iframe
				lhe.oEditor.InsertHtml(`<img src="${imageUrl}" style="max-width: 100%; height: auto; border-radius: 8px; margin: 8px 0;">`);

				// Инициализируем ресайз после вставки
				setTimeout(function() {
					initImageResize();
				}, 300);
				return;
			}
		}

		// Пытаемся вставить через BXHtmlEditor
		if (typeof BXHtmlEditor !== 'undefined') {
			const editor = BXHtmlEditor.Get(editorId);
			if (editor && typeof editor.InsertHtml === 'function') {
				editor.InsertHtml(`<img src="${imageUrl}" style="max-width: 100%; height: auto; border-radius: 8px; margin: 8px 0;">`);

				setTimeout(function() {
					initImageResize();
				}, 300);
				return;
			}
		}

		// Fallback
		const textarea = document.querySelector('textarea[name="comment_text_' + postId + '"]');
		if (textarea) {
			const currentContent = textarea.value;
			textarea.value = currentContent + `<img src="${imageUrl}" style="max-width: 100%; height: auto; border-radius: 8px; margin: 8px 0;">`;
			showToast('Изображение вставлено (текстовый режим)', 'info', 'ℹ️ Информация');
		} else {
			showToast('Не удалось вставить изображение в редактор', 'error', '⚠️ Ошибка');
		}
	}

	function updateCommentFileInput(postId) {
		const form = document.getElementById('commentForm_' + postId);
		if (!form) return;

		const oldInputs = form.querySelectorAll('input[name="COMMENT_FILE[]"]');
		oldInputs.forEach(input => input.remove());

		const files = commentUploadedFiles[postId] || [];
		for (let i = 0; i < files.length; i++) {
			const input = document.createElement('input');
			input.type = 'file';
			input.name = 'COMMENT_FILE[]';
			input.style.display = 'none';
			const dataTransfer = new DataTransfer();
			dataTransfer.items.add(files[i].file);
			input.files = dataTransfer.files;
			form.appendChild(input);
		}
	}

	async function handleCommentFileSelect(files, postId) {
		const fileInput = document.getElementById('commentFiles_' + postId);
		let hasError = false;

		addDebugLog(`Processing ${files.length} files for post ${postId}`);

		if (!commentUploadedFiles[postId]) {
			commentUploadedFiles[postId] = [];
		}

		for (let file of files) {
			addDebugLog(`Processing file: ${file.name}, size: ${file.size}`);

			if (!isCommentAllowedExtension(file.name)) {
				showToast(`Файл "${file.name}" имеет неподдерживаемое расширение`, 'error', '⚠️ Ошибка');
				hasError = true;
				continue;
			}
			if (file.size > commentMaxFileSize) {
				showToast(`Файл "${file.name}" превышает максимальный размер (20 МБ)`, 'error', '⚠️ Ошибка');
				hasError = true;
				continue;
			}

			const index = commentUploadedFiles[postId].length;
			commentUploadedFiles[postId].push({
				file: file,
				name: file.name,
				size: file.size,
				previewUrl: null
			});

			if (isCommentImageFile(file.name)) {
				await previewCommentImage(file, index, postId);
				// Убираем автоматическую вставку - пользователь сам вставит через кнопку "В текст"
			}
		}

		if (!hasError && files.length > 0) {
			showToast(`Загружено файлов: ${files.length}`, 'success', '✅ Готово');
		}

		renderCommentUploadedFiles(postId);
		updateCommentFileInput(postId);

		// Очищаем input
		if (fileInput) {
			fileInput.value = '';
		}

		addDebugLog(`Files processed for post ${postId}, total: ${commentUploadedFiles[postId].length}`);
	}

	function initCommentFileUploader(postId) {
		const fileInput = document.getElementById('commentFiles_' + postId);

		if (!fileInput) {
			addDebugLog(`File input not found for post ${postId}`, 'error');
			return;
		}

		// Проверяем, инициализирован ли уже
		if (fileInput.getAttribute('data-initialized') === 'true') {
			addDebugLog(`File input for post ${postId} already initialized`);
			return;
		}

		addDebugLog(`Init file uploader for post ${postId}`);

		// Убираем все старые обработчики
		const newFileInput = fileInput.cloneNode(true);
		fileInput.parentNode.replaceChild(newFileInput, fileInput);

		const freshFileInput = document.getElementById('commentFiles_' + postId);
		freshFileInput.setAttribute('data-initialized', 'true');

		// Прямой обработчик изменения
		freshFileInput.addEventListener('change', function(e) {
			e.preventDefault();
			e.stopPropagation();

			const files = Array.from(this.files);
			addDebugLog(`Files selected for post ${postId}: ${files.length} files`);

			if (files.length > 0) {
				handleCommentFileSelect(files, postId);
			}
			// Очищаем input
			this.value = '';
		});

		// Обработчик для клика по всей области загрузки
		const uploader = document.getElementById('commentFilesUploader_' + postId);
		if (uploader) {
			// Убираем старые обработчики
			const newUploader = uploader.cloneNode(true);
			uploader.parentNode.replaceChild(newUploader, uploader);

			const freshUploader = document.getElementById('commentFilesUploader_' + postId);

			// Клик по области загрузки открывает диалог
			freshUploader.addEventListener('click', function(e) {
				// Если клик не по кнопке или её label
				if (!e.target.closest('.b24-files-upload-btn-comments')) {
					e.preventDefault();
					e.stopPropagation();
					addDebugLog(`Uploader clicked for post ${postId}, opening file dialog`);
					freshFileInput.click();
				}
			});

			// Drag & drop
			freshUploader.addEventListener('dragover', function(e) {
				e.preventDefault();
				e.stopPropagation();
				freshUploader.classList.add('drag-over');
			});

			freshUploader.addEventListener('dragleave', function(e) {
				e.preventDefault();
				e.stopPropagation();
				freshUploader.classList.remove('drag-over');
			});

			freshUploader.addEventListener('drop', function(e) {
				e.preventDefault();
				e.stopPropagation();
				freshUploader.classList.remove('drag-over');
				const files = Array.from(e.dataTransfer.files);
				addDebugLog(`Files dropped for post ${postId}: ${files.length} files`);
				if (files.length > 0) {
					handleCommentFileSelect(files, postId);
				}
			});
		}

		// Обработчик для кнопки - используем onclick напрямую
		const uploadBtn = document.querySelector('.b24-files-upload-btn-comments[data-post-id="' + postId + '"]');
		if (uploadBtn) {
			// Убираем старые обработчики
			const newBtn = uploadBtn.cloneNode(true);
			uploadBtn.parentNode.replaceChild(newBtn, uploadBtn);

			const freshBtn = document.querySelector('.b24-files-upload-btn-comments[data-post-id="' + postId + '"]');

			// Используем onclick вместо addEventListener для надежности
			freshBtn.onclick = function(e) {
				e.preventDefault();
				e.stopPropagation();
				addDebugLog(`Upload button clicked for post ${postId}`);
				freshFileInput.click();
				return false;
			};
		}

		addDebugLog(`File uploader initialized for post ${postId}`);
	}
	// ========== КОНЕЦ ФУНКЦИЙ ДЛЯ РАБОТЫ С ФАЙЛАМИ ==========



	// Глобальная функция для открытия диалога выбора файлов
	window.openCommentFileDialog = function(postId) {
		const input = document.getElementById('commentFiles_' + postId);
		if (input) {
			addDebugLog(`Opening file dialog for post ${postId} via global function`);
			input.click();
			return true;
		} else {
			addDebugLog(`File input not found for post ${postId}`, 'error');
			return false;
		}
	};



	// ========== РЕДАКТИРОВАНИЕ ИЗОБРАЖЕНИЙ (ТОЛЬКО ДЛЯ РЕДАКТОРА) ==========

	// Глобальное состояние
	let imageResizeState = {
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

	// Проверяем, находится ли изображение внутри опубликованного поста
	function isInsidePost(img) {
		let parent = img.parentElement;
		let level = 0;
		while (parent && level < 10) {
			if (parent.classList && parent.classList.contains('b24-feed-post')) {
				return true;
			}
			// Также проверяем комментарии
			if (parent.classList && parent.classList.contains('b24-feed-comment-item')) {
				return true;
			}
			if (parent.classList && parent.classList.contains('b24-feed-comments')) {
				return true;
			}
			parent = parent.parentElement;
			level++;
		}
		return false;
	}

	// Проверяем, находится ли изображение внутри редактора
	function isInsideEditor(img) {
		// Сначала проверяем, не находится ли изображение в посте
		if (isInsidePost(img)) {
			return false;
		}

		// Проверяем, есть ли среди родителей элемент редактора
		let parent = img.parentElement;
		while (parent) {
			// Проверяем, является ли родитель iframe с классом bx-editor-iframe
			if (parent.tagName === 'IFRAME' && parent.classList && parent.classList.contains('bx-editor-iframe')) {
				return true;
			}
			// Проверяем, есть ли среди родителей элемент b24-html-editor-wrapper
			if (parent.classList && parent.classList.contains('b24-html-editor-wrapper')) {
				return true;
			}
			parent = parent.parentElement;
		}

		// Дополнительная проверка: если изображение внутри iframe, проверяем через contentWindow
		try {
			const iframes = document.querySelectorAll('.bx-editor-iframe');
			for (let iframe of iframes) {
				try {
					const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
					if (iframeDoc && iframeDoc.contains(img)) {
						return true;
					}
				} catch (e) {}
			}
		} catch (e) {}

		return false;
	}

	// Функция для создания меню при наведении (только для редактора)
	function createImageMenu(img) {
		// Проверяем, находится ли изображение в редакторе
		if (!isInsideEditor(img)) {
			return null;
		}

		// Проверяем, есть ли уже меню
		let menu = img.parentElement.querySelector('.b24-image-menu');
		if (menu) return menu;

		const container = img.parentElement;

		// Создаем меню
		menu = document.createElement('div');
		menu.className = 'b24-image-menu';
		menu.style.cssText = `
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
        border-radius: 8px;
        padding: 4px;
        display: none;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        min-width: 150px;
    `;

		// Кнопка "Изменить размер"
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
        display: flex;
        align-items: center;
        background: transparent;
        border: none;
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        transition: background 0.2s ease;
        width: 100%;
        white-space: nowrap;
    `;
		resizeBtn.addEventListener('mouseenter', function() {
			this.style.background = 'rgba(47, 198, 246, 0.3)';
		});
		resizeBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});

		// Кнопка "Сбросить размер"
		const resetBtn = document.createElement('button');
		resetBtn.className = 'b24-image-menu-btn';
		resetBtn.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
            <path d="M3 12a9 9 0 1 0 9-9m0 0v6m0-6h-6"></path>
        </svg>
        Сбросить
    `;
		resetBtn.style.cssText = `
        display: flex;
        align-items: center;
        background: transparent;
        border: none;
        color: #ffffff;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        cursor: pointer;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        transition: background 0.2s ease;
        width: 100%;
        white-space: nowrap;
    `;
		resetBtn.addEventListener('mouseenter', function() {
			this.style.background = 'rgba(239, 68, 68, 0.3)';
		});
		resetBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});

		// Добавляем кнопки в меню
		menu.appendChild(resizeBtn);
		menu.appendChild(resetBtn);
		container.appendChild(menu);

		// Обработчик для кнопки "Изменить размер"
		resizeBtn.addEventListener('click', function(e) {
			e.stopPropagation();
			e.preventDefault();
			openResizeModal(img);
			hideAllMenus();
		});

		// Обработчик для кнопки "Сбросить"
		resetBtn.addEventListener('click', function(e) {
			e.stopPropagation();
			e.preventDefault();
			resetImageSize(img);
			hideAllMenus();
		});

		return menu;
	}

	// Функция для скрытия всех меню
	function hideAllMenus() {
		document.querySelectorAll('.b24-image-menu').forEach(function(menu) {
			menu.style.display = 'none';
		});
	}

	// Функция для отображения меню (только для редактора)
	function showImageMenu(img) {
		// Проверяем, находится ли изображение в редакторе
		if (!isInsideEditor(img)) {
			return;
		}

		// Сначала скрываем все меню
		hideAllMenus();

		const container = img.parentElement;
		let menu = container.querySelector('.b24-image-menu');

		if (!menu) {
			menu = createImageMenu(img);
			if (!menu) return;
		}

		menu.style.display = 'block';
	}

	// Функция для сброса размера изображения
	function resetImageSize(img) {
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

	// Функция для открытия модального окна изменения размера
	function openResizeModal(img) {
		// Получаем текущие размеры
		const width = img.offsetWidth || img.clientWidth || img.naturalWidth || 100;
		const height = img.offsetHeight || img.clientHeight || img.naturalHeight || 100;
		const aspectRatio = width / height;

		// Сохраняем в глобальное состояние
		imageResizeState.currentImage = img;
		imageResizeState.currentWidth = width;
		imageResizeState.currentHeight = height;
		imageResizeState.aspectRatio = aspectRatio;

		// Если модальное окно уже существует - просто обновляем данные и показываем
		if (imageResizeState.modal) {
			updateModalValues();
			imageResizeState.modal.style.display = 'flex';
			imageResizeState.modal.style.opacity = '1';
			imageResizeState.modal.style.transform = 'scale(1)';
			return;
		}

		// Создаем модальное окно
		createResizeModal();
	}

	// Функция для обновления значений в модальном окне
	function updateModalValues() {
		const img = imageResizeState.currentImage;
		if (!img) return;

		const width = img.offsetWidth || img.clientWidth || img.naturalWidth || 100;
		const height = img.offsetHeight || img.clientHeight || img.naturalHeight || 100;

		// Обновляем состояние
		imageResizeState.currentWidth = width;
		imageResizeState.currentHeight = height;
		imageResizeState.aspectRatio = width / height;

		// Обновляем элементы
		if (imageResizeState.preview) {
			imageResizeState.preview.src = img.src;
			imageResizeState.preview.style.width = width + 'px';
			imageResizeState.preview.style.height = height + 'px';
			imageResizeState.preview.style.maxWidth = '100%';
			imageResizeState.preview.style.maxHeight = '300px';
			imageResizeState.preview.style.objectFit = 'contain';
		}

		if (imageResizeState.widthInput) {
			imageResizeState.widthInput.value = Math.round(width);
		}

		if (imageResizeState.heightInput) {
			imageResizeState.heightInput.value = Math.round(height);
		}

		if (imageResizeState.slider) {
			imageResizeState.slider.value = 100;
		}

		if (imageResizeState.sizeDisplay) {
			imageResizeState.sizeDisplay.textContent = Math.round(width) + ' × ' + Math.round(height);
		}
	}

	// Функция для создания модального окна
	function createResizeModal() {
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

		// Контент модального окна
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

		// Заголовок
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

		// Превью изображения
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

		// Ползунок изменения размера
		const sliderSection = document.createElement('div');
		sliderSection.style.cssText = `
        margin-bottom: 20px;
    `;

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

		// Ручной ввод размеров
		const inputsSection = document.createElement('div');
		inputsSection.style.cssText = `
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 20px;
    `;

		// Ширина
		const widthGroup = document.createElement('div');
		widthGroup.innerHTML = `
        <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 4px;">Ширина (px)</label>
        <input type="number" class="b24-resize-width" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s ease;" min="10">
    `;
		inputsSection.appendChild(widthGroup);

		// Высота
		const heightGroup = document.createElement('div');
		heightGroup.innerHTML = `
        <label style="display: block; font-size: 13px; color: #64748b; margin-bottom: 4px;">Высота (px)</label>
        <input type="number" class="b24-resize-height" style="width: 100%; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s ease;" min="10">
    `;
		inputsSection.appendChild(heightGroup);
		content.appendChild(inputsSection);

		// Чекбокс "Сохранять пропорции"
		const aspectCheckbox = document.createElement('div');
		aspectCheckbox.style.cssText = `
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
    `;
		aspectCheckbox.innerHTML = `
        <input type="checkbox" class="b24-resize-aspect" checked id="resizeAspect" style="width: 18px; height: 18px; cursor: pointer; accent-color: #2fc6f6;">
        <label for="resizeAspect" style="font-size: 14px; color: #1a2a3a; cursor: pointer;">Сохранять пропорции</label>
    `;
		content.appendChild(aspectCheckbox);

		// Кнопки действий
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

		// Сохраняем ссылки на элементы
		imageResizeState.modal = modal;
		imageResizeState.slider = slider;
		imageResizeState.widthInput = widthGroup.querySelector('.b24-resize-width');
		imageResizeState.heightInput = heightGroup.querySelector('.b24-resize-height');
		imageResizeState.preview = preview;
		imageResizeState.sizeDisplay = sliderLabel.querySelector('.b24-resize-size-display');

		// Обновляем значения
		updateModalValues();

		// Обработчик закрытия
		const closeBtn = header.querySelector('.b24-resize-close');
		closeBtn.addEventListener('click', function() {
			closeResizeModal();
		});
		closeBtn.addEventListener('mouseenter', function() {
			this.style.background = '#f1f5f9';
		});
		closeBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});

		// Обработчик отмены
		cancelBtn.addEventListener('click', function() {
			closeResizeModal();
		});
		cancelBtn.addEventListener('mouseenter', function() {
			this.style.background = '#f8fafc';
		});
		cancelBtn.addEventListener('mouseleave', function() {
			this.style.background = 'transparent';
		});

		// Обработчик применения
		applyBtn.addEventListener('click', function() {
			applyResize();
		});
		applyBtn.addEventListener('mouseenter', function() {
			this.style.background = '#1ea5d8';
			this.style.transform = 'translateY(-1px)';
		});
		applyBtn.addEventListener('mouseleave', function() {
			this.style.background = '#2fc6f6';
			this.style.transform = 'none';
		});

		// Закрытие по клику вне модального окна
		modal.addEventListener('click', function(e) {
			if (e.target === modal) {
				closeResizeModal();
			}
		});

		// Закрытие по Escape
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && modal.style.display === 'flex') {
				closeResizeModal();
			}
		});

		// Обработчик ползунка
		slider.addEventListener('input', function() {
			const percent = parseInt(this.value) / 100;
			const newWidth = Math.round(imageResizeState.currentWidth * percent);
			const newHeight = Math.round(imageResizeState.currentHeight * percent);

			imageResizeState.widthInput.value = newWidth;
			imageResizeState.heightInput.value = newHeight;
			imageResizeState.sizeDisplay.textContent = newWidth + ' × ' + newHeight;

			// Обновляем превью
			imageResizeState.preview.style.width = newWidth + 'px';
			imageResizeState.preview.style.height = newHeight + 'px';
		});

		// Обработчик ручного ввода ширины
		imageResizeState.widthInput.addEventListener('input', function() {
			const newWidth = parseInt(this.value) || 0;
			const aspectCheckbox = document.querySelector('.b24-resize-aspect');

			if (aspectCheckbox && aspectCheckbox.checked && newWidth > 0) {
				const newHeight = Math.round(newWidth / imageResizeState.aspectRatio);
				imageResizeState.heightInput.value = newHeight;
				imageResizeState.sizeDisplay.textContent = newWidth + ' × ' + newHeight;

				// Обновляем превью
				imageResizeState.preview.style.width = newWidth + 'px';
				imageResizeState.preview.style.height = newHeight + 'px';
			}
		});

		// Обработчик ручного ввода высоты
		imageResizeState.heightInput.addEventListener('input', function() {
			const newHeight = parseInt(this.value) || 0;
			const aspectCheckbox = document.querySelector('.b24-resize-aspect');

			if (aspectCheckbox && aspectCheckbox.checked && newHeight > 0) {
				const newWidth = Math.round(newHeight * imageResizeState.aspectRatio);
				imageResizeState.widthInput.value = newWidth;
				imageResizeState.sizeDisplay.textContent = newWidth + ' × ' + newHeight;

				// Обновляем превью
				imageResizeState.preview.style.width = newWidth + 'px';
				imageResizeState.preview.style.height = newHeight + 'px';
			}
		});
	}

	// Функция закрытия модального окна
	function closeResizeModal() {
		if (imageResizeState.modal) {
			imageResizeState.modal.style.opacity = '0';
			imageResizeState.modal.style.transform = 'scale(0.95)';
			setTimeout(function() {
				imageResizeState.modal.style.display = 'none';
				imageResizeState.modal.style.opacity = '1';
				imageResizeState.modal.style.transform = 'scale(1)';
			}, 200);
		}
	}

	// Функция применения изменений
	function applyResize() {
		const img = imageResizeState.currentImage;
		if (!img) return;

		const width = parseInt(imageResizeState.widthInput.value) || 0;
		const height = parseInt(imageResizeState.heightInput.value) || 0;

		if (width < 10 || height < 10) {
			showToast('Размер должен быть не менее 10px', 'error', '⚠️ Ошибка');
			return;
		}

		// Применяем размеры
		img.style.width = width + 'px';
		img.style.height = height + 'px';

		showToast('Размер изображения изменен', 'success', '✅ Готово');
		closeResizeModal();
	}

	// Основная функция обработки изображений - ТОЛЬКО ДЛЯ РЕДАКТОРА
	function processImages(images, source = 'unknown') {
		images.forEach(function(img) {
			// Пропускаем уже обработанные
			if (img.dataset.resizable === 'true' || img.dataset.resizable === 'skipped') {
				return;
			}

			// Проверяем родителя
			if (img.parentElement && img.parentElement.classList.contains('b24-image-editable')) {
				return;
			}

			// ===== ВАЖНО: Проверяем, находится ли изображение в редакторе =====
			// Если НЕ в редакторе - пропускаем (не добавляем меню и не обертываем)
			const inEditor = isInsideEditor(img);
			if (!inEditor) {
				// Отмечаем как пропущенное, чтобы не проверять снова
				img.dataset.resizable = 'skipped';
				return;
			}

			const parent = img.parentNode;

			// Создаем контейнер только для изображений в редакторе
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

			// Добавляем класс для индикации возможности редактирования
			img.style.maxWidth = '100%';
			img.style.height = 'auto';

			// Обработчик наведения - показываем меню ТОЛЬКО если изображение в редакторе
			container.addEventListener('mouseenter', function(e) {
				// Еще раз проверяем, что изображение в редакторе
				if (!isInsideEditor(img)) {
					return;
				}
				// Показываем меню
				/*  showImageMenu(img); */
				// Добавляем подсветку
				container.style.outline = '2px dashed #2fc6f6';
				container.style.outlineOffset = '2px';
			});

			container.addEventListener('mouseleave', function(e) {
				setTimeout(function() {
					const menu = container.querySelector('.b24-image-menu');
					if (menu) {
						const isHoveringMenu = menu.matches(':hover');
						if (!isHoveringMenu) {
							menu.style.display = 'none';
						}
					}
					container.style.outline = '';
					container.style.outlineOffset = '';
				}, 200);
			});

			// Обработчик клика на изображение (открываем модальное окно)
			img.addEventListener('click', function(e) {
				e.stopPropagation();
				openResizeModal(img);
				hideAllMenus();
			});

			img.dataset.resizable = 'true';

			// Логируем для отладки
			console.log('✅ Изображение в редакторе обработано');
		});
	}

	// Функция для инициализации
	function initImageResize() {
		console.log('🔄 initImageResize вызван');

		// Основной документ - проверяем все изображения, но обрабатываем только те, что в редакторе
		const mainImages = document.querySelectorAll('.b24-feed-post-text img:not(.b24-image-editable img)');
		console.log('📸 Найдено изображений в основном документе:', mainImages.length);
		processImages(mainImages, 'main');

		// Iframe редактора
		const editorIframes = document.querySelectorAll('.bx-editor-iframe');
		console.log('📦 Найдено iframe редакторов:', editorIframes.length);

		editorIframes.forEach(function(iframe, index) {
			try {
				const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
				if (iframeDoc && iframeDoc.body) {
					const iframeImages = iframeDoc.querySelectorAll('img:not(.b24-image-editable img)');
					console.log(`📸 Iframe ${index}: найдено ${iframeImages.length} изображений`);
					processImages(iframeImages, 'iframe');
				} else {
					console.log(`⏳ Iframe ${index} еще не загружен`);
					iframe.addEventListener('load', function() {
						setTimeout(function() {
							const doc = iframe.contentDocument || iframe.contentWindow.document;
							if (doc) {
								const images = doc.querySelectorAll('img:not(.b24-image-editable img)');
								processImages(images, 'iframe-loaded');
							}
						}, 300);
					});
				}
			} catch (e) {
				console.log(`❌ Ошибка доступа к iframe ${index}:`, e.message);
			}
		});
	}

	// Функция для наблюдения за iframe
	function observeEditorIframes() {
		const editorIframes = document.querySelectorAll('.bx-editor-iframe');

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
						console.log('🔄 Обнаружены новые изображения в iframe');
						setTimeout(function() {
							const images = iframeDoc.querySelectorAll('img:not(.b24-image-editable img)');
							processImages(images, 'iframe-mutation');
						}, 300);
					}
				});

				observer.observe(iframeDoc.body, {
					childList: true,
					subtree: true
				});
			} catch (e) {
				// Игнорируем ошибки доступа к iframe
			}
		});
	}

	// Функция инициализации
	function initImageResizeOnContent() {
		console.log('🚀 initImageResizeOnContent запущен');
		setTimeout(function() {
			initImageResize();
			observeEditorIframes();
		}, 500);

		setTimeout(function() {
			initImageResize();
		}, 2000);

		setTimeout(function() {
			initImageResize();
		}, 5000);
	}

	// Запуск
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function() {
			initImageResizeOnContent();
		});
	} else {
		initImageResizeOnContent();
	}

	// Переинициализация при открытии комментариев
	document.addEventListener('click', function(e) {
		if (e.target.closest('.b24-feed-action-btn.comment')) {
			console.log('💬 Комментарии открыты, переинициализация');
			setTimeout(function() {
				initImageResize();
			}, 500);
			setTimeout(function() {
				initImageResize();
			}, 1500);
		}
	});

	// Переопределяем renderComments
	const originalRenderComments = renderComments;
	renderComments = function(postId, container, iblockId) {
		originalRenderComments(postId, container, iblockId);
		setTimeout(function() {
			initImageResize();
		}, 500);
		setTimeout(function() {
			initImageResize();
		}, 1500);
	};




	// ========== РАСКРЫТИЕ/СКРЫТИЕ ПОЛУЧАТЕЛЕЙ ==========
	document.addEventListener('DOMContentLoaded', function() {
		// Раскрытие списка получателей
		document.querySelectorAll('.b24-recipients-more').forEach(function(moreLink) {
			moreLink.addEventListener('click', function(e) {
				e.stopPropagation();
				e.preventDefault();

				const targetId = this.dataset.target;
				const visibleContainer = document.getElementById(targetId + '_visible');
				const hiddenContainer = document.getElementById(targetId + '_hidden');

				if (visibleContainer && hiddenContainer) {
					visibleContainer.style.display = 'none';
					hiddenContainer.style.display = 'inline';
				}
			});
		});

		// Скрытие списка получателей
		document.querySelectorAll('.b24-recipients-hide').forEach(function(hideLink) {
			hideLink.addEventListener('click', function(e) {
				e.stopPropagation();
				e.preventDefault();

				const targetId = this.dataset.target;
				const visibleContainer = document.getElementById(targetId + '_visible');
				const hiddenContainer = document.getElementById(targetId + '_hidden');

				if (visibleContainer && hiddenContainer) {
					visibleContainer.style.display = 'inline';
					hiddenContainer.style.display = 'none';
				}
			});
		});
	});
</script>

<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/local/components/niki4/utils/smiles_js.php'; 

?>
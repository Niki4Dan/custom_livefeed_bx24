<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

global $USER;
$currentUserId = $USER->GetID();
?>



<style>
    .news-detail-modern {
        max-width: 1000px;
        margin: 0 auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        padding: 30px;
    }

    .news-detail-modern .preview_picture {
        text-align: center;
        margin-bottom: 30px;
    }

    .news-detail-modern .preview_picture img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .news-detail-modern .news-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }

    .news-detail-modern .news-date-time {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f4f8;
        color: #2c5f8a;
        font-size: 13px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 20px;
    }

    .news-detail-modern .news-author {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8f9fa;
        color: #7a8a9a;
        font-size: 13px;
        padding: 4px 12px 4px 8px;
        border-radius: 30px;
        transition: all 0.2s ease;
    }

    .news-detail-modern .news-author:hover {
        background: #eef2f5;
    }

    .news-detail-modern .author-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
    }

    .news-detail-modern .news-author a {
        color: #7a8a9a;
        text-decoration: none;
    }

    .news-detail-modern .news-author a:hover {
        color: #0066cc;
    }

    .news-detail-modern h1 {
        font-size: 32px;
        font-weight: 700;
        color: #1a2a3a;
        margin: 0 0 20px 0;
    }

    .news-detail-modern .detail-text {
        font-size: 16px;
        line-height: 1.8;
        color: #2c3e4e;
        margin: 25px 0;
    }

    .news-detail-modern .detail-text p {
        margin-bottom: 20px;
    }

    .news-detail-modern .detail-text img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .news-detail-modern .news-files {
        margin: 30px 0 20px;
        padding: 25px;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid #0066cc;
    }

    .news-detail-modern .news-files-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1a2a3a;
    }

    .news-detail-modern .files-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .news-detail-modern .file-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #eef2f5;
        flex-wrap: wrap;
    }

    .news-detail-modern .file-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .news-detail-modern .file-icon {
        font-size: 24px;
    }

    .news-detail-modern .file-info {
        flex: 1;
        min-width: 150px;
    }

    .news-detail-modern .file-name {
        font-weight: 500;
        color: #1a2a3a;
        font-size: 14px;
    }

    .news-detail-modern .file-size {
        font-size: 12px;
        color: #7a8a9a;
        margin-left: 10px;
    }

    .news-detail-modern .file-actions {
        display: flex;
        gap: 8px;
    }

    .news-detail-modern .btn-view,
    .news-detail-modern .btn-download {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        cursor: pointer;
        border: none;
    }

    .news-detail-modern .btn-view {
        background: #28a745;
        color: #ffffff;
    }

    .news-detail-modern .btn-view:hover {
        background: #218838;
    }

    .news-detail-modern .btn-download {
        background: #0066cc;
        color: #ffffff;
    }

    .news-detail-modern .btn-download:hover {
        background: #004499;
    }

    /* Модальное окно */
    .file-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .file-modal-content {
        position: relative;
        margin: 2% auto;
        width: 95%;
        height: 92%;
        max-width: 1400px;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
    }

    .file-modal-close {
        position: absolute;
        right: 15px;
        top: 10px;
        color: #1a2a3a;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
    }

    .file-modal-close:hover {
        color: #0066cc;
    }

    .file-modal-body {
        width: 100%;
        height: 100%;
    }

    .file-modal-iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    .file-modal-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #1a1a1a;
    }

    .file-modal-image img {
        max-width: 95%;
        max-height: 95%;
        object-fit: contain;
    }

    .file-modal-video {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
    }

    .file-modal-video video {
        max-width: 95%;
        max-height: 95%;
    }

    @media (max-width: 768px) {
        .news-detail-modern {
            padding: 20px;
        }

        .news-detail-modern h1 {
            font-size: 24px;
        }

        .file-actions {
            margin-top: 10px;
            width: 100%;
        }
    }


    /* Кнопки действий для владельца - в одной линии с датой и автором */
    .news-detail-modern .news-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
        justify-content: space-between;
    }

    .news-detail-modern .meta-left {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .news-detail-modern .meta-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .news-detail-modern .btn-edit,
    .news-detail-modern .btn-delete {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }

    .news-detail-modern .btn-edit {
        background: #009355;
        color: white;
    }

    .news-detail-modern .btn-edit:hover {
        background: #e09200;
        transform: translateY(-1px);
    }

    .news-detail-modern .btn-delete {
        background: #dc3545;
        color: #ffffff;
    }

    .news-detail-modern .btn-delete:hover {
        background: #c82333;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .news-detail-modern .news-meta {
            flex-direction: column;
            align-items: flex-start;
        }

        .news-detail-modern .meta-right {
            width: 100%;
            justify-content: flex-start;
        }
    }

    /* Модальное окно подтверждения удаления */
    .delete-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 20000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .delete-modal-content {
        background: white;
        border-radius: 12px;
        padding: 25px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        animation: slideIn 0.2s ease;
    }

    .delete-modal-content h3 {
        margin: 0 0 15px 0;
        color: #dc3545;
    }

    .delete-modal-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
    }

    .delete-modal-buttons button {
        padding: 8px 20px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .delete-modal-buttons .confirm {
        background: #dc3545;
        color: white;
    }

    .delete-modal-buttons .cancel {
        background: #6c757d;
        color: white;
    }
</style>

<div class="news-detail-modern">

    <!-- Модальное окно -->
    <div id="fileModal" class="file-modal">
        <div class="file-modal-content">
            <span class="file-modal-close">&times;</span>
            <div id="fileModalBody" class="file-modal-body"></div>
        </div>
    </div>

    <!-- Картинка анонса -->
    <? if (is_array($arResult["PREVIEW_PICTURE"]) && !empty($arResult["PREVIEW_PICTURE"]["SRC"])): ?>
        <div class="preview_picture">
            <img src="<?= $arResult["PREVIEW_PICTURE"]["SRC"] ?>" alt="<?= $arResult["NAME"] ?>">
        </div>
    <? endif; ?>

    <!-- Дата, автор и кнопки в одной строке -->
    <div class="news-meta">
        <div class="meta-left">
            <? if ($arResult["DISPLAY_ACTIVE_FROM"]): ?>
                <div class="news-date-time">📅 <?= $arResult["DISPLAY_ACTIVE_FROM"] ?></div>
            <? endif; ?>

            <? if (!empty($arResult["AUTHOR_NAME"])): ?>
                <div class="news-author">
                    <? if (!empty($arResult["AUTHOR_AVATAR"])): ?>
                        <img class="author-avatar" src="<?= $arResult["AUTHOR_AVATAR"] ?>" alt="">
                    <? endif; ?>
                    <? if (!empty($arResult["AUTHOR_PROFILE_URL"])): ?>
                        <a href="<?= $arResult["AUTHOR_PROFILE_URL"] ?>" target="_blank"><?= htmlspecialcharsbx($arResult["AUTHOR_NAME"]) ?></a>
                    <? else: ?>
                        <span><?= htmlspecialcharsbx($arResult["AUTHOR_NAME"]) ?></span>
                    <? endif; ?>
                </div>
            <? endif; ?>
        </div>

        <? if ($arResult["AUTHOR_ID"] == $USER->GetID()): ?>
            <div class="meta-right">

                <a href="<?= $arResult['EDIT_URL'] ?>" class="btn-edit">
                    ✏️ Редактировать
                </a>


                <button onclick="showDeleteConfirm('<?= $arResult["DELETE_URL"] ?>', '<?= addslashes($arResult["NAME"]) ?>')" class="btn-delete">
                    🗑️ Удалить
                </button>

            </div>
        <? endif; ?>
    </div>

    <!-- Заголовок -->
    <h1><?= $arResult["NAME"] ?></h1>

    <!-- Основной текст -->
    <?php
    $detailText = '';
    if (!empty($arResult["DETAIL_TEXT"])):
        $detailText = $arResult["DETAIL_TEXT"];
    elseif (!empty($arResult["PREVIEW_TEXT"])):
        $detailText = $arResult["PREVIEW_TEXT"];
    endif;
    ?>

    <pre>
        <?php /* var_dump($arResult) */?>
    </pre>
    <? if (!empty($detailText)): ?>
        <div><? echo $detailText; ?></div>
    <? endif; ?>

    <!-- Файлы -->
    <? if (!empty($arResult["FILES_LIST"])): ?>
        <div class="news-files">
            <div class="news-files-title">📎 Прикрепленные файлы (<?= count($arResult["FILES_LIST"]) ?>)</div>
            <div class="files-list">
                <? foreach ($arResult["FILES_LIST"] as $arFileData): ?>
                    <? $arFile = $arFileData["FILE"]; ?>
                    <? $bitrixDocsUrl = $arFileData["BITRIX_DOCS_URL"]; ?>
                    <? $ext = strtolower(pathinfo($arFile["ORIGINAL_NAME"], PATHINFO_EXTENSION)); ?>
                    <? $icon = "📄";
                    if (in_array($ext, array("jpg", "jpeg", "png", "gif", "webp"))) $icon = "🖼️";
                    elseif ($ext == "pdf") $icon = "📑";
                    elseif (in_array($ext, array("doc", "docx"))) $icon = "📝";
                    elseif (in_array($ext, array("xls", "xlsx"))) $icon = "📊";
                    elseif (in_array($ext, array("ppt", "pptx"))) $icon = "📽️";
                    elseif (in_array($ext, array("mp4", "avi", "mov"))) $icon = "🎬";
                    elseif (in_array($ext, array("mp3", "wav"))) $icon = "🎵";
                    ?>
                    <div class="file-item">
                        <div class="file-icon"><?= $icon ?></div>
                        <div class="file-info">
                            <span class="file-name"><?= htmlspecialcharsbx($arFile["ORIGINAL_NAME"]) ?></span>
                            <span class="file-size">(<?= round($arFile["FILE_SIZE"] / 1024) ?> КБ)</span>
                        </div>
                        <div class="file-actions">
                            <? if ($bitrixDocsUrl): ?>
                                <button class="btn-view" onclick="openBitrixDocs('<?= $bitrixDocsUrl ?>', '<?= addslashes($arFile["ORIGINAL_NAME"]) ?>')">👁️ Просмотр (Битрикс.Docs)</button>
                            <? else: ?>
                                <button class="btn-view" onclick="previewFile('<?= $arFile["SRC"] ?>', '<?= $ext ?>', '<?= addslashes($arFile["ORIGINAL_NAME"]) ?>')">👁️ Просмотр</button>
                            <? endif; ?>
                            <a href="<?= $arFile["SRC"] ?>" class="btn-download" download>💾 Скачать</a>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    <? endif; ?>

</div>

<script>
    var currentModal = null;

    // Открытие через Битрикс.Docs
    function openBitrixDocs(url, fileName) {
        var modal = document.getElementById('fileModal');
        var modalBody = document.getElementById('fileModalBody');

        modalBody.innerHTML = '';

        var content = `
        <div style="height: 100%; display: flex; flex-direction: column;">
            <div style="padding: 10px; background: #f8fafc; border-bottom: 1px solid #eef2f5; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <span style="font-size: 14px; font-weight: 500;">📄 ${fileName || 'Просмотр документа'}</span>
                <button onclick="downloadFileDirect('${url}', '${fileName}')" style="padding: 4px 10px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer;">💾 Скачать</button>
            </div>
            <iframe class="file-modal-iframe" src="${url}" style="flex: 1;"></iframe>
        </div>
    `;

        modalBody.innerHTML = content;
        modal.style.display = 'block';
        currentModal = modal;
    }

    // Обычный просмотр файлов
    function previewFile(fileUrl, ext, fileName) {
        var modal = document.getElementById('fileModal');
        var modalBody = document.getElementById('fileModalBody');

        modalBody.innerHTML = '';

        var fullUrl = fileUrl;
        if (fullUrl.indexOf('http') !== 0 && fullUrl.indexOf('/') === 0) {
            fullUrl = window.location.origin + fullUrl;
        }

        // Для изображений
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext)) {
            modalBody.innerHTML = '<div class="file-modal-image"><img src="' + fullUrl + '" alt="' + (fileName || 'Изображение') + '"></div>';
        }
        // Для PDF
        else if (ext === 'pdf') {
            modalBody.innerHTML = '<iframe class="file-modal-iframe" src="' + fullUrl + '"></iframe>';
        }
        // Для видео
        else if (['mp4', 'webm', 'ogg', 'avi', 'mov'].includes(ext)) {
            modalBody.innerHTML = '<div class="file-modal-video"><video controls autoplay><source src="' + fullUrl + '">Ваш браузер не поддерживает видео</video></div>';
        }
        // Для офисных документов (если нет Битрикс.Docs)
        else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(ext)) {
            var msViewerUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(fullUrl);
            modalBody.innerHTML = `
            <div style="height: 100%; display: flex; flex-direction: column;">
                <div style="padding: 10px; background: #f8fafc; border-bottom: 1px solid #eef2f5; text-align: center;">
                    <span style="font-size: 13px; color: #7a8a9a;">Просмотр через Microsoft Office Online</span>
                    <button onclick="downloadFileDirect('${fullUrl}', '${fileName}')" style="margin-left: 15px; padding: 4px 10px; background: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer;">💾 Скачать</button>
                </div>
                <iframe class="file-modal-iframe" src="${msViewerUrl}" style="flex: 1;"></iframe>
            </div>
        `;
        }
        // Для всего остального
        else {
            modalBody.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center; padding: 40px;">
                <div style="font-size: 64px; margin-bottom: 20px;">📄</div>
                <h3 style="margin-bottom: 15px;">Предварительный просмотр недоступен</h3>
                <p style="margin-bottom: 25px; color: #7a8a9a;">Файл "${fileName || ''}" нельзя просмотреть в браузере.</p>
                <div style="display: flex; gap: 15px;">
                    <a href="${fullUrl}" class="btn-download" download style="display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; background: #0066cc; color: white; text-decoration: none; border-radius: 6px;">💾 Скачать файл</a>
                    <button onclick="window.open('${fullUrl}', '_blank')" style="display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer;">🔗 Открыть в новой вкладке</button>
                </div>
            </div>
        `;
        }

        modal.style.display = 'block';
        currentModal = modal;
    }

    function downloadFileDirect(fileUrl, fileName) {
        var link = document.createElement('a');
        link.href = fileUrl;
        link.download = fileName || '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Закрытие модального окна
    document.querySelector('.file-modal-close').onclick = function() {
        if (currentModal) {
            currentModal.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target == currentModal) {
            currentModal.style.display = 'none';
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && currentModal && currentModal.style.display === 'block') {
            currentModal.style.display = 'none';
        }
    });






    // Подтверждение удаления
    function showDeleteConfirm(deleteUrl, newsName) {
        // Создаем модальное окно подтверждения
        var modalHtml = `
        <div id="deleteConfirmModal" class="delete-modal">
            <div class="delete-modal-content">
                <div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
                <h3>Удаление новости</h3>
                <p>Вы действительно хотите удалить новость<br><strong>"${newsName}"</strong>?</p>
                <p style="font-size: 12px; color: #7a8a9a; margin-top: 10px;">Это действие невозможно отменить.</p>
                <div class="delete-modal-buttons">
                    <button onclick="confirmDelete('${deleteUrl}')" class="confirm">Да, удалить</button>
                    <button onclick="closeDeleteModal()" class="cancel">Отмена</button>
                </div>
            </div>
        </div>
    `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Закрытие по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    }

    function confirmDelete(deleteUrl) {
        window.location.href = deleteUrl;
    }

    function closeDeleteModal() {
        var modal = document.getElementById('deleteConfirmModal');
        if (modal) {
            modal.remove();
        }
    }
</script>

<? if ($arParams["SET_TITLE"] == "Y"): ?>
    <? $APPLICATION->SetTitle($arResult["NAME"]); ?>
<? endif; ?>
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
$this->setFrameMode(true);


// Получаем параметры фильтрации из URL
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

// Текущая активная вкладка
$activeTab = isset($_GET['tab']) && $_GET['tab'] == 'files' ? 'files' : 'news';
?>

<style>
    .news-list-modern {
        max-width: 1200px;
        margin: 0 auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    /* ========== ТАБЫ ========== */
    .news-list-modern .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        border-bottom: 2px solid #eef2f5;
    }

    .news-list-modern .tab {
        padding: 12px 24px;
        background: none;
        border: none;
        font-size: 15px;
        font-weight: 500;
        color: #5a6a7a;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .news-list-modern .tab:hover {
        color: #0066cc;
    }

    .news-list-modern .tab.active {
        color: #0066cc;
    }

    .news-list-modern .tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #0066cc;
    }

    .news-list-modern .tab-badge {
        display: inline-block;
        background: #e0e4e8;
        color: #5a6a7a;
        border-radius: 20px;
        padding: 2px 8px;
        font-size: 11px;
        margin-left: 8px;
        font-weight: normal;
    }

    .news-list-modern .tab.active .tab-badge {
        background: #0066cc;
        color: #fff;
    }

    /* ========== ФИЛЬТР ========== */
    .news-list-modern .filter-section {
        margin-bottom: 30px;
    }

    .news-list-modern .filter-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #eef2f5;
    }

    .news-list-modern .filter-bar {
        padding: 16px 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
    }

    /* Поисковая строка */
    .news-list-modern .search-wrapper {
        flex: 2;
        min-width: 250px;
        position: relative;
    }

    .news-list-modern .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #cbd5e1;
        pointer-events: none;
    }

    .news-list-modern .search-input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1px solid #e0e4e8;
        border-radius: 40px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s ease;
        background: #f8fafc;
        color: #1a2a3a;
    }

    .news-list-modern .search-input:focus {
        outline: none;
        border-color: #0066cc;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.05);
    }

    .news-list-modern .search-input::placeholder {
        color: #cbd5e1;
    }

    /* Кнопка поиска (только иконка) */
    .news-list-modern .search-button {
        width: 42px;
        height: 42px;
        padding: 0;
        background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
        color: #ffffff;
        border: none;
        border-radius: 42px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .news-list-modern .search-button:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    /* Кнопка фильтра */
    .news-list-modern .filter-toggle {
        height: 42px;
        padding: 0 18px;
        background: #f0f4f8;
        color: #2c5f8a;
        border: none;
        border-radius: 40px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        flex-shrink: 0;
    }

    .news-list-modern .filter-toggle:hover {
        background: #e0e4e8;
    }

    .news-list-modern .filter-toggle.active {
        background: #0066cc;
        color: #fff;
    }

    /* Очистить поиск */
    .news-list-modern .clear-search {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fee;
        color: #dc3545;
        text-decoration: none;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .news-list-modern .clear-search:hover {
        background: #dc3545;
        color: #fff;
        transform: scale(1.05);
    }

    /* Панель расширенных фильтров */
    .news-list-modern .filter-panel {
        display: none;
        padding: 0 20px 20px 20px;
        border-top: 1px solid #eef2f5;
        background: #fafbfc;
    }

    .news-list-modern .filter-panel.show {
        display: block;
        animation: slideDown 0.2s ease;
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

    .news-list-modern .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 50px;
        align-items: flex-end;
        margin-top: 20px;
    }

    .news-list-modern .filter-field {
        flex: 1;
        min-width: 180px;
    }

    .news-list-modern .filter-label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #5a6a7a;
        margin-bottom: 6px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .news-list-modern .filter-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e0e4e8;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s ease;
        background: #fff;
        color: #1a2a3a;
    }

    .news-list-modern .filter-input:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.05);
    }

    .news-list-modern .apply-filters {
        padding: 10px 24px;
        background: #28a745;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .news-list-modern .apply-filters:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    .news-list-modern .clear-filters {
        padding: 10px 20px;
        background: #f0f4f8;
        color: #5a6a7a;
        border: none;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .news-list-modern .clear-filters:hover {
        background: #e0e4e8;
    }

    /* Активные фильтры (чипсы) */
    .news-list-modern .active-filters {
        padding: 12px 20px;
        background: #f8fafc;
        border-top: 1px solid #eef2f5;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .news-list-modern .active-filters-label {
        font-size: 11px;
        font-weight: 600;
        color: #5a6a7a;
        text-transform: uppercase;
    }

    .news-list-modern .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: #fff;
        border: 1px solid #e0e4e8;
        border-radius: 30px;
        font-size: 12px;
        color: #1a2a3a;
    }

    .news-list-modern .filter-chip .remove {
        color: #dc3545;
        text-decoration: none;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
    }

    .news-list-modern .filter-chip .remove:hover {
        transform: scale(1.2);
        display: inline-block;
    }

    .news-list-modern .result-count {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #2c5f8a;
        margin-left: auto;
    }

    /* ========== НОВОСТИ ========== */
    .news-list-modern .pagination {
        text-align: center;
        margin: 30px 0;
        padding: 20px 0;
    }

    .news-list-modern .news-item {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        padding: 20px;
        transition: all 0.3s ease;
        border: 1px solid #eef2f5;
    }

    .news-list-modern .news-item:hover {
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }

    .news-list-modern .news-content {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .news-list-modern .news-image {
        flex-shrink: 0;
        width: 220px;
    }

    .news-list-modern .news-image img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 12px;
        transition: transform 0.3s ease;
    }

    .news-list-modern .news-image img:hover {
        transform: scale(1.02);
    }

    .news-list-modern .news-info {
        flex: 1;
    }

    .news-list-modern .news-date {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f4f8;
        color: #2c5f8a;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 12px;
    }

    .news-list-modern .news-title {
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 12px 0;
        line-height: 1.4;
    }

    .news-list-modern .news-title a {
        color: #1a2a3a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .news-list-modern .news-title a:hover {
        color: #0066cc;
    }

    .news-list-modern .news-preview {
        color: #4a5a6a;
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .news-list-modern .news-preview mark {
        background: #fff3cd;
        padding: 2px 4px;
        border-radius: 4px;
        color: #856404;
    }

    .news-list-modern .news-properties {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eef2f5;
        font-size: 12px;
        color: #7a8a9a;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    /* ========== ФАЙЛЫ ========== */
    .news-list-modern .files-section {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #eef2f5;
        overflow: hidden;
    }

    .news-list-modern .files-header {
        padding: 20px;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f5;
    }

    .news-list-modern .files-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1a2a3a;
    }

    .news-list-modern .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1px;
        background: #eef2f5;
    }

    .news-list-modern .file-card {
        background: #fff;
        padding: 16px;
        transition: all 0.2s ease;
        position: relative;
    }

    .news-list-modern .file-card:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        z-index: 1;
    }

    .news-list-modern .file-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .news-list-modern .file-icon {
        font-size: 32px;
        flex-shrink: 0;
    }

    .news-list-modern .file-details {
        flex: 1;
        min-width: 0;
    }

    .news-list-modern .file-name {
        font-size: 14px;
        font-weight: 500;
        color: #1a2a3a;
        margin-bottom: 8px;
        word-break: break-all;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .news-list-modern .file-meta {
        display: flex;
        gap: 12px;
        font-size: 11px;
        color: #7a8a9a;
        margin-bottom: 8px;
    }

    .news-list-modern .file-size {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .news-list-modern .file-ext {
        display: flex;
        align-items: center;
        gap: 4px;
        text-transform: uppercase;
    }

    .news-list-modern .file-news-link {
        font-size: 11px;
        margin-bottom: 8px;
    }

    .news-list-modern .file-news-link a {
        color: #0066cc;
        text-decoration: none;
    }

    .news-list-modern .file-news-link a:hover {
        text-decoration: underline;
    }

    .news-list-modern .file-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .news-list-modern .file-card:hover .file-actions {
        opacity: 1;
    }

    .news-list-modern .file-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #fff;
        border: 1px solid #e0e4e8;
        border-radius: 8px;
        font-size: 12px;
        color: #2c5f8a;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .news-list-modern .file-btn:hover {
        background: #0066cc;
        border-color: #0066cc;
        color: #fff;
        transform: translateY(-1px);
    }

    .news-list-modern .file-btn.download {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
    }

    .news-list-modern .file-btn.download:hover {
        background: #218838;
        border-color: #218838;
    }

    /* Empty state */
    .news-list-modern .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8fafc;
        border-radius: 16px;
    }

    .news-list-modern .empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .news-list-modern .empty-title {
        font-size: 20px;
        font-weight: 600;
        color: #1a2a3a;
        margin-bottom: 10px;
    }

    /* Пагинация */
    .news-list-modern .pagination .modern-pagination {
        display: inline-flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .news-list-modern .pagination a,
    .news-list-modern .pagination span {
        padding: 8px 14px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .news-list-modern .pagination a {
        background: #ffffff;
        color: #0066cc;
        border: 1px solid #e0e4e8;
    }

    .news-list-modern .pagination a:hover {
        background: #0066cc;
        color: #ffffff;
        border-color: #0066cc;
        transform: translateY(-1px);
    }

    .news-list-modern .pagination span {
        background: #0066cc;
        color: #ffffff;
        border: 1px solid #0066cc;
    }

    /* Адаптивность */
    @media (max-width: 768px) {
        .news-list-modern .filter-bar {
            flex-wrap: wrap;
        }

        .news-list-modern .search-wrapper {
            flex: 1;
            min-width: 200px;
        }

        .news-list-modern .filter-row {
            flex-direction: column;
        }

        .news-list-modern .filter-field {
            width: 100%;
        }

        .news-list-modern .apply-filters,
        .news-list-modern .clear-filters {
            width: 100%;
            justify-content: center;
        }

        .news-list-modern .active-filters {
            flex-direction: column;
            align-items: flex-start;
        }

        .news-list-modern .result-count {
            margin-left: 0;
        }

        .news-list-modern .news-image {
            width: 100%;
        }

        .news-list-modern .news-image img {
            height: auto;
            max-height: 200px;
        }

        .news-list-modern .files-grid {
            grid-template-columns: 1fr;
        }

        .news-list-modern .file-actions {
            opacity: 1;
        }
    }
</style>

<div class="news-list-modern">

    <!-- ========== ТАБЫ ========== -->
    <div class="tabs">
        <button class="tab <?= ($activeTab == 'news' ? 'active' : '') ?>" data-tab="news">
            📰 Новости
            <span class="tab-badge"><?= count($arResult["ITEMS"]) ?></span>
        </button>
        <button class="tab <?= ($activeTab == 'files' ? 'active' : '') ?>" data-tab="files">
            📎 Файлы
            <span class="tab-badge"><?= count($arResult["ALL_FILES"]) ?></span>
        </button>
    </div>

    <!-- ========== ФИЛЬТР ========== -->
    <div class="filter-section">
        <div class="filter-card">
            <div class="filter-bar">
                <!-- Поисковая строка -->
                <div class="search-wrapper">
                    <span class="search-icon">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </span>
                    <form action="" method="GET" style="display:flex; flex: 1;" id="searchForm">
                        <input type="hidden" name="tab" value="<?= $activeTab ?>">
                        <input type="text"
                            name="search"
                            class="search-input"
                            placeholder="Поиск по новостям и файлам..."
                            value="<?= htmlspecialcharsbx($searchQuery) ?>"
                            autocomplete="off">
                    </form>
                </div>

                <!-- Кнопка поиска -->
                <button type="submit" form="searchForm" class="search-button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </button>

                <!-- Кнопка расширенного фильтра -->
                <button type="button" class="filter-toggle" id="filterToggleBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 13 10 21 14 18 14 13 22 3" />
                    </svg>
                    Фильтры
                </button>

                <!-- Очистить поиск -->
                <? if (!empty($searchQuery)): ?>
<!--                     <a href="<?= $APPLICATION->GetCurPageParam('', array('search', 'clear_cache', 'tab')) ?>&tab=<?= $activeTab ?>" class="clear-search" title="Очистить поиск">
                        ✕
                    </a> -->
                <? endif; ?>
            </div>

            <!-- Выпадающая панель с фильтрами по дате -->
            <div class="filter-panel" id="filterPanel">
                <form action="" method="GET" id="dateFilterForm">
                    <input type="hidden" name="tab" value="<?= $activeTab ?>">
                    <div class="filter-row">
                        <div class="filter-field">
                            <div class="filter-label">📅 ДАТА С</div>
                            <input type="date"
                                name="date_from"
                                class="filter-input"
                                value="<?= htmlspecialcharsbx($dateFrom) ?>"
                                placeholder="Дата с">
                        </div>

                        <div class="filter-field">
                            <div class="filter-label">📅 ДАТА ПО</div>
                            <input type="date"
                                name="date_to"
                                class="filter-input"
                                value="<?= htmlspecialcharsbx($dateTo) ?>"
                                placeholder="Дата по">
                        </div>

                        <div class="filter-field" style="flex: 0.5; min-width: auto;">
                            <div class="filter-label">&nbsp;</div>
                            <div class="filter-actions" style="display: flex; gap: 10px;">
                                <button type="submit" class="apply-filters">
                                    ✅ Применить
                                </button>
                                <? if (!empty($dateFrom) || !empty($dateTo)): ?>
                                    <button type="button" class="clear-filters" id="clearDateFilters">
                                        ✖️ Сбросить
                                    </button>
                                <? endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Активные фильтры -->
            <? if (!empty($searchQuery) || !empty($dateFrom) || !empty($dateTo)): ?>
                <div class="active-filters">
                    <span class="active-filters-label">Активные фильтры:</span>
                    <? if (!empty($searchQuery)): ?>
                        <span class="filter-chip">
                            🔍 «<?= htmlspecialcharsbx($searchQuery) ?>»
                            <a href="<?= $APPLICATION->GetCurPageParam('', array('search')) ?>&tab=<?= $activeTab ?>" class="remove" title="Убрать">×</a>
                        </span>
                    <? endif; ?>
                    <? if (!empty($dateFrom)): ?>
                        <span class="filter-chip">
                            📅 с <?= htmlspecialcharsbx($dateFrom) ?>
                            <a href="<?= $APPLICATION->GetCurPageParam('', array('date_from')) ?>&tab=<?= $activeTab ?>" class="remove" title="Убрать">×</a>
                        </span>
                    <? endif; ?>
                    <? if (!empty($dateTo)): ?>
                        <span class="filter-chip">
                            📅 по <?= htmlspecialcharsbx($dateTo) ?>
                            <a href="<?= $APPLICATION->GetCurPageParam('', array('date_to')) ?>&tab=<?= $activeTab ?>" class="remove" title="Убрать">×</a>
                        </span>
                    <? endif; ?>
                    <div class="result-count">
                        📊 Найдено: <strong><?= ($activeTab == 'news' ? $arResult["NAV_RESULT"]->NavRecordCount : count($arResult["ALL_FILES"])) ?></strong>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>

    <script>
        // Показать/скрыть панель расширенных фильтров
        document.addEventListener('DOMContentLoaded', function() {
            var filterBtn = document.getElementById('filterToggleBtn');
            var filterPanel = document.getElementById('filterPanel');

            // Проверяем, есть ли активные фильтры даты
            var hasDateFilters = <?= !empty($dateFrom) || !empty($dateTo) ? 'true' : 'false' ?>;

            if (filterBtn && filterPanel) {
                if (hasDateFilters) {
                    filterPanel.classList.add('show');
                    filterBtn.classList.add('active');
                }

                filterBtn.addEventListener('click', function() {
                    filterPanel.classList.toggle('show');
                    this.classList.toggle('active');
                });
            }

            // Очистка фильтров даты
            var clearDateBtn = document.getElementById('clearDateFilters');
            if (clearDateBtn) {
                clearDateBtn.addEventListener('click', function() {
                    var url = window.location.href.split('?')[0];
                    var params = new URLSearchParams(window.location.search);

                    params.delete('date_from');
                    params.delete('date_to');

                    var newUrl = url;
                    if (params.toString()) {
                        newUrl += '?' + params.toString();
                    }
                    window.location.href = newUrl;
                });
            }

            // Переключение вкладок
            var tabs = document.querySelectorAll('.tab');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    var tabName = this.getAttribute('data-tab');
                    var url = new URL(window.location.href);
                    url.searchParams.set('tab', tabName);
                    window.location.href = url.toString();
                });
            });
        });
    </script>

    <!-- ========== КОНТЕНТ НОВОСТЕЙ ========== -->
    <div id="newsContent" style="display: <?= ($activeTab == 'news' ? 'block' : 'none') ?>;">
        <? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
            <div class="pagination">
                <div class="modern-pagination">
                    <?= $arResult["NAV_STRING"] ?>
                </div>
            </div>
        <? endif; ?>

        <? if (count($arResult["ITEMS"]) > 0): ?>
            <? foreach ($arResult["ITEMS"] as $arItem): ?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

                // Подсветка найденных слов
                $previewText = $arItem["PREVIEW_TEXT"];
                if (!empty($searchQuery)) {
                    $pattern = '/(' . preg_quote($searchQuery, '/') . ')/iu';
                    $previewText = preg_replace($pattern, '<mark>$1</mark>', $previewText);
                }
                ?>

                <div class="news-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                    <div class="news-content">
                        <? if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arItem["PREVIEW_PICTURE"])): ?>
                            <div class="news-image">
                                <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                                    <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>">
                                        <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                            alt="<?= $arItem["NAME"] ?>"
                                            title="<?= $arItem["NAME"] ?>">
                                    </a>
                                <? else: ?>
                                    <img src="<?= $arItem["PREVIEW_PICTURE"]["SRC"] ?>"
                                        alt="<?= $arItem["NAME"] ?>"
                                        title="<?= $arItem["NAME"] ?>">
                                <? endif; ?>
                            </div>
                        <? endif; ?>

                        <div class="news-info">
                            <? if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]): ?>
                                <div class="news-date">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                        <line x1="16" y1="2" x2="16" y2="6" />
                                        <line x1="8" y1="2" x2="8" y2="6" />
                                        <line x1="3" y1="10" x2="21" y2="10" />
                                    </svg>
                                    <?= $arItem["DISPLAY_ACTIVE_FROM"] ?>
                                </div>
                            <? endif; ?>

                            <? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                                <h3 class="news-title">
                                    <? if (!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])): ?>
                                        <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>"><?= $arItem["NAME"] ?></a>
                                    <? else: ?>
                                        <?= $arItem["NAME"] ?>
                                    <? endif; ?>
                                </h3>
                            <? endif; ?>

                            <? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $previewText): ?>
                                <div class="news-preview"><?= $previewText ?></div>
                            <? endif; ?>

                            <div class="news-properties">
                                <? foreach ($arItem["FIELDS"] as $code => $value): ?>
                                    <span>📌 <?= GetMessage("IBLOCK_FIELD_" . $code) ?>: <?= $value; ?></span>
                                <? endforeach; ?>

                                <? foreach ($arItem["DISPLAY_PROPERTIES"] as $pid => $arProperty):
                                    if ($pid == "DOC_TYPE") continue;
                                    if ($pid == "NIKI4_FILES") {
                                        // Показываем количество файлов вместо полного списка
                                        if (!empty($arItem["FILES"])): ?>
                                            <span>📎 Файлов: <?= count($arItem["FILES"]) ?></span>
                                    <? endif;
                                        continue;
                                    }
                                    ?>
                                    <span>🏷️ <?= $arProperty["NAME"] ?>:
                                        <? if (is_array($arProperty["DISPLAY_VALUE"])): ?>
                                            <?= implode(" / ", $arProperty["DISPLAY_VALUE"]); ?>
                                        <? else: ?>
                                            <?= $arProperty["DISPLAY_VALUE"]; ?>
                                        <? endif ?>
                                    </span>
                                <? endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <? endforeach; ?>
        <? else: ?>
            <div class="empty-state">
                <div class="empty-icon">🔍</div>
                <div class="empty-title">Ничего не найдено</div>
                <div class="empty-description">
                    <? if (!empty($searchQuery) || !empty($dateFrom) || !empty($dateTo)): ?>
                        По заданным критериям ничего не найдено.<br>
                        Попробуйте изменить параметры поиска или фильтрации.
                    <? else: ?>
                        В этом разделе пока нет новостей.
                    <? endif; ?>
                </div>
            </div>
        <? endif; ?>

        <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
            <div class="pagination">
                <div class="modern-pagination">
                    <?= $arResult["NAV_STRING"] ?>
                </div>
            </div>
        <? endif; ?>
    </div>

   <!-- ========== КОНТЕНТ ФАЙЛОВ ========== -->
<div id="filesContent" style="display: <?= ($activeTab == 'files' ? 'block' : 'none') ?>;">
    <? if (count($arResult["ALL_FILES"]) > 0): ?>
        <div class="files-section">
            <div class="files-header">
                <h3>📎 Все файлы (<?= count($arResult["ALL_FILES"]) ?>)</h3>
            </div>
            <div class="files-grid">
                <? foreach ($arResult["ALL_FILES"] as $file): ?>
                    <div class="file-card">
                        <div class="file-info">
                            <div class="file-icon"><?= $file["ICON"] ?></div>
                            <div class="file-details">
                                <div class="file-name" title="<?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>">
                                    <?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>
                                </div>
                                <div class="file-meta">
                                    <span class="file-size">
                                        📏 <?= round($file["FILE_SIZE"] / 1024, 1) ?> KB
                                    </span>
                                    <span class="file-ext">
                                        🔤 <?= strtoupper(pathinfo($file["ORIGINAL_NAME"], PATHINFO_EXTENSION)) ?>
                                    </span>
                                    <? if ($file["IS_OFFICE"]): ?>
                                        <span class="file-office-badge" style="background: #e8f5e9; padding: 2px 8px; border-radius: 12px;">
                                            📎 Офисный документ
                                        </span>
                                    <? endif; ?>
                                </div>
                                <div class="file-news-link">
                                    📰 из новости: <a href="<?= htmlspecialcharsbx($file["NEWS_URL"]) ?>"><?= htmlspecialcharsbx($file["NEWS_NAME"]) ?></a>
                                </div>
                                <div class="file-actions">
                                    <? if ($file["IS_OFFICE"]): ?>
                                        <button type="button" 
                                                onclick="openFileDialog('<?= htmlspecialcharsbx($file["VIEWER_URL"]) ?>', '<?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>')" 
                                                class="file-btn" 
                                                title="Просмотреть файл <?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>">
                                            👁️ Просмотр (Яндекс)
                                        </button>
                                    <? else: ?>
                                        <button type="button" 
                                                onclick="openFileDialog('<?= htmlspecialcharsbx($file["FULL_URL"]) ?>', '<?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>')" 
                                                class="file-btn" 
                                                title="Просмотреть файл <?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>">
                                            👁️ Просмотр
                                        </button>
                                    <? endif; ?>
                                    <a href="<?= htmlspecialcharsbx($file["FULL_URL"]) ?>" 
                                       download="<?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>" 
                                       class="file-btn download" 
                                       title="Скачать файл <?= htmlspecialcharsbx($file["ORIGINAL_NAME"]) ?>">
                                        ⬇️ Скачать
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    <? else: ?>
        <div class="empty-state">
            <div class="empty-icon">📂</div>
            <div class="empty-title">Файлы не найдены</div>
            <div class="empty-description">
                <? if (!empty($searchQuery) || !empty($dateFrom) || !empty($dateTo)): ?>
                    По заданным критериям файлы не найдены.<br>
                    Попробуйте изменить параметры поиска или фильтрации.
                <? else: ?>
                    В этом разделе пока нет прикрепленных файлов.
                <? endif; ?>
            </div>
        </div>
    <? endif; ?>
</div>

<!-- Модальное окно для просмотра файлов -->
<div id="fileDialog" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; backdrop-filter: blur(5px);">
    <div style="position: relative; width: 90%; height: 90%; margin: 5% auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: #f8fafc; border-bottom: 1px solid #eef2f5;">
            <h3 style="margin: 0; font-size: 16px; color: #1a2a3a;" id="fileDialogTitle">Просмотр файла</h3>
            <button onclick="closeFileDialog()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #5a6a7a; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s ease;">&times;</button>
        </div>
        <iframe id="fileViewer" src="" style="width: 100%; height: calc(100% - 60px); border: none;"></iframe>
    </div>
</div>

<script>
function openFileDialog(url, filename) {
    var dialog = document.getElementById('fileDialog');
    var viewer = document.getElementById('fileViewer');
    var title = document.getElementById('fileDialogTitle');
    
    title.innerHTML = 'Просмотр файла: ' + filename;
    viewer.src = url;
    dialog.style.display = 'block';
    
    // Блокируем прокрутку страницы
    document.body.style.overflow = 'hidden';
}

function closeFileDialog() {
    var dialog = document.getElementById('fileDialog');
    var viewer = document.getElementById('fileViewer');
    
    dialog.style.display = 'none';
    viewer.src = ''; // Очищаем iframe
    document.body.style.overflow = 'auto';
}

// Закрытие по Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFileDialog();
    }
});

// Закрытие при клике на фон (но не на контент)
document.getElementById('fileDialog').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFileDialog();
    }
});
</script>

<style>
.file-btn {
    cursor: pointer;
}

.file-office-badge {
    font-size: 10px;
}

/* Стили для модального окна */
#fileViewer {
    background: #fff;
}

/* Адаптивность для модального окна */
@media (max-width: 768px) {
    #fileDialog > div {
        width: 95%;
        height: 95%;
        margin: 2.5% auto;
    }
}
</style>

</div>
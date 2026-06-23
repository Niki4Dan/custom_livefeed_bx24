<?
/** @global CMain $APPLICATION */
/** @global CUser $USER */

use Bitrix\Intranet\Integration\Templates\Air\AirTemplate;
use Bitrix\Main\Composite\StaticArea;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetMode;
global $USER;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

$asset = Asset::getInstance();
// Performance optimization for sliders
if (isset($_GET['IFRAME']) && $_GET['IFRAME'] === 'Y' && !isset($_GET['SONET']))
{
	$asset->addCss(SITE_TEMPLATE_PATH . '/src/css/typography.css', true);
	$asset->addCss(SITE_TEMPLATE_PATH . '/src/css/standalone/iframe-scrollbar.css', true);

	return;
}

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$isSearchTitleRequest = !empty($request->get('ajax_call'));
if ($request->isAjaxRequest() && !$isSearchTitleRequest)
{
	return;
}

// Live Feed Ajax
if (isset($_GET['RELOAD']) && $_GET['RELOAD'] == 'Y')
{
	return;
}

Loader::includeModule('intranet');

\Bitrix\Main\UI\Extension::load([
	'intranet.sidepanel.air',
	'socialnetwork.slider',
	'calendar.sliderloader',
	'ui.counter',
	'ui.buttons',
	'ui.icon-set.solid',
	'ui.icon-set.outline',
]);

$isBitrix24Cloud = ModuleManager::isModuleInstalled('bitrix24');

$isCompositeMode = defined('USE_HTML_STATIC_CACHE');
$isIndexPage =
	$APPLICATION->GetCurPage(true) === SITE_DIR . 'stream/index.php' ||
	$APPLICATION->GetCurPage(true) === SITE_DIR . 'index.php' ||
	(defined('BITRIX24_INDEX_PAGE') && constant('BITRIX_INDEX_PAGE') === true)
;

if ($isIndexPage)
{
	if (!defined('BITRIX24_INDEX_PAGE'))
	{
		define('BITRIX24_INDEX_PAGE', true);
	}

	if ($isCompositeMode)
	{
		define('BITRIX24_INDEX_COMPOSITE', true);
	}
}

if (defined('AIR_TEMPLATE_HIDE_CHAR_BAR') && !defined('BX_IM_FULLSCREEN'))
{
	define('BX_IM_FULLSCREEN', true);
}

Loc::loadMessages(__DIR__ . '/site_template.php');

?><!DOCTYPE html>
<html<? if (LANGUAGE_ID === 'tr'):?> lang="<?=LANGUAGE_ID?>"<? endif ?>>
<head>
<? if ($isBitrix24Cloud): ?>
<meta name="apple-itunes-app" content="app-id=561683423">
<link rel="icon" href="<?= SITE_TEMPLATE_PATH ?>/src/images/favicons/favicon.ico" sizes="32x32">
<link rel="icon" href="<?= SITE_TEMPLATE_PATH ?>/src/images/favicons/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="<?= SITE_TEMPLATE_PATH ?>/src/images/favicons/apple-touch-icon.png">
<? endif ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no"><?

$APPLICATION->showHead(false);
$asset->addCss(SITE_TEMPLATE_PATH . '/dist/bitrix24.bundle.css', true);
$asset->addJs(SITE_TEMPLATE_PATH . '/dist/bitrix24.bundle.js', true);
AirTemplate::showHeadAssets();

$layoutMode = \Bitrix\Intranet\UI\LeftMenu\Menu::isCollapsed() ? ' menu-collapsed-mode' : '';
?>
<title><? if (!$isCompositeMode) $APPLICATION->showTitle() ?></title>

<style>
/* Иконка для Новостей отдела */
#bx_left_menu_menu_department_news .menu-item-icon {
    position: relative;
    background: none !important;
}

#bx_left_menu_menu_department_news .menu-item-icon::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    height: 30px;
    background-image: url("/info/Flotenk_O_white.svg");
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}

/* Активная иконка (при наведении или активном пункте) */
#bx_left_menu_menu_department_news.menu-item-active .menu-item-icon::before,
#bx_left_menu_menu_department_news:hover .menu-item-icon::before {
    background-image: url("/info/Flotenk_O.svg");
}






</style>

</head>
<script>


BX.ready(function() {
    const items = document.querySelectorAll('.menu-item-link-text');
    
    items.forEach((i) => {
        if (i.textContent.trim().includes("FLOTENK.LIVE")) {
            const menuItem = i.closest('.menu-item-block');
            
            if (menuItem) {
                // Находим иконку внутри пункта меню
                const icon = menuItem.querySelector('.menu-item-icon');
                
                // Добавляем плавность для текста
                i.style.setProperty('transition', 'color 0.5s ease-in-out, font-weight 0.5s ease-in-out, text-shadow 0.5s ease-in-out', 'important');
                
                // Если иконка найдена, добавляем плавность и ей
                if (icon) {
                    icon.style.setProperty('transition', 'filter 0.5s ease-in-out, box-shadow 0.5s ease-in-out', 'important');
                }
                
                // Упрощенный text-shadow для лучшей производительности
                const textShadow = '0 0 3px #FFFFFF, 0 0 5px #FFFFFF, 0 0 7px #FFFFFF, 0 0 10px #FFFFFF';
                
                // Свечение для иконки
                const iconGlow = 'drop-shadow(0 0 3px white) drop-shadow(0 0 6px white) drop-shadow(0 0 10px white)';
                const iconBoxShadow = '0 0 5px rgba(47, 182, 122, 0.5), 0 0 15px rgba(47, 182, 122, 0.3), 0 0 30px rgba(47, 182, 122, 0.2)';
                
                function updateColor() {
                    const isActive = menuItem.classList.contains('menu-item-active') || 
                                   menuItem.classList.contains('selected') || 
                                   menuItem.classList.contains('current');
                    
                    const isHovered = menuItem.matches(':hover');
                    
                    
                    if (isActive || isHovered) {
                        menuItem.style.borderRadius = "10px";
                        // Применяем к тексту
                        i.style.setProperty('color', '#2FB67A', 'important');
                        i.style.setProperty('font-weight', 'bold', 'important');
                        i.style.setProperty('text-shadow', textShadow, 'important');
                        i.style.setProperty('padding', '5px', 'important')
                        i.style.setProperty('border-radius', '10px', 'important')
                        icon.parentElement.parentElement.style.borderRadius = "10px";
                        // Применяем к иконке
                        if (icon) {
                            icon.style.setProperty('filter', iconGlow, 'important');
                            // Добавляем box-shadow к родительскому контейнеру иконки
                            const iconBox = menuItem.querySelector('.menu-item-icon-box');
                            if (iconBox) {
                                iconBox.style.setProperty('box-shadow', iconBoxShadow, 'important');
                               
                            }
                        }
                    } else {
                        // Убираем с текста
                        i.style.removeProperty('color');
                        i.style.removeProperty('font-weight');
                        i.style.removeProperty('text-shadow');
                        
                        // Убираем с иконки
                        if (icon) {
                            icon.style.removeProperty('filter');
                            const iconBox = menuItem.querySelector('.menu-item-icon-box');
                            if (iconBox) {
                                iconBox.style.removeProperty('box-shadow');
                                iconBox.style.removeProperty('border-radius');
                            }
                        }
                    }
                }
                
                updateColor();
                
                menuItem.addEventListener('mouseenter', updateColor);
                menuItem.addEventListener('mouseleave', updateColor);
                
                const observer = new MutationObserver(updateColor);
                observer.observe(menuItem, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }
        }
    });
});





BX.ready(function() {
    // Функция для удаления пунктов меню
    function removeEditDeleteMenuItems(container) {
        if (!container) return;
        
        // Находим все пункты меню внутри контейнера
        var menuItems = container.querySelectorAll('.ui-popup-menu-item');
        
        menuItems.forEach(function(item) {
            // Ищем текст внутри пункта меню
            var titleText = item.querySelector('.ui-popup-menu-item-title-text');
            
            if (titleText) {
                var text = titleText.textContent.trim();
                
                // Если текст содержит "Редактировать" или "Удалить" - удаляем пункт
                if (text === 'Редактировать' || 
                    text === 'Удалить' || 
                    text.includes('Редактировать') || 
                    text.includes('Удалить')) {
                    
                    console.log('Удаляем пункт меню:', text);
                    item.remove(); // Удаляем элемент из DOM
                }
            }
            
            // Дополнительная проверка по атрибутам (на всякий случай)
            var actionButton = item.querySelector('.ui-popup-menu-item-action');
            if (actionButton) {
                var actionText = actionButton.getAttribute('title') || '';
                if (actionText.includes('Редактировать') || actionText.includes('Удалить')) {
                    item.remove();
                }
            }
        });
        
        // Проверяем, остались ли вообще пункты в меню
        var itemsContainer = container.querySelector('.ui-popup-menu-items');
        if (itemsContainer && itemsContainer.children.length === 0) {
            console.log('Меню пустое, скрываем весь контейнер');
            container.style.display = 'none';
        }
    }
    
    // Функция для скрытия кнопки массовых действий "Удалить"
    function hideBulkDeleteButton() {
        var deleteButtons = document.querySelectorAll('.bx-im-content-bulk-actions-panel__delete-button');
        deleteButtons.forEach(function(button) {
            if (button) {
                console.log('Скрываем кнопку массового удаления');
                button.style.display = 'none';
                // Дополнительно можно скрыть родительский контейнер, если он пустой
                var parentContainer = button.closest('.bx-im-content-bulk-actions-panel__delete');
                if (parentContainer) {
                    // Проверяем, нет ли других видимых кнопок в этом контейнере
                    var otherButtons = parentContainer.querySelectorAll('.bx-im-content-bulk-actions-panel__delete-button, .bx-im-content-bulk-actions-panel__button');
                    var hasVisibleButtons = false;
                    otherButtons.forEach(function(btn) {
                        if (btn.style.display !== 'none' && btn !== button) {
                            hasVisibleButtons = true;
                        }
                    });
                    if (!hasVisibleButtons) {
                        parentContainer.style.display = 'none';
                    }
                }
            }
        });
    }

    // Страховка через MutationObserver (на случай, если меню создается другим способом)
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // ELEMENT_NODE
                        // Проверяем наш контейнер меню
                        if (node.id === 'bx-im-message-context-menu' || 
                            (node.classList && node.classList.contains('popup-window') && 
                             node.querySelector('#bx-im-message-context-menu'))) {
                            
                            setTimeout(function() {
                                removeEditDeleteMenuItems(node);
                            }, 5);
                        }
                        
                        // Проверяем вложенные элементы
                        var contextMenu = node.querySelector('#bx-im-message-context-menu');
                        if (contextMenu) {
                            setTimeout(function() {
                                removeEditDeleteMenuItems(contextMenu);
                            }, 5);
                        }
                        
                        // Проверяем появление кнопки массового удаления
                        if (node.querySelector && node.querySelector('.bx-im-content-bulk-actions-panel__delete-button')) {
                            setTimeout(function() {
                                hideBulkDeleteButton();
                            }, 5);
                        }
                    }
                });
            }
            
            // Также проверяем изменения атрибутов или стилей у существующих элементов
            if (mutation.type === 'attributes' && mutation.target) {
                var target = mutation.target;
                if (target.classList && target.classList.contains('bx-im-content-bulk-actions-panel__delete-button')) {
                    setTimeout(function() {
                        hideBulkDeleteButton();
                    }, 5);
                }
            }
        });
        
        // Дополнительная проверка на наличие кнопок после каждого изменения
        setTimeout(function() {
            hideBulkDeleteButton();
        }, 10);
    });
    
    // Начинаем наблюдение за всем документом
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class']
    });
    
    // Первичная проверка при загрузке
    setTimeout(function() {
        hideBulkDeleteButton();
    }, 100);
    
    console.log('Система скрытия кнопок "Редактировать" и "Удалить" активирована');
    console.log('Система скрытия кнопки массового удаления активирована');
});
</script>
<body class="<?= AirTemplate::getBodyClasses() ?>"><?
	AirTemplate::showBodyAssets();
	$frame = new StaticArea('title');
	$frame->startDynamicArea();
		?><script>
			document.title = "<? AirTemplate::showJsTitle() ?>";
			document.body.classList.add(<?= AirTemplate::getCompositeBodyClasses() ?>);
		</script><?
	$frame->finishDynamicArea();
?>
<div id="a11y-slider-container"></div>
<div class="root<?= $layoutMode ?> js-app">
	<? if ((!$isBitrix24Cloud || $USER->isAdmin()) && !defined('SKIP_SHOW_PANEL')): ?>
	<div id="panel"><? $APPLICATION->showPanel() ?></div>
	<? endif ?>
	<div class="app__left-menu js-app__left-menu --air-context-blurred-bg">
		<? $APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"left_vertical_flotenk", 
	[
		"ROOT_MENU_TYPE" => (file_exists($_SERVER["DOCUMENT_ROOT"].SITE_DIR.".superleft.menu_ext.php")?"superleft":"top"),
		"MENU_CACHE_TYPE" => "Y",
		"MENU_CACHE_TIME" => "604800",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_USE_USERS" => "Y",
		"CACHE_SELECTED_ITEMS" => "N",
		"MENU_CACHE_GET_VARS" => [
		],
		"MAX_LEVEL" => "1",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"ADD_ADMIN_PANEL_BUTTONS" => "N",
		"COMPONENT_TEMPLATE" => "left_vertical_flotenk",
		"CHILD_MENU_TYPE" => "left",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	],
	false
);?>
	</div>
	<header class="app__header" id="app-header">
		<div class="air-header --air-context-blurred-bg" id="header">
			<button class="air-header__burger --ui-hoverable" id="air-header-burger" type="button" aria-label="Menu">
				<span class="air-header__burger-icon"></span>
				<span class="air-header__burger-counter"></span>
			</button>
			<div class="air-header__menu" id="air-header-menu"><?
				$headerArea = new StaticArea('header-menu');
				$headerArea->setContainerId('air-header-menu');
				$headerArea->setAssetMode(AssetMode::STANDARD);
				$headerArea->startDynamicArea();
				$headerArea->setStub('');

				$APPLICATION->showViewContent('above_pagetitle');
				// $APPLICATION->showViewContent('main-navigation');
				$APPLICATION->includeComponent(
					'bitrix:menu',
					'top_horizontal',
					[
						'ROOT_MENU_TYPE' => 'left',
						'CHILD_MENU_TYPE' => 'sub',
						'MENU_CACHE_TYPE' => 'N',
						'MENU_CACHE_TIME' => '604800',
						'MENU_CACHE_USE_GROUPS' => 'N',
						'MENU_CACHE_USE_USERS' => 'Y',
						'CACHE_SELECTED_ITEMS' => 'Y',
						'MENU_CACHE_GET_VARS' => [],
						'MAX_LEVEL' => '3',
						'USE_EXT' => 'Y',
						'DELAY' => 'N',
						'ALLOW_MULTI_SELECT' => 'N',
						'ADD_ADMIN_PANEL_BUTTONS' => 'N',
					],
					false
				);

				$APPLICATION->showViewContent('inline-scripts');

				$headerArea->finishDynamicArea();
				?>
			</div>
			<div class="air-header__personal-info"><?php
				$APPLICATION->includeComponent('bitrix:intranet.search.title', 'air', [
					'CHECK_DATES' => 'N',
					'SHOW_OTHERS' => 'N',
					'TOP_COUNT' => 7,
					'CATEGORY_0_TITLE' => Loc::getMessage('BITRIX24_SEARCH_EMPLOYEE'),
					'CATEGORY_0' => [
						0 => 'custom_users',
					],
					'CATEGORY_1_TITLE' => Loc::getMessage('BITRIX24_SEARCH_GROUP'),
					'CATEGORY_1' => [
						0 => 'custom_sonetgroups',
					],
					'CATEGORY_2_TITLE' => Loc::getMessage('BITRIX24_SEARCH_COLLAB'),
					'CATEGORY_2' => [
						0 => 'custom_collabs',
					],
					'CATEGORY_3_TITLE' => Loc::getMessage('BITRIX24_SEARCH_MENUITEMS'),
					'CATEGORY_3' => [
						0 => 'custom_menuitems',
					],
					'NUM_CATEGORIES' => '4',
					'CATEGORY_OTHERS_TITLE' => Loc::getMessage('BITRIX24_SEARCH_OTHER'),
					'SHOW_INPUT' => 'N',
					'INPUT_ID' => 'search-textbox-input',
					'CONTAINER_ID' => 'search',
					'USE_LANGUAGE_GUESS' => (LANGUAGE_ID == 'ru') ? 'Y' : 'N',
					]);
				?>
				<div class="air-header__logo">
					<? include(__DIR__ . '/logo.php'); ?>
				</div>
				<?php
				$APPLICATION->IncludeComponent(
					'bitrix:intranet.settings.widget',
					'.default'
				);
				?>
				<div class="air-header__buttons"><?php
					if ($USER->IsAdmin()){
					$APPLICATION->includeComponent('bitrix:intranet.invitation.widget', 'air', []);
					$APPLICATION->includeComponent(
						$isBitrix24Cloud
							? 'bitrix:bitrix24.license.widget'
							: 'bitrix:intranet.license.widget'
						,
						'air'
					);
					}
					$APPLICATION->includeComponent('bitrix:intranet.helpdesk', 'air', [], false);
					?>
				</div>
			</div>
		</div>
	</header>
	<div class="app__avatar" id="avatar-area">
		<?php $APPLICATION->IncludeComponent(
	"bitrix:intranet.avatar.widget", 
	"template1", 
	[
		"COMPONENT_TEMPLATE" => "template1",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	],
	false
);?>
	</div>
	<div class="app__page" id="page-area"><?
		$dynamicArea = new StaticArea("page-area");
		$dynamicArea->setContainerId('page-area');
		$dynamicArea->setAssetMode(AssetMode::STANDARD);
		$dynamicArea->setStub('<script>BX.Intranet.Bitrix24.Template.getComposite().showLoader()</script>');
		$dynamicArea->startDynamicArea();
		?>
		<div class="page <?$APPLICATION->showProperty('BodyClass');?>">
			<header class="page__header">
				<div class="page__menu"><? $APPLICATION->showViewContent('page_menu') ?></div>
				<div class="page__toolbar"><? $APPLICATION->includeComponent('bitrix:ui.toolbar', '', []) ?></div>
				<div class="page__actions"><? $APPLICATION->showViewContent('below_pagetitle') ?></div>
			</header>
			<div class="page__workarea">
				<div class="page__sidebar" id="sidebar"><?
					$APPLICATION->showViewContent('sidebar');
					$APPLICATION->showViewContent('sidebar_tools_1');
					$APPLICATION->showViewContent('sidebar_tools_2');
				?></div>
				<main id="air-workarea-content" class="page__workarea-content<?
					$GLOBALS['APPLICATION']->addBufferContent([AirTemplate::class, 'getWorkAreaContent'])?>"><?

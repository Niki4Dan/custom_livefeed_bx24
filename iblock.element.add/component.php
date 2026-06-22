<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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

if ($arParams["PREVIEW_TEXT_USE_HTML_EDITOR"] === "Y" && isset($_POST["PREVIEW_TEXT"]))
{
	$_POST["PREVIEW_TEXT_TYPE"] = "html";
}

if ($arParams["DETAIL_TEXT_USE_HTML_EDITOR"] === "Y" && isset($_POST["DETAIL_TEXT"]))
{
	$_POST["DETAIL_TEXT_TYPE"] = "html";
}

if (!empty($_REQUEST["edit"]))
	$componentPage = "form";
else
	$componentPage = "list";

$arParams["EDIT_URL"] = $APPLICATION->GetCurPage("", array("edit", "delete", "CODE"));
$arParams["LIST_URL"] = $arParams["EDIT_URL"];

$this->IncludeComponentTemplate($componentPage);
?>
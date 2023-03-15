<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

if ($arParams['SHOW_PROFILE_PAGE'] !== 'Y')
{
	LocalRedirect($arParams['SEF_FOLDER']);
}


if ($arParams["MAIN_CHAIN_NAME"] <> '')
{
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams["MAIN_CHAIN_NAME"]), $arResult['SEF_FOLDER']);
}
$APPLICATION->AddChainItem(Loc::getMessage("SPS_CHAIN_VIEWED_PRODUCTS"));

$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.viewed_products.list",
	"",
	array(
        "NECESSARY_COUNT_VIEWED" => $arParams["NECESSARY_COUNT_VIEWED"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"SET_TITLE" =>$arParams["SET_TITLE"]
	),
	$component
);
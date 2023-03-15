<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters = Array(
	"PARAMETERS" => Array(
        "NECESSARY_COUNT_VIEWED" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("NECESSARY_COUNT_VIEWED_PARAM"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
        "CACHE_TIME"  =>  array("DEFAULT"=>36000000),
		"SET_TITLE" => Array(),
	)
);
?>
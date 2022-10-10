<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "map.list",
	"DESCRIPTION" => "ts:map.list",
	
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "news",
			"NAME" => GetMessage("T_IBLOCK_DESC_NEWS"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "news_cmpx",
			),
		),
	),
);

?>
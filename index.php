<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("t1");
?>
<?$APPLICATION->IncludeComponent(
	"ts:map.list", 
	"",
	Array(
		"CACHE_TIME" => "6000",
		"CACHE_TYPE" => "A",
		"IBLOCK_ID" => "29",
		"MESSAGE_404" => "",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "N"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
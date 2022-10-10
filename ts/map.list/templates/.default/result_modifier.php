<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach($arResult["ITEMS"] as $key => $arElement){
	$arRes = array();
	foreach($arParams["PROPERTY_CODE"] as $pid)
	{
		$arRes[$pid] = CIBlockFormatProperties::GetDisplayValue($arElement, $arElement["PROPERTIES"][$pid], "catalog_out");
	}
	$arResult["ITEMS"][$key]["DISPLAY_PROPERTIES"] = $arRes;
	
	$lon_lat = explode (",", $arElement["PROPERTIES"]["MAP"]["VALUE"]);
	$lon = $lon_lat[0];
	$lat = $lon_lat[1];
	
	$arResult["PLACEMARKS"][$arElement["ID"]] = [
		"NAME"=>$arElement["NAME"],
		"ID"=>$arElement["ID"],
		"MAP"=>$arElement["PROPERTIES"]["MAP"]["VALUE"],
		"MAP_LON"=>$lon,
		"MAP_LAT"=>$lat,
		"TEL" => $arElement["PROPERTIES"]["TEL"]["VALUE"],
		"EMAIL" => $arElement["PROPERTIES"]["EMAIL"]["VALUE"],
		"CITY" => $arElement["PROPERTIES"]["CITY"]["VALUE"],
	];
}


global $APPLICATION;
$cp = $this->__component;

if (is_object($cp)){
    $cp->arResult['PLACEMARKS'] = $arResult["PLACEMARKS"];
    $cp->arResult['ITEMS'] = $arResult["ITEMS"];
    $cp->SetResultCacheKeys(array('PLACEMARKS','ITEMS'));
}


?>
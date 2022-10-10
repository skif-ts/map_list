<?  
if(CModule::IncludeModule("iblock")){

	$db_iblock_type = CIBlockType::GetList([], ["=ID"=>"ts"]);
	if($ar_iblock_type = $db_iblock_type->Fetch()){
		$iblock_type = "ts";
	}else{
		$arFields = Array(
			'ID'=>'ts',
			'SECTIONS'=>'Y',
			'IN_RSS'=>'N',
			'SORT'=>100,
			'LANG'=>Array(
				'en'=>Array(
					'NAME'=>'test iblock type',
				),
				'ru'=>Array(
					'NAME'=>'Тестовый тип инфоблоков',
				),
			),
		);
		$obBlocktype = new CIBlockType;
		$iblock_type = $obBlocktype->Add($arFields);
	}

	$rsSites = CSite::GetList($by="sort", $order="desc", array());
	while ($arSite = $rsSites->Fetch())
	{
	$arSites[] = $arSite["LID"];
	}

	$ib_res = CIBlock::GetList(
		Array(), 
		Array(
			'TYPE'=>'ts',
			"CODE" => "offices",		
		), true
	);
	if($ar_res = $ib_res->Fetch()){
		$IBLOCK_ID = $ar_res['ID'];
	}else{
		$ib = new CIBlock;
		$arFields = Array(
		  "ACTIVE" => "Y",
		  "NAME" => "Офисы",
		  "CODE" => "offices",
		  "API_CODE" => "offices",
		  "IBLOCK_TYPE_ID" => $iblock_type,
		  "SITE_ID" => $arSites, //Array(SITE_ID),
		  "SORT" => 100,
		  "GROUP_ID" => Array("1"=>"X", "2"=>"R")
		  );
		  
		$IBLOCK_ID = $ib->Add($arFields);
		echo $IBLOCK_ID;
		
		$ibp = new CIBlockProperty;
		$arFields = Array(
		  "IBLOCK_ID" => $IBLOCK_ID,
		  "NAME" => "Телефон",
		  "ACTIVE" => "Y",
		  "SORT" => "100",
		  "CODE" => "TEL",
		  "PROPERTY_TYPE" => "S",
		  );
		$ibp->Add($arFields);	
		
		$arFields = Array(
		  "IBLOCK_ID" => $IBLOCK_ID,
		  "NAME" => "Email",
		  "ACTIVE" => "Y",
		  "SORT" => "100",
		  "CODE" => "EMAIL",
		  "PROPERTY_TYPE" => "S",
		  );
		$ibp->Add($arFields);
		
		$arFields = Array(
		  "IBLOCK_ID" => $IBLOCK_ID,
		  "NAME" => "Город",
		  "ACTIVE" => "Y",
		  "SORT" => "100",
		  "CODE" => "CITY",
		  "PROPERTY_TYPE" => "S",
		  );
		$ibp->Add($arFields);	
		
		$arFields = Array(
		  "IBLOCK_ID" => $IBLOCK_ID,
		  "NAME" => "Координаты",
		  "ACTIVE" => "Y",
		  "SORT" => "100",
		  "CODE" => "MAP",
		  "PROPERTY_TYPE" => "S",
		  "LIST_TYPE" => "L",
		  "USER_TYPE" => "map_yandex",
		  );
		$ibp->Add($arFields);	
		
	}


	
	
	//__p(222);
	///////////////////////////////////
	//CIBlockElement Add
	//////////////////////////////////
	$el = new CIBlockElement;

	$offices = [
		//["NAME", "TEL", "EMAIL", "CITY", "55.701105136814,37.609787597656"],
		["Офис в Москве", "+7 (900) 123-45-67", "msk@emai.ru", "Москва", "55.755864,37.617698"],
		["Офис в Воронеже", "+7 (901) 123-45-67", "vrn@emai.ru", "Воронеж", "51.671456,39.188768"],
		["Офис в Питере", "+7 (902) 123-45-67", "ptr@emai.ru", "Питер", "59.939099,30.315877"],
		["Офис На Урале", "+7 (903) 123-45-67", "ekb@emai.ru", "Екатеринбург", "56.838011,60.597474"],
		["Владик", "+7 (904) 123-45-67", "vld@emai.ru", "Владивосток", "43.115542,131.885494"],
	];

	$is_offices = [];
	$res = CIBlockElement::GetList(Array(), ["IBLOCK_ID"=>$IBLOCK_ID,], false,false, ["ID", "NAME"]);
	while($ob = $res->GetNext()){
		$is_offices[] = $ob["NAME"];
	}

	foreach($offices as $one_office){
		if(in_array($one_office[0],$is_offices)) continue;
		
		$PROP = array(
			"TEL" => $one_office[1],
			"EMAIL" => $one_office[2],
			"CITY" => $one_office[3],
			"MAP" => $one_office[4],
		);
		
		$arLoadProductArray = Array(
			"IBLOCK_ID"      => $IBLOCK_ID,
			"PROPERTY_VALUES"=> $PROP,
			"NAME"           => $one_office[0],
			"ACTIVE"         => "Y",            // активен
		);
		$el->Add($arLoadProductArray);
	}	
	
}	





?>
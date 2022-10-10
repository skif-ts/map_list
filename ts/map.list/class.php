<?
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
//use \Bitrix\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


 
class SimpleNewsComp extends CBitrixComponent {
    private $_request;

    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
    private function _checkModules() {
        if (   !Loader::includeModule('iblock')
            || !Loader::includeModule('sale')
        ) {
            throw new \Exception('Не загружены модули необходимые для работы модуля');
        }

        return true;
    }


    private function _checkIBlock() {
		if(is_numeric($this->IBLOCK_ID)){
			$arIbFilter = array(
				"ACTIVE" => "Y",
				"ID" => $this->IBLOCK_ID,
			);
		}else{
			$arIbFilter = array(
				"ACTIVE" => "Y",
				"CODE" => $this->IBLOCK_ID,
				"SITE_ID" => SITE_ID,
			);
		}
		$rsIBlock = CIBlock::GetList(array(), $arIbFilter);
		$tarResult = $rsIBlock->GetNext();
		if (!$tarResult)
		{
			$this->abortResultCache();
			\Bitrix\Iblock\Component\Tools::process404(
				trim($this->arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
				,true
				,$this->arParams["SET_STATUS_404"] === "Y"
				,$this->arParams["SHOW_404"] === "Y"
				,$this->arParams["FILE_404"]
			);
			return false;
		}		
		return true;
    }
	
	
	
    /**
     * Обертка над глобальной переменной
     * @return CAllMain|CMain
     */
    private function _app() {
        global $APPLICATION;
        return $APPLICATION;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllUser|CUser
     */
    private function _user() {
        global $USER;
        return $USER;
    }

    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams) {
        // тут пишем логику обработки параметров, дополнение параметрами по умолчанию
        // и прочие нужные вещи
		if(!isset($arParams["CACHE_TIME"]))
			$arParams["CACHE_TIME"] = 36000000;
		
		$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
		if($arParams["NEWS_COUNT"]<=0)
			$arParams["NEWS_COUNT"] = 20;		
		
		if(isset($_GET["year"])){
			$arParams["CURYEAR"] = (int)$_GET["year"];
		}else{
			$arParams["CURYEAR"] = false;
		}	
		
        return $arParams;
    }

    public function executeComponent() {
        $this->_checkModules();
		$this->_app();
		$this->IBLOCK_ID = $this->arParams["IBLOCK_ID"];
		$this->_checkIBlock();

		
		if($this->startResultCache(false, array(
			//($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), 
			$bUSER_HAVE_ACCESS, 
			//$arNavigation, 
			//$arrFilter, 
			//$pagerParameters,
			//$cur_year,
		))){
			
			$this->arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
			//SELECT
			$this->arSelect = array(
				"ID",
				"IBLOCK_ID",
				"IBLOCK_SECTION_ID",
				"NAME",
				"ACTIVE_FROM",
				"TIMESTAMP_X",
				"DETAIL_PAGE_URL",
				"LIST_PAGE_URL",
				"DETAIL_TEXT",
				"DETAIL_TEXT_TYPE",
				"PREVIEW_TEXT",
				"PREVIEW_TEXT_TYPE",
				"PREVIEW_PICTURE",
			);
			
			$arFilter = array (
				"IBLOCK_ID" => $this->IBLOCK_ID,
				"ACTIVE" => "Y",
			);


			$this->arResult["ITEMS"] = array();
			$this->arResult["ELEMENTS"] = array();
			
			$this->getItems($arFilter);
			

			$this->setResultCacheKeys(array(
				"ID",
				"IBLOCK_TYPE_ID",
				"LIST_PAGE_URL",
				"NAV_CACHED_DATA",
				"NAME",
				//"SECTION",
				"ELEMENTS",
				//"ITEMS",
				"IPROPERTY_VALUES",
				"ITEMS_TIMESTAMP_X",
			));
			
			$this->includeComponentTemplate();
		}		
    }
	
	
	public function getItems($arFilter)
	{

		$nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
		$nav->allowAllRecords(true)
		   ->initFromUri();

		$res = \Bitrix\Iblock\ElementTable::getList(array(
			"select" => array("ID", "NAME", 'IBLOCK_ID'),
			"filter" => array("IBLOCK_ID" => $this->IBLOCK_ID, '=ACTIVE' => 'Y',),
			"order"  => array("ID" => "ASC")
		));
		//$nav->setRecordCount($res->getCount());
		while ($arItem = $res->fetch()) {
			$dbProperty = \CIBlockElement::getProperty(
				$arItem['IBLOCK_ID'], 
				$arItem['ID'], array("sort", "asc"), 
				array()
			);
			while ($arProperty = $dbProperty->GetNext()) {

				$arItem["PROPERTIES"][$arProperty['CODE']] = $arProperty;

			}
			
			$id = (int)$arItem['ID'];
			$this->arResult["ITEMS"][$id] = $arItem;
			$this->arResult["ELEMENTS"][] = $id;
			
		}
		unset($arItem);			
		
		if(empty($this->arResult["ITEMS"])){
			$this->abortResultCache();
			\Bitrix\Iblock\Component\Tools::process404(
				trim($this->arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
				,true
				,$this->arParams["SET_STATUS_404"] === "Y"
				,$this->arParams["SHOW_404"] === "Y"
				,$this->arParams["FILE_404"]
			);
			return;
		}else{  //if (!empty($arResult['ITEMS']))
			//__p('!empty($this->arResult["ITEMS"])');
		}
	}
	
}


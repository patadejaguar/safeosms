<style>
	.ra-settings {
	padding-left: 8px;
    padding-right: 20px;
	}
	.container-collapsed {
    margin-bottom: 20px;
	padding: 5px;
	}
	.component-settings, .expand-settings {
    display: none;
    padding: 5px;
	}
	.iframe-buttn {
	float: left;
	margin-bottom: 20px;
	}
	.inner-divs label {
    display: inline-block;
    padding-right: 10px;
    margin-top: 4px;
	}
	.effect-text {
    padding-left: 30px;
    margin-top: 4px;
	}
	#effect-code {
    padding: 5px 10px;
    border-radius: 2px;
    border: 1px solid rgb(170, 170, 170);
	}
	#effect-code code {
    color: #F00;
    font-size: 1.3em;
	}
	.collapsedlegend {
    cursor: pointer;
    background-image: url("../admin/template/images/utick.png");
    background-repeat: no-repeat;
    background-position: 92% center;
    padding: 0px 18px 0px 8px;
    margin: 6px;
	}
</style>
<?php
global $TEMPLATE; ?>
<link href="../theme/<?php echo $TEMPLATE; ?>/css/animate.min.css" rel="stylesheet">
<?php
	if(!defined('IN_GS') || !cookie_check()) die;
	
	global $error_file, $eror_path, $eror_component;
	$error_file=false;
	$eror_component=false;
	
	if (!function_exists('component_exists')) {
		function component_exists($id) {
			global $components;
			if (!$components) {
				if (file_exists(GSDATAOTHERPATH.'components.xml')) {
					$data = getXML(GSDATAOTHERPATH.'components.xml');
					$components = $data->item;
					} else {
					$components = array();
				}
			}
			$exists = FALSE;
			if (count($components) > 0) {
				foreach ($components as $component) {
					if ($id == $component->slug) {
						$exists = TRUE;
						break;
					}
				}
			}
			return $exists;
		}
	}
	
	function custom_getXML($file, $nocdata = true) {
		if (!file_exists($file)) {
			global $error_file, $eror_path;
			$eror_path = $file;
			$error_file=true;
			return false;
		}
		$xml = @file_get_contents($file);
		if ($xml) {
			if ($nocdata) {
				$data = simplexml_load_string($xml, 'SimpleXMLExtended', LIBXML_NOCDATA);
				} else {
				$data = simplexml_load_string($xml, 'SimpleXMLExtended');
			}
			return $data;
		}
	}
	
	function remove_component($file,$slug) {
		$data = custom_getXML($file, false);
		foreach($data->item as $item) {
			if($item->slug == $slug) {
				$dom = dom_import_simplexml($item);
				$dom->parentNode->removeChild($dom);
			}
		}
		XMLsave($data, $file);
	}
	
	function import_component($file, $component_slug) {
		global $TEMPLATE;
		$c_path = trim("../theme/" . $TEMPLATE)."/inc/".$component_slug.".php";
		$c_title = str_replace("-"," ", ucfirst($component_slug));
		if(file_exists($c_path)) include($c_path);
		else {
			global $eror_component;
			$eror_component=true;
			return false;
		}
		$param = htmlentities($param,ENT_QUOTES,'UTF-8');
		$data = custom_getXML($file, false);
		if ($data) {
			$component = $data;
			$components = $component->addChild('item');
			$compons = $components->addChild('title');
			$compons->addCData($c_title);
			$compons = $components->addChild('slug', $component_slug);
			$compons = $components->addChild('value');
			$compons->addCData( $param);
			XMLsave($data, $file);
		}
	}
	
	function check_data_contact() {
		/*******************************************
		***      Create XML for Contact form     ***
		***             if not exist             ***
		********************************************/
		if (!file_exists(GSDATAPAGESPATH.'contact.xml')) {
			global $pagesArray, $USR, $USR1;
			$pagesSorted = subval_sort($pagesArray, 'menuOrder');
			if (count($pagesSorted) != 0) {
				$menu_order = 0;
				foreach ($pagesSorted as $page) {
					if($menu_order < (int)$page['menuOrder']) $curr_order = (int)$page['menuOrder'];
				}
				$curr_order = $curr_order + 1;
			}
			else $curr_order = 0;
			$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
			$xml->addChild('pubDate', date(DATE_RFC2822));
			$dataval = $xml->addChild('title');
			$dataval->addCData("Contact");
			$dataval = $xml->addChild('url');
			$dataval->addCData("contact");
			$dataval = $xml->addChild('meta');
			$dataval->addCData("");
			$dataval = $xml->addChild('metad');
			$dataval->addCData("");
			$dataval = $xml->addChild('menu');
			$dataval->addCData("Contact");
			$dataval = $xml->addChild('menuOrder');
			$dataval->addCData($curr_order);
			$dataval = $xml->addChild('menuStatus');
			$dataval->addCData("Y");
			$dataval = $xml->addChild('template');
			$dataval->addCData("contact.php");
			$dataval = $xml->addChild('parent');
			$dataval->addCData("");
			$dataval = $xml->addChild('content');
			$dataval->addCData("E-message sending form");
			$dataval = $xml->addChild('private');
			$dataval->addCData("");
			$dataval = $xml->addChild('author');
			$dataval->addCData(isset($USR1)?$USR1:$USR);
			$xml->asXML(GSDATAPAGESPATH.'contact.xml');
			if (function_exists('i18n_init')) {
				if (!file_exists(GSDATAPAGESPATH.'contact_lt.xml')) {
					$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
					$xml->addChild('pubDate', date(DATE_RFC2822));
					$dataval = $xml->addChild('title');
					$dataval->addCData("Kontaktai");
					$dataval = $xml->addChild('url');
					$dataval->addCData("contact_lt");
					$dataval = $xml->addChild('meta');
					$dataval->addCData("");
					$dataval = $xml->addChild('metad');
					$dataval->addCData("");
					$dataval = $xml->addChild('menu');
					$dataval->addCData("Kontaktai");
					$dataval = $xml->addChild('menuOrder');
					$dataval->addCData("0");
					$dataval = $xml->addChild('menuStatus');
					$dataval->addCData("");
					$dataval = $xml->addChild('template');
					$dataval->addCData("contact.php");
					$dataval = $xml->addChild('parent');
					$dataval->addCData("");
					$dataval = $xml->addChild('content');
					$dataval->addCData("El. pranešimų siuntimo formą");
					$dataval = $xml->addChild('private');
					$dataval->addCData("");
					$dataval = $xml->addChild('author');
					$dataval->addCData(isset($USR1)?$USR1:$USR);
					$xml->asXML(GSDATAPAGESPATH.'contact_lt.xml');
				}
				if (!file_exists(GSDATAPAGESPATH.'contact_ru.xml')) {
					$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
					$xml->addChild('pubDate', date(DATE_RFC2822));
					$dataval = $xml->addChild('title');
					$dataval->addCData("Контакты");
					$dataval = $xml->addChild('url');
					$dataval->addCData("contact_ru");
					$dataval = $xml->addChild('meta');
					$dataval->addCData("");
					$dataval = $xml->addChild('metad');
					$dataval->addCData("");
					$dataval = $xml->addChild('menu');
					$dataval->addCData("Контакты");
					$dataval = $xml->addChild('menuOrder');
					$dataval->addCData("0");
					$dataval = $xml->addChild('menuStatus');
					$dataval->addCData("");
					$dataval = $xml->addChild('template');
					$dataval->addCData("contact.php");
					$dataval = $xml->addChild('parent');
					$dataval->addCData("");
					$dataval = $xml->addChild('content');
					$dataval->addCData("E-форма отправки сообщений");
					$dataval = $xml->addChild('private');
					$dataval->addCData("");
					$dataval = $xml->addChild('author');
					$dataval->addCData(isset($USR1)?$USR1:$USR);
					$xml->asXML(GSDATAPAGESPATH.'contact_ru.xml');
				}
			}
			$xml_file = @file_get_contents(GSDATAOTHERPATH.'pages.xml');
			$xml_pages = simplexml_load_string($xml_file, 'SimpleXMLExtended');
			if ($xml_pages) {
				$items = $xml_pages->addChild('item');
				$items->addChild('pubDate', date(DATE_RFC2822));
				$datavap = $items->addChild('title');
				$datavap->addCData("Contact");
				$datavap = $items->addChild('url');
				$datavap->addCData("contact");
				$datavap = $items->addChild('meta');
				$datavap->addCData("");
				$datavap = $items->addChild('metad');
				$datavap->addCData("");
				$datavap = $items->addChild('menu');
				$datavap->addCData("Contact");
				$datavap = $items->addChild('menuOrder');
				$datavap->addCData($curr_order);
				$datavap = $items->addChild('menuStatus');
				$datavap->addCData("Y");
				$datavap = $items->addChild('template');
				$datavap->addCData("contact.php");
				$datavap = $items->addChild('parent');
				$datavap->addCData("");
				$datavap = $items->addChild('private');
				$datavap->addCData("");
				$datavap = $items->addChild('author');
				$datavap->addCData(isset($USR1)?$USR1:$USR);
				$datavap = $items->addChild('slug');
				$datavap->addCData("contact");
				$datavap = $items->addChild('filename');
				$datavap->addCData("contact.xml");
				if (function_exists('i18n_init')) {
					/*** LT ***/
					$items = $xml_pages->addChild('item');
					$items->addChild('pubDate', date(DATE_RFC2822));
					$datavap = $items->addChild('title');
					$datavap->addCData("Kontaktai");
					$datavap = $items->addChild('url');
					$datavap->addCData("contact_lt");
					$datavap = $items->addChild('meta');
					$datavap->addCData("");
					$datavap = $items->addChild('metad');
					$datavap->addCData("");
					$datavap = $items->addChild('menu');
					$datavap->addCData("Kontaktai");
					$datavap = $items->addChild('menuOrder');
					$datavap->addCData("0");
					$datavap = $items->addChild('menuStatus');
					$datavap->addCData("Y");
					$datavap = $items->addChild('template');
					$datavap->addCData("contact.php");
					$datavap = $items->addChild('parent');
					$datavap->addCData("");
					$datavap = $items->addChild('private');
					$datavap->addCData("");
					$datavap = $items->addChild('author');
					$datavap->addCData(isset($USR1)?$USR1:$USR);
					$datavap = $items->addChild('slug');
					$datavap->addCData("contact_lt");
					$datavap = $items->addChild('filename');
					$datavap->addCData("contact_lt.xml");
					/*** RU ***/
					$items = $xml_pages->addChild('item');
					$items->addChild('pubDate', date(DATE_RFC2822));
					$datavap = $items->addChild('title');
					$datavap->addCData("Контакты");
					$datavap = $items->addChild('url');
					$datavap->addCData("contact_ru");
					$datavap = $items->addChild('meta');
					$datavap->addCData("");
					$datavap = $items->addChild('metad');
					$datavap->addCData("");
					$datavap = $items->addChild('menu');
					$datavap->addCData("Контакты");
					$datavap = $items->addChild('menuOrder');
					$datavap->addCData("0");
					$datavap = $items->addChild('menuStatus');
					$datavap->addCData("Y");
					$datavap = $items->addChild('template');
					$datavap->addCData("contact.php");
					$datavap = $items->addChild('parent');
					$datavap->addCData("");
					$datavap = $items->addChild('private');
					$datavap->addCData("");
					$datavap = $items->addChild('author');
					$datavap->addCData(isset($USR1)?$USR1:$USR);
					$datavap = $items->addChild('slug');
					$datavap->addCData("contact_ru");
					$datavap = $items->addChild('filename');
					$datavap->addCData("contact_ru.xml");
				}
				$xml_pages->asXML(GSDATAOTHERPATH.'pages.xml');
			}
			if (function_exists('i18n_init')) {
				$xml_file = @file_get_contents(GSDATAOTHERPATH.'i18n_menu_cache.xml');
				$xml_menu = simplexml_load_string($xml_file, 'SimpleXMLExtended');
				if ($xml_menu) {
					$items = $xml_menu->addChild('page');
					$datavam = $items->addChild('url');
					$datavam->addCData("contact");
					$datavam = $items->addChild('menuStatus');
					$datavam->addCData("Y");
					$datavam = $items->addChild('menuOrder');
					$datavam->addCData($curr_order);
					$datavam = $items->addChild('menu');
					$datavam->addCData("Contact");
					$datavam = $items->addChild('title');
					$datavam->addCData("Contact");
					$datavam = $items->addChild('parent');
					$datavam->addCData("");
					$datavam = $items->addChild('private');
					$datavam->addCData("");
					$datavam = $items->addChild('tags');
					$datavam->addCData("");
					$datavam = $items->addChild('menu_lt');
					$datavam->addCData("Kontaktai");
					$datavam = $items->addChild('title_lt');
					$datavam->addCData("Kontaktai");
					$datavam = $items->addChild('menu_ru');
					$datavam->addCData("Контакты");
					$datavam = $items->addChild('title_ru');
					$datavam->addCData("Контакты");
				}
				$xml_menu->asXML(GSDATAOTHERPATH.'i18n_menu_cache.xml');
			}
		}
	}
	
	function uncheck_data_contact() {
		if (file_exists(GSDATAPAGESPATH.'contact.xml')) {
			unlink(GSDATAPAGESPATH.'contact.xml');
			$xml_file = @file_get_contents(GSDATAOTHERPATH.'pages.xml');
			$xml_pages = simplexml_load_string($xml_file, 'SimpleXMLExtended');
			$xml_pages_new = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
			if ($xml_pages) {
				foreach ($xml_pages as $item) {
					if ($item->template != "contact.php") {
						$from_dom = dom_import_simplexml($item);
						$to_dom = dom_import_simplexml($xml_pages_new);
						$to_dom->appendChild($to_dom->ownerDocument->importNode($from_dom, true));
					}
				}
				$xml_pages_new->asXML(GSDATAOTHERPATH.'pages.xml');
			}
			$xml_file = @file_get_contents(GSDATAOTHERPATH.'i18n_menu_cache.xml');
			$xml_menu = simplexml_load_string($xml_file, 'SimpleXMLExtended');
			$xml_menu_new = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><pages></pages>');
			if ($xml_menu) {
				foreach ($xml_menu as $item) {
					if ($item->url != "contact") {
						$from_dom = dom_import_simplexml($item);
						$to_dom = dom_import_simplexml($xml_menu_new);
						$to_dom->appendChild($to_dom->ownerDocument->importNode($from_dom, true));
					}
				}
				$xml_menu_new->asXML(GSDATAOTHERPATH.'i18n_menu_cache.xml');
			}
		}
		if (file_exists(GSDATAPAGESPATH.'contact_lt.xml')) unlink(GSDATAPAGESPATH.'contact_lt.xml');
		if (file_exists(GSDATAPAGESPATH.'contact_ru.xml')) unlink(GSDATAPAGESPATH.'contact_ru.xml');
	}
	
	function check_data_search() {
		/*******************************************
		***      Create XML for Search page      ***
		***             if not exist             ***
		********************************************/
		if (!file_exists(GSDATAPAGESPATH.'search.xml')) {
			global $pagesArray, $USR, $USR1;
			$pagesSorted = subval_sort($pagesArray, 'menuOrder');
			if (count($pagesSorted) != 0) {
				$menu_order = 0;
				foreach ($pagesSorted as $page) {
					if($menu_order < (int)$page['menuOrder']) $curr_order = (int)$page['menuOrder'];
				}
				$curr_order = $curr_order + 1;
			}
			else $curr_order = 0;
			$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
			$xml->addChild('pubDate', date(DATE_RFC2822));
			$dataval = $xml->addChild('title');
			$dataval->addCData("Search");
			$dataval = $xml->addChild('url');
			$dataval->addCData("search");
			$dataval = $xml->addChild('meta');
			$dataval->addCData("");
			$dataval = $xml->addChild('metad');
			$dataval->addCData("");
			$dataval = $xml->addChild('menu');
			$dataval->addCData("");
			$dataval = $xml->addChild('menuOrder');
			$dataval->addCData("0");
			$dataval = $xml->addChild('menuStatus');
			$dataval->addCData("");
			$dataval = $xml->addChild('template');
			$dataval->addCData("search.php");
			$dataval = $xml->addChild('parent');
			$dataval->addCData("");
			$dataval = $xml->addChild('content');
			$dataval->addCData("");
			$dataval = $xml->addChild('private');
			$dataval->addCData("");
			$dataval = $xml->addChild('author');
			$dataval->addCData(isset($USR1)?$USR1:$USR);
			$xml->asXML(GSDATAPAGESPATH.'search.xml');
			if (function_exists('i18n_init')) {
				if (!file_exists(GSDATAPAGESPATH.'search_lt.xml')) {
					$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
					$xml->addChild('pubDate', date(DATE_RFC2822));
					$dataval = $xml->addChild('title');
					$dataval->addCData("Paieška");
					$dataval = $xml->addChild('url');
					$dataval->addCData("search_lt");
					$dataval = $xml->addChild('meta');
					$dataval->addCData("");
					$dataval = $xml->addChild('metad');
					$dataval->addCData("");
					$dataval = $xml->addChild('menu');
					$dataval->addCData("");
					$dataval = $xml->addChild('menuOrder');
					$dataval->addCData("0");
					$dataval = $xml->addChild('menuStatus');
					$dataval->addCData("");
					$dataval = $xml->addChild('template');
					$dataval->addCData("search.php");
					$dataval = $xml->addChild('parent');
					$dataval->addCData("");
					$dataval = $xml->addChild('content');
					$dataval->addCData("");
					$dataval = $xml->addChild('private');
					$dataval->addCData("");
					$dataval = $xml->addChild('author');
					$dataval->addCData(isset($USR1)?$USR1:$USR);
					$xml->asXML(GSDATAPAGESPATH.'search_lt.xml');
				}
				if (!file_exists(GSDATAPAGESPATH.'search_ru.xml')) {
					$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
					$xml->addChild('pubDate', date(DATE_RFC2822));
					$dataval = $xml->addChild('title');
					$dataval->addCData("Поиск");
					$dataval = $xml->addChild('url');
					$dataval->addCData("search_ru");
					$dataval = $xml->addChild('meta');
					$dataval->addCData("");
					$dataval = $xml->addChild('metad');
					$dataval->addCData("");
					$dataval = $xml->addChild('menu');
					$dataval->addCData("");
					$dataval = $xml->addChild('menuOrder');
					$dataval->addCData("0");
					$dataval = $xml->addChild('menuStatus');
					$dataval->addCData("");
					$dataval = $xml->addChild('template');
					$dataval->addCData("search.php");
					$dataval = $xml->addChild('parent');
					$dataval->addCData("");
					$dataval = $xml->addChild('content');
					$dataval->addCData("");
					$dataval = $xml->addChild('private');
					$dataval->addCData("");
					$dataval = $xml->addChild('author');
					$dataval->addCData(isset($USR1)?$USR1:$USR);
					$xml->asXML(GSDATAPAGESPATH.'search_ru.xml');
				}
			}
			
			$xml_file = @file_get_contents(GSDATAOTHERPATH.'pages.xml');
			$xml_pages = simplexml_load_string($xml_file, 'SimpleXMLExtended');
			if ($xml_pages) {
				$items = $xml_pages->addChild('item');
				$items->addChild('pubDate', date(DATE_RFC2822));
				$datavap = $items->addChild('title');
				$datavap->addCData("Search");
				$datavap = $items->addChild('url');
				$datavap->addCData("search");
				$datavap = $items->addChild('meta');
				$datavap->addCData("");
				$datavap = $items->addChild('metad');
				$datavap->addCData("");
				$datavap = $items->addChild('menu');
				$datavap->addCData("");
				$datavap = $items->addChild('menuOrder');
				$datavap->addCData("0");
				$datavap = $items->addChild('menuStatus');
				$datavap->addCData("");
				$datavap = $items->addChild('template');
				$datavap->addCData("search.php");
				$datavap = $items->addChild('parent');
				$datavap->addCData("");
				$datavap = $items->addChild('private');
				$datavap->addCData("");
				$datavap = $items->addChild('author');
				$datavap->addCData(isset($USR1)?$USR1:$USR);
				$datavap = $items->addChild('slug');
				$datavap->addCData("search");
				$datavap = $items->addChild('filename');
				$datavap->addCData("search.xml");
				if (function_exists('i18n_init')) {
					/*** LT ***/
					$items = $xml_pages->addChild('item');
					$items->addChild('pubDate', date(DATE_RFC2822));
					$datavap = $items->addChild('title');
					$datavap->addCData("Paieška");
					$datavap = $items->addChild('url');
					$datavap->addCData("search_lt");
					$datavap = $items->addChild('meta');
					$datavap->addCData("");
					$datavap = $items->addChild('metad');
					$datavap->addCData("");
					$datavap = $items->addChild('menu');
					$datavap->addCData("");
					$datavap = $items->addChild('menuOrder');
					$datavap->addCData("0");
					$datavap = $items->addChild('menuStatus');
					$datavap->addCData("");
					$datavap = $items->addChild('template');
					$datavap->addCData("search.php");
					$datavap = $items->addChild('parent');
					$datavap->addCData("");
					$datavap = $items->addChild('private');
					$datavap->addCData("");
					$datavap = $items->addChild('author');
					$datavap->addCData(isset($USR1)?$USR1:$USR);
					$datavap = $items->addChild('slug');
					$datavap->addCData("search_lt");
					$datavap = $items->addChild('filename');
					$datavap->addCData("search_lt.xml");
					/*** RU ***/
					$items = $xml_pages->addChild('item');
					$items->addChild('pubDate', date(DATE_RFC2822));
					$datavap = $items->addChild('title');
					$datavap->addCData("Поиск");
					$datavap = $items->addChild('url');
					$datavap->addCData("search_ru");
					$datavap = $items->addChild('meta');
					$datavap->addCData("");
					$datavap = $items->addChild('metad');
					$datavap->addCData("");
					$datavap = $items->addChild('menu');
					$datavap->addCData("");
					$datavap = $items->addChild('menuOrder');
					$datavap->addCData("0");
					$datavap = $items->addChild('menuStatus');
					$datavap->addCData("");
					$datavap = $items->addChild('template');
					$datavap->addCData("search.php");
					$datavap = $items->addChild('parent');
					$datavap->addCData("");
					$datavap = $items->addChild('private');
					$datavap->addCData("");
					$datavap = $items->addChild('author');
					$datavap->addCData(isset($USR1)?$USR1:$USR);
					$datavap = $items->addChild('slug');
					$datavap->addCData("search_ru");
					$datavap = $items->addChild('filename');
					$datavap->addCData("search_ru.xml");
				}
				$xml_pages->asXML(GSDATAOTHERPATH.'pages.xml');
			}
		}
	}
	
	function uncheck_data_search() {
		if (file_exists(GSDATAPAGESPATH.'search.xml')) {
			unlink(GSDATAPAGESPATH.'search.xml');
			$xml_file = @file_get_contents(GSDATAOTHERPATH.'pages.xml');
			$xml_pages = simplexml_load_string($xml_file, 'SimpleXMLExtended');
			$xml_pages_new = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
			if ($xml_pages) {
				foreach ($xml_pages as $item) {
					if ($item->template != "search.php") {
						$from_dom = dom_import_simplexml($item);
						$to_dom = dom_import_simplexml($xml_pages_new);
						$to_dom->appendChild($to_dom->ownerDocument->importNode($from_dom, true));
					}
				}
				$xml_pages_new->asXML(GSDATAOTHERPATH.'pages.xml');
			}
			$xml_file = @file_get_contents(GSDATAOTHERPATH.'i18n_menu_cache.xml');
			$xml_menu = simplexml_load_string($xml_file, 'SimpleXMLExtended');
			$xml_menu_new = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><pages></pages>');
			if ($xml_menu) {
				foreach ($xml_menu as $item) {
					if ($item->url != "search") {
						$from_dom = dom_import_simplexml($item);
						$to_dom = dom_import_simplexml($xml_menu_new);
						$to_dom->appendChild($to_dom->ownerDocument->importNode($from_dom, true));
					}
				}
				$xml_menu_new->asXML(GSDATAOTHERPATH.'i18n_menu_cache.xml');
			}
		}
		if (file_exists(GSDATAPAGESPATH.'search_lt.xml')) unlink(GSDATAPAGESPATH.'search_lt.xml');
		if (file_exists(GSDATAPAGESPATH.'search_ru.xml')) unlink(GSDATAPAGESPATH.'search_ru.xml');
	}
	
	global $LANG, $SITEURL;
	$def_lang=$LANG;
	$file_path=GSDATAOTHERPATH.'components.xml';
	if(!isset($def_lang) || empty($def_lang)) $def_lang="en_US";
	include(str_replace('\\','/',dirname(__FILE__)).'/lang/'.$def_lang.'.php');
	if(component_exists('ra-accordion') && return_theme_setting('accordion')==0) remove_component($file_path,'ra-accordion');
	if(return_theme_setting('accordion')==1 && !component_exists('ra-accordion')) import_component($file_path, 'ra-accordion');
	if(component_exists('ra-animated-block') && return_theme_setting('animated_block')==0) remove_component($file_path,'ra-animated-block');
	if(return_theme_setting('animated_block')==1 && !component_exists('ra-animated-block')) import_component($file_path, 'ra-animated-block');
	if(component_exists('ra-tabs') && return_theme_setting('tabs')==0) remove_component($file_path,'ra-tabs');
	if(return_theme_setting('tabs')==1 && !component_exists('ra-tabs')) import_component($file_path, 'ra-tabs');
	if(component_exists('ra-gallery') && return_theme_setting('gallery')==0) remove_component($file_path,'ra-gallery');
	if(return_theme_setting('gallery')==1 && !component_exists('ra-gallery')) import_component($file_path, 'ra-gallery');
	if(component_exists('ra-carousel') && return_theme_setting('carousel')==0) remove_component($file_path,'ra-carousel');
	if(return_theme_setting('carousel')==1 && !component_exists('ra-carousel')) import_component($file_path, 'ra-carousel');if($error_file) {
		?> <div class="fancy-message error" style="border: 1px solid; padding: 20px 10px 10px 10px; border-radius: 4px; margin-bottom: 20px; background: #F2DEDE; color: #A94442;"><p><?php echo $set_lang['XML_ERROR'].$eror_path; ?></p></div> <?php
	}
	if($eror_component) {
		?> <div class="fancy-message error" style="border: 1px solid; padding: 20px 10px 10px 10px; border-radius: 4px; margin-bottom: 20px; background: #F2DEDE; color: #A94442;"><p><?php echo $set_lang['COMPONENT_ERROR'].$eror_path; ?></p></div> <?php
	}
	if(return_theme_setting('contact_form')==1) check_data_contact();
	else uncheck_data_contact();
	
	if(return_theme_setting('search_form')==1) check_data_search();
	else uncheck_data_search();
?>
<div class="phone">
	<p>
		<label for="tagline"><?php echo $set_lang['SITE_PHONE']; ?></label>
		<input type="text" class="text" name="phone" id="phone" value="<?php get_theme_setting('phone'); ?>">
	</p>
</div>
<div class="leftsec">
	<p>
		<label for="schema"><?php echo $set_lang['COLOR_SCHEME']; ?></label>
		<?php get_schema_select(); ?>
	</p>
</div>
<div class="rightsec">
	<p>
		<label for="tagline"><?php echo $set_lang['TAG_LINE']; ?></label>
		<input type="text" class="text" name="tagline" id="tagline" value="<?php get_theme_setting('tagline'); ?>">
	</p>
</div>
<div class="leftsec">
	<p>
		<input type="checkbox" name="contact_form" value=1 <?php echo return_theme_setting('contact_form')==1?"checked":"" ?>><span class="ra-settings"><?php echo $set_lang['CONTACT_SET']; ?></span>
	</p>
</div>
<div class="rightsec">
	<p>
		<input type="checkbox" name="search_form" value=1 <?php echo return_theme_setting('search_form')==1?"checked":"" ?>><span class="ra-settings"><?php echo $set_lang['SEARCH_SET']; ?></span>
	</p>
</div>

<fieldset class="container-collapsed widesec" id="container-0" style="padding: 5px;">
	<legend class="collapsedlegend" id="legend-0"><?php echo $set_lang['RA_SET_EXPAND']; ?></legend>
	<h3 style="margin:20px 10px"><?php echo $set_lang['ADD_SOCIAL']; ?></h3>
	<div class="component-settings">
		<div class="inner-divs" style="margin: 10px;">
			<p>
				<label for="facebook">Facebook:</label>
				<input type="text" class="text" name="facebook" id="facebook" style="width: 80%;float: right;" value="<?php get_theme_setting('facebook'); ?>">
			</p>
			<p>
				<label for="twitter">Twitter:</label>
				<input type="text" class="text" name="twitter" id="twitter" style="width: 80%;float: right;" value="<?php get_theme_setting('twitter'); ?>">
			</p>
			<p>
				<label for="linkedin">Linkedin:</label>
				<input type="text" class="text" name="linkedin" id="linkedin" style="width: 80%;float: right;" value="<?php get_theme_setting('linkedin'); ?>">
			</p>
			<p>
				<label for="dribbble">Dribbble:</label>
				<input type="text" class="text" name="dribbble" id="dribbble" style="width: 80%;float: right;" value="<?php get_theme_setting('dribbble'); ?>">
			</p>
			<p>
				<label for="skype">Skype:</label>
				<input type="text" class="text" name="skype" id="skype" style="width: 80%;float: right;" value="<?php get_theme_setting('skype'); ?>">
			</p>
			<p>
				<label for="google">Google+:</label>
				<input type="text" class="text" name="google" id="google" style="width: 80%;float: right;" value="<?php get_theme_setting('google'); ?>">
			</p>
			<p>
				<label for="kontakte">VKontakte:</label>
				<input type="text" class="text" name="kontakte" id="kontakte" style="width: 80%;float: right;" value="<?php get_theme_setting('kontakte'); ?>">
			</p>
			<p>
				<label for="instagram">Instagram:</label>
				<input type="text" class="text" name="instagram" id="instagram" style="width: 80%;float: right;" value="<?php get_theme_setting('instagram'); ?>">
			</p>
			<p style="font-style:italic;margin:0;"><?php echo $set_lang['SOCIAL_DESC']; ?></p>
		</div>
	</div>
</fieldset>

<fieldset class="container-collapsed widesec" id="container-1">
	<legend class="collapsedlegend" id="legend-1"><?php echo $set_lang['RA_SET_EXPAND']; ?></legend>
	<h3 style="margin:20px 10px"><?php echo $set_lang['ADD_COMPONENTS']; ?></h3>
	<div class="component-settings">
		<div class="inner-divs">
			<input type="checkbox" name="animated_block" value=1 <?php echo return_theme_setting('animated_block')==1?"checked":"" ?>><span class="ra-settings">Animated block</span>
			<input type="checkbox" name="accordion" value=1 <?php echo return_theme_setting('accordion')==1?"checked":"" ?>><span class="ra-settings">Accordion</span>
			<input type="checkbox" name="tabs" value=1 <?php echo return_theme_setting('tabs')==1?"checked":"" ?>><span class="ra-settings">Tabs</span>
			<input type="checkbox" name="gallery" value=1 <?php echo return_theme_setting('gallery')==1?"checked":"" ?>><span class="ra-settings">Gallery</span>
			<input type="checkbox" name="carousel" value=1 <?php echo return_theme_setting('carousel')==1?"checked":"" ?>><span class="ra-settings">Carousel</span>
		</div>
		
	</div>
</fieldset>
<fieldset class="container-collapsed widesec" id="container-2">
	<legend class="collapsedlegend" id="legend-2"><?php echo $set_lang['RA_SET_EXPAND']; ?></legend>
	<h3 style="margin:20px 10px">Bootstrap<?php echo $set_lang['RA_EXT_FEATURES']; ?></h3>
	<div class="expand-settings">
		<div class="inner-divs">
			<input type="checkbox" name="tooltip" value=1 <?php echo return_theme_setting('tooltip')==1?"checked":"" ?>><span class="ra-settings">Tooltip</span>
			<input type="checkbox" name="popover" value=1 <?php echo return_theme_setting('popover')==1?"checked":"" ?>><span class="ra-settings">Popover</span>
			<input type="checkbox" name="gototop" value=1 <?php echo return_theme_setting('gototop')==1?"checked":"" ?>><span class="ra-settings">Scroll to Top</span>
		</div>
	</div>
</fieldset>
<?php
	if(return_theme_setting('carousel')) {
	?>
	<fieldset class="container-collapsed widesec" id="container-3">
		<legend class="collapsedlegend" id="legend-3"><?php echo $set_lang['RA_SET_EXPAND']; ?></legend>
		<h3 style="margin:20px 10px">Bootstrap<?php echo $set_lang['RA_CAROUSEL']; ?></h3>
		<div class="expand-settings">
			<div class="inner-divs">
				<p><label for="interval"><?php echo $set_lang['RA_CAROUSEL_INTERVAL']; ?></label>
				<input type="text" class="text" name="interval" id="interval" value="<?php get_theme_setting('interval'); ?>" style="width: 100px;"></p>
				<p><label for="min_height"><?php echo $set_lang['RA_CAROUSEL_MIN_HEIGHT']; ?></label>
				<input type="text" class="text" name="min_height" id="min_height" value="<?php get_theme_setting('min_height'); ?>" style="width: 100px;" title="<?php echo $set_lang['RA_CAROUSEL_MIN_HEIGHT_DESC']; ?>"></p>
				<p><label for="max_height"><?php echo $set_lang['RA_CAROUSEL_MAX_HEIGHT']; ?></label>
				<input type="text" class="text" name="max_height" id="max_height" value="<?php get_theme_setting('max_height'); ?>" style="width: 100px;" title="<?php echo $set_lang['RA_CAROUSEL_MAX_HEIGHT_DESC']; ?>"></p>
				<p><label for="indicator_color"><?php echo $set_lang['RA_CAROUSEL_ICOLOR']; ?></label>
				<input type="text" class="text" name="indicator_color" id="indicator_color" value="<?php get_theme_setting('indicator_color'); ?>" style="width: 100px;"></p>
				<p><label for="control_color"><?php echo $set_lang['RA_CAROUSEL_CCOLOR']; ?></label>
				<input type="text" class="text" name="control_color" id="control_color" value="<?php get_theme_setting('control_color'); ?>" style="width: 100px;"></p>
				<input type="checkbox" name="use_fade" value=1 <?php echo return_theme_setting('use_fade')==1?"checked":"" ?> style="margin-left: 26px;" ><span class="ra-settings"><?php echo $set_lang['RA_CAROUSEL_FADE']; ?></span>
				<hr style="margin-top: 20px;">
				<div class="wrap" style="margin:10px auto;max-width:38rem;">
					<span id="animationSandbox" style="display: block;"><h1 style="color:#F35626;font-size:2rem;">Animate.css</h1></span>
				</div>
				<hr style="margin-bottom: 20px;">
				<select name="carousel_effect" class="input input--dropdown js--animations">
					<optgroup label="Attention Seekers">
						<option value="bounce">bounce</option>
						<option value="flash">flash</option>
						<option value="pulse">pulse</option>
						<option value="rubberBand">rubberBand</option>
						<option value="shake">shake</option>
						<option value="swing">swing</option>
						<option value="tada">tada</option>
						<option value="wobble">wobble</option>
					</optgroup>
					
					<optgroup label="Bouncing Entrances">
						<option value="bounceIn">bounceIn</option>
						<option value="bounceInDown">bounceInDown</option>
						<option value="bounceInLeft">bounceInLeft</option>
						<option value="bounceInRight">bounceInRight</option>
						<option value="bounceInUp">bounceInUp</option>
					</optgroup>
					
					<optgroup label="Fading Entrances">
						<option value="fadeIn">fadeIn</option>
						<option value="fadeInDown">fadeInDown</option>
						<option value="fadeInDownBig">fadeInDownBig</option>
						<option value="fadeInLeft">fadeInLeft</option>
						<option value="fadeInLeftBig">fadeInLeftBig</option>
						<option value="fadeInRight">fadeInRight</option>
						<option value="fadeInRightBig">fadeInRightBig</option>
						<option value="fadeInUp">fadeInUp</option>
						<option value="fadeInUpBig">fadeInUpBig</option>
					</optgroup>
					
					<optgroup label="Flippers">
						<option value="flip">flip</option>
						<option value="flipInX">flipInX</option>
						<option value="flipInY">flipInY</option>
					</optgroup>
					
					<optgroup label="Lightspeed">
						<option value="lightSpeedIn">lightSpeedIn</option>
					</optgroup>
					
					<optgroup label="Rotating Entrances">
						<option value="rotateIn">rotateIn</option>
						<option value="rotateInDownLeft">rotateInDownLeft</option>
						<option value="rotateInDownRight">rotateInDownRight</option>
						<option value="rotateInUpLeft">rotateInUpLeft</option>
						<option value="rotateInUpRight">rotateInUpRight</option>
					</optgroup>
					
					<optgroup label="Sliding Entrances">
						<option value="slideInUp">slideInUp</option>
						<option value="slideInDown">slideInDown</option>
						<option value="slideInLeft">slideInLeft</option>
						<option value="slideInRight">slideInRight</option>
						
					</optgroup>
					
					<optgroup label="Zoom Entrances">
						<option value="zoomIn">zoomIn</option>
						<option value="zoomInDown">zoomInDown</option>
						<option value="zoomInLeft">zoomInLeft</option>
						<option value="zoomInRight">zoomInRight</option>
						<option value="zoomInUp">zoomInUp</option>
					</optgroup>
					
					<optgroup label="Specials">
						<option value="hinge">hinge</option>
						<option value="rollIn">rollIn</option>
						<option value="rollOut">rollOut</option>
					</optgroup>
				</select>
				<span class="effect-text"><?php echo $set_lang['RA_CAROUSEL_COPY_CODE']; ?></span>
				<span id="effect-code"><code>data-effect="</code><code id="effect-code-name">bounce"</code></span>
				<p  style="margin-top: 20px;"><span><?php echo $set_lang['RA_CAROUSEL_CODE_DELAY']; ?></span>
				<span id="effect-code"><code>data-delay="1000"</code></span></p>
				<p  style="margin-top: 20px;"><span><?php echo $set_lang['RA_CAROUSEL_CODE_POS']; ?></span>
				<span id="effect-code"><code>style="top:20%;left:20%"</code></span></p>
			</div>
		</div>
	</fieldset>
	<?php
	}
?>
<fieldset class="container-collapsed widesec" id="container-4">
	<legend class="collapsedlegend" id="legend-4"><?php echo $set_lang['RA_SET_EXPAND']; ?></legend>
	<h3 style="margin:20px 10px"><?php echo $set_lang['RA_SCRIPTS']; ?></h3>
	<div class="expand-settings">
		<div class="inner-divs">
			<p style="font-style:italic;"><?php echo $set_lang['RA_SCRIPT_DESC']; ?></p>
			<input type="checkbox" name="jquery" value=1 <?php echo return_theme_setting('jquery')==1?"checked":"" ?>><span class="ra-settings" style="color:red;">Jquery<?php echo $set_lang['RA_REQUIRED']; ?></span>
			<input type="checkbox" name="bootstrap" value=1 <?php echo return_theme_setting('bootstrap')==1?"checked":"" ?>><span class="ra-settings" style="color:red;">Bootstrap<?php echo $set_lang['RA_REQUIRED']; ?></span>
			<input type="checkbox" name="wow" value=1 <?php echo return_theme_setting('wow')==1?'checked':'' ?>  title="<?php echo $set_lang['RA_REQUIRED_DESC']?>  Animated blocks<?php echo $set_lang['RA_REQUIRED_MENU']?>" ><span class="ra-settings"  <?php echo return_theme_setting('animated_block')==1?'style="color:red;"':'' ?> >WOW<?php echo return_theme_setting('animated_block')==1?$set_lang['RA_REQUIRED']:"" ?></span>
			<input type="checkbox" name="prettyphoto" value=1 <?php echo return_theme_setting('prettyphoto')==1?'checked':'' ?>  title="<?php echo $set_lang['RA_REQUIRED_DESC']?>  Gallery" ><span class="ra-settings"  <?php echo return_theme_setting('gallery')==1?'style="color:red;"':'' ?> >prettyPhoto<?php echo return_theme_setting('gallery')==1?$set_lang['RA_REQUIRED']:"" ?></span>
		</div>
	</div>
</fieldset>


<script type="text/javascript">
	function testAnim(x) {
		jQuery('#animationSandbox').removeClass().addClass(x + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
			jQuery(this).removeClass();
		});
	};
	
	jQuery('.js--animations').change(function(){
		var anim = jQuery(this).val();
		testAnim(anim);
		var anim_effect=anim;
		$("#effect-code #effect-code-name").text(anim_effect+'"');
	})	
	jQuery('.collapsedlegend').click(function(){
		var path = '<?php echo $SITEURL; ?>';
		var expand = '<?php echo $set_lang['RA_SET_EXPAND']; ?>';
		var colapse = '<?php echo $set_lang['RA_SET_COLAPS']; ?>';
		if($(this).text()==expand){
			$(this).parents('fieldset').find('div:first').show(500);
			$(this).text(colapse);
			$(this).css('background-image', 'url('+path+'admin/template/images/tick.png)');
			}else{
			$(this).parents('fieldset').find('div:first').hide(500);
			$(this).text(expand);
			$(this).css('background-image', 'url('+path+'admin/template/images/utick.png)');
		}
	});
	
	jQuery(function(){
		setTimeout(function() {
			jQuery(".fancy-message").hide('slow');
		}, 10000);
		
		
	});
	jQuery('.iframe-btn').fancybox({	
		'width'		: 900,
		'height'	: 600,
		'type'		: 'iframe',
		'autoScale'    	: false
	});
	
	//document.getElementById("lang").value = window.navigator.userLanguage || window.navigator.language;
	
	jQuery(document.body).on('change',"#schema",function (e) {
		var tagline;
		var optVal= jQuery("#schema option:selected").val();
		switch(optVal) {
			case 'red':
			tagline = 'Purple Agency';
			break;
			case 'blue':
			tagline = 'Blue Agency';
			break;
			case 'green':
			tagline = 'Green Agency';
			break;
			default:
			tagline = 'Red Agency';
		} 
		document.getElementById("tagline").value = tagline;
	});
</script>
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
	/****************************************************
		*
		* @File:      functions.php
		* @Package:   GetSimple
		* @Action:    Bootstrap3 for GetSimple CMS
		*
	*****************************************************/
	
	
	function getMenuHTMLImpl_bootstrap(&$menu, $showTitles=false, $component=null, $level=1) {
		$html = '';
		foreach ($menu as &$item) {
			if (!isset($component)) {
				$href = @$item['link'] ? $item['link'] : (function_exists('find_i18n_url') ? find_i18n_url($item['url'],$item['parent']) : find_url($item['url'],$item['parent']));
			}
			$urlclass = (preg_match('/^[a-z]/i',$item['url']) ? '' : 'x') . $item['url'];
			$parentclass = !$item['parent'] ? '' : (preg_match('/^[a-z]/i',$item['parent']) ? ' ' : ' x') . $item['parent'];
			$classes = $urlclass . $parentclass . 
			($item['current'] ? ' current' : ($item['currentpath'] ? ' currentpath' : '')); //. 
			//(isset($item['children']) && count($item['children']) > 0 ? ' open' : ($item['haschildren'] ? ' closed' : ''));
			$text = $item['menu'] ? $item['menu'] : $item['title'];
			$title = $item['title'] ? $item['title'] : $item['menu'];
			if (isset($component)) {
				$navitem = new I18nNavigationItem($item, $classes, $text, $title, $showTitles, $component);
				$html .= I18nNavigationFrontend::getMenuItem($component, $navitem);
				} else {
				if (isset($item['children']) && count($item['children']) > 0) {
					//$caret = ($level == 1) ? '<b class="caret"></b>' : '';
					$caret = ($level > 0) ? '<b class="caret"></b>' : '';
					$a_class = 'class="dropdown-toggle"';
					$data_toggles = 'data-toggle="dropdown"';
					$classes .= ($level == 1) ? ' dropdown' : ' dropdown-submenu';
					if($level > 1) $classes .= ' dropdown-menu-right';
					//$classes .= ' dropdownsmart';
				}
				else {
					$caret = '';
					$a_class = '';
					$data_toggles = '';
				}
				if ($showTitles) {
					$html .= '<li class="' . $classes . '"><a href="' . $href . '" class="'.$a_class.' >' . $title . '</a>';
					//$html .= '<li><a href="' . $href . '" class="'.$a_class.' >' . $title . '</a>';
					} else {
					$html .= '<li class="' . $classes . '"><a href="' . $href . '" '.$a_class.' '.$data_toggles.' title="' . htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8')) . '">' . $text . $caret .'</a>';
					//$html .= '<li><a href="' . $href . '" '.$a_class.' '.$data_toggles.' title="' . htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8')) . '">' . $text . $caret .'</a>';
				}
				if (isset($item['children']) && count($item['children']) > 0) {
					$next_classes = 'dropdown-menu';
					if($level > 1) $next_classes .= ' next';
					$html .= '<ul class="' . $next_classes . '">' . getMenuHTMLImpl_bootstrap($item['children'], $showTitles, $component, $level+1) . '</ul>';
				}
				$html .= '</li>' . "\n";
			}
		}
		return $html;
	} 
	
	function getMenuHTML_bootstrap(&$menu, $showTitles=false, $componentname=null) {
		$component = null;
		if ($componentname && file_exists(GSDATAOTHERPATH.'components.xml')) {
			$data = getXML(GSDATAOTHERPATH.'components.xml');
			if (count($data->item) != 0) foreach ($data->item as $item) {
				if ($componentname == $item->slug) { 
					$component = stripslashes(htmlspecialchars_decode($item->value, ENT_QUOTES));
					break;
				}
			}
		}
		return getMenuHTMLImpl_bootstrap($menu, $showTitles, $component);
	} 
	
	function i18n_navigation_bootstrap($slug, $minlevel=0, $maxlevel=0, $show=I18N_SHOW_NORMAL, $component=null) {
		$slug = '' . $slug;
		require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
		$menu = I18nNavigationFrontend::getMenu($slug, $minlevel, $maxlevel, $show);
		if (isset($menu) && count($menu) > 0) {
			$html = getMenuHTML_bootstrap($menu, ($show & I18N_OUTPUT_TITLE), $component);
			echo exec_filter('menuitems',$html);
		}
	}
	
	function AddPageToNavigation_bootstrap($page, $currentpage, $pagesSorted, $level) {
		// Make sure there's both a menu and title attribute
		if ($page['menu'] == '') { $page['menu'] = $page['title']; }
		if ($page['title'] == '') { $page['title'] = $page['menu']; }
		
		// Check if the page has children
		$Children = getChildren($page['url']);
		if (count($Children) == 0) {
			// Just a regular link, no children
			$link = '<a href="' . find_url($page['url'], $page['parent']) . '" title="' . encode_quotes(cl($page['title'])) . '">' . strip_decode($page['menu']) . '</a>';
			$submenu = '';
			} else {
			// We have children, create a submenu
			//$caret = ($level == 1) ? '<b class="caret"></b>' : '';
			$caret = ($level > 0) ? '<b class="caret"></b>' : '';
			$link = '<a href="' . find_url($page['url'], $page['parent']) . '" title="' . encode_quotes(cl($page['title'])) . '" class="dropdown-toggle" data-toggle="dropdown">' . strip_decode($page['menu']) . $caret . '</a>';
			
			$next_classes = 'dropdown-menu';
			if($level > 1) $next_classes .= ' next';
			$submenu = '<ul class="' . $next_classes . '">';
			
			foreach ($pagesSorted as $Child) {
				if ((in_array($Child['url'], $Children)) && ($Child['menuStatus'] == 'Y') && (($Child['private'] != 'Y') || ((isset($USR) && $USR == get_cookie('GS_ADMIN_USERNAME'))))) {
					$submenu .= AddPageToNavigation_bootstrap($Child, $currentpage, $pagesSorted, $level + 1);
				}
			}
			
			$submenu .= '</ul>';
		}
		
		// Check if we're handling the page the user is on (or if the submenu contained the page the user is on)
		if (($currentpage == $page['url']) || (stripos($submenu, "current active") !== false)) {
			$li_classes = "current active " . $page['parent'] . " " . $page['url'];
			} else {
			$li_classes = trim($page['parent'] . " " . $page['url']);
		}
		if (count($Children) > 0) { 
			$li_classes .= ($level == 1) ? ' dropdown' : ' dropdown-submenu';
			if($level > 1) $li_classes .= ' dropdown-menu-right'; 
		}
		return '<li class="' . $li_classes . '">' . $link . $submenu . '</li>' . "\n";
	}
	
	function get_navigation_bootstrap($currentpage) {
		global $pagesArray, $USR;
		
		$menu = '';
		
		$pagesSorted = subval_sort($pagesArray, 'menuOrder');
		if (count($pagesSorted) != 0) {
			foreach ($pagesSorted as $page) {
				if ((!$page['parent']) && ($page['menuStatus'] == 'Y') && (($page['private'] != 'Y') || ((isset($USR) && $USR == get_cookie('GS_ADMIN_USERNAME'))))) {
					// Append to the menu string
					$menu .= AddPageToNavigation_bootstrap($page, $currentpage, $pagesSorted, 1);
				}
			}
		}
		
		echo exec_filter('menuitems',$menu);
	}
	
	function get_i18n_navigation_bootstrap($currentpage, $menu_structure) {
		$menu = '';
		
		if (count($menu_structure) != 0) {
			foreach ($menu_structure as $page) {
				if (( !$page['parent']) ) {
					// Append to the menu string
					$menu .= AddPageToNavigation_bootstrap($page, $currentpage, $menu_structure, 1);
				}
			}
		}
		
		echo exec_filter('menuitems',$menu);
	}
	
	function plugins_check($search_for) {
		if(!empty($search_for) && file_exists(GSDATAOTHERPATH.'plugins.xml')) {
			$data = getXML(GSDATAOTHERPATH.'plugins.xml');
			$aplugins = $data->item;
			if (count($aplugins) > 0) {
				foreach ($aplugins as $aplugin) {
					if ($search_for == rtrim($aplugin->plugin, ".php") && $aplugin->enabled == 'true') {
						return true;
						break;
					}
				}
			}
		}
		return false;
	}
	
	function getDefaultLanguage() {
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
		return parseDefaultLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		else
		return parseDefaultLanguage(NULL);
	}
	
	function parseDefaultLanguage($http_accept, $deflang = "en") {
		if(isset($http_accept) && strlen($http_accept) > 1)  {
			# Split possible languages into array
			$x = explode(",",$http_accept);
			foreach ($x as $val) {
				#check for q-value and create associative array. No q-value means 1 by rule
				if(preg_match("/(.*);q=([0-1]{0,1}.\d{0,4})/i",$val,$matches))
				$lang[$matches[1]] = (float)$matches[2];
				else
				$lang[$val] = 1.0;
			}
			
			#return default language (highest q-value)
			$qval = 0.0;
			foreach ($lang as $key => $value) {
				if ($value > $qval) {
					$qval = (float)$value;
					$deflang = $key;
				}
			}
		}
		return strtolower($deflang);
	}
	
	function get_component_with_params($name, $params = array()) {
		global $args;
		if(isset($args)) $saved_args = $args;
		$args = $params;
		if (!function_exists('get_i18n_component')) get_component($name);
		else get_i18n_component($name);
		if(isset($saved_args)) $args = $saved_args; else unset($args);
	}
	
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
	
	function simple_c_default_email() {
		$files = scandir(GSUSERSPATH);
		foreach ($files as $filename) {
			if (preg_match("#\.xml$#", $filename)) {
				$data = getXML(GSUSERSPATH . $filename);
				return $data->EMAIL;
			}
		}
		return "";
	}
	
	function check_data_theme() {
		/***********************************************
		***  Create Theme XML default settings file  ***
		***               if not exist               ***
		***********************************************/
		if(!file_exists(GSDATAOTHERPATH.'theme_settings_Red Agency.xml')) {
			$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><settings></settings>');
			$xml->addChild('textColor', '#4e4e4e');
			$xml->addChild('H1Color', '#000000');
			$xml->addChild('H3Color', '#787878');
			$xml->addChild('linkColor', '#c52d2f');
			$xml->addChild('linkHover', '#D43133');
			$xml->addChild('headerColor', '#191919');
			$xml->addChild('headerTopColor', '#191919');
			$xml->addChild('headerBottColor', '#85AD85');
			$xml->addChild('sitetagColor', '#E8EDF0');
			$xml->addChild('backgroundColor', '#FFFFFF');
			$xml->addChild('sidebarBgColor', '#FFFFFF');
			$xml->addChild('sidebarBorderColor', '#FFFFFF');
			$xml->addChild('footerBgColor', '#FFFFFF');
			$xml->addChild('DropDownBg', '#C52D2F');
			$xml->addChild('FooterTopBorder', '#C52D2F');
			$xml->addChild('FooterLinkColor', '#7F140D');
			$xml->addChild('FooterLinkHover', '#D43133');
			$xml->addChild('FooterTxtColor', '#4E4E4E');
			$xml->addChild('jquery', 1);
			$xml->addChild('bootstrap', 1);
			$xml->addChild('carousel', 1);
			$xml->asXML(GSDATAOTHERPATH.'theme_settings_Red Agency.xml'); 
		}
	}
	
	
	/***  Function to get variables from Lang array  ***/
	function get_lang_param($name="") {
		if(empty($name)) return false;
		else {
			global $language;
			if(isset($language) && !empty($language)) $def_lang=$language;
			else $def_lang = "en_US";
			if($def_lang == "en") $def_lang = "en_US";
			if(strlen($def_lang)<3) $def_lang = $def_lang.'_'.strtoupper($def_lang);
			require(str_replace('\\','/',dirname(__FILE__)).'/lang/'.$def_lang.'.php');
			if(isset($set_lang[$name]) && !empty($set_lang[$name])) $lang_param = $set_lang[$name];
			else $lang_param = "Search";
		}
		return $lang_param;
	}
	
	/***  Search function  ***/
	define('EXCERPTLENGTH', 30); 
	function get_search_results() {
		$keywords = @explode(' ', $_POST['keywords']);
		$pages = get_pages();
		
		// Find matching documents
		foreach ($keywords as $keyword) {
			$match = array();
			foreach ($pages as $page) {
				
				$data = getXML($page);
				if ($data->private != 'Y') {
					if (stripos($data->content, $keyword) !== false) {
						if (function_exists('return_i18n_default_language')) {
							//if (stripos($page, '_') !== false && in_array(substr($page,0,stripos($page, '_')).'.xml', $pages)) {
							//	continue;
							//}
							$match[] = $page;
						}
					}
				}
				$pages = $match;
			}
		}
		
		// Print results
		if (count($pages) > 0) {
			echo '<p>'.get_lang_param('RA_SEARCH_FOUND').' ('.count($pages).'):</p>';
			print_search_results($pages);
			} else {
			echo '<p style="color:red;">'.get_lang_param('RA_SEARCH_NOT').'</p>';
		}
	}
	function print_search_results($pages) {
		global $PRETTYURLS;
		global $SITEURL;
		
		echo '<ul id="search_results">';
		foreach ($pages as $page) {
			$data = getXML($page);
			$url = ($PRETTYURLS == 1) ? $SITEURL . $data->url : $SITEURL . 'index.php?id=' . $data->url;
			// Extract and filter content
			$content = preg_replace('/&#?[a-z0-9]{2,8};/i', '', stripslashes(strip_tags(html_entity_decode($data->content, ENT_QUOTES, 'UTF-8'))));
			$content = preg_replace('/\s{2,}/', ' ', trim($content));
			// Create an excerpt of the content
			$tokens = explode(' ', $content, EXCERPTLENGTH);
			array_pop($tokens);
			$content = implode(' ', $tokens);
			// Print result
			if(!strpos($data->url, '_')) $langs = "en";
			else $langs = substr($data->url, strpos($data->url, '_')+1);
			echo '<li><span class="search-entry-language" style="font-size:16px;font-weight:400;line-height:24px;margin-right:4px;color: #000;">'.$langs.'</span><a href="' . $url . '" /><b>' . $data->title . '</b></a><br />' . $content . ' [...]</li>';
		}
		echo '</ul>';
	}
	function get_pages() {
		$path = GSDATAPAGESPATH;
		$files = getFiles($path);
		$pages = array();
		foreach ($files as $file) {
			if (isFile($file, $path, 'xml')) {
				$pages[] = $path . $file;
			}
		}
		return $pages;
	}
	/***  Search functions end  ***/
?>
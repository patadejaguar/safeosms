<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } 
/****************************************************
*
* @File: 		header.inc.php
* @Package:		GetSimple
* @Action:		Theme for GetSimple CMS
* @Author:      Oleg Svetlov http://getsimplecms.ru/
*
*****************************************************/
?>
<!DOCTYPE html>
<!--[if IE 7 ]> <html lang="<?php global $language; echo @$language ? $language : 'en'; ?>" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="<?php global $language; echo @$language ? $language : 'en'; ?>" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="<?php global $language; echo @$language ? $language : 'en'; ?>" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
	
    <title><?php get_page_clean_title(); ?> - <?php get_site_name(); ?></title>
	
    <!-- styles -->
	<link href="<?php get_theme_url(); ?>/css/style.css" rel="stylesheet" type="text/css">

	<!-- modernizr -->
    <script src="<?php get_theme_url(); ?>/js/modernizr.js" type="text/javascript"></script>
	
	<!-- scripts -->
	<script type="text/javascript" src="<?php get_theme_url(); ?>/js/jquery-1.8.0.min.js"></script>
	
	<?php get_header(); ?>
	
	<!-- responsive -->
	<link href="<?php get_theme_url(); ?>/css/responsive.css" rel="stylesheet" type="text/css">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		
	<!--[if lt IE 9]>
	    <link href="<?php get_theme_url(); ?>/css/ie.css" rel="stylesheet" type="text/css">
    <![endif]-->
	
	<!-- favicons -->
	<link rel="shortcut icon" href="<?php get_site_url(); ?>favicon.ico">
    <link rel="apple-touch-icon" href="<?php get_theme_url(); ?>/images/icons/touch-icon-iphone.png"> <!-- 60x60 -->
    <link rel="apple-touch-icon" sizes="76x76" href="<?php get_theme_url(); ?>/images/icons/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php get_theme_url(); ?>/images/icons/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php get_theme_url(); ?>/images/icons/touch-icon-ipad-retina.png">	
	
</head>

<body id="<?php get_page_slug(); ?>">

<!-- wrapper -->
<div id="wrapper">
	<!-- chrome fon -->
	<div class="chrome">
		<!-- header -->
		<header id="header">
			<div class="container">
				<div class="col-7">
					<div id="logo">
						<a href="<?php get_site_url(); ?>"><?php get_site_name(); ?></a> 
						<span><?php get_component('tagline'); ?></span>
					</div>
				</div>
				<div class="col-5">
					<div class="top-contact">
						<div class="phone">+X (XXX) XXX-XX-XX</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</header>
		<!-- /header -->
		
		<!-- navigation -->
		<nav>
			<div class="container">
				<div class="col-12">
					<div id="navigation">
						<ul id="nav">
						<?php get_navigation(get_page_slug(FALSE)); ?>
						</ul>
					</div> 
					<div class="clearfix"></div>		
				</div>
				<div class="clearfix"></div>
			</div>
		</nav>
		<!-- /navigation -->
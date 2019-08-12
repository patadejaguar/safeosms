<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>

<!DOCTYPE html>
<html class="no-js" lang="en">
 
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <link href="<?php get_site_url(); ?>" />
    <link href="//www.google-analytics.com" />
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
    <title><?php get_site_name(); ?> - <?php return_page_title(); ?></title>
    <?php get_header(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php get_page_meta_desc(); ?>">
    <meta name="keywords" content="<?php get_page_meta_keywords(); ?>">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php get_theme_url(); ?>/css/foundation.min.css">
    <link rel="stylesheet" href="<?php get_theme_url(); ?>/css/foundation.css" />
    <link rel="stylesheet" href="<?php get_theme_url(); ?>/css/app.css" />
    <link rel="stylesheet" href="<?php get_theme_url(); ?>/css/mycss.css" />
    <link rel="stylesheet" href="<?php get_theme_url(); ?>/css/myFirstFonts.css" />
    <link href='https://fonts.googleapis.com/css?family=Montserrat|Comfortaa|Handlee' rel='stylesheet' type='text/css'>
    
    <!-- Open Graph Protocol -->
    <meta property="og:site_name" content="<?php get_site_name(); ?>">
    <meta property="og:url" content="<?php get_site_url(); ?>">
    <meta property="og:title" content="<?php get_site_name(); ?> | <?php get_page_slug(); ?>">

    <!-- Google+ Snippets -->
    <meta itemprop="url" content="<?php get_page_url(); ?>">
    <meta itemprop="name" content="<?php get_page_slug(); ?> | <?php get_site_name(); ?>">
    
    


</head>

<body id="<?php get_page_slug(); ?>">
 


<div class="off-canvas-wrapper">
    <div class="off-canvas-wrapper-inner" data-off-canvas-wrapper>
    
     <!-- This is the actual content for the off canvas menu -->
      <div class="off-canvas position-left" id="offCanvas" data-off-canvas>
          <ul id="topMenuOffCanvas">
            <li><img src="<?php get_theme_url(); ?>/images/miniIcon.png" alt="your icon"><h2>Giggles</h2></li>
            <?php get_navigation(return_page_slug()); ?>
          </ul>
      </div>
      
      
      <!-- page content -->
      <div class="off-canvas-content" data-off-canvas-content>
      
      <!-- This is the black title bar that appears when the device is small -->
      <div class="title-bar show-for-small-only">
        <div class="title-bar-left">
          <button class="menu-icon" type="button" data-toggle="offCanvas" data-open="offCanvas"><i class="fa fa-bars" aria-hidden="true"></i></button>
          <span class="title-bar-title">Foundation</span>
        </div>
      </div>
      
      
      <!-- This is the normal page content -->
         
         <nav>
           <div class="top-bar show-for-medium" >
             <div class="menu-centered show-for-medium" id="topNav">
                <ul class="menu" id="topMenu">
                  <li><img src="<?php get_theme_url(); ?>/images/miniIcon.png" alt="your icon"></li>
                  <?php get_navigation(return_page_slug()); ?>
                </ul>
              </div>
           </div>
         </nav>
          
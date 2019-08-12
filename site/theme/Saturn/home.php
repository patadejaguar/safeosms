<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:		home.php
* @Package:		GetSimple
* @Action:		Saturn theme for GetSimple CMS
* @Author:		Arthur Glovchynski | glowczynski.pl
*
*****************************************************/
?>
<?php include('header.inc.php'); ?>
<body class="<?php get_page_slug(); ?>">
	<!-- Navigation -->
	<div class="main-nav">
		<div class="container">
			<header class="group top-nav">
				<nav class="navbar logo-w navbar-left" >
					<a class="logo" href="<?php get_site_url(); ?>"><?php get_site_name(); ?></a>
				</nav>
				<div class="navigation-toggle" data-tools="navigation-toggle" data-target="#navbar-1">
				    <span class="logo"><?php get_site_name(); ?></span>
				</div>
			    <nav id="navbar-1" class="navbar item-nav navbar-right">
				    <ul>
				       <?php get_navigation(return_page_slug()); ?>
				    </ul>
				</nav>
			</header>
		</div>
	</div>

	<!-- Introduction -->
	<div class="intro">
		<div class="container">
			<div class="units-row">
			    <div class="unit-10">
			    	<img class="img-intro" src="<?php get_theme_url(); ?>/img/avatar.png" alt="">
			    </div>
			    <div class="unit-90">
			    	<p class="p-intro"><?php get_component( 'tagline' ); ?></p>
			    </div>
			</div>
		</div>
	</div>
	
	<!-- Content -->
	<div class="content">
		<div class="container">
			<!-- Post -->
			<div class="post">
				<!-- Heading -->
				<h1><?php get_page_title(); ?></h1>
				<hr>
				<div class="in-content">
					<?php get_page_content(); ?>
				</div>
				<div class="foot-post">
					<div class="units-row">
					    <div class="unit-100">
					    	<strong>Last update:</strong> <span style="font-weight:300;"><?php get_page_date('F jS, Y'); ?></span>
					    </div>
					</div>
				</div>
			</div>
			<!-- /post -->
		</div>
	</div>
<?php include('footer.inc.php'); ?>
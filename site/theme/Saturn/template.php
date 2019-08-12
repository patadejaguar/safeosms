<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:		template.php
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
			    <div class="unit-100">
			    <h1 style="color:#fff; text-align:center; text-shadow: 0 0 5px black;"><?php get_page_title(); ?></h1>
			    </div>
			</div>
		</div>
	</div>
	
	<!-- Content -->
	<div class="content" style="padding-top:0;">
		<div class="container">
			<!-- Post -->
			<div class="post">
				<!-- Heading -->
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
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:      nosidebar.php
* @Package:   GetSimple
* @Action:    Bootstrap3 for GetSimple CMS
*
*****************************************************/
?>
<?php include('header.inc.php'); ?>
	<h1 class="container"><?php get_page_title(); ?></h1>
	<?php get_page_content(); ?>
<?php include('footer.inc.php'); ?>

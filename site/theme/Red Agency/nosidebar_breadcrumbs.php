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
<div class="container">	  
	<div class="row">
	 <div class="col-md-12">
		 <?php if (function_exists('get_i18n_breadcrumbs')) { 
				if(return_page_slug()!='index') { 
				$to_home=return_i18n_menu_data('index'); ?>
				<div class="breadcrumbs">
					<a href="<?php echo find_url('index',null); ?>"><?php echo $to_home[0]['menu'].'&nbsp;&nbsp;'; ?></a>
					<?php get_i18n_breadcrumbs(return_page_slug()); ?>
				</div>
		<?php }} ?>
		<h1><?php get_page_title(); ?></h1>
		<?php get_page_content(); ?>

<?php include('footer.inc.php'); ?>

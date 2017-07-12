<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
	/****************************************************
		*
		* @File:      search.php
		* @Package:   GetSimple
		* @Action:    Bootstrap3 for GetSimple CMS
		*
	*****************************************************/
?>

<?php include('header.inc.php'); ?>

<div class="container">	  
	<div class="row">
        <div class="col-md-8">
			<?php if (function_exists('get_i18n_breadcrumbs')) { 
				if(return_page_slug()!='index') { 
				$to_home=return_i18n_menu_data('index'); ?>
				<div class="breadcrumbs">
					<a href="<?php echo find_url('index',null); ?>"><?php echo $to_home[0]['menu'].'&nbsp;&nbsp;'; ?></a>
					<?php get_i18n_breadcrumbs(return_page_slug()); ?>
				</div>
			<?php }} ?>
			<h1 id="pagetitle"><?php echo get_lang_param('RA_SEARCH'); ?></h1>
			<?php
				if (function_exists('get_i18n_search_form')) {
					if(isset($_POST['keywords'])) $keywords = @explode(' ', $_POST['keywords']);
					if($language == "ru") $format_date = '%d.%m.%Y';
					else $format_date = '%Y.%m.%d';
					if(isset($_GET['tags'])) $keytags = $_GET['tags'];
					if(isset($_GET['words'])) $keywords = $_GET['words'];
					get_i18n_search_form(array('slug'=>'search'));
					if(!empty($keywords) && !empty($keytags) && !is_array($keywords)) {
						get_i18n_search_results(array('tags'=>$keytags, 'words'=>$keywords, 'DATE_FORMAT'=>$format_date));
					}
					else {
						if(!empty($keywords)) { get_i18n_search_results(array('words'=>$keywords, 'DATE_FORMAT'=>$format_date)); }
						if(!empty($keytags)) { get_i18n_search_results(array('tags'=>$keytags, 'DATE_FORMAT'=>$format_date)); }
					}
				} 
				else {
					get_search_results();
				}	
			?>
		</div>
	</div>
	
	<?php include('footer.inc.php'); ?>
	
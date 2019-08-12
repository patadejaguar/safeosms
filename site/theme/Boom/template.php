<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } 
/****************************************************
*
* @File: 		template.php
* @Package:		GetSimple
* @Action:		Theme for GetSimple CMS
* @Author:      Oleg Svetlov http://getsimplecms.ru/
*
*****************************************************/

# Include the header template
include('header.inc.php'); 
?>

		<?php if ( return_page_slug() == 'index' ) { ?>
		<!-- slider -->
		<div id="slider">
			<div class="container">
				<div class="col-12">
					<div class="alt-slider">
						<img alt="" src="<?php get_theme_url(); ?>/images/slider/fullwidth-slide1.jpg" />
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<!-- /slider -->		
		<?php } ?>
		
		<!-- content -->
		<div id="content">
			<div class="container">
					<div class="col-12">
					
						<!-- page title -->
						<div id="page-title">
							<h1><?php get_page_title(); ?></h1>
							<div id="bold-line"></div>
						</div>
						<!-- /page title -->
						
					</div>
					<div class="clearfix"></div>
					
					<!-- article -->
					<div class="col-8">
						<div class="article padding-right">
							<?php get_page_content(); ?>
						</div>
					</div>
					<!-- /article -->
					
					<!-- sidebar -->
					<aside class="col-4" id="sidebar">
						<?php get_component('sidebar'); ?>
					</aside>
					<!-- /sidebar -->
					<div class="clearfix"></div>
			</div>	
			
		</div>
		<!-- /content -->
		
	</div>	
	<!-- /chrome fon -->
	
<!-- include the footer template -->
<?php include('footer.inc.php'); ?>	
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 		template_home.php
* @Package:		GetSimple
* @Action:		Foundation theme for GetSimple CMS
*
*****************************************************/

# Include the header template
include('includes/header.inc.php');
?>

<!-- Content ================================================================================= -->

<div class="blueBG">
   <div class="row go">
      <div class="medium-7 columns">
           <?php get_component('home_header_img'); ?>
      </div>
      <div class="medium-5 text-center columns topCTA">
         <h1><?php get_page_title(); ?></h1>
         <p class="lead"><?php get_component('home_subtitle'); ?></p>
         <a href="#" class="button large">Learn More...</a>
      </div>
   </div>
</div>

<div class="wave"></div>

<div class="row topText">
	<div class="medium-6 columns">
		<h2>Our Agency, our selves.</h2>
		<p>Vivamus luctus urna sed urna ultricies ac tempor dui sagittis. In condimentum facilisis porta. Sed nec diam eu diam mattis viverra. Nulla fringilla, orci ac euismod semper, magna diam porttitor mauris, quis sollicitudin sapien justo in libero. Vestibulum mollis mauris enim. Morbi euismod magna ac lorem rutrum elementum. Donec viverra auctor.</p>
		<a class="button radius" href="#">So Basic</a>
	</div>
	
	<div class="medium-4 medium-offset-1 columns">
		<?php get_component('mid_img_1') ?>
	</div>
</div>

<div class="row topText">
	<div class="medium-4 columns">
		<?php get_component('mid_img_2'); ?>
	</div>
	<div class="medium-7 columns">
		<h2>Our Agency, our selves.</h2>
		<p>Vivamus luctus urna sed urna ultricies ac tempor dui sagittis. In condimentum facilisis porta. Sed nec diam eu diam mattis viverra. Nulla fringilla, orci ac euismod semper, magna diam porttitor mauris, quis sollicitudin sapien justo in libero. Vestibulum mollis mauris enim. Morbi euismod magna ac lorem rutrum elementum. Donec viverra auctor.</p>
		<a class="button radius" href="#">So Basic</a>
	</div>
</div>

<div class="row topText">
	<div class="medium-6 columns">
		<h2>Our Agency, our selves.</h2>
		<p>Vivamus luctus urna sed urna ultricies ac tempor dui sagittis. In condimentum facilisis porta. Sed nec diam eu diam mattis viverra. Nulla fringilla, orci ac euismod semper, magna diam porttitor mauris, quis sollicitudin sapien justo in libero. Vestibulum mollis mauris enim. Morbi euismod magna ac lorem rutrum elementum. Donec viverra auctor.</p>
		<a class="button radius" href="#">So Basic</a>
	</div>
	
	<div class="medium-4 medium-offset-1 columns">
		<?php get_component('mid_img_3'); ?>
	</div>
</div>

<div class="blue2BG" id="clouds">
   <div class="row">
	   <div class="medium-4 columns">
		   <h3>Fun</h3>
		   <p>* * * * *</p>
		   <p>Vivamus luctus urna sed urna ultricies ac tempor dui sagittis. In condimentum facilisis porta. Sed nec diam eu diam mattis viverra. Nulla fringilla, orci ac euismod semper, magna.</p>
		    <div class="row">
		         <div class="medium-6 medium-centered columns">
		            <img src="<?php get_theme_url(); ?>/images/fun.png" alt="fun image">
		         </div>
		    </div>
	   </div>
   
	   <div class="medium-4 columns">
	      <div class="row">
	         <div class="medium-6 medium-centered columns">
	            <img src="<?php get_theme_url(); ?>/images/bubbles.png" alt="girl blowing bubbles">
	         </div>
	      </div>
	   	<h3>Stability</h3>
	   	<p>* * * * *</p>
	   	<p>Vivamus luctus urna sed urna ultricies ac tempor dui sagittis. In condimentum facilisis porta. Sed nec diam eu diam mattis viverra. Nulla fringilla, orci ac euismod semper, magna.</p>
	   </div>
	   
	   <div class="medium-4 columns">
		   <h3>Learning</h3>
		   <p>* * * * *</p>
		   <p>Vivamus luctus urna sed urna ultricies ac tempor dui sagittis. In condimentum facilisis porta. Sed nec diam eu diam mattis viverra. Nulla fringilla, orci ac euismod semper, magna.</p>
		    <div class="row">
		         <div class="medium-6 medium-centered columns">
		            <img src="<?php get_theme_url(); ?>/images/pencil.png" alt="pencil image">
		         </div>
		    </div>
	   </div>
	</div>
</div>







<!-- Content ================================================================================= -->


<?php
include('includes/footer.inc.php');
?>
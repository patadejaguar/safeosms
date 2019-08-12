<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>
<div class="blueBG">
<footer>
	<div class="row footer">
		<div class="large-12 columns">
		
		<div class="menu-centered">
         <ul class="menu">
		       <?php get_navigation(return_page_slug()); ?>
		   </ul>
		</div>
		
		
		</div>
	</div>
</footer>

<div class="row">
<hr>
	<div class="medium-4 columns"> 
		<small>
		&copy; <?php echo date('Y'); ?> | Designed by: <a href="http://www.codecobber.co.uk" target="_blank">Code Cobber</a>
		</small>
   </div>
   
   <div class="medium-4 columns" style="text-align: center;"> 
		<img src="<?php get_theme_url(); ?>/images/miniIcon.png" alt="your icon">
   </div>
   
   <div class="medium-4 columns"> 
		<small class="float-right">
			<?php get_site_credits(); get_footer(); ?>
		</small>
   </div>
   
</div>
</div>

</div> <!-- close off-canvas-content -->
</div> <!-- close off-canvas-wrapper-inner -->
</div> <!-- close off-canvas-wrapper -->


    <script src="<?php get_theme_url(); ?>/js/vendor/jquery.min.js"></script>
    <script src="<?php get_theme_url(); ?>/js/vendor/what-input.min.js"></script>
    <script src="<?php get_theme_url(); ?>/js/foundation.min.js"></script>
    <script src="<?php get_theme_url(); ?>/js/app.js"></script>
    
  </body>
</html>

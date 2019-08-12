<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } 
/****************************************************
*
* @File: 		footer.inc.php
* @Package:		GetSimple
* @Action:		Theme for GetSimple CMS
* @Author:      Oleg Svetlov http://getsimplecms.ru/
*
*****************************************************/
?>
		
	<!-- copy -->
	<div id="copy">
	    <div class="container">
	        <div class="col-6">
			    <div>
				    Copyright &copy; <?php echo date('Y'); ?> <a href="<?php get_site_url(); ?>" ><?php get_site_name(); ?></a>
			    </div>	
	        </div>
	        <div class="col-6">
			    <div class="text-right">
					<div id="cms">
						<a class="gs" href="http://get-simple.info/" target="_blank" title="GetSimple CMS">GS</a> 
						<a class="ru" href="http://getsimplethemes.ru/" target="_blank" title="Themes for GetSimple CMS">RU</a>
					</div>
			    </div>	
	        </div>	
            <div class="clearfix"></div>
        </div>			
	</div>
	<!-- /copy -->	
	
</div>	
<!-- /wrapper -->

<a id="gotoTop" href="#">&and;</a>	

<!-- scripts -->
<script type="text/javascript" src="<?php get_theme_url(); ?>/js/custom.js"></script>
<script type="text/javascript" src="<?php get_theme_url(); ?>/js/jquery.mobilemenu.js"></script>

<?php get_footer(); ?>
</body>
</html>
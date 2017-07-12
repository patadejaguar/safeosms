<?php 
$param = "<?php
	global \x24args;
	global \x24TEMPLATE;
	\x24error_mess1= 'Sending parameters must be in array.';
	\x24error_mess2= 'Page content slug(s) is required.';
	if(is_array(\x24args)) {
		if(isset(\x24args[0]) && !empty(\x24args[0]) ) {
			\x24param1=\x24args[0];
			if(!is_array(\x24args[0])) {
				\x24param1=array(\x24args[0]);
			}
			if(!is_array(\x24args[0])) {
				\x24param1=array(\x24args[0]);
				if( strstr( trim(\x24args[0]), ' ' ) ) {
					\x24param1=explode(' ', trim(\x24args[0]));
				}
			}
			\x24elem_count = sizeof(\x24param1);
		}
		else {  echo '<script type=\"text/javascript\">  alert(\"'.\x24error_mess2.'\"); </script>'; return; }
	}
	else { echo '<script type=\"text/javascript\">  alert(\"'.\x24error_mess1.'\"); </script>'; return; }
	\x24anim_effects=array('fadeInLeft','fadeInRight','bounceInDown','bounceIn','slideInUp','slideInDown','zoomIn','zoomInRight');
	\x24datax = getXML(GSDATAOTHERPATH.'theme_settings_'.\x24TEMPLATE.'.xml');
    if (\x24datax != null) {
		\x24use_fade=\x24datax->use_fade;
		if(!isset(\x24use_fade) || empty(\x24use_fade)) \x24use_fade=0;
		if(!isset(\x24datax->min_height) || empty(\x24datax->min_height)) \x24min_height='';
		else \x24min_height=\x24datax->min_height.'px;';
		if(!isset(\x24datax->max_height) || empty(\x24datax->max_height)) \x24max_height='';
		else \x24max_height=\x24datax->max_height.'px;';
		if(!isset(\x24datax->control_color) || empty(\x24datax->control_color)) \x24ctrl_color='';
        else \x24ctrl_color=\x24datax->control_color;
		\x24indi_color=\x24datax->indicator_color;
		if(!isset(\x24indi_color) || empty(\x24indi_color)) \x24indi_color='';
	}
	if(\x24use_fade==1) {
	?>
	<style>
		.carousel.fade {
		opacity: 1;
		overflow: hidden;
		}
		.carousel.fade .item {
		-webkit-transition: opacity 1s;
		-moz-transition: opacity 1s;
		-ms-transition: opacity 1s;
		-o-transition: opacity 1s;
		transition: opacity 1s;
		}
		.carousel.fade .active.left, .carousel .active.right {
		left:0;
		opacity:0;
		z-index:2;
		}
		.carousel.fade .next, .carousel .prev {
		left:0;
		opacity:1;
		z-index:1;
		}
<?php
			if(!empty(\x24indi_color)) { ?>
				.carousel-indicators .active {
					background-color:<?php echo \x24indi_color; ?>;
                    border: 1px solid <?php echo \x24indi_color; ?>;
				}
                .carousel-indicators li {
					border: 1px solid <?php echo \x24indi_color; ?>;
				}
<?php 		} ?>
	</style>
<?php } ?>
<div id=\"carousel-example-generic\" class=\"carousel slide<?php echo (\x24use_fade==1)?' fade':'';?>\" data-ride=\"carousel\">
	<!-- Indicators -->
	<ol class=\"carousel-indicators\">
		<?php
			for( \x24c=0; \x24c < count( \x24param1 ); \x24c++){
			?>
			<li data-target=\"#carousel-example-generic\" data-slide-to=\"<?php echo \x24c; ?>\" class=\"<?php echo (\x24c==0)?' active':'';?>\"></li>
			<?php
			}
		?>
	</ol>
	<!-- Slider Content (Wrapper for slides )-->
	<div class=\"carousel-inner\" id=\"carousel-inner\">
		<?php
			\x24slide_count = 1;
			foreach (\x24param1 as \x24ablock) {
			?>
			<div id=\"item-<?php echo \x24slide_count; ?>\" class=\"item <?php echo (\x24slide_count==1)?' active':'';?>\" style=\"height:100%;width:100%;<?php echo (!empty(\x24max_height))?'max-height:'.\x24max_height:'';?>\" >
				<?php
				if (function_exists('return_i18n_page_data')) {
					\x24mycont =  return_i18n_page_data(\x24ablock);
					\x24mycontent = preg_replace( \"/\n\s+/\", \"\n\", rtrim(html_entity_decode(strip_tags(\x24mycont->content))) );
				} else {
					\x24mycontent =  returnPageContent(\x24ablock);
				}
					
					\x24doc = new DOMDocument();
					\x24doc->loadHTML(mb_convert_encoding(\x24mycontent, 'HTML-ENTITIES', 'UTF-8'));
					\x24tags = \x24doc->getElementsByTagName('p');
					if ( count(\x24tags ) ) {
						\x24element_count = 1;
						foreach ( \x24tags as \x24tag ) {
							\x24style =  '';
							\x24src =  '';
							\x24position =  '';
							\x24effect =  '';
							\x24delay = '';
							\x24delayms = 500;
							\x24position = \x24tag->getAttribute(\"style\");
							if(!isset(\x24position) || empty(\x24position)) {
								\x24position = 'top:'.rand(-10,40).'%;left:'.rand(-50,50);
							}
							\x24effect = \x24tag->getAttribute(\"data-effect\");
							if(!isset(\x24effect) || empty(\x24effect)) {
								\x24effect = \x24anim_effects[rand(0,7)];
							}
							\x24delays = \x24tag->getAttribute(\"data-delay\");
							if(!isset(\x24delays) || empty(\x24delays)) {
								\x24delay = '-webkit-animation-delay:'.\x24delayms*\x24element_count.'ms;animation-delay:'.\x24delayms*\x24element_count.'ms';
								} else {
								\x24delay = '-webkit-animation-delay:'.\x24delays.'ms;animation-delay:'.\x24delays.'ms';
							}
							
							for( \x24i=0; \x24i < \x24tag->childNodes->length; \x24i++){
								\x24cn = \x24tag->childNodes->item(\x24i);
								\x24cn_name = \x24cn->nodeName;
								if(\x24cn_name == \"span\") {
									if(\x24element_count==1) {
										\x24attrib = \x24cn->getAttribute(\"style\");
										\x24style =  \x24attrib;
									?>
									<div style=\"<?php echo \x24style; ?> height:100%;width:100%;<?php echo (!empty(\x24min_height))?'min-height:'.\x24min_height:'';?>\"></div>
									<?php
									} else { \x24style =  \x24cn->getAttribute(\"style\"); }
								}
								if(\x24cn_name == \"img\") {
									if(\x24element_count==1) {
										\x24src =  \x24cn->getAttribute(\"src\");
									?>
									<img src=\"<?php echo \x24src; ?>\" style=\"width:100%;height:<?php echo \x24max_height; ?>;\" >
									<?php
									} else {
												\x24src =  \x24cn->getAttribute(\"src\");
												\x24img_style =  \x24cn->getAttribute(\"style\");
											}
								}
								for( \x24j=0; \x24j < \x24cn->childNodes->length; \x24j++){
									if(\x24element_count!=1) {
										\x24sub_cn = \x24cn->childNodes->item(\x24j)->nodeName;
										\x24sub_style =  \x24cn->childNodes->item(\x24j)->getAttribute(\"style\");
									}
								}
								if(isset(\x24sub_style) && \x24sub_style) \x24style = \x24style.' '.\x24sub_style;
								if(\x24delay) \x24style = \x24style.' '.\x24delay;
							}
							if(\x24element_count!=1) {
							?>
							<div class=\"carousel-caption\" style=\"<?php echo \x24position;?>\" >
								<p id=\"carousel-caption\" class=\"\" data-animation=\"animated <?php echo \x24effect;?>\" style=\"<?php echo \x24style; ?>\">
									<?php 
											if(\x24cn_name == \"img\") { ?>
												<img src=\"<?php echo \x24src; ?>\" style=\"<?php echo \x24img_style; ?>\">
											<?php
											} else {
												echo (string)\x24tag->nodeValue; }  ?>
								</p>
							</div>
							<?php
							}
							\x24element_count = \x24element_count+1;
						}
					}
					\x24slide_count = \x24slide_count+1;
				?>
			</div>
			<?php
			}
		?>
	</div>
	<!-- Controls -->
	<a class=\"left carousel-control\" href=\"#carousel-example-generic\" data-slide=\"prev\"  style=\"z-index: 999;<?php echo (!empty(\x24ctrl_color))?'color:'.\x24ctrl_color:'';?>\" >
	<span class=\"glyphicon glyphicon-chevron-left\"></span>
	</a>
	<a class=\"right carousel-control\" href=\"#carousel-example-generic\" data-slide=\"next\"  style=\"z-index: 999;<?php echo (!empty(\x24ctrl_color))?'color:'.\x24ctrl_color:'';?>\" >
	<span class=\"glyphicon glyphicon-chevron-right\"></span>
	</a>
</div>";
?>
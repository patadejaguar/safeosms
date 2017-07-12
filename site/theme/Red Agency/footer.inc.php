<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:      footer.inc.php
* @Package:   GetSimple
* @Action:    Bootstrap3 for GetSimple CMS
*
*****************************************************/


if(function_exists('return_theme_setting') && return_theme_setting('gototop')==1) {
	global $language;
	$def_lang=$language."_".strtoupper($language);
	if(!isset($def_lang) || empty($def_lang)  || $language=="en") $def_lang="en_US";
	include(str_replace('\\','/',dirname(__FILE__)).'/lang/'.$def_lang.'.php');
?>
<a href="#" class="gototop glyphicon glyphicon-chevron-up" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo $set_lang['RA_TOTOP']; ?>"></a>
<?php } ?>
      <hr>

      <footer>
        <p></p>
      </footer>

    </div> <!-- /container -->
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
<?php
if (function_exists('return_theme_setting')) {
	if(return_theme_setting('jquery')==1) { ?>
    <script src="<?php get_theme_url(); ?>/js/jquery-1.10.2.min.js"></script>
<?php }
		if(return_theme_setting('bootstrap')==1) {
?>
    <script src="<?php get_theme_url(); ?>/js/bootstrap.min.js"></script>
<?php } 
		if(return_theme_setting('wow')==1) { 
?>
	<script src="<?php get_theme_url(); ?>/js/wow.min.js"></script>
	<script> new WOW().init(); </script>
<?php }
		if(return_theme_setting('prettyphoto')==1) {
?>
	<script src="<?php get_theme_url(); ?>/js/jquery.prettyPhoto.js"></script>
	<script>
		//jQuery.noConflict();
		$("a[rel^='prettyPhoto']").prettyPhoto({
			social_tools: false
		});
	 </script>
    <?php
	  }
	if(return_theme_setting('tooltip')==1) {
		echo "<script> $(function () { $('[data-toggle=\"tooltip\"]').tooltip() }) </script>";
	}
	if(return_theme_setting('popover')==1) {
		echo "<script> $(function () { $('[data-toggle=\"popover\"]').popover() }) </script>";
	}
}
else { ?>
	<script src="<?php get_theme_url(); ?>/js/jquery-1.10.2.min.js"></script>
	<script src="<?php get_theme_url(); ?>/js/bootstrap.min.js"></script>
<?php } ?>
	<script>
	  if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
        var msViewportStyle = document.createElement("style")
        msViewportStyle.appendChild(
          document.createTextNode(
            "@-ms-viewport{width:auto!important}"
          )
        )
        document.getElementsByTagName("head")[0].appendChild(msViewportStyle)
      }
	  
    </script>
<?php
if(function_exists('return_theme_setting') && return_theme_setting('gototop')==1) { ?>
    <script type="text/javascript">
		$(document).ready(function() {
			//Scroll to top function
			var totop = "<?php echo return_theme_setting('gototop');?>";
			if(totop == 1) {
				$(window).scroll(function(){
					if ($(this).scrollTop() > 100) {
					$('.gototop').fadeIn();
					} else {
					$('.gototop').fadeOut();
					}
				});
	
				//Click event to scroll to top
				$('.gototop').click(function(){
					$('html, body').animate({scrollTop : 0},800);
					return false;
				});
			}
        });
    </script>
<?php }


if(function_exists('return_theme_setting') && return_theme_setting('carousel')==1) {
	$slider_interval=return_theme_setting('interval');
	if(!isset($slider_interval) || empty($slider_interval)) $slider_interval=5000;
?>
	<script>
		(function( $ ) {
			var interval = "<?php echo $slider_interval; ?>";
			//Function to animate slider captions 
			function doAnimations( elems ) {
				//Cache the animationend event in a variable
				var animEndEv = 'webkitAnimationEnd animationend';
				elems.each(function () {
					var $this = $(this),
					$animationType = $this.data('animation');
					$this.addClass($animationType).one(animEndEv, function () {
						$this.removeClass($animationType);
					});
				});
			}
			//Variables on page load 
			var $myCarousel = $('#carousel-example-generic');
			$firstAnimatingElems = $myCarousel.find('.item:first').find("[data-animation ^= 'animated']");
			//Initialize carousel 
			$myCarousel.carousel({
				interval: interval
			});
			//Animate captions in first slide on page load 
			doAnimations($firstAnimatingElems);
			//Pause carousel  
			$myCarousel.carousel('pause');
			//Other slides to be animated on carousel slide event 
			$myCarousel.on('slide.bs.carousel', function (e) {
				var $animatingElems = $(e.relatedTarget).find("[data-animation ^= 'animated']");
				doAnimations($animatingElems);
			});
			$myCarousel.on('slid.bs.carousel', function (e) {
				var $animatingElems = $(e.relatedTarget).find("[data-animout ^= 'animated']");
				doAnimations($animatingElems);
			});
		})(jQuery);
	
	$(".dropdown-menu li").on('mouseenter mouseleave', function (e) {
		var elm = $('ul:first', this);
		var off = elm .offset();
		var l = off.left;
		var w = elm.width();
		var docH = $(".container").height();
		var docW = $(".container").width();

		var isEntirelyVisible = (l+ w <= docW);

		if ( ! isEntirelyVisible ) {
			$(this).addClass('edge');
			$(this).find(".dropdown-menu").css("left", "-95%"); 
		} else {
			$(this).removeClass('edge');
			$(this).find(".dropdown-menu").removeAttr("style");
		}
	});
	</script>	
    
<?php } ?>

    <?php get_footer(); ?>
  </body>
</html>
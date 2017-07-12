<!DOCTYPE html>
<!--[if IE 7 ]>    <html lang="<?php global $language; echo @$language ? $language : 'en'; ?>" class="ie7 no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="<?php global $language; echo @$language ? $language : 'en'; ?>" class="ie8 no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="<?php global $language; echo @$language ? $language : 'en'; ?>" class="ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="no-js" lang="ru"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title><?php get_page_clean_title(); ?> &lt; <?php get_site_name(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="<?php get_theme_url(); ?>/js/jquery-1.8.3.min.js"></script>
	<?php get_header(); ?>
    <!-- styles -->
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Marmelad&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<link href="<?php get_theme_url(); ?>/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?php get_theme_url(); ?>/css/mobile.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?php get_theme_url(); ?>/css/fontello.css">
	<link href="<?php get_theme_url(); ?>/css/responsive.css" rel="stylesheet" type="text/css">
    <link href="<?php get_theme_url(); ?>/css/slider.css" rel="stylesheet" type="text/css">
    <script src="<?php get_theme_url(); ?>/js/modernizr.js" type="text/javascript"></script>
    
    
	<link href="<?php get_site_url(); ?>/formoid1/formoid-flat-blue.css" rel="stylesheet" type="text/css" />
	
	
	<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<!--<link href="<?php get_site_url(); ?>/formoid1/rangeslider.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="<?php get_site_url(); ?>/formoid1/rangeslider.min.js"></script> -->
	    
</head>

<body id="<?php get_page_slug(); ?>">
<!-- wrapper -->
<section id="wrapper">
  <div class="border-wrapp">
    <!-- header -->
    <header id="header">
	    <section class="container">
		    <div class="col-two-thirds">
			    <div class="logo">
						<a href="<?php get_site_url(); ?>">
						<img alt="" src="<?php get_theme_url(); ?>/logos/logo.png" />
					<span class="padd"><?php get_site_name(true); ?></span>
					<span class="slogan"><?php get_component('tagline'); ?></span>
					</a>
					</div>
			</div>
		    <div class="col-three">
					<div class="social align-right">
					<a href="#" class="icon-twitter" title="Twitter"></a>
					<a href="#" class="icon-facebook" title="Facebook"></a>
					<a href="#" class="icon-youtube" title="You Tube"></a></div>
			    <div class="top-links align-right"><i class="icon-phone-squared"></i><span>+X XXX-XXX-XXXX</span>
					</div>
			</div>
		    <div class="clearfix"></div>			
		</section>
		<section class="container">
		    <div class="col-one">
		        <div class="fon-menu shadow">
			        <nav class="nav">
	                    <ul class="menu">
                            <?php get_navigation(get_page_slug(FALSE)); ?>
	                    </ul>
                    </nav>					
                </div>	
		    </div>
		    <div class="clearfix"></div>
	    </section>		
	</header>
	<!-- /header -->
	
	<?php if ( return_page_slug() == 'index' ) { ?>
	<!-- slider -->
	<section id="slider">
		<section class="container">
			<div class="col-one">
<!--				<div class="cycle-slideshow" data-cycle-slides="> div" data-cycle-pause-on-hover="true" data-cycle-prev=".prev" data-cycle-next=".next" data-cycle-pager=".cycle-pager" data-cycle-speed="1000" data-cycle-timeout="2000" data-cycle-swipe="true" data-cycle-loader="true">
					<div data-cycle-fx="tileSlide" data-cycle-tile-vertical="false" data-cycle-tile-count="6">
						<figure>
							<img alt="" src="//placehold.it/1000x500&text=SLIDE" />
							<figcaption class="caption">
                                <h2>The first heading</h2>
								<h3>The second heading</h3>
								<p>A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.</p>
                                <a href="#">Read more</a>
                            </figcaption>
						</figure>
					</div>
				</div> -->
			</div>
			<div class="clearfix"></div>
		</section>
	</section>
    <!-- /slider -->
	<?php } else { ?>
	<!-- title -->
    <!-- <section id="title">
	    <section class="container">	
			<div class="col-one">
			<h1><?php get_page_title(); ?></h1>
		    </div>
		    <div class="clearfix"></div>
		</section>
	</section>-->
	<!-- /title -->
	<?php } ?>
	
    <!-- content -->
    <section id="content">
	    <section class="container">	
			<article class="col-one">
			    <div class="text">
				<?php if ( return_page_slug() == 'index' ) { ?>
					<h1><?php get_page_title(); ?></h1>
				<?php } ?>
					<?php get_page_content(); ?>
				</div>
			</article>				
		    <aside class="col-three">
				<div class="widget">
					<?php get_component('sidebar');	?>
				</div>
			</aside>			
			<div class="clearfix"></div>
	    </section>
	</section>
	<!-- /content -->
    <!-- footer -->
    <footer id="footer">
	    <section class="container">
		<!--<section class="container">
			<div class="col-three">
				<h3>Heading</h3>
				<p>A small river named Duden flows by their place and supplies it with the necessary <a href="#">regelialia</a>.  It is a paradisematic country, in which roasted parts of sentences fly into your mouth. A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.</p>	
			</div> 
			<div class="col-three">
				<h3>Heading</h3>
				<p>A small river named Duden flows by their place and supplies it with the necessary <a href="#">regelialia</a>.  It is a paradisematic country, in which roasted parts of sentences fly into your mouth. A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.</p>	
			</div> 
			<div class="col-three">
				<h3>Heading</h3>
				<p>A small river named Duden flows by their place and supplies it with the necessary <a href="#">regelialia</a>.  It is a paradisematic country, in which roasted parts of sentences fly into your mouth. A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth.</p>	
			</div> 
			<div class="clearfix"></div>
		</section>-->
		</section>
        <section id="copy">
			<div class="col-two"><div>Copyright © <?php echo date('Y'); ?> - <a href="<?php get_site_url(); ?>" ><strong><?php get_site_name(); ?></strong></a></div></div>
			<div class="col-two">
			    <div class="align-right">
			        safe-osms
				</div>
			</div>
			<div class="clearfix"></div>
			<div id="cms">
<!--			<a href="http://www.sipakal.com/" target="_blank" title="Balam Gonzalez Luis Humberto"><img alt="Balam Gonzalez Luis Humberto" src="<?php get_theme_url(); ?>/logos/getsimple20x20.png" /></a>-->
			<a href="http://www.sipakal.com/" target="_blank" title="Balam Gonzalez Luis Humberto"><img alt="Balam Gonzalez Luis Humberto" src="<?php get_theme_url(); ?>/logos/bws20x20.png" /></a></div>
		</section>
	</footer>
    <!-- /footer -->
	</div>
</section>
<!-- /wrapper -->
<a href="#" id="gotoTop" title="go to Top"><i class="icon-up-open"></i></a>
<script type="text/javascript" src="<?php get_theme_url(); ?>/js/jquery.mobilemenu.js"></script>
<script type="text/javascript" src="<?php get_theme_url(); ?>/js/custom.js"></script>
<?php get_footer(); ?>
</body>
</html>

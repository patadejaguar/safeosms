<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:      header.inc.php
* @Package:   GetSimple
* @Action:    Bootstrap3 for GetSimple CMS
*
*****************************************************/

$LANG = getDefaultLanguage();

check_data_theme();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php get_theme_url(); ?>/ico/favicon.png">

    <title><?php get_page_clean_title(); ?> - <?php get_site_name(); ?></title>

    <!-- Bootstrap core CSS -->
	<link href="<?php get_theme_url(); ?>/css/font-awesome.min.css" rel="stylesheet">
<?php
if (function_exists('return_theme_setting')) {
	if(return_theme_setting('bootstrap')==1) {
?>
    <link href="<?php get_theme_url(); ?>/css/bootstrap.min.css" rel="stylesheet">
<?php }
	if(return_theme_setting('carousel')==1) {
?>
	<link href="<?php get_theme_url(); ?>/css/animate.min.css" rel="stylesheet">
<?php }
	if(return_theme_setting('prettyphoto')==1) {
?>
	<link href="<?php get_theme_url(); ?>/css/prettyPhoto.css" rel="stylesheet">
<?php 
	}
}
else { 
?>
<link href="<?php get_theme_url(); ?>/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php get_theme_url(); ?>/css/animate.min.css" rel="stylesheet">
<?php } ?>
	
    <!-- Custom styles for this template -->
  <?php // Fallback: use the configurable LESS sheet only, if plugins Less and Theme Settings are available ?>
  <?php if (function_exists('get_less_css') && function_exists('return_theme_settings')) { ?>
    <link rel="stylesheet" type="text/css" href="<?php get_less_css('css/default.less', return_theme_settings('default')); ?>" />
  <?php } else { ?>
    <link rel="stylesheet" type="text/css" href="<?php get_theme_url(); ?>/css/default.css" />
  <?php } ?>
	<link href="<?php get_theme_url(); ?>/css/responsive.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<?php get_theme_url(); ?>/js/html5shiv.js"></script>
      <script src="<?php get_theme_url(); ?>/js/respond.min.js"></script>
    <![endif]-->

	<link rel="shortcut icon" href="<?php get_theme_url(); ?>/images/ico/favicon.ico">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php get_theme_url(); ?>/images/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php get_theme_url(); ?>/images/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php get_theme_url(); ?>/images/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" href="<?php get_theme_url(); ?>/images/ico/apple-touch-icon-57-precomposed.png">
	
    <?php get_header(); ?>
</head>
  <body id="<?php get_page_slug(); ?>">
	<header id="header">
		<div class="top-bar">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-xs-4">
                        <?php if (function_exists('get_theme_setting')) { ?>
							<div class="top-number widesec"><p><i class="fa fa-phone-square"></i>
								<span class="fa-phone-number">
									<?php get_theme_setting('phone'); ?>
								</span></p>
							</div>
						<?php } ?>
                    </div>
                    <div class="col-sm-6 col-xs-8">
                       <div class="social">
                            <ul class="social-share">
						<?php if (!function_exists('get_theme_setting')) { ?>
                                <li><a href="https://facebook.com/asemion"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li> 
                                <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                                <li><a href="#"><i class="fa fa-skype"></i></a></li>
								<li><a href="#"><i class="fa fa-google-plus"></i></a></li>
						<?php }
						else { 
								if(return_theme_setting('facebook')) { 
									if(strlen(return_theme_setting('facebook'))>6 || return_theme_setting('facebook')=="#") { ?>
										<li><a href="<?php get_theme_setting('facebook'); ?>" target="_blank" title="Facebook"><i class="fa fa-facebook"></i></a></li>
							<?php	}
								}
								if(return_theme_setting('twitter')) { 
									if(strlen(return_theme_setting('twitter'))>6 || return_theme_setting('twitter')=="#") { ?>
										<li><a href="<?php get_theme_setting('twitter'); ?>" target="_blank" title="Twitter"><i class="fa fa-twitter"></i></a></li>
							<?php	}
								}
								if(return_theme_setting('linkedin')) { 
									if(strlen(return_theme_setting('linkedin'))>6 || return_theme_setting('linkedin')=="#") { ?>
										<li><a href="<?php get_theme_setting('linkedin'); ?>" target="_blank" title="Linkedin"><i class="fa fa-linkedin"></i></a></li> 
							<?php	}
								}
								if(return_theme_setting('dribbble')) { 
									if(strlen(return_theme_setting('dribbble'))>6 || return_theme_setting('dribbble')=="#") { ?>
										<li><a href="<?php get_theme_setting('dribbble'); ?>" target="_blank" title="Dribbble"><i class="fa fa-dribbble"></i></a></li>
							<?php	}
								}
								if(return_theme_setting('skype')) { 
									if(strlen(return_theme_setting('skype'))>6 || return_theme_setting('skype')=="#") { ?>
										<li><a href="<?php get_theme_setting('skype'); ?>" target="_blank" title="Skype"><i class="fa fa-skype"></i></a></li>
							<?php	}
								}
								if(return_theme_setting('google')) { 
									if(strlen(return_theme_setting('google'))>6 || return_theme_setting('google')=="#") { ?>
										<li><a href="<?php get_theme_setting('google'); ?>" target="_blank" title="Google+"><i class="fa fa-google-plus"></i></a></li>
							<?php	}
								}
								if(return_theme_setting('kontakte')) { 
									if(strlen(return_theme_setting('kontakte'))>6 || return_theme_setting('kontakte')=="#") { ?>
										<li><a href="<?php get_theme_setting('kontakte'); ?>" target="_blank" title="VKontakte"><i class="fa fa-vk"></i></a></li>
							<?php	}
								}
								if(return_theme_setting('instagram')) { 
									if(strlen(return_theme_setting('instagram'))>6 || return_theme_setting('instagram')=="#") { ?>
										<li><a href="<?php get_theme_setting('instagram'); ?>" target="_blank" title="Instagram"><i class="fa fa-instagram"></i></a></li>
							<?php	}
								}
							} ?>
                            </ul>
                            <div class="search">
                                <form role="form" id="search_form" action="index.php?id=search" method="post">
                                    <input type="text" class="search-form" name="keywords" autocomplete="off" placeholder="<?php echo get_lang_param('RA_SEARCH'); ?>">
                                    <i class="fa fa-search"></i>
                                </form>
                           </div>
                       </div>
                    </div>
                </div>
            </div><!--/.container-->
        </div><!--/.top-bar-->
    <nav class="navbar navbar-inverse" role="banner">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php get_site_url(); ?>"><?php get_site_name(); ?></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <?php 
				if (function_exists('return_i18n_menu_data')) {
					i18n_navigation_bootstrap(return_page_slug(),0,99,I18N_SHOW_MENU | I18N_FILTER_LANGUAGE );
				}
				else { get_navigation_bootstrap(return_page_slug()); }
				if (function_exists('get_i18n_lang_menu')) { get_i18n_lang_menu(); } ?>
          </ul>
        </div><!--/.nav-collapse -->
		
      </div>
    </nav><!--/nav-->

  </header><!--/header-->

<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:		template.php
* @Package:		GetSimple
* @Action:		Purecss Blog theme for GetSimple CMS
* @Author:		Kazuhiro Aoki
*
*****************************************************/
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

	<!-- Site Title -->
	<title><?php get_page_clean_title(); ?> &lt; <?php get_site_name(); ?></title>
	
	<?php get_header(); ?>

	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
    <!--[if lte IE 8]>
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-old-ie-min.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/grids-responsive-min.css">
    <!--<![endif]-->
    	<!--[if lte IE 8]>
            <link rel="stylesheet" href="<?php get_theme_url(); ?>/css/layouts/blog-old-ie.css">
        <![endif]-->
        <!--[if gt IE 8]><!-->
            <link rel="stylesheet" href="<?php get_theme_url(); ?>/css/layouts/blog.css">
        <!--<![endif]-->
<!--[if lt IE 9]>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>
    <![endif]-->
</head>
<body id="<?php get_page_slug(); ?>" >

<div id="layout" class="pure-g">

    <div class="sidebar pure-u-1 pure-u-md-1-4">
        <div class="header">
            <h1 class="brand-title"><?php get_site_name(); ?></h1>
            <h2 class="brand-tagline"><?php get_component('tagline'); ?></h2>

            <nav class="nav">
                <ul class="nav-list" id="nav-list"><?php get_navigation(get_page_slug(false) , "nav-item "); ?></ul>
            </nav>
        </div>
    </div>

    <div class="content pure-u-1 pure-u-md-3-4">
        <div>
            <!-- A wrapper for all the blog posts -->
			<h2 class="post-title"><?php get_page_title(); ?></h2>
			<!--Post -->
			<div class="posts">
            	<h1 class="content-subhead"></h1>
            	<section class="post">
            	   <div class="post-description"><?php get_page_content(); ?></div>
            	</section>
            </div>

            <!-- Pinned -->
            <div class="posts">
            	<h1 class="content-subhead">Profile</h1>

            	<section class="post">
            	   <div class="post-description"><?php get_component('profile'); ?></div>
            	</section>
            </div>
			<!-- Pinned -->
            <div class="posts">
            	<h1 class="content-subhead">sidebar</h1>

            	<section class="post">
            	   <div class="post-description"><?php get_component('sidebar'); ?></div>
            	</section>
            </div>
			
			<div class="post">
				<h1 class="content-subhead">Credit</h1>
				<section class="post">
					<div class="post-description">
			<p><?php echo date('Y'); ?> - <strong><?php get_site_name(); ?></strong></p>
			<p>
				Pureblog Theme by <a href="yoursiteurl" >yourname</a><br />
				<?php get_site_credits(); ?>
			</p></div>
				</section>
				
		</div>
        </div>
    </div>
</div>

<!-- <div class="footer"> -->
	<!-- <p><a href="<?php get_site_url(); ?>"> <?php echo date('Y'); ?> - <?php get_site_name(); ?></a></p> -->
	<!-- <p><?php get_site_credits(); ?></p> -->
<!-- </div> -->
<!-- Purecss Blog theme -->
<script type="text/javascript">
/** nav-list css fix**/
var pages = document.getElementById('nav-list').getElementsByTagName('a');
Array.prototype.forEach.call(pages,function(link) {
  link.classList.add("pure-button");
});
</script>

</body>
</html>

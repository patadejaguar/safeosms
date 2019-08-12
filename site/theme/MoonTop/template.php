<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 		template.php
* @Package:		GetSimple
* @Action:		Foundation theme for GetSimple CMS
*
*****************************************************/

include('includes/header.inc.php');
?>

<!-- Content ================================================================================= -->

<div class="callout large">
<div id="headerButton" class="row column text-center">
<h1><?php get_page_title(); ?></h1>
<h2>Grow, love, care</h2>
</div>
</div>

<div class="row">
<div class="medium-6 columns medium-push-6">
<img class="thumbnail" src="http://placehold.it/750x350">
</div>
<div class="medium-6 columns medium-pull-6">
<?php get_page_content(); ?>
</div>
</div>






<!-- Content ================================================================================= -->


<?php
include('includes/footer.inc.php');
?>
<?php
$param = "<?php 
global \x24args;
\x24effects=array('fadeInDown','fadeInUp','fadeInLeft','fadeInRight','bounceInDown','bounceInUp','bounceInLeft','bounceInRight','slideInRight','slideInLeft','slideInDown','slideInUp');
\x24error_mess1= 'Sending parameters must be in array.';
\x24error_mess2= 'Page content slug(s) is required.';
if(is_array(\x24args)) {
	if(isset(\x24args[0]) && !empty(\x24args[0]) ) {
		\x24papam1=\x24args[0];
		if(!is_array(\x24args[0])) {
			\x24papam1=array(\x24args[0]);
		}
		if(!is_array(\x24args[0])) {
			\x24papam1=array(\x24args[0]);
			if( strstr( trim(\x24args[0]), ' ' ) ) {
				\x24papam1=explode(' ', trim(\x24args[0]));
			}
		}
        \x24elem_count = sizeof(\x24papam1);
	}
	else {  echo '<script type=\"text/javascript\">  alert(\"'.\x24error_mess2.'\"); </script>'; return; }
	if(isset(\x24args[1]) && !empty(\x24args[1]) && is_numeric(\x24args[1])) \x24papam2=\x24args[1];
	else  \x24papam2=1000;
	if(isset(\x24args[2]) && !empty(\x24args[2]) && is_numeric(\x24args[2])) \x24papam3=\x24args[2];
	else  \x24papam3=600;
	if(isset(\x24args[3]) && !empty(\x24args[3]) && in_array(\x24args[3],\x24effects)) \x24papam4=\x24args[3];
	else  \x24papam4='fadeInDown';
    if(isset(\x24args[4]) && !empty(\x24args[4]) && is_numeric(\x24args[4])) \x24papam5=\x24args[4];
	else  \x24papam5='';
    if(isset(\x24args[5]) && !empty(\x24args[5])) \x24papam6=\x24args[5];
    else {
		\x24classes=array(1=>'col-sm-12',2=>'col-sm-6',3=>'col-sm-4',4=>'col-sm-3',5=>'col col-sm-3',6=>'col-sm-2');
		\x24papam6=\x24classes[\x24elem_count];
	}
}
else { echo '<script type=\"text/javascript\">  alert(\"'.\x24error_mess1.'\"); </script>'; return; }
?>
<div style=\"visibility: hidden;\" class=\"container wow <?php echo \x24papam4; ?>\" data-wow-delay=\"<?php echo \x24papam3; ?>ms\" data-wow-duration=\"<?php echo \x24papam2; ?>ms\">
	<div class=\"row\">
<?php
global \x24content;
foreach (\x24papam1 as \x24ablock) {
    if (!function_exists('return_i18n_page_data')) {
		\x24title = getPageField(\x24ablock, 'title');
        \x24content = getPageContent(\x24ablock);
	}
	else {
		\x24tcontent = return_i18n_page_data(\x24ablock);
		\x24title = (string) \x24tcontent->title;
		\x24content = html_entity_decode( (string) \x24tcontent->content);
	} ?>
		<div class=\"<?php echo \x24papam6; ?>\">
			<div class=\"widget\">
				<h3><?php echo \x24title; ?></h3>
				<?php echo (!empty(\x24papam5)?get_page_excerpt(\x24papam5, false):\x24content); ?>
			</div>
		</div>
<?php } ?>
	</div>
</div>";
?>
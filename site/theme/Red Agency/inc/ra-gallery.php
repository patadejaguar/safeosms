<?php
$param = "<?php
global \x24args;
\x24error_mess1= 'Sending parameters must be in array.';
\x24error_mess2= 'Page content slug(s) is required.';
if(is_array(\x24args)) {
	if(isset(\x24args[0]) && !empty(\x24args[0]) ) {
		\x24papam1=\x24args[0];
		if(!is_array(\x24args[0])) {
			\x24papam1=array(\x24args[0]);
			if( strstr( trim(\x24args[0]), ' ' ) ) {
				\x24papam1=explode(' ', trim(\x24args[0]));
			}
		}
	}
	else {  echo '<script type=\"text/javascript\">  alert(\"'.\x24error_mess2.'\"); </script>'; return; }
	if(isset(\x24args[1]) && !empty(\x24args[1])) \x24papam2=true;
	else  \x24papam2=false;
	if(isset(\x24args[2]) && !empty(\x24args[2])) \x24papam3=true;
	else  \x24papam3=false;
}
?>
<div class=\"container\">
<?php
global \x24content;
foreach (\x24papam1 as \x24acont) {
	if (!function_exists('return_i18n_page_data')) {
		\x24content = getPageContent(\x24acont);
	}
	else {
		\x24tcontent = return_i18n_page_data(\x24acont);
		\x24content = html_entity_decode( (string) \x24tcontent->content);
	}
	\x24doc = new DOMDocument();
	\x24doc->loadHTML(mb_convert_encoding(\x24content, 'HTML-ENTITIES', 'UTF-8'));
	\x24xml=simplexml_import_dom(\x24doc);
	\x24images=\x24xml->xpath('//img');
	foreach (\x24images as \x24img) { 
		\x24img_thumb = str_replace(\"uploads\", \"thumbs\", \x24img['src']);
	?>
		<div class=\"col-xs-12 col-sm-4 col-md-3\">
			<div class=\"recent-work-wrap\">
				<?php if(\x24papam2 || \x24papam3) { ?>
					<img alt=\"<?php echo \x24img['alt']; ?>\" class=\"img-responsive\" src=\"<?php echo \x24img_thumb; ?>\" />
				<?php } else { ?>
					<a class=\"preview\" href=\"<?php echo \x24img['src']; ?>\" rel=\"prettyPhoto\" >
					<img alt=\"<?php echo \x24img['alt']; ?>\" class=\"img-responsive\" src=\"<?php echo \x24img_thumb; ?>\" />
					</a>
				<?php } ?>
				<?php if(\x24papam2 || \x24papam3) { ?>
					<div class=\"overlay\">
						<div class=\"recent-work-inner\">
						<?php if(\x24papam2) { ?>
							<h3><a href=\"<?php echo \x24acont; ?>\"><?php echo \x24img['title']; ?></a></h3>
						<?php }
							if(\x24papam3) { ?>
							<p><?php echo \x24img['alt']; ?></p>
						<?php } ?>
							<a class=\"preview glyphicon glyphicon-zoom-in\" href=\"<?php echo \x24img['src']; ?>\" rel=\"prettyPhoto\"></a>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php
	}
}
?>
</div>";
?>
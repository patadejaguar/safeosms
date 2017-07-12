<?php
$param = "<?php 
	global \x24args;
	\x24error_mess1= 'Sending parameters must be in array.';
	\x24error_mess2= 'Page content slug(s) is required.';
	if(is_array(\x24args)) {
		if(isset(\x24args[0]) && !empty(\x24args[0])) { \x24papam1=\x24args[0]; }
		else { \x24papam1='accordion1'; }
		if(isset(\x24args[1]) && !empty(\x24args[1]) ) {
			\x24papam2=\x24args[1];
			if(!is_array(\x24args[1])) {
				\x24papam2=array(\x24args[1]);
				if( strstr( trim(\x24args[1]), ' ' ) ) {
					\x24papam2=explode(' ', trim(\x24args[1]));
				}
			}
		}
		else {  echo '<script type=\"text/javascript\">  alert(\"'.\x24error_mess2.'\"); </script>'; return; }
		if(isset(\x24args[2]) && !empty(\x24args[2])) \x24papam3=\x24args[2];
		else  \x24papam3=1;
		if(isset(\x24args[3]) && !empty(\x24args[3]) && is_numeric(\x24args[3])) \x24papam4=\x24args[3];
		else  \x24papam4='';
	}
	else { echo '<script type=\"text/javascript\">  alert(\"'.\x24error_mess1.'\"); </script>'; return; }
?>
<div class=\"accordion\">
       <div class=\"panel-group\" id=\"<?php echo \x24papam1; ?>\">
<?php
    global \x24content;
	\x24nmr=1;
    if(\x24papam3=='all') \x24active=1;
	if(\x24papam3=='none') \x24active=0;
    foreach (\x24papam2 as \x24ablock) {
        if (!function_exists('return_i18n_page_data')) {
			\x24title = getPageField(\x24ablock, 'title');
            \x24content = getPageContent(\x24ablock);
		}
		else {
			\x24tcontent = return_i18n_page_data(\x24ablock);
			\x24title = (string) \x24tcontent->title;
			\x24content = html_entity_decode( (string) \x24tcontent->content);
		} ?>
       <div class=\"panel panel-default\">
       <div class=\"panel-heading<?php echo (\x24nmr==\x24papam3?' active':'')?>\">
          <h3 class=\"panel-title\">
            <a class=\"accordion-toggle\" data-parent=\"#<?php echo \x24papam1; ?>\" data-toggle=\"collapse\" href=\"#<?php echo \x24papam1.'-'.\x24nmr; ?>\"><?php echo \x24title; ?></a>
         </h3>
      </div>
		<div class=\"panel-collapse collapse<?php echo (\x24nmr==\x24papam3 || \x24papam3=='all'?' in':'')?>\" id=\"<?php echo \x24papam1.'-'.\x24nmr; ?>\">
			<div class=\"panel-body\">
				<div class=\"media accordion-inner\">
					<?php echo (!empty(\x24papam4)?get_page_excerpt(\x24papam4, false):\x24content); ?>
				</div>
			</div>
		</div>
		</div>

<?php \x24nmr=\x24nmr+1;
	} ?>
		</div>
</div>";
?>
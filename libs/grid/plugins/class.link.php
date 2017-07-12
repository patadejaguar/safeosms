<?php
require_once('class.plugin.php');

class link_Plugin extends Grid_Plugin
{
	
	function introspect()
	{
		return(array(
			"name" 			=> "Link Column",
			"description" 	=> "Shows an URL as clickable link.",
			"author"		=> "Senza Limiti",
			"version"		=> "1.0"
			));
	}
	
	function generateContent($cell, $args)
	{
		return(
			sprintf("<a href='$cell'%s>%s</a>", 
				!empty($args["target"]) ? " target='".$args["target"]."'" : "",
				!empty($args["display"]) ? $args["display"] : $cell
				)
				);
	}
}
?>

<?php
require_once('class.plugin.php');

class image_Plugin extends Grid_Plugin
{
	
	function introspect()
	{
		return(array(
			"name" 			=> "Image Column",
			"description" 	=> "Shows a filename as image.",
			"author"		=> "Senza Limiti",
			"version"		=> "1.0"
			));
	}
	
	function generateContent($cell, $args)
	{
		$file = !empty($args["image_directory"]) ? $args["image_directory"]."/" : "";
		$file .= $cell;
		
		if(!file_exists($file))
			return($cell);
		
		list($width, $height, $type, $attr) = getimagesize($file);
		
		return(
			"<img src='$file' width=$width height=$height>"
				);
	}
}
?>

<?php
require_once('class.plugin.php');

class email_Plugin extends Grid_Plugin
{
	
	function introspect()
	{
		return(array(
			"name" 			=> "Email Column",
			"description" 	=> "Shows an email address as clickable link.",
			"author"		=> "Senza Limiti",
			"version"		=> "1.0"
			));
	}
	
	function generateContent($cell, $args = NULL)
	{
		return("<a href='mailto:$cell'>$cell</a>");
	}
}
?>

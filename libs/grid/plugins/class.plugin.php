<?php
class Grid_Plugin
{
	
	function introspect()
	{
		return(array(
			"name" 			=> "",
			"description" 	=> "",
			"author"		=> "",
			"version"		=> ""
			));
			
	}
	
	function generateContent($cell, $args = NULL)
	{
		return($cell);
	}
}
?>

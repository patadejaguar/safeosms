<?php
require_once('class.plugin.php');

class add_id_Plugin extends Grid_Plugin
{
	
	function introspect()
	{
		return(array(
			"name" 			=> "Add ID",
			"description" 	=> "Adds an ID before inserting a new row to the table.",
			"author"		=> "Senza Limiti",
			"version"		=> "1.0"
			));
	}
	
	function generateContent($column_names, $column_values)
	{
		
		$column_names[] = "user_id";
		$column_values[] = 1;
		return(array($column_names, $column_values));
	}
}
?>

<?php
require_once('class.plugin.php');

class alert_Plugin extends Grid_Plugin
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
		
		if($args["mode"] == "edit" || $args["mode"] == "insert" )
		{
			$ret = '
			<form ...>
			  <input type="text" id="'.$args["name"].'" name="'.$args["name"].'" value="'.$cell.'"  class="grid_input"/>
			  <button id="'.$args["name"].'_trigger"  class="grid_button">...</button>
			</form>
			
			<script type="text/javascript">
			  Calendar.setup(
				{
				  inputField  : "'.$args["name"].'",         // ID of the input field
				  ifFormat    : "%Y-%m-%d %k-%M-00",    // the date format
				  button      : "'.$args["name"].'_trigger",       // ID of the button
				  showsTime   : true
				}
			  );
			</script>
				';
		}
		else
			$ret = $cell;
		return($ret);
	}
}
?>

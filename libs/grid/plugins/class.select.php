<?php
require_once('class.plugin.php');

class select_Plugin extends Grid_Plugin
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
	/*			sprintf("<a href='$cell'%s>%s</a>", 
				!empty($args["target"]) ? " target='".$args["target"]."'" : "",
				!empty($args["display"]) ? $args["display"] : $cell
				)**/
		$ret = $cell;
		if(function_exists("setLog")){
			//setLog("Celda es $cell");	
		}
		if($args["mode"] == "edit" || $args["mode"] == "insert" ){
			if( class_exists("MQL")){
				$mql	= new MQL();
				if(isset($args["sql"])){
					$opts	= "";
					$qry		= $mql->getRecordset($args["sql"]);
					$rs			= $qry->fetch_array(MYSQLI_NUM);
					/*$result = $mysqli->query($query);
					$row = $result->fetch_array(MYSQLI_NUM);
					printf ("%s (%s)\n", $row[0], $row[1]);*/
					//getDataRecord($args["sql"]);
					//setLog($args["sql"]);
					foreach ($rs as $row){
						$opts	.= "<option value=\"" . $row[0] . "\">" . $row[1] . "</option>";	
					}
					if($opts != ""){
						$ret	= sprintf("<select>$opts</select>");
					}				
				}
			}
		}
		return $ret;
	}
}
?>

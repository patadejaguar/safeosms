<?php
//ini_set("display_errors", "1");

// *****************************************************************************
// *  slGrid 2.0                                                            *
// *  http://slgrid.senzalimiti.sk                                             *
// *                                                                           *
// *  Copyright (c) 2006 Senza Limiti s.r.o.                                   *
// *                                                                           *
// *  This program is free software; you can redistribute it and/or            *
// *  modify it under the terms of the GNU General Public License              *
// *  as published by the Free Software Foundation; either version 2           *
// *  of the License, or (at your option) any later version.                   *
// *                                                                           *
// *  This program is distributed in the hope that it will be useful,          *
// *  but WITHOUT ANY WARRANTY; without even the implied warranty of           *
// *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
// *  GNU General Public License for more details.                             *
// *                                                                           *
// *  For commercial licenses please contact Senza Limiti at                   *
// *  - http://www.senzalimiti.sk                                              *
// *  - info(at)senzalimiti.sk                                                 *
// *****************************************************************************

set_time_limit(0);
if(!defined("GRID_SOURCE")){ define("GRID_SOURCE", "../"); }


include("gridclasses.php"); //Include the grid engine.



session_start();
$grid_id = $_GET["grid_id"];

header('Content-Type: application/vnd.ms-excel');
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');


$table_name = "";
if ($_SESSION[$grid_id]->sql_table_override != "")
	$table_name = $_SESSION[$grid_id]->sql_table_override;
else
	$table_name = $_SESSION[$grid_id]->sql_table;

header('Content-Disposition: attachment; filename="' . $table_name . '.xls"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');


?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=<?php echo isset($charset_of_file) ? $charset_of_file : "iso-8859-1"; ?>" />
<style id="Classeur1_16681_Styles">
</style>

</head>
<body>

<div id="Classeur1_16681" align=center x:publishsource="Excel">

<table x:str border=1   style='border-collapse: collapse'>
<?php

$schema_insert = '<tr>';

foreach ($_SESSION[$grid_id]->columns as $objColumn)
{
	if ($objColumn->show == true)
	{
		if ($objColumn->user_defined_name != "")
			$column_name = $objColumn->user_defined_name;
		else
			$column_name = $objColumn->name;
		
		$schema_insert .= '<td class=xl2216681 nowrap><b>' . htmlspecialchars($column_name) . '</b></td>';
		
	} 
}
$schema_insert .= "</tr>\n";
if ($_SESSION[$grid_id]->database->Connect() == false)
{
	return "Could not connect to database...";
}

$rows = $_SESSION[$grid_id]->database->GetRows($_SESSION[$grid_id]->sql_columns, 
									$_SESSION[$grid_id]->sql_table, 
									$_SESSION[$grid_id]->sql_where,
									$_SESSION[$grid_id]->columns[$_SESSION[$grid_id]->order_column_index]->name, 
									$_SESSION[$grid_id]->columns[$_SESSION[$grid_id]->order_column_index]->is_asc_order,
									"");
										
//while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
while ($row = $_SESSION[$grid_id]->database->FetchArray($rows)) {
	$schema_insert .= '<tr>';
	
	while(list($key, $val) = each($row))
	{
		if(!isset($_SESSION[$grid_id]->user_defined_columns[$key]) || $_SESSION[$grid_id]->user_defined_columns[$key]->show != false)
            $schema_insert .= "<td class=xl2216681 nowrap> " . htmlspecialchars($val) . '</td>';
	}
	
	$schema_insert .= "</tr>\n";	
}

print($schema_insert);
    ?>
</table>
</div>
</body>
</html>

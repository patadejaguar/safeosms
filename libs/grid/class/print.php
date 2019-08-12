<?php
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
//@include_once("core/core.config.inc.php");

//define("GRID_SOURCE", "../");
if(!defined("GRID_SOURCE")){ define("GRID_SOURCE", "../"); }
include("gridclasses.php"); //Include the grid engine.

session_start();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Print Grid</title>
		
		<!-- some basic css properties to make it look ok -->
		<link href="<?php echo GRID_SOURCE; ?>css/style.css" rel="stylesheet" type="text/css">
	</head>

	<body onload="javascript:print();">
		<?php
			if (isset($_GET["grid_id"]) == true)
			{
				echo $_SESSION[$_GET["grid_id"]]->MainDivTop();
				echo $_SESSION[$_GET["grid_id"]]->CreateGrid();	
				echo $_SESSION[$_GET["grid_id"]]->MainDivBottom();
			}
		?>
	</body>
</html>

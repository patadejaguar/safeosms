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

        //Define where you have placed the grid folder.
        define("GRID_SOURCE", "");       
        
        include(GRID_SOURCE."class/gridclasses.php"); //Include the grid engine.
        
        session_start();
       
        //Define identifying name(s) to your grid(s). Must be unqiue name(s).
        $grid_id = array("grid");

        //Remember to comment the again line when publishing PHP Grid, or else PHP Grid wont remember the settings between page loads.
        unset($_SESSION["grid"]); 
        include(GRID_SOURCE."class/gridcreate.php"); //Creates grid objects.
        
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
        <head>
                <title>View Grid</title>
                <script   type="text/javascript" src="<?php print(GRID_SOURCE);?>javascript/javascript.js"></script>
                <link href="../css/grid.css" rel="stylesheet" type="text/css">
				<script type='text/javascript' src="<?php print(GRID_SOURCE);?>server.php?client=all"></script>
				<script>
				HTML_AJAX.defaultServerUrl = '<?php print(GRID_SOURCE);?>server.php';
				</script>
        </head>

        <body onmouseup="SetMouseDown(false);">

                <?php
                        // Define your grid    
                        $_SESSION["grid"]->SetDatabaseConnection("database_name", "user_name", "password");
                        $_SESSION["grid"]->SetSqlSelect('field1, field2', 'table');
						$_SESSION["grid"]->SetUniqueDatabaseColumn("id_field", false);      
						$_SESSION["grid"]->SetTitleName("slGrid");
						// End definition
						
						$_SESSION["grid"]->PrintGrid(MODE_VIEW); 
						      
                ?>

        <body> 
</html>

<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once "../core/core.deprecated.inc.php";

$oficial = elusuario($iduser);
$parent = $_POST["crpt"];
if(!$parent){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Editar Menus por Modulos Especificos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>
<fieldset>

<form name="frmmenu" method="POST" action="./frm_edit_reports.php">
<?php

$sqlMost = "SELECT aplica, CONCAT(aplica , \"(\", COUNT(idgeneral_reports), \")\") AS 'reportes' FROM general_reports GROUP BY aplica";
$cSel = new cSelect("crpt", "idrpt", $sqlMost);
$cSel->setEsSql();
echo $cSel->show();
?>
<br />
<input type="submit" value="Mostrar" />
</form>
<?php
} else {
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
                <title>Editar Estructura</title>
                <script   type="text/javascript" src="<?php print(GRID_SOURCE);?>javascript/javascript.js"></script>
                <link href="../css/grid.css" rel="stylesheet" type="text/css">
				<script type='text/javascript' src="<?php print(GRID_SOURCE);?>server.php?client=all"></script>
				<script>
				HTML_AJAX.defaultServerUrl = '<?php print(GRID_SOURCE);?>server.php';
				</script>
        </head>

<body onmouseup="SetMouseDown(false);"><div id="onGrid">

                <?php
                        // Define your grid    
                        $_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
                        //,menu_type
                        $_SESSION["grid"]->SetSqlSelect(' idgeneral_reports, descripcion_reports, idreport ', 'general_reports', " aplica='$parent' ");
						$_SESSION["grid"]->SetUniqueDatabaseColumn("idreport", false);      
						$_SESSION["grid"]->SetTitleName("Editar Reportes por Tipo");
						// End definition
						$_SESSION["grid"]->SetDatabaseColumnWidth("idgeneral_reports", 150);
						$_SESSION["grid"]->SetDatabaseColumnName("idgeneral_reports", "Archivo del reporte");
						$_SESSION["grid"]->SetDatabaseColumnWidth("descripcion_reports", 150);
						$_SESSION["grid"]->SetDatabaseColumnName("descripcion_reports", "Descripcion Corta");						
						$_SESSION["grid"]->SetMaxRowsEachPage(40);
						$_SESSION["grid"]->PrintGrid(MODE_EDIT); 
						      
						//Create the grid.	
}
?>
</div>
</fieldset>
</body>
</html>
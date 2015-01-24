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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";


$oficial 	= elusuario($iduser);
$parent 	= $_POST["cmenu"];
$txtBuscar	= $_POST["cBuscar"];

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
	<legend>Editar Menus por Modulos Especificos</legend>
<form name="frmmenu" method="POST" action="./editar_menu.frm.php">
	<table>
		<tr>
			<td>
			<?php
			
				$sqlMost 	= "SELECT idgeneral_menu, CONCAT(menu_title, ' ', idgeneral_menu) AS 'menu' FROM general_menu WHERE menu_type='parent' ORDER BY menu_title";
				$cSel 		= new cSelect("cmenu", "idmenu", $sqlMost);
				$cSel->setEsSql();
				$cSel->addEspOption("todas", "TODAS");
				$cSel->setOptionSelect("todas");
				echo $cSel->show();
			?>
			</td>
			<td>
				Buscar Texto Parecido a:</td>
			<td>
				<input type="text" id="idBuscar" name="cBuscar" />
			</td>
			<td>
				<input type="submit" value="Mostrar" />
			</td>
	
		</tr>

	</table>
</form>
</fieldset>
<?php
} else {
		include(GRID_SOURCE."class/gridclasses.php"); //Include the grid engine.

        session_start();

        //Define identifying name(s) to your grid(s). Must be unqiue name(s).
        $grid_id = array("grid");

        //Remember to comment the again line when publishing PHP Grid, or else PHP Grid wont remember the settings between page loads.
        unset($_SESSION["grid"]);
        include(GRID_SOURCE."class/gridcreate.php"); //Creates grid objects.
	$filtro1	= "";
	$filtro2	= "";
	
	if ($parent != "todas"){
		$filtro1	= " menu_parent=$parent ";
	}
	if ( $txtBuscar !=  "" ){
		$filtro2	= " ( menu_file LIKE '%$txtBuscar%' OR menu_title LIKE '%$txtBuscar%' OR menu_description LIKE '%$txtBuscar%' )";
		if ( $filtro1 != ""){
			$filtro2	= " AND " . $filtro2;
		}
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
        <head>
                <title>Editar Menu por modulos especificos</title>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                
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
                        $_SESSION["grid"]->SetSqlSelect('idgeneral_menu, menu_title, menu_file, menu_parent, menu_order', 'general_menu', " $filtro1 $filtro2 ");
						$_SESSION["grid"]->SetUniqueDatabaseColumn("idgeneral_menu", false);
						$_SESSION["grid"]->SetTitleName("Editar Menu del Sistema");
						// End definition
						$_SESSION["grid"]->SetDatabaseColumnWidth("menu_parent",30);
						$_SESSION["grid"]->SetDatabaseColumnName("menu_parent", "Sup.");
						$_SESSION["grid"]->SetDatabaseColumnWidth("menu_title",100);
						$_SESSION["grid"]->SetDatabaseColumnName("menu_title", "Titulo");
						$_SESSION["grid"]->SetDatabaseColumnWidth("menu_file",100);
						$_SESSION["grid"]->SetDatabaseColumnName("menu_file", "Archivo");
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("menu_order",50);
						$_SESSION["grid"]->SetDatabaseColumnName("menu_order", "Orden");
						
						$_SESSION["grid"]->SetMaxRowsEachPage(40);
						$_SESSION["grid"]->PrintGrid(MODE_EDIT);

						//Create the grid.
}
?>
</div>
</body>
</html>
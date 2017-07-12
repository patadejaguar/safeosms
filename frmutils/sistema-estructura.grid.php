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
$xHP		= new cHPage("TR.Editar estructura", HP_GRID);

$oficial	= elusuario($iduser);
$table 		= parametro("ctable", false, MQL_RAW); $table 		= parametro("tabla", $table, MQL_RAW);
$actualizar	= parametro("forzar", false, MQL_BOOL);
$table		= strtolower($table);

if($actualizar == true){
	$xUtil		= new cTableStructure($table);
	$command	= ($xUtil->getNumeroDeCampos() > 0) ? SYS_CERO : SYS_UNO;
	$xUtil->setStructureTableByDemand($command); //0 update
}


$xHP->setNoDefaultCSS();
$xHP->init();
// Define your grid
$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
$_SESSION["grid"]->SetSqlSelect('index_struct, campo, valor, tipo, longitud, descripcion, titulo, control, sql_select, order_index', 'general_structure', " tabla='$table' ");
$_SESSION["grid"]->SetUniqueDatabaseColumn("index_struct", false);
$_SESSION["grid"]->SetTitleName("Editar Estructura de $table");
// End definition
$_SESSION["grid"]->SetDatabaseColumnName("longitud", "Tama&ntilde;o");

$_SESSION["grid"]->SetDatabaseColumnName("order_index", "Orden");

$_SESSION["grid"]->SetDatabaseColumnWidth("campo", 280);
$_SESSION["grid"]->SetDatabaseColumnWidth("valor", 280);

$_SESSION["grid"]->SetDatabaseColumnWidth("longitud", 120);
$_SESSION["grid"]->SetDatabaseColumnWidth("order_index", 80);
						
$_SESSION["grid"]->SetDatabaseColumnWidth("tipo", 80);
						
$_SESSION["grid"]->SetDatabaseColumnWidth("control", 80);
$_SESSION["grid"]->SetEditModeAdd(false);
						
$_SESSION["grid"]->SetMaxRowsEachPage(30);
$_SESSION["grid"]->PrintGrid(MODE_EDIT);

$xHP->fin();
?>
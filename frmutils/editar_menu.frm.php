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
$xHP		= new cHPage("TR.Editar Menu", HP_FORM);
$parent 	= parametro("cmenu", 0, MQL_INT);
$txtBuscar	= parametro("cBuscar", "", MQL_RAW);

if($parent <=0){
	$xHP->init();
	$xFRM		= new cHForm("frmeditmenu", "editar_menu.frm.php");
	
	$xFRM->addEnviar();
	
	$sqlMost 	= "SELECT idgeneral_menu, CONCAT(menu_title, ' ', idgeneral_menu) AS 'menu' FROM general_menu WHERE menu_type='parent' ORDER BY menu_title";
	$cSel 		= new cSelect("cmenu", "idmenu", $sqlMost);
	$cSel->setEsSql();
	$cSel->addEspOption("todas", "TODAS");
	$cSel->setOptionSelect("todas");
	$xFRM->addHElem($cSel->show());
	$xFRM->OText("txrbuscar", "", "TR.Buscar");
	
	echo $xFRM->get(true);
		
} else {
	$xHP		= new cHPage("TR.Editar Menu", HP_GRID);
	$xHP->setNoDefaultCSS();
	$xHP->init();
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
	// Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//,menu_type
	$_SESSION["grid"]->SetSqlSelect('idgeneral_menu, menu_title, menu_file, menu_parent, menu_order', 'general_menu', " $filtro1 $filtro2 ");
	$_SESSION["grid"]->SetUniqueDatabaseColumn("idgeneral_menu", false);
	$_SESSION["grid"]->SetTitleName("Editar Menu del Sistema");
	// End definition
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_parent",120);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_parent", "Sup.");
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_title",300);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_title", "Titulo");
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_file",300);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_file", "Archivo");
	
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_order",80);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_order", "Orden");
	
	$_SESSION["grid"]->SetMaxRowsEachPage(40);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);
	
	
}
$xHP->fin();
?>
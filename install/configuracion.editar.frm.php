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

$oficial 	= elusuario($iduser);
$parent 	= parametro("cmenu", false, MQL_RAW);
$parent 	= parametro("tipo", $parent, MQL_RAW);
$txtBuscar	= parametro("idbuscar", "", MQL_RAW);
$txtBuscar	= parametro("buscar", $txtBuscar, MQL_RAW);

$xHP		= new cHPage("TR.Editar Configuracion del Sistema", HP_GRID);



if($parent == false AND $txtBuscar == ""){
	$xHP		= new cHPage("TR.Editar Configuracion del Sistema", HP_FORM);
	$xHP->init();

	$xFRM		= new cHForm("frmeditar", "configuracion.editar.frm.php");
	$xFRM->setTitle($xHP->getTitle());
	
	$sqlMost 	= "SELECT tipo, CONCAT('(' , COUNT(nombre_del_parametro), ') ', tipo ) AS 'conceptos'
					    FROM entidad_configuracion
					GROUP BY tipo
					ORDER BY tipo ";
	$cSel 		= new cSelect("cmenu", "cmenu", $sqlMost);
	$cSel->setEsSql();
	$cSel->addEspOption(SYS_TODAS);
	$cSel->setOptionSelect(SYS_TODAS);
	
	$xFRM->addHElem( $cSel->get("TR.Parametro", true) );
	$xFRM->OText("idbuscar", "", "TR.Buscar Texto");
	$xFRM->addSubmit();
	
	echo $xFRM->get();

} else {
		$filtro1			= "";
		$filtro2			= "";
		
		$filtro1			= ($parent != SYS_TODAS AND $parent != false AND $parent != "") ? " tipo = '$parent' " : "";

		if ( $txtBuscar !=  "" ){
			$filtro2		.= (trim($filtro1) == "") ? "" : " AND "; 
			$filtro2		.= " ( nombre_del_parametro LIKE '%$txtBuscar%' OR descripcion_del_parametro LIKE '%$txtBuscar%' ) ";
		}
		$xHP->setNoDefaultCSS();
		echo $xHP->getHeader(true);
		//setLog("$filtro1 $filtro2");
		echo '<body onmouseup="SetMouseDown(false);" >';
                        // Define your grid
                        $_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
                        //,menu_type
                        $_SESSION["grid"]->SetSqlSelect('nombre_del_parametro,
							UCASE(nombre_del_parametro) AS "parametro",
							descripcion_del_parametro,
							valor_del_parametro ',
							'entidad_configuracion', trim("$filtro1 $filtro2"));
						$_SESSION["grid"]->SetUniqueDatabaseColumn("nombre_del_parametro", false);
						$_SESSION["grid"]->SetTitleName("Editar Configuracion de la Entidad de la Seccion " . strtoupper($parent) );
						
						// End definition

						$_SESSION["grid"]->SetDatabaseColumnWidth("parametro",400);
						$_SESSION["grid"]->SetDatabaseColumnName("parametro", "Parametro");
						$_SESSION["grid"]->SetDatabaseColumnEditable("parametro", false);
						
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("descripcion_del_parametror",400);
						$_SESSION["grid"]->SetDatabaseColumnName("descripcion_del_parametro", "Descripcion");
						$_SESSION["grid"]->SetDatabaseColumnEditable("descripcion_del_parametro", false);
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("valor_del_parametror",250);
						$_SESSION["grid"]->SetDatabaseColumnName("valor_del_parametro", "Valor");
												
						$_SESSION["grid"]->SetMaxRowsEachPage(40);
						$_SESSION["grid"]->PrintGrid(MODE_EDIT);

						//Create the grid.
}
$xHP->fin();
?>
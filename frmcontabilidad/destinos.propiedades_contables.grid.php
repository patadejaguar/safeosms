<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("Propiedades Contables de Destinos de Credito", HP_GRID);
$xHP->setIncludes();


$parent 	= ( isset($_POST["c"]) ) ? $_POST["c"] : false;


	$filtro1			= "";
	$filtro2			= "";
		

	//HTML Object Init	
	$xHP->addJsFile(GRID_SOURCE . "javascript/javascript.js");
	$xHP->addCSS("../css/grid.css");
	$xHP->addJsFile(GRID_SOURCE . "server.php?client=all");
	$xHP->addHSnip("<script> HTML_AJAX.defaultServerUrl = '" . GRID_SOURCE. "server.php';	</script>");

	$xHP->setNoDefaultCSS();
	
	echo $xHP->getHeader(true);
	//HTML Object END
	
	echo '<body onmouseup="SetMouseDown(false);" ><div id="onGrid">';
        // Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//Propiedades del GRID
	$mGridTitulo		= "Destinos de Credito.- Propiedades Contables";
	$mGridKeyField		= "idcreditos_destinos";	//Nombre del Campo Unico
	$mGridKeyEdit		= false;					//Es editable el Campo
	$mGridTable			= "creditos_destinos";	//Nombre de la tabla
	$mGridSQL			= "idcreditos_destinos, descripcion_destinos, capital_vencido_renovado, capital_vencido_reestructurado, capital_vencido_normal, capital_vigente_renovado, capital_vigente_reestructurado, capital_vigente_normal, interes_vencido_renovado, interes_vencido_reestructurado, interes_vencido_normal, interes_vigente_renovado, interes_vigente_reestructurado, interes_vigente_normal, interes_cobrado, moratorio_cobrado";
	$mGridWhere			= "";
	//layout: [Campo] => Titulo, Editable, Tamaño
	$mGridProp			= array();
	//Obtiene el Grid de la Tabla de general_description
	if( $mGridTable != "" ){
		$xTs			= new cTableStructure($mGridTable);
		$xAF			= explode(",", $mGridSQL);
		
		foreach ($xAF as $key => $value) {
			$DField							= $xTs->getInfoField( trim($value) );
			$mGridProp[ $DField["campo"] ]	=  $DField["titulo"]  .",true," . $DField["longitud"] ; 
		}
		unset($xAF, $key, $value);
	}
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	$_SESSION["grid"]->SetEditModeAdd(false);
	
	$_SESSION["grid"]->SetDatabaseColumnEditable("descripcion_destino", false);
	$_SESSION["grid"]->SetDatabaseColumnName("descripcion_destino", "Tipo de Destino");
	//interes_vencido_renovado, interes_vencido_reestructurado, interes_vencido_normal, interes_vigente_renovado, interes_vigente_reestructurado, interes_vigente_normal


	$_SESSION["grid"]->SetDatabaseColumnName("interes_cobrado", "Interes Cobrado");
	$_SESSION["grid"]->SetDatabaseColumnName("moratorio_cobrado", "Moratorios Cobrados");	
	
	$_SESSION["grid"]->SetDatabaseColumnName("capital_vencido_normal", "Capital Vigente");
	$_SESSION["grid"]->SetDatabaseColumnName("capital_vigente_normal", "Capital Vencido");

	$_SESSION["grid"]->SetDatabaseColumnName("capital_vigente_reestructurado", "Cap. Reest. Vigente");
	$_SESSION["grid"]->SetDatabaseColumnName("capital_vencido_reestructurado", "Cap. Reest. Vencido");
	
	$_SESSION["grid"]->SetDatabaseColumnName("capital_vigente_renovado", "Cap. Renov. Vigente");
	$_SESSION["grid"]->SetDatabaseColumnName("capital_vencido_renovado", "Cap. Renov. Vencido");
	
	$_SESSION["grid"]->SetDatabaseColumnName("interes_vencido_normal", "Interes Vigente");
	$_SESSION["grid"]->SetDatabaseColumnName("interes_vigente_normal", "Interes Vencido");

	$_SESSION["grid"]->SetDatabaseColumnName("interes_vigente_reestructurado", "Int. Reest. Vigentes");
	$_SESSION["grid"]->SetDatabaseColumnName("interes_vencido_reestructurado", "Int. Reest. Vencidos");
	
	$_SESSION["grid"]->SetDatabaseColumnName("interes_vigente_renovado", "Int. Renov Vigente");
	$_SESSION["grid"]->SetDatabaseColumnName("interes_vencido_renovado", "Int. Renov Vencidos");
	


	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(25);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

	//Create the grid.

echo "</div></body>";
echo $xHP->end();
?>
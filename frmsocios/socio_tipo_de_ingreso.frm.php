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
$xHP		= new cHPage("TR.Catalogo de Tipo de Ingreso", HP_GRID);
$xF			= new cFecha();

	$xTabla				= new cSocios_tipoingreso();
	$filtro1			= "";
	$filtro2			= "";
	
	$xHP->setNoDefaultCSS();
	echo $xHP->getHeader(true);
	//HTML Object END
	echo '<body onmouseup="SetMouseDown(false);" ><div id="onGrid">';
    //Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//Propiedades del GRID
	$mGridTitulo		= $xHP->getTitle();
	$mGridKeyField		= $xTabla->getKey();	//Nombre del Campo Unico
	$mGridKeyEdit		= true;					//Es editable el Campo
	$mGridTable			= $xTabla->get();	//Nombre de la tabla
	$mGridSQL			= $xTabla->query()->getListaDeCampos();//  "*"; //$xTabla->query()->getCampos();
	$mGridWhere			= "";

	$mGridProp			= array(
				"idsocios_tipoingreso" => "clave,true,4",
				"descripcion_tipoingreso" => "nombre,true,45",
				"descripcion_detallada" => "descripcion,true,200",
				"parte_social" => "parte social,true,25",
				"parte_permanente" => "parte permanente,true,25",
				"estado" => "estado,true,4",
				"nivel_de_riesgo" => "nivel de riesgo,true,4"
						);
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	$_SESSION["grid"]->SetEditModeAdd(false);
	//$_SESSION["grid"]->SetEditModeDelete(false);
	//===========================================================================================================					
		foreach ($mGridProp as $key => $value) {
			$mVals		= explode(",", $value, 10);
			
			if ( isset($mVals[0]) ) {
				$_SESSION["grid"]->SetDatabaseColumnName($key, $mVals[0]);
			}
			
			if ( isset($mVals[1]) ) {
				$_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]);
			}
			if ( isset($mVals[2]) ) {
				$_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]);
			}	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(25);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

echo $xHP->end();
?>

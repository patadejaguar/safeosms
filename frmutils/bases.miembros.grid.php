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
$xHP		= new cHPage("TR.INTEGRANTES de Bases", HP_GRID);
$xF			= new cFecha();
$xL			= new cLang();
$xTabla		= new cEacp_config_bases_de_integracion_miembros();
	
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$xHP->setNoDefaultCSS();
echo $xHP->getHeader(true);
//HTML Object END
echo '<body onmouseup="SetMouseDown(false);" >';
//Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//Propiedades del GRID
	$mGridTitulo		= $xHP->getTitle();
	$mGridKeyField		= $xTabla->getKey();	//Nombre del Campo Unico
	$mGridKeyEdit		= true;					//Es editable el Campo
	$mGridTable			= $xTabla->get();	//Nombre de la tabla
	$mGridSQL			= $xTabla->query()->getListaDeCampos();//  "*"; //$xTabla->query()->getCampos();
	$mGridWhere			= "`codigo_de_base`=$clave ";

	$mGridProp			= array(
						"ideacp_config_bases_de_integracion_miembros" => "TR.CLAVE,true,100",
						"codigo_de_base" => "TR.BASE,false,100",
						"miembro" => "TR.INTEGRANTE,true,100",
						"afectacion" => "TR.AFECTACION,true,100",
						"descripcion_de_la_relacion" => "TR.descripcion,true,450",
						"subclasificacion" => "TR.clasificacion,true,100"
						);
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	$_SESSION["grid"]->SetEditModeAdd(true);
	//$_SESSION["grid"]->SetEditModeDelete(false);
	//===========================================================================================================					
		foreach ($mGridProp as $key => $value) {
			$mVals		= explode(",", $value, 3);
			if ( isset($mVals[0]) ){ $_SESSION["grid"]->SetDatabaseColumnName($key, $xL->getT($mVals[0]));	}
			if ( isset($mVals[1]) ){ $_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]); }
			if ( isset($mVals[2]) ){ $_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]); }	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(35);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

echo $xHP->fin();
?>

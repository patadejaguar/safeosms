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
$xHP		= new cHPage("TR.Editar xclasificacion", HP_GRID);
$xF			= new cFecha();
$xL			= new cLang();

	$xTabla				= new cPersonas_xclasificacion();
	
	$xHP->setNoDefaultCSS();
	//$xHP->addCSS("../css/formoid/formoid-default.css");
	
	echo $xHP->getHeader(true);
	//HTML Object END
	echo '<body onmouseup="SetMouseDown(false);" >';
	echo "<fieldset class=\"fieldform\"><!-- <legend class=\"title title-frm\">" . $xHP->getTitle() . "</legend> -->";
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
							"idpersonas_xclasificacion" => "TR.xclasificacion,true,100",
							"descripcion_xclasificacion" => "TR.descripcion,true,200",
							"xclasificacion_etiquetas" => "TR.etiquetas,true,300",
						);
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	$_SESSION["grid"]->SetEditModeAdd(true);
	//$_SESSION["grid"]->SetEditModeDelete(false);
	//===========================================================================================================					
		foreach ($mGridProp as $key => $value) {
			$mVals		= explode(",", $value, 10);
			if ( isset($mVals[0]) ){ $_SESSION["grid"]->SetDatabaseColumnName($key, $xL->getT($mVals[0]));	}
			if ( isset($mVals[1]) ) { $_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]); }
			if ( isset($mVals[2]) ) { $_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]); }	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(25);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);
	
	echo "</fieldset>";
	
echo $xHP->fin();
?>
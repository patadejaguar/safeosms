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
$xHP		= new cHPage("TR.DESTINO_DE_CREDITO", HP_GRID);
$xF			= new cFecha();
$xL			= new cLang();
$xTabla		= new cCreditos_destinos();
	
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
	$mGridWhere			= "";
	$mGridSQL			= "idcreditos_destinos,descripcion_destinos,destino_credito,tasa_de_iva";
	
	$mGridProp			= array(
	"idcreditos_destinos" 				=> "TR.Clave,true,20",
	"descripcion_destinos" 				=> "TR.NOMBRE,true,250",
	"destino_credito" 					=> "TR.CLAVE,true,20",
	"capital_vencido_renovado" 			=> "TR.CAPITAL_REN_VENC,true,20",
	"capital_vencido_reestructurado" 	=> "TR.CAPITAL_REEST_VENC,true,20",
	"capital_vencido_normal" 			=> "TR.CAPITAL_VENC_NORMAL,true,20",
	"capital_vigente_renovado" 			=> "TR.CAPITAL_REN_VIG,true,20",
	"capital_vigente_reestructurado"	=> "TR.CAPITAL_REEST_VIG,true,20",
	"capital_vigente_normal" 			=> "TR.CAPITAL_VIG_NORMAL,true,120",
			
	"interes_cobrado" 					=> "TR.INTERES_NORMAL_COBRADO,true,120",
	"moratorio_cobrado" 				=> "TR.INTERES_MORA_COBRADO,true,120",
	"interes_vencido_renovado" 			=> "TR.INTERES_RENOVADO_VENC,true,120",
	"interes_vencido_reestructurado"	=> "TR.INTERES_REEST_VENC,true,120",
	"interes_vencido_normal" 			=> "TR.INTERES_VENCIDO,true,120",
	"interes_vigente_renovado" 			=> "TR.INTERES_RENOVADO,true,120",
	"interes_vigente_reestructurado" 	=> "TR.INTERES_REEST,true,120",
	"interes_vigente_normal" 			=> "TR.INTERES_VIGENTE,true,20",
			
	"tasa_de_iva" 						=> "TR.IMPUESTO_AL_CONSUMO,true,25",
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
			if ( isset($mVals[1]) ) { $_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]); }
			if ( isset($mVals[2]) ) { $_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]); }	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(25);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

echo $xHP->fin();
?>
<?php
/**
 * Editar Envios de Cobranza por Credito Individual
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package empresas
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
$xHP		= new cHPage("TR.ENVIOS A EMPRESA", HP_GRID);
$xF			= new cFecha();
$xL			= new cLang();
$xTabla		= new cEmpresas_cobranza();
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

$xHP->setNoDefaultCSS();
echo $xHP->getHeader(true);
//HTML Object END
echo '<body onmouseup="SetMouseDown(false);" >';
//Define your grid
$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
//Propiedades del GRID
$mGridTitulo		= $xHP->getTitle();
$mGridKeyField		= $xTabla->getKey();	//Nombre del Campo Unico
$mGridKeyEdit		= false;					//Es editable el Campo
$mGridTable			= $xTabla->get();	//Nombre de la tabla
//,clave_de_nomina,clave_de_credito, ,tiempocobro
$mGridSQL			= "idempresas_cobranza,parcialidad,monto_enviado,saldo_inicial,estado,recibo,observaciones"; //$xTabla->query()->getListaDeCampos();//  "*"; //$xTabla->query()->getCampos();
$mGridWhere			= "clave_de_credito=$credito";

$mGridProp			= array(
	"idempresas_cobranza" => "TR.CLAVE,true,80",
	"clave_de_nomina" => "TR.NOMINA,true,80",
	"clave_de_credito" => "CREDITO,true,120",
	"parcialidad" => "TR.LETRA,true,60",
	"monto_enviado" => "TR.MONTO,true,100",
	"observaciones" => "observaciones,true,150",
	"saldo_inicial" => "TR.SALDO_INICIAL,true,120",
	"estado" => "estado,true,60",
	"recibo" => "recibo,true,80",
	"tiempocobro" => "tiempocobro,true,80"
);
//===========================================================================================================

$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
$_SESSION["grid"]->SetTitleName($mGridTitulo);
$_SESSION["grid"]->SetEditModeAdd(false);
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
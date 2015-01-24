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
//=====================================================================================================
$xHP				= new cHPage("TR.Reporte de Catalogo de Cuentas Contables", HP_REPORT);
$xQl				= new MQL();
$xLi				= new cSQLListas();
	
//=====================================================================================================
$fecha_inicial 				= parametro("on", FECHA_INICIO_OPERACIONES_SISTEMA);
$fecha_final 				= parametro("off", fechasys());
$afectables					= parametro("afectables", false, MQL_BOOL);
$tipo_cuentas 				= parametro("f1", SYS_TODAS);			//Tipo de Cuentas: Activo Acreedoras
$niveles 					= parametro("f2", SYS_TODAS);			//Niveles: mayor titulo
$out 						= parametro("out", SYS_DEFAULT);
$Cuenta 					= parametro("for", SYS_TODAS);		//solo unica cuenta
$mostrarTodo				= parametro("mostrar", false, MQL_BOOL); //( isset($_GET["v"]) ) ? $_GET["v"] : 0;
/**
 * Paginacion
 * I.- Parte
 */
$rowLimit					= ($mostrarTodo == true ) ? 99999 : 90; //Paginas aprox
$InitRecords				= parametro("init", 0, MQL_INT);

//marca el Final de los Registros
$EndRecords					= $InitRecords + $rowLimit;
//captura el URI para manipularlo
$mURI						= $_SERVER['REQUEST_URI'];
//encapsula el patrom
$patron						=  "/init=\d*/";
if ( $InitRecords > 0){
	$mURI					= preg_replace($patron, "init=$EndRecords", $mURI);
} else {
	$mURI					.= "&init=$EndRecords";
}
//==================================================================================

/*if($unica_cuenta == 1){
	$dcta 			= getInfoCatalogo($esa_cuenta);
	$nivel 			= $dcta["digitoagrupador"];
	$dicat 			= explode("@", CAT_LEN);
	$largo 			= $dicat[$nivel];
	$pcta 			= getCuentaCompleta($esa_cuenta);
	$pcta 			= substr($pcta, 0, $largo);
	$sqlWHERE 		.= " AND `contable_catalogo`.`numero` LIKE '$pcta%' ";
}*/
$setSql				= $xLi->getListadoDeCuentasContables($Cuenta, $niveles, $tipo_cuentas, $afectables, 0, $InitRecords, $EndRecords);
//setLog($setSql);
$xRPT				= new cReportes();
$xRPT->setTitle($xHP->getTitle() );

$xRPT->addCSSFiles("../css/catalogo.flags.css");

$xRPT->setOut($out);

$cTbl = new cTabla($setSql);
//$xRPT->addContent($xHP->init());
$xRPT->addContent( $cTbl->Show("", true, "idcatalogocontable") );
$xRPT->addContent($xHP->fin());

if($mostrarTodo == false){ $xRPT->setToPagination($EndRecords); }

echo $xRPT->render(true);


?>
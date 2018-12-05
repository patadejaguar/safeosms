<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP		= new cHPage("TR.EXPEDIENTE DE PERSONAS", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();
$xLi		= new cSQLListas();
$xLog		= new cCoreLog();

$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$idsocio		= $persona;
/*
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$TipoDePago		= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW); $TipoDePago	= parametro("pago", $TipoDePago, MQL_RAW);
$TipoDeRecibo	= parametro("tipoderecibo", 0, MQL_INT); $TipoDeRecibo = parametro("tiporecibo", $TipoDeRecibo, MQL_INT);

$cajero 		= parametro("f3", 0, MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT); $operacion = parametro("tipodeoperacion", $operacion, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);

$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
*/
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT * FROM creditos LIMIT 0,100";
$titulo			= $xHP->getTitle();
$archivo		= $xHP->getTitle();




$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte

$body		= $xRPT->getEncabezado($xHP->getTitle());
$xRPT->setBodyMail($body);
$xRPT->addContent($body);


//$subf 		= ($todo == true) ? "" : " AND estatus_mvto=30";
$subf		= "";
// REPORTES DE SOCIOS
$cSocio			= new cSocio($idsocio);
$cSocio->init();
$xRPT->addContent( $cSocio->getFicha(true) );


$cTae		= new cTabla($xLi->getListadoDeActividadesEconomicas($idsocio));$cTae->setTdClassByType();$cTae->setTipoSalida($out);
$cnt		= $cTae->Show("TR.ACTIVIDAD_ECONOMICA");
if($cTae->getRowCount() > 0){
	$xRPT->addContent($cnt);
}
//
$cTpr		= new cTabla($xLi->getListadoDeRelaciones($idsocio));$cTpr->setTdClassByType();$cTpr->setTipoSalida($out);
$cnt		= $cTpr->Show(PERSONAS_TITULO_PARTES);
if($cTpr->getRowCount() > 0){
	$xRPT->addContent($cnt);
}
//

$sqlcred 	= $xLi->getListadoDeCreditos($idsocio, true);
$cTcred		= new cTabla($sqlcred);$cTcred->setTipoSalida($out);$cTcred->setTdClassByType();
$cnt		= $cTcred->Show("TR.Creditos");
if($cTcred->getRowCount() > 0){
	$xRPT->addContent($cnt);
}

//
$cTgar		= new cTabla($xLi->getListadoDeGarantiasReales($idsocio) );
$cnt		= $cTgar->Show("TR.GARANTIAS DE CREDITOS");
if($cTgar->getRowCount() > 0){
	$xRPT->addContent($cnt);
}
//
if(MODULO_CAPTACION_ACTIVADO == true){
	$cTcta		= new cTabla($xLi->getListadoDeCuentasDeCapt($idsocio));$cTcta->setTipoSalida($out);$cTcta->setTdClassByType();
	$cnt		= $cTcta->Show("TR.CUENTAS DE CAPTACION");
	if($cTcta->getRowCount()>0){
		$xRPT->addContent($cnt);
	}
}




$cTbl = new cTabla($xLi->getListadoDeRecibosEmitidos($idsocio) ); $cTbl->setTipoSalida($out); $cTbl->setTdClassByType();
$cnt		= $cTbl->Show("TR.Recibos");
if($cTbl->getRowCount()>0){
	$xRPT->addContent($cnt);
}
// MOVIMIENTOS

$cTi		= new cTabla($xLi->getListadoDeOperaciones($idsocio) );$cTi->setTdClassByType(); $cTi->setTipoSalida($out);
$cnt		= $cTi->Show("TR.MOVIMIENTOS GENERALES");
if($cTi->getRowCount()>0){
	$xRPT->addContent($cnt);
}
// NOTAS




$cTi		= new cTabla($xLi->getListadoDeNotas($idsocio)); $cTi->setTdClassByType(); $cTi->setTipoSalida($out);
$cnt		= $cTi->Show("TR.NOTAS");
if($cTi->getRowCount()>0){
	$xRPT->addContent($cnt);
}

//========================= Eventos


$xTTE	= new cTabla($xLog->getListadoDeEventosSQL($idsocio), 0);
if(MODO_DEBUG == false){
	$xTTE->setOmitidos("texto");$xTTE->setOmitidos("tipo");
}
$hhe	= $xTTE->Show("", true, "idlistaeventos");
if($xTTE->getRowCount()>0){
	$xRPT->addContent($hhe);
}
/*

$xRPT->setProcessSQL();*/

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>
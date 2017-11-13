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
$xHP		= new cHPage("TR.REPORTE DE RECIBO", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();


$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT * FROM socios LIMIT 0,100";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
//$xT		= new cTabla($sql, 2);
//$xT->setTipoSalida($out);

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);

$xRec		= new cReciboDeOperacion(false, false, $recibo);

if( $xRec->init() == true){
	$xRPT->addContent( $xRec->getFicha(true, "", true) );
	$cEdit		= new cTabla($xL->getListadoDeOperaciones("", "", $recibo));
	$documento	= $xRec->getCodigoDeDocumento();
	$cEdit->setTipoSalida($out);
	$cEdit->setFootSum(array(8 => "monto"));
	$xRPT->addContent($cEdit->Show("TR.Operaciones") );
	
	$cTes		= new cTabla($xL->getListadoDeOperacionesDeTesoreria("", "", $recibo));
	$cTes->setTipoSalida($out);
	$xRPT->addContent( $cTes->Show("TR.Tesoreria") );
	
	$cBan		= new cTabla($xL->getListadoDeOperacionesBancarias("", "", "", false, false, " AND `bancos_operaciones`.`recibo_relacionado` = $recibo "));
	$cBan->setTipoSalida($out);
	$xRPT->addContent( $cBan->Show("TR.Bancos") );
	
	if( getEsModuloMostrado(USUARIO_TIPO_CONTABLE) == true ){
		$xTbl	= new cTabla($xL->getListadoDePrepoliza($recibo));
		$xTbl->setTipoSalida($out);
		$xRPT->addContent($xTbl->Show("TR.Forma Poliza"));
		//poliza relacionada
			
		$xTbl	= new cTabla($xL->getListadoDePolizasContables(false, false, false,false,false, " AND (`recibo_relacionado`=$recibo) "));
		$xTbl->setTipoSalida($out);
		$xRPT->addContent($xTbl->Show("TR.Poliza"));
		//factura XML
		//$xRec->getFactura(false, OUT_RXML);
		//$xDo	= new cDocumentos();
	}
	$xT4	= new cTabla( $xL->getListadoDeEventos("", "", "", "", "", $recibo));
	$xT4->setTipoSalida($out);
	$xRPT->addContent($xT4->Show("TR.Eventos"));
}


//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show( ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

exit;


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
include_once( "../core/entidad.datos.php");
include_once( "../core/core.deprecated.inc.php");
include_once( "../core/core.fechas.inc.php");
include_once( "../libs/sql.inc.php");
include_once( "../core/core.config.inc.php");
include_once "../reports/PHPReportMaker.php";

$oficial = elusuario($iduser);
//=====================================================================================================

$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$f1					= $_GET["f1"];
$recibo 			= parametro("f10", false, MQL_INT);
$recibo				= parametro("recibo", $recibo, MQL_INT);
$input 				= parametro("out", SYS_DEFAULT, MQL_RAW);

  /******************************************************************************
	*																										*
	*	Use this file to see a sample of PHPReports.											*
	*	Please check the PDF manual for see how to use it.									*
	*	It need to be placed on a directory reached by the web server.					*
	*																										*
	******************************************************************************/

$setSql = "SELECT operaciones_recibos.idoperaciones_recibos, operaciones_recibos.recibo_fiscal, operaciones_recibostipo.descripcion_recibostipo AS 'tipo_de_recibo', `operaciones_recibos`.`observacion_recibo` AS `observaciones`, ";
$setSql .= " operaciones_recibos.fecha_operacion AS 'fecha', operaciones_recibos.numero_socio, ";
$setSql .= " CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS 'nombre_completo', ";
$setSql .= " operaciones_recibos.docto_afectado AS 'documento', operaciones_recibos.total_operacion AS 'total', operaciones_recibos.tipo_pago AS 'tipo_de_pago', ";
$setSql .= " operaciones_tipos.descripcion_operacion AS 'tipo_de_operacion', operaciones_mvtosestatus.descripcion_mvtosestatus AS 'estatus', ";
$setSql .= " operaciones_mvtos.afectacion_real AS 'monto', operaciones_mvtos.fecha_afectacion AS 'fecha_de_afectacion', ";
$setSql .= " operaciones_mvtos.fecha_vcto AS 'fecha_de_vencimiento', operaciones_mvtos.periodo_socio AS 'periodo_del_socio', ";
$setSql .= " operaciones_mvtos.docto_neutralizador, operaciones_mvtos.saldo_actual FROM operaciones_recibos,operaciones_recibostipo,  ";
$setSql .= " socios_general, operaciones_mvtos, operaciones_tipos, operaciones_mvtosestatus ";
$setSql .= " WHERE socios_general.codigo=operaciones_recibos.numero_socio AND  operaciones_mvtos.recibo_afectado=operaciones_recibos.idoperaciones_recibos ";
$setSql .= " AND operaciones_tipos.idoperaciones_tipos=operaciones_mvtos.tipo_operacion AND operaciones_recibostipo.idoperaciones_recibostipo=operaciones_recibos.tipo_docto ";
$setSql .= " AND operaciones_mvtosestatus.idoperaciones_mvtosestatus=operaciones_mvtos.estatus_mvto ";
$setSql .= " AND operaciones_recibos.idoperaciones_recibos=$recibo ";
$setSql .= " ORDER BY operaciones_recibos.idoperaciones_recibos, operaciones_recibos.fecha_operacion, ";
$setSql .= " operaciones_mvtos.idoperaciones_mvtos ";

if ($input!=OUT_EXCEL) {
//echo $setSql;
		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report22.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
	sqltabla($setSql, "", "fieldnames");
}
?>
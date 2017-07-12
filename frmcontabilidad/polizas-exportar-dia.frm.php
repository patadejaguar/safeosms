<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();


//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha 		= parametro("idfecha", false, MQL_DATE);

$xHP->init();
//Variables Banmedica
$SP			= "|";
$IDCIA		= "02";
$IDGPO		= "04";
$DEFCOB		= "9999";//Cobrador
$SCLIENTES	= "";//Cadena de CLientes
$SCREDITOS	= "";//Cadena de creditos
$SMVTOS		= "";//Cadena de Movimientos
$IDMONEDA01	= "P";
$IDPZO		= "30";
$IDCC		= "00";
$IDTDOC		= "RT"; //Tipo de documento
$IDTREF		= "FC"; //Tipo de referencia
$IDREFDEB	= "1029606"; //Numero de referencia del Debito?
$IDDATE		= fechasys();
$xFRM		= new cHForm("frm", "./");
$msg		= "";
$otros		= " AND (`operaciones_recibostipo`.`tipo_poliza_generada` != " . FALLBACK_TIPO_DE_POLIZA . ") AND `operaciones_recibos`.`total_operacion` > 0 ";
$sql		= $xLi->getListadoDeRecibos("", "", "", $fecha, $fecha, $otros);
$rs			= $xQL->getDataRecord($sql);
//setLog($sql);
$aCreditos	= array();
$aPersonas	= array();
$aRecibos	= array();

$xls		= new cHExcelNew("Exportacion-contable");

$aRecibos[]	= "no_cia|centro|tipo_doc|periodo|ruta|no_docu|grupo|no_cliente|fecha|fecha_digitacion|no_agente|cobrador|subtotal|exento|m_original|saldo|total_ref|estado|total_db|total_cr|concepto|origen|ano|mes|semana|no_fisico|serie_fisico|ind_estado_vencido|fecha_documento|anulado|numero_ctrl|cod_diario|moneda|tipo_cambio|detalle|autoriza\r\n";
$SMVTOS		.= "no_cia|centro|tipo_doc|periodo|ruta|no_docu|grupo|no_cliente|fecha|fecha_digitacion|no_agente|cobrador|subtotal|exento|m_original|saldo|total_ref|estado|total_db|total_cr|concepto|origen|ano|mes|semana|no_fisico|serie_fisico|ind_estado_vencido|fecha_documento|anulado|numero_ctrl|cod_diario|moneda|tipo_cambio|detalle|autoriza\r\n";		
$aCreditos[]	= "no_cia|tipo_doc|no_docu|tipo_refe|no_refe|fecha_vence|monto|descuento_pp|ind_procesado|fec_aplic|ano|mes|monto_refe|moneda_refe\r\n";
$aPersonas[]	= "no_cia|grupo|no_cliente|nombre|nombre_comercial|direccion|fecha_ingre|cobrador|moneda_limite|limite_credi|telefono|telefono2|plazo|cedula|centro|estado\r\n";


foreach ($rs as $rw){
	$credito	= $rw["documento"];
	$persona	= $rw["socio"];
	$recibo		= $rw["numero"];
	//Creditos
	if(!isset($aCreditos[$credito])){
		$xCred		= new cCredito($credito);
		if($xCred->init() == true){
			$aCreditos[$credito]	= "$IDCIA" . $SP . $IDTDOC . $SP . "$credito" . $IDTREF . $SP . $IDREFDEB. $SP . $xF->getFechaMX($xCred->getFechaDeVencimiento(), "/") . $SP;
			$aCreditos[$credito]	.= $xCred->getMontoAutorizado() . $SP . "0" . $SP . "N" . $SP. $xF->getFechaMX($IDDATE, "/") . $SP . $xF->anno($IDDATE) . $SP . $xF->mes($IDDATE) . $SP;
			$aCreditos[$credito]	.= $xCred->getMontoAutorizado() . $SP . $IDMONEDA01 . "\r\n";
		} 
	}
	//Personas
	if(!isset($aPersonas[$persona])){
		$xPer	= new cSocio($persona);
		if($xPer->init() == true){
			$tel2					= 0;
			$mODom					= $xPer->getODomicilio();
			$mOSuc					= new cSucursal($xPer->getSucursal());
			if($mOSuc->init() == true){
				$IDCC				= $xSuc->getCentroDeCosto();
			}
			if($mODom != null){
				$tel2				= $mODom->getTelefonoFijo();
			}
			$aPersonas[$persona]	= "$IDCIA" . $SP . "$IDGPO" . $SP . "$persona" . $SP . $xPer->getNombreCompleto(OUT_TXT) . $SP . $xPer->getNombreCompleto(OUT_TXT). $SP . $DEFCOB . $SP ;
			$aPersonas[$persona]	.= "0" . $SP . $xPer->getTelefonoPrincipal() . $SP . $tel2 . $SP . $IDPZO . $SP . $xPer->getCURP() . $SP . $IDCC . $SP . "A\r\n";
		}
	}
	//Recibos
	$xRec	= new cReciboDeOperacion(false, false, $recibo);
	if($xRec->init() == true){
		$xSuc	= new cSucursal($xRec->getSucursal());
		if($xSuc->init() == true){
			$IDCC	= $xSuc->getCentroDeCosto();
		}
		$RUTA 		= "0000";
		$IDAGENTE	= "0001";
		$IDCOB		= "0001";
		$IDTST		= "P";
		$IDCON		= "02";
		$IDOR		= "CC";
		$IDFIS		= "000000";
		$IDSFIS		= "0";
		$IDEVENC	= "N";
		$IDANULL	= "P";
		$IDCTRL		= "00000";
		$IDDIARIO	= "ASCXC";
		$IDAUT		= "S";
		$IDG		= "01";
		
		$xobservaciones	= str_replace("|", ".", $xRec->getObservaciones());
		
		$txt		= "$IDCIA" . $SP . $IDCC . $SP . $IDTDOC. $SP . $xF->anno($xRec->getFechaDeRecibo()) . $SP . $RUTA . $SP . $credito . $SP . $IDG . $SP . $persona . $SP .  $xF->getFechaMX($xRec->getFechaDeRecibo(), "/") . $SP;
		$txt		.= $xF->getFechaMX($xRec->getFechaDeCaptura(), "/") . $SP . $IDAGENTE . $SP . $IDCOB . $SP . $xRec->getTotal() . $SP . $xRec->getTotal() . $SP . $xRec->getTotal() . $SP ;
		$txt		.= $xRec->getTotal() . $SP . $xRec->getTotal() . $SP;
		$txt		.= $IDTST . $SP . $xRec->getTotal() . $SP . $xRec->getTotal() . $SP . $IDCON . $SP . $IDOR . $SP. $xF->anno($xRec->getFechaDeRecibo()) . $SP . $xF->mes($xRec->getFechaDeRecibo()) . $SP ;
		$txt		.= $xF->semana($xRec->getFechaDeRecibo()) . $SP;
		$txt		.= $IDFIS . $SP . $IDSFIS . $SP . $IDEVENC . $SP . $xF->getFechaMX($xRec->getFechaDeRecibo(), "/") . $SP . $IDANULL . $SP . $IDCTRL . $SP . $IDDIARIO . $SP . $IDMONEDA01 . $SP;
		$txt		.= VALOR_ACTUAL_DOLAR . $SP . $xobservaciones . $SP . $IDAUT . "\r\n";
		$SMVTOS		.= $txt; 
		$aRecibos[]	= $txt;
		
	}
}
$rs		= null;
//agregar titulos faltantes

//escribir libros
$iter	= 1;
foreach ($aPersonas as $idx => $cnt){
	$SCLIENTES		.= $cnt;
	$xls->addArray(explode("|", $cnt), $iter, 0);
	$iter++;
}
$iter	= 1;
foreach ($aCreditos as $idx => $cnt){
	$SCREDITOS		.= $cnt;
	$xls->addArray(explode("|", $cnt), $iter, 1);
	$iter++;
}
$iter	= 1;

foreach ($aRecibos as $idx => $cnt){
	$xls->addArray(explode("|", $cnt), $iter, 2);
	$iter++;
}

$xFil1	= new cFileLog("volcado-de-clientes-ARCCMC-$fecha.txt", true);
$xFil1->setWrite($SCLIENTES);
$xFil1->setClose();
$xFRM->addToolbar($xFil1->getLinkDownload("TR.Descargar ARCCMC", ""));

$xFil2	= new cFileLog("volcado-de-creditos-ARCCRD-$fecha.txt", true);
$xFil2->setWrite($SCREDITOS);
$xFil2->setClose();
$xFRM->addToolbar($xFil2->getLinkDownload("TR.Descargar ARCCRD", ""));


$xFil3	= new cFileLog("volcado-de-recibos-ARCCMD-$fecha.txt", true);
$xFil3->setWrite($SMVTOS);
$xFil3->setClose();
$xFRM->addToolbar($xFil3->getLinkDownload("TR.Descargar ARCCMD", ""));
//Renombrar
$xls->setRenameSheet(0, "ARCCMC-clientes");
$xls->setRenameSheet(1, "ARCCRD-creditos");
$xls->setRenameSheet(2, "ARCCMD-recibos");

$xls->setExportar("contabilidad");
$xFRM->addToolbar($xls->getLinkDownload("TR.Descargar Excel", ""));

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
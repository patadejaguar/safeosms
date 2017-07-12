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
$jxc 		= new TinyAjax();

function jsaGetLetras($idcredito, $idfecha){
	$xCred		= new cCredito($idcredito); 
	$xLi		= new cSQLListas();
	$cnt		= "";
	$xCred->init();
	$sql		= $xLi->getListadoDeLetrasPendientes($idcredito, $xCred->getTasaIVAOtros(), $xCred->getPagosSinCapital());
	//$xCred->getProximaParcialidad();
	
	$xT			= new cTabla($sql);
	$xT->setFechaCorte($idfecha);
	$xT->setFootSum(array(
		4 => "capital", 5 => "interes", 6 => "iva", 7 => "ahorro",
			8 => "otros", 9 => "total", 13 => "mora",  14 => "iva_moratorio"
	));
	$cnt		= $xT->Show();
	
	if($xCred->getPenasPorCobrar() >0){
		/*$xTb	= new cHTabla();
		$xTb->initRow();
		$xTb->addTH("TR.OTROS CARGOS");
		$xTb->addTH("TR.MONTO");
		$xTb->endRow();
		
		$xTb->initRow();
		$xTb->addTD("PENAS");
		$xTb->addTD($xCred->getPenasPorCobrar());
		$xTb->endRow();
				
		$cnt 	.= $xTb->get();*/
		
	}
	return $cnt; 
}

$jxc ->exportFunction('jsaGetLetras', array('idcredito', 'idfechadecalculo'), "#idlistado");
$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();
?> <style> #idavisopago, #idimporte, #iMontoRecibido { font-size : 1.3em !important; } </style><?php

$xFRM		= new cHForm("frm", "./");
$msg		= "";
$xFRM->OButton("TR.Obtener", "jsaGetLetras()", $xFRM->ic()->CARGAR);
$xFRM->OButton("TR.Reporte", "var xg = new CredGen(); xg.getReporteLetrasEnMora($credito, document.getElementById('idfechadecalculo').value)", $xFRM->ic()->CARGAR);
$xFRM->OHidden("idcredito", $credito);
$xFRM->ODate("idfechadecalculo", false, "TR.Fecha de Calculo");
$xFRM->addHElem("<div id='idlistado'></div>");
//$xFRM->addSubmit();
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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
$xHP		= new cHPage("TR.PARCIALIDADES PENDIENTES", HP_FORM);
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
	//echo $sql;
	//$xCred->getProximaParcialidad();
	
	$xT			= new cTabla($sql);
	$xT->setFechaCorte($idfecha);
	$xT->setOmitidos("codigo_de_base");
	$xT->setOmitidos("socio_afectado");
	$xT->setOmitidos("persona");
	$xT->setOmitidos("credito");
	$xT->setOmitidos("docto_afectado");
	$xT->setOmitidos("periodo_socio");
	$xT->setOmitidos("fecha_de_vencimiento");
	$xT->setOmitidos("tasa_de_mora");
	$xT->setOmitidos("mora");
	$xT->setOmitidos("capital_exigible");
	$xT->setOmitidos("interes_exigible");
	$xT->setOmitidos("otros_exigible");
	$xT->setOmitidos("ahorro_exigible");
	$xT->setOmitidos("iva_exigible");
	$xT->setOmitidos("total_sin_otros");
	$xT->setOmitidos("clave_otros");
	$xT->setOmitidos("int_corriente");
	$xT->setOmitidos("int_corriente_letra");
	//$xT->setOmitidos("letra");
	
	$xT->setForzarTipoSQL("dias", "int");
	
	$arrSum	= array(
			2 => "capital", 3 => "interes", 4 => "iva", 5 => "interes_moratorio", 6=>"iva_moratorio", 8=> "otros", 9=> "letra",10=> "neto"
	);
	if(MODULO_CAPTACION_ACTIVADO == true){
		$arrSum[5] = "ahorro";
		$arrSum[6] = "interes_moratorio";
		$arrSum[7] = "iva_moratorio";
		$arrSum[9] = "otros";
		$arrSum[10] = "letra";
		$arrSum[11] = "neto";
	} else {
		$xT->setOmitidos("ahorro");
		
	}
	
	$xT->setFootSum($arrSum);
	$xT->setUsarNullPorCero();

	$cnt		= $xT->Show();
	
	if($xCred->getPenasPorCobrar() >0){
		/*
		$xTb	= new cHTabla();
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


$xFRM		= new cHForm("frmcredslistapends", "./");
$msg		= "";

$xFRM->setTitle($xHP->getTitle());

$xFRM->OButton("TR.Obtener", "jsaGetLetras()", $xFRM->ic()->CARGAR);
$xFRM->OButton("TR.Reporte", "var xg = new CredGen(); xg.getReporteLetrasEnMora($credito, document.getElementById('idfechadecalculo').value)", $xFRM->ic()->CARGAR);
$xFRM->OHidden("idcredito", $credito);
$xFRM->ODate("idfechadecalculo", false, "TR.Fecha de Calculo");
$xFRM->addHElem("<div id='idlistado'></div>");

$xFRM->addJsInit("jsaGetLetras();");

//$xFRM->addSubmit();
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
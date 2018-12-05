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
$xHP		= new cHPage("TR.Agregar Notificaciones", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xSuc		= new cSucursal();
$jxc 		= new TinyAjax();

function jsaGetLetraMonto($idcredito, $fecha){
	$tab 	= new TinyAjaxBehavior();
	
	
	$xInt			= new cCreditosMontos($idcredito);
	$xInt->setSimularPorLetras();
	
	$capital		= $xInt->getCapitalPendiente();
	$interes		= $xInt->getInteresNormalPendiente();
	$moratorio		= $xInt->getInteresMoratorioPendiente();
	$otros			= $xInt->getOtros();
	$impuestos		= $xInt->getIVAPendiente();
	$total			= $capital + $interes + $moratorio + $otros + $impuestos;
	
	
	$tab->add(TabSetValue::getBehavior("periodos", $xInt->getPeriodoPends() ));
	$tab->add(TabSetValue::getBehavior("capital", $capital));
	$tab->add(TabSetValue::getBehavior("interes", $interes));
	$tab->add(TabSetValue::getBehavior("moratorio", $moratorio));
	$tab->add(TabSetValue::getBehavior("otros", $otros));
	$tab->add(TabSetValue::getBehavior("impuestos", $impuestos));
	$tab->add(TabSetValue::getBehavior("idmonto", $total));
	
	return $tab->getString();
}

$jxc->exportFunction('jsaGetLetraMonto', array('idsolicitud', 'idfechaactual'));
$jxc->process();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE); $fecha = parametro("fecha", $fecha, MQL_DATE);

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);

$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xSeg			= new cSeguimientoNotificaciones();

$total		= $monto; //parametro("total", 0, MQL_FLOAT);
$interes	= parametro("interes", 0, MQL_FLOAT);
$moratorio	= parametro("moratorio", 0, MQL_FLOAT);
$impuestos	= parametro("impuestos", 0, MQL_FLOAT);
$otros		= parametro("otros", 0, MQL_FLOAT);
$capital	= parametro("capital", 0, MQL_FLOAT);

$oficial	= parametro("idoficial", getUsuarioActual(), MQL_INT);
$formato	= parametro("idformato", 0, MQL_INT);
$canal		= parametro("idtipocanal", $xSeg->CANAL_PERSONAL, MQL_RAW);
$hora		= parametro("idhora", $xSuc->getHorarioDeEntrada(), MQL_RAW);

$enviarnow	= parametro("idahora", false, MQL_BOOL);

$periodos	= parametro("periodos", 0, MQL_INT);


$xHP->init();

$xFRM		= new cHForm("frmnewnotificacion", "notificaciones.add.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xTxt		= new cHText();
$xTxt2		= new cHText();
$xSel		= new cHSelect();
$msg		= "";
if($credito > DEFAULT_CREDITO AND $monto > TOLERANCIA_SALDOS){
	
	$res	= $xSeg->add($credito, $fecha, $hora, $total, $observaciones, $oficial, $canal, $formato, $capital, $interes, $moratorio, $otros, $impuestos, $periodos);
	//if($xSeg->init() == true){
	if($enviarnow == true AND $res !== false){
		$xSeg->enviar();
	}
	if($canal == $xSeg->CANAL_PERSONAL){
		$xFRM->addImprimir("TR.IMPRIMIR NOTIFICACION", "var xS=new SegGen();xS.getFormaNotificacion({clave:" . $xSeg->getClave() . "});", true);
	}
	//}
	$xFRM->setResultado($res, $xSeg->getMessages(), $xSeg->getMessages(), true);
	
	//$xFRM->addCerrar();
} else {
	if($credito <= DEFAULT_CREDITO){
		$xFRM->addCreditBasico();
	} else {
		$xCred	= new cCredito($credito);
		if($xCred->init() == true){
			$xFRM->addGuardar();
			
			$xFRM->addHElem( $xCred->getFichaMini());
			$xInt			= new cCreditosMontos($xCred->getNumeroDeCredito());
			$xInt->getOCredito($xCred->getDatosInArray());
			//$xInt->setActualizarPorLetras();
			$xInt->setSimularPorLetras();
			
			$capital		= $xInt->getCapitalPendiente();
			$interes		= $xInt->getInteresNormalPendiente();
			$moratorio		= $xInt->getInteresMoratorioPendiente();
			$otros			= $xInt->getOtros();
			$impuestos		= $xInt->getIVAPendiente();
			$total			= $capital + $interes + $moratorio + $otros + $impuestos;

			$xFRM->setNoAcordion();
			
			$xFRM->OHidden("idsolicitud", $credito);
			$xFRM->OHidden("idsocio", $xCred->getClaveDePersona());
			$xFRM->OHidden("idtasaiva", $xCred->getTasaIVA());
			$xFRM->OHidden("idtasaotros", $xCred->getTasaIVAOtros());
			
			$xFRM->addFecha($fecha);
			$xFRM->addHElem($xSel->getListaDeHoras("idhora")->get(true) );
			$xFRM->addHElem($xSel->getListaDeOficiales("idoficial", "", $xCred->getOficialDeSeguimiento())->get(true));
			$xFRM->addHElem($xSel->getListaDeCanalesDeNotificacion()->get(true) );
			
			
			$xFRM->addHElem( $xSel->getListaDeFormatos("idformato", 10, iDE_SEGUIMIENTO)->get(true) );
			
			$xFRM->OEntero("periodos",$xInt->getPeriodoPends(), "TR.PERIODOS");
			$xFRM->addControEvt("idfechaactual", "jsaGetLetraMonto", "change");
			
			$xFRM->addObservaciones();
			
			$xFRM->addSeccion("iddetallepago", "TR.PAGO Exigible", true);
			$xTxt->setDivClass("txmon"); $xTxt2->setDivClass("txmon");
			$xTxt->addEvent("jsUpdateSumas()", "onchange"); $xTxt2->addEvent("jsUpdateSumas2()", "onchange");
			
			$xFRM->addDivSolo( $xTxt->getDeMoneda("capital", "TR.CAPITAL", $capital) );
			
			$xFRM->addDivSolo( $xTxt->getDeMoneda("interes", "TR.INTERES", $interes) );
			$xFRM->addDivSolo( $xTxt->getDeMoneda("moratorio", "TR.MORATORIO", $moratorio) );
			$xFRM->addDivSolo( $xTxt->getDeMoneda("otros", "TR.OTROS", $otros) );
			
			$xFRM->addDivSolo( $xTxt2->getDeMoneda("impuestos", "TR.IVA", $impuestos) );
			
			$xFRM->addDivSolo( $xTxt->getDeMoneda("idmonto", "TR.TOTAL", $total) );
			
			$xFRM->OCheck("TR.ENVIAR", "idahora");
			
			$xFRM->endSeccion();
			
		} else {
			$xFRM->addCerrar();
		}
	}
}


echo $xFRM->get();

?>
<script>
function jsUpdateSumas(){
	var idtasaiva	= flotante($("#idtasaiva").val());
	var idtasaotros	= flotante($("#idtasaotros").val());	
	var capital		= flotante($("#capital").val());
	var interes		= flotante($("#interes").val());
	var moratorio	= flotante($("#moratorio").val());
	var otros		= flotante($("#otros").val());
	var iva			= redondear(interes*idtasaiva);
	var ivaotros	= redondear(((moratorio+otros)*idtasaotros));
	$("#impuestos").val(redondear(iva+ivaotros));
	//actualizar Impuestos
	var impuestos	= flotante($("#impuestos").val());
	$("#idmonto").val(capital+interes+moratorio+otros+impuestos);
	
		
}
function jsUpdateSumas2(){
	var idtasaiva	= flotante($("#idtasaiva").val());
	var idtasaotros	= flotante($("#idtasaotros").val());	
	var capital		= flotante($("#capital").val());
	var interes		= flotante($("#interes").val());
	var moratorio	= flotante($("#moratorio").val());
	var otros		= flotante($("#otros").val());
	//actualizar Impuestos
	var impuestos	= flotante($("#impuestos").val());
	$("#idmonto").val(capital+interes+moratorio+otros+impuestos);	
}
</script>
<?php

$jxc->drawJavaScript(false, true);

$xHP->fin();
?>
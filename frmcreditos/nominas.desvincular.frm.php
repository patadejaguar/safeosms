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
$DDATA		= $_REQUEST;
$jxc 		= new TinyAjax();

function jsaSetCreditosADespedidos($credito, $fecha, $observaciones){
    $msg		= "";
    $xCred		= new cCredito($credito);
    $xdat		= new cFecha(0);
    $fecha		= $xdat->getFechaISO($fecha);

    if($xCred->init() == true){
	    $socio	= $xCred->getClaveDePersona();
	    $xSoc	= new cSocio($socio);
	    $xSoc->init();
	    
	    $xCred->setCambioProducto(CREDITO_PRODUCTO_DESTINO_DESCARTADOS);
	    
	    //Agregar operacion de desvinculacion
	    $xRe	= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, false, DEFAULT_RECIBO);
	    $xRe->init();
	    $xRe->setNuevoMvto($fecha, $xCred->getSaldoActual(), OPERACION_CLAVE_DESVINCULACION, $xCred->getPeriodoActual(), "", 1, false, $socio, $credito, $fecha);
	    $xRe->setFinalizarRecibo();
	    
	    $xCred->setResetPersonaAsociada($fecha, $observaciones, FALLBACK_CLAVE_EMPRESA);
	    
	    $msg	.= $xSoc->getMessages(OUT_TXT);
	    $msg	.= $xRe->getMessages(OUT_TXT);
	
		$xRN	= new cReglaDeNegocio();
		$xEmp	= new cEmpresas($xCred->getClaveDeEmpresa()); $xEmp->init();
		$oP		= $xCred->getOPersona();
		
		$xRN->setVariables(array(
				"nombre_de_persona" => $oP->getNombreCompleto(),
				"mensaje" => $observaciones,
				"saldo_del_credito" => $xCred->getSaldoActual(),
				"nombre_de_la_empresa" => $xEmp->getNombreCorto()
		));	
		
		$xRN->setExecuteActions($xRN->reglas()->RN_NOMINA_AL_DESPEDIR);
    }
    $msg	.= $xCred->getMessages(OUT_TXT);
    $xF		= new cFileLog();
    $xF->setWrite($msg);
    $xF->setClose();
    
    return  $xF->getLinkDownload("Descarga de Log");
    
}

function jsaSetDesvincularPersona($credito, $fecha, $observaciones){
	$msg		= "";
	$xLng		= new cLang();	
	$xCred		= new cCredito($credito);
	$xCred->init();
	$xdat		= new cFecha(0);
    $fecha		= $xdat->getFechaISO($fecha);
    
	$xCred->setResetPersonaAsociada($fecha, $observaciones, FALLBACK_CLAVE_EMPRESA);
	$oP			= $xCred->getOPersona();
	$xEmp		= new cEmpresas($xCred->getClaveDeEmpresa()); $xEmp->init();
	$xRN		= new cReglaDeNegocio();
	$xRN->setVariables(array(
		"nombre_de_persona" => $oP->getNombreCompleto(),
		"mensaje" => $observaciones,
		"saldo_del_credito" => $xCred->getSaldoActual(),
		"nombre_de_la_empresa" => $xEmp->getNombreCorto()
	));
	$xRN->setExecuteActions( $xRN->reglas()->RN_NOMINA_AL_DESVINCULAR );
		
	return $xLng->get(MSG_READY_SAVE);
}

function jsaSetPagarCredito($credito, $fecha, $observaciones){
	$msg		= "";
	$xLng		= new cLang();
	$xCred		= new cCredito($credito);
	$xCred->init();
	$xdat		= new cFecha(0);
	$fecha		= $xdat->getFechaISO($fecha);
	//$xCred->setResetPersonaAsociada($fecha, $observaciones);
	//return $xLng->get(MSG_READY_SAVE);
	$xCred->setAbonoCapital($xCred->getSaldoActual(), $xCred->getPeriodoActual(), DEFAULT_CHEQUE, TESORERIA_COBRO_NINGUNO, DEFAULT_RECIBO_FISCAL, $observaciones, DEFAULT_GRUPO, $fecha);
	return $xCred->getMessages(OUT_HTML);
}

//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->exportFunction('jsaSetDesvincularPersona', array("idcredito", "idfechaactual", "idobservaciones"), "#idmsg");
$jxc ->exportFunction('jsaSetCreditosADespedidos', array("idcredito", "idfechaactual", "idobservaciones"), "#idmsg");
$jxc ->exportFunction('jsaSetPagarCredito', array("idcredito", "idfechaactual", "idobservaciones"), "#idmsg");

$jxc ->process();

$persona	= (isset($DDATA["persona"])) ? $DDATA["persona"] : DEFAULT_SOCIO;
$persona	= (isset($DDATA["socio"])) ? $DDATA["socio"] : $persona;
$credito	= (isset($DDATA["credito"])) ? $DDATA["credito"] : DEFAULT_CREDITO;
$jscallback	= (isset($DDATA["callback"])) ? $DDATA["callback"] : "";
$tiny		= (isset($DDATA["tiny"])) ? $DDATA["tiny"] : "";
$form		= (isset($DDATA["form"])) ? $DDATA["form"] : "";

echo $xHP->getHeader();

$jsb	= new jsBasicForm("", iDE_CAPTACION);

echo $xHP->setBodyinit();

$xFRM		= new cHForm("frmdesvincular", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();

$jsb->setNameForm( $xFRM->getName() );
//$xFRM->addCreditBasico();
$xFRM->addFecha();
$xFRM->addObservaciones();
//$xFRM->addHElem( $xTxt->get("idobservaciones", "", $xFRM->lang("observaciones")) );

$xFRM->addToolbar( $xBtn->getBasic("TR.Despedido de la_Empresa", "jsSaveEstado(1)", "eliminar", "id1", false) );
$xFRM->addToolbar( $xBtn->getBasic("TR.Desvincular de la_empresa", "jsSaveEstado(2)", "quitar", "id2", false) );
$xFRM->addToolbar( $xBtn->getBasic("TR.Pago Total", "jsSaveEstado(3)", "dinero", "id4", false ) );

$xFRM->addFootElement( "<p class='aviso' id='idmsg'></p>" );
$xFRM->OHidden("idcredito", $credito);

echo $xFRM->get();


$jsb->show();
$jxc->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
var msg		= "<?php  echo $xFRM->lang(MSG_CONFIRM_SAVE); ?>";
var msg1	= "<?php  echo $xFRM->lang("despedido"); ?>";
var msg2	= "<?php  echo $xFRM->lang("desvinculado"); ?>";
var xG		= new Gen();
function jsSaveEstado(tipo){
	var sip	= false;
	if (tipo == 1) {
	    var sip	= confirm(msg + ":\n" + msg1);
	    if (sip == true) { jsaSetCreditosADespedidos(); }
	} else if (tipo == 2) {
	    var sip	= confirm(msg + ":\n" + msg2);
	    if (sip == true) { jsaSetDesvincularPersona(); }
	} else if (tipo == 3){
	    var sip	= confirm(msg + ":\n" + msg2);
	    if (sip == true) { jsaSetPagarCredito(); }
	}
	setTimeout("jsEnd()",5000);
}
function jsEnd(){ xG.close();   }
</script>
<?php
$xHP->fin();
?>
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
$xHP			= new cHPage("", HP_FORM);

$xF			= new cFecha();
$xT			= new cTipos();

$DDATA			= $_REQUEST;
$action			= (isset($DDATA["cmd"])) ? $DDATA["cmd"] : SYS_NINGUNO;
$cargar			= (isset($DDATA["item"])) ? $DDATA["item"] : "";
$origen			= (isset($DDATA["origen"])) ? $DDATA["origen"] : SYS_NINGUNO;

$recibo			= (isset($DDATA["idrecibo"])) ? $DDATA["idrecibo"] : DEFAULT_RECIBO;
$operacion		= (isset($DDATA["idoperacion"])) ? $DDATA["idoperacion"] : SYS_UNO;

$tipoDeExposicion	= (isset($DDATA["ctipo_pago"])) ? $DDATA["ctipo_pago"] : DEFAULT_TIPO_PAGO;

$monto			= (isset($DDATA["idmonto"])) ? $xT->cFloat($DDATA["idmonto"]) : 0;

$MontoOperacion 	= (isset($DDATA["idmontooperado"])) ? $DDATA["idmontooperado"] : $monto;
$MontoCambio 		= (isset($DDATA["idmontocambio"])) ? $DDATA["idmontocambio"] : 0;
$banco 			= (isset($DDATA["idbanco"])) ? $DDATA["idbanco"] : 99; //FALLBACK_BANCO
$cheque 		= (isset($DDATA["idcheque"])) ? $DDATA["idcheque"] : DEFAULT_CHEQUE;
$CuentaBancaria 	= (isset($DDATA["idcuenta"])) ? $DDATA["idcuenta"] : DEFAULT_CUENTA_BANCARIA;
$documento 		= (isset($DDATA["iddoctodescontado"])) ? $DDATA["iddoctodescontado"] : DEFAULT_CREDITO;
$observaciones 		= (isset($DDATA["idobservaciones"])) ? $DDATA["idobservaciones"] : "";
$fecha 			= (isset($DDATA["idfecha-0"])) ?  $xF->getFechaISO($DDATA["idfecha-0"]) : fechasys();
$msg			= (isset($DPDATA[SYS_MSG]) ) ? $DPDATA[SYS_MSG] : "";

$hora 			= date("H:i");

//$msg		.= "MONTO $monto .... " . $DDATA["idmonto"] ;
if($monto > 0){
    $xCaja	= new cCaja();
    /*
$recibo, $tipoDeExposicion, $MontoRecibido, $MontoOperacion = 0, $MontoCambio = 0, 
		$banco = 1, $cheque = "", $CuentaBancaria = 0, $DocumentoDescontado = 0, $Observaciones = "", $fecha = false, $hora = false
    */
    $xCaja->addOperacion($recibo, $tipoDeExposicion, $monto, $MontoOperacion, $MontoCambio, $banco, $cheque, $CuentaBancaria, $documento,
			 $observaciones, $fecha, $hora);
    $msg		.= $xCaja->getMessages(OUT_TXT);
}
if($cargar != "" AND $origen = "recibo"){
	$xRec		= new cReciboDeOperacion(false, true, $cargar);
	$xRec->init();
	$monto		= $xRec->getTotal();
	//$documento	= $xRec->getCodigoDeDocumento();
	$recibo		= $cargar;
	$fecha		= $xRec->getFechaDeRecibo();
	//$operacion	= BANCOS_OPERACION_DEPOSITO;
	$cheque		= $xRec->getNumeroDeCheque();
	$observaciones	= $xRec->getObservaciones();
	//$xBanc		= new cCuentaBancaria("");
	$xBanc		= $xRec->getBancoPorOperacion();
	if($xBanc != null){
	    $banco		= $xBanc->getClaveDeBanco();
	    $CuentaBancaria	= $xBanc->getNumeroDeCuenta();
	}
	$msg		.= $xRec->getMessages(OUT_TXT);
}
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
echo $xHP->getHeader();
$jsb	= new jsBasicForm("tesoreria_operaciones", iDE_OPERACION);
$jsb->setIncludeOnlyCommons();
//$jxc ->drawJavaScript(false, true);
echo $xHP->setBodyinit();

$xFRM	= new cHForm("tesoreria_operaciones", "./tesoreria_operaciones.frm.php");
$xBtn	= new cHButton();		
$xTxt	= new cHText();


$xTest	= new cCaja();

//$xTest->addOperacion($recibo, )
$xHCob			= new cHCobros();
$xHSel			= new cHSelect();
$xHSel->addOptions( array(1=> "INGRESO", 0 => "NINGUNO", -1 => "EGRESO") );
$xHSel->setDefault(SYS_UNO);
//$selOperacion		= $xHSel->get("idoperacion", "operacion", $operacion);
$xSel			= new cSelect("idbanco", "idbanco" , TBANCOS_ENTIDADES);
$xSel->setOptionSelect($banco);
$xSel2			= new cSelect("idcuenta", "idcuenta" , TBANCOS_CUENTAS);
$xSel2->setOptionSelect($CuentaBancaria);

/*
SELECT idtesoreria_cajas_movimientos, codigo_de_caja, idusuario, documento, recibo, tipo_de_movimiento, tipo_de_exposicion, fecha, hora,
monto_del_movimiento, monto_recibido, monto_en_cambio, banco, numero_de_cheque,
observaciones, sucursal,
eacp, cuenta_bancaria, documento_descontado 
    FROM tesoreria_cajas_movimientos
*/
$xF			= new cHDate(0, $fecha, TIPO_FECHA_OPERATIVA);
$xFRM->addHElem($xF->get("Fecha de Operacion"));

$xFRM->addHElem($xHSel->get("idoperacion", "Operacion", $operacion) );
$xFRM->addHElem( "<div class='tx4'>" . $xHCob->getSelectTiposDePago() . "</div>" );
/*$xFRM->addHElem($xTxt->get("idsocio", $socio, "Persona"));*/
//$xFRM->addHElem($xTxt->get("iddocumento", $documento, "Documento"));
$xFRM->addHElem($xTxt->get("idrecibo", $recibo, "Recibo"));
$xFRM->addHElem($xTxt->getDeMoneda("idmonto", "Monto", $monto));

$xFRM->addHElem($xSel->get("Banco", true));
$xFRM->addHElem($xSel2->get("Cuenta Bancaria", true));
$xFRM->addHElem($xTxt->get("idcheque", $cheque, "Numero de Cheque"));

$xFRM->addHElem($xTxt->get("idobservaciones", $observaciones, "Observaciones"));
$xFRM->addHTML("<div class='aviso'>$msg</div>");
$xFRM->addFootElement($xBtn->getBasic("Guardar", "setGuardar", "guardar"));


echo $xFRM->get();
echo $xHP->setBodyEnd();
$jsb->show();
?>
<!-- HTML content -->
<script>
    function setGuardar(){
	jsEvaluarFormulario();
    }
</script>
<?php
$xHP->end();
?>
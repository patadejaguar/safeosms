<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_FORM);
$xHP->setIncludes();
echo $xHP->getHeader();
$xHP->setArchivo("../frmbancos/movimientos_bancarios.frm.php");
$xF		= new cFecha();
$oficial 			= elusuario($iduser);
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$DPDATA				= $_REQUEST;//)) ? isset($_REQUEST) : array();

$monto				= (isset($DPDATA["idmonto"]) ) ? $DPDATA["idmonto"] : 0;
$numero_de_cuenta		= (isset($DPDATA["idcuenta"]) ) ? $DPDATA["idcuenta"] : "";
$socio				= (isset($DPDATA["idsocio"]) ) ? $DPDATA["idsocio"] : "";
$documento			= (isset($DPDATA["iddocumento"]) ) ? $DPDATA["iddocumento"] : "";
$recibo				= (isset($DPDATA["idrecibo"]) ) ? $DPDATA["idrecibo"] : "";
$operacion			= (isset($DPDATA["idoperacion"]) ) ? $DPDATA["idoperacion"] : "";
$estado				= (isset($DPDATA["idestatus"]) ) ? $DPDATA["idestatus"] : "";
$beneficiario			= (isset($DPDATA["idbeneficiario"]) ) ? $DPDATA["idbeneficiario"] : "";
$descuento			= (isset($DPDATA["iddescuento"]) ) ? $DPDATA["iddescuento"] : 0;
$fecha				= (isset($DPDATA["idfecha-0"]) ) ? $xF->getFechaISO( $DPDATA["idfecha-0"]) : fechasys();
$msg				= (isset($DPDATA[SYS_MSG]) ) ? $DPDATA[SYS_MSG] : "";

$cargar				= (isset($DPDATA["item"]) ) ? $DPDATA["item"] : "";
$origen 			= (isset($DPDATA["origen"]) ) ? $DPDATA["origen"] : SYS_NINGUNO;

if( $monto > 0 ){
	//
			
	$xBanc			= new cCuentaBancaria($numero_de_cuenta);	
	$r				= $xBanc->addOperacion($operacion, $documento, $recibo, $beneficiario, $monto, $socio, $fecha, $estado, false, $descuento);
	if( $r == false ){
		$strGet		= "";
		$aviso		= "";
		foreach($DPDATA as $llave => $valor ){
			$valor	=	( $llave == "idmonto") ? 0 : $valor;
			$strGet	.= "$llave=$valor&";
		}
		$aviso		= "El Registro no se Guardo, Revise sus Valores, EL Monto se lleva a Cero";
		$strGet		.= "msg=" . htmlentities($aviso);
		echo $xHP->getJsBack($aviso, $strGet);
	} else {
		$msg		= "El Registro se ha Guardado Exitosamente";
	}
}
if($cargar != "" AND $origen = "recibo"){
	$xRec		= new cReciboDeOperacion(false, true, $cargar);
	$xRec->init();
	$monto		= $xRec->getTotal();
	$documento	= $xRec->getCodigoDeDocumento();
	$recibo		= $cargar;
	$fecha		= $xRec->getFechaDeRecibo();
	$operacion	= BANCOS_OPERACION_DEPOSITO;
	$socio		= $xRec->getCodigoDeSocio();
	$xSoc		= new cSocio($socio, true);
	$beneficiario	= $xSoc->getNombreCompleto();
	
}
$jsb	= new jsBasicForm("bancos_operaciones", iDE_OPERACION);
$jsb->setIncludeOnlyCommons();


//$jsb->show();
//$jxc ->drawJavaScript(false, true);
echo $jsb->setIncludeJQuery();
echo $xHP->setBodyinit();
$xTxt			= new cHText();
$xBtn			= new cHButton();

$xFRM			= new cHForm("bancos_operaciones", "movimientos_bancarios.frm.php");
//id,	label value, size,	class,	options[])
$xSel			= new cSelect("idcuenta", "idcuenta" , TBANCOS_CUENTAS);
$xSel->setOptionSelect($numero_de_cuenta);
$xHSel			= new cHSelect();
$xHSel->addOptions(array("cheque" => "Cheque", "deposito" => "Deposito", "retiro" => "Retiro") );
$selOperacion		= $xHSel->get("idoperacion", "operacion", $operacion);
$xHSel->setClearOptions();
$xHSel->addOptions(array("autorizado" => "Autorizado", "noautorizado" => "No Autorizdo", "cancelado" => "Cancelado") );
$selEstatus		= $xHSel->get("idestatus", "Estatus", $estado);
$xF			= new cHDate(0, $fecha, TIPO_FECHA_OPERATIVA);

$xFRM->addHElem($xF->get("Fecha de Operacion"));
$xFRM->addHElem($xSel->get("Cuenta", true));

$xFRM->addHElem($selOperacion);
$xFRM->addHElem($selEstatus);
$xFRM->addHElem($xTxt->get("idsocio", $socio, "Persona"));
$xFRM->addHElem($xTxt->get("idbeneficiario", $beneficiario, "Beneficiario(Nombre)"));
$xFRM->addHElem($xTxt->get("iddocumento", $documento, "Documento"));
$xFRM->addHElem($xTxt->get("idrecibo", $recibo, "Recibo"));
$xFRM->addHElem($xTxt->getDeMoneda("idmonto", "Monto", $monto));

$xFRM->addHTML("<div class='aviso'>$msg</div>");
//$xFRM->addSubmit("Guardar Movimiento", "setGuardar");
$xFRM->addToolbar($xBtn->getBasic("Guardar", "setGuardar", "guardar", "idsave", false));

echo $xFRM->get();
echo $jsb->get();

//id value class size maxlength arra(varias_opciones)
//nombre = id
echo $xHP->setBodyEnd();

?>

<script  >
function setGuardar(){

	jsEvaluarFormulario();
}

</script>
<?php
$xHP->end();
?>
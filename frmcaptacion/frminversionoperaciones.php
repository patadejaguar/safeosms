<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 * 
 * 		-
 *		-
 */
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
$xHP					= new cHPage("TR.Operaciones de Inversion a Plazo");

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$url_recibo_inversion 	= "";
$recibo_de_reinversion	= false;
$messages				= "";

$jxc 					= new TinyAjax();
$xF						= new cFecha();
$xPdto					= new cCaptacionProducto();

function jsaGetDatosCuenta($cuenta, $socio, $dias){
	$xInv	= new cCuentaInversionPlazoFijo($cuenta, $socio);
	$tasa	= $xInv->getTasaAplicable($dias) * 100;
	$xSoc	= new cSocio($socio); $xSoc->init();
	$cuentainteres	= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_INTERESES );
	$tab 	= new TinyAjaxBehavior();
	$tab -> add(TabSetValue::getBehavior("idtasa", $tasa ));
	$tab -> add(TabSetValue::getBehavior("idcuentainteres", $cuentainteres ));
	return $tab -> getString();
}
$jxc ->exportFunction('jsaGetDatosCuenta', array('idcuentaactual', "idsocioactual", "iddias") );
$jxc ->process();

echo $xHP->getHeader();
echo $xHP->setBodyinit();


$xFRM		= new cHForm("frminversiones", "frminversionoperaciones.php?action=1");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xDiv		= new cHDiv("tx14");
$xL			= new cLang();

$xFRM->addJsBasico(iDE_CAPTACION, CAPTACION_TIPO_PLAZO);

if( $action == SYS_NINGUNO ){
	$xFRM->addCuentaCaptacionBasico(true,  CAPTACION_TIPO_PLAZO);
	$xFRM->addSubmit();
	
} elseif( $action == SYS_UNO){
	//$xFRM->se
	//Codigo para Iniciar la Reinversion
	$xCta				= new cCuentaInversionPlazoFijo($cuenta, $persona);
	$xCta->init();	
	$xFRM->setAction("frminversionoperaciones.php?cuenta=$cuenta&persona=$persona&action=" . SYS_DOS);
	$xFRM->addSubmit();
	
	$xFRM->addHElem( $xCta->getFicha(true, "", true) );
	$xFRM->addHElem( $xDate->get("TR.Fecha de Operacion") );
	
	$xFRM->addHElem($xPdto->getListaDeDias(array(
			"onchange" => "jsGetDatosCuenta()",
				"onblur" => "jsGetDatosCuenta()" 
	)));


	$xFRM->addHElem( $xTxt->getDeMoneda("idtasa", "TR.Tasa Negociada") );
	//cuenta de intereses
	$xFRM->addCuentaCaptacionInteres();
	$xFRM->addObservaciones();
	$xFRM->addFootElement("<input type='hidden' id='idsocioactual' name='idsocioactual' value='$persona' />");
	$xFRM->addFootElement("<input type='hidden' id='idcuentaactual' name='idcuentaactual' value='$cuenta' />");

} elseif( $action == SYS_DOS){
	//sana las variables
	$xTyp				= new cTipos();
	$sucess				= true;
	
	$tasa 				= parametro("idtasa", 0, MQL_FLOAT);
	$dias 				= parametro("iddias", INVERSION_DIAS_MINIMOS, MQL_INT);
	$CuentaDeInteres	= parametro("idcuentainteres", DEFAULT_CUENTA_CORRIENTE, MQL_INT);
	$observaciones		= parametro("idobservaciones");

	$fecha_actual 		= parametro("idfecha-0", false);
	$fecha_actual		= ($fecha_actual == false) ? fechasys() : $xF->getFechaISO($fecha_actual);

	$xSoc				= new cSocio($persona);
	$xSoc->init();
	$tasa 				= $tasa / 100;
	
	$CuentaDeInteres	= ( $CuentaDeInteres == DEFAULT_CUENTA_CORRIENTE ) ? false : $CuentaDeInteres;

	//Codigo para Iniciar la Reinversion
	$xCta				= new cCuentaInversionPlazoFijo($cuenta, $persona);
	$xCta->init();
	
	if ( $xCta->getEsOperable($fecha_actual) == false ){
		$messages		.= "ERROR\tNO_OP\tLa Cuenta no es Operativa\r\n";
		$sucess			= false;
	}
	$arrUpdate			= array("tasa_otorgada" => $tasa, "dias_invertidos" => $dias);
	//actualizar datos de la Inversion
	if ( ($CuentaDeInteres != false) AND ( $xSoc->existeCuenta($CuentaDeInteres) == false) ) {
		$arrUpdate["cuenta_de_intereses"] = $CuentaDeInteres;
		$messages		.= "ERROR\tNO_ACT\tLa Cuenta Corriente de Interes es $CuentaDeInteres\r\n";
	}
	if ( $sucess == true ){ 
		$xCta->setUpdate($arrUpdate);
		$xCta->init();
	} else {
		$messages		.= "WARN\NO_ACT\tLa Cuenta no se actualizo\r\n";
	}
	
	$saldo				= $xCta->getNuevoSaldo();
	
	if($sucess == true ){
		if ( $saldo > INVERSION_MONTO_MINIMO ){
			$recibo_de_reinversion 	= $xCta->setReinversion($fecha_actual, true);
			$messages				.= "WARN\tLa Inversion se CIERRA\r\n";
		} elseif($saldo >= TOLERANCIA_SALDOS) {
			$messages				.= "ERROR\tSDO_MIN\tEL saldo de la Inversion($saldo) es menor a " . INVERSION_MONTO_MINIMO . ", la Inversion no se efectua\r\n";
		//LLevar a cuenta Corriente
			$mCorriente				= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA);
			if ( $xTyp->getEvalNotNull( array($mCorriente) ) == true ){
				$messages			.= "WARN\tSDO_MIN\tSe intenta el Traspaso a cuenta Corriente\r\n";
				$messages			.= $xCta->setTraspaso($mCorriente, CAPTACION_TIPO_VISTA);
			}
		}
	} else {
			$messages				.= "WARN\tLa Inversion permanece Abierta\r\n";
	}
	$url_recibo_inversion			= $xCta->getURLReciboInversion($recibo_de_reinversion);
	
	if ( MODO_DEBUG == true){ $xFRM->addLog($messages . $xCta->getMessages());	}
	
	$xFRM->addAviso($messages);
	
	$xCta->init();
	$xFRM->addHElem( $xCta->getFicha(true, "", true) );
	$xFRM->addToolbar( $xBtn->getBasic("TR.Imprimir constancia_de_inversion", "jsPrintReporto()", "imprimir", "idnim", false));

}
echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
</body>
<script  >
var mjrsF 		= "../clsfunctions.inc.php";
var mIForm		=	document.frminversiones;
var localDate	= "<?php echo fechasys(); ?>";
	function jsGetDatosCuenta(){
		jsaGetDatosCuenta();
	}
	function getReporto(){}
	function setDiasInversion(){}

	/* Imprime el Recibo de Reinversion */
 	function jsPrintReporto() {
		var elUrl= "<?php echo $url_recibo_inversion; ?>";
		jsGenericWindow(elUrl);
	}
	function setFechaInv() {
		var mDs = parseInt(document.getElementById("iddias").value);
	}
	function jsShowFirmas(){
		var cCuenta		= document.getElementById("idcuenta").value;
		var url = "frmcaptacionfirmas.php?idcuenta=" + cCuenta;
		//jsGenericWindow(url);
	}
</script>
</html>
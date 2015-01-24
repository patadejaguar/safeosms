<?php
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
$xHP	= new cHPage("TR.Retiro de cuentas_de_inversion");

$HObj	= new cHObject();
$HBtn	= new cHButton("");
$jxc 	= new TinyAjax();
$xCaja	= new cCaja();
$xF		= new cFecha();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){header ("location:../404.php?i=200");}
$com		= parametro("action", SYS_NINGUNO);
$iddocto 	= parametro("idcuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT);
$monto 		= parametro("idmonto", 0, MQL_FLOAT);
$idsocio 	= parametro("idsocio", DEFAULT_SOCIO, MQL_INT);
$idrecibo	= parametro("idrecibo");
$msg		= "";

function jssetUltimoCheque($banco, $tipo_de_pago){
	$cheque = 0;
	if(($tipo_de_pago == TESORERIA_PAGO_CHEQUE) or ($tipo_de_pago == TESORERIA_PAGO_DOCTO)) {
		$cheque = getUltimoCheque($banco);
	}
	return $cheque;
}

function jsaGetDatosCuenta($cuenta, $socio){
	$xCta		= new cCuentaInversionPlazoFijo($cuenta, $socio);
	$xCta->init();
	$DC			= $xCta->getDatosInArray();
	$MaxRet		= $xCta->getMaximoRetirable();
	$sdo		= $DC["saldo_cuenta"];
		$tab 	= new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("idMaxRet", $MaxRet ));
		$tab -> add(TabSetValue::getBehavior("idSaldoAnterior", $sdo ));
		//$tab -> add(TabSetValue::getBehavior("idmonto", $sdo ));
		//$tab -> add(TabSetValue::getBehavior("imsg", $xCta->getMessages("txt") ));
		return $tab -> getString();
}
$jxc ->exportFunction('jssetUltimoCheque', array('idcuenta_cheques', 'idtipo_pago'), "#idcheque");
$jxc ->exportFunction('jsaGetDatosCuenta', array("idcuenta", "idsocio") );

$jxc ->process();

echo $xHP->getHeader();

$jxc ->drawJavaScript(false, true);

$xFRM		= new cHForm("frmretiroinversion", "frmretiroinversion.php?action=next");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();

$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xHP->setBodyinit();
if($com == SYS_NINGUNO){
	
	
	$xFRM->addCuentaCaptacionBasico(true, CAPTACION_TIPO_PLAZO);
	$xFRM->addPagoBasico();
	$xFRM->addHElem($xDate->get("TR.Fecha"));
	$xFRM->addObservaciones();
	
	$xFRM->addHElem( $xTxt->getDeMoneda("idmonto", "TR.Monto de Retiro", 0, true) );
	
	$xFRM->addFootElement("<input type='hidden' name='cMaxRet' id='idMaxRet' value='0'  />");
	$xFRM->addFootElement("<input type='hidden' name='sdoanterior' id='idSaldoAnterior' value='0' />");
	
	$xFRM->addSubmit();
	
	
	$xFRM->addJsBasico(iDE_CAPTACION, CAPTACION_TIPO_PLAZO);

$jxc ->drawJavaScript(false, true);

} else {

	if ( $monto<=0 ) {
		$msg			.= "ERROR\tAGREGUE LOS DATOS COMPLETOS Y ENVIE EL FORMULARIO\r\n";
	} else {

	$Fecha				= parametro("idfecha-0", false);
	$Fecha				= ($Fecha == false) ? fechasys() : $xF->getFechaISO($Fecha);
		
	$detalles 			= parametro("idobservaciones");
	$cheque 			= parametro("cheque", DEFAULT_CHEQUE);
	$comopago 			= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
	//$cuenta_cheques 	= parametro("cuenta_cheques", DEFAULT_CUENTA_BANCARIA);
	$foliofiscal 		= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);
	
	
	$sucess				= true;
	$xCta				= new cCuentaInversionPlazoFijo( $iddocto, $idsocio );
	$xCta->init();
	//$xCta->setCuentaBancaria($cuenta_cheques);
	$maximo_retirable	= $xCta->getMaximoRetirable();
		if ($monto > $maximo_retirable){
			$msg	.= "ERROR\tNo puede Retirar mas de $maximo_retirable, usted quizo retirar $monto \r\n";
			$sucess		= false;
			
		} 
		if ( $xCta->getEsOperable() == false ){
			$msg	.= "ERROR\tLa cuenta No es Operable\r\n";
			$sucess		= false;			
		}

		
		$xFRM->addAviso($msg);
		
		if ( $sucess == true ){
			$recibo				= $xCta->setRetiro($monto, $cheque, $comopago, $foliofiscal, $detalles, DEFAULT_GRUPO, $Fecha);
			//Imprime la Ficha del socio
			$xCta->init();
			$xSoc		= new cSocio($xCta->getClaveDePersona());	$xSoc->init();
			$xFRM->addHTML( $xSoc->getFicha());
			$xFRM->addHTML( $xCta->getFicha(true) );
			if($xCta->getORec() == null){
				
			} else {
				$xFRM->addHTML( $xCta->getORec()->getFicha(true)  );
				$xFRM->addHTML( $xCta->getORec()->getJsPrint(true) );
				$xFRM->addPrintRecibo();
				$xFRM->addAvisoRegistroOK();
			}
		}	
		if ( MODO_DEBUG == true ){ 	$msg		.= $xCta->getMessages();		}
		$xFRM->addAviso($msg);
	}
}
echo $xFRM->get();
?>
<script>
var jrsFile	= "../clsfunctions.inc.php";
var mFrm	= document.retiroinversion;
	function jsGetSaldoActual() {
		jsaGetDatosCuenta();
	}
	function monto_maximo() {
		jsaGetDatosCuenta();
		var retmax	= parseFloat(mFrm.cMaxRet.value);
		var sdo		= parseFloat(mFrm.sdoanterior.value);
		var retped	= parseFloat(mFrm.monto.value);
		
		if (retped > retmax ) {
			alert("NO SE PUEDE RETIRAR MAS DE $ " + retmax + "\nDE UN SALDO DE $ " + sdo);
			mFrm.monto.value = retmax;
			mFrm.idcuenta.select();
		}
		if (isNaN(retmax) ){ jsGetSaldoActual(); }
	}
/*	function jsImprimirRecibo() {
		var elUrl= "../rpt_formatos/frmreciboretiro.php?recibo=<?php echo $idrecibo; ?>";
		var rptrecibo = window.open( elUrl, "");
			rptrecibo.focus();
	}*/
function obtainLastCheque(){ jssetUltimoCheque(); }
function jsShowFirmas(){
			var cSocio	= document.getElementById("idsocio").value;
			var url 	= "frmcaptacionfirmas.php?id=" + cSocio;
			var mywin 	= window.open(url, "" ,"width=800,height=600,resizable,scrollbars");
}
</script>
</html>
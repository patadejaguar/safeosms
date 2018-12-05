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
$xHP				= new cHPage("Cobranza.- Cheques Internos", HP_FORM);
//=========================== AJAX
$jxc 			= new TinyAjax();
function jsaSetPago($Recibo, $cheque1, $Cuenta1, $monto1, $cheque2, $Cuenta2, $monto2){
	$sucess		= true;
	$tab 		= new TinyAjaxBehavior();
	$xCtaBanc	= new cCuentaBancaria($Cuenta1);
	$xLog		= new cCoreLog();
	$monto1		= setNoMenorQueCero($monto1);
	$monto2		= setNoMenorQueCero($monto2);
	
	$xCaja		= new cCaja();		//$recibo, $MontoRecibido, $banco, $cheque
	if( $monto1 > 0 ){
		$s1			= $xCtaBanc->getVerificarCheque($cheque1, $monto1, true);
		$sucess		= $s1;
		if ($s1 == false ){
			$tab->add(TabSetValue::getBehavior("iNumeroCheque1", "0" ) );
			$xLog->add("ERROR\tCheque $cheque1 No existe en la cuenta $Cuenta1\r\n");
		}
	}
	if( $monto2 > 0 ){
		$xCtaBanc->set($Cuenta2);
		$s2			= $xCtaBanc->getVerificarCheque($cheque2, $monto2, true);
		$sucess		= $s2;
		if ($s1 == false ){
			$xLog->add("ERROR\tCheque $cheque2 No existe en la cuenta $Cuenta2\r\n");
			$tab->add(TabSetValue::getBehavior("iNumeroCheque2", "0" ) );
		}		
	}
	if($sucess == true ){
		if( $monto1 > 0 ){
			$xCaja->setCobroChequeInterno($Recibo, $monto1, $Cuenta1, $cheque1);
		}
		if( $monto2 > 0 ){
			$xCaja->setCobroChequeInterno($Recibo, $monto2, $Cuenta2, $cheque2);
		}
	}
	$xLog->add($xCtaBanc->getMessages(), $xLog->DEVELOPER);
	$xLog->add($xCaja->getMessages(), $xLog->DEVELOPER);
	
	$tab -> add(TabSetValue::getBehavior("avisos", $xLog->getMessages() ) );
	if($sucess == false ){
		$xRec	= new cReciboDeOperacion(false, false, $Recibo);
		if($xRec->init() == true){
			$xLog->guardar(2012, $xRec->getCodigoDeSocio(), $xRec->getCodigoDeDocumento(), $xRec->getCodigoDeRecibo());
		}
	}
	return $tab->getString();
}
function jsaGetCheque1($cheque, $cuenta){
	$xCta	= new cCuentaBancaria($cuenta);
	return $xCta->getMontoCheque($cheque);
}
function jsaGetCheque2($cheque, $cuenta){
	$xCta	= new cCuentaBancaria($cuenta);
	return $xCta->getMontoCheque($cheque);	
}

$jxc ->exportFunction('jsaSetPago', array('iRecibo', 'iNumeroCheque1', 'iCuenta1', 'iMontoCheque1', 'iNumeroCheque2', 'iCuenta2', 'iMontoCheque2' ) );
$jxc ->exportFunction('jsaGetCheque1', array('iNumeroCheque1', 'iCuenta1' ), '#iMontoCheque1' );
$jxc ->exportFunction('jsaGetCheque2', array('iNumeroCheque2', 'iCuenta2' ), '#iMontoCheque2' );
//ejecuta el script
$jxc ->process();

$xHP->init();

//=========================== HTML
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT); $recibo	= parametro("r", $recibo, MQL_INT);

if( $recibo > 0  ){
	$xRec			= new cReciboDeOperacion(false, false, $recibo);
	$xRec->init();
	$DRec			= $xRec->getDatosInArray();
	$MontoOperacion	= $DRec["total_operacion"];
	//=========================== HTML

	
	
	
	
	
	?> <style> #idavisopago, #idimporte, #iMontoRecibido, #iNumeroCheque2, #iNumeroCheque1, #iMontoCheque2, #iMontoCheque1 { font-size : 1.3em !important; } </style> <?php
	
	$xFRM	= new cHForm("frmCobrosEnCheque", "cobro-cheques.frm.php");
	$xFRM->setTitle($xHP->getTitle());
	//agrega en un hidden el idrecibo

	$xTxt	= new cHText("id");
	$xTxt2	= new cHText("id");
	$xHNot	= new cHNotif();
	$xSel	= new cHSelect();
	
	$xTxt2->setIncludeLabel(false);	
	$xTxt2->setProperty("size", "10");
	$xTxt2->setProperty("maxlength", "12");
	//$xTxt2->setProperty("class", "requiredfield");
	//
	$xTxt0	= new cHText("id");
	$xTxt0->setProperty("disabled", "true");
	
	$xSel1	= new cSelect("iCuenta1", "iCuenta1", TBANCOS_CUENTAS);
	$xSel2	= new cSelect("iCuenta2", "iCuenta2", TBANCOS_CUENTAS);
	
	$xTxt->addEvent("jsGetTotal(event)", "onkeyup");
	
	
	$xFRM->OHidden("iMontoOperacion", $MontoOperacion);
	$xFRM->OHidden("iRecibo", $recibo);
	
	$xFRM->addHElem( $xHNot->get($xHP->lang("importe") . " : " . getFMoney($MontoOperacion) . AML_CLAVE_MONEDA_LOCAL, "idimporte") );
	
	$xTxt->setDropProperty("disabled");

	
	$xTxt2->addEvent("onblur", "jsaGetCheque1");
	//$xSel1->setLabel("TR.BANCO"); $xFRM->addHElem( $xSel1->getLabel() );$xFRM->addHElem($xSel1->get());
	$xFRM->addHElem( $xSel->getListaDeCuentasBancarias("iCuenta1", true)->get(false) );
	$xFRM->addHElem( $xTxt2->getLabel("TR.CHEQUE 1") );
	$xFRM->addHElem($xTxt2->getBasic("iNumeroCheque1", 8, "required", "0"));
	$xFRM->addHElem($xTxt->getLabel("TR.MONTO 1") );
	$xFRM->addHElem($xTxt->getDeMoneda("iMontoCheque1", "", 0));
	
	
	$xTxt->addEvent("jsActualizarPago", "onblur");
	$xTxt2->addEvent("onblur", "jsaGetCheque2");
	
	//$xFRM->addHElem($xSel2->get());
	$xFRM->addHElem( $xSel->getListaDeCuentasBancarias("iCuenta2", true)->get(false) );
	$xFRM->addHElem( $xTxt2->getLabel("TR.CHEQUE 2") );
	$xFRM->addHElem($xTxt2->getBasic("iNumeroCheque2", 8, "required", "0"));
	$xFRM->addHElem($xTxt->getLabel("TR.MONTO 2") );
	$xFRM->addHElem($xTxt->getDeMoneda("iMontoCheque2", "",0));
	
	
	
	$xTxt->setProperty("disabled", "true");
	
	$xFRM->addHElem( array("", "<div class='title'>SUMA:</div>",  $xTxt->getDeMoneda("iTotal", "", 0) ));
	
	$xFRM->addAviso($xFRM->getT("MS.MSG_REQUIERE_CHEQUE"), "avisos", false, "warning");
	
	$xFRM->addGuardar("jsActualizarPago()");
		
	echo $xFRM->get();

	//=========================== HTML
	?>
	<script>
	var xG	= new Gen();
	
	function jsGetTotal(evt){
		//xG.isNumberKey({evt:evt, callback: function(){
				var mMonto1		= parseFloat( $("#iMontoCheque1").val() );
				var mMonto2		= parseFloat( $("#iMontoCheque2").val() );
				var mTotal		= parseFloat( (mMonto1 + mMonto2) );
				$("#iTotal").val( mTotal );
		//}});
	}
	function jsActualizarPago(){
		var mReady		= true;
		var mOperacion	= flotante( $("#iMontoOperacion").val() );
		var mMonto1		= flotante( $("#iMontoCheque1").val() );
		var mMonto2		= flotante( $("#iMontoCheque2").val() );
		var mTotal		= flotante( (mMonto1 + mMonto2) );
		
		var mRemanente	= flotante( ( mOperacion - mTotal ) );
		//validar cheques
		if( mMonto1 > 0){
			if( entero($("#iNumeroCheque1").val()) == 0){
				mReady = false;
				xG.alerta({msg:"El Numero de Cheque 1 debe tener un valor Valido!!", tipo: "error"});
				$("#iNumeroCheque1").focus();
			}
		}
		if( mMonto2 > 0){
			if( entero($("#iNumeroCheque2").val()) == 0){
				mReady = false;
				xG.alerta({msg:"El Numero de Cheque 2 debe tener un valor Valido!!", tipo: "error"});
				$("#iNumeroCheque2").focus();				
			}			
		}
		if( mTotal < mOperacion){
			mReady = false;
			xG.alerta({msg:"El Monto recibido $ " + mTotal + "\r\nNo debe ser DIFERENTE\r\nAl Monto de la Operacion $ " + mOperacion, tipo: "error"});
		}
		
		if( mReady == true ){
			if ( (mRemanente  > 0) || (mRemanente  < 0) ) {
				xG.alerta({msg:"El Monto recibido $ " + mTotal + "\r\nNo debe ser DIFERENTE\r\nAl Monto de la Operacion $ " + mOperacion, tipo: "error"});
				$("#iMontoCheque1").focus();
				$("#iTotal").val( mTotal );
			} else {
				$("#iTotal").val( mTotal );
				jsaSetPago();
				xG.postajax("jsReloadF()");
			}
		}
	}
	function jsReloadF(){
		try  {
			setTimeout("parent.jsRevalidarRecibo()", 500);
		} catch ( err ){
			
		}
	}
	</script>
	<?php
	$jxc->drawJavaScript(false, true);
}
$xHP->fin();
?>
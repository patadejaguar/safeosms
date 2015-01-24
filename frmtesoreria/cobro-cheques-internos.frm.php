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
//=====================================================================================================
$xP				= new cHPage("Cobranza.- Cheques Internos", HP_FORM);
$xP->setIncludes();


$xJS			= new jsBasicForm("frmCobrosEnCheque");

//=========================== AJAX
$jxc 			= new TinyAjax();
function jsaSetPago($Recibo, $cheque1, $Cuenta1, $monto1, $cheque2, $Cuenta2, $monto2){
	$sucess		= true;
	$tab 		= new TinyAjaxBehavior();
	$xCtaBanc	= new cCuentaBancaria($Cuenta1);
	
	
	$xCaja		= new cCaja();		//$recibo, $MontoRecibido, $banco, $cheque
	if( floatval($monto1) > 0 ){
		$s1			= $xCtaBanc->getVerificarCheque($cheque1, $monto1);
		$sucess		= $s1;
		if ($s1 == false ){
			$tab -> add(TabSetValue::getBehavior("iNumeroCheque1", "0" ) );
		}
	}
	if( floatval($monto2) > 0 ){
		$xCtaBanc->set($Cuenta2);
		$s2			= $xCtaBanc->getVerificarCheque($cheque2, $monto2);
		$sucess		= $s2;
		if ($s1 == false ){
			$tab -> add(TabSetValue::getBehavior("iNumeroCheque2", "0" ) );
		}		
	}
	if($sucess == true ){
		if( floatval($monto1) > 0 ){
			$xCaja->setCobroChequeInterno($Recibo, $monto1, $Cuenta1, $cheque1);
		}
		if( floatval($monto2) > 0 ){
			$xCaja->setCobroChequeInterno($Recibo, $monto2, $Cuenta2, $cheque2);
		}
	}
	if($sucess == false ){
		$tab -> add(TabSetValue::getBehavior("avisos", $xCtaBanc->getMessages("txt") ) );
	}
	return $tab -> getString();
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

//=========================== HTML
$recibo			= isset($_GET["r"]) ? $_GET["r"] : false;
if( $recibo != false ){
	$xRec			= new cReciboDeOperacion(false, false, $recibo);
	$xRec->init();
	$DRec			= $xRec->getDatosInArray();
	$MontoOperacion	= $DRec["total_operacion"];
	//=========================== HTML
	echo $xP->getHeader();
	echo $xJS->setIncludeJQuery();
	
	$jxc ->drawJavaScript(false, true);
	echo $xP->setBodyinit();
	
	$xFrm	= new cHForm("frmCobrosEnCheque", "cobro-cheques.frm.php");
	//agrega en un hidden el idrecibo

	
	$xTxt	= new cHText("id");
	$xTxt->setIncludeLabel(false);
	
	$xTxt2	= new cHText("id");
	$xTxt2->setIncludeLabel(false);	
	$xTxt2->setProperty("size", "10");
	$xTxt2->setProperty("maxlength", "12");
	$xTxt2->setProperty("class", "requiredfield");
	//
	$xTxt0	= new cHText("id");
	$xTxt0->setProperty("disabled", "true");
	
	$xSel1	= new cSelect("iCuenta1", "iCuenta1", TBANCOS_CUENTAS);
	$xSel2	= new cSelect("iCuenta2", "iCuenta2", TBANCOS_CUENTAS);
	

	
	$xFrm->addHElem( array("", "<div class='title'>IMPORTE :</div>",  $xTxt0->getDeMoneda("iMontoOperacion", "", $MontoOperacion) ));
	
	$xTxt->setDropProperty("disabled");

	$xFrm->addHElem( array( "<div class='title'>Banco</div>", "<div class='title'>Num. Cheque</div>", "<div class='title'>Monto</div>")  );
	$xTxt2->addEvent("onblur", "jsaGetCheque1");
	$xFrm->addHElem( array( $xSel1->get(), $xTxt2->getBasic("iNumeroCheque1", 8, "required", "0"), $xTxt->getDeMoneda("iMontoCheque1", "", 0.00) ) );
	
	$xTxt->addEvent("jsActualizarPago", "onblur");
//	$xTxt->addEvent("jsActualizarPago", "onchange");

	$xTxt2->addEvent("onblur", "jsaGetCheque2");
	$xFrm->addHElem( array( $xSel2->get(), $xTxt2->getBasic("iNumeroCheque2", 8, "required", "0"), $xTxt->getDeMoneda("iMontoCheque2", "", 0.00) ) );
	
	$xTxt->setProperty("disabled", "true");
	$xFrm->addHElem( array("", "<div class='title'>SUMA:</div>",  $xTxt->getDeMoneda("iTotal", "", 0) ));
	
	
	$xFrm->addHTML("<input type='hidden' id='iRecibo' name='iRecibo' value='$recibo' />");
	$xFrm->addHTML("<textarea id='avisos' rows='2' cols='52' disabled></textarea>");
		
	echo $xFrm->get();
	echo $xP->setBodyEnd();
	//=========================== HTML
	?>
	<script type="text/javascript">
	function jsActualizarPago(){
		var mReady		= true;
		var mOperacion	= parseFloat( $("#iMontoOperacion").val() );
		var mMonto1		= parseFloat( $("#iMontoCheque1").val() );
		var mMonto2		= parseFloat( $("#iMontoCheque2").val() );
		var mTotal		= parseFloat( (mMonto1 + mMonto2) );
		
		var mRemanente	= parseFloat( ( mOperacion - mTotal ) );
		//validar cheques
		if( mMonto1 > 0){
			if( $("#iNumeroCheque1").val() == 0){
				mReady = false;
				alert("El Numero de Cheque 1 debe tener un valor Valido!!");
				$("#iNumeroCheque1").focus();
			}
		}
		if( mMonto2 > 0){
			if( $("#iNumeroCheque2").val() == 0){
				mReady = false;
				alert("El Numero de Cheque 2 debe tener un valor Valido!!");
				$("#iNumeroCheque2").focus();				
			}			
		}
		if( mReady == true ){
			if ( (mRemanente  > 0) || (mRemanente  < 0) ) {
				alert("El Monto recibido $ " + mTotal + "\r\nNo debe ser DIFERENTE\r\nAl Monto de la Operacion");
				$("#iMontoCheque1").focus();
				$("#iTotal").val( mTotal );
			} else {
				$("#iTotal").val( mTotal );
				jsaSetPago();
				if($("#aviso").val() == ""){
					try  {
						setTimeout("parent.jsRevalidarRecibo()", 500);
					} catch ( err ){
						
					}
				}

			}
		}
	}
	</script>
	<?php
	$xP->end();
}
?>
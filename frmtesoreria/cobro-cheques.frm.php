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
$xP			= new cHPage("Cobranza.- Cheques Internos", HP_FORM);
$xJS			= new jsBasicForm("frmCobrosEnCheque");
//=========================== AJAX
$jxc 			= new TinyAjax();
function jsaSetPago($Recibo, $cheque1, $banco1, $monto1, $diferencia){
	$xCaja		= new cCaja();		//$recibo, $MontoRecibido, $banco, $cheque
	$op		= $xCaja->setCobroChequeForaneo($Recibo, $monto1, $banco1, $cheque1, $diferencia);
	//saveError(DEFAULT_CODIGO_DE_ERROR, false,  $xCaja->getMessages("txt") );
}
function getLetras($total){ return ($total > 0) ? "(" . convertirletras($total) . ")" : ""; }

$jxc ->exportFunction('jsaSetPago', array('iRecibo', 'iNumeroCheque1', 'iBancos1', 'iMontoCheque1', 'iDiferencia'), '#avisos' );
$jxc ->exportFunction('getLetras', array('iMontoCheque1'), "#avisos");

$jxc ->process();

//=========================== HTML
$recibo			= isset($_GET["r"]) ? $_GET["r"] : false;
if( $recibo != false ){
	$xRec			= new cReciboDeOperacion(false, false, $recibo);
	$xRec->init();
	$DRec			= $xRec->getDatosInArray();
	$MontoOperacion		= $DRec["total_operacion"];
	//=========================== HTML
	echo $xP->getHeader();
	/*echo $xJS->setIncludeJQuery();*/
	$jxc ->drawJavaScript(false, true);
	echo $xP->setBodyinit();
	
	$xFrm	= new cHForm("frmCobrosEnCheque", "cobro-cheques.frm.php");
	//agrega en un hidden el idrecibo

	
	$xTxt	= new cHText("id");
	//$xTxt->setIncludeLabel(false);
	
	$xTxt2	= new cHText("id");
	
	
	$xSel1	= new cSelect("iBancos1", "iBancos1", TBANCOS_ENTIDADES);
	$xSel2	= new cSelect("iBancos2", "iBancos2", TBANCOS_ENTIDADES);
	$xSel2->addEspOption(SYS_NINGUNO);
	$xSel2->setOptionSelect(SYS_NINGUNO);
	
	$xFrm->addHElem( "<div class='title'>IMPORTE : $MontoOperacion</div>");
	$xTxt->addEvent("jsActualizarPago", "onkeyup");
	//$xTxt->setDropProperty("disabled");

	$xFrm->addHElem(
		array( $xSel1->get("Cheque 1.- Banco"),
		      $xTxt2->get("iNumeroCheque1", "", "Cheque 1.- Numero"),
		      $xTxt->getDeMoneda("iMontoCheque1", "Cheque 1.- Monto", 0)
		));
	$xFrm->addHElem("<div class='title'>TOTAL : <mark id='idtotal'>0</mark></div>");
	
	$xFrm->addHTML("<input type='hidden' id='iRecibo' name='iRecibo' value='$recibo' />");
	$xFrm->addHTML("<input type='hidden' id='iDiferencia' name='iDiferencia' value='0' />");
	$xFrm->addHTML("<input type='hidden' id='iTotal' name='iTotal' value='$MontoOperacion' />");
	$xFrm->addHTML("<div id='avisos'></div>");
		
	echo $xFrm->get();
	echo $xP->setBodyEnd();
	//=========================== HTML
	?>
<script>
	var oCh1	= $("#iMontoCheque1");
	var oTot	= $("#iTotal");
	var oNCh1	= $("#iNumeroCheque1");
	var onEnd	= false;
	
function initComponents(){
	oNCh1.focus(); oNCh1.select();
	onEnd		= false;
}
function jsActualizarPago(){
	var mOperacion	= flotante( oCh1.val() );
	var mTotal	= flotante( oTot.val() );
	var mMonto1	= flotante( oCh1.val() );
	var mRemanente	= flotante( ( mOperacion - mTotal ) ); //2000 - 1800 = 200
	var success	= true;
	$("#idtotal").html( mOperacion );
	if ( mOperacion >= mTotal ) {
		$("#iDiferencia").val( redondear(mRemanente) );
		getLetras();
		if ( (mRemanente  > TESORERIA_MAXIMO_CAMBIO) || (mRemanente  < 0) ) {
			alert("El Monto recibido $ " + mTotal + "\r\nNo debe ser DIFERENTE\r\nAl Monto de la Operacion");
			$("#iMontoCheque1").focus();
			success	= false;
		}
		if ($.trim(oNCh1.val()) == "") {
			alert("El Numero de cheque no puede estar vacio.");
			initComponents();
			success	= false;
		}
		if(success == true && onEnd == false){
			onEnd	= false;
			var sip	= confirm("Desea guardar el Pago?");
			if (sip) {
				onEnd	= true;
				//oCh1.val( redondear(mTotal) );
				jsaSetPago();
				try  {
					setTimeout("parent.jsRevalidarRecibo()", 500);
				} catch ( err ){ onEnd	= false; }
			} else {
				initComponents();
			}
		}		
	}
}
</script>
	<?php
	$xP->end();
}
?>
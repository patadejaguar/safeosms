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
$xHP				= new cHPage("TR.Cobranza.- Efectivo", HP_FORM);

$xHP->setTitle( $xHP->lang("cobro en", "efectivo"));

$xJS			= new jsBasicForm("frmCobrosEnEfectivo");

//=========================== AJAX
$jxc 			= new TinyAjax();
function jsaSetPago($Recibo, $MontoRecibido, $Moneda, $MontoOriginal){
	$xCaja			= new cCaja();
	
	if($Moneda != AML_CLAVE_MONEDA_LOCAL ){
		$op		= $xCaja->setCobroEfectivo($Recibo, $MontoOriginal, 0, "", false, $Moneda, $MontoRecibido);
	} else {
		$op		= $xCaja->setCobroEfectivo($Recibo, $MontoRecibido, 0, "", false, $Moneda, $MontoOriginal);
	}
	
	if ( $op == false ){
		return $xCaja->getMessages(OUT_HTML);
	}
}
$jxc ->exportFunction('jsaSetPago', array('iRecibo', 'iMontoRecibido', 'idcodigodemoneda', 'montonormal'), '#avisos' );
//ejecuta el script
$jxc ->process();


$xHSel				= new cHSelect();
//=========================== HTML
$recibo			= isset($_GET["r"]) ? $_GET["r"] : false;
if( $recibo != false ){
	$xRec			= new cReciboDeOperacion(false, false, $recibo); $xRec->init();
	$DRec			= $xRec->getDatosInArray();
	$MontoOperacion	= $DRec["total_operacion"];
	//=========================== HTML
	echo $xHP->getHeader();
	//echo $xJS->setIncludeJQuery();
	$jxc ->drawJavaScript(false, true);
	echo $xHP->setBodyinit("initComponents()");
	
	
	?> <style> #idavisopago, #idimporte, #iMontoRecibido { font-size : 1.3em !important; } </style> <?php
	
	$xFRM	= new cHForm("frmCobrosEnEfectivo", "cobro-efectivo.frm.php");
	$xFRM->addGuardar("jsActualizarPago()");
	//agrega en un hidden el idrecibo
	$xHNot	= new cHNotif();
	$xTxt	= new cHText("id");
	$xTxt->addEvent("this.select()", "onfocus");
	$xTxt->addEvent("jsActualizarPago()", "onblur");
	$xTxt->addEvent("jsSetEvalMonto(event, this)", "onkeyup");
	
	
	$xFRM->addHElem( $xHNot->get($xHP->lang("importe") . " : " . getFMoney($MontoOperacion) . AML_CLAVE_MONEDA_LOCAL, "idimporte") );
	
	$xFRM->OHidden("iMontoOperacion", $MontoOperacion, "");

	$SMoneda		= $xHSel->getListaDeMonedas("idcodigodemoneda");
	$SMoneda->addEvent("onblur", "jsGetEquivalenciasA");
	$xFRM->addHElem( $SMoneda->get($xHP->lang("Moneda"), true ) );
	
	$xFRM->addHElem( $xTxt->getDeMoneda("iMontoRecibido", "TR.Monto recibido", $MontoOperacion) );	
	
	$xFRM->addHElem( $xHNot->get($xHP->lang("Cambio") . " : <mark id='idtotal'>0</mark>" . AML_CLAVE_MONEDA_LOCAL, "idavisopago", $xHNot->WARNING) );
	$xFRM->addHElem( $xHNot->get($xHP->lang("Cotizacion") . " : <mark id='idequivalente'>0</mark>", "idavisopago", $xHNot->SUCCESS) );
	
	$xFRM->OHidden("iMontoCambio", 0, "");
	$xFRM->OHidden("idTipoCambio", 1, "");
	
	$xFRM->addHTML("<div id='avisos'></div>");
	
	$xFRM->addFootElement("<input type='hidden' name='montonormal' id='montonormal' value='$MontoOperacion' />");
	$xFRM->addFootElement("<input type='hidden' id='iRecibo' name='iRecibo' value='$recibo' />");
	
	echo $xFRM->get();
	echo $xHP->setBodyEnd();
	//=========================== HTML
	?>
	<script>
	var onCobro		= false;
	var xG			= new Gen();
	var txt			= ""; 
	function initComponents(){
		setTimeout("setFocal()", 500);
	}
	function jsGetEquivalenciasA(){
		var mMoneda 	= $("#idcodigodemoneda").val();
		xG.equivalencia({ moneda : mMoneda, monto : $("#montonormal").val(), callback : jsEnValorOriginal });		
	}
	function jsEnValorOriginal(obj){
		var mval	= redondear( flotante($("#montonormal").val()) / flotante(obj.cotizacion) );
		$("#iMontoRecibido").val( mval );
		$("#idequivalente").html( obj.cotizacion );
	}
	function jsActualizarPago(){
		var mRemanente	=  redondear( flotante($("#montonormal").val()) - flotante($("#iMontoOperacion").val()) );
		
		if ( mRemanente  < 0) {
			alert("El Monto recibido\nNo debe ser menor\nAl Monto de la Operacion");
			$("#iMontoCambio").val( 0 );
			onCobro		= false;
			setTimeout("setFocal()", 500);
		} else {
			if(onCobro == false){
				$("#iMontoCambio").val( mRemanente );
				$("#idavisopago").removeClass( "warning" ).addClass( "success" );
				$("#idtotal").html( getInMoney(mRemanente) );
				var sip			= confirm("Desea Guardar el Pago?\n" + txt);
				if (sip) {
					onCobro		= true;
					jsaSetPago();
					setTimeout("parent.jsRevalidarRecibo()", 500);
				} else {
					onCobro		= false;
					setTimeout("setFocal()", 500);
				}
			}
		}
	}
	function setFocal(){ $("#idcodigodemoneda").focus(); }
	function jsSetEvalMonto(evt, obj){ xG.isNumberKey({ evt : evt, callback : 'jsGetEquivalencias()'});  }
	function setValorOriginal(obj){ //monto, letras valor contizacion
		$("#montonormal").val(obj.equivalencia);
		$("#avisos").html(obj.letras);
		//txt				= "Por : " +  obj.letras;
	}
	function jsGetEquivalencias(){
		var mMoneda 	= $("#idcodigodemoneda").val();
		xG.equivalencia({ moneda : mMoneda, monto : $("#iMontoRecibido").val(), callback : setValorOriginal });
	}
	</script>
	<?php
	$xHP->end();
}
?>
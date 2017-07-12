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
$xHP		= new cHPage("Cobranza.- Transferencia");
$xJS		= new jsBasicForm("frmCobrosEnCheque");
//=========================== AJAX
$jxc 		= new TinyAjax();
$recibo		= parametro("idrecibo", 0, MQL_INT); $recibo		= parametro("r", $recibo, MQL_INT); $recibo		= parametro("recibo", $recibo, MQL_INT);

function jsaSetPago($Recibo, $cuentabancaria, $monto1, $diferencia, $fecha, $transaccion, $bancodeorigen){
    $xF		= new cFecha(); $fecha	= $xF->getFechaISO($fecha);
    $xCaja	= new cCaja(); $op		= $xCaja->setCobroTransferencia($Recibo, $cuentabancaria, $monto1, $diferencia, $fecha, "", false, false, $transaccion, $bancodeorigen);
    if(MODO_DEBUG == true){ setLog($xCaja->getMessages()); }
}
function getLetras($total){ return ($total > 0) ? "(" . convertirletras($total) . ")" : ""; }


$jxc ->exportFunction('getLetras', array('iMonto'), "#avisos");
$jxc ->exportFunction('jsaSetPago', array('iRecibo', 'iBancos', 'iMonto', 'iDiferencia', 'idfecha-0', "idtipodeperfil", "idcodigodebanco"), '#avisos' );
$jxc ->process();
$xHP->init();

if(setNoMenorQueCero($recibo) <= 0){ header ("location:../404.php?i=" . DEFAULT_CODIGO_DE_ERROR); }
?> <style> #idavisopago, #idimporte, #iMonto { font-size : 1.3em !important; } </style> <?php
if( setNoMenorQueCero($recibo) >0 ){
	$xRec			= new cReciboDeOperacion(false, false, $recibo);
	$xRec->init();
	$DRec			= $xRec->getDatosInArray();
	$MontoOperacion		= $xRec->getTotal();// $DRec["total_operacion"];
	
    $xFRM	= new cHForm("frmCobrosEnEfectivo", "cobro-efectivo.frm.php");
    $xTxt	= new cHText("id");
    $xDat	= new cHDate();
    $xHSel	= new cHSelect();
    $xHNot	= new cHNotif();
    $xFRM->addGuardar("jsActualizarPago()");
    $xTxt->addEvent("this.select()", "onfocus");
    $xTxt->addEvent("jsActualizarPago", "onkeyup");
    $xFRM->addHElem( $xHSel->getListaDeCuentasBancarias("iBancos", true)->get( "TR.Cuenta Bancaria de Deposito", true) );
    $xFRM->addHElem( $xHNot->get($xHP->lang("importe") . " : " . getFMoney($MontoOperacion), "idimporte") );
    if(MODULO_AML_ACTIVADO == true){
    	$xFRM->addHElem( $xHSel->getListaDeTipoDePerfilTransaccional("", SYS_SALIDAS)->get(true) );
    	$xFRM->addHElem( $xHSel->getListaDeBancos()->get("TR.Banco de Origen", true) );
    } else {
    	$xFRM->OHidden("idtipodeperfil", AML_OPERACIONES_PAGOS_EFVO);
    	$xFRM->OHidden("idcodigodebanco", FALLBACK_CLAVE_DE_BANCO);
    }
    
    //TODO: Agregar cuenta de origen ultimo 4 digitos
    $xFRM->addHElem( $xDat->get("TR.Fecha de Deposito", $xRec->getFechaDeRecibo()) ); 
    $xFRM->addHElem( $xTxt->getDeMoneda("iMonto", $xHP->lang("Monto de", "Deposito"), 0) ); 
    
    $xFRM->addHElem( $xHNot->get($xHP->lang("total") . " : <mark id='idtotal'>0</mark>", "idavisopago", $xHNot->WARNING) );
    
    
    $xFRM->addHTML("<input type='hidden' id='iRecibo' name='iRecibo' value='$recibo' />");
    $xFRM->addHTML("<input type='hidden' id='iDiferencia' name='iDiferencia' value='0' />");
    $xFRM->addHTML("<input type='hidden' id='iTotal' name='iTotal' value='$MontoOperacion' />");
    $xFRM->addHTML("<div id='avisos'></div>");
	    
    echo $xFRM->get();
    $jxc ->drawJavaScript(false, true);
}
$xHP->setBodyEnd();
?>
<script>
	var oMnt	= $("#iMonto");
	var oTot	= $("#iTotal");
	var oFecha	= $("#idfecha-0");
	var oBanc	= $("#iBancos");
	var onEnd	= false;
	var xG		= new Gen();
function initComponents(){
	oBanc.focus(); oBanc.select();
	onEnd		= false;
}
function jsActualizarPago(){
	var mOperacion	= flotante( oMnt.val() );
	var mTotal	= flotante( oTot.val() );
	var mRemanente	= flotante( ( mOperacion - mTotal ) ); //2000 - 1800 = 200
	var success	= true;
	$("#idtotal").html( mOperacion );
	if ( mOperacion >= mTotal ) {
		$("#iDiferencia").val( redondear(mRemanente) );
		getLetras();
		if ( (mRemanente  > TESORERIA_MAXIMO_CAMBIO) || (mRemanente  < 0) ) {
			alert("El Monto recibido $ " + mTotal + "\r\nNo debe ser DIFERENTE\r\nAl Monto de la Operacion");
			oMnt.focus();
			success	= false;
		}
		if (oBanc.val() == FALLBACK_CUENTA_BANCARIA) {
		    //no aceptar cuenta bancaria por defecto
		    alert("CUENTA BANCARIA INVALIDA.\nCAPTURE NUEVAMENTE EL DATO");
		    success	= false;
		    oBanc.focus();
		}
		if(success == true && onEnd == false){
			onEnd	= true;
			$("#idavisopago").removeClass( "warning" ).addClass( "success" );
			var sip	= confirm("Desea guardar el Pago?");
			if (sip) {
				//oCh1.val( redondear(mTotal) );
				jsaSetPago();
				try  {
					setTimeout("parent.jsRevalidarRecibo()", 500);
				} catch ( err ){}
			} else {
				initComponents();
			}
		}		
	}
}
</script>
<?php
echo $xHP->end();
?>
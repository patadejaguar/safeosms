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
$xHP		= new cHPage("TR.Cobranza .- CARGO_DOCUMENTO");
$xHP->init();



$recibo	= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT); $recibo	= parametro("r", $recibo, MQL_INT);
$xFRM	= new cHForm("frmcobranza", "cobro-cargo-documento.frm.php");
$xHNot	= new cHNotif();
$xFRM->setTitle($xHP->getTitle());
$xRec			= new cReciboDeOperacion(false, false, $recibo);
$xFRM->addGuardar("jsSetGuardarPago()");
?> <style> #idavisopago, #idimporte, #iMontoRecibido { font-size : 1.3em !important; } </style> <?php //Style
if($xRec->init() == true){ 
	
	$DRec			= $xRec->getDatosInArray();
	$MontoOperacion	=  $xRec->getTotal();// $DRec["total_operacion"];
	$xFRM->OHidden("iMontoOperacion", $MontoOperacion);
	
	$xFRM->addHElem( $xHNot->get($xHP->lang("importe") . " : " . getFMoney($MontoOperacion) . AML_CLAVE_MONEDA_LOCAL, "idimporte") );
	$xFRM->OMoneda("iMontoRecibido", 0, "TR.Monto Recibido");
	
	$xFRM->OHidden("idrecibo", $recibo);
	$xFRM->OHidden("iMontoCambio", 0);
	$xFRM->OHidden("idTipoCambio", 1);
}
echo $xFRM->get();
?>
<script>
var xTes	= new TesGen();
var xG		= new Gen();
function jsSetGuardarPago(){
	var idrecibo	= $("#idrecibo").val();
	var idmonto		= $("#iMontoRecibido").val();
	var idtipo		= "<?php echo TESORERIA_COBRO_DOCTO; ?>";
	xTes.setAgregarPago({ recibo: idrecibo, tipo: idtipo, monto: idmonto, callback:jsSalir});
}
function jsSalir(){
	xG.close();
}
</script>
<?php
$xHP->fin();
?>
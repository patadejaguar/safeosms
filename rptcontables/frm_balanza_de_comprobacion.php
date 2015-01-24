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
$xHP		= new cHPage("TR.Balanza de Comprobacion", HP_FORM);
$xHP->init();

$xSel		= new cHSelect();
$xHRPT		= new cPanelDeReportesContables();
$xHRPT->addCuentaFinal();
$xHRPT->addMoneda();
$xHRPT->addTipoDeCuentas();
$xHRPT->addNivelesDeCuentas();
$xHRPT->addEstadoDeMovimiento();
echo $xHRPT->render(); 
?>
<script>
var xG 	= new Gen();
function jsGetReporte(){
	var mPer		= $("#idperiodoinicial").val();
	var mEjer		= $("#idejercicioinicial").val();
	var vMone		= $("#idcodigodemoneda").val();
	var vctainit	= $("#idcuentainicial").val();
	var vctafin		= $("#idcuentafinal").val();
	var vtipo		= $("#idtipodecuentacontable").val();
	var vnivel		= $("#idniveldecuenta").val();
	var edomvto		= $("#idestadomvto").val();
	
	var urlrpt 		= "rpt_balanza_de_comprobacion.php?periodo=" + mPer + '&ejercicio=' + mEjer + '&moneda=' + vMone + '&for=' + vctainit + '&to=' + vctafin + "&nivel=" + vnivel + "&tipo=" + vtipo + "&estado=" + edomvto ;
	xG.w({ url : urlrpt });
}
</script>
<?php
$xHP->fin();

?>
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
$xHP		= new cHPage("TR.Movimientos de Auxiliares del Catalogo", HP_FORM);
$xHP->init();

$xSel		= new cHSelect();
$xHRPT		= new cPanelDeReportesContables(false, false);
$xHRPT->OFRM()->setTitle($xHP->getTitle());

$xHRPT->addFechaInicial();
$xHRPT->addFechaFinal();
$xHRPT->addCuentaInicial();
$xHRPT->addCuentaFinal();
$xHRPT->addTipoDeCuentas();

$xHRPT->addEstadoDeMovimiento();

$xHRPT->addMoneda();

echo $xHRPT->render();
?>
<script>
var xG 	= new Gen();
function jsGetReporte(){
	var mFechaF		= $("#idfechafinal").val();
	var mFechaI		= $("#idfechainicial").val();
	var vMone		= $("#idcodigodemoneda").val();
	var vctainit	= $("#idcuentainicial").val();
	var vctafin		= $("#idcuentafinal").val();
	var vtipo		= $("#idtipodecuentacontable").val();
	//var vnivel		= $("#idniveldecuenta").val();
	var edomvto		= $("#idestadomvto").val();
	
	var urlrpt 		= "rpt_movimientos_de_auxiliares2.php?on=" + mFechaI + '&off=' + mFechaF + '&moneda=' + vMone + '&for=' + vctainit + '&to=' + vctafin + "&tipo=" + vtipo + "&estado=" + edomvto ;
	xG.w({ url : urlrpt });
}
</script>
<?php
$xHP->fin();

?>
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
$xHP		= new cHPage("TR.estado_de_resultados", HP_FORM);
$xHP->init();

$xSel		= new cHSelect();
$xHRPT		= new cPanelDeReportesContables(true, false);
$xHRPT->OFRM()->setTitle($xHP->getTitle());
$xHRPT->addMoneda();

echo $xHRPT->render();
?>
<script>
var xG 	= new Gen();
function jsGetReporte(){
	var mPer		= $("#idperiodoinicial").val();
	var mEjer		= $("#idejercicioinicial").val();
	var vMone		= $("#idcodigodemoneda").val();

	//var urlrpt = "rpt_balance_general2.php?on=" + fi + '&for=' + vfor+ '&m=' + vMoneda + '&f=' + vFactor;
	var urlrpt 		= "rpt_estado_resultados2.php?periodo=" + mPer + '&ejercicio=' + mEjer + '&moneda=' + vMone;
	xG.w({ url : urlrpt });
}
</script>
<?php
$xHP->fin();
?>
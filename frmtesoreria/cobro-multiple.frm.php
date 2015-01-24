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
$xP			= new cHPage("Cobranza.- Efectivo");
$xJS			= new jsBasicForm("frmCobrosEnCheque");
//=========================== AJAX
$jxc 			= new TinyAjax();

$jxc ->exportFunction('getLetras', array('iMontoCheque1'), "#avisos");
function getLetras($total){ return ($total > 0) ? "(" . convertirletras($total) . ")" : ""; }
$xP->setBodyinit();
$jxc ->exportFunction('getLetras', array('iMontoCheque1'), "#avisos");
$jxc ->process();


$xFrm	= new cHForm("frmCobrosEnEfectivo", "cobro-efectivo.frm.php");
$xTxt	= new cHText("id");
$xDat	= new cHDate();
$xSel1	= new cSelect("iBancos1", "iBancos1", TBANCOS_ENTIDADES);

	$xFrm->addHElem(
		array(
		    $xSel1->get("Banco de deposito"),
		    $xDat->get("Fecha de Deposito"),
		    $xTxt->getDeMoneda("iMonto", "Monto", 0)
	));
	
	$xFrm->addHElem("<div class='title'>TOTAL : <mark id='idtotal'>0</mark></div>");
	
	$xFrm->addHTML("<input type='hidden' id='iRecibo' name='iRecibo' value='$recibo' />");
	$xFrm->addHTML("<input type='hidden' id='iDiferencia' name='iDiferencia' value='0' />");
	$xFrm->addHTML("<input type='hidden' id='iTotal' name='iTotal' value='$MontoOperacion' />");
	$xFrm->addHTML("<div id='avisos'></div>");
	
echo $xFrm->get();
$xP->setBodyEnd();

?>
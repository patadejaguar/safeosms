<?php
/**
 * @see Ministracion de Creditos
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package creditos
 *  Forma para enviar datos de Creditos a Ministraciom
 * 		06Junio08 	- se Agrego Mejor presentacion
 *					-
 *
 */
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
$xHP		= new cHPage("TR.Desembolso de Creditos", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
$xCaja		= new cCaja();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){ header ("location:../404.php?i=200"); }

function jsaSetUltimoCheque($persona, $credito, $cuenta_bancaria){
	$xBanc		= new cCuentaBancaria($cuenta_bancaria);
	$cheque 	= $xBanc->getUltimoCheque();
	$xCred		= new cCredito($credito); $xCred->init();
	$montocheque= $xCred->getMontoAutorizado();
	$tab = new TinyAjaxBehavior();
	$tab -> add(TabSetValue::getBehavior("idnumerocheque", $cheque));
	$tab -> add(TabSetValue::getBehavior("idmontocheque", $montocheque));
	$tab -> add(TabSetValue::getBehavior("idmontooriginal", $montocheque));
	return $tab -> getString();	
}
function jsaCargaDeCreditos($persona){
	$xL		= new cSQLListas();
	$ql		= new MQL();
	$xs		= new cHSelect();
	$xs->setDivClass("");
	$sql 	= $xL->getListadoDeCreditos($persona);
	$rs		= $ql->getDataRecord($sql);
	$aOpts	= array();
	//setLog($sql);
	foreach ($rs as $row){
		$aOpts[$row["credito"]]	 = $row["credito"] . "-" . $row["producto"] . "-" . $row["periocidad"] . "-" . $row["saldo"];   
	}
	$xs->addOptions($aOpts);
	return $xs->get("idcreditodescontado", "TR.CLAVE_de_credito");
}
$jxc ->exportFunction('jsaSetUltimoCheque', array('idsocio', 'idsolicitud', 'idcodigodecuenta' ));
$jxc ->exportFunction('jsaCargaDeCreditos', array('idsocio'), "#iddivcreditos");
$jxc ->process();

echo $xHP->getHeader();

$jxB	= new jsBasicForm("frmministracion");
$jxB->setEstatusDeCreditos(CREDITO_ESTADO_AUTORIZADO);

$xFRM		= new cHForm("frmministracion", "clscreditosministracion.php");
$xSel		= new cHSelect();
$xTxM		= new cHText(); $xTxM->setDivClass("");
$xTxtMC		= new cHText(); $xTxM->addEvent("jsUpdateCheque()", "onfocus");
$msg		= "";
$xFRM->addDataTag("role", "ministracion");
$xFRM->addCreditBasico();
$xFRM->addSubmit();
//descuento //comisiones
$xFRM->addHElem("<h3>" . $xFRM->lang("Descuentos") . "</h3>");
$xFRM->addDivSolo(" ", $xTxM->getDeMoneda("idmontocreditodescontado", "TR.Descuento"), "tx2", "tx2", array( 1=> array("id" => "iddivcreditos")) );
$xFRM->addDivSolo(" ", $xTxM->getDeMoneda("idmontocomisiondescontado", "TR.Comision"), "tx2", "tx2" );
//fragmentacion del cheque

$xFRM->addHElem("<h3>" . $xFRM->lang("Cheque") . "</h3>");
$xFRM->addHElem( $xSel->getListaDeCuentasBancarias("", true)->get(true) );
$xFRM->ODate("idfechaactual", false, "TR.Fecha de otorgacion");
$xFRM->OText("idnumerocheque", "", "TR.Codigo de Cheque");

$xFRM->addHElem($xTxtMC->getDeMoneda("idmontocheque", "TR.Monto del cheque", 0, true) );

$xFRM->addHElem("<h3>" . $xFRM->lang("Otros") . "</h3>");
$xFRM->OText("idfoliofiscal", "", "TR.Folio Impreso");
$xFRM->addObservaciones();
$xFRM->OHidden("idmontooriginal", 0, "");

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
echo $jxB->get();
?>
<script>
var gn			= new Gen();
var val			= new ValidGen();
var errors		= 0;
$(document).ready(function () {
	$('#id-frmministracion').isHappy({
	    fields: {
		   "#idsolicitud" : {
			   test: jsLoadVals
		   },
		   "#idmontocreditodescontado" : {
			   test : jsUpdateCheque
		   },
		   "#idmontocomisiondescontado" : {
			   test : jsUpdateCheque
		   }
	    }
	  });	
});
function jsLoadVals(){
	jsaSetUltimoCheque();
	jsaCargaDeCreditos();
	return true;
}
function jsUpdateCheque(){
	$("#idmontocheque").val( $("#idmontooriginal").val() - $("#idmontocreditodescontado").val() - $("#idmontocomisiondescontado").val() );
	return true;
}
</script>
</body>
</html>

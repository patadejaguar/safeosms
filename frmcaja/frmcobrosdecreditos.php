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

$xHP		= new cHPage("TR.RECIBO DE PAGO", HP_FORM);


$xCaja		= new cCaja();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	$xHP->goToPageError(200); }

$jxc = new TinyAjax();
/**
 * Verifica si Existe el Plan de Pagos
 *
 * @param integer $idcred
 * @return string Mensaje de Descripcion del Estatus del Credito
 */
function jsaGetVerifyPlan($idcred){
	$idcred		= setNoMenorQueCero($idcred);
	if($idcred > DEFAULT_CREDITO){
		$xCred		= new cCredito($idcred); $xCred->init();
		
		if($xCred->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
			$plan		= $xCred->getNumeroDePlanDePagos();
			if($plan == false){
				return $xCred->getMessages(OUT_HTML);
			} else {
				return "";
			}
		}
	}
}
function jsaGetLetras($idcredito){
	$idcred		= setNoMenorQueCero($idcredito);
	$xF			= new cFecha();
	if($idcred > DEFAULT_CREDITO){
		$xCred		= new cCredito($idcred); $xCred->init();
		if($xCred->getEsAfectable() == false OR $xCred->getSaldoActual() <= 0){
			
			if(MODO_CORRECION == true){
				$xTxt		= new cHText();
				$xTxt->setDivClass("");
				return $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad");
			} else {
				return $xCred->getMessages();
			}			
		} else {
			if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
				//
				$xTxt		= new cHText();
				$xTxt->setDivClass("");
				return $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad");
			} else {
				$plan		= $xCred->getNumeroDePlanDePagos();
				if($plan != false){
					$xPlan		= new cPlanDePagos($plan); $xPlan->init();
					$parcs		= $xPlan->getParcsPendientes();
					//$txt		= "";
					$arrD		= array();
					foreach ($parcs as $p){
						//setLog( $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($p[SYS_TOTAL]));
						if( setNoMenorQueCero($p[SYS_TOTAL]) > 0){ $arrD[$p[SYS_NUMERO]]	= $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($p[SYS_TOTAL]); }
					}
					$xSel		= new cHSelect();
					$xSel->addOptions($arrD);
					$xSel->setEnclose(false);
					return $xSel->get("idparcialidad", "TR.Numero de Parcialidad", $xCred->getPeriodoActual()+1);
				} else {
					if(MODO_CORRECION == true){
						$xTxt		= new cHText();
						$xTxt->setDivClass("");
						return $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad");
					}
				}
			}
		}
	}
}
function getPagoCompleto($solicitud, $socio){ $sqlST = ""; }

function jsaGetLetrasAVencerTodas($fecha){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha	= $xD->getFechaISO($fecha);
	$sql	= $xL->getListadoDeLetrasConCreditos_Simple($fecha, false, "", "", "", true);//, false, "", "", " AND (`creditos_tipoconvenio`.`tipo_en_sistema` =$producto) ");

	$xT		= new cTabla($sql, 2);
	$xT->setWithMetaData(true);
	//$xT->setClassT("")
	$xT->setEventKey("jsCargarCredito");
	return $xT->Show();
}

//$jxc ->exportFunction('setSwToolbar', array('solicitud'), "#space-to-toolbar");
$jxc ->exportFunction('jsaGetVerifyPlan', array("idsolicitud"), "#aviso");
$jxc ->exportFunction('jsaGetLetrasAVencerTodas', array("idfecha-0"), "#lst");
$jxc ->exportFunction('jsaGetVerifyPlan', array("idsolicitud"), "#aviso");

$jxc ->exportFunction('jsaGetLetras', array("idsolicitud"), "#divparcialidad");

//$jxc ->exportFunction('setPagoCompleto', array("solicitud", "idsocio"), "#parseValueX");

$jxc ->process();

echo $xHP->getHeader();
echo $xHP->setBodyinit("initComponents()");

$jbf    	= new jsBasicForm("frmPreRecibo");
$jbf->setIncludeJQuery();
$jbf->mIncludeCreditos = true;
$jxc->drawJavaScript(false, true);


$xFRM		= new cHForm("frmPreRecibo", "frmcobrosdecreditos2.php");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$msel		= $xSel->getListaDeProductosDeCredito();
$msel->addEvent("onchange", "initComponents()");

$xDate->setDivClass("");
$xFRM->addDivSolo($xDate->get("TR.Fecha"), "<div id='mscom'></div>", "tx14", "tx34");
$xFRM->addCreditBasico();

$xTxt->addEvent("jsaGetLetras();jsaGetVerifyPlan();", "onfocus");

$xTxt->setDivClass("");
$props	= array( 1 => array("id" => "divparcialidad"));
$xFRM->addDivSolo($xTxt->get("idparcialidad", "", "TR.Numero de Parcialidad", "", false, CTRL_GOLETRAS),"<p class='aviso' id='aviso'></p>" , "tx14", "tx34", $props);

$xDate->addEvents("onblur=\"initComponents()\" onchange=\"initComponents()\" ");

$xFRM->addHTML("<div id='lst' class='inv'></div>");

$xFRM->addSubmit("", "setFrmSubmit()");

$xFRM->addToolbar($xBtn->getBasic("TR.Letras Pendientes", "jsObtenerLetras()", "ejecutar", "cmdGetLetras", false));
$xFRM->addToolbar($xBtn->getBasic("TR.panel_de_control_de", "jsGoPanel()", "panel", "idgetpanel", false));
$xFRM->addToolbar($xBtn->getBasic("TR.Estado de Cuenta", "getEdoCtaCredito()", "reporte", "cmdEdoCta", false));

echo $xFRM->get();
?>
</body>
<?php
$jbf->show();
?>
<script>
var Wo			= new Gen();
var onAsClicked = false;
var mCURFrm 	= document.getElementById("id-frmPreRecibo");
var jrsFileE 	= "./jscaja.js.php";

function setFrmSubmit(){
	var success	= true;
	if(entero($("#idsolicitud").val()) <= 0||entero($("#idsocio").val()) <= 0){
		alert( Wo.lang("Falta el el codigo de persona o de el credito") );
	}
	if( $('#idparcialidad').length > 0){
		var idx	= $("#idparcialidad");
		if ( entero(idx.val()) <= 0 ) {
			alert( Wo.lang("Falta el numero Parcialidad") );
			idx.focus();
			success	= false;
		}
	} else {
		alert( Wo.lang("Falta el numero Parcialidad") );
		success		= false;
	}
	

	if (success == true ) {
		//mCURFrm.action = "frmcobrosdecreditos2.php?m=" + sIs;
		mCURFrm.submit();
	}
}
function getEdoCtaCredito(){ Wo.w({url: "../rpt_edos_cuenta/rptestadocuentacredito.php?pb=" + $("#idsolicitud").val()}); }
function jsGoPanel(){ Wo.w({url: "../frmcreditos/creditos.panel.frm.php?idsolicitud=" + $("#idsolicitud").val() + "&idsocio=" + $("#idsocio").val()}); }
function initComponents(){	jsaGetLetrasAVencerTodas(); }
function jsCargarCredito(credito){
	var id 		= "#tr-letras-" + credito;
	var mObj	= processMetaData(id);

	jsCancelarAccion();
	$("#idsocio").val(mObj.codigo);
	$("#idsolicitud").val(mObj.credito);
	jsaGetLetras();jsaGetVerifyPlan();
	//TODO: Modificar
	$("#idparcialidad").val(entero(mObj.periodo));
	//frmSubmit(false);
	
	envsoc(); envsol();// envparc();
}
function getSetLetra(){}
function jsGetLetras(){	jsObtenerLetras(); }
function jsEvaluarSalida(evt){
	jsaGetLetras();jsaGetVerifyPlan();
}
function jsObtenerLetras(){
	jsaGetLetrasAVencerTodas();
	Wo.winTip({ element : "#idfecha-0",  content : $("#lst"), title: Wo.lang(["letras"]) });
	//getModalTip(window, $("#lst"), Wo.lang(["letras"]));
}
function jsCancelarAccion(){	$("#idfecha-0").qtip("hide");    }
</script>
</html>
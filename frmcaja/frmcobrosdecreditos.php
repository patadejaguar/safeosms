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

$xHP		= new cHPage("TR.COBRO DE CREDITOS", HP_FORM);
$xCaja		= new cCaja();
$xEvt		= new cCreditosEventos();
$xRuls		= new cReglaDeNegocio();

$ConVendedor= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_CON_VENDEDOR);

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
	$xRuls		= new cReglaDeNegocio();
	
	if($idcred > DEFAULT_CREDITO){
		$xCred		= new cCredito($idcred); 
		if($xCred->init() == true){
			$idprox		= $xCred->getPeriodoActual()+1;
			
			if($xCred->getEsAfectable() == false OR $xCred->getSaldoActual() <= 0){
				$xCred->setRevisarSaldo();
				if(MODO_CORRECION == true){
					$xTxt		= new cHText();
					$xTxt->setDivClass("");
					return $xTxt->getNumero("idparcialidad", $idprox, "TR.Numero de Parcialidad");
				} else {
					return $xCred->getMessages();
				}			
			} else {
				if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
					//
					$xTxt		= new cHText();
					$xTxt->setDivClass("");
					return $xTxt->getNumero("idparcialidad", $idprox, "TR.Numero de Parcialidad");
				} else {
					$LetraFija	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PAGO_LETRAF);
					
					if($LetraFija == true){
						$xTxt		= new cHText();
						$xNot		= new cHNotif();
						
						return $xTxt->getHidden("idparcialidad", false, $idprox) . $xNot->get($idprox);
					} else {
					
						$plan		= setNoMenorQueCero($xCred->getNumeroDePlanDePagos());
						if($plan <= 0){
							if(MODO_CORRECION == true){
								$xTxt		= new cHText();
								$xTxt->setDivClass("");
								return $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad");
							}
						} else {
							$xPlan		= new cPlanDePagos($plan); $xPlan->init();
							$parcs		= $xPlan->getParcsPendientes();
							//$txt		= "";
							$arrD		= array();
							foreach ($parcs as $p){
								//setLog( $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($p[SYS_TOTAL]));
								if( setNoMenorQueCero($p[SYS_TOTAL]) > 0){ 
									$neto	= $p[SYS_TOTAL];
									if($p[SYS_VARIOS] >0){
										$neto	+= $p[SYS_VARIOS]; 
									}
									$arrD[$p[SYS_NUMERO]]	= $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($neto);
								}
							}
							if($xCred->getPagosSinCapital() == true){
								if(!isset( $arrD[$xCred->getPeriodoActual()] )){
									$arrD[$xCred->getPeriodoActual()]	= $xCred->getPeriodoActual() . " - Pagos a Capital Letra Anterior"; 
								}
							}						
							$xSel		= new cHSelect();
							$xSel->addOptions($arrD);
							$xSel->setEnclose(false);
							return $xSel->get("idparcialidad", "TR.Numero de Parcialidad", $xCred->getPeriodoActual()+1);
						}
					}
				}
			}
		}
	}
}

function jsaGetLetrasAVencerTodas($fecha = false){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	
	$fecha	= $xD->getFechaISO($fecha);
	$filtro	= " AND (`creditos_solicitud`.`saldo_actual`> " . TOLERANCIA_SALDOS .  ") AND (`creditos_tipoconvenio`.`omitir_seguimiento` =0) ";
	$sql	= $xL->getListadoDeLetrasConCreditos_Simple($fecha, false, "", "", $filtro, true);
	$xT		= new cTabla($sql, 2);
	$xT->setWithMetaData(true);
	$xT->setEventKey("jsCargarCredito");
	return $xT->Show();
}

function jsaGetLetrasVencidas($fecha){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$xFil	= new cSQLFiltros();
	
	$BySaldo		= $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">");
	//Agregar seguimiento
	$BySaldo		= $BySaldo . $xFil->CreditosProductosPorSeguimiento(0);
	$sql			= $xL->getListadoDeLetrasPendientesReporteAcum($BySaldo, TASA_IVA, true, false, false);
	
	$xT				= new cTabla($sql, 2);
	
	$xT->setWithMetaData();
	$xT->setOmitidos("capital");
	$xT->setOmitidos("interes");
	$xT->setOmitidos("iva");
	$xT->setOmitidos("otros");
	$xT->setOmitidos("monto_ministrado");
	
	$xT->setOmitidos("moratorio");
	$xT->setOmitidos("iva_moratorio");
	$xT->setOmitidos("total");
	
	$xT->setTitulo("letra_original", "TR.TOTAL");
	
	$xT->setEventKey("jsCargarCredito2");
	
	return $xT->Show( );
}


$jxc ->exportFunction('jsaGetVerifyPlan', array("idsolicitud"), "#aviso");
$jxc ->exportFunction('jsaGetLetrasAVencerTodas', array("idfecha-0"), "#lst");
$jxc ->exportFunction('jsaGetLetrasVencidas', array("idfecha-0"), "#lst2");

$jxc ->exportFunction('jsaGetVerifyPlan', array("idsolicitud"), "#aviso");
$jxc ->exportFunction('jsaGetLetras', array("idsolicitud"), "#divparcialidad");

$jxc ->process();

echo $xHP->getHeader();
echo $xHP->setBodyinit("initComponents()");

//$jbf    				= new jsBasicForm("frmPreRecibo");
//$jbf->setIncludeJQuery();
//$jbf->mIncludeCreditos 	= true;

$jxc->drawJavaScript(false, true);


$xFRM		= new cHForm("frmPreRecibo", "frmcobrosdecreditos2.php", false, "POST");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
//$msel		= $xSel->getListaDeProductosDeCredito();
///$msel->addEvent("onchange", "initComponents()");

$xDate->setDivClass("");


//$xFRM->addDivSolo($xDate->get("TR.Fecha"), "<div id='mscom'></div>", "tx14", "tx34");

$xFRM->addCreditBasico();
$xFRM->addDataTag("role", $xEvt->PAGO);
$xFRM->setTitle($xHP->getTitle());

$xTxt->addEvent("jsaGetLetras();jsaGetVerifyPlan();", "onfocus");

$xTxt->setDivClass("");
$props	= array( 1 => array("id" => "divparcialidad"), 2 => array("id" => "divavisos"));
$xFRM->addDivSolo($xTxt->get("idparcialidad", "", "TR.Numero de Parcialidad", "", false, CTRL_GOLETRAS),"<p class='aviso' id='aviso'></p>" , "tx14", "tx34", $props);

$xDate->addEvents("onblur=\"initComponents()\" onchange=\"initComponents()\" ");

$xFRM->addAviso("", "mscom");


$xFRM->addHTML("<div id='lst' class='inv'></div>");
$xFRM->addHTML("<div id='lst2' class='inv'></div>");

$xFRM->addSubmit("", "setFrmSubmit()");

$xFRM->OButton("TR.PAGOSHOY", "jsObtenerLetras()", $xFRM->ic()->EJECUTAR, "idgetletraspendientes", "white");
$xFRM->OButton("TR.PAGOSATRASADOS", "jsObtenerLetras2()", $xFRM->ic()->EJECUTAR, "idgetletrasatrasadas", "red");

//$xFRM->addToolbar($xBtn->getBasic("TR.Letras Pendientes", "jsObtenerLetras()", "ejecutar", "cmdGetLetras", false));

if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED) == true){
	$xFRM->addToolbar($xBtn->getBasic("TR.panel_de_control_de", "jsGoPanel()", "panel", "idgetpanel", false));
}
$xFRM->OButton("TR.PAGOS POR FECHA", "jsaGetLetrasAVencerTodas()", $xFRM->ic()->REPORTE4);
$xFRM->OButton("TR.Estado de Cuenta", "getEdoCtaCredito()", $xFRM->ic()->ESTADO_CTA);
$xFRM->OButton("TR.PLAN_DE_PAGOS", "getPlanDePagos()", $xFRM->ic()->CALENDARIO);

if(MODO_DEBUG == true){
	$xFRM->OButton("TR.generar PLAN_DE_PAGOS", "getFormaPlanDePagos()", $xFRM->ic()->CALENDARIO1);
}

$xFRM->OButton("TR.NOTAS", "jsAddNota()", $xFRM->ic()->NOTA);


$xFRM->addFechaRecibo();
if($ConVendedor == true){
	$xFRM->addHElem( $xSel->getListaDeUsuarios("vendedor", getUsuarioActual())->get("TR.VENDEDOR", true) );
}

echo $xFRM->get();
?>
</body>
<?php
//$jbf->show();
?>
<script>
var Wo			= new Gen();
var xG			= new Gen();
var onAsClicked = false;
var mCURFrm 	= document.getElementById("id-frmPreRecibo");
var jrsFileE 	= "./jscaja.js.php";
var xPer		= new PersGen();
var xSeg		= new SegGen();
var xCred		= new CredGen();
function initComponents(){	 }
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
		xG.spinInit();
		//mCURFrm.action = "frmcobrosdecreditos2.php?m=" + sIs;
		mCURFrm.submit();
	}
}
function getEdoCtaCredito(){ 
	var idcredito = $("#idsolicitud").val();
	if(idcredito > DEFAULT_CREDITO){
		xCred.getEstadoDeCuenta( idcredito );
	}
}
function getPlanDePagos(){
	var idcredito = $("#idsolicitud").val();
	if(idcredito > DEFAULT_CREDITO){
		xCred.getImprimirPlanPagosPorCred( idcredito );
	}	
}
function getFormaPlanDePagos(){
	var idcredito = $("#idsolicitud").val();
	if(idcredito > DEFAULT_CREDITO){
		xCred.getFormaPlanPagos( idcredito );
	}
}
function jsGoPanel(){ Wo.w({url: "../frmcreditos/creditos.panel.frm.php?idsolicitud=" + $("#idsolicitud").val() + "&idsocio=" + $("#idsocio").val()}); }

function jsCargarCredito(credito){
	var id 		= "#tr-letras-" + credito;
	var mObj	= processMetaData(id);

	jsCancelarAccion();
	$("#idsocio").val(mObj.codigo);
	$("#idsolicitud").val(mObj.credito);
	$("#nombresocio").val(mObj.nombre);
	jsaGetLetras();jsaGetVerifyPlan();
	//TODO: Modificar
	$("#idparcialidad").val(entero(mObj.periodo));
}

function jsCargarCredito2(credito){
	var id 		= "#tr-creditos_letras_del_dia-" + credito;
	var mObj	= processMetaData(id);

	jsCancelarAccion();
	$("#idsocio").val(mObj.persona);
	$("#idsolicitud").val(mObj.credito);
	$("#nombresocio").val(mObj.nombre);
	jsaGetLetras();jsaGetVerifyPlan();
	//TODO: Modificar
	$("#idparcialidad").val(entero(mObj.periodo));
}


function getSetLetra(){}
function jsGetLetras(){	jsObtenerLetras(); }
function jsEvaluarSalida(evt){
	jsaGetLetras();jsaGetVerifyPlan();jsGetMemos();
}
function jsObtenerLetras(){
	jsaGetLetrasAVencerTodas();
	Wo.winTip({ element : "#idfecha-0",  content : $("#lst"), title: Wo.lang(["letras"]) });
}
function jsObtenerLetras2(){
	jsaGetLetrasVencidas();
	Wo.winTip({ element : "#idfecha-0",  content : $("#lst2"), title: Wo.lang(["letras"]) });
}
function jsCancelarAccion(){	xG.closeTip();    }
function jsGetMemos(){
	var idp	= $("#idsocio").val();
	var idc	= $("#idsolicitud").val();
	if(idp > DEFAULT_SOCIO){
		xPer.getListaDeNotas({persona:idp,credito:idc, tipo:12, callback:jsLoadedMemos, estado:0});
	}
}
function jsLoadedMemos(data){
	$("#divavisos").empty();
	$.each( data, function( key, val ) {
		xG.alerta({msg:val.notas,info:val.oficial});
		$("#divavisos").append("<div class='error' style='margin-bottom:0.2em'>" + val.notas + "</div>");
	});
}
function jsAddNota(){
	var idcred	= entero($("#idsolicitud").val());
	if(idcred > 1){
		xCred.setNuevaNotaCaja(idcred);
	} else {
		Wo.alerta({msg: "Credito no valido"});
	}
}
</script>
</html>
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
$xHP		= new cHPage("TR.Envios de Cobranza", HP_FORM);
$jxc 		= new TinyAjax();
$xCaja		= new cCaja();
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	$xHP->goToPageError(200); }

function jsaInitEmpresa($empresa){
	$xF				= new cFecha();
	$xEmp			= new cEmpresas($empresa); $xEmp->init();
    $tab 			= new TinyAjaxBehavior();
    $DPer			= $xEmp->getOPeriodo();
	$fechaInicial	= $xF->setSumarDias(1, $DPer->fecha_inicial()->v());
	$fechaFinal		= $xF->setSumarDias($xEmp->getPeriocidadPref(), $fechaInicial );
	
	$tab -> add(TabSetvalue::getBehavior("idperiocidad",  $xEmp->getPeriocidadPref()));
	$tab -> add(TabSetvalue::getBehavior("idperiodo",  $xEmp->getPeriodo() + 1 ));
	$tab -> add(TabSetvalue::getBehavior("idfecha-10",  $xF->getFechaMX($fechaInicial, "-") ));
	$tab -> add(TabSetvalue::getBehavior("idfecha-11",  $xF->getFechaMX($fechaFinal, "-") ));
	
    return $tab -> getString();  	
}
function jsaInitPeriodo($empresa, $periocidad, $periodo){
	$xF					= new cFecha();
	$xEmp				= new cEmpresas($empresa); $xEmp->init();
	$tab 				= new TinyAjaxBehavior();
	$DPer				= $xEmp->getOPeriodo($periocidad);
	$empPeriodo			= $xEmp->getPeriodo();
	if($periodo > $empPeriodo){
		$fechaInicial	= $xEmp->getFechaDeAviso($periocidad, false, $periodo, $periodo + 1);
	} else {
		$dias_dif		= setNoMenorQueCero($periodo - $empPeriodo );
		$starD			= ($periocidad * $dias_dif) + 1;
		$fechaInicial	= $xF->setSumarDias($starD, $DPer->fecha_final()->v());
	}
	$fechaFinal			= $xF->setSumarDias($xEmp->getPeriocidadPref(), $fechaInicial );
	
	$tab -> add(TabSetvalue::getBehavior("idfecha-10",  $xF->getFechaMX($fechaInicial, "-") ));
	$tab -> add(TabSetvalue::getBehavior("idfecha-11",  $xF->getFechaMX($fechaFinal, "-") ));
	
	return $tab -> getString();	
}
function jsaGetCobranza($empresa, $periocidad, $variacion, $periodo, $fechaInicial, $fechaFinal){
    $ByPeriodo		= ($periocidad == "todos") ? "" : " AND creditos_solicitud.periocidad_de_pago = $periocidad ";
    $xF				= new cFecha();
    $xNot			= new cHNotif();
    $content		= "";
    $fechaFinal		= $xF->getFechaISO($fechaFinal);
    $fechaInicial	= $xF->getFechaISO($fechaInicial);
     
    $xEmp			= new cEmpresas($empresa);
    $periodoReal	= $periodo + $variacion;
    $xPer			= $xEmp->getOPeriodo($periocidad, $periodoReal, false, $fechaInicial);
    $DDias			= $xEmp->getFechaDeAviso();
	if($xPer->getCobrados() > 0){
		$content	= $xNot->get("ERROR\tLa nomina tiene cobros " . $xPer->getCobrados() . " ACTIVOS ", "iderror", $xNot->ERROR);
	} else {
		$content	= $xEmp->getListadoDeCobranza($empresa, $periocidad, $variacion, $periodo, $fechaInicial, $fechaFinal);
	}
    $periodo		= $periodo + $variacion;
    
    return $content;
    
}

function jsaGetCobranzaFutura($empresa, $periocidad, $variacion, $periodo, $fechaInicial, $fechaFinal){ }
function jsaGetEmailsEmpresa($idEmpresa, $periocidad, $variacion, $periodo){
    $xEmp	= new cEmpresas($idEmpresa); $xEmp->init();
    $tab 	= new TinyAjaxBehavior();
    $mails	= $xEmp->getEmailsDeEnvio();
    $itms	= 1;
    foreach($mails as $clave => $mail){
		$tab -> add(TabSetvalue::getBehavior("idmail$itms",  $mail));
		$itms++;
    }
    return $tab -> getString();    
}

function jsaGetDatosDelEnvio($idEmpresa, $periocidad, $variacion, $periodo){
	$xEmp		= new cEmpresas($idEmpresa); $xEmp->init();
	$tab 		= new TinyAjaxBehavior();
	$periodo	= $periodo + $variacion;
	//agregar observaciones
	$monto 		= $xEmp->getMontoDelPeriodo($periodo, $periocidad);
	$ops		= $xEmp->getOperacionesDePeriodo();
	if($monto > 0){
		//$tab -> add(TabSetvalue::getBehavior("idobservaciones", "PERIODO: $periodo x MONTO $ $monto "  ));
	} else {
		//$tab -> add(TabSetvalue::getBehavior("idobservaciones", "PERIODO $periodo POR ENVIAR" ));
	}
	return $tab -> getString();
}

function jsaGetDatosEmpresa($idEmpresa, $periocidad, $variacion, $fechaInicial){
	$xEmp		= new cEmpresas($idEmpresa);
	$xF			= new cFecha(0, $fechaInicial);
	$periodo	= ($periocidad == CREDITO_TIPO_PERIOCIDAD_SEMANAL) ? $xF->semana() : $xF->quincena();
	$dias		= 24*60;
	$observaciones	= "";
	$xEmp->init();
	$fecha		= strtotime(fechasys()) + (($variacion * $periocidad) * $dias);
	$ctrl		= "<label for=\"idperiodo\">Periodo $periodo</label><input type=\"number\" id=\"idperiodo\" onchange=\"jsInitPeriodo()\" onblur=\"jsInitPeriodo()\" />";
	//$xF			= new cFecha(0);
	//$observaciones	.= " -- $periodo";
	$xEmp		= new cEmpresas($idEmpresa); $xEmp->init();
	$periodo	= ( intval($xEmp->getPeriodo()) < 1 ) ? $periodo : intval($xEmp->getPeriodo());
	$periodo	= $periodo + 1;
	$xSel		= null;
	
	switch($periocidad){
	    case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
			$xSel	= $xF->getSelectSemanas("idperiodo", $periodo);
			if($xF->mes($fechaInicial) == 12){
				//setLog("DICIEMBRE");
				$anno	= $xF->anno()+1;
				$xSel->setDelOption(1);
				$xSel->setDelOption(2);
				$xSel->setDelOption(3);
				$xSel->setDelOption(4);
				$arrOpt	= array(
						"1" => "SEMANA  1.- $anno",
						"2" => "SEMANA  2.- $anno",
						"3" => "SEMANA  3.- $anno",
						"4" => "SEMANA  4.- $anno"
				);
				$xSel->addOptions($arrOpt);
			}
		break;
	    case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
			$xSel	= $xF->getSelectQuincenas("idperiodo", $periodo);
			if($xF->mes($fechaInicial) == 12){
				//setLog("DICIEMBRE");
				$anno	= $xF->anno()+1;
				$xSel->setDelOption(1);
				$xSel->setDelOption(2);
				$arrOpt	= array(
						"1" => "QUINCENA  1.- $anno",
						"2" => "QUINCENA  2.- $anno",
				);
				$xSel->addOptions($arrOpt);
			}			
		break;
	    /*case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
		$xSel	= $xF->getSelectQuincenas("idperiodo", $periodo);
		break;
	    case CREDITO_TIPO_PERIOCIDAD_DECENAL:
		
		break;*/
	    case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
	    	$xSel	= $xF->getSelectDeMeses("idperiodo", "idperiodo", $periodo);
	    	break;
	}
	if($xSel != null){		
		$xSel->setEnclose(false);
		$xSel->addEvent("jsInitPeriodo()", "onblur");
		$xSel->addEvent("jsInitPeriodo()", "onchange");
		$ctrl	= $xSel->get("idperiodo", "Periodo", $periodo);
	}
	return $ctrl;
}
function jsaGetContarPeriodo($idEmpresa, $periocidad, $periodo, $variacion, $fechaInicial){
	$periodo	= $periodo + $variacion;
	$xPer		= new cEmpresasCobranzaPeriodos();
	$msg		= "";
	if($xPer->initByDatos($idEmpresa, $periocidad, $periodo, $fechaInicial) == true){
		$msg	= "Este periodo $periodo de la Empresa $idEmpresa con la Frecuencia $periocidad existe en el Sistema!";
	}
	return $msg;
}

function jsaSetNominaCerrada($empresa, $periocidad, $variacion, $periodo, $monto,  $observaciones, $fechaInicial, $fechaFinal, $fechaCobro){
	$xF			= new cFecha();
    $xEmp		= new cEmpresas($empresa); $xEmp->init();
    $periodo	= $periodo + $variacion;
    $xEmp->setPeriodo($periodo);
    $xEmp->setClearPeriodo(true);
    $fechaInicial	= setFechaValida($fechaInicial);
    $fechaFinal		= setFechaValida($fechaFinal);
    $fechaCobro		= setFechaValida($fechaCobro);
    $idx			= $xEmp->addOperacion($monto, $periodo, $periocidad, fechasys(), SYS_UNO, false, $observaciones, $fechaInicial, $fechaFinal, $fechaCobro);

    return $idx;
}

function jsaSetCambiarFechaMinistracion($credito, $dia, $mes, $anno){
    $fecha	= "$anno-$mes-$dia";
    $xCred	= new cCredito($credito);
    $xCred->init();
    $xCred->setCambiarFechaMinistracion($fecha);
    return "Credito $credito modificado al **$dia-$mes-$anno**";//$xCred->getMessages();
}

$jxc ->exportFunction('jsaInitEmpresa', 		array("idcodigodeempresas"));
$jxc ->exportFunction('jsaInitPeriodo', 		array("idcodigodeempresas", "idperiocidad", "idperiodo"));
$jxc ->exportFunction('jsaGetDatosEmpresa', 	array("idcodigodeempresas", "idperiocidad", "idvariacion", "idfecha-10"), "#divperiodo");
$jxc ->exportFunction('jsaGetCobranza', 		array("idcodigodeempresas", "idperiocidad", "idvariacion", "idperiodo", "idfecha-10", "idfecha-11"), "#reports");
$jxc ->exportFunction('jsaGetCobranzaFutura', array("idcodigodeempresas", "idperiocidad", "idvariacion", "idperiodo", "idfecha-10", "idfecha-11"), "#cbzafutura");
$jxc ->exportFunction('jsaGetEmailsEmpresa', 	array("idcodigodeempresas", "idperiocidad", "idvariacion", "idperiodo"));
$jxc ->exportFunction('jsaGetDatosDelEnvio', 	array("idcodigodeempresas", "idperiocidad", "idvariacion", "idperiodo"));
$jxc ->exportFunction('jsaSetNominaCerrada', 	array("idcodigodeempresas", "idperiocidad", "idvariacion", "idperiodo", "idsuma", "idobservaciones", "idfecha-10", "idfecha-11", "idfecha-12"), "#idnomina");
$jxc ->exportFunction('jsaGetContarPeriodo', 	array("idcodigodeempresas", "idperiocidad", "idperiodo", "idvariacion", "idfecha-10"), "#idmsg");

$jxc ->process();

echo $xHP->getHeader(true);

echo $xHP->setBodyinit();
$txt		= new cHText();
$HFecha		= new cHDate();
$xFRM		= new cHForm("frmcbza", "", "idsumacbza");
$xBtnN		= new cHButton();
$xSel		= new cHSelect();
$xBTN4		= new cHButton();

$xFRM->setTitle($xHP->getTitle());

$xFRM->OButton("TR.Obtener Listado", "jsGetCobranza()", "refrescar", "idgetcbza") ;
$xFRM->addToolbar( $xBTN4->getBasic("TR.Finalizar Nomina", "jsFinalizarNomina()", "finalizar", "idcierrereporte", false) );
$xFRM->OButton("TR.Enviar Listado", "getObtenListado()", "lista", "idlistado") ;
$xFRM->OButton("TR.estado_de_cuenta", "jsPrintEstadoCuenta()", "reporte", "idedo") ;

$xFRM->OButton("TR.Ver Listado", "getReporteEnPantalla()", "reporte", "idverreporte");
$xFRM->OButton("TR.Imprimir Recibos", "getRecibo()", "imprimir", "idverreporte");
$xFRM->OButton("TR.Ver en Excel", "getReporteEnExcel()", $xFRM->ic()->EXCEL, "idverreportexls");
$xFRM->OButton("TR.Ver en PDF", "getReporteEnPDF()", $xFRM->ic()->PDF, "idverreportepdf");
//$xFRM->OButton();



$xFRM->addFootElement('<input type="hidden" id="idsumacbza" value="0" />');

$xSemp		= $xSel->getListaDeEmpresasConCreditosActivos("", true);

$xSemp->addEvent("onblur", "jsResetCbza();jsCargarDatosIniciales();");

if($empresa>0){
	$xEmp	= new cEmpresas($empresa);
	if($xEmp->init() == true){
		$xFRM->addHElem($xEmp->getFicha());
		$xFRM->OHidden("idcodigodeempresas", $empresa);
	} else {
		$xFRM->addHElem( $xSemp->get(true) );
	}
} else {
	$xFRM->addHElem( $xSemp->get(true) );
}



$xSPer	= $xSel->getListaDePeriocidadDePago();
$xSPer->addEvent("onblur", "jsaGetDatosEmpresa()");
$xSPer->addEvent("onchange", "jsaGetDatosEmpresa()");
$xFRM->addHElem( $xSPer->get(true));


$xFRM->addHElem('<div class="tx4" id="divperiodo"><label for="idperiodo">Periodo</label><input type="number" id="idperiodo" onchange="jsInitPeriodo()" onblur="jsInitPeriodo()" />	</div>');

$xFRM->addHElem('<div class="tx4"><label for="idvariacion">Variaci&oacute;n</label>
	    <select id="idvariacion" name="idvariacion" onchange="jsGetCobranza()">
		<option value="-2">[-2]Dos Periodos Atras</option><option value="-1">[-1]Un Periodo Atras</option><option value="0" selected="selected">Periodo Actual</option>
		<option value="1">[+1]Un Periodo Adelante</option><option value="2">[+2]Dos Periodos Adelante</option><option value="3">[+3]Tres Periodos Adelante</option>
	    </select>
	</div>');

$xFRM->addHElem( $HFecha->get("TR.Fecha_Inicial", false, 10) );
$xFRM->addHElem( $HFecha->get("TR.Fecha_Final", false, 11) );
$xFRM->addHElem( $HFecha->get("TR.Fecha de Cobro", false, 12) );

$xFRM->addHTML('<input type="hidden" id="idnomina" name="idnomina" value="0">');
$xFRM->addObservaciones();
$xDiv		= new cHDiv();

$xFRM->addHTML('<hr id="divavisos" /><div id="reports"></div><input type="hidden" id="idcredito" /><div id="cbzafutura"></div>');
$xFRM->addAviso("", "idmsg");
echo $xFRM->get();
?>

	<div class="inv" id="irecibos">
	<?php
		$xFRM4	= new cHForm("idcobranza");	
		
		$xFRM4->addToolbar( $xBTN4->getBasic("TR.Enviar Listado", "getReporteEnMail()", "email", "idverreporte", false) );
		
		$xFRM4->addHTML("<div id=\"personas-de-envio\"></div>");
		$xFRM4->addHElem( $txt->getEmail("idmail1", "", "TR.correo_electronico destinatario 1"));
		$xFRM4->addHElem( $txt->getEmail("idmail2", "", "TR.correo_electronico destinatario 2"));
		$xFRM4->addHElem( $txt->getEmail("idmail3", "", "TR.correo_electronico destinatario 3"));
		
		$xFRM4->addHElem( $txt->getEmail("idmail4", "", "TR.correo_electronico destinatario 4"));
		$xFRM4->addHElem( $txt->getEmail("idmail5", "", "TR.correo_electronico destinatario 5"));
		
		$xFRM4->addAviso("");
		echo $xFRM4->get();
				
	?>
	</div>

    <!-- cambiar fecha de Ministracion -->
    <div class="inv" id="frmac" style="min-height:12em;">
	<?php
		$frm	= new cHForm("mfrm");
		$xFM	= new cHDate(4);
		$xFM->setIsSelect();
		$xBtn	= new cHButton("idcmdready");
		$frm->addHElem( $xFM->get("Escriba la Nueva Fecha") );
		$frm->addFootElement( $xBtn->getEjecutar("jsSetCambiarFechaMinistracion()") );
		echo $frm->get();
	?>
    </div>
<script>
var vId				= "";
var xg				= new Gen();
var xG				= new Gen();
var tipoPago		= null;//"transferencia";
var banco			= null;//"99";
var fdeposit		= null;//"2014-01-01";
var idsumacbza		= $("#idsuma");
var idFortips		= "#divavisos";
var idFortips2		= "#reports";
var ordenCbza		= {};
//ordenCbza.items		= 0;
var xCred			= new CredGen();
var sumaOriginal	= 0;
var numOriginal		= 0;
var currNomina		= 0;

var nominaFinal		= false;
function jsInitPeriodo(){ jsaInitPeriodo(); }
function jsSetCobranza(){ getModalTip(idFortips2, $("#itesofe"), "Datos del Pago"); }
function jsCargarDatosIniciales(){	currNomina = 0; jsaInitEmpresa(); setTimeout("jsaGetDatosEmpresa()",500);	}
function jsCancelLockPeriodo(){ $(idFortips).qtip("hide"); }
function setOcultar(id) {    $("#options-" + id).parent().css("display", "none"); }
function jsGetCobranza(){    
	$("#idobservaciones").focus();   
	xg.spin({ time : 5000 });
	jsResetCbza(); jsaGetDatosDelEnvio();  
	setTimeout("jsGetCobranzaStep2()",1500);
	jsaGetContarPeriodo();
}
function jsGetCobranzaStep2(){ jsaGetCobranza(); jsaGetCobranzaFutura(); /*establecer Numero y monto original*/ setTimeout("setEstablacerSumasIniciales()",2000); }
function getEstadoDeCuenta(idcredito) {  var url = "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + idcredito ;    xg.w({ url : url, w : 800, h : 600 }); }
function getObtenListado(){    jsaGetEmailsEmpresa();    xg.tipModal({	element : "#divperiodo",	title : "Obtener recibos",	msg : $("#irecibos")	}); }
function jsSaveExcel() {    tableToExcel( document.getElementById("sqltable")); }
function jsGetCobranzaDay(){ getCorteDeRecibos(); }
function desvincular(credito){ $("#idcredito").val(credito); $("#options-" + credito).parent().css("display", "none");	xg.w({	url : "../frmcreditos/nominas.desvincular.frm.php?credito=" + credito,	w : 600, h: 400, tiny : true, callback: jsGetCobranza }); }
function jsActualizarMinistracion(credito){ $("#idcredito").val(credito); var vId		= "#pk-" + credito; getModalTip(vId, $("#frmac"), "Actualizar Fecha de Ministracion"); }
function setEstablacerSumasIniciales(){ if ( $("#sum-monto").length > 0 ) { sumaOriginal	= 0; numOriginal		= 0; } }
function getReporteEnPantalla(){ getReporte(""); }
function getReporteEnExcel(){ getReporte("&out=xls"); }
function getReporteEnPDF(){ getReporte("&out=pdf"); }
function getPlanDePagos(credito){ xG.w({	url : "../rpt_formatos/rptplandepagos.php?credito=" + credito,	w : 800, h: 600, tiny : false }); }

function jsResetCbza() {
    idsumacbza.val(0);
    ordenCbza		= {};
    vId				= "";
    tipoPago		= null; //"transferencia";
    banco			= null; //"99";
    fdeposit		= null; //"2014-01-01";
    $("#idfecha-0").pickadate({format: 'dd-mm-yyyy',formatSubmit:'yyyy-mm-dd'});
    $("#idnomina").val('');
    $("#octl").html("");
    $(idFortips2).qtip("hide");
    idsumacbza.val(0);
    $("#reports").empty();
}

function jsFinalizarNomina() {
    var monto	= $("#idsuma").val();
    var isCont	= true;
    if(currNomina > 0){ var isCont	= confirm("Existe una Nomina Activa con codigo #" + currNomina + "\nDesea Eliminarla?" ); }
    if(monto > 0 && isCont == true){
    	currNomina = 0;
    	var sip		= confirm("Desea establecer como monto " + monto + " definitivo para Cobrar?\nOperaciones Generadas:\nAgregar Operacion a la Empresa.\nRegistrar el Periodo como Activo.");
    	if (sip == true) {	
        	jsaSetNominaCerrada(); 
    		setTimeout("jsToGuardarRecibos()", 4500); 
    	}
    }
}
function jsSetAlimentarEnvio(obj, id) {	
	var credito	= id; 
	var notas 	= $("#notas-" + id).val();	
	var letra	= $("#periodo-"  + id).val();	
	var monto	= $("#monto-"  + id).val();

    var idr		= $("#idcodigodeempresas").val();
    var freq	= entero($("#idperiocidad").val());
    var vari	= entero($("#idvariacion").val());
    var periodo	= entero($("#idperiodo").val());
    periodo		= periodo+vari;
    
	if (obj.checked == true) {	
		$("#periodo-" + id).prop("disabled", true);
		$("#monto-" + id).prop("disabled", true);
		$("#notas-" + id).prop("disabled", true);
		ordenCbza[id]	= { credito : credito, letra : letra, monto : monto, notas : notas };
		//Validar si es correcto el monto de las letras
		xCred.getCompareLetra({ credito : credito, periodo : letra, monto : monto, callback : setReverseCheck });
		//Validar que existe en otra nomina
		xCred.getCheckLetraEnvioAnt({credito : credito, letra : letra, periodo:periodo, empresa:idr, frecuencia: freq , callback : setReverseCheck2});
		$("#idsuma").val( redondear( flotante($("#idsuma").val()) + flotante(monto)) );
		$("#idconteo").val( redondear($("#idconteo").val()) +  redondear(letra));
	} else {
		$("#periodo-" + id).prop("disabled", false);
		$("#monto-" + id).prop("disabled", false);
		$("#notas-" + id).prop("disabled", false);
		delete ordenCbza[id];
		$("#idsuma").val( redondear(flotante($("#idsuma").val()) - flotante(monto)) );
		$("#idconteo").val( redondear($("#idconteo").val()) -  redondear(letra));		
	}
	xg.letras({ id : "idenletras", monto : $("#idsuma").val()	});
}
function setReverseCheck2(iserr, idcredito){
	if(iserr == true){
		
		//$("#chk" + idcredito).attr('checked', false);
		//var idx 	= document.getElementById("chk" + idcredito);
		//delete ordenCbza["tr-creditos_solicitud-" +  idcredito];
		//jsSetAlimentarEnvio(idx, idcredito)
		$("#options-" + idcredito).parent().addClass("tr-error");
	}
}
function setReverseCheck(iserr, idcredito){
	if(iserr == true){
		
		$("#chk" + idcredito).attr('checked', false);
		var idx 	= document.getElementById("chk" + idcredito);
		delete ordenCbza["tr-creditos_solicitud-" +  idcredito];
		jsSetAlimentarEnvio(idx, idcredito)		
	}
}
function jsToGuardarRecibos(){
    var idr		= $("#idcodigodeempresas").val();
    var per		= $("#idperiocidad").val();
    var vari	= entero($("#idvariacion").val());
    var obs 	= $("#idobservaciones").val();
    var periodo	= entero($("#idperiodo").val());
    var iF1		= $("#idfecha-10").val();
    var iF2		= $("#idfecha-11").val();
	var iF3		= $("#idfecha-12").val();
	var idnom	= entero($("#idnomina").val());
	//Corrige el numero de periodo
	periodo		= periodo+vari;
	var spinner	= null;
	
	if(idnom <= 0){
		alert("La NOMINA (" + idnom + ") no es Valida... espere unos segundos!");
		jsaSetNominaCerrada();
		setTimeout("jsToGuardarRecibos()", 3500);
	} else {
		var cnt		= 1;
		var siz		= Object.keys(ordenCbza).length;
		var spinner = $(document.body).spin("modal");

	  for (var mObj in ordenCbza) {
		var itms		= ordenCbza[mObj];
		var strCred	= "&credito=" + itms.credito + "&letra=" + itms.letra + "&monto=" + itms.monto + "&notas=" + itms.notas + "&nomina=" + idnom;
		
	    var url	= "../svc/envionomina.svc.php?empresa=" + idr + "&periocidad=" + per + "&variacion=" + vari + "&observaciones=" + obs + "&periodo=" + periodo + "&on=" + iF1 + "&off=" + iF2  + "&to=" + iF3 + strCred;
	    
	    if(entero(itms.credito) > 0){
	    //console.log(itms.credito);
		    xg.pajax({
			url : url, result : "json",
				callback : function(data){
					try { data = JSON.parse(data); } catch (e){ }
					if (typeof data != "undefined"){
						if(data.error == true){
							xG.alerta({msg: data.message});
						} else {
							$("#idmsgs").html(data.message);
							markItem(data.credito);							
						}

						
						if(cnt >= siz){
							$(document.body).spin("modal").stop();		//spin
							alert("Nomina Finalizada");
							currNomina = $("#idnomina").val();
							$("#idnomina").val('');
							getReporte();
						}
						cnt++;
					} else {
						alert("ERROR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!1");
					}
				}
			});
	    } else {
	    	alert("ERROR!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!222");
	    }
	  }
	}
}
function markItem(id){ $("#tr-" + id).removeClass(); $("#tr-" + id).addClass("tr-pagar"); }

function getReporte( strOtros ){
	strOtros 	= (typeof strOtros == "undefined") ? "" : strOtros;
    var idr		= $("#idcodigodeempresas").val();
    var per		= $("#idperiocidad").val();
    var vari	= entero($("#idvariacion").val());
    var obs 	= $("#idobservaciones").val();
    var periodo	= entero($("#idperiodo").val());
    var iF1		= $("#idfecha-10").val();
    var iF2		= $("#idfecha-11").val();
    var periodo	= periodo+vari;
    var idnom	= currNomina;
    var url		= "../rptcreditos/orden_de_cobranza.rpt.php?r=" + idr + "&p=" + per + "&v=" + vari + "&o=" + obs + "&periodo=" + periodo + "&on=" + iF1 + "&off=" + iF2 + "&nomina=" + idnom + "" + strOtros;
    xg.w({ url : url, w : 800, h : 600 });
}

function getReporteEnMail(){
    var idr		= $("#idcodigodeempresas").val();
    var per		= $("#idperiocidad").val();
    var vari	= entero($("#idvariacion").val());
    var obs 	= $("#idobservaciones").val();
    var periodo	= entero($("#idperiodo").val());
    var mail	= $("#idmail1").val();
    
    var mail2	= $("#idmail2").val();
    var mail3	= $("#idmail3").val();
    var mail4	= $("#idmail4").val();
    var mail5	= $("#idmail5").val();

    var iF1		= $("#idfecha-10").val();
    var iF2		= $("#idfecha-11").val(); 
    var idnom	= currNomina; 
    var periodo	= periodo+vari;
    
    var url	= "../rptcreditos/orden_de_cobranza.rpt.php?out=email&r=" + idr + "&p=" + per + "&v=" + vari + "&o=" + obs + "&periodo=" + periodo + "&email1=" + mail + "&email2=" + mail2 + "&email3=" + mail3 + "&email4=" + mail4+ "&email5=" + mail5 + "&on=" + iF1 + "&off=" + iF2 + "&nomina=" + idnom;

    xg.pajax({
	url : url, result : "json",
		callback : function(data){
			try { data = JSON.parse(data); } catch (e){}
			/*for (var rw in data){
			var dat	=  data[rw];
			alert(dat);
			}*/
			if (typeof data != "undefined") {
				alert(data.message);
			} else {
				alert("ERROR AL ENVIAR NOMINA");
			}
		}
	});
}

function jsSetCambiarFechaMinistracion() {
    jsaSetCambiarFechaMinistracion();
    tip(vId, "Empezando el Cambio...", 3500);
    setTimeout("jsGetCobranza()", 2600);
    setTimeout("generarPlanDePagos(" + $("#idcredito").val()  + ")", 3000);
}
function editarPlanDePagos(credito) {  var sURL = '../frmcreditos/plan_de_pagos.edicion.frm.php?credito=' + credito;    xg.w({ url : sURL, w : 800, h : 600 });    $("#options-" + credito).parent().addClass("tr-plan"); }
function jsPrintEstadoCuenta(){
	var idemp	= $("#idcodigodeempresas").val();
	var periodo	= entero($("#idperiodo").val());
	var perio	= $("#idperiocidad").val();
	var vari	= entero($("#idvariacion").val());
	var periodo	= periodo+vari;
	xg.w({ url : "../rptempresas/empresas.movimientos.rpt.php?empresa=" + idemp + "&periodo=" + periodo + "&periocidad=" + perio });
}

function getRecibo(){
    var idr		= $("#idcodigodeempresas").val();
    var per		= $("#idperiocidad").val();
    var vari	= entero($("#idvariacion").val());
    var periodo	= entero($("#idperiodo").val());
    var obs 	= $("#idobservaciones").val();
    var iF1		= $("#idfecha-10").val();
    var iF2		= $("#idfecha-11").val();
    var periodo	= periodo+vari;
    var idnom	= currNomina;
    var url	= "../rptcreditos/orden_de_cobranza.recibos.rpt.php?r=" + idr + "&p=" + per + "&v=" + vari + "&o=" + obs + "&periodo=" + periodo + "&on=" + iF1 + "&off=" + iF2  + "&nomina=" + idnom;
    xg.w({ url : url, w : 800, h : 600 });
}
function generarPlanDePagos(credito) {
    var sURL = '../frmcreditos/frmcreditosplandepagos.php?r=1&credito=' + credito;
    xg.w({ url : sURL, w : 800, h : 600 });
    $("#options-" + credito).parent().addClass("tr-plan");
}
function getCorteDeRecibos(){
	xg.ena("#idguardarcobro");
    $("#octl").html('<a onclick="getCorteDeRecibos()"><img src="../images/cash_stack_add.png" />Obtener Corte<mark id="saldocorte"></mark></a>');
    var iddep		= $("#idcodigodeempresas").val();
    var ff			= $("#idfecha-0").val();
    var url			= "../rpttesoreria/rpt_caja_corte_sobre_recibos.php?dependencia=" + iddep + "&on=" + ff + "&off=" + ff;
    xg.w({ url : url, w : 800, h : 600 });
    $(idFortips2).qtip("hide");
}

function jsGetRecibosByCredito(credito) {
    var mObj	= processMetaData("#tr-creditos_solicitud-" + credito);
    var ht		= "";
    var myId	= "#pk-" + credito;
    var ff		= $("#idfecha-0").val();
    
    	xg.pajax({
		url: "../frmoperaciones/recibos.svc.php?persona=" + mObj.persona + "&documento=" + mObj.credito + "&mx=true&fecha=" + ff,
		finder: "recibo",
		callback : function(obj, final){
			//alert("ITEMS >>>> "  + final);
			//ht	+= "<a onclick=\"setSocio(" + $(obj).attr("codigo") + ")\">" + $(obj).attr("codigo") + "-" + $(obj).text() + "</a><br />";
			ht	+= "<a>RECIBO :" +  $(obj).attr("codigo") + " - Monto :<mark>" + $(obj).text() + "</mark></a></br>";
			//alert($(evt).attr("codigo"));
			if (final == true) {
				//$(myId).qtip("hide");
				tipSuggest(myId, "" + ht + "");
			}
		}
		});
}
function jsEditarEnvioPorCredito(credito) {
    var sURL = '../frmempresas/envios.credito.edit.frm.php?credito=' + credito;
    xg.w({ url : sURL, blank:true });
    $("#options-" + credito).parent().addClass("tr-plan");
}
//window.setInterval("jsaGetMontoCobranza()", 5000);
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->setBodyEnd();
$xHP->end();
?>
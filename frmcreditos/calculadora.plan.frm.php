<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.CALCULADORA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xSvc		= new MQLService("", "");

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$tasa		= parametro("tasa", 0, MQL_FLOAT);
$residual	= parametro("residual", true, MQL_BOOL);
$opcioniva	= parametro("optiva", true, MQL_BOOL);
$opcionint	= parametro("optint", true, MQL_BOOL);
$opcionRend	= parametro("optredondeo", true, MQL_BOOL);

$solonomina		= parametro("solonomina", false, MQL_BOOL);

$useSliceMto	= parametro("slicemonto", false, MQL_BOOL);
$useSlicePagos	= parametro("slicepagos", false, MQL_BOOL);

$inicioMonto	= parametro("iniciomonto",0, MQL_FLOAT);
$finMonto		= parametro("finmonto",0, MQL_FLOAT);
$finNumero		= parametro("finnumero",96, MQL_INT);

$montoInicial	= parametro("montoinicial",10000, MQL_FLOAT);
$pagosInicial	= parametro("pagosinicial", 24, MQL_INT);

$jsReglasInit	= parametro("reglasinit", "", MQL_RAW);
$jsReglasFin	= parametro("reglasfin", "", MQL_RAW);
$jsReglasCalc	= parametro("reglascalc", "", MQL_RAW);

$useMeses		= parametro("usemeses", false, MQL_BOOL);


$xHP->init("jsInitComponents()");

?><style> #idletra { font-size : 1.3em !important; } </style><?php

$xFRM		= new cHForm("frmcalplan", "./");
$xSel		= new cHSelect();
$xHNotif	= new cHNotif();
$xTxt		= new cHText();

$xFRM->addRangeSupport();
$xTxt->setDivClass("");
$nn			= $xTxt->getLabel("TR.NOMBRE");
$xTxt->setLabelSize("");

$xFRM->setTitle($xHP->getTitle());
$xFRM->setFieldsetClass("fieldform frmpanel");
$xFRM->addDivSolo($nn, $xTxt->get("idconatencion", false, " "), "tx14", "tx34");
$xFRM->setValidacion("idconatencion", "validacion.novacio", "", true);

if($solonomina == false){
	$xFRM->addHElem($xSel->getListaDePeriocidadDePago("idfrecuencia", CREDITO_TIPO_PERIOCIDAD_QUINCENAL)->get(true));
} else {
	$xFRM->addHElem($xSel->getListaDePeriocidadDePagoNomina("idfrecuencia", CREDITO_TIPO_PERIOCIDAD_QUINCENAL)->get(true));
}


if($useSlicePagos == true){
	if($useMeses == true){
		$xFRM->setValidacion("idfrecuencia", "jsCalcularPorMeses");
		$hrange2	= "<div class=\"range-slider-int solo\">Meses
<input name=\"idmeses\" id=\"idmeses\" step=\"1\" onchange=\"jsCalcularPorMeses()\" class=\"range-slider-int__range\" type=\"range\" value=\"1\" min=\"1\" max=\"$finNumero\">
<span class=\"range-slider-int__value\">2</span></div>";
		$xFRM->addHElem($hrange2);
		$xFRM->OHidden("idpagos", $pagosInicial);
	} else {
		$hrange2	= "<div class=\"range-slider-int solo\">Pagos
	<input name=\"idmonto\" id=\"idpagos\" step=\"1\" class=\"range-slider-int__range\" type=\"range\" value=\"$pagosInicial\" min=\"1\" max=\"$finNumero\">
	<span class=\"range-slider-int__value\">$pagosInicial</span></div>";
		$xFRM->addHElem($hrange2);
	}
} else {
	$xFRM->OEntero("idpagos", $pagosInicial, "TR.NUMERO DE PAGOS", $finNumero);
}

if($useSliceMto == true){
	$hrange	= "<div class=\"range-slider solo\">Monto Credito
<input name=\"idmonto\" id=\"idmonto\" step=\"100\" class=\"range-slider__range\" type=\"range\" value=\"$montoInicial\" min=\"$inicioMonto\" max=\"$finMonto\">
<span class=\"range-slider__value\">$montoInicial</span></div>";
	$xFRM->addHElem($hrange);
	
} else {
	$xFRM->OMoneda("idmonto", $montoInicial, "TR.MONTO CREDITO", false, false, $finMonto, $inicioMonto);
}




if($tasa>0){
	$xFRM->OHidden("idtasa", $tasa);
} else {
	$xFRM->OMoneda("idtasa", 60, "TR.TASA DE INTERES ANUAL");
}
if($residual == true){
	$xFRM->OMoneda("idresidual", 0, "TR.VALORRESIDUAL");
} else {
	$xFRM->OHidden("idresidual", 0);
}
if($opcioniva == true){
	$xFRM->OCheck("TR.SIN IMPUESTO_AL_CONSUMO", "idsiniva");
} else {
	$xFRM->OHidden("idsiniva", 0);
}
if($opcionRend == true){
	$xFRM->OCheck("TR.REDONDEO", "idconredondeo", true);
} else {
	$xFRM->OHidden("idconredondeo", 1);
}

if($opcionint == true){
	$xFRM->OCheck("TR.SOLO INTERES", "idsolointeres");
} else {
	$xFRM->OHidden("idsolointeres", 0);
}

//$xFRM->addJsBasico();
$xFRM->OButton("TR.CALCULAR", "jsCalcular()", $xFRM->ic()->EJECUTAR);
$xFRM->OButton("TR.IMPRIMIR", "jsVerCotizacion()", $xFRM->ic()->IMPRIMIR, "idimprimir");

if($tasa>0){
	//$xFRM->addTag("Tasa : <strong>% $tasa</strong>", "notice");
}

//$xFRM->addAviso("Monto a Pagar", "idletra");
$xFRM->addHElem($xHNotif->get("-", "idletra"));
$xFRM->addSeccion("idcalendario", "TR.CALENDARIO");
$xFRM->addHElem("<div id='idcalendar'></div>");
$xFRM->endSeccion();
echo $xFRM->get();
?>
<script>
var xG					= new Gen();
var mMeses				= 0;
function jsInitComponents(){
	xG.desactiva("#idimprimir");

	<?php 
	if(strlen($jsReglasInit) > 10){
		echo base64_decode($jsReglasInit);
	}
	?>
}
function jsCalcularPorMeses(){
	//var idpagos			= $("#idpagos").val();
	var idmeses			= $("#idmeses").val();
	var idfrecuencia	= $("#idfrecuencia").val();
	var idpagos			= entero( Math.round((idmeses * 30.4166666666666666666666666666666)/idfrecuencia ),0 ); 

	console.log("Pagos : " + idpagos);
	$("#idpagos").val(idpagos);

	return true;
}
function jsCalcular(){
	var idsiniva		= <?php echo ($opcioniva == true) ? "$('#idsiniva').prop('checked')" : "false" ?>;;
	
	var idconredondeo	= $('#idconredondeo').prop('checked');
	var idmonto			= $("#idmonto").val();
	var idpagos			= $("#idpagos").val();
	var idtasa			= $("#idtasa").val();
	var idfrecuencia	= $("#idfrecuencia").val();
	var idsolo			= $('#idsolointeres').prop('checked');
	var idresidual		= $("#idresidual").val();
	mMeses				= entero( Math.round((idfrecuencia * idpagos) / 30.4166666666666666666666666666666666),0);
	var run				= true;
	/*
	Hasta $5,000 son 6 meses
De 5,001 a 10,000 son 9 meses
De 10,001 a $100,000 a 12 meses (Puede tener excepción)
De $101,000 en adelante a 18 meses (Puede tener excepción)
	*/
	<?php 
	if(strlen($jsReglasCalc) > 10){
		//echo "/*$jsReglasCalc*/\n";
		/*$hs = $xSvc->setBase64("if(idmonto >= 0.01 && idmonto <= 5000){ if(mMeses > 6){ run = false; xG.alerta({msg:'El Pazo no debe ser mayor a 6 meses', tipo:'warn'}); } }
	if(idmonto >= 5000.01 && idmonto < 100000){ if(mMeses > 12){ run = false; xG.alerta({msg:'El Pazo no debe ser mayor a 12 meses', tipo:'warn'}); } }
	if(idmonto >= 100000){ if(mMeses > 18){ run = false; xG.alerta({msg:'El Pazo no debe ser mayor a 18 meses', tipo:'warn'}); } }");*/
		//echo $xSvc->getBase64($hs);
		echo $xSvc->getBase64($jsReglasCalc);
	}
	?>
	if(run == true){
		var urlm			= "../svc/cotizador.plan.svc.php?monto=" + idmonto + "&pagos=" + idpagos + "&siniva=" + idsiniva + "&redondeo=" + idconredondeo + "&frecuencia=" +  idfrecuencia + "&tasa=" + idtasa + "&solointeres=" + idsolo + "&residual=" + idresidual;
		$.ajax(urlm, {
	      success: function(data) {
		//alert(data.monto);
	         //$('#main').html($(data).find('#main *'));
	         //$('#notification-bar').text('The page has been successfully loaded');
			$("#idletra").html("Cuota de Pago : $ " + getFMoney(data.monto) + "; Meses: " + mMeses);
			$("#idcalendar").html( base64.decode(data.html) );
			session("data.plan", data.html);
			xG.activa("#idimprimir");
	      },
	      error: function() {
	         //$('#notification-bar').text('An error occurred');
	      }
	   });
	}
}
function jsVerCotizacion(){
	var idnn		= $("#idconatencion").val();
	var urlm		= "../rpt_formatos/cotizador.plan.rpt.php?nombre=" + idnn ;
	
	if(xG.happy() == true){
		xG.w({url:urlm});
	}
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
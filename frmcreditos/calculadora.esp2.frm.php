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
$xHP		= new cHPage("TR.CALCULADORA MOTOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
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

$tasaAnticipoMin	= parametro("tasaanticipomininimo",0,MQL_FLOAT);
$tasaComisionApert	= parametro("tasacomisionapertura",0, MQL_FLOAT);
$seguroObligatorio	= parametro("seguroobligatorio", false, MQL_BOOL);
$tasaIncrementoSeg	= parametro("tasaincrementoseg", 0, MQL_FLOAT);
$tasaInteres		= parametro("tasainteres",0,MQL_FLOAT);
$tasaComision		= parametro("tasacomision",0,MQL_FLOAT);
$montoSeguroAnual	= parametro("montoseguroanual",0,MQL_FLOAT);

$xHP->init();

?><style>
td {
	line-height; 2.1em;
	font-size : 1.1em !important;
} 
#idletra { font-size : 1.3em !important; }
</style><?php

$xFRM		= new cHForm("frmcalcesp2", "./");
$xSel		= new cHSelect();
$xHNotif	= new cHNotif();
$xTxt		= new cHText(); $xTxt2	= new cHText();
$xTxt->setDivClass(""); $xTxt2->setDivClass(""); /*$xTxt2->addEvent("jsCalcular()", "onchange");*/


$xFRM->setValidacion("idenganche_mny", "jsCalcular");
$xFRM->setValidacion("idprecio_mny", "jsCalcularAnticipo");
$xFRM->setValidacion("idfrecuencia", "jsCalcular");

$nn			= $xTxt->getLabel("TR.NOMBRE");
$nn2		= $xTxt->getLabel("TR.PRECIO");

$xFRM->addCerrar();
$xFRM->setTitle($xHP->getTitle());
//$xFRM->setFieldsetClass("fieldform frmpanel");
$xFRM->addDivSolo($nn, $xTxt->get("idconatencion", false, " "), "tx14", "tx34");

$xFRM->OMoneda2("idprecio", 0, "TR.PRECIO");
//$xFRM->setValidacion("idprecio", "validacion.nozero");

//$xFRM->OMoneda("idpagos", 12, "TR.NUMERO DE PAGOS");
$xSel->setDivClass("tx14 tx18 green");
$xSel->addOptions(array(
		6 => "6 Meses",
		12 => "Un Año", 
		18 => "1 Año 6 Meses", 
		24 => "2 Años", 
		30 => "2 Años 6 Meses",
		36 => "3 Años"
		
));
$xSel->addEvent("jsCalcular()", "onchange");
$xFRM->addHElem(  $xSel->get("idmeses", "TR.PLAZO", 1) );

//$xFRM->addDivMedio($nn2, $xTxt->getDeMoneda2("idprecio"), "tx14", "tx34");

$xFRM->addHElem($xSel->getListaDePeriocidadDePago("idfrecuencia", CREDITO_TIPO_PERIOCIDAD_MENSUAL, false, true)->get(true));




$xFRM->OMoneda2("idenganche", 0, "TR.ANTICIPO");

if($montoSeguroAnual>0){
	$xFRM->OHidden("idseguro", $montoSeguroAnual);
	//$xFRM->OMoneda2("idseguro", $montoSeguroAnual, "TR.AUTOSEGURO");
} else {
	$xFRM->OMoneda2("idseguro", $montoSeguroAnual, "TR.AUTOSEGURO");
}
if($seguroObligatorio == true){
	$xFRM->OHidden("idfinancia", 1);
	
} else {
	
	$xFRM->OSiNo("TR.AUTOSEGURO FINANCIADO", "idfinancia");
}



//$xFRM->OHidden("tasainteres", $tasaInteres);
$xFRM->OHidden("tasaincrementoseg", $tasaIncrementoSeg);
$xFRM->OHidden("tasaanticipominimo", $tasaAnticipoMin);
$xFRM->OHidden("tasacomision", $tasaComision);

$xFRM->OHidden("idpagos", 0);
$xFRM->OHidden("idmonto", 0);
$xFRM->OHidden("idtasa", $tasaInteres);
$xFRM->OHidden("idresidual", 0);

$xTb		= new cHTabla("idtablecot");
$xTxt		= new cHText();

$xTxt->setProperty("readonly", "readonly");
$xTxt->setNoCleanProps();


/*$xTb->initRow();
$xTb->addTH("Concepto");
$xTb->addTH("Monto");
$xTb->endRow();*/

$xTb->initRow();
$xTb->addTD("Precio");
$xTb->addTD( "-", " id='txtprecio' class='mny' " );
$xTb->endRow();


$xTb->initRow();
$xTb->addTD("Seguro Financiado");
$xTb->addTD( "-", " id='txtsegurofin' class='mny' " );
$xTb->endRow();



$xTb->initRow();
$xTb->addTD("Enganche");
$xTb->addTD( "-", " id='txtenganche' class='mny' " );
$xTb->endRow();


$xTb->initRow();
$xTb->addTD("Financiamiento", " class='sumas izq' ");
$xTb->addTD( "-", " id='txtfinanciamiento' class='mny total' " );
$xTb->endRow();


$xTb->initRow(); $xTb->addTD("", " colspan='2' class='divisor' "); $xTb->endRow();


$xTb->initRow();
$xTb->addTD("Comision por Apertura");
$xTb->addTD( "-", " id='txtcomision' class='mny' " );
$xTb->endRow();


$xTb->initRow();
$xTb->addTD("Pago de Seguro Inicial");
$xTb->addTD( "-", " id='txtseguroinit' class='mny' " );
$xTb->endRow();


$xTb->initRow();
$xTb->addTD("Pago Inicial", " class='sumas izq' " );
$xTb->addTD( "-", " id='txtinicial' class='mny total' " );
$xTb->endRow();

$xTb->initRow(); $xTb->addTD("<br />", " colspan='2' class='divisor' "); $xTb->endRow();

$xFRM->addHElem("<div id='idt'>"  . $xTb->get() . "</div>");



//$xFRM->addJsBasico();
$xFRM->OButton("TR.CALCULAR", "jsCalcular0()", $xFRM->ic()->CALCULAR, "", "green");
$xFRM->OButton("TR.IMPRIMIR", "jsVerCotizacion()", $xFRM->ic()->IMPRIMIR, "idimprimir");

//$xFRM->addAviso("Monto a Pagar", "idletra");
$xFRM->addHElem($xHNotif->get("Cuota de pago: $ 0.00", "idletra"));
$xFRM->addSeccion("idcalendario", "TR.CALENDARIO");
$xFRM->addHElem("<div id='idcalendar'></div>");

$xFRM->endSeccion();


$xFRM->addJsInit("jsInitComponents();");


echo $xFRM->get();



?>
<script>
var xG					= new Gen();
var xC					= new CredGen();
var idsegurooriginal	= flotante($("#idseguro").val());
setLog("Original " + idsegurooriginal);



function jsInitComponents(){
	xG.desactiva("#idimprimir");
	idsegurooriginal	= flotante($("#idseguro").val());
	setLog("Original 2.-" + idsegurooriginal);
}
function jsCalcular0(){
	
	if(jsCalcular() == true){
		setTimeout("jsCalcular2()",1000);
	}

	
}

function jsCalcularAnticipo(){
	var idfrecuencia		= entero($("#idfrecuencia").val());
	var idprecio			= flotante($("#idprecio").val());
	var idprecioor			= flotante($("#idprecio").val());
	var tasaanticipominimo 	= flotante($("#tasaanticipominimo").val());
	var tasacomision 		= flotante($("#tasacomision").val());
	var tasaincrementoseg 	= flotante($("#tasaincrementoseg").val());
	var anticipominimo		= redondear((idprecio * (tasaanticipominimo/100)));
	
	var idmeses				= $("#idmeses").val();
	var idseguro			= flotante($("#idseguro").val());
	var idfinancia			= ($("#idfinancia").val() == 1) ? true : false;
	var idseguroinit		= 0;
	//Actualizar el Enganche 2
	var idenganche2			= flotante($("#idenganche_mny").val());
	var idenganche			= anticipominimo;
	var idfinanciamiento	= 0;//$("#idfinanciamiento").val();
	var idcomision			= 0;
	var idpagos				= 0;
	var idmonto				= $("#idmonto").val();
	var idtasa				= $("#idtasa").val();
	var idannio				= redondear((idmeses / 12),1);
	var idannioseguro		= Math.ceil((idmeses / 12));
	var idperiodos			= 0;
	var run					= true;
	var vIva				= 0.16;


	setLog("Enganche 1 : " +  idenganche);
	setLog("Enganche 2 : " +  idenganche2);

	
	if(idenganche2 > idenganche){
		
		idenganche = idenganche2;
	}
	$("#idenganche").val( idenganche );
	$("#idenganche_mny").val( getFMoney(idenganche) );
	
	
	return true;
}

function jsCalcular(){

	var idfrecuencia		= entero($("#idfrecuencia").val());
	var idprecio			= flotante($("#idprecio").val());
	var idprecioor			= flotante($("#idprecio").val());
	var tasaanticipominimo 	= flotante($("#tasaanticipominimo").val());
	var tasacomision 		= flotante($("#tasacomision").val());
	var tasaincrementoseg 	= flotante($("#tasaincrementoseg").val());
	var anticipominimo		= redondear((idprecio * (tasaanticipominimo/100)));
	
	var idmeses				= $("#idmeses").val();
	var idseguro			= flotante($("#idseguro").val());
	var idfinancia			= ($("#idfinancia").val() == 1) ? true : false;
	var idseguroinit		= 0;
	//Actualizar el Enganche 2
	var idenganche2			= flotante($("#idenganche_mny").val());
	$("#idenganche").val(idenganche2);
	
	var idenganche			= anticipominimo;
	var idfinanciamiento	= 0;//$("#idfinanciamiento").val();
	var idcomision			= 0;
	var idpagos				= 0;
	var idmonto				= $("#idmonto").val();
	var idtasa				= $("#idtasa").val();
	var idannio				= redondear((idmeses / 12),1);
	var idannioseguro		= Math.ceil((idmeses / 12));
	var idperiodos			= 0;
	var run					= true;
	var vIva				= 0.16;
	//
	
	setLog("Annios Seguro: " + idannioseguro);
	if(idenganche2 >= idprecio){
		xG.alerta({msg: "El Anticipo no puede ser mayor al Precio", tipo : "error", title : "Anticipo"});
		run					= false;
	} else {
		setLog("Precio : " + idprecio);
		setLog("Enganche : " + idenganche);
		if(idannioseguro > 1 && tasaincrementoseg > 0){
			idseguro 			= idsegurooriginal;
			var baseanterior	= idsegurooriginal;
			
			for(ix=2; ix<=idannioseguro; ix++){
				var incremento	= redondear(flotante((baseanterior * (tasaincrementoseg / 100))),2);
				var subtotal	= redondear(flotante(baseanterior) + incremento,2);
				baseanterior	= subtotal;
				
				setLog(ix +" .- Original: " + idseguro);
				setLog(ix +" .- Incremento: " + incremento);
				setLog(ix +" .- Subtotal: " + subtotal);
				
				idseguro		+= baseanterior;
	
			}
			setLog("Seguro Acumulado: " + idseguro);
			//idseguro		= idseguro;
			
			$("#idseguro").val(idseguro);
		} else {
			idseguro			= idsegurooriginal; 
			$("#idseguro").val(idseguro);
		}
		
		if(idfinancia == true||idfinancia == 1){
			idprecio			= idprecio+idseguro;
		} else {
			idseguroinit		= idseguro;
		}
		
		if(idprecio <=0){
			run 				= false;
		}
		
	
		if(idenganche2 > idenganche){
			setLog("El enganche 2 (" +  idenganche2 + ") es mayor que el enganche Calculado (" + idenganche + ")");
			
			idenganche = idenganche2;
		} else {
			$("#idenganche").val( idenganche );
			$("#idenganche_mny").val( getFMoney(idenganche) );
		}
		
	
			
		//
		//
		switch(idfrecuencia){
			case 7:
				idpagos = entero((idannio*52));
			break;
			case 14:
				idpagos = entero((idannio*26));
			break;
			case 15:
				idpagos = entero((idannio*24));
			break;
			case 30:
				idpagos = entero((idannio*12));
			break;
			default:
				run 	= false;
				idpagos = entero((idannio*12));
				
			break;
		}
	
		$("#idpagos").val(idpagos);
		//
		idfinanciamiento	= redondear((idprecio-idenganche),2);
		idcomision			= redondear((idfinanciamiento * (tasacomision/100) ));
		$("#idmonto").val(idfinanciamiento);
	
	
		$("#txtenganche").html(getFMoney(idenganche));
		$("#txtcomision").html(getFMoney(idcomision));
		$("#txtprecio").html(getFMoney(idprecioor));
		
		$("#txtsegurofin").html(getFMoney(idseguro));
		$("#txtseguroinit").html(getFMoney(idseguroinit));
		
		$("#txtfinanciamiento").html( getFMoney(idfinanciamiento) );
		$("#txtinicial").html( getFMoney((idcomision+idseguroinit)) );
	}
	if(run == true){
		var cuota = xC.getCuotaDePago({capital:idfinanciamiento, tasa: idtasa, residual: 0, frecuencia: idfrecuencia, pagos: idpagos, iva: vIva});
		$("#idletra").html("Cuota de Pago: $ " + getFMoney(cuota));
	} else {
		xG.alerta({msg: "No se puede generar la cotizacion con estos datos", tipo:"warning"});
	}

	return run;
}
function jsCalcular2(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	
	var idsiniva		= false;
	var idconredondeo	= false;
	var idmonto			= $("#idmonto").val();
	var idpagos			= $("#idpagos").val();
	var idtasa			= $("#idtasa").val();
	var idfrecuencia	= $("#idfrecuencia").val();
	var idsolo			= false;
	var idresidual		= $("#idresidual").val();
	var urlm			= "../svc/cotizador.plan.svc.php?monto=" + idmonto + "&pagos=" + idpagos + "&siniva=" + idsiniva + "&redondeo=" + idconredondeo + "&frecuencia=" +  idfrecuencia + "&tasa=" + idtasa + "&solointeres=" + idsolo + "&residual=" + idresidual;
	xG.spinInit();
   $.ajax(urlm, {
      success: function(data) {

		$("#idletra").html("Cuota de Pago : $ " + getFMoney(data.monto));

		$("#idcalendar").html( base64.decode(data.html) );
		
		var idh			= base64.encode($("#idt").html());
		session("data.head", idh);
		session("data.plan", data.html);
		
		xG.activa("#idimprimir");

		
		xG.spinEnd();

		setTimeout(callB,10);
      },
      error: function() {
         //$('#notification-bar').text('An error occurred');
    	  xG.spinEnd();
      }
   });
}
function jsVerCotizacion(){
	if(jsCalcular() == true){
		jsCalcular2({
			callback : function(){
				var idnn			= $("#idconatencion").val();
				var urlm		= "../rpt_formatos/cotizador.plan.rpt.php?logo=false&nombre=" + idnn ;
				xG.w({url:urlm});				
			}
		});
	}

}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>

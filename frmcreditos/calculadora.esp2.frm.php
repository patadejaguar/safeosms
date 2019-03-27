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

$xHP->init("jsInitComponents()");

?><style> #idletra { font-size : 1.3em !important; } </style><?php

$xFRM		= new cHForm("frmcalcesp2", "./");
$xSel		= new cHSelect();
$xHNotif	= new cHNotif();
$xTxt		= new cHText(); $xTxt2	= new cHText();
$xTxt->setDivClass(""); $xTxt2->setDivClass(""); /*$xTxt2->addEvent("jsCalcular()", "onchange");*/
$xFRM->setValidacion("idenganche_mny", "jsCalcular");

$nn			= $xTxt->getLabel("TR.NOMBRE");
$nn2		= $xTxt->getLabel("TR.PRECIO");

$xFRM->addCerrar();
$xFRM->setTitle($xHP->getTitle());
//$xFRM->setFieldsetClass("fieldform frmpanel");
$xFRM->addDivSolo($nn, $xTxt->get("idconatencion", false, " "), "tx14", "tx34");
$xFRM->OMoneda2("idprecio", 0, "TR.PRECIO");
$xFRM->OMoneda2("idseguro", 0, "TR.AUTOSEGURO");

$xFRM->OMoneda2("idenganche", 0, "TR.ANTICIPO");
$xFRM->OSiNo("TR.AUTOSEGURO FINANCIADO", "idfinancia");


//$xFRM->addDivMedio($nn2, $xTxt->getDeMoneda2("idprecio"), "tx14", "tx34");

$xFRM->addHElem($xSel->getListaDePeriocidadDePago("idfrecuencia", CREDITO_TIPO_PERIOCIDAD_MENSUAL)->get(true));







//$xFRM->OMoneda("idpagos", 12, "TR.NUMERO DE PAGOS");
$xSel->setDivClass("tx14 tx18 green");
$xSel->addOptions(array( 1 => "Un Año", 2 => "2 Años", 3 => "3 Años", 4 => "4 Años", 5 => "5 Años" ));
$xSel->addEvent("jsCalcular()", "onchange");

$xFRM->addHElem(  $xSel->get("idannio", "TR.PLAZO", 1) );

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

$xFRM->OHidden("idpagos", 0);
$xFRM->OHidden("idmonto", 0);
$xFRM->OHidden("idtasa", 0);
$xFRM->OHidden("idresidual", 0);





//$xFRM->addJsBasico();
$xFRM->OButton("TR.CALCULAR", "jsCalcular0()", $xFRM->ic()->CALCULAR, "", "green");
$xFRM->OButton("TR.IMPRIMIR", "jsVerCotizacion()", $xFRM->ic()->IMPRIMIR, "idimprimir");

//$xFRM->addAviso("Monto a Pagar", "idletra");
$xFRM->addHElem($xHNotif->get("Cuota de pago: $ 0.00", "idletra"));
$xFRM->addSeccion("idcalendario", "TR.CALENDARIO");
$xFRM->addHElem("<div id='idcalendar'></div>");

$xFRM->endSeccion();
echo $xFRM->get();
?>
<script>
var xG					= new Gen();
var xC					= new CredGen();

function jsInitComponents(){
	xG.desactiva("#idimprimir");
}
function jsCalcular0(){
	if(jsCalcular() == true){
		setTimeout("jsCalcular2()",1000);
	}
}


function jsCalcular(){

	var idfrecuencia		= entero($("#idfrecuencia").val());
	var idprecio			= flotante($("#idprecio").val());
	var idprecioor			= flotante($("#idprecio").val());
	
	var idseguro			= flotante($("#idseguro").val());
	var idfinancia			= ($("#idfinancia").val() == 1) ? true : false;
	var idseguroinit		= 0;
	//Actualizar el Enganche 2
	var idenganche2			= flotante($("#idenganche_mny").val());
	$("#idenganche").val(idenganche2);
	
	var idenganche			= 0;
	var idfinanciamiento	= 0;//$("#idfinanciamiento").val();
	var idcomision			= 0;
	var idpagos				= 0;
	var idmonto				= $("#idmonto").val();
	var idtasa				= 0;
	var idannio				= entero($("#idannio").val());
	var idperiodos			= 0;
	var run					= true;
	var vIva				= 0.16;
	if(idfinancia == true){
		idprecio			= idprecio+idseguro;
	} else {
		idseguroinit		= idseguro;
	}
	//Evaluar montos
	//evaluar criterios por annio
	if(idannio <= 2){
		if(idprecio <= 50000){
			idtasa 		= 28;
			idcomision	= 600;
			idenganche	= idprecio * 0.2;
		} else if (idprecio >= 50000.01 && idprecio <= 80000){
			idtasa 		= 24;
			idcomision	= 800;
			idenganche	= idprecio * 0.24;
		} else {
			idtasa 		= 24;
			idcomision	= 1200;
			idenganche	= idprecio * 0.26;
		}
	} else {
		if(idprecio <= 50000){
			run			= false;
		} else if (idprecio >= 50000.01 && idprecio <= 80000){
			idtasa 		= 30;
			idcomision	= 1000;
			idenganche	= idprecio * 0.28;
		} else {
			idtasa 		= 24;
			idcomision	= idprecio * 0.015;
			idenganche	= idprecio * 0.3;
		}
	}
	//Asignar avlores
	$("#idtasa").val(idtasa);

	if(idenganche2 > idenganche){
		//setLog(idenganche +"---"+ idenganche2);
		idenganche = idenganche2;
	} else {
		$("#idenganche").val( idenganche );
		$("#idenganche_mny").val( getFMoney(idenganche) );
	}
	$("#txtenganche").html(getFMoney(idenganche));
	$("#txtcomision").html(getFMoney(idcomision));
	$("#txtprecio").html(getFMoney(idprecioor));
	
	$("#txtsegurofin").html(getFMoney(idseguro));
	$("#txtseguroinit").html(getFMoney(idseguroinit));
		
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
			idpagos = entero((idannio*12));
		break;
	}

	$("#idpagos").val(idpagos);
	//
	idfinanciamiento	= redondear((idprecio-idenganche),2);

	$("#idmonto").val(idfinanciamiento);
	
	$("#txtfinanciamiento").html( getFMoney(idfinanciamiento) );
	$("#txtinicial").html( getFMoney((idcomision+idseguroinit)) );
	
	if(run == true){
		var cuota = xC.getCuotaDePago({capital:idfinanciamiento, tasa: idtasa, residual: 0, frecuencia: idfrecuencia, pagos: idpagos, iva: vIva});
		$("#idletra").html("Cuota de Pago: $ " + getFMoney(cuota));
	} else {
		xG.alerta({msg: "No se puede generar la cotizacion con estos datos", level: "error"});
	}

	return run;
}
function jsCalcular2(){
	var idsiniva		= false;
	var idconredondeo	= false;
	var idmonto			= $("#idmonto").val();
	var idpagos			= $("#idpagos").val();
	var idtasa			= $("#idtasa").val();
	var idfrecuencia	= $("#idfrecuencia").val();
	var idsolo			= false;
	var idresidual		= $("#idresidual").val();
	var urlm			= "../svc/cotizador.plan.svc.php?monto=" + idmonto + "&pagos=" + idpagos + "&siniva=" + idsiniva + "&redondeo=" + idconredondeo + "&frecuencia=" +  idfrecuencia + "&tasa=" + idtasa + "&solointeres=" + idsolo + "&residual=" + idresidual;
   $.ajax(urlm, {
      success: function(data) {

		$("#idletra").html("Cuota de Pago : $ " + getFMoney(data.monto));

		$("#idcalendar").html( base64.decode(data.html) );
		
		var idh			= base64.encode($("#idt").html());
		session("data.head", idh);
		session("data.plan", data.html);
		
		xG.activa("#idimprimir");
      },
      error: function() {
         //$('#notification-bar').text('An error occurred');
      }
   });
}
function jsVerCotizacion(){
	var idnn			= $("#idconatencion").val();
	var urlm		= "../rpt_formatos/cotizador.plan.rpt.php?logo=false&nombre=" + idnn ;
	xG.w({url:urlm});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>

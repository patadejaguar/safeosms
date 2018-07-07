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
$xHP		= new cHPage("TR.PRECLIENTES PANEL", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
$xRuls		= new cReglaDeNegocio();



$SinDatosFiscales	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATOS_FISCALES);		//regla de negocio
$SinDatoPoblacional = $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATO_POBLACIONAL);		//regla de negocio
$SinRegimenMat 		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_REG_MATRIMONIAL);		//regla de negocio
$SinDatosDocto 		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATOS_DOCTOS);		//regla de negocio
$DomicilioSimple	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_RELS_DOM_SIMPLE);
$SinDetalleAcceso 	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DETALLE_ACCESO);
$EsSimple			= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_EC_SIMPLE);
$TratarComoSalarios	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_EC_ASALARIADO);
$xclass				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_XCLASIFICACION);
$yclass				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_YCLASIFICACION);
$zclass				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_ZCLASIFICACION);
$useDExtranjero		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_DEXTRANJERO);
$useDColegiacion	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_DCOLEGIACION);
$userNoDNI			= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DNI_INGRESO);
$RelsSinDom			= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_RELS_SIN_DOM);
$RN_NoValidarCurp	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_NO_VALIDAR_DNI);
$useDatosAccidente	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_DATO_ACCIDENTE);
$UsarIDInterno		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_BUSQUEDA_IDINT);


function jsaAsociar($idpersona, $idcontrol){
	$xP		= new cCreditosPreclientes($idcontrol);
	if($xP->init()){
		$xP->setPersona($idpersona);
	}
	return $xP->getMessages();	
}
function jsaInactivar($idcontrol){
	$xP		= new cCreditosPreclientes($idcontrol);
	if($xP->init()){
		$xP->setInactivo();
	}
	return $xP->getMessages();
}
$jxc ->exportFunction('jsaAsociar', array('idpersona', 'idcontrol'), "#idavisos");
$jxc ->exportFunction('jsaInactivar', array('idcontrol'), "#idavisos");
$jxc ->process();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init("jsInitComponents()");

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->setNoAcordion();
$xFRM->addSeccion("idsolicita", "TR.DATOS");

$xTabla		= new cCreditos_preclientes();
if($clave > 0){
	$xTabla->setData( $xTabla->query()->initByID($clave));
}
$xFRM->OHidden("idcontrol", $xTabla->idcontrol()->v());
$xFRM->OHidden("idpersona", $xTabla->idpersona()->v());
$xFRM->OHidden("idcredito", $xTabla->idcredito()->v());

$xFRM->ODate("fecha_de_registro", $xTabla->fecha_de_registro()->v(), "TR.FECHA DE REGISTRO");

$xFRM->OText("nombres", $xTabla->nombres()->v(), "TR.NOMBRE_COMPLETO");
$xFRM->OText_13("apellido1", $xTabla->apellido1()->v(), "TR.PRIMER_APELLIDO");
$xFRM->OText_13("apellido2", $xTabla->apellido2()->v(), "TR.SEGUNDO_APELLIDO");

if($SinDatosFiscales === true){
	$xFRM->OHidden("rfc", "");
} else {
	$xFRM->OText_13("rfc", $xTabla->rfc()->v(), "TR.RFC");
}

if($SinDatoPoblacional == true OR $userNoDNI == true){
	$xFRM->OHidden("curp", "");
} else {
	$xFRM->OText_13("curp", $xTabla->curp()->v(), "TR.CURP");
}

$xFRM->OMoneda("telefono", $xTabla->telefono()->v(), "TR.TELEFONO");
$xFRM->OText("email", $xTabla->email()->v(), "TR.EMAIL");

$xFRM->endSeccion();
$xFRM->addSeccion("iddcreds", "TR.CREDITO");

$xFRM->addHElem($xSel->getListaDeProductosDeCredito("producto", $xTabla->producto()->v())->get(true));
$xFRM->addHElem($xSel->getListaDePeriocidadDePago("periocidad", $xTabla->periocidad()->v(), false, true)->get(true));
$xFRM->addHElem( $xSel->getListaDeTipoDePago("tipocuota_id", $xTabla->tipocuota_id()->v(),true )->get(true) );
$xFRM->OTasaInt("tasa_interes", $xTabla->tasa_interes()->v(), "TR.TASA");

$xFRM->OEntero("pagos", $xTabla->pagos()->v(), "TR.PAGOS",100);
$xFRM->OMoneda2("monto", $xTabla->monto()->v(), "TR.MONTO");

$xFRM->addHElem($xSel->getListaDeDestinosDeCredito("aplicacion", $xTabla->aplicacion()->v())->get(true));
$xFRM->addHElem($xSel->getListaDeOficiales("idoficial",SYS_USER_ESTADO_ACTIVO, $xTabla->idoficial()->v())->get(true));

$xFRM->OText("notas", $xTabla->notas()->v(), "TR.NOTAS");

//$xFRM->OMoneda("idpersona", $xTabla->idpersona()->v(), "TR.IDPERSONA");
//$xFRM->OMoneda("idcredito", $xTabla->idcredito()->v(), "TR.IDCREDITO");

$xFRM->addCRUDSave($xTabla->get(), $clave, false, true);

$xFRM->OButton("TR.DESCARTAR", "jsSetDesactivar()", $xFRM->ic()->DESCARTAR, "cmdremove", "red");

$xFRM->addAviso("", "idavisos");


$xFRM->endSeccion();

if($xTabla->idpersona()->v() <= DEFAULT_SOCIO){
	$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA, "cmdpersonaadd", "persona");
	$xFRM->OButton("TR.VINCULAR PERSONA", "jsVincularPersona()", $xFRM->ic()->PERSONA, "cmdpersonalink", "persona");
	
	$xFRM->addSeccion("idlstparece", "TR.SIMILARES");
	$sqlTT	= $xLi->getListadoDeBusquedaSocios($xTabla->nombres()->v(), $xTabla->apellido1()->v(), $xTabla->apellido2()->v(), "", "");
	$xTT2	= new cTabla($sqlTT);
	$xTT2->OButton("TR.VINCULAR", "jsSetAsociar(" . HP_REPLACE_ID. ")", $xFRM->ic()->VINCULAR);
	$xFRM->addHElem($xTT2->Show());
} else {
	$xFRM->OButton("TR.VER PERSONA", "jsVerPersona()", $xFRM->ic()->PERSONA, "cmdviewpersona", "persona");
	$xFRM->addSeccion("ididper", "TR.PERSONA ASOCIADA");
	$xSoc	= new cSocio($xTabla->idpersona()->v());
	if($xSoc->init() == true){
		$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
		//Agregar Credito en caso de que exista
		if($xTabla->idcredito()->v() > DEFAULT_CREDITO){
			$xFRM->OButton("TR.VER CREDITO", "jsVerCredito()", $xFRM->ic()->CREDITO, "cmdcreditoview", "credito");
			$xCred	= new cCredito($xTabla->idcredito()->v());
			if($xCred->init() == true){
				$xFRM->addHElem($xCred->getFichaMini());
			}
		} else {
			$xFRM->OButton("TR.VINCULAR PERSONA", "jsVincularPersona()", $xFRM->ic()->PERSONA, "cmdpersonalink", "persona");
			$xFRM->OButton("TR.AGREGAR CREDITO", "jsAgregarCredito()", $xFRM->ic()->DINERO, "cmdcreditoadd", "credito");
		}
	} else {
		$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA, "cmdpersonaadd", "persona");
		$xFRM->OButton("TR.VINCULAR PERSONA", "jsVincularPersona()", $xFRM->ic()->PERSONA, "cmdpersonalink", "persona");
	}
}

$xFRM->addHElem("<div id='idcalendar'></div>");

$xFRM->OButton("TR.PLAN_DE_PAGOS", "jsCalcularPlan()", $xFRM->ic()->PLANDEPAGOS, "cmdgenplan", "whiteblue");
$xFRM->OButton("TR.IMPRIMIR", "jsVerCotizacion()", $xFRM->ic()->IMPRIMIR, "idimprimir");
$xFRM->OButton("TR.ENVIAR CORREO_ELECTRONICO", "jsSendMail0()", $xFRM->ic()->EMAIL, "idsendmail", "yellow");

$xFRM->endSeccion();

echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var xC		= new CredGen();
var xP		= new PersGen();


function jsInitComponents(){
	xG.desactiva("#idimprimir");
	xG.desactiva("#idsendmail");
}

function jsSetAsociar(idp){
	$("#idpersona").val(idp);
	xG.confirmar({msg: "Desea vincular a la persona " + idp + " a esta PRECLIENTE", callback:jsGoAsociar });
	
}
function jsGoAsociar(){
	jsaAsociar();
	xG.spin({time:2000, callback:onRefresh});
}
function jsSetDesactivar(){
	xG.confirmar({msg: "Desea descartar la PRECLIENTE", callback: jsaInactivar});
}
function jsAgregarCredito(){
	var idpersona	= $("#idpersona").val();
	var idcredito	= $("#idcredito").val();
	if(idpersona > DEFAULT_SOCIO){
		var producto	= $("#producto").val();
		var periocidad	= $("#periocidad").val();
		var pagos		= $("#pagos").val();
		var monto		= $("#monto").val();
		var aplicacion	= $("#aplicacion").val();
		var notas		= $("#notas").val();
		var idcontrol	= $("#idcontrol").val();
		var idpersona	= $("#idpersona").val();
		var idcredito	= $("#idcredito").val();
		var idoficial	= $("#idoficial").val();
		//origen 270 PRECLIENTES
		if(idcredito <= 0){
			xC.addCredito({persona: idpersona, monto: monto, producto:producto, origen:270, idorigen:idcontrol, frecuencia: periocidad, pagos: pagos, destino:aplicacion, oficial:idoficial});
		} else {
			xG.alerta({msg: "El Credito ya existe"});
		}
		
	} else {
		xG.alerta({msg: "Debe Vincular o Agregar una Persona"});
	}
}
function jsVincularPersona(){
	xP.getFormaBusqueda({control: "idpersona", callback: jsGoAsociar});
}
function jsAgregarPersona(){
	var idpersona	= $("#idpersona").val();
	var idcredito	= $("#idcredito").val();
	if(idpersona > DEFAULT_SOCIO){
		
	} else {
		var fecha_de_registro	= $("#fecha_de_registro").val();
		var nombres				= $("#nombres").val();
		var apellido1			= $("#apellido1").val();
		var apellido2			= $("#apellido2").val();
		var rfc					= $("#rfc").val();
		var curp				= $("#curp").val();
		var telefono			= $("#telefono").val();
		var email				= $("#email").val();
		var idcontrol			= $("#idcontrol").val();
		var idcredito			= $("#idcredito").val();
		var xurl				= "email=" + email + "&telefono=" + telefono + "&curp=" + curp + "&rfc=" + rfc + "&nombre=" + nombres + "&primerapellido=" + apellido1 + "&segundoapellido=" + apellido2 ;
		xP.goToAgregarFisicas({otros: xurl, claveorigen:idcontrol,tipoorigen:270});		
	}
}

function jsVerPersona(){
	var idpersona	= $("#idpersona").val();
	var idcredito	= $("#idcredito").val();
	xP.goToPanel(idpersona);
}
function jsVerCredito(){
	var idpersona	= $("#idpersona").val();
	var idcredito	= $("#idcredito").val();
	xC.goToPanelControl(idcredito);
}
function onRefresh(){
	window.location.reload();
}

function jsCalcularPlan(){
	tomail	= (typeof tomail == "undefined") ? false : tomail;
	
	$("#btn_guardar").click();

	var fecha_de_registro = $("#fecha_de_registro").val();

	var producto 		= $("#producto").val();
	
	var pagos 			= $("#pagos").val();
	var aplicacion 		= $("#aplicacion").val();

	var idcontrol 		= $("#idcontrol").val();
	var idpersona 		= $("#idpersona").val();
	var idcredito 		= $("#idcredito").val();
	
	var idmonto			= $("#monto").val();
	var idpagos			= $("#pagos").val();
	var idtasa			= $("#tasa_interes").val();
	var idfrecuencia	= $("#periocidad").val();
	
	var tipocuota_id 	= $("#tipocuota_id").val(); 
	var nombrec			= $.trim($("#nombres").val() + " " + $("#apellido1").val() + " " + $("#apellido2").val());
	var email			= $("#email").val();
	var idcontrol		= $("#idcontrol").val();


	xG.desactiva("#idimprimir");
	xG.desactiva("#idsendmail");
	
	var txt				= "";//"<h4>Cotizacion # " + idcontrol + "</h4>";		
	txt					+= "<table>";
	txt					+= "<tr><th>Nombre</th><td>" + nombrec + "</td></tr>";

	txt					+= "<tr><th>Pagos</th><td>" + idpagos + "</td></tr>";
	txt					+= "<tr><th>Tasa</th><td>" + idtasa + "</td></tr>";
	
	idperdesc			= $("#periocidad option:selected").text();
	txt					+= "<tr><th>Frecuencia:</th><td>" + idperdesc + "</td></tr>";
	
	txt					+= "</table>";
	
	session("data.head", base64.encode(txt));
	var urlm			= "../svc/cotizador.plan.svc.php?monto=" + idmonto + "&pagos=" + idpagos + "&redondeo=true&frecuencia=" +  idfrecuencia + "&tasa=" + idtasa + "&tipocuota=" + tipocuota_id + "&destino=" + aplicacion;
   	$.ajax(urlm, {
      success: function(data) {
	//alert(data.monto);
         //$('#main').html($(data).find('#main *'));
         //$('#notification-bar').text('The page has been successfully loaded');
		//$("#idletra").html("Cuota de Pago : $ " + getFMoney(data.monto));
		$("#idcalendar").html( base64.decode(data.html) );
		session("data.plan", data.html);
		xG.activa("#idimprimir");
		xG.activa("#idsendmail");
      },
      error: function() {
         //$('#notification-bar').text('An error occurred');
      }
   });
}
function jsVerCotizacion(){
	//var idnn			= $("#idconatencion").val();
	var urlm		= "../rpt_formatos/cotizador.plan.rpt.php?conuser=true";
	xG.w({url:urlm});
}
function jsSendMail0(){
	xG.confirmar({ msg: "MSG_CONFIRMA_ENVIO", title: "EMAIL", callback:jsSendMail});
}
function jsSendMail(){
	var email		= $("#email").val();
	var idd1		= session("data.head");
	var idd2		= session("data.plan");
	
	
	var urlm		= "../svc/mail.svc.php?email=" + email + "&idd1=" + idd1 + "&idd2=" +  idd2;
   	$.ajax(urlm, {
      success: function(data) {
		xG.alerta({msg:data.message});
      },
      error: function() {
        
      }
   });
}
</script>
<?php

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
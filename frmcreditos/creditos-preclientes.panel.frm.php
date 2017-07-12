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

$xHP->init();

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

$xFRM->OText("fecha_de_registro", $xTabla->fecha_de_registro()->v(), "TR.FECHA DE REGISTRO");

$xFRM->OText("nombres", $xTabla->nombres()->v(), "TR.NOMBRE_COMPLETO");
$xFRM->OText("apellido1", $xTabla->apellido1()->v(), "TR.PRIMER_APELLIDO");
$xFRM->OText("apellido2", $xTabla->apellido2()->v(), "TR.SEGUNDO_APELLIDO");

$xFRM->OText("rfc", $xTabla->rfc()->v(), "TR.RFC");
$xFRM->OText("curp", $xTabla->curp()->v(), "TR.CURP");

$xFRM->OMoneda("telefono", $xTabla->telefono()->v(), "TR.TELEFONO");
$xFRM->OText("email", $xTabla->email()->v(), "TR.EMAIL");

$xFRM->addHElem($xSel->getListaDeProductosDeCredito("producto", $xTabla->producto()->v())->get(true));

$xFRM->addHElem($xSel->getListaDePeriocidadDePago("periocidad", $xTabla->periocidad()->v())->get(true));

$xFRM->OMoneda("pagos", $xTabla->pagos()->v(), "TR.PAGOS");

$xFRM->OMoneda("monto", $xTabla->monto()->v(), "TR.MONTO");

$xFRM->addHElem($xSel->getListaDeDestinosDeCredito("aplicacion", $xTabla->aplicacion()->v())->get(true));
$xFRM->addHElem($xSel->getListaDeOficiales("idoficial",SYS_USER_ESTADO_ACTIVO, $xTabla->idoficial()->v())->get(true));

$xFRM->OTextArea("notas", $xTabla->notas()->v(), "TR.NOTAS");

//$xFRM->OMoneda("idpersona", $xTabla->idpersona()->v(), "TR.IDPERSONA");
//$xFRM->OMoneda("idcredito", $xTabla->idcredito()->v(), "TR.IDCREDITO");

$xFRM->addCRUDSave($xTabla->get(), $clave);

$xFRM->OButton("TR.DESCARTAR", "jsSetDesactivar()", $xFRM->ic()->DESCARTAR);

$xFRM->addAviso("", "idavisos");


$xFRM->endSeccion();

if($xTabla->idpersona()->v() <= DEFAULT_SOCIO){
	$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA);
	$xFRM->OButton("TR.VINCULAR PERSONA", "jsVincularPersona()", $xFRM->ic()->PERSONA);
	
	$xFRM->addSeccion("idlstparece", "TR.SIMILARES");
	$sqlTT	= $xLi->getListadoDeBusquedaSocios($xTabla->nombres()->v(), $xTabla->apellido1()->v(), $xTabla->apellido2()->v(), "", "");
	$xTT2	= new cTabla($sqlTT);
	$xTT2->OButton("TR.VINCULAR", "jsSetAsociar(" . HP_REPLACE_ID. ")", $xFRM->ic()->VINCULAR);
	$xFRM->addHElem($xTT2->Show());
} else {
	$xFRM->OButton("TR.VER PERSONA", "jsVerPersona()", $xFRM->ic()->PERSONA);
	$xFRM->addSeccion("ididper", "TR.PERSONA ASOCIADA");
	$xSoc	= new cSocio($xTabla->idpersona()->v());
	if($xSoc->init() == true){
		$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
		//Agregar Credito en caso de que exista
		if($xTabla->idcredito()->v() > DEFAULT_CREDITO){
			$xFRM->OButton("TR.VER CREDITO", "jsVerCredito()", $xFRM->ic()->CREDITO);
			$xCred	= new cCredito($xTabla->idcredito()->v());
			if($xCred->init() == true){
				$xFRM->addHElem($xCred->getFichaMini());
			}
		} else {
			$xFRM->OButton("TR.VINCULAR PERSONA", "jsVincularPersona()", $xFRM->ic()->PERSONA);
			$xFRM->OButton("TR.AGREGAR CREDITO", "jsAgregarCredito()", $xFRM->ic()->DINERO);
		}
	} else {
		$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA);
		$xFRM->OButton("TR.VINCULAR PERSONA", "jsVincularPersona()", $xFRM->ic()->PERSONA);
	}
}
$xFRM->endSeccion();

echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var xC		= new CredGen();
var xP		= new PersGen();

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
</script>
<?php

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
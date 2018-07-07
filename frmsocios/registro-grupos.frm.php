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
$xHP		= new cHPage("TR.Registro de GRUPO_SOLIDARIO");
$jxc 		= new TinyAjax();
$xLoc		= new cLocal();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frmregistrogrupos", "registro-personas.frm.php");
$xFRM->setTitle( $xHP->getTitle() );
$xFRM->setNoAcordion();
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xTxt2		= new cHText();
$xDate		= new cHDate();
$xDate2		= new cHDate(2, false, FECHA_TIPO_NACIMIENTO);
$xSel		= new cHSelect();
if($action == SYS_NINGUNO){	$xFRM->addGuardar("jsCheck()"); }
$xFRM->OButton("TR.Agregar PERSONA_FISICA", "jsAgregarRepLegal", $xFRM->ic()->PERSONA );

$xFRM->addSeccion("iddatosgenerales", "TR.Datos generales");


$xFRM->ODate("idfecharegistro", false,"TR.fecha de registro");

if(MULTISUCURSAL == false){
	$xFRM->OHidden("idsucursal", getSucursal());
} else {
	$xFRM->addHElem( $xSel->getListaDeSucursales()->get(true) );
}

if(SISTEMA_CAJASLOCALES_ACTIVA == false) {
	$xFRM->OHidden("idcajalocal", getCajaLocal());
} else {
	$xFRM->addHElem( $xSel->getListaDeCajasLocales("", false, getCajaLocal())->get(true) );
	$xFRM->addHElem( $xSel->getListaDeRegionDePersonas("", getRegion() )->get(true));
}

$xFRM->OHidden("idtipodeingreso", TIPO_INGRESO_GRUPO, "");

//$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonas("", PERSONAS_ES_MORAL)->get("TR.tipo de persona", true) );

$xFRM->OHidden("idfigurajuridica", PERSONAS_FIGURA_MORAL, "");
//$xFRM->addHElem( $xSel->getListaDeFigurasJuridicas("", PERSONAS_ES_MORAL)->get("TR.tipo de figura juridica", true) );

$xTxt2->setProperty("list", "dlBuscarPersona");
$xTxt2->addEvent("getListaSocios(this, event)", "onkeyup");

$xFRM->OText("idrazonsocial", "", "TR.Nombre del grupo");

//$xFRM->OHidden("idregimenfiscal",DEFAULT_REGIMEN_FISCAL, "TR.Regimen Fiscal")

//$xFRM->addHElem( $xSel->getListaDeRegimenesFiscales("", PERSONAS_ES_MORAL)->get("TR.Regimen Fiscal", true) );
//$xFRM->addHElem( $xSel->getListaDeGeneros()->get("TR.genero", true) );
//$xFRM->addHElem( $xSel->getListaDePaises("idpaisdeorigen")->get("TR.Pais de Origen", true) );

$xFRM->ODate("idfechanacimiento", false,"TR.fecha de creacion");

$sEstados	= $xSel->getListaDeEntidadesFed("identidadfederativanacimiento");

$xFRM->addHElem( $sEstados->get("TR.entidad de Creacion", true) );

$xFRM->OText_13("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "TR.localidad de creacion");
//$xFRM->addHElem( $xTxt->get("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "TR.localidad de creacion") );

//$sCivil		= $xSel->getListaDeEstadoCivil();
//$xFRM->addHElem( $sCivil->get("TR.estado civil", true) );

//$xFRM->addHElem( $xSel->getListaDeRegimenMatrimonio()->get(true) );

//$sFJ		= $xSel->getListaDeTipoDeIdentificacion("", PERSONAS_ES_MORAL);
//$xFRM->addHElem( $sFJ->get(true) );

//$xFRM->OText("idnumerodocumento","", "TR.Numero de Documento");
$xFRM->OMail("idemail", "");
$xFRM->OTelefono("idtelefono", "");

//$xFRM->addHElem( $xTxt->getEmail("idemail")  );
//$xFRM->addHElem( $xTxt->getNumero("idtelefono", "", "TR.Telefono")  );

$xTCURP		= new cHText();
$xTRFC		= new cHText();
$xTCURP->setProperty("required", "true");

//$xFRM->addHElem( $xTCURP->get("idcurp", "", "TR.IDENTIFICACION_POBLACIONAL") );
//$xFRM->addHElem( $xTRFC->get("idrfc", "", "TR.IDENTIFICACION_FISCAL") );

$xFRM->addObservaciones();

//$xFRM->OMoneda("iddependientes", 0, "TR.Dependientes economicos");
//
if( EACP_CLAVE_DE_PAIS == "MX"){
	//$xFRM->OText("idclavefiel", "", "TR.Clave FIEL");
	//$xFRM->OTextArea("idrazonnofiel","", "TR.Razones por la cual no tiene FIEL");
	$xFRM->OHidden("idrazonnofiel","GRUPO INFORMAL", "TR.Razones por la cual no tiene FIEL");

}
$xFRM->endSeccion(); $xFRM->addSeccion("idintegrantes", "TR.Integrantes");
$xFRM->addPersonaBasico("2", false, false, "", "TR.Representante_de_Grupo");
$xFRM->addPersonaBasico("3", false, false, "", "TR.Vocal_de_Grupo");
$xFRM->addPersonaBasico("4", false, false, "", "TR.INTEGRANTE_DE_GRUPO");
$xFRM->addPersonaBasico("5", false, false, "", "TR.INTEGRANTE_DE_GRUPO");
$xFRM->addPersonaBasico("6", false, false, "", "TR.INTEGRANTE_DE_GRUPO");
$xFRM->addPersonaBasico("7", false, false, "", "TR.INTEGRANTE_DE_GRUPO");
$xFRM->addPersonaBasico("8", false, false, "", "TR.INTEGRANTE_DE_GRUPO");
$xFRM->addPersonaBasico("9", false, false, "", "TR.INTEGRANTE_DE_GRUPO");

//$xFRM->OMoneda("iddescuento", 0, "TR.Descuento Deseado");

//$xFRM->addHElem( $xSel->getListaDeEmpresas("idempresa")->get(true) );
//$xFRM->addGrupoBasico();
$xFRM->addHTML("<datalist id=\"dlBuscarPersona\" ><option /></datalist>");
//$xFRM->endSeccion();
//$xFRM->addSeccion("iddatosgenerales", "TR.Domicilio");
//========================================================================== domicilio
//$xFRM->endSeccion(); $xFRM->addSeccion("iddomicilio", "TR.Domicilio");




$xFRM->endSeccion();


echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var gn			= new Gen();
var val			= new ValidGen();
var errors		= 0;
$(document).ready(function () {
	$('#id-frmregistrogrupos').isHappy({
	    fields: {
	      '#idrazonsocial': {
			required : true,
	        message: 'Necesita Capturar un Nombre',
			test : jsCheckNombres
	      },

		  "#idlugardenacimiento" : {
		  },

		  "#idemail" :{
			message : "Correo electronico No Valido",
			test : happy.email
		  },
		  '#idsocio2' : {
				required : true,
		        message: 'Necesita Capturar un Numero de Representante Legal',
				test : jsCheckReplegal			  
		  }	  
	    }
	  });	
});

function jsAgregarRepLegal(){
	var xPG = new PersGen(); 
	xPG.goToAgregarFisicasRelacion({otros :'&idsucursal=' + $('#idsucursal').val() , callback : jsSetRepLegal });	
}
function jsSetRepLegal(){
	$("#idsocio2").val(session(ID_PERSONA));
}
function jsCheckReplegal(){ return val.NoVacio($("#idsocio2").val()); }
//test: happy.email // this can be *any* function that returns true or false
function jsCheckDocto(){ return val.NoVacio($("#idnumerodocumento").val()); }
function jsCheckNombres(){	return val.NoVacio($("#idrazonsocial").val()); }

function jsCheckFigura(){
	var idfigurajuridica	= $("#idfigurajuridica").val();
	if (idfigurajuridica == 9) {
		//si es menor
		$("#idregimenfiscal").css("display", "none");
	} else {
		$("#idregimenfiscal").css("display", "inherit");
	}
	return true;
}
function jsCheckRegimenF(){
	var rs					= true;
	var idfigurajuridica	= $("#idfigurajuridica").val();
	var idregimenfiscal		= $("#idregimenfiscal").val();
	if (idfigurajuridica != 9 && idregimenfiscal == 1) {
		rs = false;
	}
	return rs;
}
function jsCheckFirmaElec(){
	var rs					= true;
	var idclavefiel		= $("#idclavefiel").val();
	var idrazonnofiel	= $("#idrazonnofiel").val();
	if ($.trim(idclavefiel) == "" && $.trim(idrazonnofiel) == "") {
		rs		= false;
	}
	return rs;
}
function jsCheck(){
	if ( $(".unhappyMessage").length > 0) {
		alert("Necesita corregir algunos datos para Guardar");
	} else {
		$('#id-frmregistrogrupos').submit();
	}
}
function getListaSocios(msrc, evt) {
	evt=(evt) ? evt:event;
	var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	
	var idKey					= "nombre";
	var idnombrecompleto 		= $("#idnombrecompleto").val();
	var idapellidopaterno 		= $("#idapellidopaterno").val();
	var idapellidomaterno 		= $("#idapellidomaterno").val();
	osrc						= msrc.id;
	if(osrc == "idapellidopaterno"){	idKey	= "apellidopaterno"; }
	if(osrc == "idapellidomaterno"){	idKey	= "apellidomaterno"; }
	var xUrl	= "../svc/personas.svc.php?n=" + idnombrecompleto + "&p=" + idapellidopaterno + "&m=" + idapellidomaterno;
	if ((charCode >= 65 && charCode <= 90)) {
		if ( String(msrc.value).length >= 3 ) {
			$("#dlBuscarPersona").empty();
			gn.DataList({
				url : xUrl,
				id : "dlBuscarPersona",
				key : idKey,
				label : "nombrecompleto"
				});	
		}
	}
}

function jsTestRFC(){
	var rs = true;
	if( EACP_CLAVE_DE_PAIS == "MX"){
		var xMx	= new Mexico();
		var idregimenfiscal	= $("#idregimenfiscal").val();
		if (entero(idregimenfiscal) > 1) {
			rs		= xMx.jsValidarRFC( $("#idrfc").val() );
		}
	}
	return rs;
}

/* ------------------------------- Domicilio -------------------------------- */

</script>
<?php
$xHP->fin();
?>
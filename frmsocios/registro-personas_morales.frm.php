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
$xHP		= new cHPage("TR.Registro de Personas");
$jxc 		= new TinyAjax();
$xLoc		= new cLocal();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

//con domicilio y tipo de domicilio
function jsaGetMunicipios($estado, $pais, $cp){
	$txt	= "";
	$text	= new cHText();
	$xSel	= new cHSelect();
	$text->setDivClass("");
	$mun	= false;
	if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cDomiciliosColonias();
			$xCol->existe($cp);
			$mun		= $xCol->getClaveDeMunicipio();
		}
	}
	return ($pais != EACP_CLAVE_DE_PAIS OR PERSONAS_VIVIENDA_MANUAL == true) ? $text->getDeNombreDeMunicipio("idnombremunicipio", "", "TR.Municipio") : $xSel->getListaDeMunicipios("", $estado, $mun)->get(false);
}
function jsaGetLocalidades($estado, $municipio, $pais, $cp){
	$xSel	= new cHSelect();
	$text	= new cHText();
	$txt	= "";
	$mun	= false;
	if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cDomiciliosColonias();
			$xCol->existe($cp);
			$mun		= $xCol->getClaveDeMunicipio();
			//$xCol->getNombreMunicipio();
		}
	}
	if(PERSONAS_VIVIENDA_MANUAL == true ){
		$text->setDivClass("");
		$txt	= $text->getDeNombreDeLocalidad("idnombrelocalidad", "", "TR.Localidad");
	} else {
		$xS 		= $xSel->getListaDeLocalidades("", $estado, $pais);
		$txt		= $xS->get(false);
		if($xS->getCountRows() <= 0){						//Corregir si no hay registros
			$text->setDivClass("");
			$txt	= $text->getDeNombreDeLocalidad("idnombrelocalidad", "", "TR.Localidad");
		}
	}
	return $txt;
}
function jsaBuscarCoincidencias($nombre){
	$xLoc		= new cLocal();
	$arrBusq	= array("N" => $nombre);
	$model		= array("N" => "nombrecompleto");
	$rs			= $xLoc->getListadoDePersonasBuscadas($arrBusq, $model);
	$xUL		= new cHUl();
	foreach ($rs as $rows){
		//var_dump($rows);
		$xUL->li($rows["nombrecompleto"]);
	}
	return  $xUL->get();
}

$jxc ->exportFunction('jsaGetMunicipios', array('identidadfederativa', 'idpais', 'idcodigopostal'), "#txtmunicipio");
$jxc ->exportFunction('jsaGetLocalidades', array('identidadfederativa', 'idmunicipio', 'idpais', 'idcodigopostal'), "#txtlocalidad");
$jxc ->exportFunction('jsaBuscarCoincidencias', array('idrazonsocial'), "#idcoincidencias");

$jxc ->process();

$xHP->init();

$xFRM		= new cHForm("frmsolingreso", "registro-personas.frm.php");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xTxt2		= new cHText();
$xDate		= new cHDate();
$xDate2		= new cHDate(2, false, FECHA_TIPO_NACIMIENTO);
$xSel		= new cHSelect();
$xHNot		= new cHNotif();

if($action == SYS_NINGUNO){	$xFRM->addGuardar("jsCheck()"); }
$xFRM->OButton("TR.Agregar PERSONA_FISICA", "jsAgregarRepLegal", $xFRM->ic()->PERSONA );

$xFRM->addSeccion("iddatosgenerales", "TR.Datos generales");


$xFRM->ODate("idfecharegistro", false,"TR.fecha de registro");
$xFRM->addHElem( $xSel->getListaDeSucursales()->get(true) );
if(SISTEMA_CAJASLOCALES_ACTIVA == false) {
	$xFRM->addFootElement("<input type='hidden' name='idcajalocal' name='idcajalocal' value='" . getCajaLocal() . "' />");
} else {
	$xFRM->addHElem( $xSel->getListaDeCajasLocales("", true)->get("TR.punto de acceso", true) );
}
$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonas("", PERSONAS_ES_MORAL)->get("TR.tipo de persona", true) );
$xFRM->OHidden("idfigurajuridica", PERSONAS_FIGURA_MORAL, "");
//$xFRM->addHElem( $xSel->getListaDeFigurasJuridicas("", PERSONAS_ES_MORAL)->get("TR.tipo de figura juridica", true) );

$xTxt2->setProperty("list", "dlBuscarPersona");
$xTxt2->addEvent("getListaSocios(this, event)", "onkeyup");
$xFRM->addHElem( $xTxt2->get("idrazonsocial", "", "TR.Denominacion / Razon Social") );


$xFRM->addHElem( $xSel->getListaDeRegimenesFiscales("", PERSONAS_ES_MORAL)->get("TR.Regimen Fiscal", true) );
//$xFRM->addHElem( $xSel->getListaDeGeneros()->get("TR.genero", true) );
$xFRM->addHElem( $xSel->getListaDePaises("idpaisdeorigen")->get("TR.Pais de Origen", true) );

$xFRM->ODate("idfechanacimiento", false,"TR.fecha de creacion");

$sEstados	= $xSel->getListaDeEntidadesFed("identidadfederativanacimiento");

$xFRM->addHElem( $sEstados->get("TR.entidad de Creacion", true) );

$xFRM->addHElem( $xTxt->get("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "TR.localidad de creacion") );

//$sCivil		= $xSel->getListaDeEstadoCivil();
//$xFRM->addHElem( $sCivil->get("TR.estado civil", true) );

//$xFRM->addHElem( $xSel->getListaDeRegimenMatrimonio()->get(true) );

$sFJ		= $xSel->getListaDeTipoDeIdentificacion("", PERSONAS_ES_MORAL);
$xFRM->addHElem( $sFJ->get(true) );

$xFRM->OText("idnumerodocumento","", "TR.Numero de Documento");

$xFRM->addHElem( $xTxt->getEmail("idemail")  );
$xFRM->addHElem( $xTxt->getNumero("idtelefono", "", "TR.Telefono")  );

$xTCURP		= new cHText();
$xTRFC		= new cHText();
$xTCURP->setProperty("required", "true");

//$xFRM->addHElem( $xTCURP->get("idcurp", "", "TR.IDENTIFICACION_POBLACIONAL") );
$xFRM->addHElem( $xTRFC->get("idrfc", "", "TR.IDENTIFICACION_FISCAL") );

$xFRM->addObservaciones();

//$xFRM->OMoneda("iddependientes", 0, "TR.Dependientes economicos");
//
if( EACP_CLAVE_DE_PAIS == "MX"){
	$xFRM->OText("idclavefiel", "", "TR.Clave FIEL");
	$xFRM->OTextArea("idrazonnofiel","", "TR.Razones por la cual no tiene FIEL");

} else {
	$xFRM->OHidden("idrazonnofiel", "", "TR.Razones por la cual no tiene FIEL");
}
if(MODULO_AML_ACTIVADO == true){
	$xFRM->OCheck("TR.PREGUNTA_AML_PERSONA_2", "esextranjero");
}

$xFRM->endSeccion(); $xFRM->addSeccion("iddreplegal", "TR.Representante_Legal");
$xFRM->addPersonaBasico("2");
//$xFRM->OMoneda("iddescuento", 0, "TR.Descuento Deseado");

//$xFRM->addHElem( $xSel->getListaDeEmpresas("idempresa")->get(true) );
//$xFRM->addGrupoBasico();
$xFRM->addHTML("<datalist id=\"dlBuscarPersona\" ><option /></datalist>");
//$xFRM->endSeccion();
//$xFRM->addSeccion("iddatosgenerales", "TR.Domicilio");
//========================================================================== domicilio
$xFRM->endSeccion(); $xFRM->addSeccion("iddomicilio", "TR.Domicilio");

$xCP	= new cHText(); $xTx3		= new cHText(); $xTxtE = new cHText(); $xChk	= new cHCheckBox(); $xTxt		= new cHText(); $xHSel	= new cHSelect();
$xFRM->addHElem( $xSel->getListaDeRegimenDeVivienda()->get(true) );
$xFRM->addHElem( $xSel->getListaDeTiposDeVivienda()->get(true) );
$xFRM->addHElem( $xSel->getListaDeTiempo()->get("TR.Tiempo_de_Residencia", true) );

$lsPaises		= $xSel->getListaDePaises();
$lsPaises->addEvent("onchange", "jsSetEstadoPorPais(this)");
$xFRM->addHElem( $lsPaises->get(true) );

$xFRM->addHElem( $xCP->getNumero("idcodigopostal", $xLoc->DomicilioCodigoPostal(), "TR.codigo_postal" ));

$sentidades		= $xSel->getListaDeEntidadesFed("", true);
$sentidades->addEvent("onchange", "jsaGetMunicipios");
$xFRM->addHElem( $sentidades->get(true) );

$xHSel->setEnclose(false);
$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
$xTxtE->setDivClass("");
$xFRM->addDivSolo($xHSel->get("idtipoacceso", "", "calle"), $xTxtE->getNormal("idnombreacceso", ""), "tx14", "tx34" );

$xFRM->addHElem( $xTxt->getNormal("idnumeroexterior", "", "TR.Numero_Exterior") );
$xFRM->addHElem( $xTxt->getNormal("idnumerointerior", "", "TR.Numero_Interior") );

$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", EACP_COLONIA, "TR.Colonia" ) );


if(PERSONAS_VIVIENDA_MANUAL == true){
	$xFRM->addHElem($xTx3->getDeNombreDeMunicipio("idnombremunicipio", "", "TR.Municipio"));
	$xFRM->addHElem($xTx3->getDeNombreDeLocalidad("idnombrelocalidad", "", "TR.Localidad"));	
} else {
	$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
	$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");	
}


$xFRM->addHElem( $xTxt->getNumero("idtelefono1", "", "TR.TELEFONO_FIJO") );
//$xFRM->addHElem( $xTxt->getNumero("idtelefono2", "", "TR.TELEFONO_MOVIL") );

$xFRM->OText("idreferencias", "", "TR.Referencias");
//$xFRM->addHElem( $xChk->get("TR.Domicilio Principal?", "idprincipal") );
$xFRM->addFootElement("<input type='hidden' id='idcolonia' name='idcolonia' value='' />");
//$xFRM->addFootElement("<input type='hidden' id='idsocio' name='idsocio' value='$persona' />");
$xFRM->endSeccion();

$xFRM->addFooterBar($xHNot->get(" ", "idcoincidencias", $xHNot->NOTICE));

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var gn			= new Gen();
var val			= new ValidGen();
var errors		= 0;
$(document).ready(function () {
	$('#id-frmsolingreso').isHappy({
	    fields: {
	      '#idrazonsocial': {
			required : true,
	        message: 'Necesita Capturar un Nombre',
			test : jsCheckNombres
	      },
		  "#idfigurajuridica" : {
			test : jsCheckFigura
		  },
		  "#idregimenfiscal" : {
			test : jsCheckRegimenF,
			message: 'Debe capturar un Regimen Fiscal'
		  },

		  "#idlugardenacimiento" : {
			  required : true,
		  },

		  "#idemail" :{
			message : "Correo electronico No Valido",
			test : happy.email
		  },
		  "#idrazonnofiel" : {
			message : "Necesita un CODIGO de FIEL o Una Razon por la cual no tiene",
			test : jsCheckFirmaElec
		  },
		  "#idnumerodocumento" :{
			required : true,
			message : "Necesita un Documento de Identificacion",
			test : jsCheckDocto
		  },
		  "#idrfc" : {
			required : true,
			message : "Necesita un RFC Valido",
			test : jsTestRFC
		  },
		  '#idsocio2' : {
				required : true,
		        message: 'Necesita Capturar un Numero de Representante Legal',
				test : jsCheckReplegal			  
		  },
	      '#idcodigopostal': {
				required : true,
		        message: 'Necesita Capturar un Codigo postal',
				test : jsGetDatosHeredados
		      },
		      "#idnombreacceso" : {
			      required : true,
			      message : "Necesita una Nombre para la calle",
			      test : jsCheckCalle
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
	if( EACP_CLAVE_DE_PAIS == "MX"){
		var idclavefiel		= $("#idclavefiel").val();
		var idrazonnofiel	= $("#idrazonnofiel").val();
		if ($.trim(idclavefiel) == "" && $.trim(idrazonnofiel) == "") {
			rs		= false;
		}
	}
	return rs;
}
function jsCheck(){
	if ( $(".unhappyMessage").length > 0) {
		alert("Necesita corregir algunos datos para Guardar");
	} else {
		$('#id-frmsolingreso').submit();
	}
}
function getListaSocios(msrc, evt) {
	evt=(evt) ? evt:event;
	var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	
	//var idKey					= "nombre";
	//var idnombrecompleto 		= $("#idnombrecompleto").val();
	//var idapellidopaterno 		= $("#idapellidopaterno").val();
	//var idapellidomaterno 		= $("#idapellidomaterno").val();
	osrc						= msrc.id;
	//if(osrc == "idapellidopaterno"){	idKey	= "apellidopaterno"; }
	//if(osrc == "idapellidomaterno"){	idKey	= "apellidomaterno"; }
	//var xUrl	= "../svc/personas.svc.php?n=" + idnombrecompleto + "&p=" + idapellidopaterno + "&m=" + idapellidomaterno;
	if ((charCode >= 65 && charCode <= 90)) {
		jsaBuscarCoincidencias();	
		/*if ( String(msrc.value).length >= 3 ) {
			$("#dlBuscarPersona").empty();
			gn.DataList({
				url : xUrl,
				id : "dlBuscarPersona",
				key : idKey,
				label : "nombrecompleto"
				});	
		}*/
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

var mEdoAc	= <?php echo $xLoc->DomicilioEstadoClaveNum(); ?>;
var xGen	= new Gen();
var xVal	= new ValidGen();
$(document).ready(function () {
	$('#id-frmvivienda').isHappy({
	    fields: {
	      '#idcodigopostal': {
			required : true,
	        message: 'Necesita Capturar un Codigo postal',
			test : jsGetDatosHeredados
	      },
	      "#idnombreacceso" : {
		      required : true,
		      message : "Necesita una Nombre para la calle",
		      test : jsCheckCalle
	    }
	    }
	});	
});
function jsCheckCalle(){ return xVal.NoVacio($("#idnombreacceso").val()); }
function jsGetDatosHeredados(){
	var xPais	= ($("#idpais").length > 0) ? $("#idpais").val() : 0; //EACP_CLAVE_DE_PAIS
	if ($("#idcodigopostal").length > 0) {
		var cp		= $("#idcodigopostal").val();
		if( cp > 0){
			//si esl pais es diferente...
			if (xPais == EACP_CLAVE_DE_PAIS) {
				//buscar por codigo postal
				xGen.pajax({
					url : "../svc/colonias.svc.php?limit=1&cp=" + cp,
					finder : "codigo",
					result : "json",
					callback: setDatosPorCodigoPostal
					});
			}
		}
		return xVal.NoCero($("#idcodigopostal").val());
	}
	return true;
}

function setDatosPorCodigoPostal(obj) {
	for(mob in obj){
		var mdats	= obj[mob];
		if (flotante(mdats.estado) > 0) {
			mEdoAc	= mdats.estado;
			$("#identidadfederativa").val(mEdoAc);
		
			setTimeout("jsaGetMunicipios()", 1000);
			setTimeout("jsaGetLocalidades()", 1000);
		}
	}
}

function initComponents(){  }
function jsSetEstadoPorPais(osrc){
	var mpais	= osrc.value;
	if(mpais != EACP_CLAVE_DE_PAIS){
		$("#identidadfederativa").val(98);
		$("#identidadfederativa").css("display", "none");
		jsaGetMunicipios();
		jsaGetLocalidades();		
	} else {
		$("#identidadfederativa").val(mEdoAc);
		$("#identidadfederativa").css("display", "inherit");		
	}
}

</script>
<?php
$xHP->fin();
?>
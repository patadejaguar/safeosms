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
$xF			= new cFecha();

$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$tipo_de_ingreso	= parametro("idtipodeingreso", DEFAULT_TIPO_INGRESO, MQL_INT);
$desde_sucursal		= parametro("idsucursal", false, MQL_RAW);
$con_domicilio		= parametro("domicilio", false, MQL_BOOL);
$con_relacion		= parametro("relaciones", false, MQL_INT);
$con_actividad		= parametro("actividad", false, MQL_BOOL);
$con_nacimiento		= parametro("nacimiento", true, MQL_BOOL);
$documento_rel		= parametro("iddocumentorelacionado", false, MQL_INT);
$persona_rel		= parametro("idpersonarelacionado", false, MQL_INT);
$tipo_de_domicilio	= parametro("tipodomicilio", false, MQL_INT);
$con_legal			= parametro("legal", true, MQL_BOOL);

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
	return ($pais != EACP_CLAVE_DE_PAIS) ? $text->getDeNombreDeMunicipio("idnombremunicipio", "", "TR.Municipio") : $xSel->getListaDeMunicipios("", $estado, $mun)->get(false);
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

function jsaBuscarCoincidencias($nombre, $primerapellido, $segundoapellido){
	$xLoc		= new cLocal();
	$arrBusq	= array("AP" => $primerapellido, "AM" => $segundoapellido,  "N" => $nombre);
	$model		= array("completo" => "nombrecompleto");
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
$jxc ->exportFunction('jsaBuscarCoincidencias', array('idnombrecompleto', 'idapellidopaterno', 'idapellidomaterno'), "#idcoincidencias");
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
$xChk		= new cHCheckBox();

if($action == SYS_NINGUNO){	$xFRM->addGuardar("jsCheck()"); }
//=========================================== AVALES
if(setNoMenorQueCero($con_relacion) > 0){
	$xHSel	= new cHSelect(); 
	$tipoRe	= "";
	if( setNoMenorQueCero($persona_rel) <= 0 ){
		if($con_relacion == iDE_CREDITO){
			$xDoc			= new cCredito($documento_rel); $xDoc->init();
			$persona_rel	= $xDoc->getClaveDePersona();
			$tipoRe			= PERSONAS_REL_CLASE_AVAL;
			
		}
		//TODO: Iniciar cuenta de captacion
	}
	$tipo_de_ingreso= TIPO_INGRESO_RELACION;
	$con_domicilio	= true;
	$con_actividad	= true;
	$desde_sucursal	= getSucursal();
	
	$xFRM->addHElem( $xSel->getListaDeTiposDeRelaciones("", $tipoRe)->get(true) );
	$xFRM->addHElem( $xChk->get("TR.es dependiente_economico", "dependiente") );
	$xFRM->addHElem( $xHSel->getListaDeTiposDeParentesco()->get(true)  );
	$xFRM->OHidden("iddocumentorelacionado", $documento_rel, "");
	$xFRM->OHidden("idpersonarelacionado", $persona_rel, "");
	$xFRM->OHidden("idorigenrelacionado", $con_relacion, "");
}

//if($con_domicilio == true){ $xFRM->addSeccion("iddatosgenerales", "TR.Datos Generales"); }
if($tipo_de_ingreso == DEFAULT_TIPO_INGRESO){
	$xFRM->ODate("idfecharegistro", false,"TR.fecha de registro");
}
if($desde_sucursal == false){
	$xFRM->addHElem( $xSel->getListaDeSucursales()->get(true) );
} else {
	$xFRM->OHidden("idsucursal", $desde_sucursal, "");
}
if(SISTEMA_CAJASLOCALES_ACTIVA == false) {
	$xFRM->addFootElement("<input type='hidden' name='idcajalocal' name='idcajalocal' value='" . getCajaLocal() . "' />");
} else {
	$xFRM->addHElem( $xSel->getListaDeCajasLocales("", true)->get("TR.punto de acceso", true) );
}

if($tipo_de_ingreso != DEFAULT_TIPO_INGRESO){
	$xFRM->OHidden("idtipodeingreso", $tipo_de_ingreso, "");
} else {
	$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonas("", PERSONAS_ES_FISICA, $tipo_de_ingreso)->get("TR.tipo de persona", true) );
}
//
$xFRM->OHidden("idfigurajuridica", PERSONAS_FIGURA_FISICA, "");
//$xFRM->addHElem( $xSel->getListaDeFigurasJuridicas("", PERSONAS_ES_FISICA)->get("TR.tipo de figura juridica", true) );

$xTxt2->setProperty("list", "dlBuscarPersona");
$xTxt2->addEvent("getListaSocios(this, event)", "onkeyup");
$xFRM->addHElem( $xTxt2->get("idnombrecompleto", "", "TR.nombre completo") );
$xFRM->addHElem( $xTxt2->get("idapellidopaterno", "", "TR.primer apellido") );
$xFRM->addHElem( $xTxt2->get("idapellidomaterno", "", "TR.segundo apellido") );



$xFRM->addHElem( $xSel->getListaDeGeneros()->get("TR.genero", true) );
$xFRM->addHElem( $xSel->getListaDePaises("idpaisdeorigen")->get("TR.Pais de Origen", true) );

if($con_nacimiento == true){
	$xFRM->ODate("idfechanacimiento", false,"TR.fecha de Nacimiento");
	$sEstados	= $xSel->getListaDeEntidadesFed("identidadfederativanacimiento");
	$xFRM->addHElem( $sEstados->get("TR.entidad de nacimiento", true) );
	$xFRM->addHElem( $xTxt->get("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "TR.localidad de Nacimiento") );
} else {
	$xFRM->OHidden("identidadfederativanacimiento", $xLoc->DomicilioEstadoClaveABC(), "");
	$xFRM->OHidden("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "");
	$xFRM->OHidden("idfechanacimiento", "01-01-2001" , "");
}


$xFRM->addHElem( $xTxt->getEmail("idemail")  );
$xFRM->addHElem( $xTxt->getNumero("idtelefono", "", "TR.Telefono")  );
$xFRM->OMoneda("iddependientes", 0, "TR.Dependientes_economicos");


if($con_legal == true){
	$sCivil		= $xSel->getListaDeEstadoCivil();
	$xFRM->addHElem( $sCivil->get("TR.estado civil", true) );
	$xFRM->addHElem( $xSel->getListaDeRegimenMatrimonio()->get(true) );
		
	$xFRM->addHElem( $xSel->getListaDeRegimenesFiscales("", PERSONAS_ES_FISICA)->get("TR.Regimen Fiscal", true) );
	
	$sFJ		= $xSel->getListaDeTipoDeIdentificacion();
	$xFRM->addHElem( $sFJ->get(true) );
	$xFRM->OText("idnumerodocumento","", "TR.Numero de Documento");
	
	$xTCURP		= new cHText();
	$xTRFC		= new cHText();
	$xTCURP->setProperty("required", "true");
	$xFRM->addHElem( $xTCURP->get("idcurp", "", "TR.IDENTIFICACION_POBLACIONAL") );
	$xFRM->addHElem( $xTRFC->get("idrfc", DEFAULT_PERSONAS_RFC_GENERICO, "TR.IDENTIFICACION_FISCAL") );
	
	
	if( EACP_CLAVE_DE_PAIS == "MX"){
		if($tipo_de_ingreso == TIPO_INGRESO_RELACION){ //sinfiel
			$xFRM->OHidden("idclavefiel", "", "TR.Clave_FIEL");
			$xFRM->OHidden("idrazonnofiel","NA", "TR.Razones por la cual no tiene FIEL");
		} else {
			$xFRM->OText("idclavefiel", "", "TR.Clave_FIEL");
			$xFRM->OTextArea("idrazonnofiel","", "TR.RAZON_POR_NO CLAVE_FIEL");
		}
	} else {
		$xFRM->OHidden("idrazonnofiel", "", "TR.Razones por la cual no tiene FIEL");
	}
} else {
	$xFRM->OHidden("idclavefiel", "", "TR.Clave_FIEL");
	$xFRM->OHidden("idrazonnofiel","NA", "TR.Razones por la cual no tiene FIEL");
	$xFRM->OHidden("idrfc", DEFAULT_PERSONAS_RFC_GENERICO, "TR.IDENTIFICACION_FISCAL");
	$xFRM->OHidden("idcurp", "", "TR.IDENTIFICACION_POBLACIONAL");
	$xFRM->OHidden("idnumerodocumento","000000000000", "");
	$xFRM->OHidden("idtipoidentificacion",DEFAULT_TIPO_IDENTIFICACION, "");
	$xFRM->OHidden("idregimenfiscal",DEFAULT_REGIMEN_FISCAL, "");
	$xFRM->OHidden("idestadocivil", DEFAULT_ESTADO_CIVIL, "");
	$xFRM->OHidden("idregimenmatrimonial", DEFAULT_REGIMEN_CONYUGAL, "");
}
//===
if(MODULO_AML_ACTIVADO == true){
	$xFRM->OCheck("TR.PREGUNTA_AML_PERSONA_1", "espep");
	$xFRM->OCheck("TR.PREGUNTA_AML_PERSONA_2", "esextranjero");
}
if($tipo_de_ingreso == TIPO_INGRESO_RELACION){ //sinfie
	
} else {
	//Nomina
	$xFRM->OMoneda("iddescuento", 0, "TR.Descuento Deseado");
	$xFRM->addHElem( $xSel->getListaDeEmpresas("idempresa")->get(true) );
	
	$xFRM->addObservaciones();
	//grupos solidarios
	$xFRM->addGrupoBasico();
}
$xFRM->addHTML("<datalist id=\"dlBuscarPersona\" ><option /></datalist>");

/**--------------------- DOMICLIO --------------------------------------- */
if($con_domicilio == true){
	//$xFRM->endSeccion();
	//$xFRM->addSeccion("iddatosgenerales", "TR.Domicilio");
	
	$xCP	= new cHText(); $xTx3		= new cHText(); $xTxtE = new cHText(); $xChk	= new cHCheckBox(); $xTxt		= new cHText(); $xHSel	= new cHSelect();
	$xFRM->addHElem( $xSel->getListaDeRegimenDeVivienda()->get(true) );
	$xFRM->OHidden("idtipodevivienda", TIPO_DOMICILIO_PARTICULAR, "");
	//$xFRM->addHElem( $xSel->getListaDeTiposDeVivienda()->get(true) );
	
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
	//$xFRM->endSeccion();
}
if($con_actividad == true){
	$xFRM->OText("idrazonsocialtrabajo", "", "TR.Empresa_donde_labora");
	$xTLi	= new cHText();
	$xTLi->addEvent("getListadoAE(this, event)", "onkeyup");
	$xTLi->setProperty("list", "dlBuscarActividad");
	$xFRM->addHElem($xTLi->get("idactividad", FALLBACK_ACTIVIDAD_ECONOMICA, "TR.Clave de Actividad" ));	
	$xFRM->addHElem($xTxt->getDeMoneda("idingresos", "TR.Ingreso Mensual", 0));
	$xFRM->addHElem("<datalist id='dlBuscarActividad'><option /></datalist>");
}

$xFRM->addFooterBar($xHNot->get(" ", "idcoincidencias", $xHNot->NOTICE));

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var gn			= new Gen();
var val			= new ValidGen();
var errors		= 0;
var evalFiscal	= true;
var evalCivil	= true;
<?php
if($tipo_de_ingreso == TIPO_INGRESO_RELACION OR $con_legal == false ){
echo "evalFiscal = false;\n evalCivil = false; \n"; 
}
if(EACP_CLAVE_DE_PAIS != "MX"){
	echo "evalFiscal = false;\n";	
}
?>
$(document).ready(function () {
	$('#id-frmsolingreso').isHappy({
	    fields: {
		   "#idtipodeingreso" : {
			   test: jsCheckTipoIngreso
		   },
	      '#idnombrecompleto': {
			required : true,
	        message: 'Necesita Capturar un Nombre',
			test : jsCheckNombres
	      },
	      '#idapellidopaterno': {
	        required : true,
	        message: 'Necesita al menos un apellido',
	        test : jsCheckApellido
	      },
		  "#idfigurajuridica" : {
			test : jsCheckFigura
		  },
		  "#idregimenfiscal" : {
			test : jsCheckRegimenF,
			message: 'Debe capturar un Regimen Fiscal'
		  },
		  "#idestadocivil" : {
			test : jsCheckEstadoCivil
		  },
		  "#idregimenmatrimonial" : {
			test : jsCheckRegimenMat,
			message: 'Debe capturar un Regimen Matrimonial Valido'			
		  },
		  "#idlugardenacimiento" : {
			test : jsObtenCURP
		  },
		  "#idcurp" : {
			test : jsTestCURP,
			message: 'La Clave Unica de Poblacion parece no Valida'						
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
			message : "Necesita un RFC Valido",
			test : jsTestRFC
		  }
	    }
	  });	
});
//test: happy.email // this can be *any* function that returns true or false
function jsCheckDocto(){ return val.NoVacio($("#idnumerodocumento").val()); }
function jsCheckApellido(){ return val.NoVacio($("#idapellidopaterno").val()); }
function jsCheckNombres(){	return val.NoVacio($("#idnombrecompleto").val()); }
function jsCheckEstadoCivil(){
	var idestadocivil			= $("#idestadocivil").val();
	if (idestadocivil != 1 && idestadocivil != 7) {
		//si es menor
		$("#idregimenmatrimonial").val("NINGUNO");
		$("#idregimenmatrimonial").css("display", "none");
	} else {
		$("#idregimenmatrimonial").css("display", "inherit");
	}
	return true;	
}
function jsCheckTipoIngreso(){

	var idtipodeingreso	= entero($("#idtipodeingreso").val());
	if (idtipodeingreso == TIPO_INGRESO_RELACION ) {
		//code
		$("#idempresa").css("display", "none");
		$("#iddescuento").css("display", "none");
	} else {
		$("#idempresa").css("display", "inherit");
		$("#iddescuento").css("display", "inherit");
	}
	if (idtipodeingreso != TIPO_INGRESO_GRUPO ) {
		$("#dividgrupo").css("display", "none");
	} else {
		$("#dividgrupo").css("display", "inline-flex");
	}	
	return true;
}
function jsCheckRegimenMat(){
	var rs						= true;
	var idestadocivil			= $("#idestadocivil").val();
	var idregimenmatrimonial	= $("#idregimenmatrimonial").val();
	if ( (idestadocivil == 1||idestadocivil ==7) &&  idregimenmatrimonial == "NINGUNO"){
		rs = false;
	}
	return (evalCivil == true) ? rs : true;
}
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
	return (evalFiscal == true) ? rs : true;
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
	return (evalFiscal == true) ? rs : true;
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
	
	var idKey					= "nombre";
	var idnombrecompleto 		= $("#idnombrecompleto").val();
	var idapellidopaterno 		= $("#idapellidopaterno").val();
	var idapellidomaterno 		= $("#idapellidomaterno").val();
	osrc						= msrc.id;
	if(osrc == "idapellidopaterno"){	idKey	= "apellidopaterno"; }
	if(osrc == "idapellidomaterno"){	idKey	= "apellidomaterno"; }
	var xUrl	= "../svc/personas.svc.php?n=" + idnombrecompleto + "&p=" + idapellidopaterno + "&m=" + idapellidomaterno;
	if ((charCode >= 65 && charCode <= 90)) {
		jsaBuscarCoincidencias();
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


function jsObtenCURP(){
	var idnombrecompleto	= $("#idnombrecompleto").val();
	var idapellidopaterno	= $("#idapellidopaterno").val();
	var idapellidomaterno	= $("#idapellidomaterno").val();
	var idgenero			= $("#idgenero").val();
	var idfechanacimiento	= $("#idfechanacimiento").val();
	var identidadfederativa	= $("#identidadfederativanacimiento").val();

	if( EACP_CLAVE_DE_PAIS == "MX"){
		var xMx	= new Mexico();
		if (String($("#idcurp").val() ).length > 10) {
			//code
		} else {
			$("#idcurp").val( xMx.jsGetCURP(idnombrecompleto, idapellidopaterno, idapellidomaterno, idfechanacimiento, idgenero, identidadfederativa ) );
			$("#idrfc").val( String($("#idcurp").val() ).substring(0,10) );
		}
	}
	return true;
}
function jsTestCURP(){
	var rs = true;
	if( EACP_CLAVE_DE_PAIS == "MX"){
		var xMx	= new Mexico();
		rs		= xMx.jsValidarCURP( $("#idcurp").val() );
	}
	return (evalFiscal == true) ? rs : true;
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
	return (evalFiscal == true) ? rs : true;
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
//======================================= AE
function getListadoAE(msrc, evt){
	evt=(evt) ? evt:event;
	var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	
	var idKey					= "clave_de_actividad";
	//console.log(osrc);
	var xUrl	= "../svc/personas.actividades.economicas.php?action=LIST&lim=5&arg=" + msrc.value;
	if ((charCode >= 65 && charCode <= 90)) {
		if ( String(msrc.value).length >= 3 ) {
			$("#dlBuscarActividad").empty();
		xGen.DataList({
				url : xUrl,
				id : "dlBuscarActividad",
				key : idKey,
				label : "nombre_de_la_actividad"
				});	
		}
	}
}
</script>
<?php
$xHP->fin();
?>
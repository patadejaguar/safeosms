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
$xHP		= new cHPage("TR.Registro de Persona_MORAL");
$jxc 		= new TinyAjax();
$xLoc		= new cLocal();
$xRuls		= new cReglaDeNegocio();
$xTipoI		= new cPersonasTipoDeIngreso(0);


$SinDatosFiscales	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATOS_FISCALES);		//regla de negocio


$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$NombreCompleto		= parametro("nombrecompleto");$NombreCompleto		= parametro("nombre", $NombreCompleto);
$IDFiscal			= parametro("idfiscal", DEFAULT_PERSONAS_RFC_GENERICO);
$telefono			= parametro("telefono",0, MQL_INT);
$email				= parametro("email");
$email				= strtolower($email);
$tipoorigen			= parametro("tipoorigen",0, MQL_INT);
$claveorigen		= parametro("claveorigen",0, MQL_INT);
$tipo_de_ingreso	= parametro("idtipodeingreso", DEFAULT_TIPO_INGRESO, MQL_INT); $tipo_de_ingreso	= parametro("tipodeingreso", $tipo_de_ingreso, MQL_INT); $tipo_de_ingreso	= parametro("tipoingreso", $tipo_de_ingreso, MQL_INT);
$ConRepresentante	= true;
$ConDatosFIEL		= true;

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
$jxc ->exportFunction('jsaBuscarCoincidencias', array('idrazonsocial'), "#fb_frmsolingreso");

$jxc ->process();

$xHP->init();

$xFRM		= new cHForm("frmsolingreso", "registro-personas.frm.php");
$xFRM->setAction("registro-personas.frm.php", true);
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

$xFRM->addSeccion("iddatosgenerales_f", "TR.Datos generales");
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
if($tipo_de_ingreso <= 0 OR $tipo_de_ingreso == DEFAULT_TIPO_INGRESO OR $tipo_de_ingreso == FALLBACK_PERSONAS_TIPO_ING){
	$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonas("", PERSONAS_ES_MORAL, $tipo_de_ingreso)->get("TR.tipo de persona", true) );
} else {
	$xFRM->OHidden("idtipodeingreso", $tipo_de_ingreso);
	switch ($tipo_de_ingreso){
		case $xTipoI->TIPO_PROVEEDOR:
			$ConDatosFIEL		= false;
			$ConRepresentante	= false;
			break;
	}
}
$xFRM->OHidden("idfigurajuridica", PERSONAS_FIGURA_MORAL, "");



$xTxt2->setProperty("list", "dlBuscarPersona");
$xTxt2->addEvent("getListaSocios(this, event)", "onkeyup");
$xFRM->addHElem( $xTxt2->get("idrazonsocial", $NombreCompleto, "TR.Nombre / Razon_Social") );
$xFRM->setValidacion("idrazonsocial", "jsCheckNombres", 'Necesita Capturar un Nombre', true);

if($SinDatosFiscales == false){ 
	$xFRM->addHElem( $xSel->getListaDeRegimenesFiscales("", PERSONAS_ES_MORAL)->get("TR.Regimen Fiscal", true) );
	$xFRM->setValidacion("idregimenfiscal", "jsCheckRegimenF", 'Debe capturar un Regimen Fiscal');
}

if(MODULO_AML_ACTIVADO == true){
	$xFRM->addHElem( $xSel->getListaDePaises("idpaisdeorigen")->get("TR.Pais de Origen", true) );
} else {
	$xFRM->OHidden("idpaisdeorigen", EACP_CLAVE_DE_PAIS);
}



$sEstados	= $xSel->getListaDeEntidadesFed("identidadfederativanacimiento");

$xFRM->addHElem( $sEstados->get("TR.entidad de Creacion", true) );
$xFRM->OText_13("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "TR.lugar de creacion");
$xFRM->setValidacion("idlugardenacimiento", "validacion.novacio", "Obligatorio lugar de Creacion");


$xFRM->OMail("idemail", $email);

 
$xFRM->addHElem( $xTxt->getNumero("idtelefono", $telefono, "TR.Telefono")  );

$xFRM->OText_13("idrfc", "", "TR.IDENTIFICACION_FISCAL");
$xFRM->setValidacion("idrfc", "jsTestRFC", "Necesita un Numero de Identificacion Fiscal Valido", true);



if( EACP_CLAVE_DE_PAIS == "MX" AND $ConDatosFIEL == true){
	$xFRM->OText_13("idclavefiel", "", "TR.Clave_FIEL");
	$xFRM->setValidacion("idclavefiel", "validacion.novacio", true);
	$xFRM->OText("idrazonnofiel","", "TR.RAZON_POR_NO CLAVE_FIEL");
	$xFRM->setValidacion("idrazonnofiel", "jsCheckFirmaElec", "Necesita una CLAVE_FIEL o Una Razon por la cual no tiene");
} else {
	$xFRM->OHidden("idrazonnofiel", "");
}
if(MODULO_AML_ACTIVADO == true){
	$xFRM->OCheck("TR.PREGUNTA_AML_PERSONA_2", "esextranjero");
}

$xFRM->addObservaciones();
$xFRM->endSeccion();
//========================================================================== Acta constitutiva
$xFRM->addSeccion("idddacta", "TR.ACTACONSTITUTIVA");
//$sFJ		= $xSel->getListaDeTipoDeIdentificacion("", PERSONAS_ES_MORAL);
//$xFRM->addHElem( $sFJ->get(true) );
$xFRM->OHidden("idtipoidentificacion", PERSONAS_TIPO_IDENT_MORAL);

$xFRM->OText_13("idnumerodocumento","", "TR.idescritura");
$xFRM->setValidacion("idnumerodocumento", "validacion.novacio", "Necesita un Documento de Identificacion", true);

$xFRM->ODate("idfechanacimiento", false,"TR.fecha constitucion");

$xFRM->OText("idnotarioconst", "", "TR.NOMBRE NOTARIO constitucion");
$xFRM->OText_13("ididnotariaconts", "", "TR.Numero NOTARIA Constitucion");
$xFRM->OText_13("idfolioconst", "", "TR.IDREGISTROPUB");
$xFRM->endSeccion();
//========================================================================== rep legal
if($ConRepresentante == true){
	$xFRM->addSeccion("iddreplegal", "TR.Representante_Legal");
	$xFRM->setValidacion("idsocio2", "validacion.persona", 'Necesita Capturar un Numero de Representante Legal', true);
	$xFRM->addPersonaBasico("2");
	$xFRM->addHTML("<datalist id=\"dlBuscarPersona\" ><option /></datalist>");
	$xFRM->OText_13("idpodernotarial","", "TR.idpoder");
	$xFRM->ODate("idfechapoder", fechasys(), "TR.FECHA PODERNOTARIAL");
	$xFRM->OText("idnotariopoder", "", "TR.NOMBRE NOTARIO PODERNOTARIAL");
	$xFRM->OText_13("ididnotariapoder", "", "TR.Numero NOTARIA");
	//$xFRM->OText("idfoliopoder", "", "TR.FOLIO");
	$xFRM->endSeccion();
}
//========================================================================== domicilio
$xFRM->addSeccion("iddomiciliom", "TR.Domicilio");
$xCP	= new cHText(); $xTx3		= new cHText(); $xTxtE = new cHText(); $xTxt		= new cHText(); $xHSel	= new cHSelect();

$xFRM->addHElem( $xSel->getListaDeRegimenDeVivienda("", PERSONAS_REG_VIV_PROPIA)->get(true) );
$xFRM->OHidden("idtipodevivienda", PERSONAS_TIPO_DOM_FISCAL);

$xFRM->addHElem( $xSel->getListaDeTiempo()->get("TR.Tiempo_de_Residencia", true) );

$lsPaises		= $xSel->getListaDePaises();
$lsPaises->addEvent("onchange", "jsSetEstadoPorPais(this)");
$xFRM->addHElem( $lsPaises->get(true) );

$xFRM->addHElem( $xCP->getNumero("idcodigopostal", $xLoc->DomicilioCodigoPostal(), "TR.codigo_postal" ));
$xFRM->setValidacion("idcodigopostal", "jsGetDatosHeredados", 'Necesita Capturar un Codigo postal', true);
$xFRM->OButton("TR.BUSCAR COLONIA", "var xD=new DomGen();xD.getBuscarColonias()", $xFRM->ic()->BUSCAR);

$sentidades		= $xSel->getListaDeEntidadesFed("", true);
$sentidades->addEvent("onchange", "jsaGetMunicipios");
$xFRM->addHElem( $sentidades->get(true) );

$xHSel->setEnclose(false);
$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );

//$xTxtE->setDivClass("tx1");
$xFRM->addHElem($xSel->getListaDeTiposDeAcceso()->get(true));
$xFRM->OText("idnombreacceso", "", "TR.NOMBRE DEL ACCESO");
//$xFRM->addDivSolo($xSel->getListaDeTiposDeAcceso()->get(), $xTxtE->getNormal("idnombreacceso", "", "TR.NOMBRE DEL ACCESO"), "tx14", "tx34" );

$xFRM->setValidacion("idnombreacceso", "validacion.calle", "Nombre del Acceso Obligatorio", true);

$xFRM->OText_13("idnumeroexterior", "", "TR.Numero_Exterior");
$xFRM->OText_13("idnumerointerior", "", "TR.Numero_Interior");

$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", $xLoc->DomicilioColonia(), "TR.Colonia" ) );


if(PERSONAS_VIVIENDA_MANUAL == true){
	$xFRM->addHElem($xTx3->getDeNombreDeMunicipio("idnombremunicipio", "", "TR.Municipio"));
	$xFRM->addHElem($xTx3->getDeNombreDeLocalidad("idnombrelocalidad", "", "TR.Localidad"));	
} else {
	$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
	$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");	
}

$xFRM->addHElem( $xTxt->getNumero("idtelefono1", "", "TR.TELEFONO_FIJO") );
$xFRM->OText("idreferencias", "", "TR.Referencias");


$xFRM->endSeccion();

$xFRM->addFooterBar("<br />");

//=============== Datos de origen / Ocultos
$xFRM->OHidden("tipoorigen", $tipoorigen);
$xFRM->OHidden("claveorigen", $claveorigen);
$xFRM->OHidden("idcolonia", "");
$xFRM->OHidden("idprincipal", "true");

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var gn			= new Gen();
var val			= new ValidGen();
var errors		= 0;
var xGen		= new Gen();
var xVal		= new ValidGen();
var xP			= new PersGen();

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
function jsValidarExistePersona(existe){
	if(existe == true){
		alert("La persona existe en el Sistema");
		xGen.activarForma();
	}
}

function getListaSocios(msrc, evt) {
	evt=(evt) ? evt:event;
	var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	
	//var idKey					= "nombre";
	//var idnombrecompleto 		= $("#idnombrecompleto").val();
	//var idapellidopaterno 		= $("#idapellidopaterno").val();
	//var idapellidomaterno 		= $("#idapellidomaterno").val();
	var osrc					= msrc.id;
	var idnombre		 		= $("#idrazonsocial").val();
	//if(osrc == "idapellidopaterno"){	idKey	= "apellidopaterno"; }
	//if(osrc == "idapellidomaterno"){	idKey	= "apellidomaterno"; }
	//var xUrl	= "../svc/personas.svc.php?n=" + idnombrecompleto + "&p=" + idapellidopaterno + "&m=" + idapellidomaterno;
	var sibusq					= (String(idnombre).length >= 4) ? true : false;
	if ((charCode >= 65 && charCode <= 90) && sibusq == true) {
		//jsaBuscarCoincidencias();	
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
	var rs 		= true;
	var xRFC	= $("#idrfc").val();
	if( EACP_CLAVE_DE_PAIS == "MX"){
		var xMx	= new Mexico();
		var idregimenfiscal	= $("#idregimenfiscal").val();
		if (entero(idregimenfiscal) > 1) {
			rs		= xMx.jsValidarRFC( xRFC );
		}
	} else {
		var xLoc	= new LocalGen();
		rs			= xLoc.validarNIF(xRFC );
	}
	if(rs == true){	xP.setBuscarPorIDs({fiscal:xRFC, callback: jsValidarExistePersona});	}	
	return rs;
}

/* ------------------------------- Domicilio -------------------------------- */

var mEdoAc	= <?php echo $xLoc->DomicilioEstadoClaveNum(); ?>;
var xGen	= new Gen();
var xVal	= new ValidGen();

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
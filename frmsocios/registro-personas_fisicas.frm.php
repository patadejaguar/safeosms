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
$xHP				= new cHPage("TR.Registro de Persona_FISICA");
$jxc 				= new TinyAjax();
$xLoc				= new cLocal();
$xF					= new cFecha();
$xRuls				= new cReglaDeNegocio();

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

$PrimerApellido		= parametro("primerapellido");
$SegundoApellido	= parametro("segundoapellido");
$Nombres			= parametro("nombre");
$IDFiscal			= parametro("idfiscal", DEFAULT_PERSONAS_RFC_GENERICO);
$IDPoblacional		= parametro("idpoblacional");
$NombreCompleto		= parametro("nombrecompleto");		
$telefono			= parametro("telefono",0, MQL_INT);
$email				= parametro("email");
$email				= strtolower($email);
$fecha				= parametro("fecha", fechasys(), MQL_DATE);

$tipoorigen			= parametro("tipoorigen",0, MQL_INT);
$claveorigen		= parametro("claveorigen",0, MQL_INT);

if($Nombres == "" AND $NombreCompleto !== ""){
	$xImp			= new cTiposLimpiadores();
	$DNombre		= $xImp->cleanNombreComp($NombreCompleto);
	$PrimerApellido	= ($PrimerApellido == "") ? $DNombre[0] : $PrimerApellido;
	$SegundoApellido= ($SegundoApellido == "") ? $DNombre[1] : $SegundoApellido;
	$Nombres		= $DNombre[2];
}

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

//REGLA: PERSONAS_FILTRAR_CAJASLO
//con domicilio y tipo de domicilio
function jsaGetMunicipios($identidadfederativa, $pais, $cp){
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
			$txt		= $xCol->getNombreMunicipio();
		}
	}
	return ($pais != EACP_CLAVE_DE_PAIS OR PERSONAS_VIVIENDA_MANUAL == true) ? $text->getDeNombreDeMunicipio("idnombremunicipio", $txt, "TR.Municipio") : $xSel->getListaDeMunicipios("", $identidadfederativa, $mun)->get(false);
}
function jsaGetLocalidades($identidadfederativa, $municipio, $pais, $cp){
	$xSel	= new cHSelect();
	$text	= new cHText();
	$txt	= "";
	$v		= "";
	if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cDomiciliosColonias();
			if($xCol->existe($cp) == true){
				$v		= $xCol->getNombreLocalidad();
			}
		}
	}
	if(PERSONAS_VIVIENDA_MANUAL == true ){
		$text->setDivClass("");
		$txt		= $text->getDeNombreDeLocalidad("idnombrelocalidad", $v, "TR.Localidad");
	} else {
		$xS 		= $xSel->getListaDeLocalidades("", $identidadfederativa, $pais);
		$txt		= $xS->get(false);
		if($xS->getCountRows() <= 0){						//Corregir si no hay registros
			$text->setDivClass("");
			$txt	= $text->getDeNombreDeLocalidad("idnombrelocalidad", $v, "TR.Localidad");
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
$jxc ->exportFunction('jsaBuscarCoincidencias', array('idnombrecompleto', 'idapellidopaterno', 'idapellidomaterno'), "#fb_frmsolingreso");
$jxc ->process();


$xHP->init();

$xFRM		= new cHForm("frmsolingreso", "registro-personas.frm.php");
$xFRM->setTitle( $xHP->getTitle() );
$xFRM->setAction("registro-personas.frm.php", true);

$xBtn		= new cHButton();
$xTxt		= new cHText();
$xTxt2		= new cHText();
$xDate		= new cHDate();
$xDate2		= new cHDate(2, false, FECHA_TIPO_NACIMIENTO);
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();
$xFRM->setNoAcordion();

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
	$tipo_de_ingreso	= TIPO_INGRESO_RELACION;
	$con_domicilio		= ($RelsSinDom == true) ? false : true;
	$con_actividad		= true;
	$desde_sucursal		= getSucursal();
	
	$xFRM->addHElem( $xSel->getListaDeTiposDeRelaciones("", $tipoRe, false, true)->get(true) );
	$xFRM->addHElem( $xHSel->getListaDeTiposDeParentesco()->get(true)  );
	$xFRM->addHElem( $xChk->get("TR.es dependiente_economico", "dependiente") );
	
	$xFRM->OHidden("iddocumentorelacionado", $documento_rel, "");
	$xFRM->OHidden("idpersonarelacionado", $persona_rel, "");
	$xFRM->OHidden("idorigenrelacionado", $con_relacion, "");
}

//if($con_domicilio == true){ $xFRM->addSeccion("iddatosgenerales", "TR.Datos Generales"); }
if($tipo_de_ingreso == DEFAULT_TIPO_INGRESO){
	$xFRM->ODate("idfecharegistro", $fecha,"TR.fecha de registro");
}
if($desde_sucursal == false){
	$xFRM->addHElem( $xSel->getListaDeSucursales()->get(true) );
} else {
	$xFRM->OHidden("idsucursal", $desde_sucursal, "");
}
if(SISTEMA_CAJASLOCALES_ACTIVA == false) {
	$xFRM->OHidden("idcajalocal", getCajaLocal());
} else {
	if($tipo_de_ingreso == DEFAULT_TIPO_INGRESO){
		//Region
		$xSelReg	= $xSel->getListaDeRegionDePersonas("", getRegion() );
		$xSelReg->addEvent("onblur", "jsGetListaDePuntosDeAtencion()");
		$xFRM->addHElem( $xSelReg->get(true));
		//Centro de atencion
		$xSelCL		= $xSel->getListaDeCajasLocales("", false, getCajaLocal());
		$xSelCL->setUseIDDiv();
		$xFRM->addHElem( $xSelCL->get(true) );
		
	} else {
		$xFRM->OHidden("idcajalocal", getCajaLocal());
	}

}

if($tipo_de_ingreso != DEFAULT_TIPO_INGRESO){
	$xFRM->OHidden("idtipodeingreso", $tipo_de_ingreso, "");
} else {
	$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonas("", PERSONAS_ES_FISICA, $tipo_de_ingreso)->get("TR.tipo de persona", true) );
}
//Agregar Clasificaciones
if($xclass == true){
	$xFRM->addHElem( $xSel->getListaDePersonasXClass()->get(true));
}
if($yclass == true){
	$xFRM->addHElem( $xSel->getListaDePersonasYClass()->get(true));
}
if($zclass == true){
	$xFRM->addHElem( $xSel->getListaDePersonasZClass()->get(true));
}
//Agregar ID Interna
$xFRM->OText_13("idinterno", "", "TR.IDINTERNO");
//============================================== Datos Generales
$xFRM->addSeccion("iddivgeneral", "TR.DATOS_GENERALES");
$xFRM->OHidden("idfigurajuridica", PERSONAS_FIGURA_FISICA, "");
//$xFRM->addHElem( $xSel->getListaDeFigurasJuridicas("", PERSONAS_ES_FISICA)->get("TR.tipo de figura juridica", true) );

$xTxt2->setProperty("list", "dlBuscarPersona");
$xTxt2->addEvent("getListaSocios(this, event)", "onkeyup");
$xFRM->addHElem( $xTxt2->get("idnombrecompleto", $Nombres, "TR.nombre completo") );
$xFRM->addHElem( $xTxt2->get("idapellidopaterno", $PrimerApellido, "TR.primer apellido") );
$xFRM->addHElem( $xTxt2->get("idapellidomaterno", $SegundoApellido, "TR.segundo apellido") );
$xFRM->addHElem( $xSel->getListaDeGeneros()->get("TR.genero", true) );
if(MODULO_AML_ACTIVADO == true OR PERSONAS_ACEPTAR_EXTRANJEROS == true){
	$xFRM->addHElem( $xSel->getListaDePaises("idpaisdeorigen")->get("TR.Pais de Origen", true) );
}

if($con_nacimiento == true){
	$xFRM->ODate("idfechanacimiento", false,"TR.fecha de Nacimiento");
	$xFRM->setValidacion("idfechanacimiento", "validacion.fechaNacimiento", "Error en Fecha");
	$sEstados	= $xSel->getListaDeEntidadesFed("identidadfederativanacimiento");
	$xFRM->addHElem( $sEstados->get("TR.entidad de nacimiento", true) );
	$xFRM->addHElem( $xTxt->get("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "TR.Lugar de Nacimiento") );
	$xFRM->setValidacion("idlugardenacimiento", "jsObtenCURP");
} else {
	$xFRM->OHidden("identidadfederativanacimiento", $xLoc->DomicilioEstadoClaveABC(), "");
	$xFRM->OHidden("idlugardenacimiento", $xLoc->DomicilioMunicipio(), "");
	$xFRM->OHidden("idfechanacimiento", "01-01-2001" , "");
}

$xFRM->OMail("idemail", $email);
$xFRM->addHElem( $xTxt->getNumero("idtelefono", $telefono, "TR.Telefono_Principal")  );

if($tipo_de_ingreso == TIPO_INGRESO_RELACION){
	
} else {
	$xFRM->OText("idprofesion", "", "TR.titulo_personal");
	$xFRM->OMoneda("iddependientes", 0, "TR.Dependientes_economicos");
}
//}


if($con_legal == true ){
	//============== Estado civil
	$sCivil		= $xSel->getListaDeEstadoCivil();
	$xFRM->addHElem( $sCivil->get("TR.estado_civil", true) );
	$xFRM->setValidacion("idestadocivil", "jsCheckEstadoCivil");
	if($SinRegimenMat == false){
		$xFRM->addHElem( $xSel->getListaDeRegimenMatrimonio()->get(true) );
		$xFRM->setValidacion("idregimenmatrimonial", "jsCheckRegimenMat", "Debe capturar un Regimen Matrimonial Valido");
	} else {
		$xFRM->OHidden("idregimenmatrimonial", DEFAULT_REGIMEN_CONYUGAL);
	}
	
	//======= Fiscal
	if($SinDatosFiscales === true){ } else {
		$xFRM->addHElem( $xSel->getListaDeRegimenesFiscales("", PERSONAS_ES_FISICA)->get("TR.Regimen_Fiscal", true) );
		//$xFRM->setValidacion("idfigurajuridica", "jsCheckFigura");
		$xFRM->setValidacion("idregimenfiscal", "jsCheckRegimenF", "Debe capturar un Regimen Fiscal");
	}
	//======= Poblacional
	if($SinDatosDocto === true){
		$xFRM->OHidden("idnumerodocumento", "");
	} else {
		$sFJ		= $xSel->getListaDeTipoDeIdentificacion();
		$xFRM->addHElem( $sFJ->get(true) );
		$xFRM->OText_13("idnumerodocumento","", "TR.Numero de Documento");
		$xFRM->setValidacion("idnumerodocumento", "jsCheckDocto", "Necesita un Documento de Identificacion Valido", true);
	}
	//======= Fiscal	
	if($SinDatoPoblacional == true OR $userNoDNI == true){
		$xFRM->OHidden("idcurp", "");
	} else {
		$xTCURP		= new cHText();
		$xTCURP->setDiv13();
		$xFRM->addHElem( $xTCURP->get("idcurp", $IDPoblacional, "TR.IDENTIFICACION_POBLACIONAL") );
		if($RN_NoValidarCurp == false){
			$xTCURP->setProperty("required", "true");
			$xFRM->setValidacion("idcurp", "jsTestCURP", "TR.CURP Invalido");
		}
	}
	if($SinDatosFiscales === true){
		$xFRM->OHidden("idrfc", "");
	} else {
		$xTRFC		= new cHText();
		$xTRFC->setDiv13();
		$xFRM->addHElem( $xTRFC->get("idrfc", $IDFiscal, "TR.IDENTIFICACION_FISCAL") );
		$xFRM->setValidacion("idrfc", "jsTestRFC", "TR.RFC invalido");
	}
		
	if( EACP_CLAVE_DE_PAIS == "MX" ){
		if($tipo_de_ingreso == TIPO_INGRESO_RELACION){ //sinfiel
			$xFRM->OHidden("idclavefiel", "");
			$xFRM->OHidden("idrazonnofiel","NA" );
		} else {
			$xFRM->OText_13("idclavefiel", "", "TR.Clave_FIEL");
			$xFRM->OText("idrazonnofiel","", "TR.RAZON_POR_NO CLAVE_FIEL");
			$xFRM->setValidacion("idrazonnofiel", "jsCheckFirmaElec", "Necesita un CODIGO de FIEL o Una Razon por la cual no tiene");
		}
	} else {
		$xFRM->OHidden("idclavefiel", "");
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
} else {
	if(PERSONAS_ACEPTAR_EXTRANJEROS == true){
		//$xFRM->OCheck("TR.PREGUNTA_AML_PERSONA_2", "esextranjero");
	}
}
$xFRM->endSeccion();
//================================================

if($tipo_de_ingreso == TIPO_INGRESO_RELACION){ //sinfie
	
} else {
	
	//============================ Extranjeros
	
	if($useDExtranjero == true){
		$xFRM->addSeccion("idivextranjeros", "TR.DATOS EXTRANJEROS");
		$xFRM->OText("idextranjeropermiso", "", "TR.PERMISO_DE_RESIDENCIA");
		$xFRM->ODate("idextranjeroregistro", false, "TR.EXTRANJERO_REGISTRO");
		$xFRM->ODate("idextranjerovencimiento", false, "TR.EXTRANJERO_VENCIMIENTO");
		$xFRM->endSeccion();
	}
	if($useDColegiacion == true){
		$xFRM->addSeccion("iddivcolegiacion", "TR.DATOS COLEGIACION");
		$xFRM->addHElem( $xSel->getListaDePersonasMembresia("idtipomembresia")->get("TR.TIPO_MEMBRESIA",true));
		$xFRM->addHElem( $xSel->getListaDeDiasDelMes("", $xF->dia())->get("TR.DIA DE PAGO", true) );
		$xFRM->addHElem( $xSel->getListaDeTipoDeLugarDeCobro()->get(true) );
		$xFRM->addHElem( $xSel->getListaDePersonasZClass("idgradoacademico")->get("TR.GRADO_ACADEMICO",true));
		$xFRM->OText("idcolegiacion", "", "TR.IDCOLEGIACION");
		$xFRM->OText("iddatosemergencia", "", "TR.DATO_EMERGENCIA");
		$xFRM->endSeccion();
	}
	$xFRM->addSeccion("iddivextras", "TR.OTROS DATOS");
	//Nomina
	if(PERSONAS_CONTROLAR_POR_EMPRESA == false){
		//Sin empresa
	} else {
		if(MODULO_CAPTACION_ACTIVADO == true){
			$xFRM->OMoneda("iddescuento", 0, "TR.Descuento Deseado");
		}
		$xFRM->addHElem( $xSel->getListaDeEmpresas("idempresa")->get(true) );
	}
	if(PERSONAS_CONTROLAR_POR_GRUPO == true){
		$xFRM->addGrupoBasico();
	} else {
		$xFRM->OHidden("idgrupo", DEFAULT_GRUPO);
	}
	if($useDColegiacion == false AND $useDatosAccidente == true){
		$xFRM->OText("iddatosemergencia", "", "TR.DATO_EMERGENCIA");
	}
	$xFRM->addObservaciones();
	//grupos solidarios
	$xFRM->endSeccion();
}
$xFRM->addHTML("<datalist id=\"dlBuscarPersona\" ><option /></datalist>");

/**--------------------- DOMICLIO --------------------------------------- */
if($con_domicilio == true){
	//$xFRM->endSeccion();
	$xFRM->addSeccion("iddivdomicilio", "TR.Domicilio y Empleo");
	
	$xCP	= new cHText(); $xTx3		= new cHText(); $xTxtE = new cHText(); $xChk	= new cHCheckBox(); $xTxt		= new cHText(); $xHSel	= new cHSelect();
	$xFRM->addHElem( $xSel->getListaDeRegimenDeVivienda()->get(true) );
	$xFRM->OHidden("idtipodevivienda", TIPO_DOMICILIO_PARTICULAR, "");
	$xFRM->addHElem( $xSel->getListaDeTiempo()->get("TR.Tiempo_de_Residencia", true) );
	
	if(MODULO_AML_ACTIVADO == true){
		$lsPaises		= $xSel->getListaDePaises();
		$lsPaises->addEvent("onchange", "jsSetEstadoPorPais(this)");
		$xFRM->addHElem( $lsPaises->get(true) );
	} else {
		$xFRM->OHidden("idpais", EACP_CLAVE_DE_PAIS);
	}
	
	$xFRM->addHElem( $xCP->getNumero("idcodigopostal", $xLoc->DomicilioCodigoPostal(), "TR.codigo_postal" ));
	$xFRM->OButton("TR.BUSCAR COLONIA", "var xD=new DomGen();xD.getBuscarColonias()", $xFRM->ic()->BUSCAR);
	$xFRM->setValidacion("idcodigopostal", "validacion.codigopostal", "TR.Obligatorio codigo_postal", true);
	$xFRM->addJsInit("jsSiEsViviendaManual();");
	
	$sentidades		= $xSel->getListaDeEntidadesFed("", true);
	$sentidades->addEvent("onchange", "jsaGetMunicipios");
	$xFRM->addHElem( $sentidades->get(true) );
	
	if($SinDetalleAcceso == true){
		$xFRM->OText("idnombreacceso", "", "TR.Domicilio");
		$xFRM->OHidden("idtipoacceso", "calle");
	} else {
		$xTxtE->setDivClass("");
		$xFRM->addDivSolo($xSel->getListaDeTiposDeAcceso()->get(false), $xTxtE->getNormal("idnombreacceso", "", "TR.Nombre del Acceso"), "tx14", "tx34" );
		$xFRM->setValidacion("idnombreacceso", "validacion.calle", "TR.Obligatorio nombre de Acceso", true);
	}	
	$xFRM->addHElem( $xTxt->getNormal("idnumeroexterior", "", "TR.Numero_Exterior") );
	
	$xFRM->addHElem( $xTxt->getNormal("idnumerointerior", "", "TR.Numero_Interior") );
	$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", EACP_COLONIA, "TR.Colonia" ) );
	
	if(PERSONAS_VIVIENDA_MANUAL == true){
		$xFRM->addHElem($xTx3->getDeNombreDeMunicipio("idnombremunicipio", "", "TR.Municipio"));
		$xFRM->addHElem($xTx3->getDeNombreDeLocalidad("idnombrelocalidad", "", "TR.Localidad"));
	} else {
		$xFRM->setValidacion("idnumeroexterior", "validacion.novacio", "TR.Obligatorio Numero_exterior", true);
		$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
		$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");	
	}
	
	$xFRM->addHElem( $xTxt->getNumero("idtelefono1", $telefono, "TR.TELEFONO_FIJO") );
	$xFRM->OText("idreferencias", "", "TR.Referencias");
	$xFRM->addFootElement("<input type='hidden' id='idcolonia' name='idcolonia' value='' />");
	$xFRM->endSeccion();	
}
if($con_actividad == true AND $tipo_de_ingreso != TIPO_INGRESO_RELACION){
	$xFRM->OText("idrazonsocialtrabajo", "", "TR.Empresa_donde_labora");
	$xTLi	= new cHText();
	if($EsSimple == true){
		$xFRM->addHElem( $xSel->getListaDeTiposdeActividadEconomica("idactividad", FALLBACK_ACTIVIDAD_ECONOMICA)->get(true) );
		$xFRM->ODate("idfechaingreso", fechasys(), "TR.Fecha de Ingreso");
		$xFRM->OMoneda("idsalario", 0, "TR.Ingreso_Mensual");
	} else {
		$xFRM->ODate("idfechaingreso", fechasys(), "TR.Fecha de Ingreso");
		$xFRM->OMoneda("idsalario", 0, "TR.Ingreso Mensual");
		$xFRM->addHElem( $xTLi->getDeActividadEconomica("idactividad", FALLBACK_ACTIVIDAD_ECONOMICA, "TR.Clave de Actividad") );
		$xFRM->setValidacion("idactividad", "validacion.actividadeconomica", "TR.ACTIVIDAD_ECONOMICA invalido");
	}
	$xFRM->setValidacion("idsalario", $xFRM->VALIDARCANTIDAD, "TR.El Ingreso_Mensual debe ser mayor a 0");
}

$xFRM->addFooterBar(" ");

$xFRM->setValidacion("idtipodeingreso", "jsCheckTipoIngreso");
$xFRM->setValidacion("idnombrecompleto", "jsCheckNombres", "Necesita Capturar un Nombre", true);
$xFRM->setValidacion("idapellidopaterno", "jsCheckApellido", "Necesita Capturar al menos un Apellido", true);
$xFRM->setValidacion("idemail", "happy.email", $xFRM->l()->getMensajeByTop("GENERAL_FALTA_MAIL"));
$xFRM->setNoAcordion();


//=============== Datos de origen
$xFRM->OHidden("tipoorigen", $tipoorigen);
$xFRM->OHidden("claveorigen", $claveorigen);


echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var xG			= new Gen();
var val			= new ValidGen();
var errors		= 0;
var evalFiscal	= true;
var evalCivil	= true;
var xGen		= new Gen();
var xVal		= new ValidGen();
var xP			= new PersGen();
var mOmitirDNI	= <?php echo ($userNoDNI == true) ? "true" : "false"; ?>;
var mIDDoctoPob	= <?php echo PERSONAS_CLAVE_ID_POBLACIONAL; ?>;
<?php
if($tipo_de_ingreso == TIPO_INGRESO_RELACION OR $con_legal == false ){
echo "evalFiscal = false;\nevalCivil = false; \n"; 
}
?>

function jsSiEsViviendaManual(){
	if(PERSONAS_VIVIENDA_MANUAL == true){
		setTimeout("jsaGetMunicipios()", 1000);
		setTimeout("jsaGetLocalidades()", 1000);		
	}
}

function jsCheckDocto(){
	var iddocumento	= $.trim($("#idnumerodocumento").val());
	var tipodocto	= entero($("#idtipoidentificacion").val());
	if(mOmitirDNI == false){
		return val.NoVacio(iddocumento);
	} else {
		var rs		= true;
		var xCurp	= iddocumento;
		if(tipodocto != mIDDoctoPob){
			return true;
		} else {
			if( EACP_CLAVE_DE_PAIS == "MX"){
				var xMx	= new Mexico();
				rs		= xMx.jsValidarCURP( xCurp );
			} else {
				var xLoc= new LocalGen();
				rs 		= xLoc.validarDNI(xCurp);
			}
			if(rs == true){
				//guardar curp
				$("#idcurp").val(xCurp);
				//Cargar existencia
				//si existe bloquear form
				xP.setBuscarPorIDs({poblacional:xCurp, callback: jsValidarExistePersona});
			}
			return (evalFiscal == true) ? rs : true;
		}
	}
}
function jsCheckApellido(){ return val.NoVacio($("#idapellidopaterno").val()); }
function jsCheckNombres(){	return val.NoVacio($("#idnombrecompleto").val()); }
function jsCheckEstadoCivil(){
	var idestadocivil			= $("#idestadocivil").val();
	if (idestadocivil != 1 && idestadocivil != 7) {
		//si es menor
		$("#idregimenmatrimonial").val("NINGUNO");
		xG.verControl("idregimenmatrimonial", false);
	} else {
		xG.verControl("idregimenmatrimonial", true);
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
		xG.verControl("idregimenfiscal", false);
	} else {
		xG.verControl("idregimenfiscal", true);
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
	$('#id-frmsolingreso').submit();
	if(xG.happy() == true){
		xG.spinInit();
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
		//var xPer	= new PersGen();
		//xPer.showBuscarPersonas({ paterno : idapellidopaterno, materno : idapellidomaterno, nombre : idnombrecompleto });
		if ( String(msrc.value).length >= 3 ){
			$("#dlBuscarPersona").empty();
			xG.DataList({
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
	var rs		= true;
	var xCurp	= $("#idcurp").val();
	if( EACP_CLAVE_DE_PAIS == "MX"){
		var xMx	= new Mexico();
		rs		= xMx.jsValidarCURP( xCurp );
	} else {
		var xLoc= new LocalGen();
		rs 		= xLoc.validarDNI(xCurp);
	}
	if(rs == true){
		//Cargar existencia
		if(session("curp.valida." + xCurp) == null){
			//si existe bloquear form
			xP.setBuscarPorIDs({poblacional:xCurp, callback: jsValidarExistePersona});
		} else {
			setLog("Cup Validada " + xCurp);
		}
	}

	return (evalFiscal == true) ? rs : true;
}
function jsValidarExistePersona(existe){
	if(existe == true){
		alert("La persona existe en el Sistema");
		xGen.activarForma();
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
	} else {
		var xLoc	= new LocalGen();
		rs			= xLoc.validarNIF($("#idrfc").val() );
	}
	return (evalFiscal == true) ? rs : true;
}
/* ------------------------------- Domicilio -------------------------------- */

var mEdoAc	= <?php echo $xLoc->DomicilioEstadoClaveNum(); ?>;

//========================== END-ELIMI
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
			if(PERSONAS_VIVIENDA_MANUAL == false){
				setTimeout("jsaGetMunicipios()", 1000);
				setTimeout("jsaGetLocalidades()", 1000);
			}
		}
	}
}
//========================== END-ELIMI
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
function jsGetListaDePuntosDeAtencion(){
	var idregion	= $("#idregionpersona").val();
	var xUrl	= "../svc/centros-de-atencion.svc.php?region=" + idregion;
	$("#idcajalocal").empty();
	xGen.DataList({
				url : xUrl,
				id : "idcajalocal",
				key : "idsocios_cajalocal",
				label : "descripcion_cajalocal"
				});	
}
</script>
<?php
$xHP->fin();
?>
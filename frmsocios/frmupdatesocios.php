<?php
/**
 * Edicion de socios
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1.1
 * @package common
 * @subpackage forms
 * Actualizacion de estatus
 * 2015-06-19.- Nuevo esquema
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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xRuls		= new cReglaDeNegocio();
$xSel		= new cHSelect();


$SinDatosFiscales	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATOS_FISCALES);		//regla de negocio
$SinDatoPoblacional = $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATO_POBLACIONAL);		//regla de negocio
$SinRegimenMat 		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_REG_MATRIMONIAL);		//regla de negocio
$SinDatosDocto 		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATOS_DOCTOS);		//regla de negocio

$DomicilioSimple	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_RELS_DOM_SIMPLE);		//regla de negocio
$SinDetalleAcceso 	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DETALLE_ACCESO);		//regla de negocio
$EsSimple			= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_EC_SIMPLE);		//regla de negocio
$TratarComoSalarios	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_EC_ASALARIADO);		//regla de negocio



//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT); $persona = parametro("codigo", $persona, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM				= new cHForm("frm", "./");
$tipo_de_persona	= false;


/* ===========		FORMULARIO		============*/
$clave				= $persona;
$xTabla				= new cSocios_general();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);

if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->codigo($clave);
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmsocios_general", "frmupdatesocios.php?action=$step");


$clave 		= parametro($xTabla->getKey(), null, MQL_INT);

if( ($action == MQL_ADD OR $action == MQL_MOD) AND ($clave != null) ){
	$xTabla->setData( $xTabla->query()->initByID($clave));
	$DOriginal	= $xTabla->query()->getCampos(true);
	$xTabla->setData($_REQUEST);
	$DCambios	= $xTabla->query()->getCampos(true);
	
	$DDif		= array_diff($DOriginal, $DCambios);
	$str		= json_encode($DDif);
	if($action == MQL_ADD){
		$xTabla->query()->insert()->save();
	} else {
		$xTabla->query()->update()->save($clave);
	}
	$xSoc		= new cSocio($xTabla->codigo()->v());
	//if($xSoc->init() == true){
	//	$xSoc->setCuandoSeActualiza();
	//}
	$xSoc->setCuandoSeActualiza();
	
	$xLog		= new cCoreLog();
	$xLog->add("Cambios a la Persona $clave : $str\r\n");
	$xLog->add($xSoc->getMessages());
	$xLog->guardar($xLog->OCat()->PERSONA_MODIFICADA, $clave);
	$xFRM->addAvisoRegistroOK();
	$persona	= 0;
	$clave		= 0;
	$xFRM->addCerrar("", 3);
}

$xSoc				= new cSocio($persona);
if($xSoc->init() == true){
	$tipo_de_persona	= ($xSoc->getEsPersonaFisica() == true) ? PERSONAS_ES_FISICA : PERSONAS_ES_MORAL;
	$xFRM->addGuardar();
	$xFRM->OHidden("codigo", $xTabla->codigo()->v(), "TR.CODIGO");
	$xFRM->OHidden("eacp", $xTabla->eacp()->v(), "TR.EACP");
	$xFRM->OHidden("idusuario", $xTabla->idusuario()->v());
	$xFRM->OHidden("sucursal", $xTabla->sucursal()->v());
	$xFRM->OHidden("fecha_de_revision", $xTabla->fecha_de_revision()->v());
	
	$xFRM->OText_13("idinterna", $xTabla->idinterna()->v(), "TR.IDINTERNO", true, "", " green");
	
	$xFRM->OHidden("fechaentrevista", $xTabla->fechaentrevista()->v(), "TR.FECHA_DE_CAPTURA");
	$xFRM->ODate("fechaalta", $xTabla->fechaalta()->v(), "TR.FECHA_DE_ACEPTACION");
	//$xFRM->ODate("fecha_de_revision", $xTabla->fecha_de_revision()->v(), "TR.FECHA_DE_REVISION");
	
	
	$xFRM->OText("nombrecompleto", $xTabla->nombrecompleto()->v(), "TR.NOMBRE_COMPLETO");
	$xFRM->OText("apellidopaterno", $xTabla->apellidopaterno()->v(), "TR.APELLIDO_PATERNO");
	$xFRM->OText("apellidomaterno", $xTabla->apellidomaterno()->v(), "TR.APELLIDO_MATERNO");
	
	$xFRM->OText_13("rfc", $xTabla->rfc()->v(), "TR.IDENTIFICACION_FISCAL");
	$xFRM->OText_13("curp", $xTabla->curp()->v(), "TR.IDENTIFICACION_POBLACIONAL");
	if(EACP_CLAVE_DE_PAIS != "MX"){
		$xFRM->OText("clave_de_firma_electronica", $xTabla->clave_de_firma_electronica()->v(), "TR.CLAVE DE FIRMA ELECTRONICA");
	} else {
		$xFRM->OHidden("clave_de_firma_electronica", $xTabla->clave_de_firma_electronica()->v(), "TR.CLAVE DE FIRMA ELECTRONICA");
	}
	
	$xFRM->addHElem( $xSel->getListaDeRegimenesFiscales("regimen_fiscal", SYS_TODAS, $xTabla->regimen_fiscal()->v())->get(true) );
	//$xFRM->OMoneda("regimen_fiscal", $xTabla->regimen_fiscal()->v(), "TR.REGIMEN FISCAL");
	$xFRM->addHElem( $xSel->getListaDeEstadoDePersonas("estatusactual", $xTabla->estatusactual()->v())->get(true) );
	
	if(PERSONAS_CONTROLAR_POR_GRUPO == true){
		$xFRM->addHElem( $xSel->getListaDeGruposSolidarios("grupo_solidario", $xTabla->grupo_solidario()->v())->get(true) );
	} else {
		$xFRM->OHidden("grupo_solidario", $xTabla->grupo_solidario()->v(), "TR.GRUPO SOLIDARIO");
	}
	
	if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
		$xFRM->addHElem( $xSel->getListaDeEmpresas("dependencia", false,$xTabla->dependencia()->v())->get(true) );	
	} else {
		$xFRM->OMoneda("dependencia", $xTabla->dependencia()->v(), "TR.DEPENDENCIA");
	}
	
	if(MODULO_CAPTACION_ACTIVADO == true){
		$xFRM->OMoneda("descuento_preferente", $xTabla->descuento_preferente()->v(), "TR.DESCUENTO DESEADO");
	} else {
		$xFRM->OHidden("descuento_preferente", $xTabla->descuento_preferente()->v());
	}
	
	if(SISTEMA_CAJASLOCALES_ACTIVA == true){
		$xFRM->addHElem( $xSel->getListaDeRegionDePersonas("region", $xTabla->region()->v())->get(true) );
		$xFRM->addHElem( $xSel->getListaDeCajasLocales("cajalocal", false, $xTabla->cajalocal()->v())->get(true) ) ;
	} else {
		$xFRM->OHidden("region", $xTabla->region()->v(), "TR.REGION");
		$xFRM->OHidden("cajalocal", $xTabla->cajalocal()->v(), "TR.CAJA_LOCAL");	
	}
	
	$xFRM->ODate("fechanacimiento", $xTabla->fechanacimiento()->v(), "TR.FECHA DE NACIMIENTO");
	$xFRM->OText("lugarnacimiento", $xTabla->lugarnacimiento()->v(), "TR.LUGAR DE NACIMIENTO");
	
	$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonas("tipoingreso", SYS_TODAS, $xTabla->tipoingreso()->v())->get(true) );
	$xFRM->addHElem( $xSel->getListaDeEstadoCivil("estadocivil", $xTabla->estadocivil()->v())->get(true) );
	$xFRM->addHElem( $xSel->getListaDeGeneros("genero", $xTabla->genero()->v())->get(true));
	$xFRM->addHElem( $xSel->getListaDeFigurasJuridicas("personalidad_juridica", SYS_TODAS, $xTabla->personalidad_juridica()->v())->get(true));
	$xFRM->addHElem( $xSel->getListaDeRegimenMatrimonio("regimen_conyugal", $xTabla->regimen_conyugal()->v())->get(true) );
	$xFRM->addHElem( $xSel->getListaDeTipoDeIdentificacion("tipo_de_identificacion", $tipo_de_persona, $xTabla->tipo_de_identificacion()->v())->get(true) );
	$xFRM->OText("documento_de_identificacion", $xTabla->documento_de_identificacion()->v(), "TR.DOCUMENTO DE IDENTIFICACION");
	
	$xFRM->OText("titulo_personal", $xTabla->titulo_personal()->v(), "TR.TITULO_PERSONAL");
	$xFRM->OMail("correo_electronico", $xTabla->correo_electronico()->v(), "TR.CORREO_ELECTRONICO");
	$xFRM->OText("telefono_principal", $xTabla->telefono_principal()->v(), "TR.TELEFONO_PRINCIPAL");
	$xFRM->OMoneda("dependientes_economicos", $xTabla->dependientes_economicos()->v(), "TR.DEPENDIENTES_ECONOMICOS");
	
	if(MODULO_AML_ACTIVADO == true){
		$xFRM->OSiNo("TR.PREGUNTA_AML_PERSONA_2","nacionalidad_extranjera", $xTabla->nacionalidad_extranjera()->v());
		$xFRM->addHElem( $xSel->getListaDePaises("pais_de_origen", $xTabla->pais_de_origen()->v())->get(true) );
	} else {
		$xFRM->OHidden("nacionalidad_extranjera", $xTabla->nacionalidad_extranjera()->v(), "TR.NACIONALIDAD EXTRANJERA");
		$xFRM->OHidden("pais_de_origen", $xTabla->pais_de_origen()->v(), "TR.PAIS DE ORIGEN");	
	}
	
	
	$xFRM->OHidden("nivel_de_riesgo_aml", $xTabla->nivel_de_riesgo_aml()->v(), "TR.NIVEL DE RIESGO AML");
	$xFRM->OText("observaciones", $xTabla->observaciones()->v(), "TR.OBSERVACIONES");
}
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
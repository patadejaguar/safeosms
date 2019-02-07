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
$xHP		= new cHPage("TR.EDITAR ACTIVIDAD_ECONOMICA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xTxt6		= new cHText();
$xTxtSCIAN		= new cHText();

/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cSocios_aeconomica();
$xTabla->setData( $xTabla->query()->initByID($clave));
$persona		= $xTabla->socio_aeconomica()->v();

$xFRM    = new cHForm("frmaepers", "personas-ae.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();
$xSoc			= new cSocio($persona); 

$xSoc->init();


$xFRM->OHidden("idsocios_aeconomica", $xTabla->idsocios_aeconomica()->v());
$xFRM->OHidden("socio_aeconomica", $xTabla->socio_aeconomica()->v());

$xFRM->addHElem( $xTxt6->getDeActividadEconomica("tipo_aeconomica", $xTabla->tipo_aeconomica()->v(), "TR.Clave UIF") );
$xFRM->setValidacion("tipo_aeconomica", "validacion.actividadeconomica", "TR.ACTIVIDAD_ECONOMICA invalido");
//$xFRM->OEntero("tipo_aeconomica", $xTabla->tipo_aeconomica()->v(), "TR.TIPO AECONOMICA");
//$xFRM->OEntero("clave_scian", $xTabla->clave_scian()->v(), "TR.CLAVE SCIAN");
$xFRM->addHElem( $xTxtSCIAN->getDeActividadEconomicaSCIAN("clave_scian", $xTabla->clave_scian()->v()) );
$xFRM->setValidacion("clave_scian", "validacion.actividadeconomica", "TR.ACTIVIDAD_ECONOMICA invalido");

//$xFRM->OEntero("sector_economico", $xTabla->sector_economico()->v(), "TR.SECTOR ECONOMICO");

//$xFRM->OHidden("dependencia_ae", $xTabla->dependencia_ae()->v());
$cDE 			= $xSel->getListaDeEmpresas("dependencia_ae", false, $xTabla->dependencia_ae()->v());
//$cDE->addEvent("onblur", "jsGetDatosEmpresa");
if(PERSONAS_CONTROLAR_POR_EMPRESA == true AND $xSoc->getEsPersonaFisica() == true){
	$xFRM->addHElem($cDE->get("TR.Empresa Relacionada", true) );
} else {
	$xFRM->OHidden("dependencia_ae", DEFAULT_EMPRESA);
}


//$xFRM->OEntero("antiguedad_ae", $xTabla->antiguedad_ae()->v(), "TR.ANTIGUEDAD AE");


$xFRM->ODate("fecha_de_ingreso", $xTabla->fecha_de_ingreso()->v(), "TR.FECHA DE INGRESO");
$xFRM->OText_13("nombre_ae", $xTabla->nombre_ae()->v(), "TR.NOMBRE");
$xFRM->OText_13("puesto", $xTabla->puesto()->v(), "TR.PUESTO");
$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.DESCRIPCION");

$xFRM->addHElem($xSel->getListaDeTipoDeDispersion("empleado_tipo_de_dispersion", $xTabla->empleado_tipo_de_dispersion()->v())->get(true));
//$xFRM->OEntero("empleado_tipo_de_dispersion", $xTabla->empleado_tipo_de_dispersion()->v(), "TR.EMPLEADO TIPO DE DISPERSION");
$xFRM->OText_13("departamento_ae", $xTabla->departamento_ae()->v(), "TR.DEPARTAMENTO");
$xFRM->OTelefono("telefono_ae", $xTabla->telefono_ae()->v());
$xFRM->OText_13("extension_ae", $xTabla->extension_ae()->v(), "TR.EXTENSION");

$xFRM->OText_13("numero_empleado", $xTabla->numero_empleado()->v(), "TR.NUMERO EMPLEADO");
$xFRM->OText_13("numero_de_seguridad_social", $xTabla->numero_de_seguridad_social()->v(), "TR.NSS");


$xFRM->OMoneda2("monto_percibido_ae", $xTabla->monto_percibido_ae()->v(), "TR.INGRESOS");


//$xFRM->OText_13("fecha_alta", $xTabla->fecha_alta()->v(), "TR.FECHA ALTA");


//$xFRM->OHidden("sucursal", $xTabla->sucursal()->v(), "TR.SUCURSAL");
//$xFRM->OEntero("estado_actual", $xTabla->estado_actual()->v(), "TR.ESTADO ACTUAL");
/*
$xFRM->OText_13("fecha_de_verificacion", $xTabla->fecha_de_verificacion()->v(), "TR.FECHA DE VERIFICACION");
$xFRM->OEntero("oficial_de_verificacion", $xTabla->oficial_de_verificacion()->v(), "TR.OFICIAL DE VERIFICACION");
$xFRM->OText("notas_de_verificacion", $xTabla->notas_de_verificacion()->v(), "TR.NOTAS DE VERIFICACION");
*/
/*
$xFRM->OEntero("ae_clave_de_localidad", $xTabla->ae_clave_de_localidad()->v(), "TR.AE CLAVE DE LOCALIDAD");
$xFRM->OEntero("ae_codigo_postal", $xTabla->ae_codigo_postal()->v(), "TR.AE CODIGO POSTAL");

$xFRM->OText_13("domicilio_ae", $xTabla->domicilio_ae()->v(), "TR.DOMICILIO AE");
$xFRM->OText_13("localidad_ae", $xTabla->localidad_ae()->v(), "TR.LOCALIDAD AE");
$xFRM->OText_13("municipio_ae", $xTabla->municipio_ae()->v(), "TR.MUNICIPIO AE");
$xFRM->OText_13("estado_ae", $xTabla->estado_ae()->v(), "TR.ESTADO AE");
*/

$xSelDExt	= $xSel->getListaDeDomicilioPorPers($persona, "domicilio_vinculado", $xTabla->domicilio_vinculado()->v());
//$xSelDExt->addEspOption(SYS_NINGUNO, $xFRM->getT("TR.AGREGAR NUEVO"));
//$xSelDExt->setOptionSelect(SYS_NINGUNO);

$xSelDExt->setLabel("TR.UBICACION");
//$xSelDExt->addEvent("onchange", "jsSetDomicilioPrevio()");
$xFRM->addHElem( $xSelDExt->get(true ) );
//$xFRM->OEntero("domicilio_vinculado", $xTabla->domicilio_vinculado()->v(), "TR.DOMICILIO VINCULADO");
$xFRM->OSiNo("TR.PRINCIPAL", "principal", $xTabla->principal()->v());
//$xFRM->OEntero("principal", $xTabla->principal()->v(), "TR.PRINCIPAL");



//$xFRM->OHidden("idusuario", $xTabla->idusuario()->v(), "TR.IDUSUARIO");


//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
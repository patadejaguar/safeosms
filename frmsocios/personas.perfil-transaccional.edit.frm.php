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
$xHP		= new cHPage("TR.EDITAR PERFIL_TRANSACCIONAL", HP_FORM);
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
$tipo			= parametro("tipo", 0, MQL_INT);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cPersonas_perfil_transaccional();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmpf", "personas.perfil-transaccional.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

if($clave<=0){
	$xTabla->idpersonas_perfil_transaccional("NULL");
	$xTabla->idusuario(getUsuarioActual());
	$xTabla->clave_de_persona($persona);
	$xTabla->operaciones_calculadas(SYS_CERO);
	$xTabla->fecha_de_registro( time() );
	$xTabla->fecha_de_calculo( time() );
	$xTabla->pais_de_origen( EACP_CLAVE_DE_PAIS);
	$dias_vence		= setNoMenorQueCero(AML_PERSONA_DIAS_VENCPF);
	$dias_vence		= $dias_vence *  86400;
	$fecha_vence	= $xTabla->fecha_de_registro()->v() + $dias_vence;
	$xTabla->fecha_de_vencimiento($fecha_vence);
}




$xFRM->addSeccion("idtsecc0", "TR.TIPO");


//$xFRM->OEntero("clave_de_tipo_de_perfil", $xTabla->clave_de_tipo_de_perfil()->v(), "TR.CLAVE DE TIPO DE PERFIL");

$xFRM->addHElem( $xSel->getListaDePerfilTransaccional("clave_de_tipo_de_perfil", $xTabla->clave_de_tipo_de_perfil()->v())->get("TR.tipo de perfil", true  ) );


$xFRM->endSeccion();

//$xFRM->OText_13("recurso_origen", $xTabla->recurso_origen()->v(), "TR.RECURSO ORIGEN");
//$xFRM->OText_13("recurso_aplicacion", $xTabla->recurso_aplicacion()->v(), "TR.RECURSO APLICACION");

$xFRM->addSeccion("idtsecc1", "TR.CLASIFICACION");

$xFRM->addHElem( $xSel->getListaDePaises("pais_de_origen", $xTabla->pais_de_origen()->v() )->get("TR.pais de origen", true  ) );
//$xFRM->OText_13("pais_de_origen", $xTabla->pais_de_origen()->v(), "TR.PAIS DE ORIGEN");

//$xFRM->OEntero("res_origen_id", $xTabla->res_origen_id()->v(), "TR.RES ORIGEN ID");
$xSel1	= $xSel->getListadoGenerico("personas_pt_origen","res_origen_id",$xTabla->res_origen_id()->v() );
$xFRM->addHElem( $xSel1->get("TR.ORIGEN", true) );

//$xFRM->OEntero("res_aplicacion_id", $xTabla->res_aplicacion_id()->v(), "TR.RES APLICACION ID");
$xSel2	= $xSel->getListadoGenerico("personas_pt_destino","res_aplicacion_id",$xTabla->res_aplicacion_id()->v() );
$xFRM->addHElem( $xSel2->get("TR.DESTINO", true) );

$xFRM->endSeccion();

$xFRM->addSeccion("idtsecc2", "TR.OPERACION");

$xFRM->OEntero("maximo_de_operaciones", $xTabla->maximo_de_operaciones()->v(), "TR.MAXNUMERO DE OPERACIONES");
$xFRM->OMoneda("cantidad_maxima", $xTabla->cantidad_maxima()->v(), "TR.MAXVALOR DE OPERACIONES");


$xFRM->endSeccion();

$xFRM->addSeccion("idtsecc3", "TR.OTROS");

$xFRM->OText("observaciones", $xTabla->observaciones()->v(), "TR.OBSERVACIONES");

$xFRM->endSeccion();

$xFRM->setValidacion("maximo_de_operaciones", "validacion.nozero");
$xFRM->setValidacion("cantidad_maxima", "validacion.nozero");

$xFRM->OHidden("idpersonas_perfil_transaccional", $xTabla->idpersonas_perfil_transaccional()->v());
$xFRM->OHidden("clave_de_persona", $xTabla->clave_de_persona()->v());
$xFRM->OHidden("idusuario", $xTabla->idusuario()->v());
$xFRM->OHidden("fecha_de_registro", $xTabla->fecha_de_registro()->v());
$xFRM->OHidden("operaciones_calculadas", $xTabla->operaciones_calculadas()->v());
$xFRM->OHidden("cantidad_calculada", $xTabla->cantidad_calculada()->v());
$xFRM->OHidden("fecha_de_calculo", $xTabla->fecha_de_calculo()->v());
$xFRM->OHidden("fecha_de_vencimiento", $xTabla->fecha_de_vencimiento()->v());
$xFRM->OHidden("afectacion", $xTabla->afectacion()->v(), "TR.AFECTACION");
//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
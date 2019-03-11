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
$xHP		= new cHPage("TR.DATOS DE ORIGINACION", HP_FORM);
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
$observaciones	= parametro("idobservaciones");
$tipo			= parametro("tipo",0, MQL_INT);


$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCreditos_datos_originacion();
$xTabla->setData( $xTabla->query()->initByID($clave));
if($clave <= 0){
	$xTabla->idcreditos_datos_originacion("NULL");
	$xTabla->credito($credito);
	$xTabla->tipo_originacion($tipo);
	$xTabla->tiempo(time());
	$xTabla->idusuario(getUsuarioActual());
}

$xCache		= new cCache();
$xCache->clean("creditos_datos_originacion-credito-$credito");

$xFRM		= new cHForm("frmdatosoriginacion", "creditos.datos-origen.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

$xDOrg		= new cCreditosDatosDeOrigen();
//$xSel->addOptions($aOptions)
$arr = array(
		$xDOrg->ORIGEN_ARRENDAMIENTO => $xFRM->getT("TR.ARRENDAMIENTO"),
		$xDOrg->ORIGEN_LINEA => $xFRM->getT("TR.CREDITOS_LINEAS"),
		$xDOrg->ORIGEN_NOMINA => $xFRM->getT("TR.EMPLEADOR"),
		$xDOrg->ORIGEN_PRECLIENTE => $xFRM->getT("TR.PRECLIENTE"),
		$xDOrg->ORIGEN_PRESUPUESTO => $xFRM->getT("TR.PRESUPUESTO"),
		$xDOrg->ORIGEN_REESTRUCTURA	=> $xFRM->getT("TR.REESTRUCTURAR"),
		$xDOrg->ORIGEN_RENOVACION => $xFRM->getT("TR.RENOVAR"),
		"1" => $xFRM->getT("TR.NINGUNO")
);

$xSel->addOptions($arr);

$xFRM->OHidden("idcreditos_datos_originacion", $xTabla->idcreditos_datos_originacion()->v());
$xFRM->ODisabled_13("idtmp1", $xF->getFechaByInt($xTabla->tiempo()->v()), "TR.FECHA" );


$xFRM->OHidden("credito", $xTabla->credito()->v());

if($tipo > 0 AND $clave<=0){
	$xFRM->OHidden("tipo_originacion", $xTabla->tipo_originacion()->v());
} else {
	$xFRM->addHElem($xSel->get("tipo_originacion", "TR.TIPO DE ORIGINACION", $xTabla->tipo_originacion()->v()));
}
if($tipo == $xDOrg->ORIGEN_REESTRUCTURA OR $tipo == $xDOrg->ORIGEN_RENOVACION){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$persona	= $xCred->getClaveDePersona();
		
		$xSel2 = $xSel->getListaDeCreditosPorPersona($persona, "clave_vinculada", $xTabla->clave_vinculada()->v(), $credito);
		$xFRM->addHElem($xSel2->get("TR.CREDITO ORIGEN", true));
	}
} else {
	$xFRM->OMoneda("clave_vinculada", $xTabla->clave_vinculada()->v(), "TR.CLAVE VINCULADA");
}


$xFRM->OMoneda("monto_vinculado", $xTabla->monto_vinculado()->v(), "TR.MONTO VINCULADO");

$xFRM->OHidden("tiempo", $xTabla->tiempo()->v());
$xFRM->OHidden("idusuario", $xTabla->idusuario()->v());



//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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
$xHP		= new cHPage("TR.EDITAR MEMBRESIA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();



$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 


$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cEntidad_pagos_perfil();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmpagosperfillista", "lista_pagos_por_membresia.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

$xFRM->OHidden("identidad_pagos_perfil", $xTabla->identidad_pagos_perfil()->v());


$xFRM->ODate("fecha_de_aplicacion", $xTabla->fecha_de_aplicacion()->v(), "TR.FECHA DE APLICACION");

$xFRM->addHElem($xSel->getListaDePersonasMembresia("tipo_de_membresia", $xTabla->tipo_de_membresia()->v())->get(true));
$xFRM->addHElem($xSel->getListaDeTiposDeOperacion("tipo_de_operacion", $xTabla->tipo_de_operacion()->v())->get(true));
$xFRM->addHElem($xSel->getListaDePeriocidadDePago("periocidad", $xTabla->periocidad()->v())->get(true));

$xFRM->OMoneda2("monto", $xTabla->monto()->v(), "TR.MONTO");
$xFRM->ONumero("prioridad", $xTabla->prioridad()->v(), "TR.PRIORIDAD");
$xFRM->OText_13("rotacion", $xTabla->rotacion()->v(), "TR.ROTACION");


if($clave <= 0){
	$xFRM->addCRUD($xTabla->get(), true);
} else {
	$xFRM->addCRUDSave($xTabla->get(), $clave, true);
}


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
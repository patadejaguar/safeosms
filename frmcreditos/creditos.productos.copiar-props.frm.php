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
$xHP		= new cHPage("TR.COPIAR ELEMENTOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();


//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();

function jsaCopiarPropiedades($idproducto, $idproductocopiado, $quecopiar){
	$nuevoid	= $idproducto;
	$xQL		= new MQL();
	$idclonado	= $idproductocopiado;
	
	
	switch ($quecopiar){
		case "formatos":
			$xQL->setRawQuery("INSERT INTO `creditos_prods_formatos` SELECT NULL, $nuevoid,`formato_id`,`estatus`,`etapa_id`,`opcional` FROM `creditos_prods_formatos` WHERE `estatus`=1 AND `producto_credito_id`=$idclonado");
			break;
		case "otroscargos":
			$xQL->setRawQuery("INSERT INTO `creditos_productos_costos` SELECT NULL, $nuevoid, `clave_de_operacion`,`unidades`,`unidad_de_medida`,`editable`,`en_plan`,`exigencia`,`estatus`,`aplicar_desde`,`aplicar_hasta` FROM `creditos_productos_costos` WHERE `clave_de_producto`=$idclonado AND `estatus`=1");
			break;
	}
}


$jxc ->exportFunction('jsaCopiarPropiedades', array('idproducto', 'idproductocopiado', 'quecopiar'), "#iddatos_pago");
$jxc ->process();

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


$producto	= parametro("producto",0, MQL_INT);
$quecopiar	= parametro("quecopiar","", MQL_RAW);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->OButton("TR.COPIAR", "jsaCopiarPropiedades()", $xFRM->ic()->CONTROL);


$xFRM->addTag("Copiar : $quecopiar", "warning");

$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("idproductocopiado")->get(true) );

$xFRM->OHidden("idproducto", $producto);
$xFRM->OHidden("quecopiar", $quecopiar);

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
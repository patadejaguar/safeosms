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
$xHP		= new cHPage("TR.EDITAR MENU", HP_FORM);
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

/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cGeneral_menu();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmmenu", "menu.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

$xFRM->OHidden("idgeneral_menu", $xTabla->idgeneral_menu()->v());

$sqlMost 	= "SELECT idgeneral_menu, CONCAT(menu_title, ' ', idgeneral_menu) AS 'menu' FROM general_menu WHERE menu_type='parent' ORDER BY menu_title";
$xSelM		= $xSel->getListadoGenerico($sqlMost, "menu_parent");
$xSelM->setEsSql();$xSelM->addEspOption("0", "Raiz");
$xSelM->setOptionSelect($xTabla->menu_parent()->v());
$xFRM->addHElem( $xSelM->get("TR.SUPERIOR", true) );
//$xFRM->OMoneda("menu_parent", $xTabla->menu_parent()->v(), "TR.MENU PARENT");

$xFRM->OText_13("menu_title", $xTabla->menu_title()->v(), "TR.TITULO");
$xFRM->OText("menu_file", $xTabla->menu_file()->v(), "TR.ARCHIVO");

//$xFRM->OText_13("menu_destination", $xTabla->menu_destination()->v(), "TR.MENU DESTINATION");

$xFRM->OSelect("menu_destination", $xTabla->menu_destination()->v() , "TR.DESTINO", array("principal"=>"Menu principal", "_blank"=>"Nueva Ventana", "tiny"=>"Dialogo", "" => "Ninguno"));

$xFRM->OText("menu_description", $xTabla->menu_description()->v(), "TR.DESCRIPCION");
$xFRM->OText_13("menu_image", $xTabla->menu_image()->v(), "TR.ICONO");

$xFRM->OSelect("menu_type", $xTabla->menu_type()->v() , "TR.TIPO", array("general"=>"General", "command"=>"Comando", "parent"=>"Agrupador"));
$xFRM->OMoneda("menu_order", $xTabla->menu_order()->v(), "TR.ORDEN");
$xFRM->OMoneda("menu_help_id", $xTabla->menu_help_id()->v(), "TR.AYUDA");


$xFRM->OSelect("menu_showin_toolbar", $xTabla->menu_showin_toolbar()->v() , "TR.MOSTRAR", array("false"=>"No", "true"=>"Si"));

//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
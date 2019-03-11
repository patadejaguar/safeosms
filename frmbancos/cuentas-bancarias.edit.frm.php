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
$xHP		= new cHPage("TR.EDITAR CUENTA_BANCARIA", HP_FORM);
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
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cBancos_cuentas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmbancos", "lista-de-cuentas-bancarias.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

$xFRM->OHidden("idbancos_cuentas", $xTabla->idbancos_cuentas()->v());
$xFRM->OHidden("fecha_de_apertura", $xTabla->fecha_de_apertura()->v());
//$xFRM->addHElem( $xSel->getListaDeSucursales("sucursal", $xTabla->sucursal()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeBancos("entidad_bancaria", $xTabla->entidad_bancaria()->v())->get(true) );

$xFRM->OText("descripcion_cuenta", $xTabla->descripcion_cuenta()->v(), "TR.descripcion");


$xFRM->OSelect("tipo_de_cuenta", $xTabla->tipo_de_cuenta()->v() , "TR.tipo de cuenta", array("cheques"=>"CHEQUES", "inversion"=>"INVERSION"));
//$xFRM->OHidden("sucursal", $xTabla->sucursal()->v(), "TR.sucursal");

$xFRM->addHElem( $xSel->getListaDeSucursales("sucursal", $xTabla->sucursal()->v())->get(true) );

$xFRM->OSelect("estatus_actual", $xTabla->estatus_actual()->v() , "TR.estatus actual", array("activo"=>"ACTIVO", "baja"=>"BAJA"));
$xFRM->OText_13("consecutivo_actual", $xTabla->consecutivo_actual()->v(), "TR.consecutivo actual");
$xFRM->OMoneda2("saldo_actual", $xTabla->saldo_actual()->v(), "TR.saldo actual");

$xFRM->OTextContable("codigo_contable", $xTabla->codigo_contable()->v());


//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$msg		= "";

/* -----------------  -----------------------*/
$clave 		= parametro("idbancos_cuentas", null, MQL_INT);
$xTabla		= new cBancos_cuentas();
$xSel		= new cHSelect();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave = parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}

$xFRM		= new cHForm("frmbancos_cuentas", "bancos_alta_a_cuentas.frm.php?action=$step");

if($action == MQL_ADD){		//Agregar
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){ 
		$xTabla->setData( $xTabla->query()->initByID($clave)); 
		$xTabla->setData($_REQUEST);
		$xTabla->query()->insert()->save();
		$xFRM->addAvisoRegistroOK();
	}	
} else if($action == MQL_MOD){		//Modificar
	//iniciar
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){ 
		$xTabla->setData( $xTabla->query()->initByID($clave)); 
		$xTabla->setData($_REQUEST);
		$xTabla->query()->update()->save($clave);
		$xFRM->addAvisoRegistroOK();
	}
}

$xFRM->addSubmit();
$xFRM->OMoneda("idbancos_cuentas", $xTabla->idbancos_cuentas()->v(), "TR.clave_de_cuenta");
$xFRM->OText("descripcion_cuenta", $xTabla->descripcion_cuenta()->v(), "TR.descripcion cuenta");
$xFRM->ODate("fecha_de_apertura", $xTabla->fecha_de_apertura()->v(), "TR.fecha de registro");

//$xFRM->OHidden("sucursal", $xTabla->sucursal()->v(), "TR.sucursal");
$xFRM->addHElem( $xSel->getListaDeSucursales("sucursal", $xTabla->sucursal()->v())->get(true) );

$xFRM->OSelect("estatus_actual", $xTabla->estatus_actual()->v() , "TR.estatus actual", array("activo"=>"ACTIVO", "baja"=>"BAJA"));
$xFRM->OText("consecutivo_actual", $xTabla->consecutivo_actual()->v(), "TR.consecutivo actual");
$xFRM->OMoneda("saldo_actual", $xTabla->saldo_actual()->v(), "TR.saldo actual");

//$xTabla->entidad_bancaria()->v()

$xFRM->OText("codigo_contable", $xTabla->codigo_contable()->v(), "TR.codigo contable");
//$xFRM->OMoneda("entidad_bancaria", $xTabla->entidad_bancaria()->v(), "TR.entidad bancaria");
$xFRM->addHElem( $xSel->getListadoDeBancos("entidad_bancaria", $xTabla->entidad_bancaria()->v())->get(true) );
$xFRM->OSelect("tipo_de_cuenta", $xTabla->tipo_de_cuenta()->v() , "TR.tipo de cuenta", array("cheques"=>"CHEQUES", "inversion"=>"INVERSION"));

$xFRM->OHidden("eacp", EACP_CLAVE, "TR.eacp");



echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
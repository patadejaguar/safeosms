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
$xHP		= new cHPage("TR.AGREGAR ORIGINADORES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
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

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xTabla		= new cLeasing_originadores();


$xFRM->OHidden("idleasing_originadores", "NULL");
$xFRM->addHElem($xSel->getListaDeOriginadoresTipos("tipo_de_originador",$xTabla->tipo_de_originador()->v())->get(true));
//$xFRM->OMoneda("tipo_de_originador", $xTabla->tipo_de_originador()->v(), "TR.TIPO DE ORIGINADOR");

$xFRM->OText("nombre_originador", $xTabla->nombre_originador()->v(), "TR.NOMBRE ORIGINADOR");
$xFRM->OText_13("rfc_originador", $xTabla->rfc_originador()->v(), "TR.RFC ORIGINADOR");
$xFRM->OMoneda("clave_de_persona", $xTabla->clave_de_persona()->v(), "TR.CLAVE DE PERSONA");
$xFRM->addHElem($xSel->getListaDeBancos("clave_banco", $xTabla->clave_banco()->v())->get(true));
//$xFRM->OMoneda("clave_banco", $xTabla->clave_banco()->v(), "TR.CLAVE BANCO");
$xFRM->OText_13("cuenta_clabe", $xTabla->cuenta_clabe()->v(), "TR.CUENTA CLABE");
$xFRM->OText_13("cuenta_bancaria", $xTabla->cuenta_bancaria()->v(), "TR.CUENTA BANCARIA");
$xFRM->addHElem( $xSel->getListaDePeriocidadDePago("frecuencia_de_pago", $xTabla->frecuencia_de_pago()->v())->get(true) );
//$xFRM->OMoneda("frecuencia_de_pago", $xTabla->frecuencia_de_pago()->v(), "TR.FRECUENCIA DE PAGO");
$xFRM->OMail("email_de_contacto", $xTabla->email_de_contacto()->v(), "TR.EMAIL DE CONTACTO");

$xFRM->addHElem( $xSel->getListaDeLeasingTipoCom("tipo_de_comision", $xTabla->tipo_de_comision()->v())->get(true) );
$xFRM->OMoneda("comision", $xTabla->comision()->v(), "TR.COMISION");
$xFRM->OMoneda("meta", $xTabla->meta()->v(), "TR.META");

$xFRM->addHElem($xSel->getListaDePeriocidadDePago("frecuencia_meta",$xTabla->frecuencia_meta()->v())->get("TR.FRECUENCIA DE META", true));

$xFRM->OText("direccion", $xTabla->direccion()->v(), "TR.DOMICILIO");
$xFRM->OText_13("telefono", $xTabla->telefono()->v(), "TR.TELEFONO");



$xFRM->addCRUD($xTabla->get(), true);
echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
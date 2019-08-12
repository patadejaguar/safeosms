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

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "./");

$msg		= "";

$xCta		= new cCuentaContableEsquema ("102010101");


$xFRM->addAviso($xCta->CUENTA_FORMATEADA);
$xFRM->addAviso($xCta->CUENTA);
$xFRM->addAviso($xCta->NIVEL_ACTUAL);
$xFRM->addAviso($xCta->CUENTARAW);

$xFRM->addAviso($xCta->SUPERIORES[1]);
$xFRM->addAviso($xCta->SUPERIORES[2]);
$xFRM->addAviso($xCta->SUPERIORES[3]);
$xFRM->addAviso($xCta->SUPERIORES[4]);

$xCta		= new cCuentaContableEsquema("1-02-0000000");


$xFRM->addAviso($xCta->CUENTA_FORMATEADA);
$xFRM->addAviso($xCta->CUENTA);
$xFRM->addAviso($xCta->NIVEL_ACTUAL);

$xFRM->addAviso($xCta->CUENTARAW);

$xFRM->addSubmit();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
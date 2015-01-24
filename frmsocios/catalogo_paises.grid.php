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

$xFRM		= new cHForm("frm", "./");

$msg		= "";
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();


/* ===========		GRID JS		============*/

$xHG	= new cHGrid("personas_domicilios_paises");
$sqlpersonas_domicilios_paises	= base64_encode("SELECT * FROM personas_domicilios_paises LIMIT 0,100");

$xHG->setListAction("../svc/datos.svc.php?out=jtable&q=$sqlpersonas_domicilios_paises");
$xHG->addKey("clave_de_control", "TR.Clave");
$xHG->addElement("nombre_oficial", "TR.nombre", "10%");
$xHG->addElement("es_paraiso_fiscal", "TR.es paraiso_fiscal", "10%");
$xHG->addElement("es_considerado_riesgo", "TR.Nivel_de_riesgo", "10%");
$xHG->addElement("clave_numerica", "TR.Numero", "10%");
$xHG->addElement("clave_alfanumerica", "TR.clave_alfabetica", "10%");

$xFRM->addHTML( $xHG->getDiv() );


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);

echo $xHG->getJsHeaders();
echo $xHG->getJs(true, true);
$xHP->fin();
?>
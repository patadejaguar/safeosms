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
$jxc 		= new TinyAjax();

function jsaGuardarDatosBanco($credito, $banco, $cuenta, $clabe){
	$xCred	= new cCredito($credito); $xCred->init();
	$Cat	= $xCred->OCatOtrosDatos();
	$datobanco	= "";
	$xBan		= new cBancos_entidades(); $xBan->setData( $xBan->query()->initByID($banco) );
	$datobanco	= $xBan->idbancos_entidades()->v() . " | " . $xBan->nombre_de_la_entidad()->v();
	$xCred->setOtrosDatos($Cat->DEPOSITO_BANCO, strtoupper($datobanco));
	$xCred->setOtrosDatos($Cat->DEPOSITO_CTA_BANCARIA, $cuenta);
	$xCred->setOtrosDatos($Cat->DEPOSITO_CLABE_BANCARIA, $clabe);
	return $xCred->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaGuardarDatosBanco', array('idcredito', 'idbanco', 'idctabancaria', 'idctaclabe'), "#idmsgs");
$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM		= new cHForm("frmdatosbanco", "./");
$xSel		= new cHSelect();
$xFRM->addHElem( $xSel->getListadoDeBancos("idbanco")->get("TR.Banco de transferencia", true) );
$xFRM->OMoneda("idctabancaria", "", "TR.clave_de_cuenta de transferencia");
$xFRM->OMoneda("idctaclabe", "", "TR.Numero de CLABE");
$xFRM->addGuardar("jsaGuardarDatosBanco()");
$xFRM->OHidden("idcredito", $credito, "");
$xFRM->addAviso("");

$msg		= "";

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
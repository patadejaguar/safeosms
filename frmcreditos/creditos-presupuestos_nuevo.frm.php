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
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$notas		= parametro("idobservaciones");

$xHP->init();
$xFRM		= new cHForm("frmcredspresupuestonuevo", "creditos-presupuestos_nuevo.frm.php?action=" . MQL_ADD);
$msg		= "";

if($action == MQL_ADD){
	$xPre	= new cCreditosPresupuesto(false, $persona);
	$res 	= $xPre->add($fecha,$notas, false, $persona );
	if($res == false){
		$xFRM->addAvisoRegistroError();
	} else {
		$xFRM->addAvisoRegistroOK();
	}
} else {
	$xSoc	= new cSocio($persona);
	$xSoc->init();
	$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
	$xFRM->OHidden("persona", $persona);	
	$xFRM->addFecha();
	$xFRM->addObservaciones();
}

//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();
$xFRM->addGuardar();
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
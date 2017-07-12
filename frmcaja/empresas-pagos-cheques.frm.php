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
function jsaGetDetalleDePagos($persona){
	$xPres	= new cCreditosPresupuesto();
	$xLi	= new cSQLListas();
	$xT		= new cTabla($xLi->getListadoDePresupuestoPorPagar($persona));
	return $xT->Show();
	
}
$jxc ->exportFunction('jsaGetDetalleDePagos', array('idpersona'), "#iddatos_pago");
$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM		= new cHForm("frm", "empresas-pagos.frm.php");

$msg		= "";
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
if($persona > DEFAULT_SOCIO){
	$xFRM->OHidden("idpersona", $persona);
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->addHElem( $xSoc->getFicha(false, "", true, "", true));
	}
	//Agregar Detalle
	
} else {
	$xFRM->addPersonaBasico();
}
$xFRM->addHTML("<div id='iddatos_pago'></diV");
$xFRM->OButton("TR.Cargar Pagos", "jsaGetDetalleDePagos()");
$xFRM->addSubmit();
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
function jsGetDetalleDePago(){
	
}
</script>

<?php
$xHP->fin();
?>
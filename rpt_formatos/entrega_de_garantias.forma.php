<?php
/**
 * Forma de Entrega de garantias
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 * @subpackage formatos
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
$xHP		= new cHPage("REPORTE DE ", HP_REPORT);
	
$xHP->setTitle($xHP->lang("catalogo de", "riesgo") );

$oficial = elusuario($iduser);

/**
 */
$xF				= new cFecha();
$clave 		= (isset($_GET["clave"]) ) ? $_GET["clave"] : SYS_NINGUNO;

echo $xHP->getHeader();

echo $xHP->setBodyinit("initComponents();");

$xFMT		= new cFormato(151);
$xGar		= new cCreditosGarantias($clave);
$xGar->init();
$xFMT->setPersona( $xGar->getClaveDePersona() );
$xFMT->setCredito( $xGar->getClaveDeCredito() );
$xFMT->setGarantiaDeCredito($clave);
$xFMT->setProcesarVars();
echo $xFMT->get();

echo $xHP->setBodyEnd();
?>
<script>
function initComponents(){
	window.print();
}
</script>
<?php
$xHP->end(); 
?>
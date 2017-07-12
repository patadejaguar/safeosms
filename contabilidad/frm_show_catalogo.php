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
$xHP		= new cHPage("TR.BUSCAR EN EL CATALOGO_CONTABLE", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$xHP->init();

$xFRM		= new cHForm("frm", "./");

$msg		= "";
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
///$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
$xFRM->addCuentaContable();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>

</script>
<?php
$xHP->fin();

?>
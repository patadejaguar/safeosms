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
$xHP		= new cHPage("TR.Otros Datos de Productos de Credito", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave 		= parametro("id", null, MQL_INT);

$xHP->init();

$xFRM		= new cHForm("frm", "./");

$msg		= "";
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
$xFRM->addSubmit();

$xP			= new cCreditos_productos_otros_parametros();

$xGrid		= new cHGrid("idotrosdatos", $xHP->getTitle());
$xGrid->addkey($xP->idcreditos_productos_otros_parametros()->get(), "false");
$xGrid->addElement($xP->clave_del_parametro()->get(), "TR.Parametro", "50%");
$xGrid->addElement($xP->valor_del_parametro()->get(), "TR.Valor", "50%");
$where		= base64_encode(" `clave_del_producto`=$clave ");

$xGrid->setListAction("../svc/datos.svc.php?out=jtable&tabla=creditos_productos_otros_parametros&w=$where");
$xFRM->addHTML($xGrid->getDiv());


echo $xFRM->get();

echo $xGrid->getJsHeaders();
?>
<script>
$(document).ready(function () {
	<?php echo $xGrid->getJs(true); ?>
});
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
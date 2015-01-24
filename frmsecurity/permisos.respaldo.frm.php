<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("Respaldo de Permisos", HP_FORM);
$xHP->setIncludes();

$xHP->setArchivo("../frmsecurity/permiso.respaldo.frm.php");

$oficial 			= elusuario($iduser);
$jxc = new TinyAjax();
function jsaRespaldo($anno, $mes, $dia){
	$xSec		= new cSystemPermissions();
	$file		= $xSec->setBackup("$anno-$mes-$dia");
	return "Se hizo el respaldo en ". $file;
}
function jsaRestaurar($anno, $mes, $dia){
	$xSec		= new cSystemPermissions();
	$xSec->setRestore("$anno-$mes-$dia");
	return 		$xSec->getMessages("html");
}
$jxc ->exportFunction('jsaRespaldo', array('idelanno0', 'idelmes0', 'ideldia0'), "#aviso" );
$jxc ->exportFunction('jsaRestaurar', array('idelanno0', 'idelmes0', 'ideldia0'), "#aviso" );
$jxc ->process();

echo $xHP->getHeader();
$jxc ->drawJavaScript(false, true);
//echo $jsb->setIncludeJQuery();
echo $xHP->setBodyinit();
$msg			= "";
$xFrm			= new cHForm("respaldo_de_permisos", "movimientos_bancarios.frm.php");
//id,	label value, size,	class,	options[])
$xF				= new cHDate(0, false, TIPO_FECHA_OPERATIVA);

$xBtnBk		= new cHButton("idResp", "Respaldar Permisos");
$xBtnBk->init();
$xBtnBk->addEvent("jsaRespaldo");

$xBtnRes		= new cHButton("idRest", "Restaurar Permisos");
$xBtnRes->init();
$xBtnRes->addEvent("jsaRestaurar");

//array("onchange=alert('test')")
$xFrm->addHElem( array($xF->get("Fecha de Operacion"), $xBtnBk->get(),  $xBtnRes->get() ) );
$xFrm->addHTML("<div class='aviso' id='aviso'>$msg</div>");
echo $xFrm->get();

//id value class size maxlength arra(varias_opciones)
//nombre = id
echo $xHP->setBodyEnd();

?>

<script  >

</script>
<?php
$xHP->end();
?>
<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.operaciones.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial 	= elusuario($iduser);
$jxc 		= new TinyAjax();

function getReciboDesc($numero){
	if ( isset($numero) AND ( $numero != 0) ){
		$cRec	= new cReciboDeOperacion();
		$cRec->setNumeroDeRecibo($numero, true);
		$d		= $cRec->getDatosInArray();
		$desRec	= $cRec->getDescripcion();
		$tab = new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("txtDescRecibo", $desRec));
		$tab -> add(TabSetValue::getBehavior("idTipoRecibo", $d["tipo_docto"]));

		return $tab -> getString();
	}
}

$jxc ->exportFunction('getReciboDesc', array('idNumeroRecibo'));
$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reimprimir Recibos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>

<body>
<?php
$action = $_GET["a"];
if ( !isset($action) ){
?>
<form name="frmPrintRec" method="POST" action="./reimprimir_recibo.frm.php?a=100">
<fieldset>
	<legend>[ Reimprimir Recibo&nbsp;]</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td width="10%">Numero de Recibo</td>
			<td width="20%">
				<input type='text' name='cNumeroRecibo' value='0' id="idNumeroRecibo" onchange="getReciboDesc()" onblur="getReciboDesc()"  class='mny' size='12' />
				<?php echo CTRL_GORECIBOS; ?></td>
			<td width="10%">Descripcion Corta</td>
			<th width="60%"><input type="text" name="txtDescRecibo" id="txtDescRecibo" disabled size="100" /></th>
		</tr>
		<tr>
			<th colspan="4"><a class="button" onclick="frmSubmit();" >&nbsp;&nbsp;&nbsp;Describir Recibo antes de Imprimir&nbsp;&nbsp;&nbsp;</a></th>
		</tr>
		</tbody>
	</table>
	<input type='hidden' name='cTipoRecibo' value='0' id="idTipoRecibo" />
	<input type='hidden' name='cFormaRecibo' value='' id="idFormaRecibo" />
</fieldset>
</form>
<?php
} else {
	$recibo	= $_POST["cNumeroRecibo"];
	$xRec	= new cReciboDeOperacion(0, true, $recibo);
	$xRec->initRecibo();
	echo $xRec->getFicha(true);
	$uri = $xRec->getURI_Formato();
	echo "<a class=\"button\" onclick=\"ImprimirRecibo();\" >&nbsp;&nbsp;&nbsp;Reimprimir Recibo&nbsp;&nbsp;&nbsp;</a>";
}
?>
</body>
<?php
$jxc ->drawJavaScript(false, true);
$xc = new jsBasicForm("frmPrintRec");
$xc->setNombreCtrlRecibo("cNumeroRecibo");
$xc->show();
?>
<script  >
function ImprimirRecibo(){
	var mURI	= "<?php echo $uri; ?>";
	jsGenericWindow(mURI);
}
</script>
</html>

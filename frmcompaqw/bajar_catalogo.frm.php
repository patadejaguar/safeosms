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

$oficial = elusuario($iduser);
//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");	
//$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true); 
?>
<body onload="initComponents()">
<?php
$commad =		$_GET["c"];
if (!isset($commad)){
?>
<form name="frmOutVauchers" method="post" action="bajar_catalogo.frm.php?c=e">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td colspan='2'>
				<fieldset>
					<legend>Fecha de Catalogo</legend>
					Del: <?php echo ctrl_date(0); ?> <br /> <br />
					 Al: <?php echo ctrl_date(1); ?>
				</fieldset>
			</td>
		</tr>

		
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><a class="boton" onclick="frmOutVauchers.submit();">- Iniciar Exportaci&oacute;n -</a></td>
			</tr>
		</tfoot>
	</table>
</fieldset>
</form>
<?php
} elseif($commad == "e") {
	$xCw				= new cCatalogoCompacW();
	$fecha_inicial		= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
	$fecha_final		= $_POST["elanno1"] . "-" . $_POST["elmes1"] . "-" . $_POST["eldia1"];
	
	echo $xCw->setExport($fecha_inicial, $fecha_final);
}
?>
</body>
<script  >
function resizeMainWindow(){
	var mWidth	= 512;
	var mHeight	= 512;
	window.resizeTo(mWidth, mHeight);
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}

</script>
</html>

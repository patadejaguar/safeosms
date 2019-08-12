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
//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");	
//$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Subreporte R04 B-0417</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
$jsb	= new jsBasicForm("", iDE_CAPTACION);
$jsb->show();
//$jxc ->drawJavaScript(false, true); 
?>
<body>
<form name="" method="POST" action="./">
<fieldset>
	<legend>Reporte Prudencial R0411.- Cartera de Credito por Tipo</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Fecha Inicial</td>
			<td><?php 	$cFecha1 = new cFecha(0);
						$cFecha1->show();
			?></td>
			<td>Fecha Final</td>
			<td><?php 	$cFecha2 = new cFecha(0);
						$cFecha2->show();
			?></td>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
</body>
<script  >
</script>
</html>

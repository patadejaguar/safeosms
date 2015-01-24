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
<body>

<form name="" method="post" action="">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>
			<fieldset>
				<legend>Rango de Personas</legend>
				De c&oacute;digo: <br />
				<input type='text' name='cSocioInicial' value='0' id="idSocioInicial" /><br /><br />
				Al C&oacute;digo: <br />
				<input type='text' name='cSocioFinal' value='999999' id="idSocioFinal" /><br />
			</fieldset>
			</td>
			<td>
			<fieldset>
				<legend>Opciones</legend>
				<table  >
				<tbody>
					<tr>
						<td>Mostrar Aportaciones a la Reserva:</td>
						<th><input name="siReserva" type="checkbox" /></th>
					</tr>
					<tr>
						<td>Mostrar Aportaciones al Capital Social:</td>
						<th><input name="siCapital" type="checkbox" /> </th>
					</tr>
					<tr>
						<td>Mostrar Aportaciones al Fondo de Contingencia:</td>
						<th><input name="siContingencia" type="checkbox" /></th>
					</tr>
				</tbody>
				</table>
			</fieldset></td>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
</body>
<script  >

</script>
</html>

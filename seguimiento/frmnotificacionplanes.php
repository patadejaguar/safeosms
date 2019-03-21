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
	include_once "../core/core.deprecated.inc.php";
	include_once "../core/core.fechas.inc.php";
	include_once "../core/entidad.datos.php";

	$eacp= EACP_CLAVE;
	$trec = 15;


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>NOTIFICACIONES DE COBRO</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php 
jsbasic("frmnotificacion", "1", ".");
?>

<body>
<hr />
	<p class="frmTitle"><script> document.write(document.title ); </script></p>
<hr />
<form name="frmnotificacion" action="" method="">

	<table   border='0'>
		<tr>
			<td>Desde el Socio: </td><td>
			<input type='text' name='desde' value='37001' onchange="">
			</td>
			<td>Nombre:</td><td><input disabled name='nombredesde' type='text' value='' size="60"></td>
		</tr>
		<tr>
			<td>Hasta el Socio: </td>
			<td>
			<input type='text' name='hasta' value='37999' onchange="">
			</td>
			<td>Nombre:</td>
			<td><input disabled name='nombrehasta' type='text' value='' size="60"></td>
		</tr>
		<tr>
			<td>Notificacion que Quiere Ver</td><td colspan="2"><?php ctrl_select("general_reports", "name='ereport'", " WHERE aplica='notificacion_plan'"); ?></td>
		</tr>
		<tr>
			<td>Frecuencia de Pago</td>
			<td><?php echo ctrl_select("creditos_periocidadpagos", " name='frecuenciapagos' "); ?></td>
		</tr>

	</table>
<input type='button' name='btsend' value='VER INFORME'onClick='checksubmit();'>
</form>
</body>
<script  >
function checksubmit() {
	var vDesde = parseFloat(document.frmnotificacion.desde.value);
	var vHasta = parseFloat(document.frmnotificacion.hasta.value);
	var ynotif = document.frmnotificacion.ereport.value;
	var vfi = document.frmnotificacion.frecuenciapagos.value;
	if (vDesde > vHasta) {
		alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---����RANGO INVALIDO!!!!---");
	} else if (vHasta < vDesde) {
		alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---����RANGO INVALIDO!!!!---");
	} else {
			var sURL = ynotif + '?on=' +  vDesde + '&off=' + vHasta + '&f1=' + vfi;
			//alert(ynotif);
			rptNotif = window.open(sURL);
			//, "window", "width=600,height=400,scrollbars=yes,dependent"
			rptNotif.focus();

	}
}

</script>
</html>

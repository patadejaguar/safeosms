<?php
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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Editar Mvtos de Creditos</title>
</head>
	<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
	<?php
	jsbasic("frmdelrecibos", "", "1");
	?>
<body>
<p class="frmTitle"><script> document.write(document.title ); </script></p>
<form name="frmdelrecibos" action="frmupdatemvtos.php" method="post">
	<table   border='0'>
		<tr>
			<td>Clave de Persona</td><td><input type='text' name='idsocio' value='' onchange="envsoc();"></td>
			<td>Nombre Completo</td><td><input disabled name='nombresocio' type='text' size="60"></td>
		</tr>
		<tr>
			<td>Numero de Solicitud</td><td><input type='text' name='idsolicitud' value='' onchange="envsol();">
			<input type='button' name='concreds' value='[...]' onClick="listarcreds();"></td>
			<td>Descripcion Corta</td><td><input disabled name='nombresolicitud' type='text' value='' size="60"></td>
		</tr>

	</table>
	<input type='button' name='btsend' value='GUARDAR DATOS'onClick='frmSubmit();'>
</form>
<?php
$iddocto = $_POST["idsolicitud"];
	if (!$iddocto) {
		exit($msg_rec_warn . $fhtm);
	}
	minificha(2, $iddocto);
	echo "<hr></hr>";
	$sqlmvto = "SELECT * FROM operaciones_mvtos WHERE docto_afectado=$iddocto ORDER BY idoperaciones_mvtos";
	$rsmvto = mysql_query($sqlmvto);
	echo "<table   border='0'><tr>
	<th scope='col' class='th'>Fecha Afectacion</th>
	<th scope='col' class='th'>Tipo Operacion</th>
	<th scope='col' class='th'>Afectacion</th>
	<th scope='col' class='th'>Valor Afectacion</th>
	<th scope='col' class='th'>Vencimiento</th>
	<th scope='col' class='th'>Estatus</th>
	<th scope='col' class='th'>Periodo del Socio</th>
	<th scope='col' class='th'>Detalles</th>
	<th scope='col' class='th'>Operaciones</th>
	</tr>";
		while($ryx = mysql_fetch_array($rsmvto)) {
			$tipoop = eltipo("operaciones_tipos", $ryx[6]);
			$estatus = eltipo("operaciones_mvtosestatus", $ryx[12]);
			echo "<tr>
			<td class='ligth'>$ryx[2]</td>
			<td class='ligth'>$tipoop</td>
			<td class='number'>$ryx[7]</td>
			<td class='number'>$ryx[10]</td>
			<td class='ligth'>$ryx[11]</td>
			<td class='ligth'>$estatus</td>
			<td class='ligth'>$ryx[14]</td>
			<td class='ligth'>$ryx[23]</td>
<th><img src='images/common/edit.png' width='18' height='17' onClick='modifmvto($ryx[0]);' title='Editar Registro'>
					<img src='images/common/trash.png' width='18' height='17' onClick='eliminame($ryx[0]);' title='Eliminar Registro'></th>			</tr>";
		}
	echo "</table>";
	@mysql_free_result($rsrec);
	//
	$numeroops = "SELECT COUNT(idoperaciones_mvtos) AS 'obtener' FROM operaciones_mvtos WHERE recibo_afectado=$idrecibo";
	$nopers = mifila($numeroops, "obtener");
	//

	echo "<form name='frmgoelim' action='clseliminarrecibos.php' method='post'>
	<input type='hidden' name='idrecibo' value='$idrecibo'>
	<input type='button' name='btsend' value='ELIMINAR RECIBO' onClick='frmgoelim.submit();'>
	</form>
	<p class='aviso'>Numero de Operaciones: $nopers</p>";
?>
</body>
<script>
	function modifmvto(curval) {
		elid = curval;
		urlcur = 'utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=operaciones_mvtos&f=idoperaciones_mvtos=' + elid;
		ulan = window.open(urlcur);
		ulan.focus();
	}
	function eliminame(id) {
		var sURL = "frm9d23d795f8170f495de9a2c3b251a4cd.php?t=operaciones_mvtos&f=idoperaciones_mvtos=" + id;
	delme = window.open( sURL, "window", "width=300,height=300,scrollbars=yes,dependent");
	delme.focus();

	}

</script>
</html>

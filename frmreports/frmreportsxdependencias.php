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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.config.inc.php";
include_once "../libs/sql.inc.php";

$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reportes Por Instituciones con Convenios</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("frmreports", "1", ".");
?>
<body>
<fieldset>
	<legend>Reportes Por Instituciones con Convenios</legend>
<form name="frmreports" action="frmreportsxcuenta.php" method="post">
	<table width="95%"  >
		<tr>
		<td>Dependencia</td><td colspan="3"><?php ctrl_select("socios_aeconomica_dependencias", " name='dependencia' "); ?></td>		</tr>
		<tr>
			<td>Fecha Inicial</td><td><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td>Del Registro:</td><td><input type="text" name="for_r" value="0"></td>
			<td>Al Registro:</td><td><input type="text" name="to_r" value="100"></td>
		</tr>

		<tr>
			<td >Reporte a Ejecutar</td>
			<td colspan="3"><?php ctrl_select("general_reports", " name='ereport' ", " WHERE aplica='dependencia'"); ?></td>
		</tr>

		<tr>

		</tr>
		<tr>
			<td>Exportar como</td>
			<td colspan="3"><select name="mostrar_como">
				<option value="default" selected>Por Defecto(xml)</option>
				<option value="csv">Archivo Delimitado por comas (cvs)</option>
				<option value="csv">Archivo Delimitado por Tabulaciones(tvs)</option>
				<option value="txt">Archivo de Texto(txt)</option>
				<option value="page">Pagina Web(www)</option>
				<option value="xls">Excel</option>
			</select>
			</td>
		</tr>

	</table>
	<input type='button' name='btsend' value='EJECUTAR / VER REPORTE'onClick='open_rpt_x_date();'>
</form>
</fieldset>
</body>
<script>
	function open_rpt_x_date() {
		//
		isUrl = document.frmreports.ereport.value;
		//
		anno0 = document.frmreports.elanno0.value;
		mes0 = document.frmreports.elmes0.value;
		dia0 = document.frmreports.eldia0.value;
		fi = anno0 + '-' + mes0 + '-' + dia0;
		//
		anno1 = document.frmreports.elanno1.value;
		mes1 = document.frmreports.elmes1.value;
		dia1 = document.frmreports.eldia1.value;
		ff = anno1 + '-' + mes1 + '-' + dia1;
		
		vfor = document.frmreports.for_r.value;
		vto = document.frmreports.to_r.value;
		vfi = 0;	//document.frmreports.frecuenciapagos.value;
		vf2 = 0; 	//document.frmreports.estatuscredito.value;
		vf3 = 0;	//document.frmreports.tipo_operacion.value;
		vOut = document.frmreports.mostrar_como.value;
		vDoc = document.frmreports.dependencia.value;
		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---!!!!RANGO INVALIDO!!!!---");
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---!!!!RANGO INVALIDO!!!!---");
		} else {

			var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vfi + '&f2=' + vf2  + '&f3=' + vf3 + '&out=' + vOut + '&docto=' + vDoc;
				prep = window.open(urlrpt, "window" + vOut,"width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();
		}
	}

</script>
</html>

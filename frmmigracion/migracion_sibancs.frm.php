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
<title>Reportes de Creditos por oficial</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("frmreports", "1", ".");
?>
<body onload='initComponents()'>
<fieldset>
	<legend>Reportes Exportables de Migracion.- SI-BancS</legend>

<form name="frmreports" action="reportes_de_migracion.frm.php" method="post">
<hr />

	<table border='0'  >

<tr>
			<td colspan="2">
			<fieldset>
			<legend>Reporte a Ejecutar</legend>
			<?php ctrl_select("general_reports", " name='ereport' ", " WHERE aplica='migracion-sibancs'"); ?>
			 <br />
			</fieldset>
			</td>
</tr>
<tr>

		<td colspan="2">
			<fieldset>
			<legend>Exportar Reporte Como</legend>
			<select name="mostrar_como">
				<option value="default" selected>Por Defecto(xml)</option>
				<option value="csv">Archivo Delimitado por comas (cvs)</option>
				<option value="csv">Archivo Delimitado por Tabulaciones(tvs)</option>
				<option value="txt">Archivo de Texto(txt)</option>
				<option value="page">Pagina Web(www)</option>
				<option value="excel">Excel</option>
			</select>
			 <br />
			</fieldset>

		</tr>



  </table>
  <input type="button" name="cmdRun" value="VER / IMPRIMIR REPORTE" onClick="open_rpt_x_date(document.frmreports.ereport.value);">
</form>
</fieldset>
</body>
<script>
var workform = document.frmreports;
var wfrm = document.frmreports;

	function open_rpt_x_date(isUrl) {
				//control de opciones

		vOut 	= document.frmreports.mostrar_como.value;


		fi = 0;
		ff = 0;
			var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&out=' + vOut;
				prep = window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();

	}
function resizeMainWindow(){
	var mWidth	= 384;
	var mHeight	= 384;
	window.resizeTo(mWidth, mHeight);	
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}
</script>
</html>

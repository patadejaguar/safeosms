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
include_once("../libs/dbase.inc.php");
include_once("../libs/contpaqw_dbx.inc.php");
$oficial = elusuario($iduser);
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
$jxc = new TinyAjax();
function initProcessDB($file){
	$x = volcarDBF($file);
	return $x[SYS_MSG];
}
$jxc ->exportFunction('initProcessDB', array('idNameDB'), "#idmsg");
$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
$jxc ->drawJavaScript(false, true);
?>
<body>

<form name="frmVolcarDB" method="post" action="frm_export_table_cw.php">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Nombre de la Tabla a Exportar</td>
			<td><select name="cNameDB" id="idNameDB">
					<option value="CTW10001">Catalogo</option>
					<option value="CTW10002">Asociacion</option>
					<option value="CTW10003">Polizas</option>
					<option value="CTW10004">Movimientos</option>
					<option value="CTW10005">Saldos, cargos y abonos</option>
					<option value="CTW10006">Prep?lizas</option>
					<option value="CTW10007">Movimientos de prep?lizas</option>
					<option value="CTW10008">Tablas</option>
					<option value="CTW10009">Diarios</option>
					<option value="CTW10010">Grupos estad?sticos autom?ticos</option>
					<option value="CTW10011">Tipos de cambio de monedas</option>
					<option value="CTW10012">Definici?n de Ejercicios</option>
					<option value="CTW10015">Porcentajes</option>
					<option value="CTW10016">Presupuesto</option>
					<option value="CTW20001">Activos fijos</option>
					<option value="CTW20002">Datos fiscales</option>
				</select></td>
		</tr>
		<tr>
			<th colspan="2"><a class="boton" onclick="initProcessDB();">Ejecutar Proceso</a></th>
		</tr>
		</tbody>
	</table>
	<p id="idmsg" class="aviso">Este Procedimiento sustituir&aacute; los Datos Contenidos en la Base de Datos</p>
</fieldset>
</form>
</body>
<script  >
</script>
</html>

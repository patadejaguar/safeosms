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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Resguardo de Garantias</title>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
	jsbasic("frmresguardo", "", ".");
?>
</head>
<body>
<fieldset>
	<legend>Resguardo de Garantias</legend>


<form name="frmresguardo" action="frmresguardogarantias.php" method="post">
	<table    >
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='0' onchange="envsoc();" size='12' class='mny' /><?php echo CTRL_GOSOCIO; ?></td>
			<td  colspan='2'><input name='nombresocio' type='text' disabled value='' size="40" /></td>
		</tr>
		<tr>
			<td>Numero de Solicitud</td>
			<td><input type='text' name='idsolicitud' value='0' onchange="envsol();"  size='12' class='mny' />
			<?php echo CTRL_GOCREDIT; ?></td>
			<td  colspan='2'><input name='nombresolicitud' type='text' disabled value='' size="40" /></td>
		</tr>
		<tr>
			<td>Identificador de la Garantia</td>
			<td><input type='text' name='idgarantia' id="idgarantia" value='0'  size='8' class='mny' />
			<img style="width: 16px; height: 16px;" alt="" src="../images/common/search.png" align='middle' onclick="showGarantias()"/></td>
			<td>Observaciones</td>
			<td><input type="text" value="" name="o_resguardo" size="50" maxlength="55" /></td>
		</tr>
		<tr>
			<th colspan='4' align='center'>
	<input type='button' name='btnsend' value='GUADAR DATOS' onclick="document.frmresguardo.submit();">   
	<input type='button' name='cActualizar' value='Editar/Actualizar' onclick='actualizame();' /></th>
		</tr>
	</table>

	<p class='aviso'>NO OLVIDE AGREGAR EL IDENTIFICADOR</p>
</form>

</fieldset>
<?php
	$idgar = $_POST["idgarantia"];
	$idsoc = $_POST["idsocio"];
	$idsol = $_POST["idsolicitud"];
	$o_res = $_POST["o_resguardo"];
	
/*	if ($estatus == 2) {
		exit("<p class='aviso'>LA GARANTIA YA SE HA ESTA RESGUARDADA</p></body></html>");
	}		// 	*/
if ($idgar){
	
	if (!$idsoc) {
		echo ("<p class='aviso'>FALTA EL NUMERO DE PERSONA</p>");
	}
	if (!$idsol) {
		echo ("<p class='aviso'>FALTA LA SOLICITUD</p>");
	}
	$estatus = volcartabla("creditos_garantias", 10, "idcreditos_garantias=$idgar");
	if ($estatus == 3) {
		echo ("<p class='aviso'>LA GARANTIA YA SE HA ENTREGADO</p>");
	} elseif ($estatus == 2) {
		echo ("<p class='aviso'>LA GARANTIA YA SE HA RESGUARDADO ANTERIORMENTE</p>");
	} else {
	$fresguardo = fechasys();
	$sqlug = "UPDATE creditos_garantias SET estatus_actual=2, fecha_resguardo='$fresguardo', idusuario=$iduser,
	observaciones_del_resguardo='$o_res'
	 WHERE socio_garantia=$idsoc AND idcreditos_garantias=$idgar AND solicitud_garantia=$idsol";
	
	my_query($sqlug);	
	}
	$x = new cFicha(iDE_SOCIO, $idsoc);
	$x->setTableWidth();
	$x->show();
	minificha(4, $idgar);
	
	echo "<p class='aviso'>EL REGISTRO SE HA HECHO DE FORMA SATISFACTORIA</P>
	<p class='aviso'><input type='button' name='btnprint' value='IMPRIMIR RECIBO DE RESGUARDO' onclick='printrecibo();'>
	</p>";
}	
?>
</body>
<script>
function printrecibo() {
	var nURL = "../rpt_formatos/rptreciboresguardo.php?i=<?php echo $idgar; ?>";
	var killrpt = window.open(nURL, "window");
		killrpt.focus()
}
function showGarantias(){
	var id = document.frmresguardo.idsolicitud.value;
	var url = "../utils/frmsearchgarantias.php?f=idgarantia&i=" + id;
	jsGenericWindow(url);
}
function actualizame() {
	var id = document.frmresguardo.idgarantia.value;
	var url = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=creditos_garantias&f=idcreditos_garantias=" + id;
	jsGenericWindow(url);
}	
</script>
</html>
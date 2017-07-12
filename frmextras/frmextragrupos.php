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
include_once "../core/core.common.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";

	$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Actualizar / Agregar Datos Complementarios de Grupos Solidarios</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("myform", 1, ".");
?>
<body onLoad="loaddatos();">
<fieldset>
<legend>Actualizar / Agregar Datos Complementarios de Grupos Solidarios</legend>
<form name='myform' action='' method='post'>
	<table   border='0'>
		<tr>
			<td>Numero del Grupo</td>
			<td><input type='text' name='idgrupo' value='' onchange="envgpo();" class='mny' size='4' /></td>
			<td>Nombre del Grupo</td>
			<td><input disabled name='nombregrupo' type='text' value='' size="40"></td>
		</tr>
	</table>
<input type='button' name='btsend' value='ENVIAR DATOS'onClick='frmSubmit();'>
</form>
<?php
$idgrupo = $_POST["idgrupo"];
	if(!$idgrupo) {
		$idgrupo = 99;
		echo($msg_rec_warn . $fhtm);
	} else {
		$xGr	= new cGrupo($idgrupo, true);
		$xGr->init();
		echo $xGr->getFicha(true);
	echo "<hr>
	<input type='button' name='cmdModif' value='MODIFICAR DATOS'onClick='modifdatos();'>
	<input type='button' name='cmdplaneacionprint' value='VER/IMPRIMIR PLANEACION DE CREDITO'onClick='open_planeacion($idgrupo);'>
	<hr />
	";
		$sqlcx = $sqlb10 . " AND socios_general.grupo_solidario=$idgrupo AND socios_vivienda.principal='1' LIMIT 0,50";


		$cTbl = new cTabla($sqlcx, 0);
		$cTbl->setKeyField("codigo");
		$cTbl->addTool(1);
		$cTbl->Show("", false);

	}
?>
</fieldset>

</body>
<script>
 function modifdatos() {
 	var idgpo = document.myform.idgrupo.value;
 	url1 = '../frmgrupos/frmgposolidario.php?x=' + idgpo;
	mywin = window.open(url1);
	mywin.focus();
 }
 function loaddatos() {
 	document.myform.idgrupo.value = <?php echo $idgrupo; ?>;
 }
 function actualizame(id) {
			var urlrpt = "../frmsocios/frmupdatesocios.php?elsocio=" + id;
				prep = window.open(urlrpt, "window","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();				//			*/

 }
 function open_planeacion(id){
			var urlrpt = "../rpt_formatos/rptplaneacioncredito.php?on=" + id;
				prep = window.open(urlrpt, "window","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();				//			*/
 }
</script>
</html>

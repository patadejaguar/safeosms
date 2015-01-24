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
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";

$oficial 	= elusuario($iduser);
$f 			= $_GET["f"];

	require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//-------------------------------------------------------------
function jsShowGrupos($nombre_del_grupo){
	settype($nombre_del_grupo, "string");
	$rst = "";
if ($nombre_del_grupo) {
		$nombre_del_grupo = substr($nombre_del_grupo,0,6);
		/**
 		* 							Sql
 		*/
	$sql_grupos = "SELECT 
	`socios_grupossolidarios`.`idsocios_grupossolidarios`  AS 'numero',
	`socios_grupossolidarios`.`nombre_gruposolidario`          
	AS `nombre`,
	`socios_grupossolidarios`.`colonia_gruposolidario`         
	AS `colonia`,
	`socios_grupossolidarios`.`representante_nombrecompleto`   
	AS `representante`
	
			FROM socios_grupossolidarios 
			WHERE nombre_gruposolidario LIKE '%$nombre_del_grupo%'
					AND sucursal = '" . getSucursal() . "'
			LIMIT 0,10";
	$ctb = new cTabla($sql_grupos);
	$ctb->setEventKey("setGrupo");
	$ctb->setWidth();
	$rst = $ctb->Show();
}
	return $rst;
}
$jxc = new TinyAjax();
$jxc ->exportFunction('jsShowGrupos', array("idnombredelgrupo"), "#dResults");	
$jxc ->process();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Buscar Grupo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php 
$jxc ->drawJavaScript(false, true);
?>
<body>
<fieldset>
	<legend>&nbsp;&nbsp;&nbsp;&nbsp;Buscar Grupos&nbsp;&nbsp;&nbsp;&nbsp;</legend>
<form name="frmsearchgroup" method="post" action="frmsgrupos.php">
	<table border='0'    >
		<tr>
			<td>Nombre del Grupo</td><td><input type='text' name='nombredelgrupo' id="idnombredelgrupo" value='' size="50" onkeydown="jsShowGrupos();"></td>
		</tr>
	</table>
	<div id="dResults">
	<?php
	$sqlGr = "SELECT 
	`socios_grupossolidarios`.`idsocios_grupossolidarios`  AS 'numero',
	`socios_grupossolidarios`.`nombre_gruposolidario`          
	AS `nombre`,
	`socios_grupossolidarios`.`colonia_gruposolidario`         
	AS `colonia`,
	`socios_grupossolidarios`.`representante_nombrecompleto`   
	AS `representante`

	FROM socios_grupossolidarios 
	WHERE
		sucursal = '" . getSucursal() . "'
	ORDER BY
	 	`socios_grupossolidarios`.`fecha_de_alta`
	LIMIT 0,10";
	$cTb = new cTabla($sqlGr);
	$cTb->setEventKey("setGrupo");
	$cTb->setWidth();
	$cTb->Show("", false);
	?>
	</div>
	<p class="aviso"><input type="button" onclick="window.close();" value="cerrar ventana" /></p>

</form>
</fieldset>
</body>

<script  >
function setGrupo(id){
	opener.document.<?php echo $f; ?>.idgrupo.value = id;
	opener.document.<?php echo $f; ?>.idgrupo.focus();
	opener.document.<?php echo $f; ?>.idgrupo.select();
	opener.envgpo();
	window.close();
}
</script>
</html>

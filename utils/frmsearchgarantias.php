<?php
/**
 * Titulo:
 * Actualizado:
 * Responsable:
 * Funcion:
 */
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

$oficial = elusuario($iduser);
$i = $_GET["i"];		//Solicitud
$f = $_GET["f"];
if (!$i){
	echo "<script languaje=\"javascript\">window.close();</script>";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>LISTADO GARANTIAS X CREDITO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>

<body onload="resizeMainWindow()">
<hr />

<form name="" method="post" action="">
	<table border='0'  >
		<tr>
			<th>Codigo</th>
			<th>Tipo</th>
			<th>Estatus</th>
			<th>Valor</th>
		</tr>	
	<?php
		$sql = "SELECT
	`creditos_garantias`.`idcreditos_garantias`                AS `codigo`,
	`creditos_garantias`.`solicitud_garantia`                  AS `credito`,
	`creditos_tgarantias`.`descripcion_tgarantias`             AS `tipo`,
	`creditos_garantiasestatus`.`descripcion_garantiasestatus` AS `estatus`,
	`creditos_garantias`.`monto_valuado`                       AS `valor` 
FROM
	`creditos_garantias` `creditos_garantias` 
		INNER JOIN `creditos_tgarantias` `creditos_tgarantias` 
		ON `creditos_garantias`.`tipo_garantia` = `creditos_tgarantias`.
		`idcreditos_tgarantias` 
			INNER JOIN `creditos_garantiasestatus` `creditos_garantiasestatus` 
			ON `creditos_garantias`.`estatus_actual` = 
			`creditos_garantiasestatus`.`idcreditos_garantiasestatus`
WHERE
	(`creditos_garantias`.`solicitud_garantia` =$i)";
		
		$rs = mysql_query($sql, cnnGeneral());
		$trs = "";
		while($rw = mysql_fetch_array($rs)){

			$trs = $trs . "<tr>
								<td onclick='setItem(" . $rw["codigo"] . ")' class='key'>" . $rw["codigo"] . "</td>
								
								<td>" . $rw["tipo"] . "</td>
								<td>" . $rw["estatus"] . "</td>
								<td class='mny'>" . $rw["valor"] . "</td>
							</tr>";
		}
		
		echo $trs;
	?>
	</table>
	<p class="aviso"><input type="button" onclick="window.close();" value="cerrar ventana" /></p>
</form>
</body>
<script  >
function setItem(id){
	opener.document.getElementById("<?php echo $f; ?>").value = id;
	window.close();
}
function resizeMainWindow(){
	var mWidth	= 640;
	var mHeight	= 320;
	window.resizeTo(mWidth, mHeight);	
}
</script>
</html>

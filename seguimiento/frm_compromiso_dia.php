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

$oficial = elusuario($iduser);
$i = $_GET["i"];
if (!$i){
	echo JS_CLOSE;
}
$fc = fecha_corta($i);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Compromisos del dia <?php echo $fc; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>

<body>
<hr />
	<p class="frmTitle"><script> document.write(document.title); </script></p>
<hr />
<?php
$sql = "SELECT socios.codigo, socios.nombre, seguimiento_compromisos.tipo_compromiso, seguimiento_compromisos.anotacion, 
	seguimiento_compromisos.idseguimiento_compromisos AS 'id',
	seguimiento_compromisos.oficial_de_seguimiento,
	seguimiento_compromisos.tipo_compromiso,
	seguimiento_compromisos.credito_comprometido,
	seguimiento_compromisos.idseguimiento_compromisos,
	seguimiento_compromisos.estatus_compromiso
				FROM socios, seguimiento_compromisos 
				WHERE seguimiento_compromisos.socio_comprometido=socios.codigo
				AND seguimiento_compromisos.fecha_vencimiento='$i' ";
//AND seguimiento_compromisos.fecha_vencimiento='$dia'
	$rs = mysql_query($sql);
	$tds = "";
	while ($rw = mysql_fetch_array($rs)){
		$oficial_a_cargo = elusuario($rw[5]);
			echo	"<fieldset>
			<legend>Compromiso Num. $rw[8] </legend>
					<table width='100%'>
						 <tr>
							<th>Clave de Persona</th> <td>$rw[0]</td>
						</tr><tr>
							<th>Nombre Completo</th> <td>$rw[1]</td>
						</tr><tr>
							<th>Numero de Credito</th> <td>$rw[7]</td>							
						</tr><tr>
							<th>Oficial a Cargo</th> <td>$oficial_a_cargo</td>
						</tr><tr>
							<th>Tipo de Comrpomiso</th><td>$rw[6]</td>
						</tr><tr>
							<th>Detalles</th><td rowspan='4'>$rw[3]</td>							
						</tr>
						</table>
		</fieldset> ";
			minificha(2, $rw[7]);
			echo "<hr />";
	}
	@mysql_free_result($rs);

?>
<p class="aviso"><input type="button" onclick="window.close();" value="cerrar ventana" /></p>
</body>
</html>

<?php
/**
 * Titulo: Creditos Autorizados
 * @since Actualizado: 27-Agosto-2007
 * @author Responsable: Balam Gonzalez Luis
 *  Funcion: Autoriza los Creditos
 * Se modifico el saldo actual del Mopnto Autorizado a Cero, este cambio se hara hasta la ministracion
 * 20080602	Se efectuaron algunas modificaciones menores
 * 20080702	Mejor soporte en Datos de Creditos
 * 20080722	Se Agrego el Documento de Autorizacion para Grupos Solidarios
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Cancelacion de Lineas de Credito</title>
</head>
	<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
	<script   src="../jsrsClient.js"></script>
<body>
<fieldset>
	<legend>Cancelacion de Lineas de Credito</legend>
	
<form name="frmcancelarlineas" action="frmcancelarlineas.php" method="post">
	<table   width="95%">
		<tr>
			<td>Numero de Linea</td><td><input type='text' name='idlinea' value=''></td>
		</tr>
		<tr>
			<td>Descripcion</td><td><input type='text' name='descripcionlinea' value='' size="45"></td>
		</tr>
	</table>
</form>
</fieldset>

</body>
</html>

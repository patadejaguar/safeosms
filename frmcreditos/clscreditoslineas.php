<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once ("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		header ("location:../404.php?i=999");	
	}
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
<title>Linea de Credito Autorizada</title>
</head>
	<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
	<script   src="../js/jsrsClient.js"></script>
<body>
<fieldset>
<legend>Linea de Credito Autorizada</legend>
<?php
	$idsocio 			= $_POST["idsocio"];
	$montolinea 		= $_POST["montolinea"];
	$observaciones 		= $_POST["observaciones"];
	$numerohipoteca		= $_POST["numerohipoteca"];
	$montohipoteca 		= $_POST["montohipoteca"];
	$fechavenc 			= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
	$fechaalta 			= fechasys();
	$eacp				= EACP_CLAVE;
	$sucursal			= getSucursal();
	$estado = 1;						// VIGENTE
	
		$sqllcf = "numero_socio, monto_linea, observaciones, numerohipoteca, monto_hipoteca, 
					fecha_de_vencimiento, fecha_de_alta, estado, idusuario, sucursal, eacp";
		$sqllcv = "$idsocio, $montolinea, '$observaciones', '$numerohipoteca', $montolinea, 
				'$fechavenc', '$fechaalta', $estado, $iduser, '$sucursal', '$eacp'";
		
	$sqllc = "INSERT INTO creditos_lineas($sqllcf) VALUES ($sqllcv)";
	my_query($sqllc);
	
	echo "<p class='aviso'>la Linea de Credito ha Sido Agregada como Autorizada, de esta fecha en adelante, el Socio <b>" . getNombreSocio($idsocio) . "</b>
	sera tomado en cuenta para Ministrarse Creditos por un monto no mayor a $montolinea; por lo que se tendra que respetar dicha cantidad.</p> 
	<input type='button' name='btnprint' value='IMPRIMIR AUTORIZACION'>
	";
?>
</fieldset>
</body>
</html>

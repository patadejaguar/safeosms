<?php
/**
 * Editor de Movimientos de captacion [RAW]
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package captacion
 * @subpackage forms
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
include_once("../core/core.captacion.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.config.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Editar Mvtos de Captacion</title>
</head>
	<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
	<?php
	jsbasic("frmdelrecibos", "", ".");
	?>
<body>
<fieldset>
	<legend>Editar Mvtos de Captacion [v1.0.02]</legend>
<form name="frmdelrecibos" action="frmupdatemvtoscaptacion.php" method="post">
	<table   border='0'>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc();"  class='mny' size='12'></td>
			<td>Nombre Completo</td>
			<td><input disabled name='nombresocio' type='text' size="40"></td>
		</tr>
		<tr>
			<td>Numero de cuenta de Inversi&oacute;n</td>
			<td><input type='text' name='idcuenta' value='' onchange="envcta();" class='mny' size='12' >
			<?php echo CTRL_GOCUENTAS; ?></td>
			<td>Descripcion Corta</td><td><input disabled name='nombrecuenta' type='text' value='' size="40"></td>
		</tr>
	</table>
	<input type='button' name='btsend' value='ENVIAR DATOS'onClick='frmSubmit();'>
</form>
<?php
$iddocto = $_POST["idcuenta"];
	if (!$iddocto) {
		exit($msg_rec_warn . $fhtm);
	}


		$cCap	= new cCuentaDeCaptacion($iddocto);
		$cCap->init();
		echo $cCap->getFicha(true);

		$sqlmvto = "SELECT
		`operaciones_mvtos`.`idoperaciones_mvtos`   AS `codigo`,
		`operaciones_mvtos`.`fecha_operacion`       AS `operado`,
		`operaciones_mvtos`.`fecha_afectacion`      AS `afectado`,
		`operaciones_mvtos`.`recibo_afectado`       AS `recibo`,
		`operaciones_mvtos`.`tipo_operacion`        AS `operacion`,
		`operaciones_tipos`.`descripcion_operacion` AS `descripcion`,
		`operaciones_mvtos`.`afectacion_real`       AS `monto`,
		`operaciones_mvtos`.`docto_afectado`
	FROM
		`operaciones_mvtos` `operaciones_mvtos`
			INNER JOIN `operaciones_tipos` `operaciones_tipos`
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos`
	WHERE
		(`operaciones_mvtos`.`docto_afectado` =$iddocto)
	ORDER BY
		`operaciones_mvtos`.`fecha_operacion`
	";

		$cEdit		= new cTabla($sqlmvto);
		$cEdit->addTool(1);
		$cEdit->addTool(2);
		$cEdit->setKeyField("idoperaciones_mvtos");
		$cEdit->Show("", false);

?>
</fieldset>
</body>
<script   >
	<?php
		echo $cEdit->getJSActions();
	?>

</script>
</html>

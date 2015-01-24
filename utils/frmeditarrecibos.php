<?php
/**
 * Editor de recibos, operaciones modo RAW.
 * @author Balam Gonzalez Luis Humberto
 * @package operaciones
 * @subpackage forms
 * @version 1.1.20
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
include_once "../core/core.operaciones.inc.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Editar / Consultar / Eliminar :: Recibo por Numero</title>
</head>
	<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
<form name="frmdelrecibos" action="frmeditarrecibos.php" method="post">
<fieldset>
	<legend>Editar / Consultar / Eliminar :: Recibo por Numero</legend>
<table  >
	<tr>
		<td>Numero de Recibo a Modificar</td>
		<td><input type="text" name="idrecibo" class='mny' size='12' ></td>
	</tr>
	<tr>
		<th colspan='2'><input type='button' name='btnEnviar' value='CONSULTAR MOVIMIENTOS DEL RECIBO' onClick='frmdelrecibos.submit();'></th>
	</tr>
</table>
</fieldset>
</form>
<hr />
<?php
$idrecibo = $_POST["idrecibo"];
	if (!$idrecibo) {
		exit($msg_rec_warn . $fhtm);
	}
	$xRec	= new cReciboDeOperacion(false, false, $idrecibo);
	$xRec->init();
	echo $xRec->getFicha(true);
	$uri = $xRec->getURI_Formato();
/* ----------------- DATOS --------------- */
//	$numeroops = "SELECT COUNT(idoperaciones_mvtos) AS 'obtener' FROM operaciones_mvtos WHERE recibo_afectado=$idrecibo";
//	$nopers = mifila($numeroops, "obtener");

		$sqlmvto = "SELECT
		`operaciones_mvtos`.`idoperaciones_mvtos`   AS `codigo`,
		`operaciones_mvtos`.`socio_afectado`       	AS `socio`,
		`operaciones_mvtos`.`docto_afectado`       	AS `documento`,
		`operaciones_mvtos`.`fecha_operacion`       AS `operado`,
		`operaciones_mvtos`.`fecha_afectacion`      AS `afectado`,
		`operaciones_mvtos`.`periodo_socio`			AS `per`,
		`operaciones_mvtos`.`tipo_operacion`        AS `mvto`,
		`operaciones_tipos`.`descripcion_operacion` AS `descripcion`,
		`operaciones_mvtos`.`afectacion_real`       AS `monto`
	FROM
		`operaciones_mvtos` `operaciones_mvtos`
			INNER JOIN `operaciones_tipos` `operaciones_tipos`
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos`
	WHERE
		(`operaciones_mvtos`.`recibo_afectado` =$idrecibo)
	ORDER BY
		`operaciones_mvtos`.`fecha_operacion`,
		`operaciones_mvtos`.`socio_afectado`,
		`operaciones_mvtos`.`docto_afectado`,
		`operaciones_mvtos`.`periodo_socio`
	";

		$cEdit		= new cTabla($sqlmvto);
		$cEdit->addTool(1);
		$cEdit->addTool(2);
		$cEdit->setTdClassByType();
		$cEdit->setKeyField("idoperaciones_mvtos");
		$cEdit->Show("", false);
	echo "<form name='frmgoelim' action='clseliminarrecibos.php?u=e' method='POST'>
	<hr />
	<input type='hidden' name='idrecibo' value='$idrecibo'>
	<table border='0'>

		<tr>
		<th><input type='button' name='btsend' value='EDITAR RECIBO' onClick='actualizaRec($idrecibo);'></th>
		<th><input type='button' name='btsend' value='ELIMINAR RECIBO Y OPERACIONES' onClick='frmgoelim.submit();'></th>
		<th><a class=\"button\" onclick=\"ImprimirRecibo();\" >&nbsp;&nbsp;&nbsp;Reimprimir Recibo&nbsp;&nbsp;&nbsp;</a></th>
		</tr>
	</table>
	<hr />
	</form>
	<p class='aviso'>Numero de Operaciones: $nopers</p>";
?>
</fieldset>
</body>
<script   >
	<?php
		echo $cEdit->getJSActions();
	?>
	function actualizaRec(id) {
		url = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=operaciones_recibos&f=idoperaciones_recibos=" + id;
				myurl = window.open(url);
				myurl.focus();

		}
		function ImprimirRecibo(){
			var mURI	= "<?php echo $uri; ?>";
			var	x		= window.open(mURI);
				x.focus();

		}
</script>
</html>

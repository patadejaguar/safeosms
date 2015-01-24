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

$xHP		= new cHPage("Editar / Consultar / Eliminar :: Movimientos de Personas");

$socio		= ( isset($_REQUEST["s"]) ) ? $_REQUEST["s"] : false;
if ( isset($_REQUEST["persona"]) ){
	$socio	= $_REQUEST["persona"];
}
echo		$xHP->getHeader();

echo $xHP->setBodyinit();
if($socio == false){
?>
<form name="frmdelrecibos" action="socios.editar_mvtos.frm.php" method="post">
<fieldset>
	<legend>Editar / Consultar / Eliminar :: Movimientos de Personas</legend>
<table  >
	<tr>
		<td>Clave de Persona</td>
		<td><input type="text" name="s"  class='mny' size='12'  ></td>
	</tr>
	<tr>
		<td><input type='button' name='sendme' value='CONSULTAR MOVIMIENTOS' onClick='frmdelrecibos.submit();' /></td>
	</tr>
</table>
</fieldset>
</form>
<?php
} else {

	
	$xSoc		= new cSocio($socio, true);
	echo $xSoc->getFicha(true);
/* ----------------- DATOS --------------- */
	//$numeroops = "SELECT COUNT(idoperaciones_mvtos) AS 'obtener' FROM operaciones_mvtos WHERE recibo_afectado=$idrecibo";
	//$nopers = mifila($numeroops, "obtener");

		$sqlmvto = "SELECT
			`operaciones_mvtos`.`idoperaciones_mvtos`   AS `codigo`,
			`operaciones_mvtos`.`docto_afectado`       AS `documento`,
			`operaciones_mvtos`.`recibo_afectado`       AS `recibo`,
			`operaciones_mvtos`.`fecha_operacion`       AS `operado`,
			`operaciones_mvtos`.`fecha_afectacion`      AS `afectado`,
	
			`operaciones_mvtos`.`tipo_operacion`        AS `operacion`,
			`operaciones_tipos`.`descripcion_operacion` AS `descripcion`,
			`operaciones_mvtos`.`afectacion_real`       AS `monto`
		FROM
			`operaciones_mvtos` `operaciones_mvtos`
				INNER JOIN `operaciones_tipos` `operaciones_tipos`
				ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
				`idoperaciones_tipos`
		WHERE
			(`operaciones_mvtos`.`socio_afectado` =$socio)
		ORDER BY
			`operaciones_mvtos`.`fecha_operacion`,
			`operaciones_mvtos`.`docto_afectado`,
			`operaciones_mvtos`.`tipo_operacion`
		";

		$cEdit		= new cTabla($sqlmvto);
		$cEdit->setEventKey("jsEditClick");
		//$cEdit->addTool(1);
		$cEdit->addTool(2);
		$cEdit->setKeyField("idoperaciones_mvtos");
		$cEdit->Show("", false);
}
?>
</body>
<script   >
	<?php
		echo $cEdit->getJSActions();
	?>
function jsEditClick(id){
	jsUp('operaciones_mvtos','idoperaciones_mvtos', id);
}
</script>
</html>

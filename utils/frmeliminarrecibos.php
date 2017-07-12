<?php
/**
 * Editor/Exterminador de recibos, operaciones modo RAW.
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
$xHP				= new cHPage();
$xQL				= new MQL();
$xLi				= new cSQLListas();

ini_set("max_execution_time", 300);

$xHP->init();

?>
<form name="frmdelrecibos" action="frmeliminarrecibos.php" method="post">
<?php
$FiltroByUser			= " AND `operaciones_recibos`.idusuario=$iduser ";
$dias_max_recibos		= DIAS_PARA_EDITAR_RECIBOS;


	if (MODO_DEBUG == true){
		$FiltroByUser 		= "";
		$dias_max_recibos 	= 1;
	}
	$FechaInicial = restardias(fechasys(), $dias_max_recibos);

	$sql 		= "
	SELECT
	`operaciones_recibos`.`idoperaciones_recibos`,
	CONCAT( `operaciones_recibos`.`idoperaciones_recibos`, '-', 
	getFechaMX(`operaciones_recibos`.`fecha_operacion`),	'-',
	`socios_general`.`codigo`, '-',
	`socios_general`.`nombrecompleto`, ' ',
	`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`, '-Monto: ',
	`operaciones_recibos`.`total_operacion`) AS 'concepto'
	FROM
	`socios_general` `socios_general` 
		INNER JOIN `operaciones_recibos` `operaciones_recibos` 
		ON `socios_general`.`codigo` = `operaciones_recibos`.`numero_socio`
	WHERE
		fecha_operacion>='$FechaInicial' $FiltroByUser 
	ORDER BY
		`operaciones_recibos`.`idoperaciones_recibos`,
		`operaciones_recibos`.`fecha_operacion`,
		`socios_general`.`nombrecompleto` ";
?>
<fieldset>
	<legend>Editar / Consultar / Eliminar :: Recibo por Numero</legend>
<table  >
	<tr>
		<td>Numero de Recibo a Modificar</td>
		<td><?php
		$cFJ = new cSelect("idrecibo", "id-recibo", $sql);
		$cFJ->setEsSql();
		$cFJ->show(false);		
		?></td>
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
	echo $xRec->getFicha(true, "", true);
	$uri = $xRec->getURI_Formato();
/* ----------------- DATOS --------------- */
//	$numeroops = "SELECT COUNT(idoperaciones_mvtos) AS 'obtener' FROM operaciones_mvtos WHERE recibo_afectado=$idrecibo";
//	$nopers = mifila($numeroops, "obtener");

		$sqlmvto = "SELECT
		`operaciones_mvtos`.`idoperaciones_mvtos`   AS `codigo`,
		`operaciones_mvtos`.`socio_afectado`       AS `socio`,
		`operaciones_mvtos`.`docto_afectado`       AS `documento`,
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
		(`operaciones_mvtos`.`recibo_afectado` =$idrecibo)
	ORDER BY
		`operaciones_mvtos`.`fecha_operacion`,
		`operaciones_mvtos`.`socio_afectado`,
		`operaciones_mvtos`.`docto_afectado`
	";

		$cEdit		= new cTabla($sqlmvto);
		$cEdit->addTool(1);
		$cEdit->addTool(2);
		$cEdit->setKeyField("idoperaciones_mvtos");
		$nopers	= $cEdit->getRowCount();
		$cEdit->Show("", false);
	echo "<form name='frmgoelim' action='clseliminarrecibos.php' method='POST'>
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
	<p class='aviso'>Numero de Operaciones: $nopers</p>
	</fieldset>";
?>

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
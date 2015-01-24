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
$xHP		= new cHPage();
$xHP->setTitle($xHP->lang(array("registro", "de", "flujo_de_efectivo")));

$idsolicitud = $_GET["solicitud"];

echo $xHP->getHeader(true);
?>
<script>
	function checkvalue() {
		if (document.frmflujoefvo.montoflujo.value > 0 ) {
			document.frmflujoefvo.submit();

		} else {
			alert('El Valor Indicado debe ser Mayor a 0');
			return 0;
		}
	}
</script>
<body>
<hr>
<?php 
/* verifica si el Numero de Solicitud exista */
	if  (!$idsolicitud) {
		echo "<p class='aviso'>NO EXISTE EL NUMERO DE SOLICITUD</p>";
		exit;
	}
	$isql = "SELECT numero_solicitud, numero_socio FROM creditos_solicitud WHERE numero_solicitud = $idsolicitud";
	if (db_regs($isql) <1){
		echo "<p class='aviso'>NO EXISTE EL NUMERO DE SOLICITUD</p>";
		exit;
	}
	$idsocio =	mifila($isql, "numero_socio");

?>
<form name="frmflujoefvo" action="clscreditosflujoefvo.php?socio=<?php echo $idsocio; ?>&solicitud=<?php echo $idsolicitud; ?>" method="POST">
	<table      >
	<tr>
	<td>Frecuencia</td>
	<td>
		<?php
			$gTSql= "SELECT * FROM creditos_periocidadflujo";
			$cSPF = new cSelect("periocidadflujo", "", $gTSql);
			$cSPF->setEsSql();
			$cSPF->show(false);
		?>
	</td>

	<td>Origen</td>
	<td><?php
			$gSql= "SELECT * FROM creditos_origenflujo";
			$cSFE	= new cSelect("origenflujo", "", $gSql);
			$cSFE->setEsSql();
			$cSFE->setOptionSelect(100);
			//$cSPE->setNRows(3);
			$cSFE->show(false);
		?></td>
	</tr>
	<tr>
		<td>Monto</td>
		<td><input TYPE="number" NAME="montoflujo" VALUE="0.00" class="mny" /></td>
		<td>Descripcion Exacta</td>
		<td><input type='text' name='describalo' value=''  size="45" maxlength="120" /></td>
	</tr>
	<tr>
		<td>Observaciones</td>
		<td colspan="3"><input type='text' name='observaciones' value='' maxlength="100" size="45" /></td>
	</tr>
	</table>
<p class='aviso'>
<input type="button" name="sendme" value="GUARDAR DATOS Y LIMPIAR FORMULARIO" onClick="checkvalue();">
</p>
</form>
<?php

$sqlm = $sqlb15  . " AND solicitud_flujo=$idsolicitud";
//echo $sqlm;
		$mTab = new cTabla($sqlm);
		$mTab->addTool(1);
		$mTab->addTool(2);
		$mTab->Show("", false);
		$mTab->setKeyField("idcreditos_flujoefvo");

$sqlss = "SELECT SUM(afectacion_neta) AS 'sumtrim' FROM $t_cfe WHERE solicitud_flujo=$idsolicitud";
$ssmoun = mifila($sqlss, "sumtrim");
echo "<hr>
	<table align='center'>
	<tr>
		<th>CAPACIDAD DE PAGO DIARIA:</th>
		<td class='mny'>$ $ssmoun</td>
	</tr>
	<tr>
		<th colspan='2'><input type='button' name='cmdrpt' value='Ver / Imprimir Formato de Acuse del Flujo de Efectivo' onClick='rptflujo($idsolicitud);'></td>
	</tr>
	</table>";

?>
<hr>
</body>
<script  >
	function rptflujo(lasol) {
		var misol 	= lasol;
		var varurl 	= '../rptcreditos/rpt_acuse_flujo_efvo.php?s=' + misol;
		var ulan 	= window.open(varurl);
			ulan.focus();
	}
	<?php
	echo $mTab->getJSActions();
	?>
</script>
</html>

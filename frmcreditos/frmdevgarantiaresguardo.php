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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Devoluci&oacute;n de Garantias en Resguardo</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("myformdgr", "", ".");
?>
<body>
<fieldset>
	<legend>Devoluci&oacute;n de Garantias en Resguardo</legend>


<form name='myformdgr' action='frmdevgarantiaresguardo.php' method='post'>
	<table   border='0'>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc();">
			<?php echo CTRL_GOSOCIO; ?></td>
			<td colspan='2'><input name='nombresocio' type='text' disabled value='' size="40"></td>
		</tr>
		<tr>
			<td>N&uacute;mero de Solicitud</td>
			<td><input type='text' name='idsolicitud' value='' onchange="envsol();">
			<?php echo CTRL_GOCREDIT; ?></td>
			<td  colspan='2'><input name='nombresolicitud' type='text' disabled value='' size="40"></td>
		</tr>
	</table>
<input type='button' name='btsend' value='ENVIAR DATOS'onClick='frmSubmit();'>
</form>
</fieldset>

<?php
$idsolicitud = $_POST["idsolicitud"];
	if (!$idsolicitud) {
		exit($msg_rec_warn . $fhtm);
	}
	// si el saldo del credito es mayor a cero, nanay, no se devuelve la garantia
	$saldo 		= volcarsol($idsolicitud, 22);
	$sdovenc 	= volcarsol($idsolicitud, 26);
	if($saldo > TOLERANCIA_SALDOS) {
			exit("<p class='aviso'>EL CREDITO TIENE SALDO POR PAGAR</p></body></html>");
	}
	if ($sdovenc > 0) {
			//exit("<p class='aviso'>EL CREDITO TIENE SALDO VENCIDO POR PAGAR</p></body></html>");
	}
	// Imprime la FICHA
	$xCred		= new cCredito($idsolicitud);
	$xCred->init();
	echo $xCred->getFicha();
	
	echo "
		<br>
	";
	//
	$sqli = "SELECT idcreditos_garantias, estatus_actual FROM creditos_garantias WHERE solicitud_garantia=$idsolicitud AND estatus_actual=2";
	$rsi = mysql_query($sqli);
		while($rwi = mysql_fetch_array($rsi)) {
			$idgar= $rwi[0];

		// Checa si la Garantia ya se entreg?
			$estatus = $rwi[1];
			if ($estatus == 3) {
				echo("<p class='aviso'>LA GARANTIA YA SE HA ENTREGADO</p></body></html>");
			}
			echo "<form name='myform$idgar' action='' method='post'>";
				minificha(4, $idgar);
			echo "
			<input type='hidden' name='idgar' value='$idgar'>
			</form>
			<input type='button' name='btend' value='GUARDAR / IMPRIMIR ENTREGA'onClick='frmEntrega(document.myform$idgar.idgar.value);'>
			<hr>
			";
		}
	@mysql_free_result($rsi);
?>
</body>
<script  >
	function frmEntrega(lavar) {
		var mivar = lavar;
			url = "../rptcreditos/rptentregagarantia.php?idg=" + mivar;
				miwin = window.open(url);
				miwin.focus();
	}
</script>
</html>
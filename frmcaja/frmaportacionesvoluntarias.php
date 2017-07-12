<?php
/**
 * @see : Modulo de Aportaciones Voluntarias
 * Actualizado: 13-Abril-2007
 * Responsable: Luis Balam
 * funcion: genera y Guarda Aportaciones Voluntarias segun los datos capturados
 *
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

	include_once "../core/core.deprecated.inc.php";
	include_once "../core/core.fechas.inc.php";
	include_once "../core/entidad.datos.php";
	include_once "../core/core.config.inc.php";
	include_once("../core/core.operaciones.inc.php");

	$eacp= EACP_CLAVE;
	
$xCaja		= new cCaja();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){
	header ("location:../404.php?i=200");
}
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Pago de Aportaciones Voluntarias a la Reserva</title>
</head>
<?php
jsbasic("frmpartessociales","1",".");
?>
<script  >
	function uptotal() {
		document.frmpartessociales.total.value = parseFloat(document.frmpartessociales.capital.value) + parseFloat(document.frmpartessociales.interes.value);
	}
</script>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
	<?php
	$idsocio 		= $_POST["idsocio"];
	$montoaport 	= $_POST["total"];
	if(!$idsocio or $montoaport<=0){
	?>
<fieldset>
<legend>| Pago de Aportaciones Voluntarias a la Reserva |</legend>
	<form name="frmpartessociales" action="frmaportacionesvoluntarias.php" method="post">
		<fieldset>
		<legend>| Datos del Aportante y Relacion|</legend>
		<table   width="95%"  >
		<tbody>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc();" class='mny' size='12' /><?php echo CTRL_GOSOCIO; ?></td>
			<td>Nombre del Socio</td>
			<td><input disabled name='nombresocio' type='text' value='' size="40"></td>
		</tr>
		<tr>
			<td>Cr&eacute;dito Relacionado:</td>
			<td><input type='text' name='idsolicitud' value='1' onchange="envsol();" onblur="envsol();" class='mny' size='12' /></td>
			<td>Descripci&oacute;n</td>
			<td><input disabled name='nombresolicitud' type='text' value='' size="40"></td>
		</tr>
		<tr>
			<td>Grupo Relacionado</td>
			<td><input type='text' name='idgrupo' value='1' onchange="envgpo();" class='mny' size='6' /></td>
			<td>Nombre Grupo</td>
			<td><input disabled name='nombregrupo' type='text' value='' size="40"></td>
		</tr>
		</tbody>
		</table>
		</fieldset>

		<fieldset>
		<legend>| Datos del Programa Origen |</legend>
		<table   width="95%"  >
		<tbody>
		<tr>
		<td>Programa Origen</td><td>
				<?php
						ctrl_select("socios_aportacionesorigen", "name='programaorigen'");
				?>
		</td>
		<td>A&ntilde;o de Informe</td>
		<td><input name='annoinforme' type='text' value='<?php echo date("Y"); ?>'></td>
		</tr>
		<tr>
			<td>Referencia de Min.</td><td>
			<input type='text' name='idministrado' value=''></td>
			<td>Numero / A&ntilde;o de Pago</td>
			<td><input type='text' name='idpago' value='<?php echo date("Y"); ?>'></td>
		</tr>
		</tbody>
		</table>
		</fieldset>


		<fieldset>
		<legend>||</legend>
		<table   width="55%"  >
		<tbody>
		<tr>
			<td>Otorgado Originalmente</td>
			<td><input type='text' name='montooriginal' value='0' class="mny" /></td>
		</tr>
		<tr>
			<th>Capital Recuperado</th>
			<td><input type='text' name='capital' value='0' onchange="uptotal();" class="mny" /></td>
		</tr>
		<tr>
			<td>Interes Recuperado</td>
			<td><input type='text' name='interes' value='0' onchange="uptotal();" class="mny" /></td>
		</tr>
		<tr>
			<td>Total Recuperado</td>
			<td><input type='text' name='total' value='0' onchange="uptotal();" class="mny" /></td>
		</tr>

		  <tr>
		<td>Recibo Fiscal</td><td><input type='text' name='foliofiscal' value='-' /></td>
	  </tr>
		<tr>
		<td>Tipo de Pago</td><td><?php echo ctrl_tipo_pago(); ?></td>
		</tr>
		<tr>
			<td>Observaciones</td><td colspan="1"><input name='detalles' type='text' value='' size="50" /></td>
		</tr>
		</tbody>
		</table>
		</fieldset>
		<input type="button" name="sendme" value="ENVIAR REGISTRO" onClick="frmpartessociales.submit();">
		<input type='hidden' name='cheque' value='' />
	</form>
	<p class='aviso'>AGREGUE UN NUMERO DE SOCIO Y ENVIELO</p>
</fieldset>
	<?php

	} else {


	$observaciones 		= $_POST["detalles"];

	$idsolicitud 		= $_POST["idsolicitud"];

	$idgrupo 			= $_POST["idgrupo"];
	$idprograma 		= $_POST["programaorigen"];
	$annoinforme 		= $_POST["annoinforme"];
	$capital 			= $_POST["capital"];
	$interes 			= $_POST["interes"];
	$idministrado 		= $_POST["idministrado"];
	$idpago 			= $_POST["idpago"];
	$montooriginal 		= $_POST["montooriginal"];
	$aportsocial 		= $montoaport * EACP_TASA_RESERVA ;
	$aportvol 			= $montoaport - $aportsocial;
	$cheque 			= $_POST["cheque"];
	$comopago 			= $_POST["ctipo_pago"];
	$foliofiscal 		= $_POST["foliofiscal"];
	$fecha				= fechasys();

	$cRec 				= new cReciboDeOperacion(5, false);
	//$cRec->setGenerarBancos();
	$cRec->setGenerarPoliza();
	$cRec->setGenerarTesoreria();

	$idrecibo			= $cRec->setNuevoRecibo($idsocio, DEFAULT_CREDITO, $fecha, 1, 5, $observaciones, $cheque, $comopago, $foliofiscal, $idgrupo);

	$cRec->setNuevoMvto($fecha, $aportvol, 702, 1, $observaciones, 1, TM_ABONO, $idsocio);
	$cRec->setNuevoMvto($fecha, $aportsocial, 710, 1, $observaciones, 1, TM_ABONO, $idsocio);


	$cRec->addMvtoContableByTipoDePago($totalcuotas, TM_CARGO);

	$cRec->setFinalizarRecibo(true);


	//PolizaPorRecibo($idrecibo);


		$cFicha = new cFicha(iDE_SOCIO, $idsocio);
		$cFicha->setTableWidth();
		$cFicha->show();

		echo $cRec->getFicha(true);


		echo $msg_rec_end;
		echo  $cRec->getJsPrint(true);
	}
	?>
</body>

</html>
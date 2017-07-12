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
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.operaciones.inc.php");

	require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
	$jxc = new TinyAjax();
$xCaja		= new cCaja();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){
	header ("location:../404.php?i=200");
}
	
function  calcula_ica($solicitud, $form){

	$sql = "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$solicitud";
	$mysolicitud = obten_filas($sql);
	$socio 		= $mysolicitud["numero_socio"];					// Numero de Socio
	$diasaut 	= $mysolicitud["dias_autorizados"];				// Dias Autorizados
	$montoMin 	= $mysolicitud["monto_autorizado"];				// Monto Autorizado
	$tasaInt	= $mysolicitud["tasa_interes"];					// Monto Autorizado
	$tipoConv 	= $mysolicitud["tipo_convenio"];				// Interes Diario */
	$ICA_pagado	= $mysolicitud["sdo_int_ant"];					// saldo ICA */
	$infoConv	= get_convenio($tipoConv);
	$tasaICA	= $infoConv["porcentaje_ica"];

	$intneto 	= 0;
	$inttotal 	= 0;
	$intpagado	= 0;
	$intdiario	= ($montoMin * $tasaInt) / EACP_DIAS_INTERES;
	/**
	 * Consulta todos los creditos que aplican Interes Anticipado
	 */

	$totaliva		= 0;
	$nota = "";

	$intneto		= $intdiario * $diasaut * $tasaICA;
			//Resta el Interes pagado
			$intneto		-= $ICA_pagado;
	/**
	 * @see Parche: Solo se Incluye la afectacion del Credito
	 */
	$nota .= "Tasa ICA $tasaICA; Dias $diasaut, Int. Diario" . getFMoney($intdiario) . ", ICA Pag. $ICA_pagado";
		//Valor IVA, obtenido del tipo de solicitud
		$tasaiva		= $infoConv["tasa_iva"];
		$iva_inc		= $infoConv["iva_incluido"];
			if($iva_inc ==1){

				$intneto 	= $intneto * (1 / (1 + $tasaiva));
				$totaliva	= $intneto * $tasaiva;

			} else {
				$totaliva	= $intneto * $tasaiva;
			}

			$intneto		= number_format($intneto, 2, '.', '');//getFMoney($intneto);
			$totaliva		= number_format($totaliva, 2, '.', '');//getFMoney($totaliva);

		$tab = new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("valorinteres", $intneto));
		$tab -> add(TabSetValue::getBehavior("valoriva", $totaliva));
		$tab -> add(TabSetValue::getBehavior("idobservaciones", $nota));
		$tab -> add(TabSetValue::getBehavior("idTasaIva", $tasaiva));
		return $tab -> getString();

}
	$jxc ->exportFunction('calcula_ica', array('solicitud', "frminteresant"));
	$jxc ->process();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Cobro de Interes Anticipado</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("frminteresant","1",".");
$jxc ->drawJavaScript(false, true);
?>
<body>
<fieldset>
<legend><script> document.write(document.title ); </script></legend>
<form name="frminteresant" action="frminteresanticipado.php?a=p" method="post">
<input type="hidden" id="idTasaIva" name="TasaIva" value="0" />
	<table   width="95%">
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='0' onchange="envsoc();" class='mny' size='12' />
			<?php echo CTRL_GOSOCIO; ?></td>
			<td colspan='2'><input name='nombresocio' type='text' disabled size="40"></td>
		</tr>
		<tr>
			<td>Numero de Solicitud</td>
			<td><input type='text' name='idsolicitud' value='0' onchange="envsol(); calcula_ica();
				" id="solicitud" class='mny' size='12' />
			<?php echo CTRL_GOCREDIT; ?></td>
			<td colspan='2'><input name='nombresolicitud' type='text' disabled value='' size="40"></td>
		</tr>
	  <tr>
			<td>Recibo Fiscal</td>
			<td><input type='text' name='foliofiscal' value='-'></td>
	</tr>
		<tr>
		<td>Tipo de Pago</td>
		<td><?php echo ctrl_tipo_pago(); ?></td>
		<td>Numero de Cheque</td>
		<td><input type='text' name='cheque' value=''></td>
	</tr>

	<tr>
		<td>Monto del Interes</td>
		<td><input type='text' name='montointeres' value='0' onfocus="getJsIca();"  id="valorinteres" class="mny" /></td>
	</tr>
	<tr>
		<td>Monto del I.V.A</td>
		<td><input type='text' name='montoiva' value='0' onchange="getIvaICA();" onblur="getTotalIca();" onfocus="getIvaICA();" id="valoriva" class="mny" /></td>
	</tr>
	<tr>
		<td>Monto a Pagar</td>
		<td><input type='text' name='montototal' value='0' onchange="getJsIca();" onfocus="getTotalIca();" class="mny" /></td>
	</tr>
	<tr>
		<td>Observaciones</td>
		<td colspan='3'><input name='observaciones' type='text' value='' size="50" maxlength="60" id="idobservaciones" /></td>
	</tr>
	<tr>
		<th colspan="4"><a onClick="frmSubmit();" class="boton">ENVIAR / GUARDAR DATOS</a></th>
	</tr>
	</table>
</form>
<p class='aviso'>EL INTERES SE GENERA AUTOMATICAMENTE , SEGUN SOLICITUD DE CREDITO DADA</p>
</fieldset>
<?php
	$action			= $_GET["a"];
	$idsolicitud		= $_POST["idsolicitud"];
	$monto 			= $_POST["montointeres"];

	if ($action == "p" and isset($idsolicitud) and isset($monto) and ($monto>0)) {

	$observaciones 		= $_POST["observaciones"];
	$iva			= $_POST["montoiva"];
	$comopago		= $_POST["ctipo_pago"];
	$cheque			= $_POST["cheque"];
	$foliofiscal 		= $_POST["foliofiscal"];
	$idsocio		= $_POST["idsocio"];
	$fecha			= fechasys();
	$iddocto		= $idsolicitud;
	
//===================================================================================================
	$cRec	= new cReciboDeOperacion(15, true, false);
	$cRec->setGenerarBancos();
	$cRec->setGenerarPoliza();
	$cRec->setGenerarTesoreria();

	$idrecibo = $cRec->setNuevoRecibo($idsocio, $iddocto, $fecha, 1, 15, $observaciones, $cheque, $comopago, $foliofiscal);


	$cRec->setNuevoMvto($fecha, $monto, 351, 1, $observaciones, 1, TM_ABONO, $idsocio);
	//IVA
	$cRec->setNuevoMvto($fecha, $iva, 151, 1, $observaciones, 1, TM_ABONO, $idsocio);
	
	$cRec->addMvtoContableByTipoDePago($montooperacion, TM_CARGO);

	$cRec->setFinalizarRecibo(true);

		$cfSocio = new cFicha(iDE_SOCIO, $idsocio);
		$cfSocio->setTableWidth();
		$cfSocio->show();
		echo $cRec->getFicha();
		echo $msg_rec_end;
		
		// actualiza el interes Anticipado del Credito
		$sqlica = "UPDATE creditos_solicitud
		SET sdo_int_ant=(sdo_int_ant + ($monto))
		WHERE numero_solicitud=$idsolicitud";
		my_query($sqlica);

		//
	}
?>
</body>
<script  >
var wFrm = document.frminteresant;
	function printrec() {
		var elUrl= "../frmextras/frmrecibodepago.php?recibo=<?php echo $idrecibo; ?>";
		rptrecibo = window.open(elUrl, "");
		rptrecibo.focus();
	}
	function getJsIca(){
		calcula_ica();
		setTimeout("getTotalIca()", 500);
	}
	function getTotalIca(){
		var sMonto	= parseFloat(wFrm.montointeres.value) + parseFloat(wFrm.montoiva.value);
		var aMonto 	= new String(sMonto + ".00");
		var vMonto	= aMonto.split(".");
		var nMonto	= vMonto[0] + "." + vMonto[1].substr(0,2);
		wFrm.montototal.value = nMonto;
	}
	function getIvaICA(){
		var Base 	= parseFloat(wFrm.montointeres.value);
		var Tasa	= parseFloat(wFrm.TasaIva.value);

		wFrm.montoiva.value = Base * Tasa;
	}
</script>
</html>

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

	include_once "../core/core.deprecated.inc.php";
	include_once "../core/core.fechas.inc.php";
	include_once "../core/entidad.datos.php";
	include_once "../core/core.config.inc.php";
	include_once "../core/core.common.inc.php";
	include_once "../core/core.operaciones.inc.php";
	$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>PLANEACION DE CREDITOS.- GRUPOS SOLIDARIOS</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
<fieldset>
<legend>PLANEACION DE CREDITOS.- GRUPOS SOLIDARIOS</legend>
<?php
$orden 					= $_POST["ordencompleta"];
$idrecibo 				= $_POST["idrecibo"];
$idgrupo				= $_GET["grupo"];

$sucess					= true;
$msg					= "";

	if ( !isset($orden) ) {
		$sucess	= false;
	}
	if ( !isset($idrecibo) ) {
		$sucess	= false;
	}
	if ( !isset($idgrupo) ) {
		$sucess	= false;
	}
	$orden 		= trim($orden);

	$xG	= new cGrupo($idgrupo);
	$presidenta = $xG->getRepresentanteCodigo();
	$DCredito	= $xG->getDatosDelCreditoGrupalInArray();
	$xP			= $xG->getDatosDePlaneacionInArray();

	echo $xG->getFicha(true);

	if ( isset($xP["idoperaciones_recibos"]) ){
		$msg	.= "\tLa Planeacion Existe con el Numero " . $xP["idoperaciones_recibos"] ."\r\n";

		//si el credito fue autorizado, mministrado o cualquier otro estatus
		if ($DCredito["estatus_actual"] != 99){
			$msg	.= "\tEl Credito tiene Estatus de " . $DCredito["estatus_actual"] . ", Diferente al Aceptable\r\n";
			//Obtiene Datos de la Planeacion
			$sucess	= false;
			//Si el registros sucede entoces se sale de la programacion
		} else {
			$msg	.= "\tEl Estatus Es Aceptable\r\n";
			$sucess	= true;
		}
	} else {
		$msg	.= "\tLa Planeacion de un Credito con menos de " . DIAS_ESPERA_CREDITO . " Dias no Existe\r\n";
		$sucess	= true;
	}

	if ($sucess == true ){
		$PlanAnterior =  $xP["idoperaciones_recibos"];
	//Eliminar la Planeacion Anterior
		if ( isset($PlanAnterior) ){
			$DRec	= new cReciboDeOperacion(14, false, $PlanAnterior);
			$DRec->setNuevoRecibo($PlanAnterior);
			$DRec->setRevertir();
			$msg	.= $DRec->getMessages();
		}
	//--------------------------------------- DATOS DEL RECIBO -----------------------------------------------
									// DATOS GENERALES
	$fechaop = fechasys();			// fecha de la Operacion y el recibo.
	/* *******************************************************************************************************
                                Agrega el Recibo..- VALIDO SOLO PARA PLANEACION DE CREDITO
    **************************************************************************************************** */
	$msqlf 	= "idoperaciones_recibos, fecha_operacion, numero_socio, docto_afectado, tipo_docto, total_operacion, observacion_recibo, tipo_pago, grupo_asociado, idusuario";
	$msqlv 	= "$idrecibo, '$fechaop', $presidenta, 1, 14, 0, 'PLANEACION DE CREDITO HECHA POR $oficial', 'ninguno', $idgrupo, $iduser";
	$sqlrec = "INSERT INTO operaciones_recibos($msqlf) VALUES ($msqlv)";


	// --------------------------------------- VALOR SQL DEL MVTO.-------------------------------------------------------
			// VALORES FIJOS
	$smf	= "idoperaciones_mvtos, fecha_operacion, fecha_afectacion, recibo_afectado, socio_afectado, docto_afectado, ";
	$smf 	.= "tipo_operacion, afectacion_real, afectacion_cobranza, afectacion_contable, ";
	$smf 	.= "valor_afectacion, fecha_vcto, estatus_mvto, codigo_eacp, periodo_socio, ";
	$smf 	.= "periodo_contable, periodo_cobranza, periodo_seguimiento, periodo_mensual, periodo_semanal, periodo_anual, saldo_anterior, saldo_actual, detalles, idusuario, afectacion_estadistica, ";
	$smf 	.= "docto_neutralizador, tasa_asociada, dias_asociados, grupo_asociado";
	// "idusuario, codigo_eacp, socio_afectado, docto_afectado, recibo_afectado, fecha_operacion, ";
	// --------------------------------------------- AGREGA EL RECIBO  GRABA EL FOLIO
		my_query($sqlrec);		// GRABA EL RECIBO

		//obtienes un Valor Invertable puro

		$orden = strstr($orden, "(");

		$orden = stripslashes($orden);
		//TODO: Grupos Solidarios. Migrar esta parte del proceso
		$sdm = "INSERT INTO operaciones_mvtos($smf) VALUES $orden";

		// AGREGA EL MOVIMIENTO Y GRABA EL FOLIO
		my_query($sdm);

		// Para Imprimir el Recibo
		echo $msg_rec_end;
	} else {
		echo "<p class='aviso'>Algo Paso al Guardar la Planeacion de Credito</p>";

	}
	echo $msg;
?>
</fieldset>
</body>
<script  >
	function printrec() {
		var elUrl= "../rpt_formatos/rptplaneacioncredito.php?plan=<?php echo $idrecibo; ?>";
		rptrecibo = window.open( elUrl, "window");
		rptrecibo.focus(); // */
	}
</script>
</html>
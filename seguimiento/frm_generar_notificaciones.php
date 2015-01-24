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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";

$oficial = elusuario($iduser);
//funciones de ajax que muestran para generar notificaciones
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>GENERAR NOTIFICACIONES DE COBRO</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>
<hr />
	<p class="frmTitle"><script  > document.write(document.title); </script></p>
<hr />

<form name="frmnotif" method="post" action="./frm_generar_notificaciones.php?a=1">
	<table border='0'    >
		<tr>
			<td>Tipo de Notificaciones</td>
			<td><select name="tipo">
				<option value="pagos_frecuentes" selected>Planes de Pagos</option>
				<option value="pagos_a_final_de_plazo">A Final de Plazo</option>
				<option value="avisos_preventivos_a_final_de_plazo">Aviso de Cobro Preventivo de Creditos a Final de Plazo</option>
			</select></td>
		</tr>
	</table>
	<input type="button" value="Generar/Actualizar Notificaciones" onclick="document.frmnotif.submit(); " />
</form>
<?php
$tipo_a_extraer = $_POST["tipo"];
$sucursal		= getSucursal();
$action			= $_GET["a"];
if(isset($tipo_a_extraer) and ($action= "1")){
	/**
	 * Obtiene la Letra por
	 */
/** ***************************************************************************************************************************
 * NOTFICACIONES PARA PLANES DE PAGO
 ***************************************************************************************************************************  */
if ($tipo_a_extraer == "pagos_frecuentes"){
//seleccionar Letras no pagadas en tiempo y forma
$sql = "SELECT
			numero_socio,
			numero_solicitud,
			fecha_ultimo_mvto,
			periocidad_de_pago,
			saldo_actual,
			ROUND(((DATEDIFF(CURDATE(),fecha_ultimo_mvto))/ periocidad_de_pago)) AS 'letras_vencidas',
			( (DATEDIFF(CURDATE(), DATE_ADD(fecha_ultimo_mvto, INTERVAL 1 DAY) ) ) ) AS 'dias',
			tasa_interes,
			tasa_moratorio,
			dias_autorizados,
			interes_diario,
			(monto_autorizado / periocidad_de_pago) AS 'letra'
		FROM creditos_solicitud

		WHERE saldo_actual>0 AND periocidad_de_pago!=360
			AND estatus_actual!=50
			AND sucursal='$sucursal'
			HAVING dias > periocidad_de_pago
			ORDER BY fecha_ultimo_mvto ";
//echo $sql;
$rsNot = mysql_query($sql, cnnGeneral());
//Abre una Bitacora


		$LFile	= "bitacora_de_notificaciones" . date("YmdHsi") . "_" . $iduser;
		$PFile	= PATH_TMP . $LFile . ".txt";
		$RFile	= fopen($PFile, "a+");

$msg	= "========================NOTIFICACIONES DE PLANES=========================\r\n";
while($rw = mysql_fetch_array($rsNot)){
	$socio 			= $rw["numero_socio"];					//numero de socio
	$solicitud 		= $rw["numero_solicitud"];
	$domicilio 		= domicilio($socio);					// Domicilio
	$base 			= $rw["saldo_actual"];					// Base de Calculo
	$tasa 			= $rw["tasa_interes"];					//
	$tasa_m 		= $rw["tasa_interes"] + $rw["tasa_moratorio"];	// Interes Rem Moratorio + Int Normal = 48% generalmente
	$periodos 		= floor($rw["letras_vencidas"]);		// redondea la letra
	$mletra 		= $rw["letra"];							// Monto de la Letra
	$frecuencia 	= $rw["frecuencia_de_pago"];								// Periocidad
	$interes 		= $frecuencia * $rw["interes_diario"];	// periocidad * interes_diario

	// (Abono + Interes) * tasa_moratorio * periocidad_de_pago
	$interes_moratorio 		= ((($mletra + $interes) * $tasa_m) * $frecuencia) / EACP_DIAS_INTERES;
	$capital 				= $rw["saldo_actual"];				// Saldo del capital
	$otros_cargos			= 0;				//
	//

	//
	$total = $mletra + $interes + $interes_moratorio + $otros_cargos;
	//Obtiene el Telefono del Socio

	$ddomicilio =getDatosDomicilio($socio);

	$telefono1 = $ddomicilio[10];
	$telefono2 = $ddomicilio[11];

	$hora_default = date("H:i");
	$fecha_default = fechasys();
	$observaciones = "Generado el $fecha_default a las $hora_default Hrs. por $oficial";


	switch($periodos){
		case 1:
			$msg .= date("H:s:i") . "\t\t El Socio $socio con credito $solicitud tiene solo $periodos vencidos, y se omite cualquier accion \t\n";

		case 2:
			$sql_llamada = "INSERT INTO seguimiento_llamadas(numero_socio, numero_solicitud, deuda_total,
										telefono_uno, telefono_dos, fecha_llamada, hora_llamada, observaciones, estatus_llamada)
								VALUES
									($socio, $solicitud, $total, '$telefono1', '$telefono2', '$fecha_default',
									'$hora_default', '$observaciones', 'pendiente')";
			$x	= my_query($sql_llamada);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t No se registro la Llamada al socio $socio por el Credito $solicitud \t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t Se Agrego una Llamada al socio $socio por el Credito $solicitud \t\n";
			}
			break;

		// LETRA 3 ------ Primera Notificacion
		case 3:
			//Buscar Compromisos
			/**
			 * Si el Compromiso existe, checar si esta vencido
			 *
			 */

			$q = my_query($sql_uproc);
			//Generar la Notificacion si existe una Llamada sin compromiso
			if ($q["stat"]  != false){
				$msg .= date("H:s:i") . "\t\t" . $q[SYS_MSG] . "\t\n";
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, domicilio_completo)
								VALUES
								($socio, $solicitud, 1, '$fecha_default', $iduser, '$fecha_venc_notif', 'segunda_notificacion',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', '$domicilio')
								";
					$x = my_query($sql_notif);
					if($x["stat"] == false ){
						$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
					} else {
						$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
					}


			}
		break;

		// LETRA 4 ----- Segunda Notificacion
		case 4:
			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=1";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=1";

			$rs = mysql_query($sql_uproc);
				$afected_row = mysql_affected_rows($rs);
			@mysql_free_result($rs);

			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row >0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, domicilio_completo)
								VALUES
								($socio, $solicitud, 2, '$fecha_default', $iduser, '$fecha_venc_notif', 'tercera_notificacion',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', '$domicilio')";
					$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}

				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, domicilio_completo)
				VALUES
				($socio, $solicitud, 2, '$fecha_default', $iduser, '$fecha_venc_notif', 'tercera_notificacion',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', '$domicilio')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}

			}
		break;

		// LETRA 5 ------ REQUERIMIENTO EXTRAJUDICIAL
		case 5:
			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=2";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=2";

			$rs = mysql_query($sql_uproc);
			$afected_row = mysql_affected_rows($rs);
			@mysql_free_result($rs);

			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row>0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, domicilio_completo)
								VALUES
								($socio, $solicitud, 2, '$fecha_default', $iduser, '$fecha_venc_notif', 'requerimiento_extrajudicial',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente''$domicilio')";
					$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}

				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion)
				VALUES
				($socio, $solicitud, 2, '$fecha_default', $iduser, '$fecha_venc_notif', 'requerimiento_extrajudicial',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}

			}
		break;

		//CITATORIO EXTRAJUDICIAL
		case 6:
		$nant = 3;
		$nact = 4;
		$titact = "citatorio_extrajudicial";

			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=$nant";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=$nant";

			$rs = mysql_query($sql_uproc);
			$afected_row = mysql_affected_rows($rs);
			@mysql_free_result($rs);

			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row>0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, domicilio_completo)
								VALUES
								($socio, $solicitud, $nact, '$fecha_default', $iduser, '$fecha_venc_notif', '$titact',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente''$domicilio')";
					$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}

				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion)
				VALUES
				($socio, $solicitud, $nact, '$fecha_default', $iduser, '$fecha_venc_notif', '$titact',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}

			}
			break;

	}


}
fwrite($RFile, $msg);
@mysql_free_result($rsNot);
/** *****************************************************************************************************************************************************
 * NOTFICACIONES PARA AUTOMATIZADOS
 ****************************************************************************************************************************************************  */
} elseif($tipo_a_extraer == "pagos_a_final_de_plazo") {
	$msg	= "==========================NOTIFICACIONES AUTOMATIZADAS=========================\n";
	//seleccionar Letras no pagadas en tiempo y forma
$sql = " select * from creditos_solicitud
		where periocidad_de_pago=360
		AND saldo_actual>0 AND fecha_vencimiento<CURDATE()
		AND estatus_actual!=50
		AND sucursal = '$sucursal'
		ORDER BY fecha_vencimiento";
$rsPic = mysql_query($sql);

while($rw = mysql_fetch_array($rsPic)){
	$socio = $rw[5];				//numero de socio
	$solicitud = $rw[0];
	$domicilio = domicilio($socio);	// Domicilio

	$saldo 				= $rw[22];
	$f_ultimo_mvto 		= $rw[23];
	$f_vencimiento 		= $rw[15];
	$tasan 				= $rw[9];
	$tasam 				= $rw[30];
	$capital 			= $saldo;
	$mletra 			= $saldo;
	$dias_vigente 		= 0;
	$dias_vencidos 		= 0;
	$interes 			= 0;
	$interes_moratorio 	= 0;
	$otros_cargos 		= 0;


	//Cuando el Ultimo Mvto es mayor a la fecha de Vcto
	if ($f_ultimo_mvto < $f_vencimiento){
		$dias_vigente = restarfechas($f_ultimo_mvto, $f_vencimiento);
		$dias_vencidos = restarfechas($f_vencimiento, fechasys());
	} else {
		$dias_vencidos = restarfechas($f_ultimo_mvto, fechasys());
	}

	$interes = (($capital * $dias_vigente) * $tasan) / EACP_DIAS_INTERES;
	$interes_moratorio = (($capital * $dias_vencidos) * ($tasan + $tasam)) / EACP_DIAS_INTERES;


	//
	$total = $saldo + $interes + $interes_moratorio + $otros_cargos;
	//Obtiene el Telefono del Socio
	$sql_150 = "SELECT * FROM socios_vivienda WHERE socio_numero=$socio AND principal='1'";
	$ddomicilio = obten_filas($sql_150);
	$telefono1 = $ddomicilio[10];
	$telefono2 = $ddomicilio[11];

	$hora_default = date("H:i");
	$fecha_default = fechasys();
	$observaciones = "Generado el $fecha_default a las $hora_default Hrs. por $oficial";


if($dias_vencidos > 1 && $dias_vencidos <= 15){
//Primera Notificacion
$tnotif = "segunda_notificacion";
$idnant = 1;
$idnnew = 2;
			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=$idnant";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=$idnant";

			$rsUc = mysql_query($sql_uproc);
			$afected_row = mysql_affected_rows($rsUc);
			@mysql_free_result($rsUc);



			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row >0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
								VALUES
								($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}

				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
				VALUES
				($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
			}
} elseif($dias_vencidos > 15 && $dias_vencidos <= 30){
//Segunda Notificacion
$tnotif = "tercera_notificacion";
$idnant = 2;
$idnnew = 3;
			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=$idnant";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=$idnant";

			$rs = mysql_query($sql_uproc);
			$afected_row = mysql_affected_rows($rs);
			@mysql_free_result($rs);



			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row >0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
								VALUES
								($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
					$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
				VALUES
				($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
			}
} elseif($dias_vencidos > 30 && $dias_vencidos <= 40){
//Tercera Notificacion
$tnotif = "tercera_notificacion";
$idnant = 3;
$idnnew = 4;
			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=$idnant";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=$idnant";

			$rs = mysql_query($sql_uproc);
			$afected_row = mysql_affected_rows($rs);
			@mysql_free_result($rs);



			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row >0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
								VALUES
								($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
					$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
				VALUES
				($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
			}
} elseif($dias_vencidos > 40 && $dias_vencidos <= 50){
//Req. Extrajudicial
$tnotif = "requerimiento_extrajudicial";
$idnant = 4;
$idnnew = 5;
			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=$idnant";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=$idnant";

			$rs = mysql_query($sql_uproc);
			$afected_row = mysql_affected_rows($rs);
			@mysql_free_result($rs);



			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row >0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
								VALUES
								($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
					$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
				VALUES
				($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
				my_query($sql_notif);
			}
} elseif($dias_vencidos > 50 && $dias_vencidos <= 60){
//Cit. Extrajudicial
$tnotif = "citatorio_extrajudicial";
$idnant = 5;
$idnnew = 6;
			$sql_hay = "SELECT COUNT('idseguimiento_notificaciones') AS 'siexiste' FROM seguimiento_notificaciones WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND numero_notificacion=$idnant";
			$exists = mifila($sql_hay, "siexiste");

			//si existe llamada, dar seguimiento
			if ($exists > 0){
			//Cancelar las Llamadas ya hechas, pero sin compromisos
			$sql_uproc = "UPDATE seguimiento_notificacion SET estatus_notificacion='vencido' WHERE socio_notificado=$socio
			AND numero_solicitud=$solicitud AND estatus_notificacion='efectuado' AND numero_notificacion=$idnant";

			$rs = mysql_query($sql_uproc);
			$afected_row = mysql_affected_rows($rs);
			@mysql_free_result($rs);



			//Generar la Notificacion si existe una Llamada sin compromiso
				if ($afected_row >0){
					$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
					$sql_notif = "INSERT INTO seguimiento_notificaciones
								(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
								procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
								VALUES
								($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
								$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
					$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
				}
			} else {

			//Generar la Notificacion
				$fecha_venc_notif = sumardias($fecha_default, $dias_para_notificar);
				$sql_notif = "INSERT INTO seguimiento_notificaciones
				(socio_notificado, numero_solicitud, numero_notificacion, fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento,
				procedimiento_proximo, capital, interes, moratorio, otros_cargos, total, observaciones, estatus_notificacion, tipo_credito, domicilio_completo)
				VALUES
				($socio, $solicitud, $idnnew, '$fecha_default', $iduser, '$fecha_venc_notif', '$tnotif',
				$mletra, $interes, $interes_moratorio, $otros_cargos, $total, '$observaciones', 'pendiente', 'automatizado', '$domicilio')";
				$x = my_query($sql_notif);
			if($x["stat"] == false ){
				$msg .= date("H:s:i") . "\t\t" . $x[SYS_MSG] . "\t\n";
			} else {
				$msg .= date("H:s:i") . "\t\t" . $x["info"] . "\t\n";
			}
			}
} elseif($dias_vencidos > 60) {
//Inicio Proc. Extrajudicial
}

} //end while
	fwrite($RFile, $msg);
@mysql_free_result($rsPic);

/** ***************************************************************************************************************************
 * AVISOS DE PAGOS PARA AUTOMATIZADOS
 ***************************************************************************************************************************  */
}
	fclose($RFile);

}	//END IF MATRIX
?>
</body>
</html>

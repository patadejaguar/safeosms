<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package common
 *  Actualizacion
 * 		16/04/2008
 *		2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimeintos y recibos
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.captacion.inc.php");

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);


$action 			= isset( $_GET["o"] ) ? $_GET["o"] : false;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Inversiones a Plazo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
//Si la Operacion es Configurar los Datos
if ( $action == false ){
?>
<form name="frmSendFiles" method="POST" action="inversiones.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga Automatica de Inversiones a Plazo&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO DE INVERSIONES</th>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
<?php

} elseif ( $action ==  "u" ) {

?>
<form name="frmConvs" method="POST" action="inversiones.upload.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
<?php

		$usrFiles	= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$msg			= "";


		$prePath		= PATH_BACKUPS;
		$lim			= 1; //sizeof($usrFiles) -1;

		for($i=0; $i<=$lim; $i++){
			if(isset($usrFiles[$i])==true){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[$i]['name'], -6));
				$mExt	= $DExt[1];

				if($mExt == "csv"){
					$completePath	= $prePath . $usrFiles[$i]['name'];
					if(file_exists($completePath)==true){
						unlink($completePath);
						echo "<p class='aviso'> SE ELIMINO EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					}
					if(move_uploaded_file($usrFiles[$i]['tmp_name'], $completePath )) {
						//echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					} else {
						//echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
					}
					//analizar el Archivo
						$gestor = @fopen($completePath, "r");

						$iReg 	= 0;
						$cT		= new cTipos();
						//inicializa el LOG del proceso
						$aliasFil	= getSucursal() . "-carga-batch-de-inversiones-" . fechasys();
						$xLog		= new cFileLog($aliasFil, true);
						if ($gestor) {
							while (!feof($gestor)) {
								$bufer			= fgets($gestor, 4096);
								//$bufer			= stream_get_line($gestor, "\r\n");
								if (!isset($bufer) ){
									$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
								} else {
									$bufer			= trim($bufer);
									$datos			= explode(",", $bufer, 6);
									$socio			= $cT->cInt($datos[0]);
									$importe		= $cT->cFloat($datos[1]);
									$fechaApertura	= $cT->cFecha($datos[2]);
									$plazo			= $cT->cInt($datos[3]);
									$tasa			= $cT->cFloat($datos[4]);
									$observaciones	= $cT->cChar($datos[5]);
									$ClaveCuenta	= false;
									//Iniciar el Socio
									//$msg .= "$iReg\t$socio\tERROR_SOCIO\t$socio -- $importe -- $fechaApertura -- $plazo -- $tasa -- $observaciones\r\n";
									$xCL			= new cCajaLocal(99);
									if ( $xCL->getExistenciaSocio($socio) <= 0 ){
										$msg .= "$iReg\t$socio\tERROR_SOCIO\tLa Linea($iReg) no se leyo por que no existe el socio($bufer)\r\n";
									} else {

										$xInv	= new cCuentaInversionPlazoFijo(false, $socio, $plazo, $tasa, $fechaApertura);
										//Agrega la Cuenta
										$ClaveCuenta = $xInv->setNuevaCuenta(99, 2, $socio, $observaciones, DEFAULT_CREDITO, "", "", DEFAULT_GRUPO,
																			$fechaApertura, CAPTACION_TIPO_PLAZO, 99, $plazo, $tasa);
										if ($ClaveCuenta != false){
											$msg .= "$iReg\t$socio\tCUENTA\tSe Agrego Exitosamente la cuenta $ClaveCuenta\r\n";
											if ( $importe > 0 ){
												$xInv->init();
												$xInv->setFechaDeOperacion($fechaApertura);
												$xInv->setDiasInvertidos($plazo);
												
												$RDeposito				= $xInv->setDeposito($importe, DEFAULT_CHEQUE, DEFAULT_TIPO_PAGO, DEFAULT_RECIBO_FISCAL, $observaciones,
																				DEFAULT_GRUPO, $fechaApertura);
												$msg 					.= "$iReg\t$socio\tRECIBO_DEPOSITO\tSe Efectua un Deposito de $importe al Recibo $RDeposito\r\n";
												$xInv->init();
												$recibo_de_reinversion 	= $xInv->setReinversion($fechaApertura, true, $tasa, $plazo);
												$msg 					.= "$iReg\t$socio\tRECIBO_INVERSION\tSe Efectua una Inversion de $importe al Recibo $recibo_de_reinversion\r\n";
												$msg					.= $xInv->getMessages("txt");
											}
										} else {
											$msg .= "$iReg\t$socio\tERROR\tSe Fallo al Agregar la Cuenta\r\n";
										}
									}
								}
							$iReg++;
							}
						}
						fclose ($gestor);
						$xLog->setWrite($msg);
						echo $xLog->getLinkDownload("Archivo del proceso");
				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
				}
			}
		}
?>
</fieldset>
</form>
<?php
}
if ( !isset($iReg) ){
	$iReg	= 0;
}
?>

</body>
<script  >
</script>
</html>

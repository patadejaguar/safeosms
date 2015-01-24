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
include_once("../core/core.creditos.inc.php");

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);


$action 			= $_GET["o"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Prestamos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
//Si la Operacion es Configurar los Datos
if ( !isset($action) ){
?>
<form name="frmSendFiles" method="POST" action="prestamos.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga batch de Prestamos&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO DE PRESTAMOS</th>
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
echo '<form name="frmConvs" method="POST" action="prestamos.upload.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend> ';
		setFoliosAlMaximo();
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
						$aliasFil	= getSucursal() . "-carga -batch-de-creditos-" . fechasys();
						$xLog		= new cFileLog($aliasFil, true);
						if ($gestor) {
							while (!feof($gestor)) {
								$bufer			= fgets($gestor, 4096);
								//$bufer			= stream_get_line($gestor, "\r\n");
								if (!isset($bufer) ){
									$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
								} else {
									$bufer				= trim($bufer);
									$datos				= explode(",", $bufer, 12);
									$socio				= $cT->cInt($datos[0]);
									$credito			= $cT->cInt($datos[1]);
									$producto			= $cT->cInt($datos[2]);
									$monto				= $cT->cFloat($datos[3]);
									
									$ministracion			= $cT->cFecha($datos[4]);
									$vencimiento			= $cT->cFecha($datos[5]);
									$pagos				= $cT->cInt($datos[6]);
									$periocidad			= $cT->cInt($datos[7]);
									$saldo				= $cT->cFloat($datos[8]);
									$UltimaOperacion		= $cT->cFecha($datos[9]);
									$ContratoCorriente		= $cT->cInt($datos[10]);
									if($socio == 0){
										$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
									} else {
										$xCred				= new cCredito();
										//Crear Contrato corriente si el producto tiene ahorro
										$DConv				= $xCred->getDatosDeProducto($producto);
										$tasaAhorro			= $cT->cFloat( $DConv["tasa_ahorro"] );
										if($ContratoCorriente == 0 AND $tasaAhorro > 0){
											$xCapta				= new cCuentaALaVista(false);
											$ContratoCorriente	= $xCapta->setNuevaCuenta(99, DEFAULT_SUBPRODUCTO_CAPTACION, $socio, "CUENTA POR IMPORTACION", $credito);
											$msg .= "$iReg\t$socio\t$credito\tAgregando una Cuenta Corriente $ContratoCorriente NUEVO\r\n";
										}
										//Agregar
										$ok	 = $xCred->add($producto, $socio, $ContratoCorriente, $monto, $periocidad, $pagos, 0, CREDITO_DEFAULT_DESTINO, $credito,
										DEFAULT_GRUPO, "", "CREDITO IMPORTADO #$iReg", DEFAULT_USER, $ministracion);
										if($ok == true){
											///Inicializar
											//autorizar
											$xCred->setAutorizado($monto, $pagos, $periocidad, CREDITO_TIPO_AUTORIZACION_AUTOMATICA, $ministracion,
											"CREDITO IMPORTADO #$iReg", false, $ministracion,2, false, 
											$vencimiento, CREDITO_ESTADO_AUTORIZADO, $monto, 0, $UltimaOperacion);
											$xCred->init();
											//ministrar
											$xCred->setForceMinistracion();
											$xCred->setMinistrar(DEFAULT_RECIBO_FISCAL, DEFAULT_CHEQUE, $monto, DEFAULT_CUENTA_BANCARIA, 0, DEFAULT_CUENTA_BANCARIA, "CREDITO IMPORTADO #$iReg", $ministracion);
											
											if( $monto > $saldo ){
												$abono	= ($monto - $saldo);
												$msg .= "$iReg\t$socio\t$credito\tAgregando un Abono por $abono por el Saldo $saldo del Monto $monto\r\n";
												$xCred->setAbonoCapital($abono, 1, DEFAULT_CHEQUE, DEFAULT_RECIBO_FISCAL,
												"CREDITO IMPORTADO #$iReg", DEFAULT_GRUPO, $UltimaOperacion);
											}
										} else {
											$msg .= "$iReg\t$socio\t$credito\tEL Credito no se pudo agregar\r\n";
										}
										$msg		.= $xCred->getMessages("txt");
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
echo "</fieldset>
</form> ";
}
if ( !isset($iReg) ){
	$iReg	= 0;

}
?>

</body>
<script  >
</script>
</html>

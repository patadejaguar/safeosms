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

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);

//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$action 			= $_GET["o"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Archivos CompacW para Comparacion [V 1.12]-<?php echo getSucursal(); ?></title>
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
<form name="frmSendFiles" method="POST" action="compacw.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga automatica de archivos para comparar con CompacW&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="1">ARCHIVO COMPACW A ENVIAR</th>
			<td colspan="1"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th>Tipo de Archivo</th>
			<td><select name="tArchivo">
				<option value="ahorro">De Captacion(Ahorros)</option>
				<option value="credito">De Colocacion(Creditos)</option>
			</select></td>
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
<form name="frmConvs" method="POST" action="convenios_factura_rapida.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
<?php

		$usrFiles			= array();
		$usrFiles[0]		= $_FILES["cFile1"];
		$msg				= "";

		$TipoDeImportacion	= $_POST["tArchivo"];
		$SAhorro			= 0;
		$SCapital			= 0;
		$SInteres			= 0;
		$SIva				= 0;
		$STotal				= 0;
		$diferencias		= 0;

		$arrEstatus		= array(
						"1-3-01" => 10,
						"1-3-03" => 20,
						"2-1-01" => 99
						);
		$arrTCuenta		= array (
						"2-1-01-01-01" => 1,
						"2-1-01-01-02" => 2
						);
		$prePath		= PATH_BACKUPS;
		$lim			= 1;
		for($i=0; $i<=$lim; $i++){
			if(isset($usrFiles[$i])==true){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[$i]['name'],-6));
				$mExt	= trim($DExt[1]);

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
						$xT = new cTipos;

						$iReg = 0;
						$cT	= new cTipos();
						if ($gestor) {
						//eliminar los datos diferentes a la actual sucursal
						if ($TipoDeImportacion == "credito"){
							$sqlD	= "DELETE FROM sisbancs_temp_creditos  ";
							my_query($sqlD);
						} else {
							$sqlD	= "DELETE FROM sisbancs_temp_depositos ";
							my_query($sqlD);
						}
							while (!feof($gestor)) {
								$bufer			= fgets($gestor, 4096);
								//$bufer			= stream_get_line($gestor, "\r\n");
								if (!isset($bufer) ){
									$msg .= "La Linea($iReg) no se leyo($bufer)\r\n";
								} else {

								$datos		= explode(",", trim($bufer), 3);
								$cuenta		= trim($datos[0]);
								$nombre		= strtoupper( trim($datos[1]) );
								$estatus	= $arrEstatus[ substr($cuenta, 0, 6) ];
								$TCuenta	= ( isset($arrTCuenta[ substr($cuenta, 0, 12) ] ) ) ? $arrTCuenta[ substr($cuenta, 0, 12) ] : 0;
								$monto		= $cT->cFloat( trim($datos[2]) );
								$modalidad	= 1;

								$aDCuenta	= explode("-", $cuenta);
								$socio		= $cT->cInt($aDCuenta[5]);
								$sucursal	= mifila("SELECT sucursal FROM socios_general WHERE codigo = $socio LIMIT 0,1 ", "sucursal");
									if ($sucursal != getSucursal() ){
										$sucursal	= "otra";
									}
								//1-3-01-02
								if ( substr($cuenta, 7, 2) == "02" ){
									$modalidad	= 3;
								}
									if ( ($monto > 0) AND ( $socio != 0) ){
										if ($TipoDeImportacion == "credito"){

											$sqlD	= "DELETE FROM sisbancs_temp_creditos WHERE cuenta_contable = '$cuenta' ";
											my_query($sqlD);
											//
											if ( $socio != 0 ){
												$sqlI = "INSERT INTO sisbancs_temp_creditos(numero_de_socio, cuenta_contable, nombre,
														estatus, modalidad, saldo, sucursal)
													VALUES($socio, '$cuenta', '$nombre', $estatus, $modalidad , $monto, '$sucursal') ";
												my_query($sqlI);
												$msg	.= "CREDITO\t$sucursal\t$socio\t$cuenta\tAgregando un monto de $monto\r\n";
											} else {
												$msg	.= "OMITIDO\t$sucursal\t$socio\t$cuenta\tSocio y Dato Omitido\r\n";
											}
										} else {
											$sqlD	= "DELETE FROM sisbancs_temp_depositos WHERE cuenta_contable = '$cuenta' ";
											my_query($sqlD);
											//
											if ( $socio != 0 ){
												$sqlI = "INSERT INTO sisbancs_temp_depositos(numero_de_socio, cuenta_contable, nombre,
													tipo_de_saldo, monto, sucursal)
													VALUES($socio, '$cuenta', '$nombre', $TCuenta, $monto, '$sucursal')";
												my_query($sqlI);
												$msg	.= "AHORRO\t$sucursal\t$socio\t$cuenta\tAgregando un monto de $monto\r\n";
											} else {
												$msg	.= "OMITIDO\t$sucursal\t$socio\t$cuenta\tSocio y Dato Omitido\r\n";
											}
										}
									} else {
										$msg	.= "$iReg\tNO_DATA\tNo se agrego la Linea del socio $socio\r\n";
									}
									//$msg	.= "$iReg\tSQL\t$sqlI\r\n";
								}
							$iReg++;
							}
							//eliminar los datos diferentes a la actual sucursal
							if ($TipoDeImportacion == "credito"){
								$sqlD	= "DELETE FROM sisbancs_temp_creditos WHERE sucursal != '" . getSucursal() . "' ";
								my_query($sqlD);
							} else {
								$sqlD	= "DELETE FROM sisbancs_temp_depositos WHERE sucursal != '" . getSucursal() . "' ";
								my_query($sqlD);
							}
						}
						fclose ($gestor);
						$html = new cHTMLObject();
						
							//$htmlmsg = $html->setInHTML($msg);
							//echo "<p class ='aviso'>$htmlmsg</p>";
							$cF = new cFileLog();
							$cF->setWrite($msg);
							$cF->setClose();
							echo $cF->getLinkDownload("Datos del proceso");
						
						//echo $msg;
				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO ES SOPORTADO</p>";
				}
			}
		}
?>
</fieldset>
</form>
<?php
}	//end else action
if ( !isset($iReg) ){
	$iReg	= 0;
}
?>
</body>
<script  >
</script>
</html>

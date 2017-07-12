<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 0.98
 * @package common
 *  Actualizacion
 * 		16/04/2008
 *		2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimientos y recibos
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


$action 			= $_GET["o"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga para Analisis de Auxiliares del CompacW</title>
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
<form name="frmSendFiles" method="POST" action="analisis_de_auxiliares.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga para Analisis de los Auxiliares de CompaCW V 0.09.23&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO DE PARA ANALISIS</th>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		</tbody>
	</table>
<?php

} elseif ( $action ==  "u" ) {

?>
<form name="frmConvs" method="POST" action="inversiones.upload.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
<?php

		$usrFiles		= array();
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

						$iReg 			= 0;
						$cT				= new cTipos();
						//inicializa el LOG del proceso
						$aliasFil		= getSucursal() . "-analisis_de_auxiliares-" . fechasys();
						$xLog			= new cFileLog($aliasFil, true);
						$exito			= 0;
						$similar		= 0;
						$varios			= 0;
						$arrCargos		= array();
						$arrAbonos		= array();
						$arrInfo		= array();
						$arrIntegrados	= array();
						$msg			= "";
						$busq			= 0;
						//=============================================================================================
						if ($gestor) {
							while (!feof($gestor)) {
								$bufer				= fgets($gestor, 4096);
								//$bufer			= stream_get_line($gestor, "\r\n");
								if (!isset($bufer) ){
									//$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
								} else {
									$bufer			= trim($bufer);
									$datos			= explode(",", $bufer, 8);

									$fecha			= trim($datos[0]);
									$tipoPoliza		= trim($datos[1]);
									$numPoliza		= trim($datos[2]);
									$concepto		= trim($datos[3]);
									$referencia		= trim($datos[4]);
									$mcargo			= $cT->cFloat($datos[5]);
									//$cargo			= trim($datos[5]);
									$mabono			= $cT->cFloat($datos[6]);
									//$abono			= trim($datos[6]);
									//ignorar linea 1
									if ( $iReg >= 1 ){
										$arrInfo[$iReg]		= "$fecha,$tipoPoliza,$numPoliza,$concepto,$referencia";
										if ( $mcargo > 0 ){
											$arrCargos[$iReg]	= $mcargo;
										}
										if ( $mabono > 0 ){
											$arrAbonos[$iReg]	= $mabono;
										}
									}
								}
							$iReg++;
							}
						}
						fclose ($gestor);
						//=================================================================================================
						//Fin de Lectura de Archivo
				//Analizar Matrices
				//CARGOS
					foreach ($arrCargos as $key => $value) {
						$cargo		= $value;

						if ( ($cargo > 0) AND (isset($cargo)) ){
							$buscado	= array_search($cargo, $arrAbonos);
							if ( isset($buscado) AND !is_null($buscado) AND ($buscado !== false) ){
								//recomponer el valor buscado
								$msg				.= "$key,CUADRADO,$exito," .  $arrInfo[$key] . "," . $cargo .  ",0\r\n";
								$msg				.= "$buscado,CUADRADO,$exito," .  $arrInfo[$buscado] . ",0," . $arrAbonos[$buscado] . "\r\n";
								//Eliminar llave
								unset( $arrAbonos[$buscado] );
								unset( $arrCargos[$key] );
								$exito++;
								$busq++;
								$trick	= true;
								//print_r($arrAbonos[$buscado]);
							} else {
								$trick	= false;

								reset($arrAbonos);
								while (list($key0, $val0) = each($arrAbonos)) {
									$eval0	= EsValido($cargo, $val0);
									//Evaluar Cargo y primer Abono
									if( EsApp($cargo) == true AND EsApp($val0) == true ){
										if ($eval0 == true){
											//recomponer el valor buscado
											$msg				.= "$key,SIMILAR,$exito," .  $arrInfo[$key] . "," . $cargo .  ",0\r\n";
											$msg				.= "$key0,SIMILAR,$exito," .  $arrInfo[$key0] . ",0," . $arrAbonos[$key0] . "\r\n";
											//Eliminar llave
											unset( $arrAbonos[$key0] );
											unset( $arrCargos[$key] );
											unset( $val0 );
											unset( $cargo );
											$exito++;
											$busq++;
											$trick	= true;
											break;
										} else {
											//============================================ 2
											$trick		= false;
											$arrAbonos1	= $arrAbonos;
											unset( $arrAbonos1[$key0] );
											reset($arrAbonos1);
											//TODO: Filter a
											if( EsApp($cargo) == true AND EsApp($val0) == true){
											while (list($key1, $val1) = each($arrAbonos1)) {
												$eval1	= EsValido($cargo, $val0, $val1);
												//Evaluar Cargo y primer Abono
												if( EsApp($cargo) == true AND EsApp($val0) == true AND EsApp($val1) == true ){
													if ($eval1 == true){
														//recomponer el valor buscado
														$msg				.= "$key,SIMILAR2,$exito," .  $arrInfo[$key] . "," . $cargo .  ",0\r\n";
														$msg				.= "$key0,SIMILAR2,$exito," .  $arrInfo[$key0] . ",0," . $arrAbonos[$key0] . "\r\n";
														$msg				.= "$key1,SIMILAR2,$exito," .  $arrInfo[$key1] . ",0," . $arrAbonos[$key1] . "\r\n";
														//Eliminar llave
														unset( $arrAbonos[$key0] );
														unset( $arrAbonos[$key1] );
														unset( $arrCargos[$key] );
														unset( $val0 );
														unset( $val1 );
														unset( $cargo );
														$exito++;
														$busq++;
														$trick	= true;
														break;		//ROMPER
													} else {
														//=============================== 3
														if ( $busq <= 1000000 ){
															$trick		= false;
															$arrAbonos2	= $arrAbonos1;
															unset( $arrAbonos2[$key0] );
															unset( $arrAbonos2[$key1] );
															reset($arrAbonos2);
															//TODO: filter b
															if( EsApp($cargo) == true AND EsApp($val0) == true AND EsApp($val1) == true){
															while (list($key2, $val2) = each($arrAbonos2)) {
																$eval2	= EsValido($cargo, $val0, $val1, $val2);
																//Evaluar Cargo y primer Abono
																if( EsApp($cargo) == true AND EsApp($val0) == true AND EsApp($val1) == true AND EsApp($val2) == true ){
																	if ($eval2 == true){
																		//recomponer el valor buscado
																		$msg				.= "$key,SIMILAR3,$exito," .  $arrInfo[$key] . "," . $cargo .  ",0\r\n";
																		$msg				.= "$key0,SIMILAR3,$exito," .  $arrInfo[$key0] . ",0," . $arrAbonos[$key0] . "\r\n";
																		$msg				.= "$key1,SIMILAR3,$exito," .  $arrInfo[$key1] . ",0," . $arrAbonos[$key1] . "\r\n";
																		$msg				.= "$key2,SIMILAR3,$exito," .  $arrInfo[$key2] . ",0," . $arrAbonos[$key2] . "\r\n";
																		//Eliminar llave
																		unset( $arrAbonos[$key0] );
																		unset( $arrAbonos[$key1] );
																		unset( $arrAbonos[$key2] );
																		unset( $arrCargos[$key] );
																		unset( $val0 );
																		unset( $val1 );
																		unset( $val2 );
																		unset( $cargo );
																		$exito++;
																		$busq++;
																		$trick	= true;
																		break;
																	} else {
																		$busq++;
																		$trick	= false;
																	}

																}
																//
															}	//			3 END WHILE
															unset($arrAbonos2);
															}
														}
													}

												}
												//
											}	//			2 END WHILE
											unset( $arrAbonos1 );
											}
										}

									}
									//
								}	//			1 END WHILE
							}
						}

					}
					reset($arrAbonos);
					foreach ($arrAbonos as $key => $value) {
							foreach ($arrCargos as $a0key => $a0value) {
								$arrCargos1	= $arrCargos;
								unset( $arrCargos1[$a0key] );
									foreach ($arrCargos1 as $a1key => $a1value) {
								//No buscar en el mismo valor que el 2::: y las busquedas son menores a 5000
									if ( (EsValido($value, $a0value, $a1value ) == true) AND EsApp($a0value) == true AND EsApp($a1value) ){
										$msg		.= "$key,SIMILAR4,$exito," .  $arrInfo[$key] . ",0," . $value .  "\r\n";
										$msg		.= "$a0key,SIMILAR4,$exito," .  $arrInfo[$a0key] . "," . $arrCargos[$a0key] . ",0\r\n";
										$msg		.= "$a1key,SIMILAR4,$exito," .  $arrInfo[$a1key] . "," . $arrCargos[$a1key] . ",0\r\n";
										//Eliminar llave
										unset( $arrAbonos[$key] );
										unset( $arrCargos[$a0key] );
										unset( $arrCargos[$a1key] );
										unset($key);
										unset($a0key);
										unset($a1key);
										$exito++;
										$busq++;
										$trick	= true;
										break;
									} else {
										$trick	= false;
									}
								}
							}//				2 END EACH
					}
					//Eliminar Abonos
					reset($arrAbonos);
					foreach ($arrAbonos as $key => $value) {
						$msg		.= "$key,FRACASO,0," .  $arrInfo[$key] . ",0," . $value .  "\r\n";
						unset( $arrAbonos[$key] );
						$busq++;
					}
					//Eliminar Cargos
					reset($arrCargos);
					foreach ($arrCargos as $key => $value) {
						$msg				.= "$key,FRACASO,0," .  $arrInfo[$key] . "," . $value .  ",0\r\n";
						$busq++;
						unset( $arrCargos[$key] );
					}

					$xLog->setWrite($msg);

					unset($msg);

					echo $xLog->getLinkDownload("Archivo del proceso con $busq Busquedas");
				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
				}
			}
		}

}


function EsValido($valor, $comparado1, $comparado2 = 0, $comparado3 = 0){
	$comp	= $comparado1 + $comparado2 + $comparado3;
	$ret	= false;
	if ( ($valor < ($comp + 4.99) ) AND ( $valor > ($comp - 4.99) )  ){
		$ret	= true;
	}

	return $ret;
}
function EsApp($valor){
	$val		= true;
	if ( $valor  <= 0) { $val	= false; }
	if ( $valor  == "") { $val	= false; }
	if ( !isset($valor) ) { $val	= false; }
	if ( is_null($valor) ) { $val	= false; }
	return $val;
}
?>
</fieldset>
</form>
</body>
<script  >
</script>
</html>

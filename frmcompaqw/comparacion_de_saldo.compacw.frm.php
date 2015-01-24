<?php
/**
 * Modulo de Compracion de Auxiliares del Catalogo CompacW
 * @author Balam Gonzalez Luis Humberto
 * @version 0.1.01
 * @package common
 *  Actualizacion
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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../libs/compacw.inc.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.creditos.inc.php";
include_once "../core/core.creditos.utils.inc.php";
include_once "../core/core.operaciones.inc.php";
include_once "../core/core.common.inc.php";
include_once "../core/core.security.inc.php";
include_once "../core/core.utils.inc.php";

include_once("../core/core.captacion.utils.inc.php");
include_once("../core/core.captacion.inc.php");

include_once("../core/core.contable.inc.php");
include_once("../core/core.contable.utils.inc.php");

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
<title>Carga de Archivos CompacW para Comparacion [V 0.0.26]</title>
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
<form name="frmSendFiles" method="POST" action="comparacion_de_saldo.compacw.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Cargar de Archivos CompacW y Comparar con SAFE&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="1">ARCHIVO COMPACW A ENVIAR</th>
			<td colspan="1"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th>Tipo de Archivo</th>
			<td><select name="tArchivo">
				<option value="credito">De Colocacion(Creditos)</option>
				<option value="ahorro" selected>De Captacion(Ahorros)</option>
				
			</select></td>
		</tr>
		<tr>
		<!-- <option value="saldos_a_fecha">Comparar con saldos a fecha determinada</option> -->
			<th>Tipo de Comparacion</th>
			<td><select name="tCompara">
				<option value="normal" selected>Comparar con saldos Actuales</option>
				<option value="ultimocierre">Comparar con el Ultimo cierre</option>
			</select></td>
		</tr>		
		<tr>
			<th>Fecha de Cierre de CompacW</th>
			<td><?php
			$xF		= new cFecha(0);
			echo $xF->show(true, "OPERATIVO");
			?></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivo" /></th>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
<?php

} elseif ( $action ==  "u" ) {

?>
<form name="frmConvs" method="POST" action="">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
<?php

		$usrFiles			= array();
		$usrFiles[0]		= $_FILES["cFile1"];
		$msg				= "";

		$TipoDeImportacion	= $_POST["tArchivo"];
		$TipoDeComparacion	= $_POST["tCompara"];
		$fecha_de_corte		= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
		$SAhorro			= 0;
		$SCapital			= 0;
		$SInteres			= 0;
		$SIva				= 0;
		$STotal				= 0;
		$diferencias		= 0;

		$arrTipoCred		= array (
								"1-3-01-01-05" => CREDITO_TIPO_COMERCIAL,
								"1-3-01-02-03" => CREDITO_TIPO_CONSUMO,
								
								"1-3-01-01-01" => CREDITO_TIPO_COMERCIAL,
								"1-3-01-01-02" => CREDITO_TIPO_COMERCIAL,
								
								"1-3-03-01-05" => CREDITO_TIPO_COMERCIAL,
								"1-3-03-02-03" => CREDITO_TIPO_CONSUMO,
								
								"1-3-03-01-01" => CREDITO_TIPO_COMERCIAL,
								"1-3-03-01-02" => CREDITO_TIPO_COMERCIAL
							);
		
		$arrEstatus		= array(
						"1-3-01" => 10,
						"1-3-03" => 20,
						"2-1-01" => 99
						);
		$arrTCuenta		= array (
						"2-1-01-01-01" => 1,
						"2-1-01-01-02" => 2
						);
		$arrComp		= array("normal" => "saldo", "ultimocierre" => "saldo_al_corte");
		$prePath		= PATH_BACKUPS;
		$lim			= 1;

		$arrUltFechas	= array();
		$aCreditos		= array();
		$aCaptacion		= array();
		$aCompacW		= array();
		$aCompacWC		= array();		//Array de cuentas compacw
		$cT				= new cTipos();
		
		$msg			.= "SOCIO\tAPUNTE\tSDOSAFE\tSDOCW\tDIFERENCIA\tOBSEVACIONES\r\n";
		$msgD			.= "SOCIO\tSDOSAFE\tSDOCW\tDIFERENCIA\tCUENTA1\tSDO1\tCUENTA2\tSDO2\r\n";
		
		switch ( $TipoDeImportacion ){
			case "credito":
				//carga ultimas fechas de saldos
				//Sql Creditos
				$sqlCreds			= "SELECT
										`creditos_solicitud`.`numero_socio`,
										SUM(`creditos_solicitud`.`saldo_actual`) AS 'saldo',
										SUM(`creditos_solicitud`.`saldo_conciliado`) AS 'saldo_al_corte'
										
									FROM
										`creditos_solicitud` `creditos_solicitud`
									WHERE
										`creditos_solicitud`.`estatus_actual` != 50 
									GROUP BY
										`creditos_solicitud`.`numero_socio`
									ORDER BY
										`creditos_solicitud`.`numero_socio`,
										`creditos_solicitud`.`saldo_actual` ";
				$rsC 				= mysql_query($sqlCreds, cnnGeneral());
				while ($rwC = mysql_fetch_array($rsC)){
					$aCreditos[ $rwC["numero_socio"] ] += $cT->cFloat( $rwC[ $arrComp[$TipoDeComparacion] ], 2);
					//$msg			.= "SAFE\tSocio: " . $rwC["numero_socio"] . " Saldo: " . $rwC["saldo_actual"] . " \r\n";
				}
				@mysql_free_result($rsC);				
				
				break;
			case "ahorro":
				$sqlCreds			= "SELECT
										`captacion_cuentas`.`numero_socio`,
										SUM(`captacion_cuentas`.`saldo_cuenta`) AS `saldo`, 
										SUM(`captacion_cuentas`.`saldo_conciliado`) AS 'saldo_al_corte'
									FROM
										`captacion_cuentas` `captacion_cuentas` 
									GROUP BY
										`captacion_cuentas`.`numero_socio` ";
				$rsC 				= mysql_query($sqlCreds, cnnGeneral());
				while ($rwC = mysql_fetch_array($rsC)){
					$aCaptacion[ $rwC["numero_socio"] ] += $cT->cFloat( $rwC[ $arrComp[$TipoDeComparacion] ], 2);
					//$msg			.= "SAFE\tSocio: " . $rwC["numero_socio"] . " Saldo: " . $rwC["saldo_actual"] . " \r\n";
				}
				@mysql_free_result($rsC);				
								
				break;
		}
										
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
					$gestor		= @fopen($completePath, "r");
					$iReg 		= 0;
					
					
					if ($gestor) {
						//eliminar los datos diferentes a la actual sucursal
						while (!feof($gestor)) {
							$bufer			= fgets($gestor, 4096);
							//$bufer			= stream_get_line($gestor, "\r\n");
							if (!isset($bufer) ){
								$msg 		.= "La Linea($iReg) no se leyo($bufer)\r\n";
							} else {
								$datos		= explode(",", trim($bufer), 3);
								$cuenta		= trim($datos[0]);
								$DCuenta	= explode("-", $cuenta);
								
								$saldoCW	= $cT->cFloat( trim($datos[2] ) , 2);
								$socioCW	= $cT->cInt( trim($DCuenta[5] ), true);
								//$msg		.= "COMPACW\tSaldo $saldoCW SOCIO $socioCW\r\n";
								$aCompacW[$socioCW] += $saldoCW;
								$aCompacWC[$socioCW] .= "$cuenta\t$saldoCW\t";
							}
							$iReg++;
						}
		
					}
					fclose ($gestor);
						foreach ($aCompacW as $key => $value) {
							$socio	= $cT->cInt( $key, true );
							$sdoCW	= $cT->cFloat( $value );
							//Filtrar por sucursal
							$xSoc	= new cSocio($socio);
							if ( $xSoc->existe($socio) == true ){
								switch ( $TipoDeImportacion ){
									case "credito":
									//1-3-01-02-03-0037002
										$saldo	= ( isset( $aCreditos[$socio] ) ) ? $aCreditos[$socio] : 0;
										if( $cT->getEvalNumeroSimilar($saldo, $sdoCW) == true ){
											$diferencia	= $saldo - $sdoCW;
											$msg		.= "$socio\tSUCESS\t$saldo\t$sdoCW\t$diferencia\tSaldo Correcto, SIMILAR/IGUAL\r\n";
										} else {
											$diferencia	= $saldo - $sdoCW;
											$msg		.= "$socio\tERROR\t$saldo\t$sdoCW\t$diferencia\tSaldo Incorrecto\r\n";
											$msgD		.= "$socio\t$saldo\t$sdoCW\t$diferencia\t" . $aCompacWC[$socio] . "\r\n";
										}
										break;
									case "ahorro":
										$saldo	= ( isset( $aCaptacion[$socio] ) ) ? $aCaptacion[$socio] : 0;
										if( $cT->getEvalNumeroSimilar($saldo, $sdoCW) == true ){
											$diferencia	= $saldo - $sdoCW;
											$msg		.= "$socio\tSUCESS\t$saldo\t$sdoCW\t$diferencia\tSaldo Correcto, SIMILAR/IGUAL\r\n";
										} else {
											$diferencia	= $cT->cFloat( ($saldo - $sdoCW), 2);
											
											$msg		.= "$socio\tERROR\t$saldo\t$sdoCW\t$diferencia\tSaldo Incorrecto\r\n";
											$msgD		.= "$socio\t$saldo\t$sdoCW\t$diferencia\t" . $aCompacWC[$socio] . "\r\n";
										}										
										break;
								}
							} else {
								$msg		.= "$socio\tNO.EXIST\t0\t$sdoCW\t0\tSocio No existe en la Sucursal\r\n";
							}
						}
												
					$html 	= new cHTMLObject();
					$fileDw	= getSucursal() .  "-compacw-comparacion_auxiliares_del_catalogo-" . date("ydmHsi");
					$fileDi	= getSucursal() .  "-$TipoDeImportacion-diferencias_compaqw-vs-safe_" . date("ydmHsi");
					
					$cF 	= new cFileLog($fileDw);
					$cFd 	= new cFileLog($fileDi);
					
					$cF->setWrite($msg);
					$cF->setClose();
					//
					$cFd->setWrite($msgD);
					$cFd->setClose();
					
					echo $cFd->getLinkDownload("Archivo de diferencias");					
					echo $cF->getLinkDownload("Datos del proceso de Comparacion");
				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO ES SOPORTADO</p>";
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

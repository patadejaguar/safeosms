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
$xP		= new cHPage("");
$xP->setIncludes();

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 1800);

//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$operation 			= $_GET["o"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Respaldos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
//Si la Operacion es Configurar los Datos
if($operation == "s"){
?>
<form name="frmSetBackup" method="post" action="matriz.restore_backup.frm.php?o=e">
<fieldset>
	<legend>Restaurar Datos de Sucursales</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Fecha de Corte</td>
			<td><input type='text' name='cFechaCorte' value='<?php echo fechasys(); ?>' id="idFechaCorte" /></td>
		</tr>
		<tr>
			<td>Sucursal</td>
			<td><?php
				$sql = "SELECT * FROM general_sucursales WHERE codigo_sucursal!='matriz'";
				$cSel = new cSelect("cSucursal", "idSucursal", $sql);
				$cSel->setEsSql();
				$cSel->show(false);
			?></td>
		<tr>
			<th colspan="2"><input type="submit" value="Iniciar las Restauracion" /></th>
		</tr>
		</tbody>
	</table>

<?php
//si la Operacion es Excutar
} elseif($operation == "e" and $_POST["cSucursal"]!="matriz"){

		$FechaCorte			= $_POST["cFechaCorte"];	//Fecha Inicial de Corte
		$LoadSucursal		= $_POST["cSucursal"];


	$aliasFils	= "$LoadSucursal-$FechaCorte-archivo-log-de-carga-batch";
	//Elimina el Archivo
	//unlink(PATH_TMP . $aliasFils . ".txt");
	//Abre Otro, lo crea si no existe
	$URIFil			= fopen(PATH_TMP . $aliasFils . ".txt", "a+");

	$msg			= "=========================================================================================================\r\n";
	$msg			.= "===================================== INICIANDO LA RESTAURACION DE $LoadSucursal =============================\r\n";
	$msg			.= "=========================================================================================================\r\n";




	$xSuc 			= new cSucursal( getSucursal() );
	$msg			.= $xSuc->setValidar();
    $xCL 			= new cCajaLocal(getCajaLocal());
    $msg			.= $xCL->setValidar();
      		
	$msg			.= date("H:i:s") . "\tActualizar a la Sucursal el USUARIO POR DEFECTO\r\n";
	
	//Elimina el Registro de Temporal
	$sqlDTmp		= "DELETE FROM general_tmp";
	$msg			.= date("H:i:s") . "\tEliminar la Tabla de Registros Temporales\r\n";
	my_query($sqlDTmp);
	//PURGA LAS OPERACIONES DE OTRAS SUCURSALES
		$chri 			= STD_LITERAL_DIVISOR;
		$sucursal		= getSucursal();
		$bkpath 		= PATH_BACKUPS;
		//step one: Socios a sus sucursales
		$xCL			= new cCajaLocal(getCajaLocal());
		$msg			= $xCL->setValidar();
		//step two: Folios al Maximo

		$msg			.= setFoliosAlMaximo();
		
		/** @since 2010-11-30 */
       $cDB 		= new cSAFEData();
       $msg  		.= $cDB->setLowerSucursal();

						
//====================================PURGAR DATOS DE A SUCURSAL ===================================================
		$msg	.= $cDB->setDeleteSucursal($sucursal);
//=====================================================================================================================

		$inFiles	= array();
		$inQuerys	= array();
		$inSQL		= array();
		$inPath		= "$bkpath/$LoadSucursal-$FechaCorte";
		//step one: Socios a sus sucursales
		$msg		= $xCL->setValidar();
		//step two: Folios al Maximo
		$msg		.= setFoliosAlMaximo();
		//Tablas
		$xTabla		= new cSAFETabla();
//==================================================================================================================
//==================================================================================================================
		//Respaldo SOCIOS
		$index 		= 5;
		$inFiles[$index] = "socios_general.sbk";
		if(file_exists("$inPath-$inFiles[$index]")==true) {
		$inSQL[$index] = "LOAD DATA INFILE '$inPath-$inFiles[$index]'
					REPLACE INTO TABLE socios_general
					FIELDS TERMINATED BY '$chri'
					LINES TERMINATED BY '\\r\\n'";
		$inQuerys[$index] =	my_query($inSQL[$index] );
			if($inQuerys[$index]["stat"]== false){
				$msg	.= date("H:i:s") . "\t HUBO UN ERROR AL CARGAR EL ARCHIVO" . $inFiles[$index] . "; EL SISTEMA DEVOLVIO " . $inQuerys[$index]["error"] . "\r\n";
			}else {
				$msg	.= date("H:i:s") . "\tEL ARCHIVO " . $inFiles[$index] . " SE PROCESO EXITOSAMENTE \r\n\t EL SISTEMA DEVUELVE " . $inQuerys[$index]["info"] . "\r\n";
			}
			//unlink("$inPath-$inFiles[$index]");
		} else {
			$msg	.= date("H:i:s") . "\t SE EXCLUYE EL ARCHIVO " . $inFiles[$index] . " PORQUE NO EXISTE\r\n";
		}
		//GRUPOS SOLIDARIOS
		$index 		= 8;
		$inFiles[$index] = "socios_grupossolidarios.sbk";
		if(file_exists("$inPath-$inFiles[$index]")==true) {
		$inSQL[$index] = "LOAD DATA INFILE '$inPath-$inFiles[$index]'
					IGNORE INTO TABLE socios_grupossolidarios
					FIELDS TERMINATED BY '$chri'
					LINES TERMINATED BY '\\r\\n'";
		$inQuerys[$index] =	my_query($inSQL[$index] );
			if($inQuerys[$index]["stat"]== false){
				$msg	.= date("H:i:s") . "\t HUBO UN ERROR AL CARGAR EL ARCHIVO" . $inFiles[$index] . "; EL SISTEMA DEVOLVIO " . $inQuerys[$index]["error"] . "\r\n";
			}else {
				$msg	.= date("H:i:s") . "\tEL ARCHIVO " . $inFiles[$index] . " SE PROCESO EXITOSAMENTE \r\n\t EL SISTEMA DEVUELVE " . $inQuerys[$index]["info"] . "\r\n";
			}
			//unlink("$inPath-$inFiles[$index]");
		} else {
			$msg	.= date("H:i:s") . "\t SE EXCLUYE EL ARCHIVO " . $inFiles[$index] . " PORQUE NO EXISTE\r\n";
		}
		//CAPTACION	CUENTAS.- Reemplaza
		$index 		= 1;
		$inFiles[$index] = "captacion_cuentas.sbk";
		if(file_exists("$inPath-$inFiles[$index]")==true) {
		$inSQL[$index] = "LOAD DATA INFILE '$inPath-$inFiles[$index]'
					REPLACE INTO TABLE captacion_cuentas
					FIELDS TERMINATED BY '$chri'
					LINES TERMINATED BY '\\r\\n'";
		$inQuerys[$index] =	my_query($inSQL[$index] );
			if($inQuerys[$index]["stat"]== false){
				$msg	.= date("H:i:s") . "\t HUBO UN ERROR AL CARGAR EL ARCHIVO " . $inFiles[$index] . "; EL SISTEMA DEVOLVIO " . $inQuerys[$index]["error"] . "\r\n";
			} else {
				$msg	.= date("H:i:s") . "\tEL ARCHIVO " . $inFiles[$index] . " SE PROCESO EXITOSAMENTE \r\n
												\t EL SISTEMA DEVUELVE " . $inQuerys[$index]["info"] . "\r\n";
			}
			//unlink("$inPath-$inFiles[$index]");
		} else {
			$msg	.= date("H:i:s") . "\t SE EXCLUYE EL ARCHIVO " . $inFiles[$index] . " PORQUE NO EXISTE\r\n";
		}
		//CREDITOS
		$index 		= 3;
		$inFiles[$index] = "creditos_solicitud.sbk";
		if(file_exists("$inPath-$inFiles[$index]")==true) {
		$inSQL[$index] = "LOAD DATA INFILE '$inPath-$inFiles[$index]'
					REPLACE INTO TABLE creditos_solicitud
					FIELDS TERMINATED BY '$chri'
					LINES TERMINATED BY '\\n'";
		$inQuerys[$index] =	my_query($inSQL[$index] );
			if($inQuerys[$index]["stat"]== false){
				$msg	.= date("H:i:s") . "\tHUBO UN ERROR AL CARGAR EL ARCHIVO" . $inFiles[$index] . "; EL SISTEMA DEVOLVIO " . $inQuerys[$index]["error"] . "\r\n";
			}else {
				$msg	.= date("H:i:s") . "\tEL ARCHIVO " . $inFiles[$index] . " SE PROCESO EXITOSAMENTE \r\n\t EL SISTEMA DEVUELVE " . $inQuerys[$index]["info"] . "\r\n";
			}
			//unlink("$inPath-$inFiles[$index]");
		} else {
			$msg	.= date("H:i:s") . "\t SE EXCLUYE EL ARCHIVO " . $inFiles[$index] . " PORQUE NO EXISTE\r\n";
		}
		
//==================================================================================================================
		//Restaurar	OPERACIONES RECIBOS
		$xTabla->init("operaciones_recibos");
		$index 				= 1;
		$inFiles[$index] 	= $xTabla->getNombreRespaldo($FechaCorte);
		
		if(file_exists( $inFiles[$index] )==true) {
		$inSQL[$index] 		= "LOAD DATA INFILE '$inFiles[$index]'
								IGNORE INTO TABLE operaciones_recibos
								FIELDS TERMINATED BY '$chri'
								LINES TERMINATED BY '\\r\\n'

									(@var1, fecha_operacion, numero_socio, docto_afectado, tipo_docto,
									total_operacion, idusuario, observacion_recibo, cheque_afectador,
									cadena_distributiva, tipo_pago, indice_origen, grupo_asociado,
									recibo_fiscal, sucursal, eacp )
								
								SET idoperaciones_recibos = getMorphosRecibo(@var1, getUltimoRecibo())";
		$inQuerys[$index] 	=	my_query($inSQL[$index] );
			if($inQuerys[$index]["stat"]== false){
				$msg		.= "<p class='warn'> HUBO UN ERROR AL CARGAR EL ARCHIVO" . $inFiles[$index] . "; EL SISTEMA DEVOLVIO " . $inQuerys[$index]["error"] . "\r\n";
			}else {
				$msg		.= date("H:i:s") . "\tEL ARCHIVO " . $inFiles[$index] . " SE PROCESO EXITOSAMENTE \r\n
												\t EL SISTEMA DEVUELVE " . $inQuerys[$index]["info"] . "\r\n";
			}
			//unlink("$inPath-$inFiles[$index]");
		} else {
			$msg			.= date("H:i:s") . "\t SE EXCLUYE EL ARCHIVO " . $inFiles[$index] . " PORQUE NO EXISTE\r\n";
		}


	//Restaurar	OPERACIONES
		$index 		= 2;
		$inFiles[$index] = "operaciones_mvtos.sbk";
		if(file_exists("$inPath-$inFiles[$index]")==true) {
		$inSQL[$index] = "LOAD DATA INFILE '$inPath-$inFiles[$index]'
					INTO TABLE operaciones_mvtos
					FIELDS TERMINATED BY '$chri'
					LINES TERMINATED BY '\\r\\n'
					(fecha_operacion, fecha_afectacion, @var3, socio_afectado, docto_afectado, tipo_operacion, afectacion_real,
					afectacion_cobranza, afectacion_contable, valor_afectacion, fecha_vcto, estatus_mvto, codigo_eacp, periodo_socio,
					periodo_contable, periodo_cobranza, periodo_seguimiento, periodo_mensual, periodo_semanal, periodo_anual, saldo_anterior,
					saldo_actual, detalles, idusuario, afectacion_estadistica, docto_neutralizador, cadena_heredada, tasa_asociada,
 					dias_asociados, grupo_asociado, sucursal)
 					SET recibo_afectado	= getReciboByMorphedAnterior(@var3) ";
		
		$inQuerys[$index] =	my_query($inSQL[$index] );
			if($inQuerys[$index]["stat"]== false){
				$msg	.= date("H:i:s") . "\t HUBO UN ERROR AL CARGAR EL ARCHIVO" . $inFiles[$index] . "; EL SISTEMA DEVOLVIO " . $inQuerys[$index]["error"] . "\r\n";
			}else {
				$msg	.= date("H:i:s") . "\tEL ARCHIVO " . $inFiles[$index] . " SE PROCESO EXITOSAMENTE \r\n\t EL SISTEMA DEVUELVE " . $inQuerys[$index]["info"] . "\r\n";
			}
			//unlink("$inPath-$inFiles[$index]");
		} else {
			$msg	.= date("H:i:s") . "\t SE EXCLUYE EL ARCHIVO " . $inFiles[$index] . " PORQUE NO EXISTE\r\n";
		}
		

		
//==================================================================================================================		
	//FLUJO DE EFVO
		$index 		= 2;
		$inFiles[$index] = "creditos_flujoefvo.sbk";
		if(file_exists("$inPath-$inFiles[$index]")==true) {
		$inSQL[$index] = "LOAD DATA INFILE '$inPath-$inFiles[$index]'
					INTO TABLE creditos_flujoefvo
					FIELDS TERMINATED BY '$chri'
					LINES TERMINATED BY '\\r\\n'
					(solicitud_flujo, socio_flujo, tipo_flujo,
					origen_flujo, monto_flujo, afectacion_neta, periocidad_flujo, idusuario,
					observacion_flujo, descripcion_completa, sucursal, fecha_captura)";
		$inQuerys[$index] =	my_query($inSQL[$index] );
			if($inQuerys[$index]["stat"]== false){
				$msg	.= date("H:i:s") . "\tHUBO UN ERROR AL CARGAR EL ARCHIVO" . $inFiles[$index] . "; EL SISTEMA DEVOLVIO " . $inQuerys[$index]["error"] . "\r\n";
			}else {
				$msg	.= date("H:i:s") . "\tEL ARCHIVO " . $inFiles[$index] . " SE PROCESO EXITOSAMENTE \r\n\t EL SISTEMA DEVUELVE " . $inQuerys[$index]["info"] . "\r\n";
			}
			//unlink("$inPath-$inFiles[$index]");
		} else {
			$msg	.= date("H:i:s") . "\t SE EXCLUYE EL ARCHIVO " . $inFiles[$index] . " PORQUE NO EXISTE\r\n";
		}



//========================================================================================================================================
		//Actualiza los Folios
		$mRec = setNuevoRecibo(DEFAULT_SOCIO, 1, $FechaCorte, 1, 10, "RECIBO_DE_MVTOS_HUERFANOS", "na", "ninguno", "na", DEFAULT_GRUPO, 0);
			$sqlAMvtosH	= "UPDATE operaciones_mvtos SET cadena_heredada =
							TRIM(LEFT(CONCAT(cadena_heredada, ' ' ,'originado de ', recibo_afectado),195)) , recibo_afectado = $mRec
							WHERE
									(SELECT COUNT(idoperaciones_recibos)
										FROM operaciones_recibos
											WHERE idoperaciones_recibos = operaciones_mvtos.recibo_afectado) = 0
								AND fecha_operacion >= '$FechaCorte' ";
			$rsMH = my_query($sqlAMvtosH);
			echo date("H:i:s") . "\tAgregando el Recibo de Movimientos Huerfanos NUM $mRec\r\n";
		$msg			.= setPurgeFromDuplicatedRecibos();
		setFoliosAlMaximo();
//Si No Hay Operacion se estable a Upload Files
@fwrite($URIFil, $msg);
echo "<a href=\"../utils/download.php?type=txt&download=$aliasFils&file=$aliasFils\" target=\"_blank\" class='boton'>Descargar Archivo de EVENTOS</a>";

} else {
?>
<form name="frmSendFiles" method="post" action="matriz.restore_backup.frm.php?a=s" enctype="multipart/form-data">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVOS AL SERVIDOR DE RESPALDOS</th>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile1" size="100" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile2" size="100" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile3" size="100" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile4" size="100" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile5" size="100" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		</tbody>
	</table>
<?php
	//capturar segunda Accion
	$action = $_GET["a"];
	if($action=="s"){
		$usrFiles	= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$usrFiles[1]	= $_FILES["cFile2"];
		$usrFiles[2]	= $_FILES["cFile3"];
		$usrFiles[3]	= $_FILES["cFile4"];
		$usrFiles[4]	= $_FILES["cFile5"];

		$prePath	= PATH_BACKUPS;
		$lim		= 4; //sizeof($usrFiles) -1;
		for($i=0; $i<=$lim; $i++){
			if(isset($usrFiles[$i])==true){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[$i]['name'],-6));
				$mExt	= $DExt[1];
				//if($usrFiles[$i]["type"] == "text/plain"){
				if($mExt == "sbk"){
					$completePath	= $prePath . $usrFiles[$i]['name'];
					if(file_exists($completePath)==true){
						unlink($completePath);
						echo "<p class='aviso'> SE ELIMINO EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					}
					if(move_uploaded_file($usrFiles[$i]['tmp_name'], $completePath )) {
						//cambiar los permisos
						chmod($completePath,�0755);
						exec("chmod -R 0755 " . PATH_BACKUPS . "");
						//chown($completePath, "www-data");
						echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					} else {
						echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
					}
				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
				}
			}
		}
//echo "	<a href=\"./matriz.restore_backup.frm.php\" target=\"_self\">Subir mas Archivos</a> \r\n\t";
	}

echo "<a href=\"./matriz.restore_backup.frm.php?o=s\" target=\"_self\">Comenzar la Restauracion</a>\r\n\t
		";
}
?>
</fieldset>
</form>
</body>
<script  >
</script>
</html>

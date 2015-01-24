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
include_once("sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.contable.inc.php");

ini_set("max_execution_time", 1800);

function ImportCatalogoCW(){
	//Elimina los Datos de la DB del Catalogo
	$sqlDelC = "DELETE FROM contable_catalogo";
	my_query($sqlDelC);
/**
 * Genera le Nuevo esquema de Naturaleza Contable
 */
$arrTipos = array(
			"A"=>"AD",
			"B"=>"AA",
			"C"=>"PD",
			"D"=>"PA",
			"E"=>"CD",
			"F"=>"CA",
			"G"=>"RD",
			"H"=>"RA",
			"K"=>"OD",
			"L"=>"OA",
			"I"=>"ED",
			"J"=>"EA"
			);
$arrMayor = array(
	"1"	=> "3",
	"2"	=> "4",
	"3"	=> "1",
	"4"	=> "2"
	);
	$pathdbase = CTW_PATH . vLITERAL_SEPARATOR . "CTW10001.dbf";
	$rs = dbase_open($pathdbase, 0);
	$results[SYS_MSG] .= "Abrir $pathdbase <br />";
	$num_rows = dbase_numrecords ($rs);
	if ($num_rows > 100000) {
			//$num_rows = 10000;
	}
	$results[SYS_MSG] .= "Numero de Filas $num_rows <br />";
	$rechazados		= 0;
	if(isset($rs)){
		//$results[SYS_MSG] .= "Cerrando " . dbase_get_header_info($rs) . " <br />";
		for ($i=1; $i <= $num_rows; $i++){
    		//$field = dbase_get_record_with_names($rs, $i);
    		$field = dbase_get_record($rs, $i);
    		$sqlW	= "";
    		$cuenta	= "";
    		$nombre	= "";
    		$tipo	= "";
    		$mayor	= "";
    		$afect	= "";
    		$ccosto	= "";
    		$falta	= "";
    		$digito	= "";
    			if(trim($field[0])!="" or trim($field[0])!=0){
    				$digito = getDigitoAgrupador(trim($field[0]));
					$cuenta = trim($field[0]);
					//Corrige el Cuadre
						if($cuenta == "_CUADRE"){
							$cuenta = CUENTA_DE_CUADRE;
						}
					$nombre	= trim($field[1]);
					$tipo	= $arrTipos[trim($field[3])];
					$mayor	=  $arrMayor[trim($field[5])];
					$afect	= 1;
					//Verifica si la Cuenta es Afectable
					$hijas	= getNumsCtasInferiores($cuenta);
					if($hijas>0){
						$afect	= 0;
					}
					$falta	= trim($field[7]);
					$ccosto = trim($field[11]);
					//Execute query
					$sql = "INSERT INTO contable_catalogo(numero, equivalencia, nombre, tipo, ctamayor, afectable, centro_de_costo, fecha_de_alta, digitoagrupador) 
   										VALUES($cuenta, '00000000000000', '$nombre', '$tipo', $mayor, $afect, $ccosto, '$falta', $digito)";
					my_query($sql);

    			} else {
    				$rechazados++;
    				//$results[SYS_MSG] .= "La Fila $i No se Importo, por contener " . trim($field[0]) . "-" . trim($field[1]) . " <br />";
    			}    			
		} 
	} else {
		$results[SYS_MSG] .= "No se Pudo Abrir la DB";
	}
	dbase_close ($rs);
	$results[SYS_MSG] .= "$rechazados Registros Furon Excluidos por no tener Valores Validos <br />";
	$results[SYS_MSG] .= "Cerrando $pathdbase <br />";
	
	return $results;
}
function ImportPolizasCW(){
	//Elimina los Datos de la DB del Catalogo
	$sqlDelC = "DELETE FROM contable_polizas";
	my_query($sqlDelC);
/**
 * Genera le Nuevo esquema de Naturaleza Contable
 */
	$pathdbase = CTW_PATH . vLITERAL_SEPARATOR . "CTW10003.dbf";
	$rs = dbase_open($pathdbase, 0);
	$results[SYS_MSG] .= "Abrir $pathdbase <br />";
	$num_rows = dbase_numrecords ($rs);
	if ($num_rows > 100000) {
			//$num_rows = 10000;
		}
	$results[SYS_MSG] .= "Numero de Filas $num_rows <br />";
	$aceptados		= 0;
	$rechazados		= 0;
	if(isset($rs)){
		for ($i=1; $i <= $num_rows; $i++){
    		//$field = dbase_get_record_with_names($rs, $i);
    		$field 			= dbase_get_record($rs, $i);
			$ejercicio		= "";
			$periodo		= "";
			$tipo			= "";
			$numero			= "";
			$clase			= "";
			$impresa		= "";
			$concepto		= "";
			$cargos			= "";
			$abonos			= "";
			$diario			= "";
			$fecha			= "";
    			if(trim($field[0])!="" and trim($field[0])!=0){
					$ejercicio	= trim($field[0]);
					$periodo	= trim($field[1]);
					$tipo		= trim($field[2]);
					$numero		= trim($field[3]);
					$clase		= trim($field[4]);
					$impresa	= trim($field[5]);
					$concepto	= purgeText(trim($field[6]));
					$fecha		= trim($field[7]);
					if(!$fecha or $fecha==""){
						$fecha	= fechasys();
					}
					$cargos		= trim($field[8]);
					$abonos		= trim($field[9]);
					$diario		= trim($field[10]);
					$sqlIM		= "INSERT INTO contable_polizas(ejercicio, periodo, tipopoliza, 
											numeropoliza, clase, impresa, concepto, fecha, cargos, abonos, diario) 
									VALUES($ejercicio, $periodo, $tipo, $numero, 
											$clase, '$impresa', '$concepto', '$fecha', $cargos, $abonos, $diario)";
					my_query($sqlIM);
    			} else {
    				$rechazados++;
    				//$results[SYS_MSG] .= "La Fila $i No se Importo, por contener " . trim($field[0]) . "-" . trim($field[1]) . " <br />";
    			}    			
		} 
	} else {
		$results[SYS_MSG] .= "No se Pudo Abrir la DB <br />";
	}
dbase_close ($rs);
$results[SYS_MSG] .= "$rechazados Fueron Excluidos por no tener Valores validos <br />";
$results[SYS_MSG] .= "Cerrando $pathdbase <br />";
return $results;	
}
function ImportMvtosCW(){
	//Elimina los Datos de la DB del Catalogo
	$sqlDelC = "DELETE FROM contable_movimientos";
	my_query($sqlDelC);
	$rechazados		= 0;
	$aceptados		= 0;
/**
 * Genera le Nuevo esquema de Naturaleza Contable
 */
	$pathdbase = CTW_PATH . vLITERAL_SEPARATOR . "CTW10004.dbf";
	$rs = dbase_open($pathdbase, 0);
	$results[SYS_MSG] .= "Abrir $pathdbase <br />";
	$num_rows = dbase_numrecords ($rs);
	if ($num_rows > 100000) {
			//$num_rows = 102000;
		}
	$results[SYS_MSG] .= "Numero de Filas $num_rows <br />";
	//Conversion del Tipo de Movimientos
	//Para efectos de DBX 0 = false; 1 = True
	$arrTipoMvto	= array(
						"0"		=> TM_CARGO,
						"1"		=> TM_ABONO
						);
	if(isset($rs)){
		//$results[SYS_MSG] .= "Cerrando " . dbase_get_header_info($rs) . " <br />";
		for ($i=1; $i <= $num_rows; $i++){
    		//$field = dbase_get_record_with_names($rs, $i);
    		$field 			= dbase_get_record($rs, $i);
			$ejercicio		= "";
			$periodo		= "";
			$tipoPol		= "";
			$numeroPol		= "";
			$cuenta			= "";
			$mvto			= "";
			$tipoMvto		= "";		
			$referencia		= "";
			$concepto		= "";
			$cargo			= "";
			$abono			= "";
			$importe		= "";
			$diario			= "";
			$fecha			= "";
			settype($tipoMvto, "string");
    			if(trim($field[0])!="" and trim($field[0])!=0){
    				$ejercicio	= trim($field[0]);
    				$periodo	= trim($field[1]);
    				$tipoPol	= trim($field[2]);
    				$numeroPol	= trim($field[3]);
    				$mvto		= trim($field[4]);
    				$cuenta		= trim($field[5]);
    					if($cuenta == "_CUADRE"){
    						$cuenta	= CUENTA_DE_CUADRE;
    					}
    				$tipoMvto	= trim($field[6]);
    				$tipoMvto	= $arrTipoMvto[$tipoMvto];
    				//$results[SYS_MSG] .= " EL TIPO BOOLEAN ES " . trim($field[6]) . " de $ejercicio, $periodo, $tipoPol, $numeroPol, $mvto<br />";
					$referencia	= purgeText(trim($field[7]));
					$concepto	= purgeText(trim($field[11]));
					$importe	= trim($field[8]);
					$diario		= trim($field[9]);
					$fecha		= trim($field[12]);
					if($tipoMvto == TM_CARGO){
						$cargo 	= $importe;
						$abono	= 0;
					} else {
						$abono	= $importe;
						$cargo	= 0;
					}
					//Execute Query
					$sqlIM = "INSERT INTO contable_movimientos(ejercicio, periodo, tipopoliza, numeropoliza, 
																numeromovimiento, numerocuenta, tipomovimiento, 
																referencia, importe, diario, moneda, concepto, 
																fecha, cargo, abono) 
    															VALUES($ejercicio, $periodo, $tipoPol, $numeroPol, 
    																	$mvto, $cuenta, '$tipoMvto', 
    																	'$referencia', $importe, $diario, 0, '$concepto', 
    																	'$fecha', $cargo, $abono)";
					my_query($sqlIM);
					$aceptados++;
    			} else {
    				$rechazados++;
    			}    			
		} 
	} else {
		$results[SYS_MSG] .= "No se Pudo Abrir la DB";
	}
dbase_close ($rs);
$results[SYS_MSG] .= "$aceptados Registros fueron Agregados a la Base de Datos <br />";
$results[SYS_MSG] .= "$rechazados Registros fueron rechazados por no Contener Valores Validos <br />";
$results[SYS_MSG] .= "Cerrando $pathdbase <br />";
return $results;
}
function ImportRelacionesCW(){
	//Elimina los Datos de la DB del Catalogo
	$sqlDelC = "DELETE FROM contable_catalogorelacion";
	my_query($sqlDelC);
	$rechazados		= 0;
	$aceptados		= 0;
/**
 * Genera le Nuevo esquema de Naturaleza Contable
 */
	$pathdbase = CTW_PATH . vLITERAL_SEPARATOR . "CTW10002.dbf";
	$rs = dbase_open($pathdbase, 0);
	$results[SYS_MSG] .= "Abrir $pathdbase <br />";
	$num_rows = dbase_numrecords ($rs);
	if ($num_rows > 100000) {
			//$num_rows = 1000000;
		}
	$results[SYS_MSG] .= "Numero de Filas $num_rows <br />";
	//Conversion del Tipo de Movimientos
	if(isset($rs)){
		for ($i=1; $i <= $num_rows; $i++){
    		$field 			= dbase_get_record($rs, $i);

    			if(trim($field[0])!="" and trim($field[0])!=0){
    				$sql = "INSERT INTO contable_catalogorelacion
    				(cuentasuperior, subcuenta, tiporelacion) 
   					VALUES(" . trim($field[0]) . ", " . trim($field[1]) . ", " . trim($field[2]) . ")";
    				my_query($sql);
					$aceptados++;
    			} else {
    				$rechazados++;
    			}    			
		} 
	} else {
		$results[SYS_MSG] .= "No se Pudo Abrir la DB";
	}
dbase_close ($rs);
$results[SYS_MSG] .= "$aceptados Registros fueron Agregados a la Base de Datos <br />";
$results[SYS_MSG] .= "$rechazados Registros fueron rechazados por no Contener Valores Validos <br />";
$results[SYS_MSG] .= "Cerrando $pathdbase <br />";
return $results;
}
function ImportSaldosCW(){
	//Elimina los Datos de la DB del Catalogo
	$sqlDelC = "DELETE FROM contable_saldos";
	my_query($sqlDelC);
	$rechazados		= 0;
	$aceptados		= 0;
/**
 * Genera le Nuevo esquema de Naturaleza Contable
 */
	$pathdbase = CTW_PATH . vLITERAL_SEPARATOR . "CTW10005.dbf";
	$rs = dbase_open($pathdbase, 0);
	$results[SYS_MSG] .= "Abrir $pathdbase <br />";
	$num_rows = dbase_numrecords ($rs);
	if ($num_rows > 100000) {
			//$num_rows = 1000000;
		}
	$results[SYS_MSG] .= "Numero de Filas $num_rows <br />";
	//Conversion del Tipo de Movimientos
	if(isset($rs)){
		for ($i=1; $i <= $num_rows; $i++){
    		$field 			= dbase_get_record($rs, $i);

    			if(trim($field[0])!="" and trim($field[0])!=0){
    				$cuenta = trim($field[0]);
    					if($cuenta == "_CUADRE"){
    						$cuenta = CUENTA_DE_CUADRE;
    					}
    				$sql = "INSERT INTO contable_saldos(cuenta, 
    				ejercicio, tipo, saldo_inicial, imp1, imp2, imp3, imp4, imp5, imp6, imp7, imp8, imp9, imp10, imp11, imp12, imp13, imp14, captado) 
    													VALUES($cuenta, 
    													" . trim($field[1]) . ", " . trim($field[2]) . ",
    													" . trim($field[3]) . ", " . trim($field[4]) . ", 
    													" . trim($field[5]) . ", " . trim($field[6]) . ", 
    													" . trim($field[7]) . ", " . trim($field[8]) . ",
    													" . trim($field[9]) . ", " . trim($field[10]) . ", 
    													" . trim($field[11]) . ", " . trim($field[12]) . ", 
    													" . trim($field[13]) . ", " . trim($field[14]) . ", 
    													" . trim($field[15]) . ", " . trim($field[16]) . ", 
    													" . trim($field[17]) . ", '" . trim($field[18]) . "')";
    				my_query($sql);
					$aceptados++;
    			} else {
    				$rechazados++;
    			}    			
		} 
	} else {
		$results[SYS_MSG] .= "No se Pudo Abrir la DB";
	}
dbase_close ($rs);
$results[SYS_MSG] .= "$aceptados Registros fueron Agregados a la Base de Datos <br />";
$results[SYS_MSG] .= "$rechazados Registros fueron rechazados por no Contener Valores Validos <br />";
$results[SYS_MSG] .= "Cerrando $pathdbase <br />";
return $results;
}
function purgeText($text) {
	$text = str_replace("'", "", $text);
	$text = str_replace('"', "", $text);
	$text = str_replace('\\', "", $text);
	return $text;
}
?>


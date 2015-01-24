<?php
/**
 * Consulta Contpaq
 */
include_once ("../core/core.deprecated.inc.php");
//ini_set("display_errors", "on");

function odbc_fila($sql, $fila) {
	$cnn_odbc 	= odbc_connect("ctw", "", "");
	$rs 		= odbc_exec($cnn_odbc, $sql);
	$value 		= "NO_EXISTE";
	$value 		= odbc_fetch_array($rs, 1);
	odbc_free_result($rs);
	odbc_close($cnn_odbc);
		//if(!$rs){
		//	return odbc_error($rs);
		//} else {

			return $value[$fila];
		//}
}
function cw_cuenta($socio, $credito=0){
	$sql = "SELECT codigo, cajalocal FROM socios_general WHERE codigo=$socio LIMIT 0,1";
	$resc = "";
	 	if ($credito!=0){
	 		$resc = " AND numero_solicitud=$credito ";
	 	} else {
	 		$resc = " AND numero_socio=$socio ";
	 	}
	$sqlc 		= "SELECT * FROM creditos_solicitud WHERE saldo_actual>0 $resc  LIMIT 0,1";
	$datos 		= obten_filas($sql);
	$datosc 	= obten_filas($sqlc);
	$socio_cw 	= "";
	$cuenta 	= "";
	$prefijo 	= "";
	/**
	 * Si el Socio es Menor a 37999
	 */
	if($datos[0]<=37999){
		$socio_cw = substr(("00" . $datos[1] . "0" . substr($datos[0], -3)), -6);

	} else {
		$socio_cw = $datos[0];
	}
	/**
	 * Si es automatizado se va a Automaticos
	 */
	if ($datosc[24]==10){
		if ($datosc[12]==20) {
			$cuenta = "111201" . $socio_cw;
		} else {
			$cuenta = "110501" . $socio_cw;
		}
	} else {
		if ($datosc[12]==20) {
			$cuenta = "111203" . $socio_cw;
		} else {
			$cuenta = "110601" . $socio_cw;
		}
	}
	return  $cuenta;
}
function saldo_cw($cuenta){
	$sql_gen = "SELECT * FROM general_configuration LIMIT 0,1";
	$dats = obten_filas($sql_gen);
	$periodo = $dats[78];
	$anno = date("Y");
	return   odbc_fila("SELECT * FROM ctw10005 WHERE cuenta='$cuenta' AND eje=$anno", "imp$periodo");
}
function fecha_cw($cuenta){
	$sql_gen = "SELECT * FROM general_configuration LIMIT 0,1";
	$dats = obten_filas($sql_gen);
	$periodo = $dats[78];
	$anno = date("Y");
	return   odbc_fila("SELECT * FROM ctw10004 WHERE cuenta='$cuenta' AND periodo=$periodo AND eje=$anno ORDER BY fecha DESC", "fecha");
}
function cwcuenta($socio, $tipo, $estatus, $credito = 0){
	$sql = "SELECT codigo, cajalocal FROM socios_general WHERE codigo=$socio LIMIT 0,1";
	$resc = "";

	 	if ($credito!=0){
	 		$resc = " AND numero_solicitud=$credito ";
	 	} else {
	 		$resc = " AND numero_socio=$socio ";
	 	}
	$datos = obten_filas($sql);
	$socio_cw = "";
	$cuenta = "";
	$prefijo = "";
	/**
	 * Si el Socio es Menor a 37999
	 */
	if($datos[0]<=37999){
		$socio_cw = substr(("00" . $datos[1] . "0" . substr($datos[0], -3)), -6);

	} else {
		$socio_cw = $datos[0];
	}
	/**
	 * Si es automatizado se va a Automaticos
	 */
	if ($tipo==10){
		if ($estatus==20) {
			$cuenta = "111201" . $socio_cw;
		} else {
			$cuenta = "110501" . $socio_cw;
		}
	} else {
		if ($estatus==20) {
			$cuenta = "111203" . $socio_cw;
		} else {
			$cuenta = "110601" . $socio_cw;
		}
	}
	return  $cuenta;
}

function setCompacWtExportarPolizas($fecha_inicial, $fecha_final, $FolioInicial = 0, $FolioFinal = 999999, $mTipo ="todas"){
	$sucursal		= getSucursal();
	//Traducciones del ContPaqw
	$CWTipoMvto 	= array("1"=>1,"-1"=>"2");
	
	//Formato	:	polizas + fecha + sucursal;
	
	$mTmpFileAlias	= "$sucursal-polizas-" . date("Y-m-d") . "";
	$mNametmpFile 	= PATH_TMP . $mTmpFileAlias . ".txt";
	
	if(file_exists($mNametmpFile)) {
		$BKPFile = fopen($mNametmpFile, "a+");
	} else {
		//$mNametmpFile = tempnam (PATH_BACKUPS, "polizas" . date("Y-m-d") . $sucursal . ".sbk");
		$BKPFile = fopen($mNametmpFile, "a");
	}
	//filtros
	$wByTipo			= ( $mTipo == "todas" ) ? "" : " AND (`contable_polizas`.`tipopoliza` =" . $mTipo. ")  ";
	
	//Generar Polizas
	$FInicial			= $FolioInicial;
	$FFinal				= $FolioFinal;
	
	$sqlPol 			= "SELECT
						*
						FROM
							`contable_polizas` `contable_polizas` 
						WHERE
							(`contable_polizas`.`fecha` >='$fecha_inicial')
							AND
							(`contable_polizas`.`fecha` <='$fecha_final')
							AND
							(
							(`contable_polizas`.`numeropoliza` >=$FInicial) 
							AND
							(`contable_polizas`.`numeropoliza` <=$FFinal) 
							) $wByTipo ";
	//echo $sqlPol;
	
	$rs = mysql_query($sqlPol, cnnGeneral());
		if (!$rs){
			//Codigo de Control de Error
			saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|||Numero: " .mysql_errno() . "|||Instruccion SQL: \n ". $sqlPol);
		}
		while($rw = mysql_fetch_array($rs)){
			//Seleccionar los Movimientos
			$ejercicio			= $rw["ejercicio"];
			$periodo			= $rw["periodo"];
			$tipoPoliza			= $rw["tipopoliza"];
			$numeroPoliza		= $rw["numeropoliza"];
			$fechaPoliza		= $rw["fecha"];
			$conceptoPoliza		= $rw["concepto"];
			
			$WriteText	= "P " . date("Ymd", strtotime($fechaPoliza));
			$WriteText .= " " . $tipoPoliza;
			$WriteText .= " " . substr(str_pad($numeroPoliza, 8, "0", STR_PAD_LEFT), -8);
			$WriteText .= " 1 000 " . substr(str_pad($conceptoPoliza, 100, " " , STR_PAD_RIGHT),0, 100);
			$WriteText .= " 01 2 
";
			//Escribe la Poliza
			@fwrite($BKPFile, $WriteText);
			/*
3 Caracter para quien sabe.- Diario.- supongo
2 Caracter para quien sabe
1 Caracter Espacion
1 Caracter Para quien Sabe
1 Caracter Espacio
			*/
			$sqlMvtos = "SELECT
						`contable_movimientos`.* 
						FROM
							`contable_movimientos` `contable_movimientos` 
						WHERE
							(`contable_movimientos`.`ejercicio` =$ejercicio) AND
							(`contable_movimientos`.`periodo` =$periodo) AND
							(`contable_movimientos`.`tipopoliza` =$tipoPoliza) AND
							(`contable_movimientos`.`numeropoliza` =$numeroPoliza)
						ORDER BY `contable_movimientos`.`ejercicio`,
						`contable_movimientos`.`periodo`,
						`contable_movimientos`.`tipopoliza`,
						`contable_movimientos`.`numeropoliza`,
						`contable_movimientos`.`numeromovimiento`
						";
				$MRs = mysql_query($sqlMvtos, cnnGeneral());
					while($MRw = mysql_fetch_array($MRs)){
						$cuenta 		= $MRw["numerocuenta"];
						$referencia		= $MRw["referencia"];
						//Corrige la Cuenta de Cuadre
							if ($cuenta	== CUENTA_DE_CUADRE){
								$cuenta = "_CUADRE";
							}
							//Tipo M + espacio
							//Cuenta   20
							//Referencia 10
							//TipoMvto 2 espacios 1 Cargo 2 Abono
							//Importe 16 Alineado
							//espacio + 000 + espacio + "            0.00 "
							//concepto 30 + espacio
						$WriteMvto		 = "M " . substr(str_pad($cuenta, 20, " ", STR_PAD_RIGHT), 0, 20);
						$WriteMvto		.= " " . substr(str_pad($referencia, 10, " ", STR_PAD_RIGHT), 0, 10);
						$WriteMvto		.= " " . $CWTipoMvto[$MRw["tipomovimiento"]];
						$WriteMvto		.= " " . substr(str_pad($MRw["importe"], 16, " ", STR_PAD_LEFT), -16);
						$WriteMvto		.= " 000 " . "            0.00 " .  substr(str_pad($MRw["concepto"], 30, " ", STR_PAD_RIGHT), 0, 30) . " 
";
						@fwrite($BKPFile, $WriteMvto);
					}
			
		}
	@fclose($BKPFile);
	return "<a href=\"../utils/download.php?type=txt&download=$mTmpFileAlias&file=$mTmpFileAlias\" target=\"_blank\" class='boton'>Descargar Archivo de Polizas</a>";	
}
?>

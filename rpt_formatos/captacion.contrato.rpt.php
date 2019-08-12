<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	//$xErr		= new cError();
	$nivelmin = 2;
		if ($_SESSION["log_nivel"] < $nivelmin) {
			header ("location:../inicio.php");
			exit();
		}
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.captacion.inc.php";
include_once "../core/core.common.inc.php";
include_once "../core/core.error.inc.php";

$idcuenta	= ( isset($_GET["idcuenta"]) ) ? $_GET["idcuenta"] : $_GET["c"]; 
$documento	= ( isset($_GET["documento"]) ) ? $_GET["documento"] : $_GET["d"];

if ( !isset($idcuenta) ){
	echo $regresar;
	exit;
}
if ( !isset($documento) ){
	$documento		= 18;
}
$oficial			= elusuario($iduser);

//ini_set("display_errors", "on");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<!-- -->
<?php
echo getRawHeader();

$sqlcontrato 						= "SELECT * FROM captacion_cuentas where numero_cuenta=$idcuenta";
$datos_de_la_cuenta 				= obten_filas($sqlcontrato);
$idsocio 							= $datos_de_la_cuenta["numero_socio"];
$tasa 								= $datos_de_la_cuenta["tasa_otorgada"];
$tasa 								= $tasa * 100;

$xSoc								= new cSocio($idsocio);
$xSoc->init();
$DSoc								= $xSoc->getDatosInArray();
$domicilio_del_socio 				= $xSoc->getDomicilio();
$nombre_del_socio 					= $xSoc->getNombreCompleto();
$numero_de_socio 					= $idsocio;

$caja_local 						= eltipo("socios_cajalocal", $DSoc["cajalocal"]);

	/**
	 * Busca el Primer Deposito del Socio.
	 */
	$datos_primer_deposito 			= obten_filas("SELECT * FROM operaciones_mvtos
												WHERE docto_afectado=$idcuenta
												ORDER BY fecha_operacion LIMIT 0,1");
	$monto_inicial 					= setNoMenorQueCero($datos_primer_deposito[7]);
	$monto_inicial_letras 			= "";
	if ( $monto_inicial > 0){
		$monto_inicial_letras 			= convertirletras($monto_inicial);
	}
	$numero_dias 					= $datos_primer_deposito[29];
	$variable_lugar 				= $eacp_estado . ",  " . $eacp_municipio;
	$variable_fecha_actual 			= fecha_larga();
	$variable_tasa_otorgada 		= $datos_primer_deposito[28] * 100;
	$variable_fecha_vencimiento 	= fecha_larga($datos_primer_deposito[11]);
	$variable_oficial 				= $oficial;
	/**
	 *  Obtiene la Lista de Beneficiados
	 */
	$beneficiados					= "";
	$sql_beneficiados				= "SELECT * FROM socios_relaciones WHERE tipo_relacion=11 AND socio_relacionado=$idsocio LIMIT 0,100";
	$rs_beneficiados				= mysql_query($sql_beneficiados);
	
	while ($row_beneficiado = mysql_fetch_array($rs_beneficiados)) {
		$beneficiados = $beneficiados . "<li>$row_beneficiado[6] $row_beneficiado[7] $row_beneficiado[5]</li> ";
	}
	$variable_lista_beneficiados = "<ol>
				$beneficiados
			</ol>";
	/**
	 * Compara si existen Datos de Mancomunados
	 */
	if ($datos_de_la_cuenta["nombre_mancomunado1"] != "" & $datos_de_la_cuenta["nombre_mancomunado1"] != "_") {
			$nombre_mancomunados = "<br /><br /><br />" . $datos_de_la_cuenta["nombre_mancomunado1"] . " <br /> <br /><br />" . $datos_de_la_cuenta["nombre_mancomunado2"];
	} else {
			$nombre_mancomunados = "";
	}
	/**
	 * Empieza el Intercambio de variables en el contrato
	 */
	 $vars = array(
					"variable_numero_de_cuenta" => $idcuenta,
					"variable_nombre_del_socio" => $nombre_del_socio,
					"variable_numero_de_socio" => $numero_de_socio,
					"variable_domicilio_del_socio" => $domicilio_del_socio,
					"variable_nombre_de_la_entidad" => EACP_NAME,
					"variable_domicilio_de_la_entidad" => EACP_DOMICILIO_CORTO,
					"variable_monto_inicial_en_numero" => $monto_inicial,
					"variable_monto_inicial_en_letras" => $monto_inicial_letras,
					"variable_numero_de_dias" => $numero_dias,
					"variable_caja_local" => $caja_local,
					"variable_lugar" => $variable_lugar,
					"variable_fecha_actual" => $variable_fecha_actual,
					"variable_nombre_mancomunados" => $nombre_mancomunados,
					"variable_tasa_otorgada" => $variable_tasa_otorgada,
					"variable_fecha_de_vencimiento" => $variable_fecha_vencimiento,
					"variable_oficial" => $variable_oficial,
					"variable_titular_de_cobranza" => $titular_cobranza,
					"variable_lista_de_beneficiados" => $variable_lista_beneficiados,
					"variable_nombre_de_la_sociedad" => EACP_NAME,
					"variable_fecha_larga_actual" => fecha_larga(),
			 		"variable_rfc_del_socio" => $DSoc["rfc"],
					"variable_acta_notarial_de_poder_al_representante" => EACP_DOCTO_REP_LEGAL,
			 		"variable_rfc_de_la_entidad" => EACP_RFC
	 );



	$texto_contrato = contrato($documento, "texto_del_contrato");
			foreach ($vars as $key => $value) {
				$texto_contrato = str_replace($key, $value, $texto_contrato);
			}
	echo $texto_contrato;

echo getRawFooter();
?>
</body>
</html>

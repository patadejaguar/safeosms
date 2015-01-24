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
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$cuenta = parametro("i", DEFAULT_CUENTA_CORRIENTE, MQL_INT);
$cuenta = parametro("cuenta", $cuenta, MQL_INT);

	if (!$cuenta){
		echo $regresar;
		exit;
	}

$oficial = elusuario($iduser);

//ini_set("display_errors", "on");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<style>
body {
		padding-top:1in;
		padding-bottom:1in;
		padding-left:1in;
		padding-right:1in;	
	}
</style>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<!-- -->
<?php
echo getRawHeader();

$sqlcontrato 					= "SELECT
									`socios_general`.*,
									`captacion_cuentas`.*
								FROM
									`captacion_cuentas` `captacion_cuentas`
										INNER JOIN `socios_general` `socios_general`
										ON `captacion_cuentas`.`numero_socio` = `socios_general`.`codigo`
								WHERE
									(`captacion_cuentas`.`numero_cuenta` =$cuenta)";
$datos_de_la_cuenta 			= obten_filas($sqlcontrato);
$idsocio 						= $datos_de_la_cuenta["numero_socio"];
$tasa 							= $datos_de_la_cuenta["tasa_otorgada"];
$tasa 							= $tasa * 100;
$saldo							= $datos_de_la_cuenta["saldo_cuenta"];
	$domicilio_del_socio 		= sociodom($idsocio);
	$nombre_del_socio 			= getNombreSocio($idsocio);
	$numero_de_socio 			= $idsocio;

	/**
	 * Busca el Primer Deposito del Socio.
	 */
	$datos_primer_deposito 			= obten_filas("SELECT * FROM operaciones_mvtos WHERE docto_afectado=$cuenta ORDER BY fecha_operacion LIMIT 0,1");
	$monto_inicial 					= $datos_primer_deposito[7];
	$monto_inicial_letras 			= convertirletras($monto_inicial);
	$numero_dias 					= $datos_primer_deposito[29];
	$variable_lugar 				= $eacp_estado . ",  " . $eacp_municipio;
	$variable_fecha_actual 			= fecha_larga();
	$variable_tasa_otorgada 		= $datos_primer_deposito[28] * 100;
	$variable_fecha_vencimiento 	= fecha_larga($datos_primer_deposito[11]);
	$variable_oficial 				= $oficial;
	//Datos del Credito
	$credito						= $datos_de_la_cuenta["numero_solicitud"];
	$xCred							= new cCredito($credito, $numero_de_socio);
	$xCred->initCredito();
	$DCred							= $xCred->getDatosDeCredito();
	$SQLTCred 						= "SELECT * FROM creditos_modalidades
										WHERE idcreditos_modalidades=" . $DCred["tipo_credito"];
	$tipo_de_credito 				= mifila($SQLTCred, "descripcion_modalidades");
	//Caja local por SQL
	$SQLCL = "SELECT idsocios_cajalocal, descripcion_cajalocal, ultimosocio, region, sucursal
    		FROM socios_cajalocal
    		WHERE
    		idsocios_cajalocal=". $datos_de_la_cuenta["cajalocal"];
	$nombre_caja_local 				= mifila($SQLCL, "descripcion_cajalocal");
	$caja_local 					= $datos_de_la_cuenta["cajalocal"];
	/**
	 *  Obtiene la Lista de Beneficiados
	 */
	$beneficiados 			= "";
	$sql_beneficiados 		= "SELECT * FROM socios_relaciones WHERE tipo_relacion=11 AND socio_relacionado=$idsocio AND credito_relacionado = $cuenta LIMIT 0,100";
	$rs_beneficiados 		= mysql_query($sql_beneficiados);
	while ($row_beneficiado = mysql_fetch_array($rs_beneficiados)) {
		$beneficiados 		= $beneficiados . "<li>$row_beneficiado[6] $row_beneficiado[7] $row_beneficiado[5]</li> ";
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
					"variable_numero_de_cuenta" => $cuenta,
					"variable_nombre_del_socio" => $nombre_del_socio,
					"variable_numero_de_socio" => $numero_de_socio,
					"variable_domicilio_del_socio" => $domicilio_del_socio,
					"variable_nombre_de_la_entidad" => EACP_NAME,
					"variable_domicilio_de_la_entidad" => EACP_DOMICILIO_CORTO,
					"variable_monto_inicial_en_numero" => $monto_inicial,
					"variable_monto_inicial_en_letras" => $monto_inicial_letras,
					"variable_numero_de_dias" => $numero_dias,
					"variable_caja_local" => $caja_local,
					"variable_nombre_caja_local" => $nombre_caja_local,
					"variable_lugar" => $variable_lugar,
					"variable_fecha_actual" => $variable_fecha_actual,
					"variable_nombre_mancomunados" => $nombre_mancomunados,
					"variable_tasa_otorgada" => $variable_tasa_otorgada,
					"variable_fecha_de_vencimiento" => $variable_fecha_vencimiento,
					"variable_oficial" => $variable_oficial,
					"variable_titular_de_cobranza" => $titular_cobranza,
					"variable_lista_de_beneficiados" => $variable_lista_beneficiados,
					"variable_nombre_de_la_sociedad" => EACP_NAME,
					"variable_monto_ministrado"		=> getFMoney($DCred["monto_autorizado"]),
					"variable_tasa_mensual_de_interes_ordinario" => ( ($DCred["tasa_interes"] / 12) *  100),
					"variable_credito_fecha_de_vencimiento" => getFechaLarga($DCred["fecha_vencimiento"]),
					"variable_monto_garantia_liquida" => getFMoney($saldo),
					"variable_credito_fecha_de_ministracion" => getFechaLarga($DCred["fecha_ministracion"]),
					"variable_tipo_de_credito" => $tipo_de_credito
	 );

	$texto_contrato = contrato(6, "texto_del_contrato");
			foreach ($vars as $key => $value) {
			$texto_contrato = str_replace($key, $value, $texto_contrato);
			}
	echo $texto_contrato;

echo getRawFooter();
?>
</body>
</html>

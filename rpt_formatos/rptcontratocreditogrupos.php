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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.config.inc.php";

$oficial = elusuario($iduser);
$idsolicitud = $_GET["solicitud"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>CONTRATO DE CREDITO</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<!-- -->
<?php
	echo getRawHeader();

	if (!$idsolicitud) {
		exit($msg_rpt_exit . $fhtm);
	}


	$SQLDCred = "SELECT
	`creditos_solicitud`.*,
	`creditos_tipoconvenio`.*,
	`socios_general`.*
FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `socios_general` `socios_general`
		ON `creditos_solicitud`.`numero_socio` =
		`socios_general`.`codigo`
			INNER JOIN `creditos_tipoconvenio`
			`creditos_tipoconvenio`
			ON `creditos_solicitud`.`tipo_convenio` =
			`creditos_tipoconvenio`.
			`idcreditos_tipoconvenio`
WHERE
	(`creditos_solicitud`.`numero_solicitud` =$idsolicitud)
	";
	$DCred						= obten_filas($SQLDCred);

	$numero_de_socio			= $DCred["codigo"];
	$domicilio_del_socio		= domicilio($DCred["codigo"])	;
	//Info del Credito
	$svar_info_cred				= "";
	$tblInfCred 				= new cFicha(iDE_CREDITO, $idsolicitud);
	$tblInfCred->setTableWidth("80%");
	$svar_info_cred 			= $tblInfCred->show(true);
	//Lista de Beneficiados
	$lst_beneficiados = "";
	$SQLCBen = "SELECT
	`socios_relacionestipos`.`descripcion_relacionestipos` AS 'relacion',
	`socios_relaciones`.`nombres`,
	`socios_relaciones`.`apellido_paterno`,
	`socios_relaciones`.`apellido_materno`,
	`socios_consanguinidad`.`descripcion_consanguinidad` AS 'consaguinidad'
FROM
	`socios_relaciones` `socios_relaciones`
		INNER JOIN `socios_consanguinidad`
		`socios_consanguinidad`
		ON `socios_relaciones`.`consanguinidad` =
		`socios_consanguinidad`.`idsocios_consanguinidad`
			INNER JOIN `socios_relacionestipos`
			`socios_relacionestipos`
			ON `socios_relaciones`.`tipo_relacion` =
			`socios_relacionestipos`.
			`idsocios_relacionestipos`
WHERE
	(`socios_relaciones`.`socio_relacionado` =$numero_de_socio) AND
	(`socios_relaciones`.`credito_relacionado` =$idsolicitud)
	AND
	(`socios_relaciones`.`tipo_relacion`=11)";
	$tblCBen 				= new cTabla($SQLCBen);
	$tblCBen->setWidth(80);
	$lst_beneficiados 		= $tblCBen->Show();
	//Caja local por SQL
	$SQLCL = "SELECT idsocios_cajalocal, descripcion_cajalocal, ultimosocio, region, sucursal
    		FROM socios_cajalocal
    		WHERE
    		idsocios_cajalocal=". $DCred["cajalocal"];
	$caja_local 				= mifila($SQLCL, "descripcion_cajalocal");
	//Tipo de Credito(COMERCIAL, VIVIENDA) por SQL


	//Plan de Pago segun SQL
	$splan_pagos = "";
	$SQLPPagos = "SELECT
	DATE_FORMAT(MAX(`operaciones_mvtos`.`fecha_afectacion`),'%d-%m-%Y') AS `fecha`,
	SUM(`operaciones_mvtos`.`afectacion_real`)  AS `monto`
FROM
	`operaciones_recibos` `operaciones_recibos`
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
		ON `operaciones_recibos`.`idoperaciones_recibos` =
		`operaciones_mvtos`.`recibo_afectado`
WHERE
	(`operaciones_recibos`.`numero_socio` =$numero_de_socio) AND
	(`operaciones_recibos`.`docto_afectado` = $idsolicitud) AND
	(`operaciones_recibos`.`tipo_docto` =11)
GROUP BY
	`operaciones_mvtos`.`periodo_socio`";
	
	$tblPlan 						= new cTabla($SQLPPagos);
	$splan_pagos 					= $tblPlan->Show("CALENDARIO DE PAGOS");
	//Otros Datos
	$monto_ministrado 				= $DCred["monto_autorizado"];
	$tasa_interes_mensual_ordinario	= ($DCred["tasa_interes"] / 12) * 100;
	$tasa_interes_anual_ordinario	= $DCred["tasa_interes"];
	$fecha_de_vencimiento			= $DCred["fecha_vencimiento"];
	$tasa_garantia_liquida			= $DCred["porciento_garantia_liquida"] * 100;
	$monto_garantia_liquida			= $monto_ministrado * $tasa_garantia_liquida;
	$tasa_interes_mensual_moratorio	= ($DCred["tasa_moratorio"] / 12) * 100;
	$dias_del_credito				= $DCred["dias_autorizados"];
	$meses_del_credito				= sprintf ("%02d", ($dias_del_credito / 30.416666666666666666666));
	$fecha_de_ministracion			= $DCred["fecha_ministracion"];
	//Tipo de Credito por SQL
	$SQLTCred 						= "SELECT * FROM creditos_modalidades WHERE idcreditos_modalidades=" . $DCred["tipo_credito"];
	$tipo_de_credito 				= mifila($SQLTCred, "descripcion_modalidades");
	//Datos del Grupo Solidarios por SQL
	$SQLGAsoc 						= "SELECT * FROM socios_grupossolidarios
										WHERE idsocios_grupossolidarios=" . $DCred["grupo_asociado"];
	$InfoGrupo						= obten_filas($SQLGAsoc);
	$nombre_rep_social				= $InfoGrupo["representante_nombrecompleto"];
	$codigo_rep_social				= $InfoGrupo["representante_numerosocio"];
	$nombre_voc_vigila				= $InfoGrupo["vocalvigilancia_nombrecompleto"];
	$nombre_del_grupo				= $InfoGrupo["nombre_gruposolidario"];
	$nivel_ministracion				= $InfoGrupo["nivel_ministracion"];
	$domicilio_rep_social			= domicilio($codigo_rep_social);

	$tabla_asociadas				= "";
	$lista_asociadas				= "";
		if($DCred["grupo_asociado"]!=99){
			$SQL_get_grupo = "SELECT
								`socios_general`.`codigo`,
								CONCAT(`socios_general`.`nombrecompleto`, ' ',
								`socios_general`.`apellidopaterno`, ' ',
								`socios_general`.`apellidomaterno`) AS 'nombre_completo'
							FROM
							`socios_general` `socios_general`
							WHERE
								(`socios_general`.`grupo_solidario` =" . $DCred["grupo_asociado"] . ")";
				$rsg	= mysql_query($SQL_get_grupo, cnnGeneral());
				$il		= 0;
					while ($rwt = mysql_fetch_array($rsg)) {
						if( $il == 0  ){
							$lista_asociadas .= "" . $rwt["nombre_completo"];
						} else {
							$lista_asociadas .= ", " . $rwt["nombre_completo"];
						}
						$il++;
					}
		}

//variable_firmas_de_obligados_solidarios
	$vars = array(
			"variable_nombre_del_socio" => $DCred["apellidopaterno"] . " " . $DCred["apellidomaterno"] . " " . $DCred["nombrecompleto"],
			"variable_nombre_de_la_sociedad" => EACP_NAME,
			"variable_nombre_de_la_entidad" => EACP_NAME,
			"variable_domicilio_del_socio" => $domicilio_del_socio,
			"variable_documento_de_constitucion_de_la_sociedad" => EACP_DOCTO_CONSTITUCION,
			"variable_rfc_de_la_entidad" => EACP_RFC,
			"variable_rfc_del_socio" => $DCred["rfc"],
			"variable_curp_del_socio" => $DCred["curp"],
			"variable_nombre_del_representante_legal_de_la_sociedad" => EACP_REP_LEGAL,
			"variable_informacion_del_credito" => $svar_info_cred,
			"variable_domicilio_de_la_entidad" => EACP_DOMICILIO_CORTO,
			"variable_acta_notarial_de_poder_al_representante" => EACP_DOCTO_REP_LEGAL,
			"variable_lista_de_beneficiados" => $lst_beneficiados,
			"variable_numero_de_socio" => $numero_de_socio,
			"variable_nombre_caja_local" => $caja_local,
			"variable_tipo_de_credito" => $tipo_de_credito,
			"variable_monto_ministrado" => getFMoney($monto_ministrado),
			"variable_tasa_mensual_de_interes_ordinario" => $tasa_interes_mensual_ordinario,
			"variable_credito_fecha_de_vencimiento" => $fecha_de_vencimiento,
			"variable_monto_garantia_liquida" => getFMoney($monto_garantia_liquida),
			"variable_tasa_mensual_de_interes_moratorio" => $tasa_interes_mensual_moratorio . " %",
			"variable_tasa_de_garantia_liquida" => $tasa_garantia_liquida . " %",
			"variable_plan_de_pagos" => $splan_pagos,
			"variable_horario_de_trabajo_de_la_entidad"	=> EACP_HORARIO_DE_TRABAJO,
			"variable_testigo_del_acto" => $oficial,
			"variable_fecha_larga_actual" => fecha_larga(),
			"variable_nombre_de_presidente_de_vigilancia_de_la_entidad"=>EACP_PDTE_VIGILANCIA,
			"variable_nombre_de_la_representante_social" =>$nombre_rep_social,
			"variable_listado_de_integrantes" => $lista_asociadas,
			"variable_nombre_de_la_vocal_de_vigilancia" => $nombre_voc_vigila,
			"variable_nombre_del_grupo_solidario" => $nombre_del_grupo,
			"variable_domicilio_de_la_representante_social" => $domicilio_rep_social,
			"variable_meses_de_duracion_del_credito" => $meses_del_credito,
			"variable_en_letras_monto_ministrado" => convertirletras($monto_ministrado),
			"variable_grupo_nivel_ministracion" => $nivel_ministracion,
			"variable_credito_fecha_de_ministracion" => getFechaLarga($fecha_de_ministracion)
	);
	$texto_contrato = contrato(5, "texto_del_contrato");
			foreach ($vars as $key => $value) {
			$texto_contrato = str_replace($key, $value, $texto_contrato);
			}
	echo $texto_contrato;
echo getRawFooter();
?>
</body>
</html>
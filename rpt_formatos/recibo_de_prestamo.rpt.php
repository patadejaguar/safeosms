<?php
/**
 * @since 31/03/2008
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0.1
 *  01/Abril/2008
 * 		- cambios en la fecha
 * 		- Agregar Documento de Destino
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
	
$xHP		= new cHPage("Recibo de Prestamo", HP_RECIBO);
$oficial 	= elusuario($iduser);
$xT		= new cTipos();
$recibo 	= (isset($_GET["recibo"])) ? $_GET["recibo"]: false;

if($recibo != false){	
echo $xHP->getHeader();
?>
<style>
body {
		padding-top:1in;
		padding-bottom:1in;
		padding-left:1in;
		padding-right:1in;	
	}
</style>
<?php
echo $xHP->setBodyinit("javascript:window.print()");

$arrQ						= array( "aumento" => 1, "disminucion" => -1, "ninguna" => 0);
//capturar datos del recibo
$xRec						= new cReciboDeOperacion(false, false, $recibo);

$DRec 						= $xRec->getDatosInArray();
$idsocio 					= $DRec["numero_socio"];
$numero_de_socio				= $DRec["numero_socio"];
$afectaCaja					= $arrQ[ $DRec["afectacion_en_flujo_efvo"] ];
//datos del socio
$cSoc						= new cSocio($numero_de_socio);
$DSoc						= $cSoc->getDatosInArray();
$numero_caja_local				= $DSoc["cajalocal"];
$cCL						= new cCajaLocal( $numero_caja_local );
$caja_local					= $cCL->getNombre();
$variable_nombre_del_socio			= ($idsocio == DEFAULT_SOCIO ) ? $DRec["cadena_distributiva"] : $cSoc->getNombreCompleto();
$tipo_de_pago					= $DRec["tipo_pago"];
//$variable_nombre_del_cajero
//
$tipoderecibo					= $DRec["tipo_docto"];
$docto 						= $DRec["docto_afectado"];
$origen						= $DRec["origen"];

$variable_tipo_de_recibo 			= eltipo("operaciones_recibostipo", $tipoderecibo);

$totaloperacion 				= $DRec["total_operacion"];

//<----------------------------------------------------------

$variable_monto_del_recibo 			= number_format($totaloperacion, 2, '.', ',');
$variable_monto_del_recibo_en_letras 		= convertirletras($totaloperacion);
$variable_fecha_del_recibo			= "";
$variable_tipo_de_pago				= "";
$xCajero					= new cSystemUser( $DRec["idusuario"] );
$variable_nombre_del_cajero			= $xCajero->getNombreCompleto();
$variable_observacion_del_recibo 		= $DRec["observacion_recibo"];

$xSuc						= new cSucursal($DRec["sucursal"]);
$xSuc->init();
$DSuc						= $xSuc->getDatosInArray();

$variable_lugar					= $DSuc["municipio"] . ", " . $DSuc["estado"];
$variable_marca_de_tiempo			= date("Ymd:His");

/* -----------------Verifica si es solicitud, si es imprime el saldo actual */
if ( ($origen == "colocacion") OR ($origen == "mixto") ){
	$DD					= $xRec->getInfoDoctoInArray();
	if ( ($DD != false) AND (is_array($DD)) ) {
		$sdoctacred 			= $DD["saldo_actual"];
		$sdov 				= $DD["saldo_vencido"];
		$nota 				= "Capital Insoluto: $ " . getFMoney($sdoctacred) . "|";
		$tipoDocto			= eltipo("creditos_modalidades", $DD["tipo_credito"]);
		$estatDocto			= eltipo("creditos_estatus", $DD["estatus_actual"]);
	}
}
//inicializa variables vacias
$svar_info_cred					= "_NO_APLICA_";
$variable_lista_beneficiados			= "_NO_APLICA_";
$tipo_de_credito				= "_NO_APLICA_";
$monto_ministrado				= "_NO_APLICA_";
$tasa_interes_mensual_ordinario			= "_NO_APLICA_";
$fecha_de_vencimiento				= "_NO_APLICA_";
$monto_garantia_liquida				= "_NO_APLICA_";
$tasa_interes_mensual_moratorio			= "_NO_APLICA_";
$tasa_garantia_liquida				= "_NO_APLICA_";
$splan_pagos					= "_NO_APLICA_";
$nombre_rep_social				= "_NO_APLICA_";	//pdta del grupo solidario
$lista_asociadas				= "_NO_APLICA_";
$nombre_voc_vigila				= "_NO_APLICA_";
$domicilio_rep_social				= "_NO_APLICA_";
$nombre_del_grupo				= "_NO_APLICA_";
$meses_del_credito				= "_NO_APLICA_";
$monto_ministrado				= "_NO_APLICA_";
$fecha_de_ministracion				= "_NO_APLICA_";
$ficha_socio					= "_NO_APLICA_";
$fichas_de_avales				= "_NO_APLICA_";
$fichas_de_respsolidarios			= "_NO_APLICA_";
$firmas_de_respsolidarios			= "_NO_APLICA_";
//Captacion
$numero_de_cuenta				= "_NO_APLICA_";
$nombre_mancomunados				= "_NO_APLICA_";
$variable_tasa_otorgada				= "_NO_APLICA_";
$variable_fecha_vencimiento			= "_NO_APLICA_";
$numero_dias					= "_NO_APLICA_";
$monto_inicial_letras				= "_NO_APLICA_";
$monto_inicial					= "_NO_APLICA_";
$monto_letras					= "_NO_APLICA_";
$variable_oficial				= "_NO_APLICA_";
$descripcion_cajalocal				= "_NO_APLICA_";

$xFDE								= new cFecha(0, $xRec->getFechaDeRecibo());
$fecha_larga_de_documento		= $xFDE->getFechaLarga();

 	$vars = array(
			"variable_nombre_del_socio" => $variable_nombre_del_socio ,
			"variable_nombre_de_la_sociedad" => EACP_NAME,
			"variable_nombre_de_la_entidad" => EACP_NAME,
			"variable_domicilio_del_socio" => trim( substr($cSoc->getDomicilio(), 0, 60) ),
			"variable_documento_de_constitucion_de_la_sociedad" => EACP_DOCTO_CONSTITUCION,
			"variable_rfc_de_la_entidad" => EACP_RFC,
			"variable_rfc_del_socio" => $DSoc["rfc"],
			"variable_curp_del_socio" => $DSoc["curp"],
			"variable_nombre_del_representante_legal_de_la_sociedad" => EACP_REP_LEGAL,
			"variable_informacion_del_credito" => $svar_info_cred,
			"variable_domicilio_de_la_entidad" => EACP_DOMICILIO_CORTO,
			"variable_acta_notarial_de_poder_al_representante" => EACP_DOCTO_REP_LEGAL,
			"variable_lista_de_beneficiados" => $variable_lista_beneficiados,
			"variable_numero_de_socio" => $numero_de_socio,
			"variable_nombre_caja_local" => $caja_local,
 			"variable_caja_local" => $numero_caja_local,
			"variable_tipo_de_credito" => $tipo_de_credito,
			"variable_monto_ministrado" => getFMoney($monto_ministrado),
			"variable_tasa_mensual_de_interes_ordinario" => $tasa_interes_mensual_ordinario,
			"variable_credito_fecha_de_vencimiento" => getFechaLarga($fecha_de_vencimiento),
			"variable_monto_garantia_liquida" => getFMoney($monto_garantia_liquida),
			"variable_tasa_mensual_de_interes_moratorio" => $tasa_interes_mensual_moratorio . " %",
			"variable_tasa_de_garantia_liquida" => $tasa_garantia_liquida . " %",
			"variable_plan_de_pagos" => $splan_pagos,
			"variable_horario_de_trabajo_de_la_entidad"	=> EACP_HORARIO_DE_TRABAJO,
			"variable_testigo_del_acto" => $oficial,
 			
 			"variable_docto_fecha_larga_actual" => $fecha_larga_de_documento,
 			
			"variable_fecha_larga_actual" => fecha_larga(),
			"variable_nombre_de_presidente_de_vigilancia_de_la_entidad"=>EACP_PDTE_VIGILANCIA,
			"variable_nombre_de_la_representante_social" =>$nombre_rep_social,
			"variable_listado_de_integrantes" => $lista_asociadas,
			"variable_nombre_de_la_vocal_de_vigilancia" => $nombre_voc_vigila,
			"variable_nombre_del_grupo_solidario" => $nombre_del_grupo,
			"variable_domicilio_de_la_representante_social" => $domicilio_rep_social,
			"variable_meses_de_duracion_del_credito" => $meses_del_credito,
			"variable_en_letras_monto_ministrado" => convertirletras($monto_ministrado),
			"variable_credito_fecha_de_ministracion" => getFechaLarga($fecha_de_ministracion),
			"variable_informacion_del_socio" => $ficha_socio,
			"variable_avales_en_fichas" => $fichas_de_avales,
			"variable_responsable_solidario_en_fichas"  => $fichas_de_respsolidarios,
			"variable_firmas_de_obligados_solidarios" => $firmas_de_respsolidarios,
 	
		 	"variable_numero_de_cuenta" => $numero_de_cuenta,
		 	"variable_oficial" => $variable_oficial,
		 	"variable_lugar" => $variable_lugar,
		 	"variable_lugar_actual" => $variable_lugar,
		 	"variable_monto_inicial_en_numero" => $monto_inicial,
			"variable_monto_inicial_en_letras" => $monto_inicial_letras,
			"variable_numero_de_dias" => $numero_dias,
			"variable_fecha_de_vencimiento" => $variable_fecha_vencimiento,
			"variable_nombre_mancomunados" => $nombre_mancomunados,
			"variable_tasa_otorgada" => $variable_tasa_otorgada,
 	
		 	"variable_nombre_del_cajero" => $variable_nombre_del_cajero,
		 	"variable_fecha_del_recibo" => $variable_fecha_del_recibo,
 			"variable_monto_del_recibo_en_letras" => $variable_monto_del_recibo_en_letras,
 	
 			"variable_monto_del_recibo" => $variable_monto_del_recibo,
		 	
	 		"variable_tipo_de_recibo" => $variable_tipo_de_recibo,
	 		"variable_tipo_de_pago" => $variable_tipo_de_pago,
	 		"variable_observacion_del_recibo" => $variable_observacion_del_recibo,
 			"variable_marca_de_tiempo" => $variable_marca_de_tiempo,
			
			"variable_encabezado_de_reporte" => getRawHeader(),
			"variable_pie_de_reporte" => getRawFooter()
 	
	);
	/*,
 			"variable_operacion_nombre_corto" => $variable_operacion_nombre_corto*/
		$texto_contrato = contrato(200, "texto_del_contrato");
		//$tamTexto		= strlen($texto_contrato);
		//Buscar la parte que define el formato de Movimientos
		

	foreach ($vars as $key => $value) {
		$texto_contrato = str_replace($key, $value, $texto_contrato);
	}
	
	echo $texto_contrato;


?>
</body>
<script type="text/javascript">
function jsRevalidarRecibo(){
	document.location = "../rpt_formatos/recibo.rpt.php?recibo=" + <?php echo $recibo; ?>;
}
</script>
</html>
<?php
}
?>
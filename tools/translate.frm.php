<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

function jsaGetTrans($idtabla, $idcampo){
	$xObj		= new cSQLTabla($idtabla);
	$str		= "-- Traducir $idcampo en la Tabla $idtabla\r\n";
	
	$source 	= 'es';
	$target 	= 'pt';
	
	
	if( $xObj->obj() == null){
		$err	= true;
		$str	.= "ERROR";
	} else {
		$obj	= $xObj->obj();
		//$obj	= new cAml_alerts();
		
		$sel	= $obj->query()->select()->exec();
		$key	= $obj->getKey();
		
		foreach ($sel as $rw){
			$trans = new \Statickidz\GoogleTranslate();
			
			$text	= $rw[$idcampo];
			$vkey	= $rw[$key];
			
			$word 	= $trans->translate($source, $target, $text);
			
			$str	.= "UPDATE $idtabla SET $idcampo ='$word' WHERE $key='$vkey';\r\n";
			
			//$obj->setData($rw);
			
		}
	}	
	
	return $str;
}
//$tab 		= new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();

$jxc ->exportFunction('jsaGetTrans', array('idtabla', 'idcampo'), "#idtraduce");
$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);


$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

//require_once ('vendor/autoload.php');
//use \Statickidz\GoogleTranslate;



$sql		= "SELECT TABLE_NAME, CONCAT(TABLE_NAME, '.-', TABLE_COMMENT) AS 'name' FROM information_schema.tables WHERE table_schema='" . MY_DB_IN . "' AND TABLE_TYPE != 'VIEW'
AND TABLE_NAME NOT LIKE 'tmp_%'
AND TABLE_NAME NOT LIKE 'temp_%'
AND TABLE_NAME NOT LIKE 'vv_%'
AND TABLE_NAME NOT LIKE 'vw_%'";
//$xT			= new cTabla($sql);
//$xT->setEventKey("jsGetTable");
$xNSel			= new cSelect("idtabla", "idtabla", $sql);
$xNSel->setEsSql();
$xNSel->setNoMayus();
$xNSel->setLabel("TR.TABLA");
$lbl			= $xNSel->getLabel();
$xNSel->setLabel("");
$arrV	= explode(",", "aml_alerts,aml_listanegra_int,aml_perfil_egresos_por_persona,aml_personas_descartadas,aml_riesgo_perfiles,aml_riesgo_producto,aml_risk_register,bancos_cuentas,bancos_entidades,bancos_operaciones,captacion_cuentas,captacion_sdpm_historico,captacion_subproductos,captacion_tasas,catalogos_localidades,contable_catalogo,contable_catalogo_perfil,contable_catalogorelacion,contable_equivalencias,contable_movimientos,contable_polizas,contable_polizas_perfil,contable_polizas_proforma,contable_saldos,creditos_a_final_de_plazo,creditos_abonos_por_mes,creditos_datos_originacion,creditos_destino_detallado,creditos_eventos,creditos_firmantes,creditos_flujoefvo,creditos_garantias,creditos_letras_del_dia,creditos_letras_pendientes,creditos_lineas,creditos_montos,creditos_nievelesdereserva,creditos_nivelesdegrupo,creditos_notarios,creditos_otros_datos,creditos_parametros_negociados,creditos_periodos,creditos_plan_de_pagos,creditos_preclientes,creditos_presupuestos,creditos_productos_costos,creditos_productos_etapas,creditos_productos_otros_parametros,creditos_productos_promo,creditos_productos_req,creditos_rechazados,creditos_reconvenio,creditos_sdpm_historico,creditos_sic_notas,creditos_solicitud,creditos_tipoconvenio,eacp_config_bases_de_integracion,eacp_config_bases_de_integracion_miembros,empresas_cobranza,empresas_operaciones,entidad_autorizaciones,entidad_calificacion,entidad_configuracion,entidad_creditos_proyecciones,entidad_pagos_perfil,entidad_reglas,entidad_reportes_props,general_colonias,general_folios,general_estados,general_folios,general_import,general_log,general_municipios,general_sql_stored,general_sucursales,general_tmp,historial_de_pagos,leasing_activos,leasing_bonos,leasing_comisiones,leasing_originadores,leasing_plazos,leasing_rentas,leasing_residual,leasing_tasas,leasing_usuarios,listado_de_ingresos,mercadeo_campana,mercadeo_envios,operaciones_archivo_de_facturas,operaciones_mvtos");
foreach ($arrV as $id => $vv){
	$xNSel->setEliminarOption($vv);
}

$arrV	= explode(",", "operaciones_mvtos_arch,operaciones_promociones,operaciones_recibos,operaciones_recibos_arch,originacion_leasing,personas_aseguradoras,personas_checklist,personas_consulta_centralriesgos,personas_consulta_lista,personas_datos_colegiacion,personas_datos_extranjero,personas_documentacion,personas_morales_anx,personas_operaciones_recursivas,personas_pagos_perfil,personas_pagos_plan,personas_perfil_transaccional,personas_proveedores,personas_relaciones_recursivas,programacion_de_avisos,seguimiento_compromisos,seguimiento_llamadas,seguimiento_notificaciones,sistema_avisos_db,sistema_eliminados,sistema_equivalencias,sistema_permisos,sistema_programacion_de_avisos,socios_aeconomica_dependencias,socios_aportaciones,socios_baja,socios_cajalocal,socios_firmas,socios_general,socios_grupossolidarios,socios_memo,socios_otros_parametros,socios_patrimonio,socios_region,socios_relaciones,socios_scoring_simple,socios_vivienda,t_03f996214fba4a1d05a68b18fece8e71,tesoreria_caja_arqueos,tesoreria_cajas,tesoreria_cajas_movimientos,tesoreria_valoracion_diaria,tmp_colonias_activas,tmp_creditos_abonos_totales,tmp_creditos_mensuales_cnivelsalarial,tmp_creds_prox_letras,tmp_personas_aport_cal,tmp_personas_domicilios,tmp_personas_estadisticas,tmp_personas_extranjeras,tmp_personas_geografia,tmp_recibos_datos_bancarios,usuarios_web,usuarios_web_connected,usuarios_web_notas,vehiculos_gps,vehiculos_gps_costeo,vehiculos_tenencia");
foreach ($arrV as $id => $vv){
	$xNSel->setEliminarOption($vv);
}
$xNSel->addEvent("onchange", "jsLoadCampos()");

$xFRM->addDivSolo($lbl, $xNSel->get(false), "tx14", "tx34");


$xTSel	= new cSelect("idcampo", "idcampo", "SELECT COLUMN_NAME  FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'aml_instrumentos_financieros'  AND table_schema = '" . MY_DB_IN . "'");
$xTSel->setEsSql();
$xTSel->setNoMayus();
$xTSel->setLabel("TR.CAMPO");

$lbl			= $xTSel->getLabel();
$xTSel->setLabel("");

$xFRM->addDivSolo($lbl, $xTSel->get(false), "tx14", "tx34");

//$xFRM->addTag($result);

$xFRM->OTextArea("idtraduce", "", "Parche");

//$xFRM->addAviso("", "idmsg");

$xFRM->OButton("TR.TRADUCIR", "jsaGetTrans()", $xFRM->ic()->EJECUTAR);
echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
<script>
var xG	= new Gen();

function jsLoadCampos(){
	var idt	= $("#idtabla").val();
	var sql	= base64.encode("SELECT COLUMN_NAME  FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" + idt +  "'  AND table_schema = '<?php echo MY_DB_IN; ?>'");
	xG.DataList({url:"../svc/datos.svc.php?q=" +  sql, id: "idcampo", label : "COLUMN_NAME", key : "COLUMN_NAME"});
	//SHOW COLUMNS FROM socios_vivienda FROM nexum;
}
</script>
<?php

$xHP->fin();
?>
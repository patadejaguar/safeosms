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
$xHP		= new cHPage("TR.OPCIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
$xCatConf	= new cConfigCatalogo();
$xRuls		= new cReglaDeNegocio();

$SyncApp	= $xRuls->getValorPorRegla($xRuls->reglas()->SYNC_APP);		//regla de negocio
$SyncAML	= $xRuls->getValorPorRegla($xRuls->reglas()->SYNC_AML_MIG);		//regla de negocio

//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
function jsaActualizarParam($id, $v){
	$xConf		= new cConfiguration();
	$xCatConf	= new cConfigCatalogo();
	$xSAFE		= new cSAFEData();
	
	$res		= $xConf->set($id, $v);
	switch ($id){
		case $xCatConf->MODULO_LEASING_ACTIVADO:
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModArrendamientoDA($enable);
			break;
		case $xCatConf->MODULO_AML_ACTIVADO:
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModAML_DA($enable);
			
			break;
		case $xCatConf->MODULO_CAJA_ACTIVADO:
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModTesoreriaDisEn($enable);
			break;
		case $xCatConf->MODULO_CAPTACION_ACTIVADO:
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModCaptacionDA($enable);
			break;
		case $xCatConf->MODULO_SEGUIMIENTO_ACTIVADO:
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModSeguimientoDA($enable);
			break;
		case $xCatConf->MODULO_CONTABILIDAD_ACTIVADO:
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModContableDA($enable);
			break;
		case "personas_controlar_por_grupos":
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModGruposDisEn($enable);
			break;
		case "personas_controlar_por_aportaciones":
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModAportacionesDisEn($enable);
			break;
		case "personas_controlar_por_empresas":
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModNominasDisEn($enable);
			break;
		case "credito_registrar_estados_de_credito":
			$enable	= ($v == "true") ? true : false;
			$xSAFE->setModEstadosCredsDisEn($enable);
			break;
	}
	return ($res == false) ? "FALLO" : "EXITO";
}
$jxc ->exportFunction('jsaActualizarParam', array('idclave', 'idvalor'), "#idmsg");
$jxc ->process();



$xHP->init();

$xFRM		= new cHForm("frmopciones", "./");
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();

$xHT		= new cHTabs();


$xFRM->addCerrar();



$xFRM->setTitle($xHP->getTitle());
$xChk->setDivClass("");

//$xTbl->initRow();
$xTbl		= new cHTabla("idtblconfig", "listado");
$xTbl->addTH("TR.PARAMETRO");
$xTbl->addTH("TR.VALOR");
//$xTbl->endRow();

/* ------ 1 ------*/
$xTbl->initRow();
$xTbl->addTD("En Migracion");
$rr	= (MODO_MIGRACION == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('sistema_en_migracion', this)");
$xTbl->addTD($xChk->get("", "id1", $rr), " class='izq' ");
$xTbl->endRow();

/* ------ 2 ------*/
$xTbl->initRow();
$xTbl->addTD("En Correcion");
$rr	= (MODO_CORRECION == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('sistema_en_correcion', this)");
$xTbl->addTD($xChk->get("", "id2", $rr), " class='izq' ");
$xTbl->endRow();


/* ------ 3 ------*/
$xTbl->initRow();
$xTbl->addTD("Modulo De Aml Activado");
$rr	= (MODULO_AML_ACTIVADO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('modulo_de_aml_activado', this)");
$xTbl->addTD($xChk->get("", "id3", $rr), " class='izq' ");
$xTbl->endRow();
/* ------ 4 ------*/
$xTbl->initRow();
$xTbl->addTD("Modulo De Caja Activado");
$rr	= (MODULO_CAJA_ACTIVADO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('modulo_de_caja_activado', this)");
$xTbl->addTD($xChk->get("", "id4", $rr), " class='izq' ");
$xTbl->endRow();
/* ------ 5 ------*/
$xTbl->initRow();
$xTbl->addTD("Modulo De Captacion Activado");
$rr	= (MODULO_CAPTACION_ACTIVADO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('modulo_de_captacion_activado', this)");
$xTbl->addTD($xChk->get("", "id5", $rr), " class='izq' ");
$xTbl->endRow();

/* ------ 6 ------*/
$xTbl->initRow();
$xTbl->addTD("Modulo De Contabilidad Activado");
$rr	= (MODULO_CONTABILIDAD_ACTIVADO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('modulo_de_contabilidad_activado', this)");
$xTbl->addTD($xChk->get("", "id6", $rr), " class='izq' ");
$xTbl->endRow();

/* ------ 7 ------*/
$xTbl->initRow();
$xTbl->addTD("Modulo De Leasing Activado");
$rr	= (MODULO_LEASING_ACTIVADO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('modulo_de_leasing_activado', this)");
$xTbl->addTD($xChk->get("", "id7", $rr), " class='izq' ");
$xTbl->endRow();

/* ------ 8 ------*/
$xTbl->initRow();
$xTbl->addTD("Modulo De Multisucursal Activado");
$rr	= (MULTISUCURSAL == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('modulo_de_multisucursal_activado', this)");
$xTbl->addTD($xChk->get("", "id8", $rr), " class='izq' ");
$xTbl->endRow();

/* ------ 9 ------*/
$xTbl->initRow();
$xTbl->addTD("Modulo De Seguimiento Activado");
$rr	= (MODULO_SEGUIMIENTO_ACTIVADO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('modulo_de_seguimiento_activado', this)");
$xTbl->addTD($xChk->get("", "id9", $rr), " class='izq' ");
$xTbl->endRow();




/* ------  ------*/





/*liberar_modulos_de_informacion*/
$xTbl->initRow();
$xTbl->addTD("Sistema.- Liberar Modulos");
$rr	= (OPERACION_LIBERAR_ACCIONES == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('liberar_modulos_de_informacion', this)");
$xTbl->addTD($xChk->get("", "id11", $rr), " class='izq' ");
$xTbl->endRow();
/* ------  ------*/

/*liberar_informacion_de_sucursales*/
$xTbl->initRow();
$xTbl->addTD("Sistema.- Liberar Informacion de Sucursales");
$rr	= (OPERACION_LIBERAR_SUCURSALES== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('liberar_informacion_de_sucursales', this)");
$xTbl->addTD($xChk->get("", "id12", $rr), " class='izq' ");
$xTbl->endRow();


/*
$xTbl->initRow();
$xTbl->addTD("");
$rr	= ( == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('', this)");
$xTbl->addTD($xChk->get("", "id2", $rr), " class='izq' ");
$xTbl->endRow();
*/

$xHT->addTab("TR.SISTEMA", $xTbl->get());

$xTbl		= new cHTabla("idtblconfig2", "listado");
$xTbl->addTH("TR.PARAMETRO");
$xTbl->addTH("TR.VALOR");

/* ------  ------*/
//persona_aceptar_menores_de_edad
$xTbl->initRow();
$xTbl->addTD("Aceptar Menores de Edad");
$rr	= (PERSONAS_ACEPTAR_MENORES== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('persona_aceptar_menores_de_edad', this)");
$xTbl->addTD($xChk->get("", "idkgen01", $rr), " class='izq' ");
$xTbl->endRow();

/* ------  ------*/
//
$xTbl->initRow();
$xTbl->addTD("Habilitar el Modulo de Aportaciones");
$rr	= (PERSONAS_CONTROLAR_POR_APORTS== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('personas_controlar_por_aportaciones', this)");
$xTbl->addTD($xChk->get("", "idkgen02", $rr), " class='izq' ");
$xTbl->endRow();


/* ------  personas_controlar_por_empresas ------*/
//
$xTbl->initRow();
$xTbl->addTD("Controlar por Empleador");
$rr	= (PERSONAS_CONTROLAR_POR_EMPRESA== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('personas_controlar_por_empresas', this)");
$xTbl->addTD($xChk->get("", "idkgen03", $rr), " class='izq' ");
$xTbl->endRow();

/* ------ GARANTIA LIQUIDA  ------*/
//
$xTbl->initRow();
$xTbl->addTD("Usar Garantia Liquida en Ahorro");
$rr	= (GARANTIA_LIQUIDA_EN_CAPTACION== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('utilizar_garantia_liquida_en_captacion', this)");
$xTbl->addTD($xChk->get("", "idkgen04", $rr), " class='izq' ");
$xTbl->endRow();



//Aportaciones en Capital o captacion
$xTbl->initRow();
$xTbl->addTD("Controlar Aportaciones en Cuentas de Ahorrro");
$rr	= (CAPITAL_SOCIAL_EN_CAPTACION== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('manejar_captal_social_en_captacion', this)");
$xTbl->addTD($xChk->get("", "idkgen05", $rr), " class='izq' ");
$xTbl->endRow();



$xTbl->initRow();
$xTbl->addTD("Habilitar el Modulo de Grupos");
$rr	= (PERSONAS_CONTROLAR_POR_GRUPO== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('personas_controlar_por_grupos', this)");
$xTbl->addTD($xChk->get("", "idkgen06", $rr), " class='izq' ");
$xTbl->endRow();


//================== Compartir con Asociada

$xTbl->initRow();
$xTbl->addTD("Compartir con Asociada");
$rr	= (PERSONAS_COMPARTIR_CON_ASOCIADA== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('compartir_datos_con_entidad_asocidad', this)");
$xTbl->addTD($xChk->get("", "idkgen07", $rr), " class='izq' ");
$xTbl->endRow();



$xHT->addTab("TR.Personas", $xTbl->get());




$xTbl		= new cHTabla("idtblconfig3", "listado");
$xTbl->addTH("TR.PARAMETRO");
$xTbl->addTH("TR.VALOR");

/*creditos_controlar_por_periodos*/
$xTbl->initRow();
$xTbl->addTD("Creditos.- Controlar por periodos");
$rr	= (CREDITO_CONTROLAR_POR_PERIODOS == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('creditos_controlar_por_periodos', this)");
$xTbl->addTD($xChk->get("", "idkcred01", $rr), " class='izq' ");
$xTbl->endRow();
/* ------  ------*/

/*creditos_controlar_por_origen*/
$xTbl->initRow();
$xTbl->addTD("Creditos.- Controlar por Origen");
$rr	= (CREDITO_CONTROLAR_POR_ORIGEN == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('creditos_controlar_por_origen', this)");
$xTbl->addTD($xChk->get("", "idkcred02", $rr), " class='izq' ");
$xTbl->endRow();
/* ------  ------*/


/*creditos_controlar_por_origen*/
$xTbl->initRow();
$xTbl->addTD("Creditos.- Usar Ahorro");
$rr	= (CREDITO_USAR_AHORRO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('creditos_usar_ahorro_en_creds', this)");
$xTbl->addTD($xChk->get("", "idkcred03", $rr), " class='izq' ");
$xTbl->endRow();
/* ------  ------*/

/*credito_registrar_estados_de_credito*/
$xTbl->initRow();
$xTbl->addTD("Creditos.- Registrar Estados de credito");
$rr	= (CREDITO_REGISTRAR_ESTADOS == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('credito_registrar_estados_de_credito', this)");
$xTbl->addTD($xChk->get("", "idkcred04", $rr), " class='izq' ");
$xTbl->endRow();

if(CREDITO_REGISTRAR_ESTADOS == true){
	/*credito_generar_operacion_en_mvto_a_mora*/
	$xTbl->initRow();
	$xTbl->addTD("Creditos.- Generar Movimiento por Mora");
	$rr	= (CREDITO_GENERAR_MVTO_MORA == true) ? true : false;
	$xChk->setOnClick("jsActualizarParam('credito_generar_operacion_en_mvto_a_mora', this)");
	$xTbl->addTD($xChk->get("", "idkcred05", $rr), " class='izq' ");
	$xTbl->endRow();
	/* ------  ------*/
	
	
	/*credito_generar_operacion_en_mvto_a_vigente*/
	$xTbl->initRow();
	$xTbl->addTD("Creditos.- Generar Movimiento por Vigente");
	$rr	= (CREDITO_GENERA_MVTO_VIGENTE == true) ? true : false;
	$xChk->setOnClick("jsActualizarParam('credito_generar_operacion_en_mvto_a_vigente', this)");
	$xTbl->addTD($xChk->get("", "idkcred06", $rr), " class='izq' ");
	$xTbl->endRow();
	/* ------  ------*/

}

/*usar_oficial_por_producto*/
$xTbl->initRow();
$xTbl->addTD("Creditos.- Usar Oficial Por Producto");
$rr	= (USE_OFICIAL_BY_PRODUCTO == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('usar_oficial_por_producto', this)");
$xTbl->addTD($xChk->get("", "idkcred07", $rr), " class='izq' ");
$xTbl->endRow();
/* ------  ------*/


$xHT->addTab("TR.Creditos", $xTbl->get());





/*utilizar_garantia_liquida_en_captacion*/
/*$xTbl->initRow();
$xTbl->addTD("Captacion.- Garantia Liquida en captacion");
$rr	= (GARANTIA_LIQUIDA_EN_CAPTACION == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('utilizar_garantia_liquida_en_captacion', this)");
$xTbl->addTD($xChk->get("", "idcapta01", $rr), " class='izq' ");
$xTbl->endRow();
*/
/* ------  ------*/


if(MODULO_CAPTACION_ACTIVADO == true){
	//------------------------
	$xTbl		= new cHTabla("idtcfgcapta", "listado");
	$xTbl->addTH("TR.PARAMETRO");
	$xTbl->addTH("TR.VALOR");
	
	
	/*manejar_en_detalle_las_tasas*/
	$xTbl->initRow();
	$xTbl->addTD("Captacion. Manejar En Detalle Las Tasas");
	$rr	= (CAPTACION_USE_TASA_DETALLADA == true) ? true : false;
	$xChk->setOnClick("jsActualizarParam('manejar_en_detalle_las_tasas', this)");
	$xTbl->addTD($xChk->get("", "idcapt01", $rr), " class='izq' ");
	$xTbl->endRow();
	
	$xHT->addTab("TR.Captacion", $xTbl->get());


}





if(MODULO_CONTABILIDAD_ACTIVADO == true){
	//------------------------
	$xTbl		= new cHTabla("idtcfgcont", "listado");
	$xTbl->addTH("TR.PARAMETRO");
	$xTbl->addTH("TR.VALOR");
	
	

	/* ------ contabilidad_en_migracion  ------*/
	
	$xTbl->initRow();
	$xTbl->addTD("Contabilidad. Contabilidad En Migracion");
	$rr	= (CONTABLE_EN_MIGRACION == true) ? true : false;
	$xChk->setOnClick("jsActualizarParam('contabilidad_en_migracion', this)");
	$xTbl->addTD($xChk->get("", "idcont01", $rr), " class='izq' ");
	$xTbl->endRow();
	
	/* ------ generar_contabilidad  ------*/
	
	$xTbl->initRow();
	$xTbl->addTD("Contabilidad. Generar Contabilidad");
	$rr	= (GENERAR_CONTABILIDAD == true) ? true : false;
	$xChk->setOnClick("jsActualizarParam('generar_contabilidad', this)");
	$xTbl->addTD($xChk->get("", "idcont02", $rr), " class='izq' ");
	$xTbl->endRow();
	
	
	$xHT->addTab("TR.Contabilidad", $xTbl->get());
	
	
}



$xFRM->addHElem( $xHT->get() );

$xFRM->OHidden("idclave", "");
$xFRM->OHidden("idvalor", "");

$xFRM->addAviso("", "idmsg");
if($SyncApp == true){
	$xFRM->OButton("TR.Sync App Catalogos", "jsSyncCatalogos()", $xFRM->ic()->EJECUTAR, "cmdexecapp", "yellow");
}
//var_dump(MODO_MIGRACION);
if(MODO_DEBUG == true){
	$xFRM->OButton("TR.EDITAR ARCHIVO DE CONFIGURACION", "jsGoEditConfig()", $xFRM->ic()->CONTROL, "cmdeditconfig", "red");
}
$xFRM->OButton("TR.OPCIONES DE SISTEMA", "jsGoEditConfigSistema()", $xFRM->ic()->CONTROL, "cmdeditsistema", "orange2");
$xFRM->OButton("TR.OPCIONES DE ENTIDAD", "jsGoEditConfigSistema('entidad')", $xFRM->ic()->EMPRESA, "cmdeditdentidad", "gblue");
$xFRM->OButton("TR.OPCIONES DE DOMICILIO", "jsGoEditConfigSistema('entidad.domicilio')", $xFRM->ic()->DOMICILIO, "cmdeditdom", "gorange");
$xFRM->OButton("TR.OPCIONES DE LEYES", "jsGoEditConfigSistema('entidad.legal')", $xFRM->ic()->LEGAL, "cmdeditlega", "gred");

if(MODULO_SEGUIMIENTO_ACTIVADO == true){
	$xFRM->OButton("TR.OPCIONES DE SEGUIMIENTO", "jsGoEditConfigSistema('mmod-seguimiento')", $xFRM->ic()->SMS, "cmdeditseg", "ggreen");
}
if(MODULO_CAJA_ACTIVADO == true){
	$xFRM->OButton("TR.OPCIONES DE BANCOS", "jsGoEditConfigSistema('" + MMOD_BANCOS + "')", $xFRM->ic()->CONTROL, "cmdeditbanco", "green2");
	$xFRM->OButton("TR.OPCIONES DE TESORERIA", "jsGoEditConfigSistema('tesoreria')", $xFRM->ic()->CAJA, "cmdeditteso", "green2");
}
if(MODULO_AML_ACTIVADO == true){
	$xFRM->OButton("TR.OPCIONES DE PLD", "jsGoEditConfigSistema('pld')", $xFRM->ic()->NOTIFICACION, "cmdeditaml", "blue4");
}

if(CREDITO_CONTROLAR_POR_PERIODOS == false){
	$xFRM->OButton("TR.CREDITOS_PERIODOS", "jsGoPeriodosDeCredito()", $xFRM->ic()->CREDITO, "cmdperiodoscreditos", "blue");
}
if(PERSONAS_CONTROLAR_POR_APORTS == true){
	$xFRM->OButton("TR.PAGOS POR MEMBRESIA", "var xG=new Gen();xG.w({url:'/frmsocios/lista_pagos_por_membresia.frm.php', principal:true})", $xFRM->ic()->ESTADO_CTA, "cmdeditaports", "yellow");
}
$xFRM->OButton("TR.BIZRULES", "jsGoReglasDeNegocio()", $xFRM->ic()->EJECUTAR, "cmd_exec_rules", "yellow");
echo $xFRM->get();
?>
<script>
var xG	= new Gen();

function jsActualizarParam(id, obj){
	var tt	= $(obj).attr("type");
	$("#idclave").val(id);
	//$("#idvalor").val(v);
	
	switch(tt){
	case "checkbox":
		if($(obj).prop('checked') == true){
			$("#idvalor").val("true");
		} else {
			$("#idvalor").val("false");
		}
		
		break;
		
	}
	//console.log(tt);
	//console.log($("#idvalor").val());
	xG.confirmar({msg: "CONFIRMA_ACTUALIZACION", callback: jsaActualizarParam});
}
function jsGoEditConfig(){
	xG.w({url: "../frmsystem/edit-config.frm.php", tab:true});
}
function jsGoEditConfigSistema(tema){
	tema	= (typeof tema == "undefined") ? "sistema" : tema;
	if(tema == "pld"){
		tema = "aml";
	}
	if(tema == "mmod-seguimiento"){
		tema = "seguimiento";
	}
	xG.w({url: "../install/configuracion.editar.frm.php?tipo=" + tema, tab:true});
}
function jsGoPeriodosDeCredito(){
	xG.w({url: "../frmcreditos/cambiarperiodo.frm.php", tab:true});
}
function jsGoReglasDeNegocio(){
	xG.w({url: "../frmsecurity/entidad-reglas.frm.php", tab:true});
}
function jsSyncCatalogos(){
	var xApp	= new AppGen();
	xApp.sync({msg : "¿ Confirma ejecutar el SYNC con Catalogos de la App ?", catalogos:true });
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
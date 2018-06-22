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
			//$xSAFE->setModC($enable);
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
			break;
				
	}
	return ($res == false) ? "FALLO" : "EXITO";
}
$jxc ->exportFunction('jsaActualizarParam', array('idclave', 'idvalor'), "#idmsg");
$jxc ->process();



$xHP->init();

$xFRM		= new cHForm("frm", "./");
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
$xTbl->addTD($xChk->get("", "id13", $rr), " class='izq' ");
$xTbl->endRow();

/* ------  ------*/
//
$xTbl->initRow();
$xTbl->addTD("Habilitar el Modulo de Aportaciones");
$rr	= (PERSONAS_CONTROLAR_POR_APORTS== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('personas_controlar_por_aportaciones', this)");
$xTbl->addTD($xChk->get("", "idca01", $rr), " class='izq' ");
$xTbl->endRow();


/* ------ GARANTIA LIQUIDA  ------*/
//
$xTbl->initRow();
$xTbl->addTD("Usar Garantia Liquida en Ahorro");
$rr	= (GARANTIA_LIQUIDA_EN_CAPTACION== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('utilizar_garantia_liquida_en_captacion', this)");
$xTbl->addTD($xChk->get("", "idca03", $rr), " class='izq' ");
$xTbl->endRow();



//Aportaciones en Capital o captacion
$xTbl->initRow();
$xTbl->addTD("Controlar Aportaciones en Cuentas de Ahorrro");
$rr	= (CAPITAL_SOCIAL_EN_CAPTACION== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('manejar_captal_social_en_captacion', this)");
$xTbl->addTD($xChk->get("", "idca02", $rr), " class='izq' ");
$xTbl->endRow();



$xTbl->initRow();
$xTbl->addTD("Habilitar el Modulo de Grupos");
$rr	= (PERSONAS_CONTROLAR_POR_GRUPO== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('personas_controlar_por_grupos', this)");
$xTbl->addTD($xChk->get("", "idcg01", $rr), " class='izq' ");
$xTbl->endRow();


//================== Compartir con Asociada

$xTbl->initRow();
$xTbl->addTD("Compartir con Asociada");
$rr	= (PERSONAS_COMPARTIR_CON_ASOCIADA== true) ? true : false;
$xChk->setOnClick("jsActualizarParam('compartir_datos_con_entidad_asocidad', this)");
$xTbl->addTD($xChk->get("", "idcca01", $rr), " class='izq' ");
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
$xTbl->addTD($xChk->get("", "id10", $rr), " class='izq' ");
$xTbl->endRow();
/* ------  ------*/

$xHT->addTab("TR.Creditos", $xTbl->get());

//------------------------
$xTbl		= new cHTabla("idtcfgcapta", "listado");
$xTbl->addTH("TR.PARAMETRO");
$xTbl->addTH("TR.VALOR");
/*utilizar_garantia_liquida_en_captacion*/
$xTbl->initRow();
$xTbl->addTD("Captacion.- Garantia Liquida en captacion");
$rr	= (GARANTIA_LIQUIDA_EN_CAPTACION == true) ? true : false;
$xChk->setOnClick("jsActualizarParam('utilizar_garantia_liquida_en_captacion', this)");
$xTbl->addTD($xChk->get("", "idcapta01", $rr), " class='izq' ");
$xTbl->endRow();
/* ------  ------*/

$xHT->addTab("TR.Captacion", $xTbl->get());






















$xFRM->addHElem( $xHT->get() );

$xFRM->OHidden("idclave", "");
$xFRM->OHidden("idvalor", "");

$xFRM->addAviso("", "idmsg");

$xFRM->OButton("TR.Sync App", "jsSyncCatalogos()", $xFRM->ic()->EJECUTAR, "cmdexecapp", "yellow");

//var_dump(MODO_MIGRACION);
if(MODO_DEBUG == true){
	$xFRM->OButton("TR.EDITAR ARCHIVO DE CONFIGURACION", "jsGoEditConfig()", $xFRM->ic()->CONTROL, "cmdeditconfig", "red");
}
$xFRM->OButton("TR.OPCIONES DE SISTEMA", "jsGoEditConfigSistema()", $xFRM->ic()->CONTROL, "cmdeditsistema", "orange");
if(CREDITO_CONTROLAR_POR_PERIODOS == false){
	$xFRM->OButton("TR.CREDITOS_PERIODOS", "jsGoPeriodosDeCredito()", $xFRM->ic()->CREDITO, "cmdperiodoscreditos", "blue");
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
	xG.w({url: "../frmsystem/edit-config.frm.php"});
}
function jsGoEditConfigSistema(){
	xG.w({url: "../install/configuracion.editar.frm.php?tipo=sistema"});
}
function jsGoPeriodosDeCredito(){
	xG.w({url: "../frmcreditos/cambiarperiodo.frm.php"});
}
function jsGoReglasDeNegocio(){
	xG.w({url: "../frmsecurity/entidad-reglas.frm.php"});
}
function jsSyncCatalogos(){
	
	xG.confirmar({ msg : "¿ Confirma ejecutar el SYNC con Catalogos de la App ?",
		callback : function(){
			xG.spinInit();
				
			xG.svc({url:"app-sync.svc.php", 
				callback : function (dd){
					xG.spinEnd();
					xG.alerta({ msg: dd.message });
				}
			});		
		}
	});
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
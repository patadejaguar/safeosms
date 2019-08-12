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
$xHP		= new cHPage("TR.DATOS DE PRODUCTOS DE CREDITOS", HP_FORM);
$jxc 		= new TinyAjax();

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);$clave		= parametro("producto", $clave, MQL_INT);
$todos			= parametro("todos", false, MQL_BOOL);

$xHP->addJTableSupport();
$xHP->setIncludeJQueryUI();

function jsaLoadOtrosDatos($idproducto){
	$xLi		= new cSQLListas();
	$xT			= new cTabla($xLi->getListadoDeOtrosDatosPorProductoCred($idproducto, true));
	$xObj		= new cCreditos_productos_otros_parametros();
	$xT->setKeyField($xObj->getKey());
	$xT->setKeyTable($xObj->get());
	
	$xT->addEditar();
	
	if(MODO_DEBUG == true){
		$xT->addEliminar();
	}
	
	return $xT->Show("",true, "tblListaOtrosDatos");
}
function jsaLoadOtrosCargos($idproducto){
	$xLi		= new cSQLListas();
	$xT	= new cTabla($xLi->getListadoDeCargosPorProductoCred($idproducto, true, true));
	$xT->setKeyField("idcreditos_productos_costos");
	$xT->setKeyTable("creditos_productos_costos");
	$xT->OButton("TR.EDITAR", "jsOtrosCargosEditar(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->EDITAR);
	//$xT->addEditar();
	$xT->addBaja();
	if(MODO_DEBUG){
		$xT->addEliminar();
	}
	return $xT->Show("",true, "tblListaCostos");
}
function jsaLoadEtapas($idproducto){
	$xLi		= new cSQLListas();
	$xT	= new cTabla($xLi->getListadoDeEtapasPorProductoCred($idproducto));
	$xT->setKeyField("");
	$xT->setKeyTable("");
	//$xT->OButton("TR.EDITAR", "jsOtrosCargosEditar(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->EDITAR);
	//$xT->addEditar();
	$xT->setOmitidos("producto");
	
	if(MODO_DEBUG == true){
		$xT->addEliminar();
	}
	
	return $xT->Show("",true, "tblListaEtapas");
}
function jsaLoadRequisitos($idproducto){
	$xLi		= new cSQLListas();
	$xT	= new cTabla($xLi->getListadoDeRequisitosPorProductoCred($idproducto));
	$xT->setKeyField("");
	$xT->setKeyTable("");
	//$xT->OButton("TR.EDITAR", "jsOtrosCargosEditar(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->EDITAR);
	//$xT->addEditar();
	if(MODO_DEBUG == true){
		$xT->addEliminar();
	}
	return $xT->Show("",true, "tblListaRequisitos");
}
function jsaLoadPromociones($idproducto){
	$xLi	= new cSQLListas();
	$xT		= new cTabla($xLi->getListadoDePromosPorProductoCred($idproducto));
	$xT->setKeyField("");
	$xT->setKeyTable("");
	$xT->OButton("TR.EDITAR", "jsPromocionesEditar(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->EDITAR);
	//$xT->addEditar();
	if(MODO_DEBUG == true){
		$xT->addEliminar();
	}
	return $xT->Show("",true, "tblListaPromos");
}

function jsaLoadReglas($idproducto){
	$xLi	= new cSQLListas();
	$xT		= new cTabla($xLi->getListadoDeReglasPorProductoCred($idproducto));
	$xT->setOmitidos("producto");
	$xT->setKeyField("");
	$xT->setKeyTable("");
	
	$xT->setOmitidos("tipo");
	$xT->setOmitidos("evoluciona");
	$xT->setOmitidos("contador");
	
	$xT->OButton("TR.EDITAR", "jsReglasEditar(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->EDITAR);
	//$xT->addEditar();
	if(MODO_DEBUG == true){
		$xT->addEliminar();
	}
	
	return $xT->Show("",true, "tblListaPromos");
}

function jsaMigrarFormatos($producto){
	$xLF	= new cFormatosDelSistema();
	$xQL	= new MQL();
	if($producto >0){
		$xProd	= new cProductoDeCredito($producto);
		if($xProd->init() == true){
			$subtipo	= $xProd->getTipoEnSistema();
			$tipo     = iDE_CREDITO;
			//$xFMT	= new cFormato();
			if($subtipo == 500){
				$subtipo	= 281;			//FiXME: Corregir por leasing
			}
			$sql	= $xLF->getSQL_Lista(false, $tipo, $subtipo);
			$rs		= $xQL->getDataRecord($sql);
			$xPFm	= new cCreditosProductosFormatos();
			$xTT	= new cGeneral_contratos();
			
			foreach ($rs as $rw){
				$xTT->setData($rw);
				$xPFm->add(CREDITO_ESTADO_SOLICITADO, $xTT->idgeneral_contratos()->v(), $producto);
			}
		}
		
	}
}
function jsaMigrarDocumentos($producto){
	
}
function jsaMigrarEtapas($producto){
	$xTEp	= new cCreditos_etapas();
	$xQL	= new MQL();
	$rs		= $xTEp->query()->select()->exec();
	$xProE	= new cCreditosProductosEtapas();
	$xPer	= new cSystemPermissions();
	
	foreach ($rs as $rw){
		$xTEp->setData($rw);
		$idetapa	= $xTEp->idcreditos_etapas()->v();
		$nombre		= $xTEp->descripcion()->v();
		$tags		= $xTEp->tags()->v();
		$orden		= $xTEp->orden_general()->v();
		
		$xProE->add($producto, $idetapa, $nombre, $xPer->DEF_PERMISOS,$orden, $tags);
	}
	return "";
}
$jxc ->exportFunction('jsaLoadOtrosDatos', array('idproducto'), "#iddivotrosdatos");
$jxc ->exportFunction('jsaLoadOtrosCargos', array('idproducto'), "#iddivotroscargos");
$jxc ->exportFunction('jsaLoadEtapas', array('idproducto'), "#iddivetapas");
$jxc ->exportFunction('jsaLoadRequisitos', array('idproducto'), "#iddivrequisitos");
$jxc ->exportFunction('jsaLoadPromociones', array('idproducto'), "#iddivpromociones");
$jxc ->exportFunction('jsaLoadReglas', array('idproducto'), "#iddivreglas");
$jxc ->exportFunction('jsaMigrarFormatos', array('idproducto'), "#idmsgs");

$jxc ->exportFunction('jsaMigrarDocumentos', array('idproducto'), "#idmsgs");
$jxc ->exportFunction('jsaMigrarEtapas', array('idproducto'), "#idmsgs");

$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frmcredsprodspanel", "./");
$xSel		= new cHSelect();
$xTab		= new cHTabs();


$xFRM->setTitle($xHP->getTitle());
$xFRM->OButton("TR.Datos Generales", "jsGoToGeneral()", "ejecutar");
$xFRM->OButton("TR.Tasas", "jsGoToTasas()", "tasa");
$xFRM->OButton("TR.Dias", "jsGoToDias()", "fecha");
$xFRM->OButton("TR.cantidades", "jsGoToCantidades()", "moneda");
$xFRM->OButton("TR.Garantias", "jsGoToGarantias()", "garantia");



$xFRM->OButton("TR.Comisiones", "jsGoToComisiones()", "dinero");
if(getEsModuloMostrado(USUARIO_TIPO_GERENTE)){
	$xFRM->OButton("TR.Permisos", "jsGoToPermisos()", "permisos");
}
//if(getEsModuloMostrado(USUARIO_TIPO_GERENTE)){
if(MODO_DEBUG == true){
	$xFRM->OButton("TR.Scripting", "jsGoToScript()", "codigo");
}
if(getEsModuloMostrado(USUARIO_TIPO_GERENTE, MMOD_CONTABILIDAD) == true){
	$xFRM->OButton("TR.Contabilidad de Capital", "jsGoToContableCapital()", "contabilidad");
	$xFRM->OButton("TR.Contabilidad de Intereses", "jsGoToContableInteres()", "contabilidad");
}

$xFRM->OButton("TR.Duplicar", "jsClonarProducto()", $xFRM->ic()->EJECUTAR, "idduplicar", "blue2");

$xFRM->OButton("TR.Otros Cargos", "jsOtrosCargos()", $xFRM->ic()->CONTROL);
$xFRM->OButton("TR.Copiar Otros Cargos", "jsCopiarOtrosCargos()", $xFRM->ic()->CONTROL);

$xFRM->OButton("TR.Otros parametros", "jsOtrosParametros()", $xFRM->ic()->CONTROL);

$xFRM->OButton("TR.Requisitos", "jsRequisitos()", $xFRM->ic()->CONTROL);
$xFRM->OButton("TR.Reglas", "jsReglas()", $xFRM->ic()->CONTROL);

$xFRM->OButton("TR.Etapas", "jsEtapas()", $xFRM->ic()->CONTROL);
$xFRM->OButton("TR.Promociones", "jsPromociones()", $xFRM->ic()->CONTROL);

//$xFRM->OButton("TR.FORMS_Y_DOCS", "jsFormatos()", $xFRM->ic()->FORMATO);

$xFRM->addCerrar();

$xProd	= new cProductoDeCredito($clave);

if($xProd->init() == true){
	$xFRM->OHidden("idproducto", $clave);
	$xFRM->addHElem( $xProd->getFicha() );
} else {
	$xSProd		= $xSel->getListaDeProductosDeCredito("", false, true);
	if($todos == true){
		$xSProd	= $xSel->getListaDeProductosDeCredito();
	}
	$xSProd->addEvent("onblur", "jsLoadInit()");
	$lbl	= $xSProd->getLabel();
	$xSProd->setLabel("");
	$xFRM->addDivSolo( $lbl, $xSProd->get(false), "tx14", "tx34" );
}
$xFRM->addJsInit("jsLoadInit();");


if($xProd->getTieneEtapas() == false){
	$xFRM->OButton("TR.Migrar Etapas", "jsMigrarEtapas()", $xFRM->ic()->EXPORTAR, "idmigraretapas", "yellow");
}


$xTab->addTab("TR.OTROS CARGOS", "<div id='iddivotroscargos'></div>");
$xTab->addTab("TR.REGLAS", "<div id='iddivreglas'></div>");

$xTab->addTab("TR.OTROS DATOS", "<div id='iddivotrosdatos'></div>");
$xTab->addTab("TR.ETAPA", "<div id='iddivetapas'></div>");
$xTab->addTab("TR.REQUISITOS", "<div id='iddivrequisitos'></div>");
$xTab->addTab("TR.PROMOCIONES", "<div id='iddivpromociones'></div>");

$xTab->addTab("TR.Documentacion", "<div id='iddivcredptodsdoctos'></div>");
$xTab->addTab("TR.Formatos", "<div id='iddivcredsprodsforms'></div>");



/*===========GRID JS============*/
$xHG1    = new cHGrid("iddivcredptodsdoctos",$xHP->getTitle());
$xHG1->setSQL("SELECT   `creditos_prods_doctos`.`idcreditos_prods_doctos`,
         `personas_documentacion_tipos`.`nombre_del_documento` AS `documento_id`,
         `creditos_etapas`.`descripcion` AS `etapa_id`,
         getBooleanMX(`creditos_prods_doctos`.`estatus`) AS `estatusactivo`,
         getBooleanMX(`creditos_prods_doctos`.`opcional`) AS `opcional`
FROM     `creditos_prods_doctos` 
INNER JOIN `creditos_etapas`  ON `creditos_prods_doctos`.`etapa_id` = `creditos_etapas`.`idcreditos_etapas` 
INNER JOIN `personas_documentacion_tipos`  ON `creditos_prods_doctos`.`documento_id` = `personas_documentacion_tipos`.`clave_de_control`
WHERE `producto_credito_id`=$clave LIMIT 0,100");

$xHG1->addList();
$xHG1->setOrdenar();
$xHG1->addKey("idcreditos_prods_doctos");

//$xHG->col("producto_credito_id", "TR.PRODUCTO CREDITO ID", "10%");
$xHG1->col("documento_id", "TR.DOCUMENTO", "20%");
$xHG1->col("etapa_id", "TR.ETAPA", "10%");
//$xHG1->col("estatusactivo", "TR.ESTATUS", "10%");
$xHG1->col("opcional", "TR.OPCIONAL", "10%");

$xHG1->OToolbar("TR.AGREGAR", "jsAddDoctos()", "grid/add.png");
$xHG1->OButton("TR.EDITAR", "jsEditDoctos('+ data.record.idcreditos_prods_doctos +')", "edit.png");

$xHG1->OToolbar("TR.MIGRAR", "jsMigrarDocumentos()", "grid/folder.png");

if(MODO_DEBUG == true){
	$xHG1->OButton("TR.ELIMINAR", "jsDelDoctos('+ data.record.idcreditos_prods_doctos +')", "delete.png");
}
$xHG1->OButton("TR.BAJA", "jsDeactDoctos('+ data.record.idcreditos_prods_doctos +')", "undone.png");

$xFRM->addJsCode( $xHG1->getJs(true) );

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivcredsprodsforms",$xHP->getTitle());

$xHG->setSQL("SELECT   `creditos_prods_formatos`.`idcreditos_prods_contratos` AS `clave`,
         `creditos_etapas`.`descripcion` AS `etapa`,
         `general_contratos`.`titulo_del_contrato` AS `formato`,
         getBooleanMX(`creditos_prods_formatos`.`estatus`) AS `estatus`,
         getBooleanMX(`creditos_prods_formatos`.`opcional`) AS `opcional`
FROM     `creditos_prods_formatos` 
INNER JOIN `creditos_etapas`  ON `creditos_prods_formatos`.`etapa_id` = `creditos_etapas`.`idcreditos_etapas` 
INNER JOIN `general_contratos`  ON `creditos_prods_formatos`.`formato_id` = `general_contratos`.`idgeneral_contratos` 
WHERE ( `creditos_prods_formatos`.`producto_credito_id` = $clave ) AND (`creditos_prods_formatos`.`estatus`=1)");

$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("idcreditos_prods_contratos");

//$xHG->col("producto_credito_id", "TR.PRODUCTO CREDITO ID", "10%");

$xHG->col("formato", "TR.FORMATO", "20%");
$xHG->col("etapa", "TR.ETAPA", "10%");

$xHG->col("estatus", "TR.ESTATUS", "10%");
$xHG->col("opcional", "TR.OPCIONAL", "10%");

$xHG->OToolbar("TR.COPIAR", "jsCopiarFormatos()", "grid/duplicate.png");
$xHG->OToolbar("TR.MIGRAR", "jsMigrarFormatos()", "grid/folder.png");
$xHG->OToolbar("TR.AGREGAR", "jsAddFormatos()", "grid/add.png");

$xHG->OButton("TR.EDITAR", "jsEditFormatos('+ data.record.clave +')", "edit.png");
if(MODO_DEBUG == true){
	$xHG->OButton("TR.ELIMINAR", "jsDelFormatos('+ data.record.clave +')", "delete.png");
}
$xHG->OButton("TR.BAJA", "jsDeactFormatos('+ data.record.clave +')", "undone.png");

$xFRM->addAviso();

$xFRM->addJsCode( $xHG->getJs(true) );


$xFRM->addHTML($xTab->get());

echo $xFRM->get();

?>
<script >
var xG		= new Gen();
function jsLoadInit(){
	jsaLoadOtrosCargos();
	jsaLoadOtrosDatos();
	jsaLoadEtapas();
	jsaLoadRequisitos();
	jsaLoadPromociones();
	jsaLoadReglas();

}
function jsGoToGeneral(){jsLoadObject("generales"); }
function jsGoToTasas(){jsLoadObject("tasas"); }
function jsGoToDias(){jsLoadObject("dias"); }
function jsGoToCantidades(){jsLoadObject("cantidades"); }
function jsGoToPermisos(){ jsLoadObject("permisos"); }
function jsGoToScript(){ jsLoadObject("codigo"); }
function jsGoToComisiones(){ jsLoadObject("comisiones"); }
function jsGoToGarantias(){ jsLoadObject("garantias"); }
function jsGoToContableCapital(){ jsLoadObject("contablecapital"); }
function jsGoToContableInteres(){ jsLoadObject("contableinteres"); }
function jsLoadObject(tema){
	var idproducto = $("#idproducto").val();
	sURI	= "../frmcreditos/creditos.productos.frm.php?tema="  + tema  + "&id=" + idproducto; xG.w({url: sURI, tiny : true});
}
function jsClonarProducto(){
	var idproducto = $("#idproducto").val();
	sURI	= "../frmcreditos/creditos.productos.add.frm.php?producto=" + idproducto;
	 xG.w({url: sURI, tiny : true, w: 400, callback: jsRecargar});
}
function jsRecargar(){ window.location = "frmdatos_de_convenios_de_creditos.xul.php"; }
function jsOtrosParametros(){ var idproducto = $("#idproducto").val(); xG.w({url: "../frmcreditos/creditos.productos.otros-datos.frm.php?producto=" + idproducto, tiny : true, w: 600, callback:jsaLoadOtrosDatos}); }
function jsOtrosCargos(){ var idproducto = $("#idproducto").val(); xG.w({url: "../frmcreditos/creditos.productos.otros-cargos.frm.php?producto=" + idproducto, tiny : true, w: 600, callback: jsaLoadOtrosCargos}); }
function jsOtrosCargosEditar(id){ var idproducto = $("#idproducto").val(); xG.w({url: "../frmcreditos/creditos.productos.otros-cargos.frm.php?producto=" + idproducto + "&clave=" + id, tiny : true, w: 600, callback: jsaLoadOtrosCargos}); }

function jsCopiarOtrosCargos(){
	var idproducto = $("#idproducto").val();
	xG.w({url: "../frmcreditos/creditos.productos.copiar-props.frm.php?quecopiar=otroscargos&producto=" + idproducto, tiny : true, w: 600, callback: jsaLoadOtrosCargos});
}

function jsRequisitos(){ var idproducto = $("#idproducto").val();xG.w({url: "../frmcreditos/creditos.productos.requisitos.frm.php?producto=" + idproducto, tiny : true, w: 600, callback: jsaLoadRequisitos}); }
function jsEtapas(){ var idproducto = $("#idproducto").val();xG.w({url: "../frmcreditos/creditos.productos.etapas.frm.php?producto=" + idproducto, tiny : true, w: 600, callback: jsaLoadEtapas}); }

function jsReglas(){
	var idproducto = $("#idproducto").val();
	xG.w({url: "../frmcreditos/creditos.productos.reglas.new.frm.php?producto=" + idproducto, tiny : true, w: 600, callback: jsaLoadEtapas}); 
}

function jsPromociones(){ var idproducto = $("#idproducto").val();xG.w({url: "../frmcreditos/creditos.productos.promociones.frm.php?producto=" + idproducto, tiny : true, w: 600, callback: jsaLoadPromociones}); }

function jsPromocionesEditar(id){ 
	var idproducto = $("#idproducto").val(); 
	xG.w({
		url: "../frmcreditos/creditos.productos.promociones.editar.frm.php?producto=" + idproducto + "&clave=" + id, 
		tiny : true, w: 600, callback: jsaLoadOtrosCargos
		}); 
}

function jsEtapasEditar(id){ 
	var idproducto = $("#idproducto").val(); 
	xG.w({
		url: "../frmcreditos/creditos.productos.etapas.editar.frm.php?producto=" + idproducto + "&clave=" + id, 
		tiny : true, w: 600, callback: jsaLoadOtrosCargos
		}); 
}

function jsReglasEditar(id){ 
	var idproducto = $("#idproducto").val(); 
	xG.w({
		url: "../frmcreditos/creditos.productos.reglas.edit.frm.php?producto=" + idproducto + "&clave=" + id, 
		tiny : true, w: 600, callback: jsaLoadReglas
		});
}

function jsFormatos(){ var idproducto = $("#idproducto").val();
	xG.w({url: "../frmutils/contratos-editor.frm.php?producto=" + idproducto, tab: true }); 
}
function jsEditFormatos(id){
    xG.w({url:"../frmcreditos/creditos-productos-formatos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivcredsprodsforms});
}
function jsMigrarFormatos(){
	xG.confirmar({
		callback : jsaMigrarFormatos,
		msg : "¿Desea Migrar Desde la Tabla general formatos?"
		});
}
function jsCopiarFormatos(){
	var idproducto = $("#idproducto").val();
	xG.w({url: "../frmcreditos/creditos.productos.copiar-props.frm.php?quecopiar=formatos&producto=" + idproducto, tiny : true, w: 600, callback: jsLGiddivcredsprodsforms}); 
}

function jsAddFormatos(){
	var idproducto = $("#idproducto").val();
    xG.w({url:"../frmcreditos/creditos-productos-formatos.new.frm.php?producto=" + idproducto, tiny:true, callback: jsLGiddivcredsprodsforms});
}
function jsDelFormatos(id){
    xG.rmRecord({tabla:"creditos_prods_formatos", id:id, callback:jsLGiddivcredsprodsforms });
}
function jsDeactFormatos(id){
    xG.recordInActive({tabla:"creditos_prods_formatos", id:id, callback:jsLGiddivcredsprodsforms, preguntar:true });
} 

function jsEditDoctos(id){
    xG.w({url:"../frmcreditos/creditos-productos-doctos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivcredptodsdoctos});
}
function jsAddDoctos(){
	var idproducto = $("#idproducto").val();
    xG.w({url:"../frmcreditos/creditos-productos-doctos.new.frm.php?producto=" + idproducto, tiny:true, callback: jsLGiddivcredptodsdoctos});
}
function jsDelDoctos(id){
    xG.rmRecord({tabla:"creditos_prods_doctos", id:id, callback:jsLGiddivcredptodsdoctos });
}
function jsDeactDoctos(id){
    xG.recordInActive({tabla:"creditos_prods_doctos", id:id, callback:jsLGiddivcredptodsdoctos, preguntar:true });
}
function jsMigrarDocumentos(){
	xG.confirmar({
		callback : jsaMigrarDocumentos,
		msg : "¿Desea Migrar Desde la Tabla general de Documentos?"
		});
}
function jsMigrarEtapas(){
	xG.confirmar({
		callback : jsaMigrarEtapas,
		msg : "¿Desea Migrar Desde la Tabla general Etapas?"
		});
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
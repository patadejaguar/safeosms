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
$xHP		= new cHPage("TR.MATRIZRIESGO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xMR		= new cAMLMatrizDeRiesgo();
$xTMR		= new cAml_riesgo_matrices();
$jxc 		= new TinyAjax();

function jsaUpdateRiesgo($id, $riesgo){
	
}

$jxc ->exportFunction('jsaUpdateRiesgo', array('idtopico', 'idriesgo'), "#imsgs");
$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->addJTableSupport();
$xHP->addJExcelSupport();

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();
$xFRM->addCerrar();
$xFRM->OButton("TR.NUEVO RIESGO", "addTopico()", $xFRM->ic()->AGREGAR);
$xFRM->OButton("TR.RIESGO PAIS", "jsGetRiesgoPais()", $xFRM->ic()->EDITAR);
$xFRM->OButton("TR.RIESGO ACTIVIDAD_ECONOMICA", "jsGetRiesgoActividad()", $xFRM->ic()->EDITAR);
$xFRM->OButton("TR.PERFIL ADICIONAL", "addTopicoExtra()", $xFRM->ic()->NOTA);
if(MODO_DEBUG == true){
	$xFRM->OButton("TR.EXPORTAR", "$('table').tableExport({bootstrap: false,position:'top',fileName:'MatrizDeRiesgo'});", $xFRM->ic()->EXPORTAR, "idtoexport", "yellow");
}


//Riesgo por pais de origen
//riesgo por Nacionalidad
//Riesgo por ser extranjero
//si es persona moral, obtener datos del representante
//Riesgo PM Si no hay representante
//Riesgo de no tener Actividad Economica
//Riesgo de Actividad en Paises de Alto riesgo
//Riesgo de Clave de Actividad Economica
$xFRM->addSeccion("idtipospersona", "TR.MATRIZRIESGO .- PERSONAS");

$xTb	= new cHTabla("t-riesgo-p", "listado");
$xTb->addTH("Tipo de Riesgo");
$xTb->addTH("Descripcion");


$xTb->initRow("tdOdd");
$xTb->addTD("Riesgo de Nacionalidad");
$xTb->addTD("<details><summary>Cuando una persona tiene una o más Nacionalidades Diferentes</summary><p>Cuando una persona tiene una o más Nacionalidades Diferentes. El riesgo debe ser medio por la movilidad entre naciones. En el caso de SAFE-OSMS el riesgo varía según el Nivel de Riesgo país, basado en el catálogo de la UIF/FAFT (Financial Action Task Force on Money Laundering) clasificado de la siguiente forma:</p>
   <ol><li>Países Cooperantes: Nivel Bajo.</li>
    <li>Países en Lista Grises: Nivel Medio.</li>
    <li>Países No Cooperantes: Nivel Alto.</li></ol></details>");
$xTb->endRow();


$xTb->initRow();
$xTb->addTD("Riesgo por Actividad Economica");
$xTb->addTD("<details><summary> Cuando una Persona tiene uno o más Actividades puede existir un riesgo que se asocia a:</summary>
   <ol><li>Actividades de Alto/Medio Riesgo (Como las casas de Bolsa)</li>
    <li>Actividades Asociadas a Personas Políticamente Expuestas (Gobernadores, Alcaldes, etc).</li></ol></details>");
$xTb->endRow();



$xTb->initRow();
$xTb->addTD("Riesgo Geografico en Actividad Economica");
$xTb->addTD("<details><summary> Cuando una persona tiene Actividades economicas en dos o más países el riesgo se eleva</summary><p>En el caso de SAFE-OSMS el riesgo varía según el Nivel de Riesgo país, basado en el catálogo de la UIF/FAFT (Financial Action Task Force on Money Laundering) clasificado de la siguiente forma: </p>
   <ol><li>Países Cooperantes: Nivel Bajo.</li>
    <li>Países en Lista Grises: Nivel Medio.</li>
    <li>Países No Cooperantes: Nivel Alto.</li></ol></details>");
$xTb->endRow();


$xFRM->addHElem($xTb->get());

//Agregar la Tabla de Riesgos
$xHG	= new cHGrid("iddivrp","TR.MATRIZRIESGO DE PERSONAS");
$xHG->setSQL($xLi->getListadoDeMatrizRiesgoV("", $xMR->TIPO_PERSONA));
$xHG->addList();
$xHG->setNoPaginar();
$xHG->addKey("clave");
$xHG->col("nombre", "TR.NOMBRE", "10%");
$xHG->col("clasificacion", "TR.CLASIFICACION", "10%");
$xHG->col("nivel_de_riesgo", "TR.NIVEL_DE_RIESGO", "10%");
$xHG->col("finalizador", "TR.FINALIZADOR", "10%");

$xHG->col("probabilidad", "TR.PROBABILIDAD", "10%");
$xHG->col("impacto", "TR.IMPACTO", "10%");
$xHG->col("consecuencia", "TR.CONSECUENCIA", "10%");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
$xFRM->addHElem("<div id='iddivrp'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


$xFRM->endSeccion();

$xFRM->addSeccion("idtipospdto", "TR.MATRIZRIESGO .- PRODUCTOS");


//Agregar la Tabla de Riesgos

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivriesgoprod","TR.MATRIZRIESGO POR PRODUCTO");

$xHG->setSQL($xLi->getListadoDeRiesgoPorPdto());

$xHG->addList();
$xHG->addKey("clave");
$xHG->col("tipo", "TR.TIPO", "10%");
$xHG->col("producto", "TR.PRODUCTO", "10%");
$xHG->col("riesgo", "TR.NIVEL_DE_RIESGO", "10%");
$xHG->col("observaciones", "TR.OBSERVACIONES", "10%");
$xHG->OToolbar("TR.RIESGO CREDITO", "jsAddRP(" . iDE_CREDITO . ")", "grid/add.png");

if(MODULO_CAPTACION_ACTIVADO == true){
	$xHG->OToolbar("TR.RIESGO CAPTACION", "jsAddRP(" . iDE_CAPTACION . ")", "grid/add.png");
}

$xHG->OButton("TR.EDITAR", "jsEditRP('+ data.record.clave +')", "edit.png");
$xHG->setNoPaginar();

if(MODO_DEBUG == true){
	$xHG->OButton("TR.ELIMINAR", "jsDelRP('+ data.record.clave +')", "delete.png");
}
$xFRM->addHElem("<div id='iddivriesgoprod'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );

$xFRM->endSeccion();
$xFRM->addSeccion("idtiposoperacion", "TR.MATRIZRIESGO .- OPERACIONES");

//Agregar la Tabla de Riesgos
$xHG	= new cHGrid("iddivrops","TR.MATRIZRIESGO DE OPERACIONES");
$xHG->setSQL($xLi->getListadoDeMatrizRiesgoV("", $xMR->TIPO_OPERACION));
$xHG->addList();
$xHG->setNoPaginar();
$xHG->addKey("clave");
$xHG->col("nombre", "TR.NOMBRE", "10%");
$xHG->col("clasificacion", "TR.CLASIFICACION", "10%");
$xHG->col("nivel_de_riesgo", "TR.NIVEL_DE_RIESGO", "10%");
$xHG->col("finalizador", "TR.FINALIZADOR", "10%");
$xHG->col("probabilidad", "TR.PROBABILIDAD", "10%");
$xHG->col("impacto", "TR.IMPACTO", "10%");
$xHG->col("consecuencia", "TR.CONSECUENCIA", "10%");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
$xFRM->addHElem("<div id='iddivrops'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


$xFRM->endSeccion();



$xFRM->OHidden("idtopico", "0");
$xFRM->OHidden("idriesgo", "0");


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function addTopico(){
	xG.w({url: "../frmpld/matriz-de-riesgo.new.frm.php?", tiny: false, w:600, callback: jsReloadGrids});
}
function addTopicoExtra(){
	xG.w({url: "../frmpld/perfiles-de-riesgo.frm.php?", tiny: false, w:600, callback: jsReloadGrids});
}
function setTopico(id, riesgo){
	$("#idtopico").val(id);
	$("#idriesgo").val(riesgo);
}
function jsGetRiesgoPais(){
	xG.w({url: "../frmsocios/catalogo_paises.grid.php?", tab: true, w:600});
}
function jsGetRiesgoActividad(){
	xG.w({url: "../frmsocios/catalogo_paises.grid.php?", tab: true, w:600});
}
function jsEdit(id){
	xG.w({url:"../frmpld/matriz-de-riesgo.edit.frm.php?clave=" + id, tiny:true, callback: jsReloadGrids});
}
function jsReloadGrids(){
	jsLGiddivrp();
	//jsLGiddivrpr();
	jsLGiddivriesgoprod();
	jsLGiddivrops();
}
function jsEditRP(id){
	xG.w({url:"../frmpld/riesgo-producto.edit.frm.php?clave=" + id, tiny:true, callback: jsReloadGrids});
}
function jsAddRP(idt){
	xG.w({url:"../frmpld/riesgo-producto.new.frm.php?tipo=" + idt, tiny:true, callback: jsReloadGrids});
}
function jsDelRP(id){
	xG.rmRecord({tabla:"aml_riesgo_producto", id:id, callback:jsReloadGrids});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
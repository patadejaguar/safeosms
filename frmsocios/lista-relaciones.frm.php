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
$xHP		= new cHPage("TR.ARBOL_DE_RELACIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
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
$xHP->init();



$xFRM    	= new cHForm("frmlistaavales", "lista-avales.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

$xFRM->OBuscar("", "", "", "jsBuscar");

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivlistaavales",$xHP->getTitle());

$xHG->setSQL("SELECT   `socios_relaciones`.`idsocios_relaciones` AS `clave`,
         `socios`.`nombre` AS `relacion`,
         `socios_relacionestipos`.`descripcion_relacionestipos` AS `tipo`,
         `personas`.`nombre` AS `relacionado`,
         `socios_consanguinidad`.`descripcion_consanguinidad` AS `consanguinidad`,
		`socios_relaciones`.`credito_relacionado` AS `contrato`,
         `socios_relaciones`.`fecha_alta`,
         `socios_relaciones`.`dependiente`,
         `socios_relaciones`.`calificacion_del_referente`,
         `socios_relaciones`.`dato_extra_1`,
         `socios_relaciones`.`dato_extra_2`,
         `socios_relaciones`.`dato_extra_3`
FROM `personas`
INNER JOIN `socios_relaciones`  ON `personas`.`codigo` = `socios_relaciones`.`numero_socio`
INNER JOIN `socios`  ON `socios_relaciones`.`socio_relacionado` = `socios`.`codigo`
INNER JOIN `socios_relacionestipos`  ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.`idsocios_relacionestipos` 
INNER JOIN `socios_consanguinidad`  ON `socios_relaciones`.`consanguinidad` = `socios_consanguinidad`.`idsocios_consanguinidad`
WHERE (`idsocios_relaciones`>0)");

$xHG->addList();
$xHG->setOrdenar();

$xHG->col("relacion", "TR.NOMBRE", "20%");
$xHG->col("relacionado", "TR.RELACIONADO", "20%");
$xHG->col("contrato", "TR.CONTRATO", "10%");
$xHG->col("tipo", "TR.TIPO", "10%");
$xHG->col("consanguinidad", "TR.CONSANGUINIDAD", "10%");



//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");

//$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.clave +')", "undone.png");


$xFRM->addHElem("<div id='iddivlistaavales'></div>");

$xFRM->addJsCode( $xHG->getJs(true) );

echo $xFRM->get();

?>
<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmsocios/lista-avales.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivlistaavales});
}
function jsAdd(){
    xG.w({url:"../frmsocios/lista-avales.new.frm.php?", tiny:true, callback: jsLGiddivlistaavales});
}
function jsDel(id){
    xG.rmRecord({tabla:"socios_relaciones", id:id, callback:jsLGiddivlistaavales });
}
function jsDeact(id){
    xG.recordInActive({tabla:"socios_relaciones", id:id, callback:jsLGiddivlistaavales, preguntar:true });
}
function jsBuscar(){
	var txt	= $("#idbuscar").val();
	var mstr		= " AND (`personas`.`nombre` LIKE '%" + txt + "%' OR `socios`.`nombre` LIKE '%" + txt + "%')";
	mstr		= "&w="  + base64.encode(mstr);
	$('#iddivlistaavales').jtable('destroy');
	jsLGiddivlistaavales(mstr);
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
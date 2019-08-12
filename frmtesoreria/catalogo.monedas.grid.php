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
$xHP		= new cHPage("TR.CATALOGO DE MONEDAS", HP_FORM);
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



$xFRM	= new cHForm("frrmonedas", "catalogo.moneda.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();


$xHG	= new cHGrid("iddivmonedas",$xHP->getTitle());

$xHG->setSQL("SELECT   `tesoreria_monedas`.`clave_de_moneda` AS `clave`,
         `tesoreria_monedas`.`nombre_de_la_moneda` AS `nombre`,
         `personas_domicilios_paises`.`nombre_oficial` AS `pais`,
         `tesoreria_monedas`.`simbolo`,
         `aml_instrumentos_financieros`.`nombre_de_instrumento` AS `instrumento`,
         `tesoreria_monedas`.`quivalencia_en_moneda_local` AS `equivalencia`
FROM     `tesoreria_monedas` 
INNER JOIN `aml_instrumentos_financieros`  ON `tesoreria_monedas`.`instrumento` = `aml_instrumentos_financieros`.`tipo_de_instrumento` 
INNER JOIN `personas_domicilios_paises`  ON `tesoreria_monedas`.`pais_de_origen` = `personas_domicilios_paises`.`clave_de_control` ");
$xHG->addList();
$xHG->addKey("clave");
$xHG->col("nombre", "TR.NOMBRE", "10%");
$xHG->col("pais", "TR.PAIS", "10%");
$xHG->col("instrumento", "TR.INSTRUMENTO", "10%");
$xHG->col("simbolo", "TR.SIMBOLO", "10%");
$xHG->col("equivalencia", "TR.QUIVALENCIA", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit(\'' + data.record.clave + '\')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel(\'' + data.record.clave + '\')", "delete.png");
$xFRM->addHElem("<div id='iddivmonedas'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmtesoreria/catalogo.monedas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivmonedas});
}
function jsAdd(){
	xG.w({url:"../frmtesoreria/catalogo.monedas.new.frm.php?", tiny:true, callback: jsLGiddivmonedas});
}
function jsDel(id){
	xG.rmRecord({tabla:"tesoreria_monedas", id:id, callback:jsLGiddivmonedas});
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
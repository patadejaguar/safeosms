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
$xHP		= new cHPage("TR.TASAS DE ARRENDAMIENTO", HP_FORM);
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

$xFRM	= new cHForm("frmleasing_tasas", "leasing-tasas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

$xHG	= new cHGrid("iddivtasas",$xHP->getTitle());

$xHG->setSQL("SELECT   `leasing_tasas`.`idleasing_tasas` AS `clave`,
         `leasing_tipo_rac`.`nombre_tipo_rac` AS `tipo_de_rac`,
         `creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `frecuencia`,
         `leasing_tasas`.`limite_inferior`,
         `leasing_tasas`.`limite_superior`,
         `leasing_tasas`.`tasa_ofrecida`,
			`leasing_tasas`.`tasa_marginal` AS `tasa_marginal`,
		 `leasing_tasas`.`comision_apertura`,
		`leasing_tasas`.`tasa_vec`
FROM     `leasing_tasas` 
INNER JOIN `creditos_periocidadpagos`  ON `leasing_tasas`.`frecuencia` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
INNER JOIN `leasing_tipo_rac`  ON `leasing_tasas`.`tipo_de_rac` = `leasing_tipo_rac`.`idleasing_tipo_rac` ORDER BY `leasing_tasas`.`tipo_de_rac`, `leasing_tasas`.`limite_superior`");
$xHG->addList();
$xHG->addKey("clave");

$xHG->col("tipo_de_rac", "TR.TIPO DE RAC", "10%");

$xHG->col("frecuencia", "TR.FRECUENCIA", "10%");

$xHG->col("limite_inferior", "TR.LIMITEINFERIOR", "10%");
$xHG->col("limite_superior", "TR.LIMITESUPERIOR", "10%");


$xHG->col("tasa_ofrecida", "TR.TASA", "10%");
$xHG->col("tasa_marginal", "TR.TASAMARGINAL", "10%");
$xHG->col("tasa_vec", "TR.TASAVEC", "10%");

$xHG->col("comision_apertura", "TR.COMISION_POR_APERTURA", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
$xFRM->addHElem("<div id='iddivtasas'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/leasing-tasas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivtasas});
}
function jsAdd(){
	xG.w({url:"../frmarrendamiento/leasing-tasas.new.frm.php?", tiny:true, callback: jsLGiddivtasas});
}
function jsDel(id){
	xG.rmRecord({tabla:"leasing_tasas", id:id, callback:jsLGiddivtasas});
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
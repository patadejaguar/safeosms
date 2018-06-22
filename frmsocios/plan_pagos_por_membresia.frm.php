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
$xHP		= new cHPage("TR.PLANCUOTAS POR EACP", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();


function jsaCrearCuotas($clave, $mes){
	$xF		= new cFecha();
	$xPP	= new cEntidadPerfilDePagos($clave);
	$msg	= "";
	if($xPP->init() == true){
		$ejercicio	= $xF->anno();
		
		$xPP->setActualizarCalendario($ejercicio, $mes);
	}
	return $xPP->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaCrearCuotas', array('idkey', 'idmes'), "#idmsg");


$jxc ->process();


$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frmpagosplancuotas", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

$xSelMes	= $xF->getSelectDeMeses("idmes", $xF->mes());
$xSelMes->addEvent("jsReloadCuotas()", "onchange");

$xFRM->addHElem( $xSelMes->get("idmes", "TR.MES") );




$xPP		= new cEntidadPerfilDePagos($clave); $xPP->init();
$operacion	= $xPP->getTipoOperacion();

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivplancuotas",$xHP->getTitle());
$sql	= "SELECT   `personas_pagos_plan`.`idpersonas_aportaciones_plan`,
         `personas`.`nombre`,
         `operaciones_tipos`.`descripcion_operacion` AS `operacion`,
         `creditos_periocidadpagos`.`periocidad_de_pago` AS `periocidad`,
         `personas_pagos_plan`.`ejercicio`,
         `personas_pagos_plan`.`periodo`,
         `personas_pagos_plan`.`monto`
FROM     `personas` 
INNER JOIN `personas_pagos_plan`  ON `personas`.`codigo` = `personas_pagos_plan`.`persona` 
INNER JOIN `operaciones_tipos`  ON `operaciones_tipos`.`idoperaciones_tipos` = `personas_pagos_plan`.`tipo_de_operacion` 
INNER JOIN `creditos_periocidadpagos`  ON `creditos_periocidadpagos`.`idcreditos_periocidadpagos` = `personas_pagos_plan`.`periocidad` 
WHERE    (`personas_pagos_plan`.`tipo_de_operacion`=$operacion) AND ( `personas_pagos_plan`.`ejercicio` = getEjercicioDeTrabajo() ) AND ( `personas_pagos_plan`.`periodo` = ? ) ";


$xHG->setSQL($sql);
$xHG->addList();
$xHG->addKey("identidad_pagos_perfil");



//$xHG->col("membresia", "TR.MEMBRESIA", "10%");
$xHG->col("operacion", "TR.OPERACION", "10%");
$xHG->col("periocidad", "TR.PERIOCIDAD", "10%");

$xHG->col("monto", "TR.MONTO", "10%");
$xHG->col("prioridad", "TR.PRIORIDAD", "10%");
$xHG->col("rotacion", "TR.ROTACION", "10%");
$xHG->col("fecha_de_aplicacion", "TR.FECHA", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.identidad_pagos_perfil +')", "edit.png");
$xHG->OButton("TR.ACTUALIZAR CUOTAS", "jsUpdateCuotas('+ data.record.identidad_pagos_perfil +')", "refresh.png");


//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.identidad_pagos_perfil +')", "delete.png");
$xFRM->addHElem("<div id='iddivplancuotas'></div>");
$xFRM->addJsCode( $xHG->getJs(false) );

$xFRM->OButton("TR.NUEVO PLANCUOTAS", "jsCrearCuotas()", $xFRM->ic()->RECARGAR, "cmdactualizarcuotas", "red");

$xFRM->addAviso("", "idmsg");

$xFRM->OHidden("idkey", $clave);

//$xFRM->OHidden("idkey", $clave);


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/pagos_por_membresia.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivperfilista, w:600});
}
function jsAdd(){
	xG.w({url:"../frmsocios/pagos_por_membresia.new.frm.php?", tiny:true, callback: jsLGiddivperfilista, w:600});
}
function jsDel(id){
	//xG.rmRecord({tabla:"entidad_pagos_perfil", id:id, callback:jsLGiddivperfilista});
}
function jsCrearCuotas(){
	var idmes	= $("#idmes").val();
	xG.confirmar({msg: "CUOTA_CONFIRMA_ACTUAL", callback: jsaCrearCuotas});
}
function jsReloadCuotas(){
	var idmes	= $("#idmes").val();
	var str		= "&vars=" +  idmes;
	jsLGiddivplancuotas(str);
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
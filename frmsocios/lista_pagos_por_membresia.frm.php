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
$xHP		= new cHPage("TR.PERFILPAGO POR EACP", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();


function jsaActualizarCuotas($clave){
	$xPP	= new cEntidadPerfilDePagos($clave);
	$msg	= "";
	if($xPP->init() == true){
		$xPP->setActualizarConcepto();
	}
	return $xPP->getMessages(OUT_HTML);
}
function jsaGenerarPorTipoAportacion($clave){
	$xQL	= new MQL();
	$rs		= $xQL->getDataRecord("SELECT * FROM `personas_membresia_tipo`");
	$xTM	= new cPersonas_membresia_tipo();
	$xTA	= new cPersonas_aports_tipos();
	
	foreach($rs as $data){
		$idmembresia	= $data[$xTM->IDPERSONAS_MEMBRESIA_TIPO];
		$rs1			= $xQL->getDataRecord("SELECT * FROM `personas_aports_tipos` WHERE `estatusactivo` = 1");
		$xMem			= new cPersonasMembresiasTipos();
		$contar			= 0;
		foreach($rs1 as $data1){
			$idoperacion	= $data1[$xTA->OPERACION_TIPO_ID];
			$rotacion		= $data1[$xTA->PAGADERO];
			$xMem->addPerfil($idoperacion, 0, CREDITO_TIPO_PERIOCIDAD_MENSUAL, false, "1", $idmembresia, $contar);
			$contar++;
		}
		
	}
}

$jxc ->exportFunction('jsaActualizarCuotas', array('idkey'), "#idmsg");
$jxc ->exportFunction('jsaGenerarPorTipoAportacion', array('idkey'), "#idmsg");

$jxc ->process();


$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frmpagosperfillista", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
//$xFRM->OButton("TR.PLANCUOTAS", "", $xFRM->ic()->CALENDARIO, "yellow");

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivperfilista",$xHP->getTitle());
$sql	= "SELECT   `entidad_pagos_perfil`.`identidad_pagos_perfil`,
         `personas_membresia_tipo`.`descripcion_membresia_tipo` AS `membresia`,
         `operaciones_tipos`.`descripcion_operacion` AS `operacion`,
         `creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
         `entidad_pagos_perfil`.`monto`,
         `entidad_pagos_perfil`.`prioridad`,
         `entidad_pagos_perfil`.`rotacion`,
         `entidad_pagos_perfil`.`fecha_de_aplicacion`
FROM     `entidad_pagos_perfil` 
INNER JOIN `personas_membresia_tipo`  ON `entidad_pagos_perfil`.`tipo_de_membresia` = `personas_membresia_tipo`.`idpersonas_membresia_tipo` 
INNER JOIN `creditos_periocidadpagos`  ON `entidad_pagos_perfil`.`periocidad` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
INNER JOIN `operaciones_tipos`  ON `entidad_pagos_perfil`.`tipo_de_operacion` = `operaciones_tipos`.`idoperaciones_tipos`";

$xHG->setSQL($sql);
$xHG->addList();
$xHG->setOrdenar();

$xHG->addKey("identidad_pagos_perfil");

$xHG->col("membresia", "TR.MEMBRESIA", "20%");
$xHG->col("operacion", "TR.OPERACION", "20%");
$xHG->col("periocidad", "TR.PERIOCIDAD", "10%");
$xHG->col("prioridad", "TR.PRIORIDAD", "10%");
$xHG->col("rotacion", "TR.ROTACION", "10%");
$xHG->col("monto", "TR.MONTO", "10%");

//$xHG->col("fecha_de_aplicacion", "TR.FECHA", "10%");

$xFRM->OButton("TR.GENERAR POR APORTACION", "jsGenerarPorTipoAportacion()", $xFRM->ic()->AUTOMAGIC);

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");

$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.identidad_pagos_perfil +')", "edit.png");
$xHG->OButton("TR.ACTUALIZAR CUOTAS", "jsUpdateCuotas('+ data.record.identidad_pagos_perfil +')", "refresh.png");
$xHG->OButton("TR.PLANCUOTAS", "jsPlanMembresia('+ data.record.identidad_pagos_perfil +')", "calendar.png");

//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.identidad_pagos_perfil +')", "delete.png");
$xFRM->addHElem("<div id='iddivperfilista'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );

$xFRM->addAviso("", "idmsg");

$xFRM->OHidden("idkey", 0);

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
function jsPlanMembresia(id){
	xG.w({url:"../frmsocios/plan_pagos_por_membresia.frm.php?clave=" + id, tab:true, callback: jsLGiddivperfilista, w:600});
}
function jsDel(id){
	//xG.rmRecord({tabla:"entidad_pagos_perfil", id:id, callback:jsLGiddivperfilista});
}
function jsGenerarPorTipoAportacion(){
	xG.confirmar({msg: "MSG_CONFIRMA_IMPORTAR", callback: function(){
			xG.postajax("jsLGiddivperfilista()");
			jsaGenerarPorTipoAportacion();
			
		}});
}
function jsUpdateCuotas(id){
	//xG.rmRecord({tabla:"entidad_pagos_perfil", id:id, callback:jsLGiddivperfilista});
	$("#idkey").val(id);
	xG.confirmar({msg: "CUOTA_CONFIRMA_ACTUAL", callback: jsaActualizarCuotas});
}


</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
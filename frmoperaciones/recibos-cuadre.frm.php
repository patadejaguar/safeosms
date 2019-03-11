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
$xHP		= new cHPage("TR.CUADRE DE RECIBO", HP_FORM);
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

$echale         = parametro("echale", false, MQL_BOOL);
$positivos      = parametro("positivos", false, MQL_BOOL);

$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->addCerrar();
if(strpos(strtolower(EACP_NAME), "echale") === false){
	//$xFRM->OButton("Echale", "jsGoToEchale()", $xFRM->ic()->EMPRESA, "", "yellow");
} else {
	$echale		= true;
}

$xFRM->OButton("Positivos", "jsGoToPositivos()", $xFRM->ic()->FILTRO, "", "yellow");

$xFRM->addHElem( $xSel->getListaDeTiposDeOperacion("idtipo", OPERACION_CLAVE_PAGO_MORA)->get(true) );

$xFRM->OCheck("TR.AUTOMATICO", "idauto", false);

$xFRM->OCheck("TR.RECARGAR", "idreload", false);
//$xFRM->OCheck("TR.POSITIVOS", "idpositivos", false);


$ByPos      = ($positivos == false) ? "" : " AND (`operaciones_recibos`.`total_operacion` - `num_operaciones_por_rec`.`sum_ops`) > 0 ";

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddiv",$xHP->getTitle());

$xHG->setSQL("SELECT   `operaciones_recibos`.`idoperaciones_recibos` AS `recibo`,
         `operaciones_recibos`.`fecha_operacion` AS `fecha`,
         `operaciones_recibos`.`numero_socio` AS `persona`,
         `operaciones_recibos`.`docto_afectado` AS `documento`,`operaciones_recibos`.`periodo_de_documento` AS `periodo`,
         `operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
         `operaciones_recibos`.`total_operacion` AS `total`,
         `num_operaciones_por_rec`.`sum_ops` AS `suma_operaciones`,
		 (`operaciones_recibos`.`total_operacion` - `num_operaciones_por_rec`.`sum_ops`) AS `diferencia` 
FROM     `operaciones_recibos` 
INNER JOIN `operaciones_recibostipo`  ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.`idoperaciones_recibostipo` 
INNER JOIN `num_operaciones_por_rec`  ON `operaciones_recibos`.`idoperaciones_recibos` = `num_operaciones_por_rec`.`recibo_afectado` 
WHERE `operaciones_recibos`.`total_operacion` <> `num_operaciones_por_rec`.`sum_ops` AND `afectacion_en_flujo_efvo` !='ninguna' $ByPos ");

$xHG->addList();
$xHG->addKey("recibo");

$xHG->col("recibo", "TR.RECIBO", "10%");
$xHG->col("fecha", "TR.FECHA", "10%");
$xHG->col("persona", "ID", "10%");
$xHG->col("documento", "TR.DOCUMENTO", "10%");
$xHG->col("periodo", "TR.PERIODO", "10%");

$xHG->col("tipo", "TR.TIPO", "10%");

$xHG->ColMoneda("total", "TR.TOTAL", "10%");
$xHG->ColMoneda("suma_operaciones", "TR.SUMA", "10%");
$xHG->ColMoneda("diferencia", "TR.DIFERENCIA", "10%");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.recibo +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.recibo +')", "delete.png");

$xHG->OButton("TR.CUADRE", "jsCuadrar('+ data.record.recibo +','+ data.record.diferencia +')", "balance.png");
$xHG->OButton("TR.REPORTE", "jsReporte('+ data.record.recibo +')", "employee.png");

$xHG->setOrdenar();

$xFRM->addHElem("<div id='iddiv'></div>");

$xFRM->addJsCode( $xHG->getJs(true) );

if($positivos == false){
    $xFRM->OHidden("positivos", "false");
} else {
    $xFRM->OHidden("positivos", "true");
}
if($echale == false){
    $xFRM->OHidden("echale", "false");
} else { 
    $xFRM->OHidden("echale", "true");
}


echo $xFRM->get();


?>
<script>
var xG	= new Gen();

function jsGoToEchale(){
	xG.go({url:"../frmoperaciones/recibos-cuadre.frm.php?echale=true&positivos=" + $("#positivos").val() });
}
function jsGoToPositivos(){
	xG.go({url:"../frmoperaciones/recibos-cuadre.frm.php?positivos=true&echale=" + $("#echale").val() });
}

function jsCuadrar(idrecibo, iddif){
	//var idrecibo 	= d.recibo;
	//var iddif		= d.diferencia;
	var auto		= $("#idauto").prop("checked");
	var reload		= $("#idreload").prop("checked");
	var idtipo		= $("#idtipo").val();
	var xRec		= new RecGen();
	var run 		= true;
	var echale		= $("#echale").val();
	if(echale == 'true'){
		auto		= false;
	}
	
	
	if(iddif <= 0){
		xRec.setCuadrar({recibo: idrecibo, callback: setMessage});
	} else {
		if(auto == true){
			xRec.addOperacion({recibo: idrecibo, monto: iddif, tipo: idtipo, callback:setMessage});
		} else {
			xG.w({tiny:true, url : "../frmoperaciones/operaciones.mvtos.add.frm.php?cuadre=true&recibo=" + idrecibo + "&tinybox=true&monto=" + iddif + "&tipo=" + idtipo + "&echale=" + echale,w:480, callback:jsLGiddiv });//, 
		}
	}
	
	// 

}
function jsReporte(idrecibo){
	xG.w({blank:true, url : "../rptoperaciones/rpt_consulta_recibos_individual.php?recibo=" + idrecibo });
}
function setMessage(obj){
	var reload		= $("#idreload").prop("checked");
	
	if(typeof obj != "undefined"){
		
		if(typeof obj.message != "undefined"){
			xG.alerta({msg: obj.message});
		}
	}	
	
	if(reload == true){
		jsLGiddiv();
	}
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
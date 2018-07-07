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
$xHP		= new cHPage("TR.EDITAR OPERACIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

//function jsaRemParcialidad(){}

//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

$documento		= parametro("cuenta", 0, MQL_INT); $documento = parametro("idcuenta", $documento, MQL_INT);
$documento		= parametro("credito", $documento, MQL_INT); $documento = parametro("idsolicitud", $documento, MQL_INT); $documento = parametro("solicitud", $documento, MQL_INT);

$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT); $recibo	= parametro("i", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");


$SinCeros		= parametro("sinceros", false, MQL_BOOL);
$EsPlan			= parametro("activeplan", false, MQL_BOOL);
$ByCeros		= ($SinCeros == false) ? "" : " AND `operaciones_mvtos`.`afectacion_real` > 0 ";

if($recibo > 0 AND $documento <= DEFAULT_CREDITO){
	$xRec		= new cReciboDeOperacion(false, false, $recibo);
	if($xRec->init() == true){
		$documento	= $xRec->getCodigoDeDocumento();
	}
}


$xHP->addJTableSupport();
$xHP->init();
$ByRecibo	= $xLi->OFiltro()->OperacionesPorRecibo($recibo);
$ByDocto	= $xLi->OFiltro()->OperacionesPorDocumento($documento);
$ByPersona	= $xLi->OFiltro()->OperacionesPorPersona($persona);

$sql		= "SELECT   TRIM( CONCAT( `socios_general`.`nombrecompleto`, ' ' ,
         `socios_general`.`apellidopaterno`, ' ' , 
         `socios_general`.`apellidomaterno` ) ) AS `persona`,
         `operaciones_mvtos`.`idoperaciones_mvtos` AS `clave`,
         `operaciones_mvtos`.`fecha_operacion` AS `fecha`,
         `operaciones_mvtos`.`fecha_afectacion` AS `afectacion`,
         `operaciones_mvtos`.`recibo_afectado` AS `recibo`,
         `operaciones_mvtos`.`docto_afectado` AS `documento`,
         `operaciones_tipos`.`descripcion_operacion` AS `operacion`,

		`operaciones_mvtos`.`tipo_operacion` AS `tipo_operacion`,

         `operaciones_mvtos`.`afectacion_real` AS `monto`,
         `operaciones_mvtos`.`periodo_socio` AS `periodo`,
         `operaciones_mvtos`.`saldo_anterior`,
         `operaciones_mvtos`.`saldo_actual`
FROM     `operaciones_mvtos` 
INNER JOIN `socios_general`  ON `operaciones_mvtos`.`socio_afectado` = `socios_general`.`codigo` 
INNER JOIN `operaciones_tipos`  ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.`idoperaciones_tipos` 
WHERE `operaciones_mvtos`.`idoperaciones_mvtos` $ByDocto $ByPersona $ByRecibo $ByCeros 
ORDER BY `operaciones_mvtos`.`docto_afectado`,
         `operaciones_mvtos`.`recibo_afectado`,
         
         `operaciones_mvtos`.`periodo_socio` ASC,
		`operaciones_mvtos`.`tipo_operacion`
LIMIT 0,1000";




$xFRM	= new cHForm("frmmvtos", "operaciones_mvtos.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$xHG	= new cHGrid("iddivmvtos",$xHP->getTitle());

$xHG->setSQL($sql);
$xHG->addList();
$xHG->addKey("clave");
$xHG->setOrdenar();

if($ByPersona == "" AND $EsPlan == false){
	$xHG->col("persona", "TR.NOMBRE_COMPLETO", "20%");
}
if($ByDocto == "" AND $EsPlan == false){
	$xHG->col("documento", "TR.DOCUMENTO", "10%");
}
if($ByRecibo == "" AND $EsPlan == false){
	$xHG->col("recibo", "TR.RECIBO", "10%");
}

$xHG->col("clave", "TR.CLAVE", "10%");
$xHG->col("operacion", "TR.OPERACION", "20%");

if($EsPlan == false){
	$xHG->col("fecha", "TR.FECHA", "10%");
}

$xHG->OColFunction("afectacion", "TR.AFECTADO", "10%", "jsRenderFecha");

$xHG->OColFunction("periodo", "TR.PERIODO", "5%", "jsRenderPeriodo");
$xHG->OColFunction("monto", "TR.MONTO", "10%", "jsRenderMonto");
$xHG->OColFunction("saldo_anterior", "TR.SALDO_INICIAL", "10%", "jsRenderInicial");
$xHG->OColFunction("saldo_actual", "TR.SALDO_FINAL", "10%", "jsRenderFinal");


//$xHG->OColFunction("sucursal", "TR.HERRAMIENTAS", "10%", "jsRenderParc");
//$xHG->col("sucursal", "TR.SUCURSAL", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
$xHG->OButton("TR.ELIMINAR PARCIALIDAD", "jsDelParcialidad('+ data.record.periodo +')", "delete-all.png");


$xCred	= new  cCredito($documento);
if($xCred->init() == true){
	$xFRM->addHElem( $xCred->getFichaMini() );
}

$xFRM->addHElem("<div id='iddivmvtos'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );

$xFRM->OHidden("idrecibo", $recibo);
$xFRM->OHidden("idcredito", $documento);


$xFRM->addCerrar();

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsRenderFecha(data){
	var id 	= data.record.clave;
	var vv	= data.record.afectacion;
	//$("#id-afectacion-" + id).pickadate({format: 'dd-mm-yyyy',formatSubmit:'yyyy-mm-dd', editable : true });
	return "<input type=\"text\" data-clave=\"" + id + "\" data-campo=\"fecha_afectacion\" id=\"afect_" + id + "\" value=\"" + vv +  "\" onchange=\"jsSetUpdate(this)\" class=\"date\" style=\"max-width:8em !important;\">";
}
function jsRenderPeriodo(data){
	var id 	= data.record.clave;
	var vv	= data.record.periodo;
	return "<input type=\"number\" data-clave=\"" + id + "\" data-campo=\"periodo_socio\" id=\"perio_" + id + "\" value=\"" + vv +  "\" onchange=\"jsSetUpdate(this)\" class=\"mny\" style=\"max-width:4em !important;\">";
}
function jsRenderMonto(data){
	var id 	= data.record.clave;
	var vv	= data.record.monto;
	//var vti	= data.record.tipo_operacion;
	
	var ctrl= "<input type=\"number\" data-clave=\"" + id + "\" data-campo=\"afectacion_real\" id=\"monto_" + id + "\" value=\"" + vv +  "\" onchange=\"jsSetUpdate(this)\" class=\"mny\" style=\"max-width:8em !important;\">";
	
	return ctrl;
}
function jsRenderInicial(data){
	var id 	= data.record.clave;
	var vv	= data.record.saldo_anterior;
	return "<input type=\"number\" data-clave=\"" + id + "\" data-campo=\"saldo_anterior\" id=\"sdoi_" + id + "\" value=\"" + vv +  "\" onchange=\"jsSetUpdate(this)\" class=\"mny\" style=\"max-width:8em !important;\">";
}
function jsRenderFinal(data){
	var id 	= data.record.clave;
	var vv	= data.record.saldo_actual;
	return "<input type=\"number\" data-clave=\"" + id + "\" data-campo=\"saldo_actual\" id=\"sdof_" + id + "\" value=\"" + vv +  "\" onchange=\"jsSetUpdate(this)\" class=\"mny\" style=\"max-width:8em !important;\">";
}
function jsDel(id){
	xG.rmRecord({tabla:"operaciones_mvtos", id:id, callback:jsLGiddivmvtos});
}
function jsRenderParc(data){
	var idx	= data.record.clave;
	
	var txt	= '<img src="../images/delete.png" onclick="jsDel(' + idx  +');return false" alt="Eliminar" title="Eliminar" class="jtable-command-button" style="width:2em;height:2em;margin:0.2em">';
	//txt		+= '<img src="../images/delete-all.png" onclick="jsDel(' + idx  +');return false" alt="Eliminar Parcialidad" title="Eliminar Parcialidad" class="jtable-command-button" style="width:2em;height:2em;margin:0.2em">';

	return txt;
}
function jsDelParcialidad(idp){
	var idp 		= idp;
	var ops			= CNF.mql.del;
	var idrecibo 	= $("#idrecibo").val();
	var idcredito 	= $("#idcredito").val(); 
	
	//xG.rmRecord({tabla:"operaciones_mvtos", id:id, callback:jsLGiddivmvtos});
	xG.confirmar({msg : "MSG_CONFIRM_RM_PARC", callback: function(){
			xG.svc({url: "letras.svc.php?credito="+ idcredito + "&letra=" + idp + "&cmd=" + ops, callback:jsReloadG });
		}
		});
}
function jsReloadG(){
	$('#iddivmvtos').jtable('destroy');
	jsLGiddivmvtos();
}
function jsEdit(id){
	xG.editar({tabla:'operaciones_mvtos',id:id});	
}
function jsSetUpdate(obj){
	var clave	= $(obj).attr("data-clave");
	var campo	= $(obj).attr("data-campo");
	var valor	= $(obj).val();
	var cnt		= campo + "=" + valor;
	
	$(obj).parent().addClass("tr-plan");
	//setLog(cnt);
	xG.save({tabla:'operaciones_mvtos', id:clave, content: cnt});
	xG.markTR({src:obj});
}
function jsAdd(){
	var idrecibo	= $("#idrecibo").val();
	xG.w({url:"../frmoperaciones/operaciones.mvtos.add.frm.php?recibo="+idrecibo, tiny:true, w:600, callback:jsLGiddivmvtos});
}

</script>
<?php

//$jxc ->drawJavaScript(false, true);

$xHP->fin();

?>
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
$xHP		= new cHPage("TR.TIPO_DE OPERACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

function jsaAddBase($idop, $idbase){
	$xBase	= new cBases($idbase);
	//if($xBase->init() ){
		$xBase->addMember($idop);
	//}
	return $xBase->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaAddBase', array('idoperando', 'idbase'), "#idmsg");
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
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->addCerrar();

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddiv",$xHP->getTitle());

$xHG->setSQL("SELECT `operaciones_tipos`.`idoperaciones_tipos`,
         `operaciones_tipos`.`descripcion_operacion`,
         `operaciones_tipos`.`nombre_corto`,
         IF(`operaciones_tipos`.`estatus` = 0, '" . $xFRM->l()->getT("TR.BAJA") .  "', '" . $xFRM->l()->getT("TR.ESTATUSACTIVO") . "') AS `estatus`,
         `operaciones_tipos`.`precio`,

(SELECT COUNT(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`)
FROM     `eacp_config_bases_de_integracion_miembros` INNER JOIN `eacp_config_bases_de_integracion`  ON `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = `eacp_config_bases_de_integracion`.`codigo_de_base` 
WHERE    ( `eacp_config_bases_de_integracion_miembros`.`miembro` = `operaciones_tipos`.`idoperaciones_tipos`)) AS 'items',
`operaciones_recibostipo`.`descripcion_recibostipo` AS `recibo_de_operacion`

FROM     `operaciones_tipos` INNER JOIN `operaciones_recibostipo`  ON `operaciones_tipos`.`recibo_que_afecta` = `operaciones_recibostipo`.`idoperaciones_recibostipo` ");



$xHG->addList();
$xHG->setOrdenar();

$xHG->addKey("idoperaciones_tipos");

$xHG->col("idoperaciones_tipos", "TR.CLAVE", "8%");

$xHG->col("descripcion_operacion", "TR.NOMBRE", "22%");

$xHG->col("nombre_corto", "TR.ALIAS", "12%");
$xHG->col("estatus", "TR.ESTATUS", "8%");


$xHG->col("recibo_de_operacion", "TR.RECIBO", "12%");
//$xHG->ColMoneda("precio", "TR.PRECIO", "10%");

$xHG->col("items", "TR.BASES", "8%");


//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idoperaciones_tipos +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idoperaciones_tipos +')", "delete.png");

$xHG->OButton("TR.GENERAL", "jsEditGeneral('+ data.record.idoperaciones_tipos +')", "computer-configuration.png");
$xHG->OButton("TR.CLASE", "jsEditClase('+ data.record.idoperaciones_tipos +')", "controls.png");
$xHG->OButton("TR.CLASE RECIBO", "jsEditClaseRecibos('+ data.record.idoperaciones_tipos +')", "levels.png");
$xHG->OButton("TR.FORMULAS", "jsEditFormulas('+ data.record.idoperaciones_tipos +')", "formula.png");
$xHG->OButton("TR.CANCELACION", "jsEditCancelacion('+ data.record.idoperaciones_tipos +')", "web.png");

if(MODULO_CONTABILIDAD_ACTIVADO == true){
	$xHG->OButton("TR.CONTABLE", "jsEditContable('+ data.record.idoperaciones_tipos +')", "balance.png");
}

$xHG->OButton("TR.BASES", "jsListBases('+ data.record.idoperaciones_tipos +')", "order-159.png");
$xHG->OButton("TR.AGREGAR BASES", "jsListAddBases('+ data.record.idoperaciones_tipos +')", "add.png");

//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idoperaciones_tipos +')", "edit.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idoperaciones_tipos +')", "edit.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idoperaciones_tipos +')", "edit.png");

$xFRM->OButton("TR.AGREGAR", "jsAdd()", $xFRM->ic()->AGREGAR);

$xFRM->addHElem("<div id='iddiv'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


$xFRM->OHidden("idoperando", "");
$xFRM->OHidden("idbase", "");

$xFRM->addAviso("", "idmsg");

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEditGeneral(id){
	jsActionGo(id, 'general');
}
function jsEditClase(id){
	jsActionGo(id, 'clase');
}
function jsEditFormulas(id){
	jsActionGo(id, 'formulas');
}
function jsEditContable(id){
	jsActionGo(id, 'contable');
}
function jsEditCancelacion(id){
	jsActionGo(id, 'cancelacion');
}

function jsEditClaseRecibos(id){
	jsActionGo(id, 'claserecibos');
}
function jsActionGo(id, action){
	xG.w({url:"../frmoperaciones/operaciones_tipos.edit.frm.php?clave=" + id + "&tipo=" + action, tiny:true, callback: jsLGiddiv});
}
function jsAdd(){
	xG.w({url:"../frmoperaciones/operaciones_tipos.frm.php?", tiny:true, w:800});
}
function jsListAddBases(id){
	$("#idoperando").val(id);
	
	var q	= "<?php echo base64_encode("SELECT   `eacp_config_bases_de_integracion`.`codigo_de_base`, CONCAT(`eacp_config_bases_de_integracion`.`codigo_de_base`, '-',`eacp_config_bases_de_integracion`.`descripcion`) AS `descripcion` FROM     `eacp_config_bases_de_integracion` WHERE (`eacp_config_bases_de_integracion`.`tipo_de_base` = 'de_operaciones' ) AND `estatus` = 1
AND (SELECT COUNT(*) FROM `eacp_config_bases_de_integracion_miembros` WHERE ( `eacp_config_bases_de_integracion_miembros`.`miembro` = ?) AND `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`= `eacp_config_bases_de_integracion`.`codigo_de_base`) <= 0 "); ?>";

		xG.QList({key: "codigo_de_base", label: "descripcion", url:"../svc/datos.svc.php?q=" + q + "&vars=" + id, element : $("#dlg"),func:'addNewBase', title: "Nueva Base"  });
			
}
function jsListBases(id){
	var q	= "<?php echo base64_encode("SELECT `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`, CONCAT(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`, '-', `eacp_config_bases_de_integracion`.`descripcion`) AS `descripcion`
FROM     `eacp_config_bases_de_integracion_miembros` INNER JOIN `eacp_config_bases_de_integracion`  ON `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = `eacp_config_bases_de_integracion`.`codigo_de_base` 
WHERE    ( `eacp_config_bases_de_integracion_miembros`.`miembro` = ? )"); ?>";
	xG.QList({key: "codigo_de_base", label: "descripcion", url:"../svc/datos.svc.php?q=" + q + "&vars=" + id, element : $("#dlg"), title: "Bases Actuales" });
}
function addNewBase(idb){
	$("#idbase").val(idb);
	var idop	= $("#idoperando").val();
	
	session(TINYAJAX_CALLB, "jsCerrarDiag()");
	
	xG.confirmar({msg:"¿ Desea Agregar la Operacion con ID #" +  idop + " A la base con ID " + idb + " ?", callback: jsaAddBase });
}
function jsCerrarDiag(){
	jQuery($("#dlg")).dialog("close");
}
</script>
<?php
	




$jxc ->drawJavaScript(false, true);
$xHP->fin();

?> 
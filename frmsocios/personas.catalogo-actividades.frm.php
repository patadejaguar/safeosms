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
$xHP		= new cHPage("TR.CATALOGO ACTIVIDAD_ECONOMICA", HP_FORM);
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



$xFRM	= new cHForm("frmactividades", "catalogo_activdades.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivactvidades",$xHP->getTitle());

$xHG->setSQL("SELECT 
		`personas_actividad_economica_tipos`.`clave_interna`,
        `personas_actividad_economica_tipos`.`clave_de_actividad`,
        `personas_actividad_economica_tipos`.`nombre_de_la_actividad`,
        `entidad_niveles_de_riesgo`.`nombre_del_nivel` AS `nivel_de_riesgo`,
        getBooleanMX( `personas_actividad_economica_tipos`.`califica_para_pep` ) AS `califica_para_pep`
FROM    `personas_actividad_economica_tipos` 
INNER JOIN `entidad_niveles_de_riesgo`  ON `personas_actividad_economica_tipos`.`nivel_de_riesgo` = `entidad_niveles_de_riesgo`.`clave_de_nivel` ORDER BY `entidad_niveles_de_riesgo`.`nombre_del_nivel` ASC, `personas_actividad_economica_tipos`.`califica_para_pep` DESC LIMIT 0,50");
$xHG->addList();
$xHG->addKey("clave_interna");

$xHG->col("clave_de_actividad", "TR.CLAVE", "10%");
$xHG->col("nombre_de_la_actividad", "TR.NOMBRE", "10%");


$xHG->col("nivel_de_riesgo", "TR.NIVEL_DE_RIESGO", "10%");
$xHG->col("califica_para_pep", "TR.PEPS", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave_interna +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave_interna +')", "delete.png");
$xFRM->addHElem("<div id='iddivactvidades'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
$xFRM->addCerrar();
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/catalogo_actividades.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivactvidades});
}
function jsAdd(){
	xG.w({url:"../frmsocios/catalogo_actividades.new.frm.php?", tiny:true, callback: jsLGiddivactvidades});
}
function jsDel(id){
	xG.rmRecord({tabla:"personas_actividad_economica_tipos", id:id, callback:jsLGiddivactvidades});
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
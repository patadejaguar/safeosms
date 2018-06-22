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
$xHP		= new cHPage("TR.MUNICIPIO", HP_FORM);
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



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();


/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivmunicipios",$xHP->getTitle());

$xHG->setSQL("SELECT   `general_municipios`.`idgeneral_municipios`,
         `general_municipios`.`clave_de_municipio` AS `clave`,
         `general_municipios`.`nombre_del_municipio` AS `nombre`,
         `general_estados`.`nombre` AS `estado`
FROM     `general_municipios` 
INNER JOIN `general_estados`  ON `general_municipios`.`clave_de_entidad` = `general_estados`.`clave_numerica` WHERE `general_municipios`.`idgeneral_municipios` > 0 ");
$xHG->addList();
$xHG->addKey("idgeneral_municipios");
$xHG->setOrdenar();

$xHG->col("clave", "TR.CLAVE", "10%");
$xHG->col("nombre", "TR.NOMBRE", "30%");
$xHG->col("estado", "TR.ENTIDAD_FEDERATIVA", "30%");


$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idgeneral_municipios +')", "edit.png");

if(MODO_DEBUG == true){
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idgeneral_municipios +')", "delete.png");
}
$xEst	= $xSel->getListaDeEntidadesFed("", true);
$xEst->addEvent("onchange", "jsSetFiltraPorEntidadFed()");
$xEst->addVacio(true);

$xFRM->addHElem($xEst->get(true));
$xFRM->addHElem("<div id='iddivmunicipios'></div>");

$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmtipos/municipios.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivmunicipios});
}
function jsAdd(){
	var idesta	= entero($("#identidadfederativa").val());
	xG.w({url:"../frmtipos/municipios.new.frm.php?estado=" + idesta, tiny:true, callback: jsLGiddivmunicipios});
}
function jsDel(id){
	//xG.rmRecord({tabla:"general_municipios", id:id, callback:jsLGiddivmunicipios});
}
function jsSetFiltraPorEntidadFed(){
	var idesta	= entero($("#identidadfederativa").val());
	if(idesta >0){
		var str		= base64.encode(" AND general_estados.clave_numerica="+ idesta);
		var str		= "&w=" + str;
		$("#iddivmunicipios").jtable("destroy");
		jsLGiddivmunicipios(str);
	}
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
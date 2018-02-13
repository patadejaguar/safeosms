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
$xHP		= new cHPage("TR.ASEGURADORAS", HP_FORM);
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



/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivaseguradoras",$xHP->getTitle());

$xHG->setSQL("SELECT   `personas_aseguradoras`.`idpersonas_aseguradoras`,
         `personas_aseguradoras`.`persona`,
         `socios`.`nombre`,
         `personas_aseguradoras`.`alias`
FROM     `personas_aseguradoras`
INNER JOIN `socios`  ON `personas_aseguradoras`.`persona` = `socios`.`codigo` WHERE `personas_aseguradoras`.`estatus` = 1 ");
$xHG->addList();
$xHG->addKey("idpersonas_aseguradoras");
$xHG->col("persona", "TR.PERSONA", "10%");
$xHG->col("nombre", "TR.NOMBRE", "50%");
$xHG->col("alias", "TR.ALIAS", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idpersonas_aseguradoras +')", "edit.png");
$xHG->OButton("TR.BAJA", "jsBaja('+ data.record.idpersonas_aseguradoras +')", "archive.png");

if(MODO_DEBUG == true){
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idpersonas_aseguradoras +')", "delete.png");
}

$xFRM->addHElem("<div id='iddivaseguradoras'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/personas-aseguradoras.edit.frm.php?clave=" + id, tiny:true, callback: jsReloadGrid});
}
function jsAdd(){
	xG.w({url:"../frmsocios/personas-aseguradoras.new.frm.php?", tiny:true, callback: jsReloadGrid});
}
function jsDel(id){
	xG.rmRecord({tabla:"personas_aseguradoras", id:id, callback:jsReloadGrid});
}
function jsBaja(id){
	xG.recordInActive({tabla:"personas_aseguradoras", id:id, callback:jsReloadGrid});
}
function jsReloadGrid(){
	$('#iddivaseguradoras').jtable('destroy');
	jsLGiddivaseguradoras();
}
</script>
<?php



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
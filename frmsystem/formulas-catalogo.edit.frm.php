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
$xHP		= new cHPage("TR.EDITAR FORMULA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->init();


/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cGeneral_formulas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmeditformula", "formulas-catalogo.edit.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();
if($action == MQL_MOD){	
	$code_type 			= parametro("code_type", "", MQL_RAW);
	$describe 			= parametro("description_short", "", MQL_RAW);
	$formula 			= parametro("estructura_de_la_formula", "", MQL_RAW);
	$aplicado_a 		= parametro("aplicado_a", "", MQL_RAW);
	
	
	
	
} else {


$xFRM->addGuardar("jsGuardar()");
$xFRM->OHidden("aplicado_a", $xTabla->aplicado_a()->v());
$xFRM->OSelect("code_type", $xTabla->code_type()->v() , "TR.TIPO", array("php"=>"PHP", "js"=>"JS", "human"=>"HUMAN", "null"=>"NULL", "mysql"=>"MYSQL"));
$xFRM->OText("description_short", $xTabla->description_short()->v(), "TR.DESCRIPCION");

$xFRM->OTextArea("estructura_de_la_formula", $xTabla->estructura_de_la_formula()->v(), "TR.FORMULA");

}

//$xFRM->addCRUD($xTabla->get(), true);
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();
?>
<script>
var xG	= new Gen();

function jsGuardar(){
	$("#estructura_de_la_formula").val( base64.encode( $("#estructura_de_la_formula").val()  ) );
	xG.enviar({form:"frmeditformula"}); 	
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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
$xHP		= new cHPage("TR.LISTA DE EMPRESAS", HP_FORM);
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



$xFRM	= new cHForm("frmempresas", "empresas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

/*	$mtbl = new cTabla($xLi->getListadoDeEmpresas(false, false, false));
	$mtbl->setKeyField("idsocios_aeconomica_dependencias");
	$mtbl->OButton("TR.Editar", "var xEmp = new EmpGen(); xEmp.setActualizarDatos(" . HP_REPLACE_ID . ")", $xFRM->ic()->EDITAR);
	$mtbl->OButton("TR.Panel", "jsGoToPanel(" . HP_REPLACE_ID . ")", $xFRM->ic()->EJECUTAR);
	$mtbl->setWithMetaData();
	$xFRM->addHTML( $mtbl->Show() );
	
	*/
//$xFRM->OButton("TR.AGREGAR", "var xEmp = new EmpGen(); xEmp.setAgregar()", $xFRM->ic()->AGREGAR);

//$xFRM->OButton("TR.AGREGAR", "jsAdd()", $xFRM->ic()->AGREGAR);

$xFRM->addCerrar();

$xHG	= new cHGrid("iddivempresas",$xHP->getTitle());

$xHG->setSQL($xLi->getListadoDeEmpresas(false, false, false));
$xHG->addList();

		
$xHG->addKey("clave");
$xHG->col("clave_de_persona", "TR.CLAVE_DE_PERSONA", "10%");
$xHG->col("alias", "TR.NOMBRE_CORTO", "20%");
$xHG->col("nombre", "TR.NOMBRE", "40%");
$xHG->col("telefono", "TR.TELEFONO", "10%");
//$xHG->col("", "TR.", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
$xHG->OButton("TR.PANEL", "jsGoToPanel('+ data.record.clave +')", "controls.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idsocios_aeconomica_dependencias +')", "delete.png");
$xFRM->addHElem("<div id='iddivempresas'></div>");

$xHG->setOrdenar();

$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
var xEmp	= new EmpGen();
function jsEdit(id){
	xG.w({url:"../frmempresas/empresas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivempresas});
}
function jsAdd(){
	xG.w({url:"../frmempresas/empresas.new.frm.php?", tiny:true, callback: jsLGiddivempresas});
}
function jsDel(id){
	//xG.rmRecord({tabla:"socios_aeconomica_dependencias", id:id, callback:jsLGiddivempresas});
}
function jsGoToPanel(id){
	xEmp.goToPanel(id);
}
</script>
<?php
	


//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>
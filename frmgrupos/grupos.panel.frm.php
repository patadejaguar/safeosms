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
$xHP		= new cHPage("TR.GRUPOS_SOLIDARIOS PANEL", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xHT		= new cHTabs();
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



$xHP->addJTableSupport();
$xHP->init();

$xFRM		= new cHForm("frmgrupospanel", "../frmgrupos/grupos.panel.frm.php");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$xGru		= new cGrupo($grupo);
if($xGru->init() == true){
	$xFRM->addCerrar();
	$xFRM->OButton("TR.AGREGAR CREDITO", "jsAddCredito()", $xFRM->ic()->CREDITO, "idaddcrednew", "credito");
	
	$xFRM->OHidden("idgrupo", $grupo);
	
	
	$xFRM->addHElem($xGru->getFicha(true));
	
	
	$xHG	= new cHGrid("idintegrantes","TR.INTEGRANTES");
	$xHG->setSQL($xLi->getListadoDePersonasV2(" AND ( `socios_general`.`grupo_solidario` = $grupo ) ", "0,100"));
	$xHG->addList();
	$xHG->setOrdenar();
	$xHG->addKey("codigo");
	$xHG->col("codigo", "TR.CODIGO", "10%");
	$xHG->col("nombre", "TR.NOMBRE_COMPLETO", "60%");
	$xHG->col("curp", "TR.CURP", "10%");
	
	$xHG->OToolbar("TR.AGREGAR INTEGRANTE", "jsAddIntegrante()", "grid/add.png");
	
	//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idsocios_grupossolidarios +')", "edit.png");
	$xFRM->addJsCode( $xHG->getJs(true) );
	
	//$xFRM->OButton("TR.AGREGAR INTEGRANTE", "jsAddIntegrante()", $xFRM->ic()->AGREGAR, "idcmdaddint", "blue");
	$xHT->addTab("TR.INTEGRANTES","", "idintegrantes");
	
	//==================== Solicitudes
	
	
	$xHG2	= new cHGrid("idsolicitudes","TR.SOLICITUDES");
	$xHG2->setSQL("SELECT * FROM socios");
	$xHG2->addList();
	$xHG2->addKey("codigo");
	$xHG2->col("codigo", "TR.CODIGO", "10%");
	$xHG2->col("nombre", "TR.NOMBRE_COMPLETO", "60%");
	$xHG2->col("curp", "TR.CURP", "10%");
	
	$xHG2->OToolbar("TR.AGREGAR INTEGRANTE", "jsAddIntegrante()", "grid/add.png");
	
	
	//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idsocios_grupossolidarios +')", "edit.png");
	//$xFRM->addJsCode( $xHG2->getJs(true) );
	
	
	$xFRM->addHElem( $xHT->get());

} else {
	$xFRM->addEnviar();
	$xFRM->addGrupoBasico();	
}
echo $xFRM->get();
?>
<script>
var xG 		= new Gen();
var xP		= new PersGen();
//var xGpo	= new 

function jsAddIntegrante(){
	var idgpo	= entero($("#idgrupo").val());
	xP.getFormaBusqueda({args : "&grupo=" + idgpo, next : "addgrupo", callback: jsLGidintegrantes});	
}
function jsAddCredito(){
	//var mmsg	= base64.decode(jsonWords.HTML_WARN_GRUPOADDCRED);
	xG.confirmar({msg: "MSG_CONFIRMA_ADD_CRED", callback: jsConfirmAddCredito});
}
function jsConfirmAddCredito(){
	var idgpo	= entero($("#idgrupo").val());
	xG.w({url:"../frmgrupos/grupos-add-solicitud.frm.php?grupo=" + idgpo});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
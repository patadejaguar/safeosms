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
$xHP		= new cHPage("TR.SUCURSALES", HP_FORM);
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



$xFRM		= new cHForm("frmsucursales", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivsucursales",$xHP->getTitle());

$xHG->setSQL($xLi->getListadoDeSucursales());
$xHG->addList();
$xHG->setOrdenar();
$xHG->col("clave", "TR.CLAVE", "10%");
$xHG->col("nombre", "TR.NOMBRE", "10%");

//$xHG->col("persona", "TR.PERSONA", "10%");
//$xHG->col("clave_en_numero", "TR.CLAVE EN NUMERO", "10%");
if(getEsModuloMostrado(false, MMOD_CONTABILIDAD)){
	$xHG->col("centro_de_costo", "TR.CENTRO_DE_COSTOS", "10%");
}
if(SISTEMA_CAJASLOCALES_ACTIVA == true){
	$xHG->col("caja_local", "TR.CAJA_LOCAL", "10%");
}
$xHG->col("hora_inicial", "TR.HORARIO INICIAL", "10%");
$xHG->col("hora_final", "TR.HORARIO FINAL", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");

$xHG->OButton("TR.DOMICILIO", "var xPv=new PersVivGen();xPv.getVerVivienda('+ data.record.iddomicilio +')", "direction-signs.png");

$xHG->OButton("TR.EDITAR", "jsEdit(\''+ data.record.clave +'\')", "edit.png");

//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");

$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.clave +')", "undone.png");

$xSoc	= new cSocio(EACP_ID_DE_PERSONA);
if($xSoc->init() == true){
	$xFRM->addHElem( $xSoc->getFicha() );
	
	$xFRM->OButton("TR.PANEL PERSONA", "var xP=new PersGen();xP.goToPanel(" . $xSoc->getCodigo() . ")", $xFRM->ic()->PERSONA, "cmdpanelpers", "persona");
	$xFRM->OButton("TR.Agregar Referencias_Domiciliarias", "var xP= new PersGen();xP.setAgregarVivienda(" . $xSoc->getCodigo() . ")", "vivienda", "cmdagregarvivienda" );
	
}

$xFRM->addHElem("<div id='iddivsucursales'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


$xFRM->addCerrar();

echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmtipos/sucursales.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivsucursales});
}
function jsAdd(){
    xG.w({url:"../frmtipos/sucursales.new.frm.php?", tiny:true, callback: jsLGiddivsucursales});
}
function jsDel(id){
    //xG.rmRecord({tabla:"tmp_1274089405", id:id, callback:jsLGiddivsucursales });
}
function jsDeact(id){
   //xG.recordInActive({tabla:"tmp_1274089405", id:id, callback:jsLGiddivsucursales, preguntar:true });
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>
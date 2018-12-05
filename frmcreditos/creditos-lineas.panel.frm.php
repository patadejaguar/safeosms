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
$xHP		= new cHPage("TR.PANEL CREDITOS_LINEAS", HP_FORM);
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


$xHP->addJTableSupport();
$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xTO			= new cCreditosDatosDeOrigen();

$xLin			= new cCreditosLineas($clave);
if($xLin->init() == true){
	
	$xFRM->addHElem($xLin->getFicha());
	
	$xTab	= new cHTabs();
	
	$sqlG	= "SELECT
         `personas`.`codigo`,
         `personas`.`nombre`,
         `creditos`.`solicitud`,
         `creditos`.`convenio` AS `producto`,
         `creditos`.`fecha_ministracion`,
         `creditos`.`monto_autorizado`,
         `creditos`.`plazo`,
         `creditos`.`saldo_actual`,
         `creditos`.`estatusactivo`
FROM     `creditos_datos_originacion` 
INNER JOIN `creditos`  ON `creditos_datos_originacion`.`credito` = `creditos`.`solicitud` 
INNER JOIN `personas`  ON `creditos`.`numero_socio` = `personas`.`codigo` 
WHERE ( `creditos_datos_originacion`.`clave_vinculada` = $clave ) AND ( `creditos_datos_originacion`.`tipo_originacion` = " . $xTO->ORIGEN_LINEA . " ) 
AND ( `creditos`.`saldo_actual` >0 ) AND ( `creditos`.`estatusactivo` = 1 ) ";
	
	
	
	$xHG    = new cHGrid("iddivcredorgs","TR.CREDITOS");
	
	$xHG->setSQL($sqlG);
	$xHG->addList();
	$xHG->setOrdenar();
	

	$xHG->col("codigo", "TR.CODIGO", "10%");
	$xHG->col("nombre", "TR.NOMBRE", "10%");
	$xHG->col("solicitud", "TR.SOLICITUD", "10%");
	$xHG->col("convenio", "TR.CONVENIO", "10%");
	$xHG->col("fecha_ministracion", "TR.FECHA MINISTRACION", "10%");
	$xHG->col("monto_autorizado", "TR.MONTO AUTORIZADO", "10%");
	$xHG->col("plazo", "TR.PLAZO", "10%");
	$xHG->col("saldo_actual", "TR.SALDO ACTUAL", "10%");
	
	
	//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
	
	//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record. +')", "edit.png");
	//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record. +')", "delete.png");
	
	//$xFRM->addHElem("<div id='iddivcredorgs'></div>");
	
	$xTab->addTab("TR.CREDITOS", "<div id='iddivcredorgs'></div>", "idtabcreds");
	
	$xFRM->addHElem($xTab->get());
	
	$xFRM->addJsCode( $xHG->getJs(true) );
	
	$xFRM->OButton("TR.AGREGAR CREDITO", "jsAddCredito()", $xFRM->ic()->CREDITO, "idlineaddcreds", "credito");
	$xFRM->OButton("TR.REPORTE CREDITOS_LINEAS", "var xC=new CredGen(); xC.getReporteDeLinea({id:$clave})", $xFRM->ic()->REPORTE);
	
	$xFRM->OButton("TR.EDITAR", "jsEdit()", $xFRM->ic()->EDITAR, "idcmdeditlinea", "editar");
	
	$xFRM->addCerrar();
	
	
	$xFRM->OHidden("idclave", $clave);
	
}


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
var xC	= new CredGen();

function jsAddCredito(){
	
	var id	= $("#idclave").val();
	xC.addCredito({origen:CNF.credito.origen.lineas, idorigen:id});
	
}
function jsEdit(){
	var id	= $("#idclave").val();
	xG.w({url:"../frmcreditos/creditos-lineas.edit.frm.php?clave=" + id, tiny:true});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
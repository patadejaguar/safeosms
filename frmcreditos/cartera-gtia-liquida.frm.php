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
$xHP		= new cHPage("TR.CARTERA GTIALIQ", HP_FORM);
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
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones	= parametro("idobservaciones");

$vertodos		= parametro("todos", false, MQL_BOOL);


$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frmcarteragtialiq", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();


$BySdo		= ($vertodos == true) ? "" : " AND pendiente >  " . TOLERANCIA_SALDOS .  " ";

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddiv",$xHP->getTitle());

$xHG->setSQL("SELECT   `personas`.`codigo`,
         `personas`.`nombre`,
         `creditos`.`solicitud`,
         ROUND((( `creditos`.`monto_autorizado` *  `creditos`.`tasa_gtialiq`) /100),2) AS `monto`,
         ROUND((( IF(`creditos`.`estatusactivo`=1,`creditos`.`saldo_actual`, `creditos`.`monto_autorizado`) *  `creditos`.`tasa_gtialiq`) /100),2) AS `exigible`,
         IF(ISNULL(`garantia_liquida`.`monto`),0,`garantia_liquida`.`monto`) AS `pagado`,
         ROUND(
         ( ROUND((( IF(`creditos`.`estatusactivo`=1,`creditos`.`saldo_actual`, `creditos`.`monto_autorizado`) *  `creditos`.`tasa_gtialiq`) /100),2) - IF(ISNULL(`garantia_liquida`.`monto`),0,`garantia_liquida`.`monto`)),2)
         AS `pendiente`
         
         
FROM     `garantia_liquida` 
RIGHT OUTER JOIN `creditos`  ON `garantia_liquida`.`docto_afectado` = `creditos`.`solicitud` 
INNER JOIN `personas`  ON `creditos`.`numero_socio` = `personas`.`codigo`

WHERE `personas`.`codigo` > 0

HAVING monto >0 $BySdo");
$xHG->addList();
$xHG->setOrdenar();

$xHG->col("codigo", "TR.CODIGO", "10%");
$xHG->col("nombre", "TR.NOMBRE", "40%");
$xHG->col("solicitud", "TR.SOLICITUD", "8%");
$xHG->col("monto", "TR.ORIGINAL", "8%");

$xHG->col("exigible", "TR.EXIGIBLE", "8%");
$xHG->col("pagado", "TR.PAGADO", "8%");
$xHG->col("pendiente", "TR.PENDIENTE", "8%");


$xHG->OButton("TR.PANEL", "var xC=new CredGen();xC.goToPanelControl('+ data.record.solicitud +')", "controls.png");


$xHG->OButton("TR.COBRO", "jsAddCobro('+ data.record.solicitud +','+ data.record.pendiente +')", "money.png");
if($vertodos == true){
	$xHG->OButton("TR.COBRO TOTAL", "jsAddCobroTodo('+ data.record.solicitud +','+ data.record.monto +')", "piece.png");
}

$xHG->OButton("TR.DEVOLVER", "jsAddDevolver('+ data.record.solicitud +','+ data.record.pagado +')", "right-arrow.png");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record. +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record. +')", "delete.png");

//$xFRM->addBuscar("jsBuscar()");
$xFRM->OBuscar("idbuscar", "", "", "jsAutoBuscar");

$xFRM->OButton("TR.TODOS", "jsGoTodos()",$xFRM->ic()->FILTRO,  "idvertodos", "blue");


$xFRM->addHElem("<div id='iddiv'></div>");

$xFRM->addJsCode( $xHG->getJs(true) );

echo $xFRM->get();
?>
<script>
var xG	= new Gen();

function jsAddCobro(idcredito, monto){
	if(monto >0){
		xG.w({url:"../frmcreditos/creditos.garantia-liquida.frm.php?credito=" + idcredito, tiny:true, callback: jsLGiddiv});
	} else {
		xG.aviso({msg:"MSG_MONTO_REQUIRED"});
		
	}
}
function jsAddCobroTodo(idcredito, monto){
	if(monto >0){
		xG.w({url:"../frmcreditos/creditos.garantia-liquida.frm.php?pagolibre=true&credito=" + idcredito + "&monto=" + monto, tiny:true, callback: jsLGiddiv});
	} else {
		xG.aviso({msg:"MSG_MONTO_REQUIRED"});
		
	}
}
function jsAddDevolver(idcredito, monto){
	if(monto >0){
		xG.w({url:"../frmcreditos/creditos.garantia-liq-dev.frm.php?credito=" + idcredito, tiny:true, callback: jsLGiddiv});
	} else {
		xG.aviso({msg:"MSG_MONTO_REQUIRED"});
	}
}
function jsAutoBuscar(){
	var txt	= $("#idbuscar").val();
	var str	= "";
	if(txt !== ""){
		txt = " AND (`personas`.`codigo` LIKE '%" + txt + "%' OR `personas`.`nombre` LIKE '%" + txt + "%' OR `creditos`.`solicitud`  LIKE '%" + txt + "%') ";
		$("#iddiv").jtable("destroy");
		str	= "&w=" + base64.encode(txt);
		jsLGiddiv(str);
	}
}
function jsGoTodos(){
	xG.go({url: "../frmcreditos/cartera-gtia-liquida.frm.php?todos=true"});
}
</script>
<?php
	



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
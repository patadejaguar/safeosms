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
$xHP		= new cHPage("TR.CARTERA DE CREDITOS", HP_FORM);
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



$xFRM		= new cHForm("frmlistacreds", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivlistacreds",$xHP->getTitle());

$xHG->setSQL("SELECT   `personas`.`codigo`,
         `personas`.`nombre`,
         `personas`.`alias_dependencia`,
         `personas`.`sucursal`,
         `creditos`.`solicitud`,
         `creditos`.`modalidad`,
         `creditos`.`convenio`,
         `creditos`.`fecha_ministracion`,
         `creditos`.`monto_autorizado`,
         `creditos`.`plazo`,
         `creditos`.`fecha_vencimiento`,
         `creditos`.`saldo_actual`,
         `creditos`.`periocidad`,
         `creditos`.`estatus`,
         `creditos`.`pagos`,
         `creditos`.`estatus_credito`,
		`creditos`.`parcialidad`
FROM     `personas`
INNER JOIN `creditos`  ON `personas`.`codigo` = `creditos`.`numero_socio` WHERE (`personas`.`codigo` != " . DEFAULT_SOCIO . ") 
AND `estatusactivo`=1 AND `creditos`.`saldo_actual`>" . TOLERANCIA_SALDOS . "
ORDER BY `creditos`.`fecha_ministracion` DESC
");
$xHG->addList();
$xHG->setOrdenar();
/*$xHG->col("codigo", "TR.CODIGO", "10%");*/

$xHG->col("solicitud", "TR.SOLICITUD", "7%");
$xHG->col("nombre", "TR.NOMBRE", "20%");

//$xHG->col("alias_dependencia", "TR.EMPRESA", "10%");

/*$xHG->col("modalidad", "TR.MODALIDAD", "10%");*/
$xHG->col("convenio", "TR.PRODUCTO", "10%");
//$xHG->col("sucursal", "TR.SUCURSAL", "7%");
$xHG->col("estatus_credito", "TR.ESTATUS", "7%");

$xHG->col("fecha_ministracion", "TR.FECHA MINISTRACION", "7%");

//$xHG->col("plazo", "TR.PLAZO", "10%");
/*$xHG->col("fecha_vencimiento", "TR.FECHA VENCIMIENTO", "10%");*/


$xHG->col("periocidad", "TR.PERIOCIDAD", "7%");
/*$xHG->col("estatus", "TR.ESTATUS", "10%");*/
$xHG->col("pagos", "TR.PAGOS", "5%");

$xHG->ColMoneda("monto_autorizado", "TR.MONTO ORIGINAL", "10%");
$xHG->ColMoneda("saldo_actual", "TR.SALDO CAPITAL", "10%");

$xHG->ColMoneda("parcialidad", "TR.PARCIALIDAD", "10%");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");

$xHG->OButton("TR.PANEL", "var xC=new CredGen(); xC.goToPanelControl('+ data.record.solicitud +')", "web.png");

//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.solicitud +')", "edit.png");

//$xHG->OButton("TR.BAJA", "jsDeact('+ data.record. +')", "undone.png");
$xFRM->addBuscar("jsBuscar()");
$xFRM->OBuscar("", "", "", "jsBuscar");


$xFRM->addHElem("<div id='iddivlistacreds'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmcreditos/lista-creditos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivlistacreds});
}
function jsAdd(){
    //xG.w({url:"../frmcreditos/lista-creditos.new.frm.php?", tiny:true, callback: jsLGiddivlistacreds});
}
function jsBuscar(){
	var txtb	= $("#idbuscar").val();
    var txt 	= " AND (`personas`.`nombre` LIKE '%" + txtb + "%' OR `personas`.`alias_dependencia` LIKE '%" + txtb + "%' OR `personas`.`sucursal` LIKE '%" + txtb + "%' OR `creditos`.`solicitud` LIKE '%" + txtb + "%' OR `creditos`.`modalidad` LIKE '%" + txtb + "%' OR `creditos`.`convenio` LIKE '%" + txtb + "%' OR `creditos`.`periocidad` LIKE '%" + txtb + "%')";
    
    var txt		= "&w=" + base64.encode(txt);
    $("#iddivlistacreds").jtable("destroy");
    jsLGiddivlistacreds(txt);
}

</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
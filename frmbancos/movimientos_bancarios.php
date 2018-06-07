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
$xHP		= new cHPage("TR.OPERACIONES BANCARIAS", HP_FORM);
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


$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xTxt		= new cHText();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();




$xSelBanco			= $xSel->getListaDeCuentasBancarias();
$xSelBanco->addTodas(true);
$xSelBanco->addEvent("onchange", "jsBuscar(event)");

$xFRM->addHElem($xSelBanco->get(true));

$xSelOB				= $xSel->getListaDeTiposDeOperacionesBancarias();
$xSelOB->addTodas(true);
$xSelOB->addEvent("onchange", "jsBuscar(event)");

$xFRM->addHElem($xSelOB->get(true));

$xFRM->OText("idbuscar", "", "TR.BENEFICIARIO");

$xFRM->addBuscar("jsBuscar()");

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivopsbancs",$xHP->getTitle());

//$xHG->setSQL("SELECT * FROM `bancos_operaciones` LIMIT 0,100");
$xHG->setSQL($xLi->getListadoDeOperacionesBancarias());

//$xSel->getListaDeEstadoMvtosDeCuentasContables();

$xHG->addList();
$xHG->addKey("clave");
$xHG->setOrdenar();


//$xHG->col("", "", "10%");
$xHG->ColFecha("fecha", "TR.FECHA", "10%");

//$xHG->col("clave", "TR.CLAVE", "10%");
$xHG->col("cuenta", "TR.CUENTA", "15%");
$xHG->col("recibo", "TR.RECIBO", "8%");
$xHG->col("persona", "TR.PERSONA", "8%");
$xHG->col("contrato", "TR.CONTRATO", "8%");
$xHG->col("operacion", "TR.TIPO", "8%");


$xHG->col("beneficiarios", "TR.BENEFICIARIO", "30%");
$xHG->ColMoneda("monto", "TR.MONTO","10%");





/*

$xHG->col("numero_de_socio", "TR.PERSONA", "10%");
$xHG->col("documento_de_origen", "TR.CONTRATO", "10%");

$xHG->col("tipo_operacion", "TR.TIPO OPERACION", "10%");
$xHG->col("numero_de_documento", "TR.NUMERO DE DOCUMENTO", "10%");

$xHG->col("beneficiario", "TR.BENEFICIARIO", "10%");
$xHG->col("monto_descontado", "TR.MONTO DESCONTADO", "10%");
$xHG->col("monto_real", "TR.MONTO REAL", "10%");
$xHG->col("estatus", "TR.ESTATUS", "10%");

$xHG->col("idusuario", "TR.IDUSUARIO", "10%");
$xHG->col("usuario_autorizo", "TR.USUARIO AUTORIZO", "10%");



$xHG->col("sucursal", "TR.SUCURSAL", "10%");*/


//$xHG->col("clave_de_conciliacion", "TR.CLAVE DE CONCILIACION", "10%");
//$xHG->col("clave_de_moneda", "TR.CLAVE DE MONEDA", "10%");

//$xHG->col("tipo_de_exhibicion", "TR.TIPO DE EXHIBICION", "10%");
//$xHG->col("cuenta_de_origen", "TR.CUENTA DE ORIGEN", "10%");


$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");

$xFRM->addHElem("<div id='iddivopsbancs'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


echo $xFRM->get();


?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmbancos/banco-operaciones.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivopsbancs});
}
function jsAdd(){
	xG.w({url:"../frmbancos/banco-operaciones.new.frm.php?", tiny:true, callback: jsLGiddivopsbancs});
}
function jsDel(id){
	xG.rmRecord({tabla:"bancos_operaciones", id:id, callback:jsLGiddivopsbancs});
}
function jsBuscar(){
	var idcodigodecuenta 		= entero($("#idcodigodecuenta").val());
	var idtipooperacionbanco 	= $("#idtipooperacionbanco").val();
	var idbuscar 				= $("#idbuscar").val();

	var str						= "";

	if(idcodigodecuenta > 0){
		str						+= " AND (`bancos_operaciones`.`cuenta_bancaria`=" + idcodigodecuenta  +  ") ";
	}
	if(idtipooperacionbanco != "todas" && idtipooperacionbanco != ""){
		str						+= " AND (`tipo_operacion`='" + idtipooperacionbanco  +  "') ";
	}
	if(idbuscar != ""){
		str						+= " AND (`beneficiario` LIKE '%" + idbuscar  +  "%') ";
	}
	if(str != ""){
		$("#iddivopsbancs").jtable("destroy");
		str = "&w=" + base64.encode(str);
		jsLGiddivopsbancs(str);
	} else {
		$("#iddivopsbancs").jtable("destroy");
		jsLGiddivopsbancs();
	}
	
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();


?>
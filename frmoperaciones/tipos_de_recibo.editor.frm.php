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
$xHP		= new cHPage("TR.Tipo de Recibo", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xSel		= new cHSelect();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); 
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

/* ===========		FORMULARIO		============*/
$clave		= parametro("idoperaciones_recibostipo", null, MQL_INT);
$xTabla		= new cOperaciones_recibostipo();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave		= parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->idoperaciones_recibostipo($clave);
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("tiposderecibo", "tipos_de_recibo.editor.frm.php?action=$step");

if($step == MQL_MOD){ $xFRM->addGuardar(); } else { $xFRM->addSubmit(); }
$clave 		= parametro($xTabla->getKey(), null, MQL_INT);

if( ($action == MQL_ADD OR $action == MQL_MOD) AND ($clave != null) ){
	$xTabla->setData( $xTabla->query()->initByID($clave));
	$xTabla->setData($_REQUEST);

	if($action == MQL_ADD){
		$xTabla->query()->insert()->save();
	} else {
		$xTabla->query()->update()->save($clave);
	}
	$xFRM->addAvisoRegistroOK();
}


$xFRM->setTitle($xHP->getTitle());

$xFRM->OHidden("idoperaciones_recibostipo", $xTabla->idoperaciones_recibostipo()->v(), "TR.Clave");

$xFRM->OText("descripcion_recibostipo", $xTabla->descripcion_recibostipo()->v(), "TR.descripcion");
$xFRM->OText("detalles_del_concepto", $xTabla->detalles_del_concepto()->v(), "TR.Notas");
$xFRM->OMoneda("subclasificacion", $xTabla->subclasificacion()->v(), "TR.clasificacion");
$xFRM->OText("nombre_sublasificacion", $xTabla->nombre_sublasificacion()->v(), "TR.nombre clasificacion");
$xFRM->OSelect("mostrar_en_corte", $xTabla->mostrar_en_corte()->v() , "TR.ver en corte", array("1"=>"SI", "0"=>"NO"));
$xFRM->addHElem( $xSel->getListaDeTiposDePolizas("tipo_poliza_generada", true, $xTabla->tipo_poliza_generada()->v())->get(true) );
//$xFRM->OMoneda("tipo_poliza_generada", $xTabla->tipo_poliza_generada()->v(), "TR.Tipo de Poliza");

$xFRM->OSelect("afectacion_en_flujo_efvo", $xTabla->afectacion_en_flujo_efvo()->v() , "TR.Efecto en Efectivo", 
		array("aumento"=>"AUMENTO", "disminucion"=>"DISMINUCION", "ninguna"=>"NINGUNA")
		);
$xFRM->OText("path_formato", $xTabla->path_formato()->v(), "TR.URL de Formato");
$xFRM->OSelect("origen", $xTabla->origen()->v() , "TR.origen", array("colocacion"=>"COLOCACION", "captacion"=>"CAPTACION", "otros"=>"OTROS", "mixto"=>"MIXTO"));

$sql		= "SELECT
	`contable_polizas_perfil`.`idcontable_poliza_perfil` AS `codigo`,
	`operaciones_tipos`.`descripcion_operacion`          AS `tipo`,
	`contable_polizas_perfil`.`descripcion`              AS `descripcion`,
	IF( (`contable_polizas_perfil`.`operacion` = "  . TM_CARGO . "), 'X', '') AS `cargos`,
	IF( (`contable_polizas_perfil`.`operacion` = "  . TM_ABONO . "), 'X', '') AS `abonos`
FROM
	`operaciones_tipos` `operaciones_tipos` 
		RIGHT OUTER JOIN `contable_polizas_perfil` `contable_polizas_perfil` 
		ON `operaciones_tipos`.`idoperaciones_tipos` = `contable_polizas_perfil`.`tipo_de_operacion` 
WHERE
	(`contable_polizas_perfil`.`tipo_de_recibo` =" . $xTabla->idoperaciones_recibostipo()->v() . ")
ORDER BY `contable_polizas_perfil`.`operacion` DESC
			";
$xT			= new cTabla($sql);
$xFRM->addHElem($xT->Show());

//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();
$xFRM->OButton("TR.AGREGAR PERFIL CONTABLE", "jsAgregarPerfilContable()", $xFRM->ic()->AGREGAR);
echo $xFRM->get();
?>
<script>
function jsAgregarPerfilContable(){
	var clave = $("#idoperaciones_recibostipo").val();
	var xGen	= new Gen(); xGen.w({ url: "../frmoperaciones/recibos-tipo.perfil-contable.frm.php?tipo=" + clave, h:600, w : 800, tiny : true});
}
</script>
<?php 
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
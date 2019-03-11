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
$xHP		= new cHPage("TR.TIPO_DE OPERACION", HP_FORM);
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

$xHP->init();



$tipo		= parametro("tipo", SYS_TODAS);



/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cOperaciones_tipos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xTabla->tipo_operacion()->v($xTabla->idoperaciones_tipos()->v());


$xFRM	= new cHForm("frmoperacionesmvtosedit", "");
$xFRM->setNoAcordion();
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xFRM->OHidden("idoperaciones_tipos", $xTabla->idoperaciones_tipos()->v());
$xFRM->OHidden("tipo_operacion", $xTabla->tipo_operacion()->v());


//$aGridSQL["GENERAL"]		= "tipo_operacion,descripcion_operacion,nombre_corto,descripcion,estatus";

$xOps		= new cTipoDeOperacion($clave);
$xOps->init();
$xFRM->addSeccion("id", $xOps->getNombre() . " - $tipo");

switch ($tipo){
	case "general":
		$xFRM->OText("descripcion_operacion", $xTabla->descripcion_operacion()->v(), "TR.NOMBRE");
		$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.DESCRIPCION");
		$xFRM->OText("nombre_corto", $xTabla->nombre_corto()->v(), "TR.ALIAS");
		$xFRM->OSiNo("TR.ESTATUSACTIVO", "estatus", $xTabla->estatus()->v());
		$xFRM->OMoneda("precio", $xTabla->precio()->v(), "TR.PRECIO");
		$xFRM->OMoneda("tasa_iva", $xTabla->tasa_iva()->v(), "TR.TASA IVA");
		
		break;
	case "clase":
		
		$xSelCE		= new cHSelect();
		$xSelCE->addOptions(array( "0" => "Ninguno", "1" => "Aumenta", "-1" => "Disminucion" ));

		$xSelAE		= new cHSelect();
		$xSelAE->addOptions(array( "0" => "Ninguno", "1" => "Aumenta", "-1" => "Disminucion" ));
		
		$xFRM->OMoneda("clasificacion", $xTabla->clasificacion()->v(), "TR.CLASIFICACION");
		$xFRM->OMoneda("subclasificacion", $xTabla->subclasificacion()->v(), "TR.SUBCLASIFICACION");
		
		
		//$xFRM->OMoneda("visible_reporte", $xTabla->visible_reporte()->v(), "TR.VISIBLE REPORTE");
		$xFRM->addHElem($xSelCE->get("class_efectivo","TR.CLASE EFECTIVO", $xTabla->class_efectivo()->v() ));
		//$xFRM->OMoneda("class_efectivo", $xTabla->class_efectivo()->v(), "TR.CLASS EFECTIVO");
		//$xFRM->OMoneda("afectacion_en_sdpm", $xTabla->afectacion_en_sdpm()->v(), "TR.AFECTACION EN SDPM");
		$xFRM->addHElem($xSelAE->get("afectacion_en_sdpm","TR.AFECTACION EN SDPM", $xTabla->afectacion_en_sdpm()->v()));
		
		$xFRM->OSelect("periocidad_afectada", $xTabla->periocidad_afectada()->v() , "TR.PERIOCIDAD AFECTADA", array("ninguna"=>"NINGUNA", "todas"=>"TODAS", "vencimiento"=>"VENCIMIENTO", "periodico"=>"PERIODICO"));
		
		$xFRM->OMoneda("mvto_que_afecta", $xTabla->mvto_que_afecta()->v(), "TR.MVTO QUE AFECTA");
		
		
		
		$xFRM->OMoneda("producto_aplicable", $xTabla->producto_aplicable()->v(), "TR.PRODUCTO APLICABLE");
		
		$xFRM->OSiNo("TR.CONSTITUYE FONDO AUTOMATICO","constituye_fondo_automatico", $xTabla->constituye_fondo_automatico()->v());
		$xFRM->OSiNo("TR.INTEGRA VENCIDO","integra_vencido", $xTabla->integra_vencido()->v() );
		//$xFRM->OSelect("constituye_fondo_automatico", $xTabla->constituye_fondo_automatico()->v() , "TR.CONSTITUYE FONDO AUTOMATICO", array("1"=>"1", "0"=>"0"));
		//$xFRM->OSelect("integra_vencido", $xTabla->integra_vencido()->v() , "TR.INTEGRA VENCIDO", array("1"=>"1", "0"=>"0"));
		$xFRM->OSiNo("TR.VISIBLE REPORTE","visible_reporte", $xTabla->visible_reporte()->v());
		$xFRM->OSiNo("TR.INTEGRA PARCIALIDAD","integra_parcialidad", $xTabla->integra_parcialidad()->v());
		$xFRM->OSiNo("TR.ES ESTADISTICO","es_estadistico", $xTabla->es_estadistico()->v());
		//$xFRM->OSelect("integra_parcialidad", $xTabla->integra_parcialidad()->v() , "TR.INTEGRA PARCIALIDAD", array("1"=>"1", "0"=>"0"));
		//$xFRM->OSelect("es_estadistico", $xTabla->es_estadistico()->v() , "TR.ES ESTADISTICO", array("1"=>"1", "0"=>"0"));
		//$xFRM->OMoneda("cargo_directo", $xTabla->cargo_directo()->v(), "TR.CARGO DIRECTO");
		$xFRM->OSiNo("TR.CARGO DIRECTO", "cargo_directo", $xTabla->cargo_directo()->v() );
		//$xFRM->OMoneda("afectacion_en_notificacion", $xTabla->afectacion_en_notificacion()->v(), "TR.AFECTACION EN NOTIFICACION");
		$xFRM->OSiNo("TR.AFECTACION EN NOTIFICACION", "afectacion_en_notificacion", $xTabla->afectacion_en_notificacion()->v());
		
		
		
		
	
		break;
	case "claserecibos":
		$xSelAR		= new cHSelect();
		$xSelAR->addOptions(array( "0" => "Ninguno", "1" => "Aumenta", "-1" => "Disminucion" ));
		
		$xSelTR		= $xSel->getListaDeTiposDeRecibos("recibo_que_afecta", $xTabla->recibo_que_afecta()->v());
		$xFRM->addHElem($xSelTR->get("TR.RECIBO QUE AFECTA", true));
		//$xFRM->OMoneda("recibo_que_afecta", $xTabla->recibo_que_afecta()->v(), "TR.RECIBO QUE AFECTA");
		$xFRM->addHElem($xSelAR->get("afectacion_en_recibo","TR.AFECTACION EN RECIBO", $xTabla->afectacion_en_recibo()->v()));
		
		
		break;
	case "formulas":
		$xFRM->OTextArea("codigo_de_valoracion", $xTabla->codigo_de_valoracion()->v(), "TR.CODIGO DE VALORACION");
		$xFRM->OTextArea("formula_de_calculo", $xTabla->formula_de_calculo()->v(), "TR.FORMULA DE CALCULO");
		
		
		break;
	case "cancelacion":
		$xFRM->OMoneda("importancia_de_neutralizacion", $xTabla->importancia_de_neutralizacion()->v(), "TR.IMPORTANCIA DE NEUTRALIZACION");
		
		//$xFRM->OSelect("preservar_movimiento", $xTabla->preservar_movimiento()->v() , "TR.PRESERVAR MOVIMIENTO", array("0"=>"0", "1"=>"1"));
		$xFRM->OSiNo("TR.PRESERVAR MOVIMIENTO","preservar_movimiento", $xTabla->preservar_movimiento()->v());
		$xFRM->OTextArea("formula_de_cancelacion", $xTabla->formula_de_cancelacion()->v(), "TR.FORMULA DE CANCELACION");
		break;
	case "contable":
		$xFRM->OText("cuenta_contable", $xTabla->cuenta_contable()->v(), "TR.CUENTA CONTABLE");
		
		break;
}
$xFRM->endSeccion();


$xFRM->addCRUDSave($xTabla->get(), $clave, false);
echo $xFRM->get();



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
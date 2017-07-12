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
$xHP		= new cHPage("TR.AGREGAR RIESGOS", HP_FORM);
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

$xHP->init();

$xFRM		= new cHForm("frmaml_risk_catalog", "catalogo.riesgos.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xTabla		= new cAml_risk_catalog();
$xTabla->setData( $xTabla->query()->initByID($clave));




$xSel1	= $xSel->getListaDeTipoDeRiesgoEnAML("tipo_de_riesgo");
$xSel1->setOptionSelect($xTabla->tipo_de_riesgo()->v());
$xSel2	= $xSel->getListaDeUnidadMedidaAML("unidad_de_medida");
$xSel2->setOptionSelect($xTabla->unidad_de_medida()->v(OUT_TXT));
$xSel3	= $xSel->getListaDeFormaReportaRiesgo("forma_de_reportar");
$xSel3->setOptionSelect($xTabla->forma_de_reportar()->v(OUT_TXT));
$xSel4	= $xSel->getListaDeFrecuenciaChequeoRiesgo("frecuencia_de_chequeo");
$xSel4->setOptionSelect($xTabla->frecuencia_de_chequeo()->v(OUT_TXT));

$xFRM->OMoneda("clave_de_control", $xTabla->clave_de_control()->v(), "TR.clave de control");
$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.descripcion");
$xFRM->addHElem( $xSel1->get(true) );
$xFRM->OMoneda("valor_ponderado", $xTabla->valor_ponderado()->v(), "TR.valor ponderado");
$xFRM->OMoneda("unidades_ponderadas", $xTabla->unidades_ponderadas()->v(), "TR.unidades ponderadas");
$xFRM->addHElem( $xSel2->get(true) );
$xFRM->addHElem( $xSel3->get(true) );
$xFRM->addHElem( $xSel4->get(true) );
$xFRM->OTextArea("fundamento_legal", $xTabla->fundamento_legal()->v(), "TR.fundamento legal");

$xFRM->addCRUD($xTabla->get(), true);

echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
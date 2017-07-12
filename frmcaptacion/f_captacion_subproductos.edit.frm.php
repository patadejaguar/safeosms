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
$xHP		= new cHPage("TR.PRODUCTOS DE CAPTACION", HP_FORM);
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

$xTabla		= new cCaptacion_subproductos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmcaptacionproducto", "f_captacion_subproductos.frm.php?action=$action");
$xSel		= new cHSelect();
$xTxt		= new cHText();


$xFRM->setTitle($xHP->getTitle() . " " . $xTabla->descripcion_subproductos()->v());

$xFRM->setNoAcordion();
$xFRM->addSeccion("idsgeneral", "TR.Datos Generales");
$xFRM->OHidden("idcaptacion_subproductos", $xTabla->idcaptacion_subproductos()->v());

$xFRM->OText("descripcion_subproductos", $xTabla->descripcion_subproductos()->v(), "TR.Nombre");
$xFRM->OText("descripcion_completa", $xTabla->descripcion_completa()->v(), "TR.descripcion");
$xFRM->addHElem( $xSel->getListaDeTiposDeCuentasCaptacion("tipo_de_cuenta", $xTabla->tipo_de_cuenta()->v())->get(true) );
//$xFRM->OMoneda("tipo_de_cuenta", $xTabla->tipo_de_cuenta()->v(), "TR.tipo de cuenta");
$xFRM->OText("nombre_del_contrato", $xTabla->nombre_del_contrato()->v(), "TR.nombre del contrato");
$xFRM->OSelect("metodo_de_abono_de_interes", $xTabla->metodo_de_abono_de_interes()->v() , "TR.tipo_de DEPOSITO_CAPTACION de interes", array("AL_FIN_DE_MES"=>"AL FIN DE MES", "AL_VENCIMIENTO"=>"AL VENCIMIENTO"));
$xFRM->OSelect("destino_del_interes", $xTabla->destino_del_interes()->v() , "TR.destino del interes", array("CUENTA"=>"CUENTA", "NUEVA"=>"NUEVA", "CUENTA_INTERESES"=>"CUENTA INTERESES"));

$xFRM->endSeccion();
$xFRM->addSeccion("idscontabilidad", "TR.Contabilidad");

//$xFRM->addHElem($xTxt->getDeCuentaContable("capital_vigente_renovado", $xTabla->capital_vigente_renovado()->v(), false, CONTABLE_MAYOR_CARTERA_REN, "TR.CARTERA RENOVADO NORMAL"));

$xFRM->addHElem($xTxt->getDeCuentaContable("contable_movimientos", $xTabla->contable_movimientos()->v(), false, CONTABLE_CLAVE_PASIVO, "TR.CUENTA_CONTABLE .- operaciones"));
$xFRM->addHElem($xTxt->getDeCuentaContable("contable_intereses_por_pagar", $xTabla->contable_intereses_por_pagar()->v(), false, CONTABLE_CLAVE_PASIVO,  "TR.CUENTA_CONTABLE .- intereses por pagar"));
$xFRM->addHElem($xTxt->getDeCuentaContable("contable_gastos_por_intereses", $xTabla->contable_gastos_por_intereses()->v(), false, CONTABLE_CLAVE_PASIVO, "TR.CUENTA_CONTABLE .- gastos por intereses"));
$xFRM->addHElem($xTxt->getDeCuentaContable("contable_cuentas_castigadas", $xTabla->contable_cuentas_castigadas()->v(), false, CONTABLE_CLAVE_PASIVO, "TR.CUENTA_CONTABLE .- cuentas castigadas"));

//$xFRM->OText("contable_movimientos", $xTabla->contable_movimientos()->v(), "TR.CUENTA_CONTABLE .- operaciones");
//$xFRM->OText("contable_intereses_por_pagar", $xTabla->contable_intereses_por_pagar()->v(), "TR.CUENTA_CONTABLE .- intereses por pagar");
//$xFRM->OText("contable_gastos_por_intereses", $xTabla->contable_gastos_por_intereses()->v(), "TR.CUENTA_CONTABLE .- gastos por intereses");
//$xFRM->OText("contable_cuentas_castigadas", $xTabla->contable_cuentas_castigadas()->v(), "TR.CUENTA_CONTABLE .- cuentas castigadas");

$xFRM->OSiNo("TR.ESTATUSACTIVO", "estatus", $xTabla->estatus()->v());
$xFRM->endSeccion();
$xFRM->addSeccion("idsalgoritmos", "TR.Formulas");
//$xFRM->OText("fecha_alta", $xTabla->fecha_alta()->v(), "TR.fecha alta");
//$xFRM->OText("fecha_baja", $xTabla->fecha_baja()->v(), "TR.fecha baja");

$xFRM->OTextArea("algoritmo_de_premio", $xTabla->algoritmo_de_premio()->v(), "TR.formula de premio");
$xFRM->OTextArea("algoritmo_de_tasa_incremental", $xTabla->algoritmo_de_tasa_incremental()->v(), "TR.formula de tasa incremental");
$xFRM->OTextArea("algoritmo_modificador_del_interes", $xTabla->algoritmo_modificador_del_interes()->v(), "TR.formula modificador del interes");
	
//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
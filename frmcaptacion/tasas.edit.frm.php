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
$xHP		= new cHPage("TR.EDITAR TASAS DE CAPTACION", HP_FORM);
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


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCaptacion_tasas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmtasa", "tasas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$xFRM->OHidden("idcaptacion_tasas", $xTabla->idcaptacion_tasas()->v());

$xFRM->addHElem($xSel->getListaDeTipoDeCaptacion("modalidad_cuenta", $xTabla->modalidad_cuenta()->v())->get(true));
$xSelTC	= $xSel->getListaDeProductosDeCaptacion("subproducto", $xTabla->subproducto()->v());
$xSelTC->addEspOption("0", "NINGUNO");
$xFRM->addHElem($xSelTC->get(true));

$xFRM->OMoneda2("monto_mayor_a", $xTabla->monto_mayor_a()->v(), "TR.LIMITEINFERIOR");
//$xFRM->OMoneda("monto_menor_a", $xTabla->monto_menor_a()->v(), "TR.LIMITESUPERIOR");
$xFRM->OMoneda2("monto_menor_a", $xTabla->monto_menor_a()->v(), "TR.LIMITESUPERIOR");
$xFRM->ONumero("dias_mayor_a", $xTabla->dias_mayor_a()->v(), "TR.DIAS MAYOR A");
$xFRM->ONumero("dias_menor_a", $xTabla->dias_menor_a()->v(), "TR.DIAS MENOR A");

//$xFRM->OMoneda("tasa_efectiva", $xTabla->tasa_efectiva()->v(), "TR.TASA");
$xFRM->OTasa("tasa_efectiva", $xTabla->tasa_efectiva()->v(), "TR.TASA");

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();




//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
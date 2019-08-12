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
$xHP		= new cHPage("TR.EDITAR ALERTAS", HP_FORM);
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

$xHP->addWizardSuport();

$xHP->init();

?>
<style>
#microformato {
min-height:250px;
}
</style>
<?php

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cSistema_programacion_de_avisos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmalertas", "sistema_programacion_de_avisos.frm.php?action=$action");
$xFRM->setIsWizard();

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

$xFRM->addSeccion("idfr1", "TR.DATOS GENERALES");
$xFRM->OHidden("idprograma", $xTabla->idprograma()->v());

//$xFRM->OText("forma_de_creacion", $xTabla->forma_de_creacion()->v(), "TR.FORMA DE CREACION");
$xFRM->ODisabled("forma_de_creacion", $xTabla->forma_de_creacion()->v(), "TR.FORMA DE CREACION");
//$xFRM->OText("programacion", $xTabla->programacion()->v(), "TR.PROGRAMACION");
$xFRM->ODisabled("programacion", $xTabla->programacion()->v(), "TR.PROGRAMACION");
$xFRM->OText("nombre_del_aviso", $xTabla->nombre_del_aviso()->v(), "TR.NOMBRE DEL AVISO");
$xFRM->OText("intent_command", $xTabla->intent_command()->v(), "TR.CONSULTA");

$xFRM->endSeccion();

$xFRM->addSeccion("idfr2", "TR.DESTINATARIOS");
//CORREO: OFICIALES: EMPRESAS: 

$xFRM->OTextArea("destinatarios", $xTabla->destinatarios()->v(), "TR.DESTINATARIOS");


$xFRM->endSeccion();

$xFRM->addSeccion("idfr3", "TR.MICROFORMATO");
$xFRM->OTextArea("microformato", $xTabla->microformato()->v(), "TR.MICROFORMATO");
$xFRM->endSeccion();


/*$xFRM->addSeccion("idfr4", "TR.CONSULTA");
$xFRM->OText("intent_command", $xTabla->intent_command()->v(), "TR.CONSULTA");
$xFRM->endSeccion();*/

//$xFRM->OText("tipo_de_medios", $xTabla->tipo_de_medios()->v(), "TR.TIPO DE MEDIOS");
//$xFRM->OTextArea("intent_check", $xTabla->intent_check()->v(), "TR.INTENT CHECK");
//$xFRM->OTextArea("intent_command", $xTabla->intent_command()->v(), "TR.INTENT COMMAND");

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);



echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
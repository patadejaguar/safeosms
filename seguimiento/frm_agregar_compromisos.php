<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xP		= new cHPage("TR.Calendario de Compromisos", HP_FORM);
$xQL	= new cSQLListas();

$xP->init();

$oficial = elusuario($iduser);
/**
Valores Pasados por GET numero_de_socio|numero_de_credito|fecha
*/
$xParams		= parametro("p");
$defValues		= "0|0|" . fechasys();
if($xParams == ""){	$defValues	= $xParams; }
//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

//$jxc ->drawJavaScript(false, true);

/**
 * Obtiene Parametros a traves de un explode
 */
$DVals	= explode("|", $defValues);


$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xP->init();

$xFRM		= new cHForm("frmCompromisos", "frm_agregar_compromisos.php?action=" . MQL_ADD );

$msg		= "";

$xFRM->addJsBasico();
$xFRM->addCreditBasico();

$xFRM->addSubmit();

$arr	= array(
		
		"promesa_de_pago" => "Promesa de Pago",
		"promesa_de_revision" => "Promesa de Revision(Cita)",
		"promesa_de_reestructuracion" => "Promesa de Reestructuracion",
		"promesa_de_renovacion" => "Promesa de Renovacion"
);
$xHSel	= new cHSelect();
$xHSel->addOptions($arr);

$xFRM->addHElem( $xHSel->get("idtipocompromiso", "TR.Tipo de Compromiso", "promesa_de_pago") );
$xHF	= new cHDate();

$cH 	= new cFecha();
$xFRM->addHElem( $xHF->get("TR.Fecha") );
$xFRM->addHElem( $cH->getHours(true, "TR.Horario", "idhora") );


$xFRM->OTextArea("idnotas", "", "TR.Notas");






if ($action == MQL_ADD){
	//Insertar Nuevo Registro
	$socio			= $persona;
	$solicitud		= $credito;
	$oficial		= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
	$fecha			= $cH->getFechaISO($_POST["idfecha-0"] );
	$hora			= $_POST["idhora"];
	$compromiso		= $_POST["idtipocompromiso"];
	$anotacion		= $_POST["idnotas"];

	//Valores Pre-establecidos
	$eacp			= EACP_CLAVE;
	$sucursal		= getSucursal();
	$estatus		= "pendiente";
	//$xSeg			= new cSe
	$sqlIC = "INSERT INTO seguimiento_compromisos(socio_comprometido, oficial_de_seguimiento, fecha_vencimiento, hora_vencimiento, tipo_compromiso, anotacion, credito_comprometido, estatus_compromiso, sucursal, eacp)
    			VALUES($socio, $iduser, '$fecha', '$hora', '$compromiso', '$anotacion', $solicitud, '$estatus', '$sucursal', '$eacp')";
	$ms		= my_query($sqlIC);
	if($ms["stat"]!=false){
		$xFRM->addAviso("Se Agrego un compromiso para el socio num $socio por el Credito $solicitud el dia $fecha");
	}
}
//Imprimir la Tabla de compromisos para hoy
	$sql			= $xQL->getListadoDeCompromisosSimple("", "", getUsuarioActual());
//echo $sqlTComp;

	$cTbl = new cTabla($sql);
	$cTbl->setWidth();
	$cTbl->setKeyField("idseguimiento_compromisos");
	$cTbl->addTool(1);
	$cTbl->addTool(2);
	
	$xFRM->addHTML( $cTbl->Show() );
	
	
	echo $xFRM->get();
	
	echo $cTbl->getJSActions(true);
	
	echo $xP->fin();
?>
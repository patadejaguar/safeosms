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
$xHP		= new cHPage("", HP_FORM);

$xHP->setTitle( $xHP->lang("REPORTAR", "CONDUCTA_INADECUADA") );

$DDATA		= $_REQUEST;
$action		= (isset($DDATA["action"])) ? $DDATA["action"] : SYS_NINGUNO;
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$xHP->init();


//$jxc ->drawJavaScript(false, true);

$xFRM	= new cHForm("frmreporteempleado", "reportar_empleado.frm.php?action=" . SYS_UNO);
$xBtn	= new cHButton();		
$xTxt	= new cHText();
$xSel	= new cHSelect();
$xFec	= new cHDate();
$xTa	= new cHTextArea();

if($action == SYS_NINGUNO){
	$xUsers 			= $xSel->getUsuarios("idusuarioreportado");
	$xMot				= $xSel->getListaDeRiesgosAML("", AML_CLAVE_OPERACIONES_INTERNAS);
	
	$xFRM->setTitle($xHP->getTitle());
	$xFRM->ODate("fecha_de_evento", false, "TR.Fecha de Suceso");
	$xFRM->addHElem($xUsers->get($xHP->lang("usuario", "reportado"), true ) );
	$xFRM->addHElem($xMot->get($xHP->lang("motivos"), true ) );
	
	$xFRM->addPersonaBasico("", false, false, "", "TR.Persona Relacionada");
	$xFRM->addHElem($xTxt->get("iddocumento", DEFAULT_CREDITO, $xHP->lang("documento", "relacionado") ) );
	$xFRM->addHElem( $xSel->getListaDeObjetosEnSistema()->get(true) );

	$xFRM->addHElem($xTa->get("idmensaje", "",  $xHP->lang("mensaje")) );
	//$xFRM->addCreditBasico();
	$xFRM->addSubmit();
	
	
} else {
	//LOS REPORTES DIRECTOS SI SE NOTIFICAN AL OFICIAL
	$arrValores	= array (
	 		"montoabonado" => MQL_FLOAT,
			"idusuarioreportado" => MQL_INT,
			"idmensaje" => MQL_STRING,
			"fecha_de_evento" => MQL_STRING,
			"idtipoderiesgo" => MQL_INT,
			"iddocumento" => MQL_INT,
			"idsocio" => MQL_INT
	);
	$xF				= new cFecha();
	$VR				= getVariablesSanas($_POST, $arrValores);

	$fecha				= $xF->getFechaISO($VR["fecha_de_evento"]);
	//$fecha			= $xF->getInt($fecha);
	$usuarioreportado	= $VR["idusuarioreportado"];
	$motivo				= $VR["idtipoderiesgo"];
	$mensaje			= $VR["idmensaje"];
	$documento			= $VR["iddocumento"];
	$tercero			= $VR["idsocio"];
	$tipo_de_documento	= parametro("idobjetodesistema", iDE_RECIBO, MQL_INT);
	$xAml				= new cAML();
	$xAml->setReportarUsuario($usuarioreportado, $motivo, $mensaje, $documento, $fecha, $tercero, $tipo_de_documento);
	
	$xFRM->addAviso($xAml->getMessages());
	$xFRM->addToolbar($xBtn->getIrAlInicio(true));
}
echo $xFRM->get();


?>
<!-- HTML content -->
<script>
</script>
<?php
$xHP->fin();
?>
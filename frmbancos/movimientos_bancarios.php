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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$oficial 		= elusuario($iduser);
$xHP			= new cHPage("TR.OPERACIONES BANCARIAS", HP_FORM);
$operacion		= parametro("operacion", BANCOS_OPERACION_DEPOSITO, MQL_RAW);
//require_once(TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");	
//$jxc ->process();

echo $xHP->getHeader();
echo $xHP->setBodyinit("initComponents()");
$fecha				= fechasys();

//$xFrm->addHElem()

$xTxt				= new cHText();
$xBtn				= new cHButton();
$xSel				= new cHSelect();

$xFRM				= new cHForm("bancos_operaciones", "movimientos_bancarios.frm.php");
//id,	label value, size,	class,	options[])
$xFRM->setTitle($xHP->getTitle());


$selBanco			= $xSel->getListaDeCuentasBancarias("");
$selBanco->addEspOption(SYS_TODAS);
$selBanco->setOptionSelect(SYS_TODAS);

//$xSel->setOptionSelect($numero_de_cuenta);
$xHSel				= new cHSelect();
$xHSel->addOptions(array("cheque" => $xHP->lang("Cheque"), "deposito" => $xHP->lang("Deposito"), "retiro" => $xHP->lang("Retiro")) );
$selOperacion		= $xHSel->get("idoperacion", $xHP->lang("operacion"), $operacion);
$xHSel->setClearOptions();
$xHSel->addOptions(array("autorizado" => $xHP->lang("Autorizado"), "noautorizado" => $xHP->lang("No Autorizado"), "cancelado" => $xHP->lang("Cancelado")) );
$selEstatus			= $xHSel->get("idestatus", $xHP->lang( "Estatus"));
$xF					= new cHDate(0, $fecha, TIPO_FECHA_OPERATIVA);

$xF2				= new cHDate(1, $fecha, TIPO_FECHA_OPERATIVA);

$xFRM->addHElem($xF->get($xHP->lang("Fecha Inicial")));
$xFRM->addHElem($xF2->get($xHP->lang("Fecha Final")));

$xFRM->addHElem($selBanco->get("TR.Cuenta Bancaria", true));

$xFRM->addHElem($selOperacion);
$xFRM->addHElem($selEstatus);

$xFRM->addHElem($xTxt->getNormal("idbeneficiario", "", "TR.Beneficiario") );

$xFRM->addToolbar($xBtn->getBasic($xHP->lang("Obtener"), "jsGetReporte", "guardar", "cmdsave", false));
//$xFRM->addSubmit("Guardar Movimiento", "setGuardar");
$xFRM->addFootElement("<div id='content'><object type=\"text/html\" id=\"idFPrincipal\" data=\"./utils/frm_calendar_tasks.php\" width='100%' height=\"1200px\" ></object></div>");

echo $xFRM->get();


?>

</body>
<script >
function jsGetReporte() {
	
	var fi 		= $("#idfecha-0").val();
	var ff 		= $("#idfecha-1").val();
	//var usr	= $("#idusuario").val();
	var rpt		= "movimientos_bancarios.grid.php?";
	//var sta	= $("#idestado").val();
	//var emp	= $("#idempresa").val();
	var cta		= $("#idcodigodecuenta").val();
	var tope	= $("#idoperacion").val();
	//var usr	= $("#idusuario").val();
	var busq	= $("#idbeneficiario").val();
	
	var out		= "";
	
	var isURL	= rpt + "mx=true&on=" + fi + "&off=" + ff + "&out=" + out + "&cuenta=" + cta + "&operacion=" + tope + "&busqueda=" + busq;
	var iFr		= document.getElementById("idFPrincipal");
	console.log(isURL);
	//$('#idFPrincipal').attr('data', sURI);
	$('#content').html("<object type='text/html' id='idFPrincipal' data='" + isURL +  "' width='100%' height='800px' ></object>");
			
}
function resizeMainWindow(){
	var mWidth	= 400;
	var mHeight	= 600;
	//window.resizeTo(mWidth, mHeight);	
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}
</script>
</html>
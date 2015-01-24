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

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xHNot		= new cHNotif();
$msg		= "";
$xRPT		= new cPanelDeReportes(iDE_AML, "aml");

//$xRPT->setConFechas();

//$xRPT->addFechaInicial();
//$xRPT->OFRM()->addToolbar("<div class='noticon'> <i class='fa fa-warning fa-lg'></i> <span id='numeroerrores' class='noticount'>0</span></div><div class='noticon'> <i class='fa fa-newspaper-o fa-lg'></i><span id='numeroregistros' class='noticount'>0</span></div>");
//$xRPT->OFRM()->addToolbar($xHNot->getNoticon("numeroerrores", $xHNot->NOTICE) . $xHNot->getNoticon("numeroregistros", $xHNot->ic()->REGISTROS));
//$xRPT->OFRM()->addDivSolo($xHNot->getNoticon("numeroerrores", $xHNot->NOTICE) . "<label>ERRORES</label>", $xHNot->getNoticon("numeroerrores", $xHNot->NOTICE), "tx24", "txt24");

//$xRPT->OFRM()->addHElem( $xRPT->addFechaFinal("TR.Fecha de Corte") );

//$xRPT->addCheckBox("TR.Definitivo", "definitivo");

$xhtm		= "<h2>Errores</h2><p id='mensajesdelreporte' class='warn'></p>";
//$xRPT->addFooterBar($xhtm);// $xHNot->get($xhtm, "idnoticias", $xHNot->WARNING) );
echo $xRPT->get();
echo $xRPT->getJs();
//$jxc ->drawJavaScript(false, true);
?>
<script>
function jsBlurListaDeReportes(){
	jsDiagnosticoReporte();
}
function jsDiagnosticoReporte(){
	var fechaFinal	= $('#idfecha-1').val();
	var idreporte	= $('#idreporte').val();
	var idsucursal	= $('#idsucursal').val();
	var idtiposalida	= $('#idtipodesalida').val();
	var g 		= new Gen();
	var murl 	= idreporte + "mx=true" + "" + "&off=" + fechaFinal  + "&fechafinal=" + fechaFinal  + "&sucursal=" + idsucursal  + "&s=" + idsucursal  + "&out=" + idtiposalida + "&pregunta=true";
	
var AjxOpts	= {
	url		: murl,
	contentType	: "json",
	success		: function(rs){
		if (typeof rs.mensajes != "undefined") {

			 $.amaran({
				 content:{
					 message : "Numero de registros : " + rs.registros,
					 info : "Numero de registros que contiene el reporte",
					 icon : 'fa fa-info',
					 title : "Registros"
					 },
				 theme:'awesome green'
			 	
			 });			

			 $.amaran({
				 content:{
					 message : "Numero de errores : " + rs.errores,
					 info : rs.mensajes,
					 icon : 'fa fa-warning',
					 title : "Errores" 
					 },
				 theme:'awesome error'
			 	
			 });

		}
		
	}
};

$.ajax(AjxOpts);	  	
}
</script>
<?php
$xHP->fin();
?>
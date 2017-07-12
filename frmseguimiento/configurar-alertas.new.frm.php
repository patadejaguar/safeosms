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
$xHP		= new cHPage("TR.AGREGAR Alertas", HP_FORM);

$jxc = new TinyAjax();
function jsaListadoDeEventos($generadoEn, $programado){
	$xPRG		= new cAlertasDelSistema();
	$cnt		= "";
	$xSel		= new cHSelect();
	$xRuls		= new cReglaDeNegocio();
	if($generadoEn == SYS_ALERTA_POR_EVENTO){
		
		
		$xProg		= new cAlertasDelSistema();
		
		$xSel->addOptions( $xRuls->getEventos() );
		$cnt		= $xSel->get("idprogramacion", "TR.Evento Marcado");
	} else {
		$xTxtA		= new cHTextArea();
				
		$xFld		= new cHFieldset("TR.Programacion");
		if($programado == ""){
			//$xFld->addHElem(  );
		} else {
			//$xFld->addHElem(  );
		}
		$xFld->addHElem($xTxtA->get("idprogramacion", "", "TR.programacion"));
		$cnt		.= $xFld->get();
	}

	return $cnt;
}
$jxc ->exportFunction('jsaListadoDeEventos', array('idgeneradoen', "idtipodeprogramacion"), "#lstevento");
$jxc ->process();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);

$xHP->init();

$xFRM		= new cHForm("frmalertas", "alertas.agregar.frm.php?action=1");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();
$msg		= "";
$txtA		= new cHTextArea();

$xFRM->setTitle($xHP->getTitle());


$titulo			= parametro("idtitulo");
$oficiales		= parametro("idoficiales");
$empresas		= parametro("idempresas");
$personas		= parametro("idpersonas");
$ByTel			= parametro("portelefono", false, MQL_BOOL);
$ByMail			= parametro("pormail", false, MQL_BOOL);
$BySMS			= parametro("porsms", false, MQL_BOOL);

$precomandos	= parametro("idcomando", "", MQL_RAW);
$intentcheck	= parametro("idcomandocheck", "", MQL_RAW);
$contenido		= parametro("idcontenido", "", MQL_RAW);
$programacion	= parametro("idprogramacion");
$tipodeprog		= parametro("idtipodeprogramacion");
$generadoen		= parametro("idgeneradoen");
$mails			= parametro("idmails", "", MQL_RAW);

$medios			= "";
$destinatarios	= "";

$medios			= ($ByMail == true) ? ",MAIL" : "";
$medios			= ($BySMS == true) ? ",SMS" : "";

$programacion	= ($generadoen == SYS_ALERTA_POR_EVENTO) ? $programacion : "$tipodeprog:$programacion";

if($action == SYS_UNO){
	if($titulo == "" OR (trim("$oficiales $empresas $personas $mails")  == "") ){
		//$msg			.= "$titulo |$oficiales| $empresas |$personas |$mails \r\n";
		$msg			.= $xHP->lang(MSG_ERROR_SAVE);
		if(MODO_DEBUG == true){ $xErr	= new cError(); $msg.= $xErr->getREQVars(); }
	} else {
		$xA				= new cAlertasDelSistema();
		
		$destinatarios	.= ($oficiales == "") ? "" : "OFICIALES:$oficiales|";
		$destinatarios	.= ($empresas == "") ? "" : "EMPRESAS:$empresas|";
		$destinatarios	.= ($personas == "") ? "" : "PERSONAS:$personas|";
		$destinatarios	.= ($mails == "") ? "" : "CORREO:$mails|";

		$xA->setAgregarProgramacion($titulo, $contenido, $destinatarios, $programacion, $generadoen, $medios, $precomandos, $intentcheck);
		$msg			.= $xA->getMessages();
		$msg			.= $xHP->lang(MSG_READY_SAVE);
	}
}

$xPRG		= new cAlertasDelSistema();

$xSel2		= new cHSelect();
$xSel2->setDivClass("tx4 tx18 green"); $xSel->setDivClass("tx4 tx18 green");

$xSel2->addOptions( $xPRG->getATipoDeEvento() );
$xSel2->addEvent("jsaListadoDeEventos()", "onblur");
$xFRM->addHElem( $xTxt->getNormal("idtitulo", $titulo, "TR.Nombre del Aviso") );
$xFRM->addHElem( $xSel2->get("idgeneradoen", "TR.Generado") );

$xSel->addOptions( $xPRG->getTipoDeProgramacion() );

$xFRM->addHElem($xSel->get("idtipodeprogramacion", "TR.Programado") );


$xFS2	= new cHFieldset("TR.Destinatarios");
$xFS	= new cHFieldset("TR.Medios de envio");

$xFS->addHElem( $xChk->get("TR.Aviso por Telefono", "portelefono") );
$xFS->addHElem( $xChk->get("TR.Aviso por Email", "pormail") );
$xFS->addHElem( $xChk->get("TR.Aviso por SMS", "porsms") );

$xFRM->addHElem( $xFS->get() );

$xFRM->addHElem("<div id='lstevento'></div>");

$xFRM->addSeccion("iddestinatarios", "TR.Destinatarios");

$xOficiales		= $xSel->getListaDeOficiales();
$xOficiales->addEvent("onchange", "jsAddItem(this, '#idoficiales')");

$xFRM->addDivSolo($xOficiales->get(), $txtA->get("idoficiales", $oficiales), "tx2","tx2"  );

$xEmpresas		= $xSel->getListaDeEmpresas("", true);
$xEmpresas->addEvent("onchange", "jsAddItem(this, '#idempresas')");
$xFRM->addDivSolo( $xEmpresas->get(), $txtA->get("idempresas", $empresas), "tx2","tx2"  );


$xFRM->addHElem( $txtA->get("idpersonas", $personas, "TR.Personas") );
$xFRM->addHElem( $txtA->get("idmails", $mails, "TR.Correos electronicos") );

$xFRM->addAviso($msg);

$xFRM->endSeccion();
$xFRM->addSeccion("iddatocontenido", "TR.Contenido");
$xFRM->addHElem( $txtA->get("idcontenido", $contenido, "TR.Contenido") );
$xFRM->addHElem( $txtA->get("idcomando", $precomandos, "TR.Precondicionales") );
$xFRM->addHElem( $txtA->get("idcomandocheck", $intentcheck, "TR.URL de Chequeo") );
$xFRM->endSeccion();

$xFRM->addSubmit();

echo $xFRM->get();
?>
<script >
<!--

//-->
function jsAddItem(evt, item){
	if($(item).val() == ""){
		$(item).val( evt.value );
	} else {
		$(item).val( evt.value + "," + $(item).val() );
	} 
}
</script>
<?php 
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
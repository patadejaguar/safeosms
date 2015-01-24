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
	$iduser 	= $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Agregar Empresas", HP_FORM);
$jxc 		= new TinyAjax();
$xQl		= new MQL();
$xLi		= new cSQLListas();
$xHP->setIncludeJQueryUI();

$empresa			= parametro("empresa", null, MQL_INT);
$step				= MQL_ADD;

$idsocio 			= parametro("idsocio", DEFAULT_SOCIO, MQL_INT);
$directivo 			= parametro("directivo", "");
$iddirectivo		= parametro("iddirectivo", DEFAULT_SOCIO, MQL_INT);
$diasaviso1			= parametro("dias_de_aviso1");
$diasaviso2			= parametro("dias_de_aviso2");

$diaspago1			= parametro("dias_de_pago1");
$diaspago2			= parametro("dias_de_pago2");

$diasnomina1		= parametro("dias_de_nomina1");
$diasnomina2		= parametro("dias_de_nomina2");

$periocidad1		= parametro("idperiocidad1", CREDITO_TIPO_PERIOCIDAD_SEMANAL, MQL_INT);
$periocidad2		= parametro("idperiocidad2", CREDITO_TIPO_PERIOCIDAD_QUINCENAL, MQL_INT);

$alias				= parametro("nombrecorto");
$oficial			= parametro("idoficial", getUsuarioActual(), MQL_INT);
$emails				= getEmails(false, MQL_STRING);
$producto			= parametro("idproducto", DEFAULT_TIPO_CONVENIO, MQL_INT);
$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$mail1				= "";
$mail2				= "";
$mail3				= "";
$msg				= "";
//$emaildeenvio		=
if($action == MQL_ADD AND ($idsocio > DEFAULT_SOCIO)){
	//TODO: Actualizar
	$xEmp			= new cEmpresas($idsocio);
	$xEmp->init();
	$dias_aviso		= $xEmp->getConjugarPeriodo($periocidad1, $diasaviso1) . $xEmp->getConjugarPeriodo($periocidad2, $diasaviso2);
	$dias_pago		= $xEmp->getConjugarPeriodo($periocidad1, $diaspago1) . $xEmp->getConjugarPeriodo($periocidad2, $diaspago2);
	$dias_nomina	= $xEmp->getConjugarPeriodo($periocidad1, $diasnomina1) . $xEmp->getConjugarPeriodo($periocidad2, $diasnomina2);
	$res			= $xEmp->add($idsocio, $directivo, $iddirectivo, $dias_aviso, $periocidad1, $alias, $oficial, $emails, $producto, $dias_nomina, $dias_pago);
	$msg			.= $xEmp->getMessages();
}

if($action == MQL_MOD AND (setNoMenorQueCero($empresa) > 0) ){
	//TODO: Actualizar
	$xEmp			= new cEmpresas($empresa);
	$xEmp->init();
	$dias_aviso		= $xEmp->getConjugarPeriodo($periocidad1, $diasaviso1) . $xEmp->getConjugarPeriodo($periocidad2, $diasaviso2);
	$dias_pago		= $xEmp->getConjugarPeriodo($periocidad1, $diaspago1) . $xEmp->getConjugarPeriodo($periocidad2, $diaspago2);
	$dias_nomina	= $xEmp->getConjugarPeriodo($periocidad1, $diasnomina1) . $xEmp->getConjugarPeriodo($periocidad2, $diasnomina2);
	$msg			.= $xEmp->getMessages();
	
	$xOEmp			= new cSocios_aeconomica_dependencias();
	$xOEmp->setData( $xOEmp->query()->initByID($empresa) );
	$xOEmp->dias_de_avisos($dias_aviso);
	$xOEmp->dias_de_liquidacion($dias_pago);
	$xOEmp->dias_de_pago_nomina($dias_nomina);
	$xOEmp->directivo_principal($directivo);
	$xOEmp->clave_de_directivo($iddirectivo);
	$xOEmp->periocidad_de_avisos($periocidad1);
	$xOEmp->oficial_que_cierra($oficial);
	$xOEmp->nombre_corto($alias);
	$xOEmp->producto_preferente($producto);
	$xOEmp->email_de_envio($emails);
	$xOEmp->clave_de_persona($idsocio);
	$xOEmp->query()->update()->save($empresa);
	
	$xEmp			= new cEmpresas($empresa);
	$xEmp->init();
	$xEmp->setActualizarPorPersona();
	$msg			.= $xEmp->getMessages();
}

if(setNoMenorQueCero($empresa) > 0 ){
	$xEmp			= new cEmpresas($empresa);
	$xEmp->init();
	if($xEmp->isInit() == true){
		$alias		= $xEmp->getNombreCorto();
		//$iddirectivo= 
		$iddirectivo	= $xEmp->getClaveDeContacto();
		$directivo		= $xEmp->getNombreContacto();
		$idsocio		= $xEmp->getClaveDePersona();
		$periocidad1	= $xEmp->getPeriocidadPref();
		$lstPeriodos	= $xEmp->getListaDePeriocidad();
		$periocidad2	= isset($lstPeriodos[1]) ? $lstPeriodos[1] : $periocidad1; 
		$producto		= $xEmp->getProductoPref();
		$mails			= $xEmp->getEmailsDeEnvio();
		$oficial		= $xEmp->getClaveDeOficial();
		$mail1			= (isset($mails[0])) ? $mails[0] : ""; 
		$mail2			= (isset($mails[1])) ? $mails[1] : "";
		$mail3			= (isset($mails[2])) ? $mails[2] : "";
		
		$diasaviso1		= $xEmp->getDiasDeAviso($periocidad1, MQL_STRING);
		$diasaviso2		= $xEmp->getDiasDeAviso($periocidad2, MQL_STRING);
		$diasnomina1	= $xEmp->getDiasDeNomina($periocidad1, MQL_STRING);
		$diasnomina2	= $xEmp->getDiasDeNomina($periocidad2, MQL_STRING);
		$diaspago1		= $xEmp->getDiasDePago($periocidad1, MQL_STRING);
		$diaspago2		= $xEmp->getDiasDePago($periocidad2, MQL_STRING);
		
		$step		= MQL_MOD;
		
	}
}
function jsaGetNombreDirector($persona){
	$xSocio		= new cSocio($persona);
	$xSocio->init();
	//idelmes0 idelanno0 ideldia0
	$tab = new TinyAjaxBehavior();
	//$tab -> add(TabSetvalue::getBehavior("idNumeroSocio", $socio));
	$tab -> add(TabSetvalue::getBehavior("idnombredependencia", $xSocio->getNombreCompleto()  ));
	//$tab -> add(TabSetvalue::getBehavior('idObservaciones', $xSoc->getMessages() ));
	return $tab -> getString();
}

$jxc ->exportFunction('jsaGetDatosHeredados', array("idsocio" ));
$jxc ->process();

echo $xHP->getHeader();
//$jsb	= new jsBasicForm("");

$xHP->init();

$xFRM		= new cHForm("frmagregarempresas", "agregar-empresas.frm.php?action=$step&empresa=$empresa");
$xTxt2		= new cHText(); 	$xTxt	= new cHText();
$xSel		= new cHSelect(); 	$xTabs	= new cHTabs();

$xFRM->addPersonaBasico("", false, $idsocio);

$xFRM->OText("nombrecorto", $alias, "TR.Nombre_corto");

$xFRM->addHElem( $xTxt2->getDeNombreDePersona("iddirectivo", $iddirectivo, "TR.Clave_de_Persona del Contacto") );
$xFRM->OText("directivo", $directivo, "TR.Nombre de Contacto");
$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("", $producto)->get(true) );

$xTabs->addTab("TR.Periocidad 1", $xSel->getListaDePeriocidadDePago("idperiocidad1", $periocidad1)->get("TR.Periocidad de pago", true) );
$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_aviso1", $diasaviso1, "TR.Dias de Aviso") );
$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_nomina1", $diasnomina1, "TR.Dias de Nomina") );
$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_pago1", $diaspago1, "TR.Dias de Pago") );


$xTabs->addTab("TR.Periocidad 2", $xSel->getListaDePeriocidadDePago("idperiocidad2", $periocidad1)->get("TR.Periocidad de pago", true) );
$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_aviso2", $diasaviso2, "TR.Dias de Aviso") );
$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_nomina2", $diasnomina2, "TR.Dias de Nomina") );
$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_pago2", $diaspago2, "TR.Dias de Pago") );

$xFRM->addAviso($msg);

$xFRM->addHTML( $xTabs->get() );


$xFRM->addHElem( $xSel->getListaDeOficiales("", "", $oficial)->get(true));

$xFRM->OText("idemail1", $mail1, "TR.Email de contacto 1");
$xFRM->OText("idemail2", $mail2, "TR.Email de contacto 2");
$xFRM->OText("idemail3", $mail3, "TR.Email de contacto 3");

$xFRM->addSubmit();

// $dias_de_aviso = "", $periocidad_de_aviso, $nombre_corto = "", $oficial_a_cargo = false


//

if($step != MQL_MOD){
	
	$mtbl = new cTabla($xLi->getListadoDeEmpresas());
	$mtbl->setKeyField("idsocios_aeconomica_dependencias");
	//$mtbl->addTool(SYS_UNO);
	//$mtbl->addTool(SYS_DOS);
	$mtbl->OButton("TR.Editar", "var xEmp = new EmpGen(); xEmp.setActualizarDatos(" . HP_REPLACE_ID . ")", $xFRM->ic()->EDITAR);
	$mtbl->OButton("TR.Panel", "jsGoToPanel(" . HP_REPLACE_ID . ")", $xFRM->ic()->EJECUTAR);
	$mtbl->setWithMetaData();
	$xFRM->addHTML( $mtbl->Show() );
}
	echo $xFRM->get();

//$jsb->setNameForm( $xFRM->getName() );
//$jsb->show();

$jxc ->drawJavaScript(false, true);
if($step != MQL_MOD){
	echo $mtbl->getJSActions(true);
}
?>
<script>
var xG	= new Gen();
var xPe	= new PersGen();

function jsGoToPanel(id){
	var mObj	= processMetaData("#tr-socios_aeconomica_dependencias-" + id);
	xPe.goToPanel(mObj.clave_de_persona, true);
}
</script>
<?php
$xHP->fin();
?>
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
$xHP		= new cHPage("TR.EDITAR EMPRESAS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
function jsaGetNombreDirector($persona){
	$xSocio		= new cSocio($persona);
	if($xSocio->init() == true){
		//idelmes0 idelanno0 ideldia0
		$tab = new TinyAjaxBehavior();
		//$tab -> add(TabSetvalue::getBehavior("idNumeroSocio", $socio));
		$tab -> add(TabSetvalue::getBehavior("idnombredependencia", $xSocio->getNombreCompleto()  ));
		//$tab -> add(TabSetvalue::getBehavior('idObservaciones', $xSoc->getMessages() ));
		return $tab -> getString();
	}
}

$jxc ->exportFunction('jsaGetDatosHeredados', array("idsocio" ));
$jxc ->process();

//$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$empresa		= parametro("id", 0, MQL_INT); $empresa		= parametro("clave", $empresa, MQL_INT);
$empresa		= parametro("empresa", $empresa, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);

$directivo 		= parametro("directivo", "");
$iddirectivo	= parametro("iddirectivo", DEFAULT_SOCIO, MQL_INT);
$diasaviso1		= parametro("dias_de_aviso1");
$diasaviso2		= parametro("dias_de_aviso2");

$diaspago1		= parametro("dias_de_pago1");
$diaspago2		= parametro("dias_de_pago2");

$diasnomina1	= parametro("dias_de_nomina1");
$diasnomina2	= parametro("dias_de_nomina2");

$periocidad1	= parametro("idperiocidad1", CREDITO_TIPO_PERIOCIDAD_SEMANAL, MQL_INT);
$periocidad2	= parametro("idperiocidad2", CREDITO_TIPO_PERIOCIDAD_QUINCENAL, MQL_INT);

$alias			= parametro("nombrecorto");
$oficial		= parametro("idoficial", getUsuarioActual(), MQL_INT);
$emails			= getEmails(false, MQL_STRING);
$producto		= parametro("idproducto", DEFAULT_TIPO_CONVENIO, MQL_INT);
$idcomision		= parametro("idcomision", 0, MQL_FLOAT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$tasapreferente	= parametro("tasa", 0, MQL_FLOAT);
$estatus		= parametro("estatus", 1, MQL_INT);

$mail1			= "";
$mail2			= "";
$mail3			= "";
$msg			= "";




$observaciones= parametro("idobservaciones");
$xHP->init();


$xOEmp			= new cSocios_aeconomica_dependencias();
$xOEmp->setData( $xOEmp->query()->initByID($empresa));
$xEmp			= new cEmpresas($empresa);

$xFRM			= new cHForm("frmempresas", "empresas.frm.php?action=$action");
$xSel			= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xTxt2			= new cHText(); 	$xTxt	= new cHText();
$xSel			= new cHSelect(); 	$xTabs	= new cHTabs();



$dias_aviso		= $xEmp->getConjugarPeriodo($periocidad1, $diasaviso1);
$dias_aviso		.= ($periocidad1 != $periocidad2) ? $xEmp->getConjugarPeriodo($periocidad2, $diasaviso2) : "";
$dias_pago		= $xEmp->getConjugarPeriodo($periocidad1, $diaspago1);
$dias_pago		.= ($periocidad1 != $periocidad2) ?  $xEmp->getConjugarPeriodo($periocidad2, $diaspago2) : "";
$dias_nomina	= $xEmp->getConjugarPeriodo($periocidad1, $diasnomina1);
$dias_nomina	.= ($periocidad1 != $periocidad2) ?  $xEmp->getConjugarPeriodo($periocidad2, $diasnomina2) : "";


if($action == SYS_NINGUNO) {
	$xFRM->setAction("empresas.edit.frm.php?action=" . MQL_MOD . "&empresa=$empresa");
	$xFRM->OHidden("empresa", $empresa);
	if($xEmp->init() == true){
		
		$lstPeriodos	= $xEmp->getListaDePeriocidad();
		$periocidad1	= isset($lstPeriodos[0]) ? $lstPeriodos[0] : $xEmp->getPeriocidadPref();
		$periocidad2	= isset($lstPeriodos[1]) ? $lstPeriodos[1] : $periocidad1;
		
		$mails			= $xEmp->getEmailsDeEnvio();
		$mail1			= (isset($mails[0])) ? $mails[0] : "";
		$mail2			= (isset($mails[1])) ? $mails[1] : "";
		$mail3			= (isset($mails[2])) ? $mails[2] : "";
		
		$diasaviso1		= $xEmp->getDiasDeAviso($periocidad1, MQL_STRING);
		$diasaviso2		= $xEmp->getDiasDeAviso($periocidad2, MQL_STRING);
		
		$diasnomina1	= $xEmp->getDiasDeNomina($periocidad1, MQL_STRING);
		$diasnomina2	= $xEmp->getDiasDeNomina($periocidad2, MQL_STRING);
		
		$diaspago1		= $xEmp->getDiasDePago($periocidad1, MQL_STRING);
		$diaspago2		= $xEmp->getDiasDePago($periocidad2, MQL_STRING);
	}
	$xFRM->addPersonaBasico("", false, $xOEmp->clave_de_persona()->v(), "jsValidarEmpresa()");
	$xFRM->OText("nombrecorto", $xOEmp->nombre_corto()->v(), "TR.Nombre_corto");
	$xFRM->setValidacion("nombrecorto", "validacion.novacio", "TR.EL NOMBRE_CORTO ES OBLIGATORIO", true);
	
	
	$xFRM->addHElem( $xTxt2->getDeNombreDePersona("iddirectivo", $xOEmp->clave_de_directivo()->v(), "TR.Clave_de_Persona del Contacto") );
	$xFRM->OText("directivo", $xOEmp->directivo_principal()->v(), "TR.Nombre de Contacto");
	$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("", $xOEmp->producto_preferente()->v())->get(true) );
	
	if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
		$xTabs->addTab("TR.Periocidad 1", $xSel->getListaDePeriocidadDePago("idperiocidad1",  $periocidad1)->get("TR.Periocidad de pago", true) );
		$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_aviso1", $diasaviso1, "TR.Dias de Aviso") );
		$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_nomina1", $diasnomina1, "TR.Dias de Nomina") );
		$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_pago1", $diaspago1, "TR.Dias de Pago") );
	
	
		$xTabs->addTab("TR.Periocidad 2", $xSel->getListaDePeriocidadDePago("idperiocidad2", $periocidad2)->get("TR.Periocidad de pago", true) );
		$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_aviso2", $diasaviso2, "TR.Dias de Aviso") );
		$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_nomina2", $diasnomina2, "TR.Dias de Nomina") );
		$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_pago2", $diaspago2, "TR.Dias de Pago") );
		$xFRM->addHTML( $xTabs->get() );
	}
	
	
	
	$xFRM->addHElem( $xSel->getListaDeOficiales("", "", $oficial)->get(true));
	
	$xFRM->OMail("idemail1", $mail1, "TR.Email de contacto 1");
	$xFRM->OMail("idemail2", $mail2, "TR.Email de contacto 2");
	$xFRM->OMail("idemail3", $mail3, "TR.Email de contacto 3");
		
	$xFRM->OTasa("idcomision", $xOEmp->comision_por_encargo()->v(), "TR.Comision_por_Encargo");
	$xFRM->OTasa("tasa", $xOEmp->tasa_preferente()->v(), "TR.TASA PREFERENTE");
	$xFRM->OSiNo("TR.ESTATUSACTIVO", "estatus", $xOEmp->estatus()->v());
	$xFRM->addSubmit();
	$xFRM->addJsInit("jsInitComponents();");
} else {

	
	
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
	$xOEmp->clave_de_persona($persona);
	$xOEmp->comision_por_encargo($idcomision);
	$xOEmp->tasa_preferente($tasapreferente);

	$xOEmp->estatus($estatus);
	
	$res 	= $xOEmp->query()->update()->save($empresa);
	
	$xEmp			= new cEmpresas($empresa);
	$xEmp->setCleanCache();
	if($xEmp->init() == true){
		$xEmp->setActualizarPorPersona();
	}

	
	
	$msg			.= $xEmp->getMessages();
	$xFRM->setResultado($res, $msg, $msg, true);
	
}
echo $xFRM->get();

?>
<script>
var xG		= new Gen();
var xPe		= new PersGen();
var xEmp	= new EmpGen();
var xVal	= new ValidGen();
var xNuevo	= false;
var cnf		= false;

function jsInitComponents(){
	if(xNuevo == false){
		$("#idsocio").trigger("onblur"); $("#idsocio").focus();
	}
}

function jsValidarEmpresa(){
	var idpersona	= $("#idsocio").val();
	if(xNuevo == true){
		xEmp.setBuscar({ persona : idpersona, callback: jsConfirmarPersona	});
	}
	return xVal.NoVacio($("#nombrecorto").val() );
}
function jsConfirmarPersona(existe){
	if(existe == true && cnf == false){
		xG.activarForma();
		xG.confirmar({ callback : jsOkConPersona , msg : "La persona existe como Empresa. Es Correcto duplicar el registro?", cancelar : jsNoConPersona	});
	}
}
function jsOkConPersona(){ 
	xG.activarForma(true);
	cnf		= true;
}
function jsNoConPersona(){
	xG.activarForma(true);
	if(xNuevo == true){
		$("#id-frmempresas").trigger("reset");
	}
	$("#nombrecorto").focus();
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
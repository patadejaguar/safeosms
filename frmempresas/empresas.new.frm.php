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
$xHP		= new cHPage("TR.AGREGAR EMPRESAS", HP_FORM);
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

$step			= parametro("step", SYS_NINGUNO, MQL_RAW);;

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

$mail1			= "";
$mail2			= "";
$mail3			= "";
$msg			= "";
$cerrar			= false;



$observaciones= parametro("idobservaciones");

//$xHP->addJsFile("../js/jquery.tagsinput.min.js");
//$xHP->addCSS("../css/jquery.tagsinput.min.css");

$xHP->init();


$xTabla		= new cSocios_aeconomica_dependencias();
$xTabla->setData( $xTabla->query()->initByID($empresa));
$xEmp			= new cEmpresas($empresa); //$xEmp->init();

$xFRM		= new cHForm("frmempresas", "empresas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xTxt2		= new cHText(); 	$xTxt	= new cHText();
$xSel		= new cHSelect(); 	$xTabs	= new cHTabs();
$xTxt->setPlaceholder("Separe con Comas como 15,30");


$dias_aviso		= $xEmp->getConjugarPeriodo($periocidad1, $diasaviso1);
$dias_aviso		.= ($periocidad1 != $periocidad2) ? $xEmp->getConjugarPeriodo($periocidad2, $diasaviso2) : "";
$dias_pago		= $xEmp->getConjugarPeriodo($periocidad1, $diaspago1);
$dias_pago		.= ($periocidad1 != $periocidad2) ?  $xEmp->getConjugarPeriodo($periocidad2, $diaspago2) : "";
$dias_nomina	= $xEmp->getConjugarPeriodo($periocidad1, $diasnomina1);
$dias_nomina	.= ($periocidad1 != $periocidad2) ?  $xEmp->getConjugarPeriodo($periocidad2, $diasnomina2) : "";


if($action == SYS_NINGUNO) {
	$xFRM->setAction("empresas.new.frm.php?action=" . MQL_ADD . "&empresa=$empresa");
	$xFRM->OHidden("empresa", $empresa);
	
	$xFRM->addPersonaBasico("", false, $persona, "jsValidarEmpresa()");
	$xFRM->OText("nombrecorto", $alias, "TR.Nombre_corto");
	$xFRM->setValidacion("nombrecorto", "validacion.novacio", "TR.EL NOMBRE_CORTO ES OBLIGATORIO", true);
	
	
	$xFRM->addHElem( $xTxt2->getDeNombreDePersona("iddirectivo", $iddirectivo, "TR.Clave_de_Persona del Contacto") );
	$xFRM->OText("directivo", $directivo, "TR.Nombre de Contacto");
	$xFRM->addHElem( $xSel->getListaDeProductosDeCreditoNomina("", $producto)->get(true) );
	
	if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
		$xST1		= $xSel->getListaDePeriocidadDePagoNomina("idperiocidad1", $periocidad1);
		
		//$xST1->addEvent("onchange", "jsEvtSel1()");
		
		$xTabs->addTab("TR.Periocidad 1", $xST1->get("TR.Periocidad de pago", true) );
		$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_aviso1", $diasaviso1, "TR.Dias de Aviso") );
		$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_nomina1", $diasnomina1, "TR.Dias de Nomina") );
		$xTabs->addTab("TR.Periocidad 1", $xTxt->getNormal("dias_de_pago1", $diaspago1, "TR.Dias de Pago") );
	
		$xST2		= $xSel->getListaDePeriocidadDePagoNomina("idperiocidad2", $periocidad2);
		//$xST2->addEvent("onchange", "jsEvtSel2()");
		
		$xTabs->addTab("TR.Periocidad 2", $xST2->get("TR.Periocidad de pago", true) );
		$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_aviso2", $diasaviso2, "TR.Dias de Aviso") );
		$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_nomina2", $diasnomina2, "TR.Dias de Nomina") );
		$xTabs->addTab("TR.Periocidad 2", $xTxt->getNormal("dias_de_pago2", $diaspago2, "TR.Dias de Pago") );
		
		$xFRM->addHTML( $xTabs->get() );
	}
	
	
	
	$xFRM->addHElem( $xSel->getListaDeOficiales("", SYS_USER_ESTADO_ACTIVO, $oficial)->get(true));
	
	
	$xFRM->OMail("idemail1", $mail1, "TR.Email de contacto 1");
	$xFRM->OMail("idemail2", $mail2, "TR.Email de contacto 2");
	$xFRM->OMail("idemail3", $mail3, "TR.Email de contacto 3");

	
	$xFRM->OTasa("idcomision", $idcomision, "TR.Comision_por_Encargo");
	$xFRM->OTasa("tasa", $tasapreferente, "TR.TASA POR_DEFECTO");
	
	$xFRM->addSubmit();
	$xFRM->addJsInit("jsInitComponents();");
} else {

	$res			= $xEmp->add($persona, $directivo, $iddirectivo, $dias_aviso, $periocidad1, $alias, $oficial, $emails, $producto, $dias_nomina, $dias_pago, $idcomision, $tasapreferente);
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
var xNuevo	= <?php echo ($persona <= DEFAULT_SOCIO) ? "true" : "false"; ?>;
var cnf		= false;


function jsInitComponents(){
	if(xNuevo == false){
		$("#idsocio").trigger("onblur"); $("#idsocio").focus();
		//$("#dias_de_aviso1").tagsInput({			'autocomplete': { "opt" : "val", "opt2":"val2"}			});
	}
	
}
function jsEvtSel1(){
	//$(".ct option[value='X']").remove(); remover opcion
}
function jsEvtSel2(){
	
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
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
$xHP		= new cHPage("TR.Registro de Personas Riesgosas", HP_FORM);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xF			= new cFecha();
$xAml		= new cAML();
$nivelcargado	= SYS_RIESGO_BAJO;
$notas			= "";
if($persona > DEFAULT_SOCIO){
	$xAml	= new cAMLPersonas($persona);
	if( $xAml->init() == true){
		$nivelcargado	= $xAml->setAnalizarNivelDeRiesgo();
		$notas			= $xAml->getMessages();	
	}
}
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$xHP->init();
?>
<style>
textarea { min-height:8em; }
</style>
<?php

//$jxc ->drawJavaScript(false, true);

$xFRM	= new cHForm("frmriesgo", "registro_persona_riesgosa.frm.php?action=" . MQL_ADD);
$xBtn	= new cHButton();		
$xTxt	= new cHText();
$xSel	= new cHSelect();
$xFec	= new cHDate();
$xTa	= new cHTextArea();
$xChk	= new cHCheckBox();

if($action == SYS_NINGUNO){
	$xUsers = $xSel->getUsuarios("idusuarioreportado");
	$xFRM->setTitle($xHP->getTitle());
	if($persona > DEFAULT_SOCIO){
		$xFRM->OHidden("idsocio", $persona);
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xFRM->addHElem( $xSoc->getFicha(false, false, "", true) );
		}
	} else {
		$xFRM->addPersonaBasico("", false, $persona);
	}
	$xFRM->addJsBasico(iDE_AML);
	$xFRM->ODate("idfechaevento", false, "TR.Fecha de Evento");
	$xFRM->addHElem($xSel->getListaDeNivelDeRiesgo("", $nivelcargado)->get("TR.Nivel de Riesgo", true));
	$xFRM->OTextArea("idmensaje", $notas,  "TR.AML_TEXTO_C");
	$xFRM->addHElem( $xChk->get("TR.Agregar como no_vigilada", "idnovigilada") );
	$xFRM->OCheck("TR.Agregar a LISTA_NEGRA", "idaddlistanegra");
	$xFRM->addSubmit();
	
} else {
	if(setNoMenorQueCero($persona) > 0 ){
		$fecha				= parametro("idfechaevento", false, MQL_DATE);
		$mensaje			= parametro("idmensaje");
		$nivel				= parametro("idnivelderiesgo",0, MQL_INT);
		$novigilada			= parametro("idnovigilada",false, MQL_BOOL);
		$addlistanegra		= parametro("idaddlistanegra",false, MQL_BOOL);
		$motivo				= parametro("idmotivo", AML_RISK_INTERNAL_OPERATION, MQL_INT);
		
		$xSoc				= new cSocio($persona);
		$xSoc->init();
		$xSoc->setActualizarNivelDeRiesgo($nivel, $mensaje, $fecha, $novigilada);
		if(MODO_DEBUG == true){ $xFRM->addAviso($xSoc->getMessages()); }
		if($addlistanegra == true){
			$xAml				= new cAMLPersonas($persona);
			if( $xAml->init() == true){
				$xAml->addToListaNegra($motivo, $nivel, $mensaje, $xF->getFechaMaximaOperativa());
			}
		}
		$xFRM->addAvisoRegistroOK();
		$xFRM->addCerrar();
	}
}

echo $xFRM->get();

?>
<script>

</script>
<?php
$xHP->fin();
?>
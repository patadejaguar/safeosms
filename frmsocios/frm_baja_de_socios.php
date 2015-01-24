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
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT); $persona = parametro("i", $persona, MQL_INT);
$xHP->init();
$xFRM		= new cHForm("frm", "frm_baja_de_socios.php");
$xSel		= new cHSelect();

$msg		= "";
if($persona <= DEFAULT_SOCIO){
	$xFRM->addPersonaBasico();
} else {
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->OHidden("persona", $persona);
		//checar si existen razones
		$fecha		= parametro("fecha", false, MQL_DATE);
		$idrazon	= parametro("idrazondebaja", false,  MQL_INT);
		$notas		= parametro("notas");
		$fechavenc	= parametro("idfechavencimiento", false, MQL_DATE);
		$documento	= parametro("documento", 0, MQL_INT);
//		$fechadocto	
		if(setNoMenorQueCero($idrazon) > 0){
			$ok		= $xSoc->setBaja($idrazon, $fecha, $fechavenc, $documento);
			if($ok == true){
				$xFRM->addAvisoRegistroOK();
			} else {
				$xFRM->addAvisoRegistroError();
			}
		} else {
			$xFRM->ODate("fecha", false, "TR.Fecha");
			$xFRM->ODate("idfechavencimiento", $xF->getFechaMaximaOperativa(), "TR.Fecha de Vencimiento");
			$xFRM->addHElem( $xSel->getListaDeRazonesDeBaja()->get(true) );
			$xFRM->OText("documento", 0, "TR.Documento de Prueba");
			$xFRM->OTextArea("notas", "", "TR.Notas");
		}
	}
		
}
//razones de baja

$xFRM->addSubmit();
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
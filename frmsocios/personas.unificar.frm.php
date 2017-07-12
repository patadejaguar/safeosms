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
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
function jsaSetUnificar($persona1, $persona2){
	$msg		= "";
	$xSoc1		= new cSocio($persona1);
	if($xSoc1->init() == true){
		$xSoc2	= new cSocio($persona2);
		if($xSoc2->init() == true){
			$xSoc2->setChangeCodigo($persona1);
			$xSoc2->setDeleteSocio();
			$xSoc1->addMemo(MEMOS_TIPO_HISTORIAL, "Unificacion de personas, se agrega $persona2");
		}
		$msg	.= $xSoc2->getMessages();
	}
	$msg	.= $xSoc1->getMessages();
	return $msg;
}
$jxc ->exportFunction('jsaSetUnificar', array('idpersona1', 'idpersona2'), "#idmsg");
$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

$persona2	= parametro("persona2", DEFAULT_SOCIO, MQL_INT); $persona2 = parametro("socio2", $persona2, MQL_INT); $persona2 = parametro("idsocio2", $persona2, MQL_INT);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();

//$xFRM->addJsBasico();

$xSoc1		= new cSocio($persona);
$xFRM->setNoAcordion();
if($xSoc1->init() == true){
	$xFRM->addSeccion("id1", "TR.PERSONA SIN CAMBIOS");
	$xFRM->addHElem($xSoc1->getFicha(true));
	$xFRM->OHidden("idpersona1", $xSoc1->getCodigo());
	$xFRM->endSeccion();
}

$xSoc2		= new cSocio($persona2);
if($xSoc2->init() == true){
	$xFRM->addSeccion("id2", "TR.PERSONA A ELIMINAR");
	$xFRM->addHElem($xSoc2->getFicha(true));
	$xEstat	= new cPersonasEstadisticas($persona2);
	$xEstat->initDatosDeCredito();
	if($xEstat->getTotalCreditosActivos() > 0){
		$xFRM->addAvisoRegistroError("TR.ESTA PERSONA NO PUEDE SER UNIFICADA");
		$xFRM->addCerrar();
	} else {
		$xFRM->OButton("TR.Unificar", "jsSetUnificar()", $xFRM->ic()->GUARDAR);
		$xFRM->OHidden("idpersona2", $xSoc2->getCodigo());
	}
	$xFRM->endSeccion();
}

echo $xFRM->get();

?>
<script>
var xG	= new Gen();
function jsSetUnificar(){
	xG.spin({time:4500, callback: jsSalir});
	jsaSetUnificar();
}
function jsSalir(){ xG.close(); }
</script>
<?php

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
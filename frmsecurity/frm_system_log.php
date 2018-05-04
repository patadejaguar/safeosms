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


$xHP->init();
$xSel		= new cHSelect();
$xPan		= new cPanelDeReportes(0, "seguridad");


$xSel1		= $xSel->getListadoGenerico("general_error_codigos", "codigo");
$xSel1->addVacio(true);
$xSel2		= $xSel->getListaDeCatalogoGenerico("cat_error_clases", "idtipo");
$xSel2->addVacio(true);
//$xSel2		= $xSel->getListadoGenerico("general_error_codigos", "idnivel");

$xPan->setConCajero(false);
$xPan->setConCreditos(false);
$xPan->setConOperacion(false);
$xPan->setConRecibos(false);
$xPan->setConSucursal(false);
$xPan->setConEmpresa(false);

$xPan->addFechaInicial();
$xPan->addFechaFinal();

$xPan->OFRM()->OText("buscar", "", "TR.BUSCAR");
$xPan->addjsVars("buscar", "buscar");

$xPan->addUsuarios();

$xPan->addHTML($xSel2->get("TR.TIPO", true));
$xPan->addjsVars("idtipo", "idtipo");

$xPan->addHTML($xSel1->get("TR.CODIGO", true));
$xPan->addjsVars("codigo", "codigo");





//$xPan->OFRM()->addHElem();
//$xPan->addControl()

//$xFRM		= new cHForm("frmreports", "./");
//$xSel		= new cHSelect();
//$xFRM->setTitle($xHP->getTitle());
//$xFRM->setNoAcordion();
echo $xPan->get();
echo $xPan->getJs();

//$xFRM->ODate("idfinicial", "false", "TR.FECHA_INICIAL");
//$xFRM->ODate("idffinal", "false", "TR.FECHA_FINAL");
$xHP->fin();
?>

<?php
//====================================================================================================
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
//====================================================================================================
$xHP		= new cHPage("TR.Informacion del Sistema");
$xDiv		= new cHDiv();

$xHP->init();

$xFRM		= new cHForm("frmcreditosbuscar", "./");
$msg		= "";

$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();

$xFRM->addDivSolo('<img src="../images/banner-safe.png"/>');

$xFRM->addSeccion("id0", "TR.Autor");
$xFRM->addHElem("<h5>Balam Gonzalez Luis Humberto 2005-2018</h5>");
$xUl2	= new cHUl("", "ul", "");
$xUl2->li("<a href='https://www.sipakal.com/'>Soporte Comercial</a>");
$xFRM->addHElem($xUl2->get());
//$xUl2->li('<a href="http://www.opencorebanking.com/">P&aacute;gina del Proyecto : www.opencorebanking.com</a>');
//$xUl2->li('<a href="http://sourceforge.net/projects/safemicrofin/"> Hosting del Proyecto :  SourceForge</a>');
//$xUl2->li('Blog del Proyecto <a href="http://sourceforge.net/apps/wordpress/safemicrofin/">Hospedado en SourceForge</a>');
//$xFRM->addHTML( $xUl2->li('<a href="http://wiki.opencorebanking.com/">Wiki del Proyecto Hospedado en www.opencorebanking.com</a>')->end());
$xFRM->endSeccion();

//$xFRM->addDivSolo("")
$xFRM->addSeccion("id1", "Soporte Financiero");
$xUl3	= new cHUl();
$xUl3->li('GRUPO PADIO, SOFOM ENR (Desde Agosto de 2013)');
$xFRM->addHElem( $xUl3->li('CAJA SOLIDARIA MULMEYAH, S.C. DE A.P. DE C.V. DE R.L (Hasta Diciembre de 2006)')->end() );
$xFRM->endSeccion();


$xFRM->addSeccion("id2", "Agradecimientos Especiales");
$xUl		= new cHUl();
$xUl->li("Lic. Alejandro Ojeda, Por su confianza en el proyecto.");
$xUl->li("Ing. Jorge Poot.- Motivaci&oacute;n, Base de datos, Normalizaci&oacute;n  y su Gran Experiencia");
$xUl->li("Ing. Gabriel Ruiz.- Seguridad, Motivaci&oacute;n y uso en otras entidades");
//$xUl->li("");
$xFRM->addHElem( $xUl->li("L.I. Victor Rojas.- Motivaci&oacute;n, Recomendaci&oacute;n y uso del Now How")->end() );
$xFRM->endSeccion();

$xFRM->addSeccion("id3", "TR.DATOS DEL SISTEMA");
$xULi	= new cHUl();
$xFRM->addHElem( $xULi->li("Base de Datos:" . MY_DB_IN)->li("Servidor: " . WORK_HOST)->li("Sucursal: " . getSucursal())
		->li("Version S.A.F.E.:" . SAFE_VERSION)->li("Revision S.A.F.E: " . SAFE_REVISION)->li("Path Temporal:" . PATH_TMP)
		->li("Path Backups:" . PATH_BACKUPS)->li("Fecha del Sistema: " . date("Y-m-d H:i:s"))->li("Usuario Activo: " .elusuario(getUsuarioActual()))->end() );
$xFRM->endSeccion();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>
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
$xHP		= new cHPage("TR.Creditos del Sistema");


$xDiv		= new cHDiv();

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$msg		= "";

$xFRM->addDivSolo('<img src="../images/banner-safe.png" />');
$xFRM->addHTML("<h3>AUTOR</h3>");
$xFRM->addHTML("<h5>Balam Gonzalez Luis Humberto 2005-2014</h5>");

$xUl2	= new cHUl();
$xUl2->li('<a href="http://www.opencorebanking.com/">P&aacute;gina del Proyecto : www.opencorebanking.com</a>');
$xUl2->li('<a href="http://sourceforge.net/projects/safemicrofin/"> Hosting del Proyecto :  SourceForge</a>');
//$xUl2->li('Blog del Proyecto <a href="http://sourceforge.net/apps/wordpress/safemicrofin/">Hospedado en SourceForge</a>');
//$xFRM->addHTML( $xUl2->li('<a href="http://wiki.opencorebanking.com/">Wiki del Proyecto Hospedado en www.opencorebanking.com</a>')->end());


//$xFRM->addDivSolo("")
$xFRM->addHTML("<h3>FINANCIAMIENTO</h3>");

$xUl3	= new cHUl();
//<a href="http://www.grupopadio.com.mx"> <a href="http://www.mulmeyah.org">
$xUl3->li('GRUPO PADIO, SOFOM ENR (Desde Agosto de 2013).- Calle 25 # 87-A, Col. México, Mérida, Yucatán');
$xFRM->addHTML( $xUl3->li('CAJA SOLIDARIA MULMEYAH, S.C. DE A.P. DE C.V. DE R.L (Hasta Diciembre de 2006).- Calle 61 Num. 50 Entre 16 y 14, Col. Centro. San Francisco de Campeche, 01(981)8113766')->end() );

$xFRM->addHTML("<h3>AGRADECIMIENTOS ESPECIALES</h3>");
$xUl		= new cHUl();
$xUl->li("Lic. Alejandro Roberto de jesus Ojeda Mendez, Por su confianza en el proyecto.");
$xUl->li("Ing. Jorge Alberto Poot Xiu.- Motivaci&oacute;n, Base de datos, Normalizaci&oacute;n  y su Gran Experiencia");
$xUl->li("Ing. Gabriel Orozco Ruiz Velazco.- Seguridad, Motivaci&oacute;n y uso en otras entidades");
//$xUl->li("");

$xFRM->addHTML( $xUl->li("L.I. Victor Rojas.- Motivaci&oacute;n, Recomendaci&oacute;n y uso del Now How")->end() );

$xFRM->addHTML("<h3>DATOS DEL SISTEMA</h3>");
$xULi	= new cHUl();
$xFRM->addHTML( $xULi->li("Base de Datos:" . MY_DB_IN)->li("Servidor: " . WORK_HOST)->li("Sucursal: " . getSucursal())
		->li("Version S.A.F.E.:" . SAFE_VERSION)->li("Revision S.A.F.E: " . SAFE_REVISION)->li("Path Temporal:" . PATH_TMP)
		->li("Path Backups:" . PATH_BACKUPS)->li("Fecha del Sistema: " . date("Y-m-d H:i:s"))->li("Usuario Activo: " .elusuario(getUsuarioActual()))->end() );


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>
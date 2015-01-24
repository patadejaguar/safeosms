<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	//$permiso					= getSIPAKALPermissions($theFile);
	//if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xP		= new cHPage("Pruebas de la Clase Common", HP_FORM);
$persona	= parametro("persona", 1901850);
$xP->setIncludes();
$xSoc			= new cSocio($persona, true);

echo $xP->getHeader();
echo $xP->setBodyinit();
//
function list_system_locales(){
    ob_start();
    system('locale -a');
    $str = ob_get_contents();
    ob_end_clean();
    return split("\\n", trim($str));
}

$locale = "es_MX.iso-8859-1";
setlocale(LC_ALL, $locale);

$locales = list_system_locales();
//var_dump($locales);
//Crear formularios
$xFRM	= new cHForm("frmTest", "./test.php");

$xHTxt	= new cHText("");
//====================================================================================================
$xFRM->addHElem( "<p class='aviso'>Pruebas de la Clase Colonia</p>" );
//$txt 	= $xHTxt->getDeMoneda("id", "Moneda de Prueba",  100);
$xColonia	= new cDomiciliosColonias();
$clave		= $xColonia->getClavePorNombre("Ampliacion josefa ortiz de dom");
$xColonia->init();

$lng			= new cLang();

$xFRM->addHElem( $lng->getT( "TR.La Clave Buscada es ") . $clave . "<br />");
$xFRM->addHElem( $lng->getT( "TR.El Codigo Postal Buscado es ") . $xColonia->getCodigoPostal() . "<br />");
$xFRM->addHElem( $lng->getT( "TR.EL Nombre de la Colonia es ") . $xColonia->getNombre() . "<br />");
$xFRM->addHElem( $lng->getT( "TR.EL Nombre del Municipio es "). $xColonia->getNombreMunicipio() . "<br />");
$xFRM->addHElem( $lng->getT( "TR.EL Nombre del Estado es "). $xColonia->getNombreEstado() . "<br />");
$xFRM->addHElem( $lng->getT( "TR.EL Nombre de la Localidaad es "). $xColonia->getNombreLocalidad() . "<br />");
//$xFRM->addHElem( $txt );
$xFRM->addHElem( "<p class='aviso'>Probando con el C.P. 24026</p>" );
$clave		= $xColonia->getClavePorCodigoPostal(24026);
//$xColonia->init();
$xFRM->addHElem( "<p>La OTRA Clave Buscada es ". $clave . "</p>");
$xFRM->addHElem( "<p>EL Nombre de la Colonia es ". $xColonia->getNombre() . "</p>");
$xFRM->addHElem( "<p>EL Nombre del Municipio es ". $xColonia->getNombreMunicipio() . "</p>");
$xFRM->addHElem( "<p>EL Nombre del Estado es ". $xColonia->getNombreEstado() . "</p>");
$xFRM->addHElem( "<p>EL Nombre de la Localidad es ". $xColonia->getNombreLocalidad() . "</p>");

$xFRM->addHElem( "<p>EL Nombre de la Localidad2 es ". $xColonia->getOColonia()->sucursal()->v() . "</p>");
///Iniciar el estado
$xColonia->getOEstado();
$xFRM->addHElem( "<p>EL Nombre de la Localidad2 es ". $xColonia->getClaveEstadoEnSIC() . "</p>");
$xFRM->addHTML("<p class='aviso'>" . $xColonia->getMessages(OUT_HTML) . "</p>");

$xFRM->addHElem( "<hr />");
$xFRM->addHElem( "<p class='aviso'>Probando con el C.P. 24010 y la palabra Guadalupe (2)</p>" );
$existentes		= $xColonia->existe(24010, false, "guadalupe", true);
$xFRM->addHElem( "<p>Existentes en Guadalupe ". $existentes . "</p>");
$xFRM->addHElem( "<p>EL Nombre de la Colonia es ". $xColonia->getNombre() . "</p>");
$xFRM->addHElem( "<p>EL Nombre del Municipio es ". $xColonia->getNombreMunicipio() . "</p>");
$xFRM->addHElem( "<p>EL Nombre del Estado es ". $xColonia->getNombreEstado() . "</p>");
$xFRM->addHElem( "<p>EL Nombre de la Localidad es ". $xColonia->getNombreLocalidad() . "</p>");
$xFRM->addHElem( "<p>EL Tipo de Asentamiento es ". $xColonia->getTipoDeAsentamiento() . "</p>");
//$xFRM->addHElem( "La $miFecha, Dias del Mes", $xF->getDiasDelMes() ));

$xFRM->addHElem( "<p class='aviso'>Probando con el C.P. 24010 y la palabra ERRONEA caisa</p>" );
$xFRM->addHElem( "<p class='aviso'>El sistema debe devolver valor por defecto</p>" );
$existentes		= $xColonia->existe(24010, false, "caisa", true);
$xFRM->addHElem( "<p>Existentes en Guadalupe ". $existentes );
$xFRM->addHElem( "<p>EL Nombre de la Colonia es ". $xColonia->getNombre() . "</p>");
$xFRM->addHElem( "<p>EL Nombre del Municipio es ". $xColonia->getNombreMunicipio() . "</p>");
$xFRM->addHElem( "<p>EL Nombre del Estado es ". $xColonia->getNombreEstado() . "</p>");
$xFRM->addHElem( "<p>EL Nombre de la Localidad es ". $xColonia->getNombreLocalidad() . "</p>");
$xFRM->addHElem( "<p>EL Tipo de Asentamiento es ". $xColonia->getTipoDeAsentamiento() . "</p>");
$xFRM->addHElem( "<hr />");
$xFRM->addHElem( "<hr />");
//$xSoc->setInitDatosDeCredito();
$xFRM->addAviso("Total de creditos activo " . $xSoc->getTotalCreditosActivos());
$xFRM->addAviso("Total de Saldo de Creditos  " . $xSoc->getTotalCreditosSaldo());
//$xFRM->addHElem( "La $miFecha, Dias del Mes", $xF->getDiasDelMes() ));
//iniciando Domicilio de persona

$xFRM->addHElem( "<hr />");
$DDOM			= $xSoc->getODomicilio();
if($DDOM == null){
	
} else {
$DEstado		= $DDOM->getOEstado();
$xFRM->addHElem( "<p>El Nombre es ". $DDOM->getEstado() . "</p>" );
$xFRM->addHElem( "<p>La clave SIC es ". $DDOM->getClaveDeEstadoEnSIC() . "</p>" );
}
$xFRM->addHTML("<p class='aviso'>" . $xColonia->getMessages(OUT_HTML) . "</p>");
$xFRM->addHTML("<h3>DATOS FISCALES</h3>");

$xFRM->addHTML("<p class='aviso'>RFC evaluado : " . $xSoc->getRFC(true) . "</p>");
$xFRM->addHTML("<p class='aviso'>RFC evaluado/ret: " . $xSoc->getRFC(true, true) . "</p>");
$xFRM->addHTML("<p class='aviso'>RFC : " . $xSoc->getRFC() . "</p>");
//Iniciar por CURP

if( $xSoc->initByIDLegal("RACD-890730- KC3") == true){

	$xFRM->addHElem( "<p>El Nombre es por CURP ES ". $xSoc->getNombreCompleto() . "</p>" );
	$xID		= new cIDLegal( $xSoc->getCURP() );
	
	$xFRM->addHElem( "<p>La fecha por CURP es ". $xID->getFechaCreacion() . "</p>" );

}

$xLoc	= new cDomicilioLocalidad(false);

$xLoc->setBuscar("MERXICA", 4, 1, "MX");

$xFRM->addAviso( $xLoc->getNombre() );
//$xC		= new cSocios_general();

/*$query		= $xC->query()->select();
$query->where("codigo > 0");
$rs	= $query->exec();
foreach ($rs as $rows){
	$xC->setData($rows);
	echo $xC->nombrecompleto()->v();
}*/
$xFRM->addHElem( "<hr />");
$xUS		= new cSystemUser(TASK_USR, false);
$xUS->init(); 
$xFRM->addHElem("<p class='aviso'>USER SYSTEM: " . $xUS->getNombreCompleto() . "</p>");


echo $xFRM->get();

echo $xP->setBodyEnd();

echo $xP->end();
//=====================================================================================================

?>
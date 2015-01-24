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
$xP		= new cHPage("Pruebas de Fechas", HP_FORM);


echo $xP->getHeader();
echo $xP->setBodyinit();
//Crear formularios
$xFRM	= new cHForm("frmTest", "./test.php");

$xHTxt	= new cHText("");
$xHChk	= new cHCheckBox();
$xFRM->setTitle("TR.Pruebas HTML");
//$xFRM->addHTML("<p>Esto es un parrafo de prueba</p>");
$xFRM->addHElem( $xHChk->get("id1") );

$xFRM->addHElem( $xHChk->get("id2") );
$xFRM->addHElem( $xHChk->get("id3") );
$xFRM->addHElem( $xHChk->get("id4") );

$xSoc		= new cSocios_general();
$xSoc->setData( $xSoc->query()->initByID(DEFAULT_SOCIO) );
var_dump($xSoc->query()->getCampos());
//$xFRM->addAviso( $xSoc->query()->getListaDeCampos() );

$xFRM->OMoneda("ix", 4500, convertirletras("4,455.05"));
$xFRM->OMoneda("ix", 4500, convertirletras("4454.455.05"));
$xFRM->OMoneda("ix", 4500, convertirletras(45000));


//echo $xFRM->get();
$xTabla		= new cCreditos_tipoconvenio();

$xFRM	= new cHForm("frmcreditos_tipoconvenio", "creditos_tipoconvenio");
$xFRM->addSubmit();

$xEmp	= new cEmpresas(100);
$xEmp->init();
if($xEmp->getOPersona() == null){
	$xFRM->OText("idper", "SIN PERSONA", "TR.Persona");
}  else {
	$xPer		= $xEmp->getOPersona()->init();
	$xFRM->OText("idper", $xEmp->getOPersona()->getNombreCompleto(), "TR.Persona");
}


echo $xFRM->get();


#require('lib/gantti.php');

#date_default_timezone_set('UTC');
#setlocale(LC_ALL, 'en_US');

$data = array();

$data[] = array(
		'label' => 'Project 1',
		'start' => '2012-04-20',
		'end'   => '2012-05-12'
);

$data[] = array(
		'label' => 'Project 2',
		'start' => '2012-04-22',
		'end'   => '2012-05-22',
		'class' => 'important'
);

$data[] = array(
		'label' => 'Project 3',
		'start' => '2012-05-25',
		'end'   => '2012-06-20',
		'class' => 'urgent'
);

$gantti = new Gantti($data, array(
		'title'      => 'Demo',
		'cellwidth'  => 25,
		'cellheight' => 35
));

echo $gantti;


echo $xP->setBodyEnd();

echo $xP->end();
//=====================================================================================================

?>

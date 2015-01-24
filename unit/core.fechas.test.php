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

$xP->setIncludes();

echo $xP->getHeader();
echo $xP->setBodyinit();
//Crear formularios
$xHFrm	= new cHForm("frmTest", "./test.php");

$xHTxt	= new cHText("");
//$txt 	= $xHTxt->getDeMoneda("id", "Moneda de Prueba",  100);
$miFecha	= parametro("fecha", fechasys());
$xF		= new cFecha(0, $miFecha);

//$xHFrm->addHElem( $txt );

$xHFrm->addHElem( $xF->show(true) );

$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Inicio se semana==". $xF->getFechaDeInicioDeSemana() ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Inicio se nombre semana==". $xF->getDayName( $xF->getFechaDeInicioDeSemana() ) ."</p>");

$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Dias del Mes==". $xF->getDiasDelMes() ."</p>");
$xHFrm->addHElem("<p class='aviso'>La $miFecha, En Fecha Corta==". $xF->getFechaCorta()  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha Larga==". $xF->getFechaLarga()  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, En Fecha Mediana==".  $xF->getFechaMediana()  ."</p>");
//$xHFrm->addHElem(  );
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha Operativa Maxima==". $xF->getFechaMaximaOperativa()  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, El Mes Anterior==". $xF->getFechaMesAnterior()  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, dos meses atras==". $xF->getFechaMesAnterior(false, 2)  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha  - 30 Dias==". $xF->setRestarDias(30)  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha  + 30 Dias==" . $xF->setSumarDias(30)  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>La $miFecha  + 2 meses==" . $xF->setSumarMeses(2)  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha  - 2 meses==" . $xF->setRestarMeses(2)  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Nombre del Dia==" . $xF->getDayName()  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Dia Inicial del mes==" . $xF->getDiaInicial()  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Obtener Dia Habil==". $xF->getDiaHabil()  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Dias desde el 31Dic2013==" . $xF->setRestarFechas($xF->get(), "2013-12-31")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Buscar Sabado(D6) para el dia de hoy==". $xF->getDiaAbonoSemanal(6)  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Buscar NOMBRE Sabado(D6) para el dia de hoy==". $xF->getDayName( $xF->getDiaAbonoSemanal(6) )  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha de Pago Decenal 1==". $xF->getDiaAbonoDecenal(10, 20, 30, "2014-03-02")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha de Pago Decenal 1==". $xF->getDiaAbonoDecenal(10, 20, 30, "2014-03-11")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha de Pago Decenal 1==". $xF->getDiaAbonoDecenal(10, 20, 30, "2014-03-18")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha de Pago Decenal 1==". $xF->getDiaAbonoDecenal(10, 20, 30, "2014-03-20")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha de Pago Decenal 1==". $xF->getDiaAbonoDecenal(10, 20, 30, "2014-03-24")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha de Pago Decenal 1==". $xF->getDiaAbonoDecenal(10, 20, 30, "2014-03-29")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Fecha de Pago Decenal 1==". $xF->getDiaAbonoDecenal(10, 20, 30, "2014-03-31")  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Obtener dias Loborales en el mes==". $xF->getDiasHabilesEnRango("2014-03-31", "2014-03-01")  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Obtener entero del mes==". $xF->getInt( fechasys() )  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>La $miFecha, DIAS CORRIENTES DE MES". $xF->getDiasCorrientesDeMes()  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>La $miFecha, sumar  7 dias time stamp 84600==". $xF->getFechaByInt(( $xF->getInt( fechasys() ) + ((7+1) * 84600) ) )  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>" . $xF->getMessages(OUT_HTML)  ."</p>");
//$xHFrm->addHTML("<p>Esto es un parrafo de prueba</p>");

echo $xHFrm->get();

echo $xP->setBodyEnd();

echo $xP->end();
//=====================================================================================================

?> 
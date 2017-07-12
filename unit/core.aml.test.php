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
$xP		= new cHPage("Pruebas del modulo de lavado de dinero", HP_FORM);
include_once("../core/core.riesgo.reports.php");
$xP->setIncludes();

echo $xP->getHeader();
echo $xP->setBodyinit();
//Crear formularios
$xHFrm	= new cHForm("frmTest", "./test.php");

$xHTxt	= new cHText("");
//$txt 	= $xHTxt->getDeMoneda("id", "Moneda de Prueba",  100);
$miFecha	= fechasys();
$xF			= new cFecha(0, $miFecha);

$runTest				= isset($_GET["run"]) ? true : false;
$persona_de_pruebas		= parametro("persona",99999, MQL_INT);
$credito_de_pruebas		= 29000201;

$xAML	= new cAMLPersonas($persona_de_pruebas);
$x2AML	= new cAMLPersonasPerfilTransaccional($persona_de_pruebas);
$xCon	= new cAMLListasProveedores();

$xCon->getConsultaGWS("enrique", "Pena", "Nieto");

$js = json_decode('{
  "spotlight": {
    "folio": "64564",
    "fecha_busqueda": "19 de Enero del 2016, 12:30 pm",
    "busqueda_peps": {
      "encontrados": {
        "pep": {
          "nombre": "Enrique",
          "apellido_paterno": "Peña",
          "apellido_materno": "Nieto",
          "clasificacion": "PEP",
          "cargo": "Presidente de los Estados Unidos Mexicanos",
          "ciudad": "Cd. de México",
          "institucion": "Presidencia de la República.",
          "observaciones": "Lugar y fecha de nacimiento: 20 de julio de 1966 en Atlacomulco, Estado de
México."
        }
      }
    }
  }
}', true);

if($runTest == true){
	
	$xCred		= new cCredito($credito_de_pruebas);
	$init		= true;
	$xCred->init();
	//Ministrar
	if($xCred->getEsAfectable() == false){
		$xCred->setForceMinistracion();
		$xCred->setMinistrar("", DEFAULT_CHEQUE, 0, DEFAULT_CUENTA_BANCARIA, 0, 0, "", '2014-01-01');
		$init		= $xCred->init();
		
	}
	if($init == true){
		$xRec	= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO, true);
		$xRec->setDocumento($xCred->getNumeroDeCredito());
		$xRec->setSocio($xCred->getClaveDePersona());
		$idrec	= $xRec->setNuevoRecibo($xCred->getClaveDePersona(), $xCred->getNumeroDeCredito(), fechasys(), 1);
		$xCred->setReciboDeOperacion($idrec);
		//agregar pagos
		$xCred->setAbonoCapital(2200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-01-15");
		//if($xCred->getORecibo() != null){ $xCred->getORecibo()->setFinalizarRecibo(true); }
		$xCred->setAbonoCapital(5200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-01-20");
		//if($xCred->getORecibo() != null){ $xCred->getORecibo()->setFinalizarRecibo(true); }
		$xCred->setAbonoCapital(6200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-01-30");
		//if($xCred->getORecibo() != null){ $xCred->getORecibo()->setFinalizarRecibo(true); }
		$xCred->setAbonoCapital(8200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-02-05");
		//if($xCred->getORecibo() != null){ $xCred->getORecibo()->setFinalizarRecibo(true); }
		$xCred->setAbonoCapital(9200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-02-06");
		$xCred->setAbonoCapital(9200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-02-10");
		//if($xCred->getORecibo() != null){ $xCred->getORecibo()->setFinalizarRecibo(true); }
		$xCred->setAbonoCapital(2200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-02-15");
		$xCred->setAbonoCapital(2200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-02-20");
		//if($xCred->getORecibo() != null){ $xCred->getORecibo()->setFinalizarRecibo(true); }
		$xCred->setAbonoCapital(4200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-02-25");
		$idrec	= $xCred->setAbonoCapital(4200, 1, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", DEFAULT_GRUPO, "2014-02-28");
		//if($xCred->getORecibo() != null){ $xCred->getORecibo()->setFinalizarRecibo(true); }
		
		$xRec->init();
		
		$xRec->setFinalizarRecibo(true);
		$xRec->setMoneda("USD");
		$xRec->setUnidadesOriginales(100);
		
		$x2Rec	= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_APORTACIONES, true);
		$x2Rec->setNuevoRecibo($xCred->getClaveDePersona(), 
				DEFAULT_CREDITO, "2013-01-01", 1);
		$x2Rec->setNuevoMvto("2013-01-01", 15000, OPERACION_CLAVE_APORT_CORRIENTE, 1, "Prueba de AML");
		$x2Rec->setNuevoMvto("2013-01-02", 1500, OPERACION_CLAVE_APORT_FONDO, 1, "Prueba de AML 2");
		$x2Rec->setNuevoMvto("2013-01-03", 45000, OPERACION_CLAVE_APORT_VOLUNTARIA, 1, "Prueba de AML 3");
		
		$x2Rec->setFinalizarRecibo(true);

		$xHFrm->addHElem( "<p class='aviso'>" . $x2Rec->getMessages(OUT_HTML)  ."</p>");
		
		$xAML->setGuardarPerfilTransaccional(101, "MX", 10000, 10, "");
		$xAML->setGuardarPerfilTransaccional(201, "MX", 20000, 2, "");
		$xAML->setGuardarPerfilTransaccional(301, "MX", 30000, 3, "");
		$xAML->setGuardarPerfilTransaccional(401, "MX", 40000, 4, "");
	}
	$xHFrm->addHElem( "<p class='aviso'>" . $xCred->getMessages(OUT_HTML)  ."</p>");
}




//$xHFrm->addHElem( $txt );

//$xHFrm->addHElem( $xF->show(true) );

//$xHFrm->addHElem( "<p class='aviso'>La $miFecha, Dias del Mes==". $xF->getDiasDelMes() ."</p>");
//$xAML->getSimilaresPorNombre("jose leonides", "cocom");
//$xAML->getSimilaresPorNombre("alberto", "chi");
$miFecha		= "2014-03-01";
//$xAML->getSimilaresPorNombre();
//$acum	= $xAML->getOAcumuladoDeOperaciones("2013-12-30", $miFecha, SYS_TODAS);
$xAML->setVerificarPerfilTransaccional($miFecha);
//$acum6	= $xAML->get
//$xHFrm->addHElem( "<p class='aviso'> Las operaciones Acumulados son de " . $acum->getNumero() . " </p>");

//$xHFrm->addHElem( "<p class='aviso'> El Monto Acumulado es de " . $acum->getMonto() . " </p>");

//$xHFrm->addHElem( "<p class='aviso'>" . $acum->getSQL()  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>PAGOS EN EFECTIVO: " . $x2AML->getPagosEnEfectivoNac(SYS_MONTO)  ."</p>");
$xHFrm->addHElem( "<p class='aviso'>NUMERO DE PAGOS EN EFECTIVO " . $x2AML->getPagosEnEfectivoNac(SYS_NUMERO)  ."</p>");

$xAML->getEsPersonaVigilada();
$xHFrm->addHElem( "<p class='aviso'>" . $x2AML->getMessages(OUT_HTML)  ."</p>");

$xHFrm->addHElem( "<p class='aviso'>" . $xAML->getMessages(OUT_HTML)  ."</p>");


$xRPT	= new cReportes_Layout();
$xRPT->setTipo( $xRPT->OPERACIONES_INUSUALES );
//var_dump( $xRPT->read() );
$datos		= $xRPT->read();
//var_dump($datos["contenido"]);

echo $xHFrm->get();

echo $xP->setBodyEnd();

echo $xP->end();
//=====================================================================================================

?> 
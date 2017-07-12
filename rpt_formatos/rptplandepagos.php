<?php
//=====================================================================================================
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP			= new cHPage("TR.Plan_de_Pagos", HP_REPORT);
$xRuls			= new cReglaDeNegocio();
$xF				= new cFecha();
$ql				= new MQL();

$oficial 		= elusuario($iduser);

$idrecibo 		= parametro("idrecibo", false, MQL_INT);
$idrecibo 		= parametro("recibo", $idrecibo, MQL_INT);

$idsolicitud	= parametro("is", false, MQL_INT);
$idsolicitud	= parametro("credito", $idsolicitud, MQL_INT);
$ShowAvales		= parametro("p", false, MQL_BOOL);

$formato		= parametro("forma", PLAN_DE_PAGOS_IDFORMA, MQL_INT);


$PlanSimple		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIMPLE);
$PlanFinalCap	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SDO_FCAP);

if($idrecibo <=0 AND $idsolicitud > DEFAULT_CREDITO ){
	if($idsolicitud > DEFAULT_CREDITO){
		$xCred			= new cCredito($idsolicitud); $xCred->init();
		$idrecibo		= $xCred->getNumeroDePlanDePagos();
		if(setNoMenorQueCero($idrecibo) > 0){
			
		} else {
			$xHP->goToPageX("../frmcreditos/plan_de_pagos.frm.php?credito=$idsolicitud&recibo=$idrecibo");
		}
	} else {
		$xHP->goToPageX("../frmcreditos/plan_de_pagos.frm.php?credito=$idsolicitud&recibo=$idrecibo");
	}
}
if($idrecibo >0 AND $idsolicitud <= DEFAULT_CREDITO){
	$xRec		= new cReciboDeOperacion(false, false, $idrecibo);
	if($xRec->init() == true){
		$idsolicitud	= $xRec->getCodigoDeDocumento();
	} else {
		$xHP->goToPageError(2011);
	}
}

$xHP->setTitle($xHP->getTitle() .  " # $idrecibo");

if($formato > 0){
	
	$xHP->init();
	$xForma						= new cFormato($formato);
	$xForma->setCredito($idsolicitud);
	
	$xForma->setProcesarVars();
	
	echo $xForma->get();	
	
	$xHP->fin();
	//============================================= Plan predeterminado
} else {
echo $xHP->getHeader(true);

echo $xHP->setBodyinit("window.print()");
echo $xHP->getEncabezado();

$PlanBody			= $xHP->h1() . "<hr />";

$xRec				= new cReciboDeOperacion(false, false, $idrecibo); $xRec->init();
$xSoc				= $xRec->getSocio(); $xSoc->init();
$xCred				= $xRec->getCredito(); $xCred->init();
$xF					= new cFecha();

$idsocio 			= $xSoc->getCodigo(); //"numero_socio"
$idsolicitud 		= $xRec->getCodigoDeDocumento(); // docto_afectado
$nombre 			= $xSoc->getNombreCompleto();
// ------------------------------------ DATOS DE LA SOLICITUD.
$tasa_ahorro 		= $xCred->getTasaDeAhorro() * 100;
$tasa_interes 		= $xCred->getTasaDeInteres() * 100;
$dias_totales 		= $xCred->getDiasAutorizados();
$numero_pagos		= $xCred->getPagosAutorizados();
$nombre_otro		= "";
$observaciones		= $xRec->getObservaciones();
$ODOProd			= $xCred->getOProductoDeCredito()->getOOtrosParametros();
if($ODOProd->get($ODOProd->PLAN_PAGOS_SIMPLE) !== null){
	$xT				= new cTipos();
	$PlanSimple		= $xT->cBool($ODOProd->get($ODOProd->PLAN_PAGOS_SIMPLE));
}
$extTool			= "";

	echo $xSoc->getFicha();
	echo $xCred->getFicha(true, "", false);

	$pagoactual		= $xCred->getPeriodoActual();
	
	if ($ShowAvales == true){
		$avals		= $xCred->getAvales_InText();
		echo $avals;
	}
	$xPlan			= new cPlanDePagos($idrecibo);
	if($xPlan->init() == true){
		if($xCred->getEsArrendamientoPuro() == true){
			echo $xPlan->getVersionImpresaLeasing();
		} else {
			echo $xPlan->getVersionImpresa(false, false, $PlanSimple, false, true, $PlanFinalCap);
		}
	}
//------------------------------------- DATOS DEL RECIBO

$PlanBody = "
	<table >
	<tr>
	<td><center>" . $xHP->lang("firma del", "solicitante") . "</td>
	<td><center>" . $xHP->lang("por la", "empresa") . "</center></td>
	</tr>
	<tr>
	<td>
		<br />
		<br />
		<br />
	</td>
	</tr>
	<tr>
	<td><center>$nombre</center></td>
	<td><center>$oficial</center></td>
	</tr>
	<tr>
		<th>" . $xHP->lang("observaciones") . "</th><td>$observaciones</td>
	</tr>
	</table>";

	echo $PlanBody;

echo getRawFooter();
$xHP->fin();
}

?>

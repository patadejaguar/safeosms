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
$xHP			= new cHPage("TR.Cedula de pagos por Empresa", HP_REPORT);
$xF				= new cFecha();
$Fenv			= new cFecha();
$xL				= new cSQLListas();
	
	
$empresa		= (isset($_GET["r"])) ? $_GET["r"] :  0;
$periocidad		= (isset($_GET["p"])) ? $_GET["p"] :  SYS_TODAS;

$variacion		= (isset($_GET["v"])) ? $_GET["v"] :  0;
$out			= parametro("out", OUT_HTML);
$periodo		= (isset($_GET["periodo"])) ? $_GET["periodo"] :  0;

$fechaInicial	= parametro("on", false);
$fechaFinal		= parametro("off", false);
$fechaFinal		= $xF->getFechaISO($fechaFinal);
$fechaInicial	= $xF->getFechaISO($fechaInicial);
$idnomina		= parametro("nomina", 0, MQL_INT);

$mails			= getEmails($_REQUEST);

$FAnt			= new cFecha();
$xRPT			= new cReportes("");

if($periocidad == SYS_TODAS){
	$xPerNom	= new cEmpresas_operaciones();
	$xPerNom->setData( $xPerNom->query()->initByID($idnomina) );
	$empresa	= $xPerNom->clave_de_empresa()->v();
	$periocidad	= $xPerNom->periocidad()->v();
	$periodo	= $xPerNom->periodo_marcado()->v();
}


    
$ByMinistracion	= "";

//$periodo		= $periodo + $variacion;
$observaciones	= (isset($_GET["o"])) ? $_GET["o"] :  0;
$xLoc			= new cLocal();



$xHP->addJsFile("../js/jquery/jquery.js");
$xHP->addJsFile("../js/general.js");

$xEmp			= new cEmpresas($empresa); $xEmp->init();
$xTPer			= new cPeriocidadDePago($periocidad); $xTPer->init();

if($xEmp->getEsPeriodoCerrado($periocidad, $periodo, $idnomina) == false){	$xHP->goToPageError(20101, $out); }
$periodo		= $xF->semana();
$bheader		= "";
$title			= $xHP->getTitle() . "_" . $xEmp->getNombre() . "_" . $xTPer->getNombre() . "_$periodo";
$xPer			= $xEmp->getOPeriodo(false, false, $idnomina);

$xRPT->setTitle($title);
$xRPT->setOut($out);
$xRPT->setSenders($mails);
$xRPT->setResponse();

	//if($out == OUT_EXCEL ){
		//$xRPT->setOut($out);
	//} else {
		  // $xLoc->DomicilioLocalidad() . "," . $xLoc->DomicilioEstado() . "," .
    	$xFMT			= new cFormato( $xEmp->getIDDeFormatoDeAviso() );
    	$xFMT->setEmpresaPeriodo($empresa, $idnomina);
		$xFMT->setProcesarVars();
		//$xFMT->setOut($out);
	   	$xRPT->addContent( $xFMT->get() );
    	$xRPT->addContent( "<hr />" );
    	
	//}
	$xRPT->setBodyMail($bheader);
   
    //filtrar domicilio -> socio -> credito -> letra
    $sql	= $xL->getListadoDeCobranza($idnomina);
    $xRPT->setSQL($sql);
        
    //exit($sql);
     $xT	= new cTabla($sql);
     
     $xT->setTipoSalida($out);
     $xT->setRowCSS("monto", "mnyres");
     $xT->setColTitle("monto", "Monto de Retencion");
     $xT->setKeyField("numero_solicitud");
     $xT->setKeyTable("creditos_solicitud");
     $xT->setTdClassByType();
     $xT->setFootSum(array(
							3 => "letra",
						   6 => "monto"
						   ));
     //$xT->getFieldsSum()
     $xRPT->addContent( $xT->Show() );
     //=================== Agregar Pie de Formato ================
     $xFMT			= new cFormato();
     //$xFMT->setOut($out);
     $xFMT->init($xFMT->FMT_NOMINA_ENVP);
     
     $xFMT->setEmpresaPeriodo($empresa, $idnomina);
     $xFMT->setProcesarVars();

     $xRPT->addContent( "<hr />" );
     $xRPT->addContent( $xFMT->get() );
     
     //===========================================================
    echo  $xRPT->render(true);

?>
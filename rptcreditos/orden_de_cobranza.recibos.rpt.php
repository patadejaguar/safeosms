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
$xHP		= new cHPage("TR.Recibos de Cobranza", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();

$empresa	= (isset($_GET["r"])) ? $_GET["r"] :  0;
$periocidad	= (isset($_GET["p"])) ? $_GET["p"] :  "todos";
$variacion	= (isset($_GET["v"])) ? $_GET["v"] :  0;
$ByPeriodo	= ($periocidad == "todos") ? "" : " AND creditos_solicitud.periocidad_de_pago = $periocidad ";

$periodo	= (isset($_GET["periodo"])) ? $_GET["periodo"] :  0;
$out		= parametro("out", OUT_HTML);

$xRPT		= new cReportes($xHP->getTitle());
    
$fecha_filtro	= fechasys();
$ByMinistracion	= "";
//$periodo		= $periodo + $variacion;

$fechaInicial	= parametro("on", false);
$fechaFinal		= parametro("off", false);
$fechaFinal		= $xF->getFechaISO($fechaFinal);
$fechaInicial	= $xF->getFechaISO($fechaInicial);

$idnomina		= parametro("nomina", 0, MQL_INT);

//$xHP->addJsFile("../js/jquery/jquery.js");
//$xHP->addJsFile("../js/general.js");
//$xHP->addJsFile("../js/jquery/jquery.qtip.min.js");

$xEmp	= new cEmpresas($empresa); $xEmp->init();
$xTPer	= new cPeriocidadDePago($periocidad); $xTPer->init();

//if($xEmp->getEsPeriodoCerrado($periocidad, $periodo) == false){	$xHP->goToPageError(20101); }


//$xHP->addStyle("");
$style	= " body { margin-top:0; margin-bottom:0.5in; margin-left:0.5in; margin-right:0.5in; 
		font: 8pt \"Trebuchet MS\", Arial, Helvetica, sans-serif !important; font-stretch: extra-condensed;	text-transform: uppercase; }
#ticket { height: 3.8in; } .npage { page-break-after: always;} .divisormedio { margin-bottom: 1.25in; border-style: dotted;  border-color: transparent; }   ";


$xRPT->setToPrint();
$xRPT-> addHeaderCNT("<style>$style</style>");
if($out == OUT_DOC){
	$xRPT->addHeaderCNT("<style>h1,h2,h3,h4 {text-align:center; font-size: 12pt; } body{ line-height: 10pt; }</style>");
}
$xRPT->setOut($out);

    //filtrar domicilio -> socio -> credito -> letra
	$sql			= $xL->getListadoDeCobranza($idnomina); 
    $base_contrato	= contrato(401, "texto_del_contrato");
     $Dep			= new cEmpresas($empresa); $Dep->init();
     
     $nempresa		= $Dep->getNombre();
     $TCobros		= 0;

    $rs						= getRecordset($sql);
    $ppn					= 1;
    $contar					= 1;
    while($rw = mysql_fetch_array($rs)){
	//cargar datos de la parcialidad
	$numero_de_socio		= $rw["persona"];
	$cSoc 					= new cSocio($numero_de_socio); $cSoc->init();
	$DSoc					= $cSoc->getDatosInArray();
	$ficha_socio			= $cSoc->getFicha();
	
	$numero_credito			= $rw["credito"];
	$xCred					= new cCredito($numero_credito, $numero_de_socio); $xCred->init();
	$DCred					= $xCred->getDatosInArray();
	
	$svar_info_cred			= $xCred->getShortDescription();
	$variable_lugar			= "";
	$monto_inicial			= 0;
	$monto_inicial_letras		= 0;
	$numero_dias			= 0;
	$variable_fecha_vencimiento	= "";
	$nombre_mancomunados		= "";
	$variable_tasa_otorgada		= "";
	$monto_parcialidad		= $rw["monto"];
	$TCobros			+= $monto_parcialidad;
	$numero_parcialidad		= (setNoMenorQueCero($rw["letra"]) == 0) ? 1 : $rw["letra"];
	$numero_de_pagos		= $rw["pagos"];//$DCred[""];
	$periocidad_titulo		= $xTPer->getNombre();
	
	$tipo_de_credito			= "";
	$monto_ministrado			= "";
	$DOficial					= $xCred->getDatosOficialDeCredito_InArray();
	$oficial					= $DOficial["nombre_completo"];
	$variable_oficial			= $DOficial["nombre_completo"];
	
	$fecha_de_vencimiento		= "";
	$tasa_interes_mensual_ordinario	= "";
	$tasa_garantia_liquida		= "";
	$tasa_interes_mensual_moratorio	= 0;
	$fecha_de_ministracion		= "";
	
	//cargar Datos de la Empresa
	
 	$vars = array(
	    "variable_nombre_del_socio" 		=> $cSoc->getNombreCompleto() ,
	    "variable_nombre_de_la_sociedad" 		=> EACP_NAME,
	    "variable_nombre_de_la_entidad" 		=> EACP_NAME,
	    "variable_domicilio_del_socio" 		=> trim( substr($cSoc->getDomicilio(), 0, 60) ),
	    "variable_documento_de_constitucion_de_la_sociedad" => EACP_DOCTO_CONSTITUCION,
	    "variable_rfc_de_la_entidad" 		=> EACP_RFC,
	    "variable_rfc_del_socio" 			=> $DSoc["rfc"],
	    "variable_curp_del_socio" 			=> $DSoc["curp"],
	    "variable_nombre_del_representante_legal_de_la_sociedad" => EACP_REP_LEGAL,
	    "variable_informacion_del_credito" 		=> $svar_info_cred,
	    "variable_domicilio_de_la_entidad" 		=> EACP_DOMICILIO_CORTO,
	    "variable_acta_notarial_de_poder_al_representante" => EACP_DOCTO_REP_LEGAL,

	    "variable_numero_de_socio" 			=> $numero_de_socio,

	    "variable_tipo_de_credito" 			=> $tipo_de_credito,
	    "variable_monto_ministrado" 		=> getFMoney($monto_ministrado),
	    "variable_tasa_mensual_de_interes_ordinario" => $tasa_interes_mensual_ordinario,
	    "variable_credito_fecha_de_vencimiento" 	=> getFechaLarga($fecha_de_vencimiento),
	    "variable_tasa_mensual_de_interes_moratorio" => $tasa_interes_mensual_moratorio . " %",
	    "variable_tasa_de_garantia_liquida" 	=> $tasa_garantia_liquida . " %",
	    "variable_horario_de_trabajo_de_la_entidad"	=> EACP_HORARIO_DE_TRABAJO,
	    "variable_testigo_del_acto" 		=> $oficial,
	    "variable_fecha_larga_actual" 		=> fecha_larga(),
	    "variable_nombre_de_presidente_de_vigilancia_de_la_entidad"=>EACP_PDTE_VIGILANCIA,
	    "variable_en_letras_monto_ministrado" 	=> convertirletras($monto_ministrado),
	    "variable_credito_fecha_de_ministracion" 	=> getFechaLarga($fecha_de_ministracion),
	    "variable_informacion_del_socio" 		=> $ficha_socio,
	    "variable_oficial" 				=> $variable_oficial,
	    "variable_lugar" 				=> $variable_lugar,
	    "variable_lugar_actual" 			=> $variable_lugar,
	    "variable_monto_inicial_en_numero" 		=> $monto_inicial,
	    "variable_monto_inicial_en_letras" 		=> $monto_inicial_letras,
	    "variable_numero_de_dias" 			=> $numero_dias,
	    "variable_fecha_de_vencimiento" 		=> $variable_fecha_vencimiento,
	    "variable_tasa_otorgada" 			=> $variable_tasa_otorgada,
	    
	    "variable_nombre_empresa" 			=> $nempresa,
	    "variable_numero_de_pagos" 			=> $numero_de_pagos,
	    "variable_numero_parcialidad_actual" 	=> $numero_parcialidad,
	    "variable_monto_parcialidad" 		=> getFMoney($monto_parcialidad),
	    "variable_monto_letras_parcialidad" 	=> convertirletras($monto_parcialidad),
	    "variable_encabezado_de_reporte" 		=> getRawHeader(false, $out),
	    "variable_pie_de_reporte" 				=> getRawFooter(false, $out),
	    "variable_paginas" 						=> $contar
	);
 	if($out == OUT_DOC){
 		$vars["<hr />"]	= "";	
 	} 	
	$texto_contrato = $base_contrato;
	foreach ($vars as $key => $value) {
		$texto_contrato = str_replace($key, $value, $texto_contrato);
	}
	if($monto_parcialidad > 0){
		$xRPT->addContent( $texto_contrato);
		$ppn++;
		if($ppn == 2){
			if($out == OUT_DOC){
				
			} else {
		    	$xRPT->addContent("<hr class='divisormedio' />\n");
			}
		}
		if($ppn > 2){
			if($out == OUT_DOC){
				$xRPT->addContent("<br clear=all style='mso-special-character:line-break;page-break-before:always'>");
			} else {
		    	$xRPT->addContent("\n\n<br class='npage' />\n\n");
			}
		    $ppn=1;
		}
	}
	$contar++;
    }
if($out == OUT_DOC){
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=document_name.doc");	
	
}
   echo $xRPT->render(false);
   
?>
<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP		= new cHPage("TR.VISTA_PREVIA DEL REPORTE DE OPERACIONES_RELEVANTES", HP_REPORT);
$mql		= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xT			= new cTipos();
$xLoc		= new cLocal();
$xLayout	= new cReportes_Layout();
$xHTable	= new cHTabla();
$xHNot		= new cHNotif();


$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);

$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("off", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$xFTrim			= $xF->getOTrimestre($FechaFinal);
$FechaInicial	= $xFTrim->getFechaInicial();
$FechaFinal		= $xFTrim->getFechaFinal();

$definitivo		= parametro("definitivo", false, MQL_BOOL);
$preguntar		= parametro("pregunta", false, MQL_BOOL);

$xLayout->setTipo( $xLayout->OPERACIONES_RELEVANTES);
$datos					= $xLayout->read();
$Estructura				= $xLayout->getContent();
$FechaExtraccion		= $FechaFinal;
$FechaTitulo			= date("ym", strtotime($FechaFinal));
$casfin					= EACP_CLAVE_CASFIN;
$delimitador			= $xLayout->getSeparador();
$msg					= "";
$production				= ($out == OUT_TXT OR $out == OUT_CSV) ? true : false; 
$supervisor				= 2;			//supervisor 002 CNBV y su casfin 1002
$sql		= "SELECT 
	`aml_risk_register`.`persona_relacionada` 		AS 'persona',
	`aml_risk_register`.`fecha_de_reporte` 			AS 'fecha',
	`aml_risk_register`.`instrumento_financiero` 	AS 'instrumento',
	`aml_risk_register`.`documento_relacionado` 	AS `recibo` 
FROM
	`aml_risk_catalog` `aml_risk_catalog` 
		INNER JOIN `aml_risk_register` `aml_risk_register` 
		ON `aml_risk_catalog`.`clave_de_control` = `aml_risk_register`.
		`tipo_de_riesgo` 
WHERE
	`aml_risk_catalog`.`tipo_de_riesgo` = " . AML_CLAVE_OPERACIONES_RELEVANTES . "
	AND (
	getFechaByInt(`aml_risk_register`.`fecha_de_reporte`) >='$FechaInicial' 
	AND 
	getFechaByInt(`aml_risk_register`.`fecha_de_reporte`) <='$FechaFinal'
	) 
	AND (`aml_risk_register`.`estado_de_envio` =0)

";


$xTBL			= new cTabla($sql);
$rpt			= "";
$rs				= $query->getDataRecord($sql);
$xRisk			= new cAml_risk_register();
$xEquivOps		= new cSistemaEquivalencias(TOPERACIONES_RECIBOSTIPOS);
$xEquivOps->init($xEquivOps->PLD_OPERACIONES);
$xEquivInst		= new cSistemaEquivalencias(TTESORERIA_TIPOS_DE_PAGO);
$xEquivInst->init($xEquivInst->PLD_OPERACIONES);
//$xEquivIns		
$operaciones	= 0;
$totalerrores	= 0;

foreach($rs as $data){
	$persona	= $data["persona"];
	$idrecibo	= $data["recibo"];
	$fechaDetec	= $xF->getFechaByInt( $data["fecha"]);
	//obtener listado de operaciones en el mes
	$xAML		= new cAMLPersonas($persona);
	$xSoc		= new cSocio($persona, true);
	
	$xSoc->init();
	
	$ODom		= $xSoc->getODomicilio();
	$OAEc		= $xSoc->getOActividadEconomica();

	$tp			= ($xSoc->getEsPersonaFisica() == true) ? 1 : 2;
	$nac		= ($xSoc->getPaisDeOrigen() == EACP_CLAVE_DE_PAIS) ? 1 : 2;

	$sqlR		= "SELECT `operaciones_recibos`.* FROM	`operaciones_recibos` WHERE `idoperaciones_recibos`=$idrecibo ";

	//setLog($sqlR);

	$rs1		= $query->getDataRecord($sqlR);
	$orels		= 0;

	foreach($rs1 as $datos){
		$recibo		= $datos["idoperaciones_recibos"];
		$cont		= array();
		$linea		= "";
			
		$cont[1]	= $xLayout->getClave();
		$cont[2]	= $FechaExtraccion;
			
		$cont[3]	= ($operaciones == 0) ? 1 : $operaciones;
		
		$cont[4]	= "01" . $xT->cSerial(3, $supervisor); //clave nacional de entidad supervisora 1002 = CNBV
			
		$cont[5]	= $casfin;
			
		$cont[6]	= $xLoc->DomicilioLocalidadClave();// cambiar por la UIF
		$cont[7]	= $xLoc->DomicilioCodigoPostal();// CP de la sucursal
			

		$xRec		= new cReciboDeOperacion(false, false, $recibo);
		$xRec->init();
		$docto_relacionado	= ($xRec->getCodigoDeDocumento() == DEFAULT_CREDITO) ? $xRec->getCodigoDeRecibo() : $xRec->getCodigoDeDocumento();

		$cont[8]	= $xEquivOps->get($xRec->getTipoDeRecibo());// Tipo de Operacion 01 deposito 02 retiro 03 compra divisas 04 venta divisas
		$cont[9]	= $xEquivInst->get($xRec->getTipoDePago()) ; //TODO: Instrumento monetario		
		
		$cont[10]	= $docto_relacionado;
		$cont[11]	= $xRec->getTotal();
		$cont[12]	= $xRec->getMoneda();
		$cont[13]	= $xRec->getFechaDeRecibo();
		$cont[14]	= "";//$fechaDetec; inusuales
			
		$cont[15]	= $nac;
		$cont[16]	= $tp;
			
		$nombresujeto	= $xT->getCSV($xSoc->getNombre());
			
		$cont[17]	= ($tp == SYS_UNO) ? "" : $nombresujeto;
			
			
		$cont[18]	= ($tp == SYS_UNO) ? $nombresujeto : "";
		$cont[19]	= ($tp == SYS_UNO) ? $xT->getCSV($xSoc->getApellidoPaterno()) : "";
		$cont[20]	= ($tp == SYS_UNO) ? $xT->getCSV($xSoc->getApellidoMaterno()) : "";
			
		$cont[21]	= $xSoc->getRFC(true);
		$cont[22]	= ($tp == SYS_UNO) ? $xSoc->getCURP() : "";
			
		$cont[23]	= $xSoc->getFechaDeNacimiento();
		if($ODom == null){
			$cont[24]	= "";
			$cont[25]	= "";
			$cont[26]	= $xLoc->DomicilioLocalidadClave();
			$cont[27]	= "";

		} else {//10500001
			$idlocalidad	= $ODom->getClaveDeLocalidad();

			//$cont[24]	= ($orels == 0) ? strtoupper($xT->cChar($ODom->getCalleConNumero(), 59)) : "";
			//$cont[25]	= ($orels == 0) ? $xT->cChar($ODom->getColonia(), 29) : "";
			//$cont[26]	= ($orels == 0) ? $xT->cSerial(8, $idlocalidad) : "";
			$cont[24]	= strtoupper($xT->cChar($ODom->getCalleConNumero(), 59));
			$cont[25]	= $xT->cChar($ODom->getColonia(), 29);
			$cont[26]	= $idlocalidad;
			//validar la clave de localidad, si no emitir la actual

			$xMLocal	= new cDomicilioLocalidad($idlocalidad);
			//Existe?
			if($xMLocal->existe($ODom->getClaveDeLocalidad($idlocalidad)) == false){ $cont[26]	= $xLoc->DomicilioLocalidadClave(); }
			//$cont[27]	= ($orels == 0) ? $xSoc->getTelefonoPrincipal() : "";

			$cont[27]	= $xSoc->getTelefonoPrincipal();
		}
		if($OAEc == null){
			$cont[28]	= 0;//"8944098";
		} else {
			$xCatAct	= new cPersonaActividadEconomicaCatalogo($OAEc->getClaveDeActividad());
			$clave_uif	= $xCatAct->getCodigoUIF();
			$cont[28]	= $clave_uif;

		}
			
		$cont[29]		=  "";
		$cont[30]		=  "";
		$cont[31]		=  "";
			
		$cont[32]		=  "";
		$cont[33]		=  "";
		$cont[34]		=  "";
		$cont[35]		=  "";
		$cont[36]		=  "";
			
			
		//acomodar el array
		//asort($cont);
		
		//evaluar
		$conteo			= 1;
		$errors			= 0;
		foreach ($Estructura as $indice => $propiedades){
			//var_dump($propiedades);
			if(isset($cont[$conteo])){
				//setLog("$conteo--" . $cont[$conteo]);
				$xCampo			= new cReportes_LayoutTipos($propiedades, $cont[$conteo], $xLayout->getSeparador());			
				$msg			.= ($preguntar == false) ? $xCampo->getMessages(OUT_HTML) : $xCampo->getMessages(OUT_TXT);
				$cont[$conteo]	= $xCampo->get();
				$errors			+= $xCampo->ERRORES;
				$totalerrores	+= $errors;
				
					//setLog($xCampo->getMessages());
					//setLog($propiedades);
				
			} else {
				$errors++;
				$totalerrores++;
				$msg			.= "ERROR\tColumna $conteo NO existe \r\n";
			}
			$conteo++;
		}		
		
		
		foreach($cont as $t => $k){ $linea	.= ($linea == "") ? $k : $delimitador . $k; }
		$rpt		.= $linea . "$delimitador\r\n";
		$css		= ($errors > 0) ? " class='error'" : "";
		$xHTable->addRow($cont, "td", $css);

		$orels++;
		$operaciones++;
	}
}
$errors		= ($msg == "") ? false : true;
if($preguntar == true){
	header('Content-type: application/json');
	$arr	= array(
		"mensajes" => $msg,
		"registros" => $operaciones,
		"errores"	=>$totalerrores
	);
	echo json_encode($arr);
	
} else {

	if(($production == true) AND $errors == false AND $operaciones > 0){
		$archivo		= $xLayout->getClave() . $xT->cSerial(6, $casfin) . $FechaTitulo . "."  . $xT->cSerial(3, $supervisor);
		//header("Content-type: text/x-csv");
		header("Content-type: text/plain");
		//header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=$archivo" );
		echo $rpt;
		if($definitivo == true){
			//Guardar
			$sqlUpdate = " UPDATE `aml_risk_register` SET `estado_de_envio` = 1, `fecha_de_envio`= " . $xF->getInt() . "
			WHERE (SELECT `tipo_de_riesgo` FROM `aml_risk_catalog` WHERE 
			`clave_de_control`= `aml_risk_register`.`tipo_de_riesgo`= " . AML_CLAVE_OPERACIONES_RELEVANTES. "
			AND (getFechaByInt(`aml_risk_register`.`fecha_de_reporte`) <='$FechaFinal')
			AND (`aml_risk_register`.`estado_de_envio` =0)";
			$query->setRawQuery($sqlUpdate);
			//enviar por mail al administrador
			$xLog			= new cFileLog($archivo, true);
			$xLog->setWrite($rpt);
			$xLog->setSendToMail($xHP->getTitle(), ARCHIVO_MAIL);
		}
	} else {
		$xRPT			= new cReportes();
		$xRPT->setOut(OUT_HTML);
		$xRPT->addContent( $xRPT->getHInicial($xHP->getTitle(), $FechaInicial, $FechaFinal) );
		$arrTitulos			= array();
		foreach ($Estructura as $indice => $propiedades){
			$xCampo			= new cReportes_LayoutTipos($propiedades);
			$arrTitulos[]	= $xCampo->getNombre();
		}
		$xHTable->addTitles($arrTitulos);
		
		if($msg != ""){ $xRPT->addFooterBar("<h3>El reporte contiene los siguientes errores</h3>" . $msg); }
		
		$xRPT->addContent( $xHTable->get() );
		$xRPT->setToPrint();
		echo $xRPT->render(true);
	}

}
?>
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
$xHP		= new cHPage("TR.VISTA_PREVIA DEL REPORTE DE OPERACIONES PREOCUPANTES", HP_REPORT);
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

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-1", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("off", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

$definitivo		= parametro("definitivo", false, MQL_BOOL);
$preguntar		= parametro("pregunta", false, MQL_BOOL);

$xLayout->setTipo( $xLayout->OPERACIONES_INTERNAS);
$datos					= $xLayout->read();
$Estructura				= $xLayout->getContent();
$production				= ($out == OUT_TXT OR $out == OUT_CSV) ? true : false;

$FechaExtraccion		= $FechaFinal;
$FechaTitulo			= date("ymd", strtotime($FechaFinal));
$casfin					= EACP_CLAVE_CASFIN;
$delimitador			= $xLayout->getSeparador();
$msg					= "";
$supervisor				= 2;

//$xCatalogoActividades	= new cPersonaActividadEconomica();

$sql		= "SELECT
		*
FROM
	`aml_risk_catalog` `aml_risk_catalog`
		INNER JOIN `aml_risk_register` `aml_risk_register`
		ON `aml_risk_catalog`.`clave_de_control` = `aml_risk_register`.
		`tipo_de_riesgo`
WHERE
	`aml_risk_catalog`.`tipo_de_riesgo` = " . AML_CLAVE_OPERACIONES_INTERNAS . "
	AND (getFechaByInt(`aml_risk_register`.`fecha_de_reporte`) <='$FechaFinal')
	AND (`aml_risk_register`.`estado_de_envio` =0)
	ORDER BY `aml_risk_register`.`fecha_de_reporte`
";

//setLog($sql);

$xTBL			= new cTabla($sql);
$rpt			= "";
$rs				= $query->getDataRecord($sql);

$xEquivOps		= new cSistemaEquivalencias(TOPERACIONES_RECIBOSTIPOS);
$xEquivOps->init($xEquivOps->PLD_OPERACIONES);
$xEquivInst		= new cSistemaEquivalencias(TTESORERIA_TIPOS_DE_PAGO);
$xEquivInst->init($xEquivInst->PLD_OPERACIONES);
//$xEquivIns		
$operaciones	= 0;
$totalerrores	= 0;

foreach($rs as $data){
	$xRisk			= new cAml_risk_register();
	$xRisk->setData($data);
	
	$persona		= $xRisk->persona_relacionada()->v();// $data["persona"];
	$fechaDetec		= $xF->getFechaByInt( $xRisk->fecha_de_reporte()->v() );//$data["fecha"]);
	//$tipo_de_riesgo	=  $data["tipo_de_riesgo"];
	//obtener listado de operaciones en el mes
	$xAML			= new cAMLPersonas($persona);
	$xSoc			= new cSocio($persona, true);
	$ODom			= $xSoc->getODomicilio();
	$OAEc			= $xSoc->getOActividadEconomica();

	$tp				= ($xSoc->getEsPersonaFisica() == true) ? 1 : 2;
	$nac			= ($xSoc->getPaisDeOrigen() == EACP_CLAVE_DE_PAIS) ? 1 : 2;
	$detalles		= $xRisk->notas_de_checking()->v(OUT_TXT);
	$razones		= $xRisk->razones_de_reporte()->v(OUT_TXT);
	$tipo_de_obj	= $xRisk->tipo_de_documento()->v();
	$tercero		= $xRisk->tercero_relacionado()->v();
	//obtiene las razones de cada operacion presente.

		$recibo		= $xRisk->documento_relacionado()->v();
		$cont		= array();
		$linea		= "";
			
		$cont[1]	= $xLayout->getClave();
		$cont[2]	= $FechaExtraccion;
			
		$cont[3]	= ($operaciones == 0) ? 1 : $operaciones;
		
		$cont[4]	= "01" . $xT->cSerial(3, $supervisor); //clave nacional de entidad supervisora 1002 = CNBV
			
		$cont[5]	= $casfin;
			
		$cont[6]	= $xLoc->DomicilioLocalidadClave();// cambiar por la UIF
		$cont[7]	= $xLoc->DomicilioCodigoPostal();// CP de la sucursal
			


		$docto_relacionado	= ($OAEc == null) ? $OAEc->getNumeroDeSeguridadSocial() : $xRisk->documento_relacionado()->v();
		//($xRec->getCodigoDeDocumento() == DEFAULT_CREDITO) ? $xRec->getCodigoDeRecibo() : $xRec->getCodigoDeDocumento();
	
		$fecha_de_op= $xF->getFechaByInt( $xRisk->fecha_de_reporte()->v() );
		$instrumento= $xRisk->instrumento_financiero()->v();
		$tipo_de_op	= $xRisk->tipo_de_operacion()->v();
		$total		= $xRisk->monto_total_relacionado()->v();
		$moneda		= AML_CLAVE_MONEDA_LOCAL;
		//TODO: Agregar soporte para captación y colocación
		switch ($tipo_de_obj){
			case iDE_RECIBO:
				$xRec				= new cReciboDeOperacion(false, false, $recibo);
				if( $xRec->init() == true){
					if($xRec->getCodigoDeSocio() == $tercero){
						$tipo_de_op			= $xEquivOps->get($xRec->getTipoDeRecibo());
						$instrumento		= $xEquivInst->get($xRec->getTipoDePago());
						$moneda				= $xRec->getMoneda();
						$total				= $xRec->getTotal();						
					} else {
						$msg			.= "ERROR\tLa persona $tercero no es misma del recibo $recibo- " . $xRec->getCodigoDeSocio() . "\r\n";
						$totalerrores++;
					}
				} else {
					$msg			.= "ERROR\tRecibo no existe $recibo\r\n";
					$totalerrores++;
				}
				break;
			case iDE_CREDITO:
				break;
			case iDE_CAPTACION:
				break;
		}
		
		$cont[8]	= $tipo_de_op;// // Tipo de Operacion 01 deposito 02 retiro 03 compra divisas 04 venta divisas
		$cont[9]	= $instrumento; // Instrumento monetario		
		
		
		$cont[10]	= $docto_relacionado;
		$cont[11]	= $total;//
		$cont[12]	= $moneda;// 
		$cont[13]	= $fecha_de_op;// $xRec->getFechaDeRecibo();
		$cont[14]	= $fechaDetec; //inusuales internas
			
		$cont[15]	= $nac;
		$cont[16]	= $tp;
			
		$nombresujeto	= $xT->getCSV($xSoc->getNombre());
			
		$cont[17]	= ($tp == SYS_UNO) ? "" : $nombresujeto;
			
			
		$cont[18]	= ($tp == SYS_UNO) ? $nombresujeto : "";
		$cont[19]	= ($tp == SYS_UNO) ? $xT->getCSV($xSoc->getApellidoPaterno()) : "";
		$cont[20]	= ($tp == SYS_UNO) ? $xT->getCSV($xSoc->getApellidoMaterno()) : "";
			
		$cont[21]	= $xSoc->getRFC(true);
		$cont[22]	= ($tp == SYS_UNO) ? $xSoc->getCURP(true) : "";
			
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
			//TODO: Existe?
			if($xMLocal->existe($ODom->getClaveDeLocalidad($idlocalidad)) == false){ $cont[26]	= $xLoc->DomicilioLocalidadClave(); }
			//$cont[27]	= ($orels == 0) ? $xSoc->getTelefonoPrincipal() : "";

			$cont[27]	= $xSoc->getTelefonoPrincipal();
		}
		if($OAEc == null){
			$cont[28]	= 0;// "8944098";
		} else {
			$xCatAct	= new cPersonaActividadEconomicaCatalogo($OAEc->getClaveDeActividad());
			$clave_uif	= $xCatAct->getCodigoUIF();
			$cont[28]	= $clave_uif;

		}
			
		
		$cont[29]		=  ""; //cuentas y o personas relacionadas
		$cont[30]		=  "";
		$cont[31]		=  "";
		$cont[32]		=  "";
		$cont[33]		=  "";
		$cont[34]		=  "";
		$cont[35]		=  $detalles; //detalles de la operacion
		$cont[36]		=  $razones;
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
				//setLog($conteo . "---" . $xCampo->get());
				$errors			+= $xCampo->ERRORES;
				$totalerrores	+= $errors;
			}
			$conteo++;
		}		
		

		foreach($cont as $t => $k){ $linea	.= ($linea == "") ? $k : $delimitador . $k; }
		$rpt		.= $linea . "$delimitador\r\n";
		$css		= ($errors > 0) ? " class='error'" : "";
		$xHTable->addRow($cont, "td", $css);
		
		//$orels++;
		$operaciones++;

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
			`clave_de_control`= `aml_risk_register`.`tipo_de_riesgo`= " . AML_CLAVE_OPERACIONES_INTERNAS . "
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
<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
 */
//ini_set("display_errors", "1");
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
$xHP		= new cHPage("TR.REPORTE VINTAGE", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();
$xLayout	= new cReportes_Layout();
	
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$TipoDePago		= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW); $TipoDePago	= parametro("pago", $TipoDePago, MQL_RAW);
$TipoDeRecibo	= parametro("tipoderecibo", 0, MQL_INT); $TipoDeRecibo = parametro("tiporecibo", $TipoDeRecibo, MQL_INT);

$cajero 		= parametro("f3", 0, MQL_INT); $cajero = parametro("cajero", $cajero, MQL_INT); $cajero = parametro("usuarios", $cajero, MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT); $operacion = parametro("tipodeoperacion", $operacion, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT * FROM creditos LIMIT 0,100";
$titulo			= $xHP->getTitle();
$archivo		= $xHP->getTitle();

$xQL->setCall("proc_creditos_abonos_parciales");
$xQL->setCall("proc_recibos_distrib");

$idfrecuencia	= $frecuencia;
$idfecha		= $FechaFinal;

$xF		= new cFecha();
$xFreq	= new cPeriocidadDePago($idfrecuencia);
$xFreq->init();
$IDX	= $xFreq->getNombre();
$IDX	= substr($IDX, 0,1);
$idfecha= $xF->getFechaISO($idfecha);
$xQL	= new MQL();
$sql	= "SELECT   `creditos_solicitud`.`numero_solicitud` AS `id`,

	'Grupo Padio' AS `originador`,
	'Grupo Padio' AS `suboriginador`,
	'Grupo Padio' AS `administrador`,
	/*'' AS ``,*/
         `creditos_solicitud`.`monto_autorizado` AS `monto_original`,
         `creditos_solicitud`.`saldo_actual` AS `saldo_insoluto`,
         ROUND((`creditos_solicitud`.`tasa_interes` *100),2) AS `tasa_de_interes`,
         '0' AS `tasa_de_fondeo`,
         ROUND((`creditos_solicitud`.`tasa_interes` *100),2) AS `margen`,
         '0' AS `tasa_administracion`,
         '0' AS `tasa_otros`,
         '30/360' AS `dias_contar`,
         ROUND((`plazo_en_dias`/30.416666),0) AS `plazo_original`,
         
         `plazo_en_dias`,
         IF(DATEDIFF('$idfecha',`fecha_vencimiento_dinamico`) > 0, DATEDIFF('$idfecha',`fecha_vencimiento_dinamico`), 0) AS `dias_vencidos`,
         `fecha_vencimiento_dinamico`,
         
         
         
         `creditos_solicitud`.`numero_pagos`,
         `creditos_solicitud`.`monto_parcialidad`,
         `creditos_solicitud`.`recibo_ultimo_capital`,
         getUltimoMontoPagado(`numero_solicitud`, 120) AS  `ultimo_capital`,
         getUltimoMontoPagado(`numero_solicitud`, 140) AS  `ultimo_interes`/*,
         
		getMontoPagadoMexFecha(`numero_solicitud`, '$idfecha') AS  `monto_mes`*/
		
FROM     `creditos_solicitud`


WHERE    ( `creditos_solicitud`.`saldo_actual` >0.99 ) AND ( `creditos_solicitud`.`estatus_actual` !=50 )
	";
$xXls	= new cHExcelNew($titulo);
//	$xXls->setRenameSheet(0, "pruebas");
$rs		= $xQL->getRecordset($sql);
$fila	= 2;
$strT	="LoanID|Originator|Sub Originator|Servicer|Original Loan Amount|Current Loan Amount|Rate|Funding Rate|Margin|Servicing fee|Other parts of rate|Day Count (30/360, Act/365, BUS/252)|Original Term|Remaining Term";
$strT	.= "|Payment Frequency|Payment Amount|Payment per frequency|Interest Amount Last Payment|Principal Amount Last Payment|Other Fees|Monthly Income Amount|Other Deductions|Net Take Home";
$strT	.= "|Employer|Industry|Public/Private Sector| Time with Employer (months)|Source of Payment";
$strT	.= "|Current Status|Months Del|x30 (life)|x60 (life)|x90 (life)|City|State|Date Orig|Loan Disbursement Date|Date First Deduction|Date Maturity|Cut off Date";


$tt		= explode("|", $strT);
$xXls->addArray($tt, 1);

while($rw = $rs->fetch_assoc()){
	$arrV	= array();
	//$arrV[]	=
	$idcredito	= $rw["id"];
	$arrV[0]	= $rw["id"];
	$arrV[1]	= $rw["originador"];
	$arrV[2]	= $rw["suboriginador"];
	$arrV[3]	= $rw["administrador"];
	$arrV[4]	= $rw["monto_original"];
	$arrV[5]	= $rw["saldo_insoluto"];
	$arrV[6]	= $rw["tasa_de_interes"];
	$arrV[7]	= $rw["tasa_de_fondeo"];
	$arrV[8]	= $rw["margen"];
	
	$arrV[9]	= $rw["tasa_administracion"];
	$arrV[10]	= $rw["tasa_otros"];
	$arrV[11]	= $rw["dias_contar"];
	$arrV[12]	= $rw["plazo_original"];
	
	$arrV[13]	= "";
	$arrV[14]	= "";
	$arrV[15]	= "";
	$arrV[16]	= "";
	$arrV[17]	= "";
	$arrV[18]	= "";
	$arrV[19]	= "";
	$arrV[20]	= "";
	$arrV[21]	= "";
	$arrV[22]	= "";
	$arrV[23]	= "";
	$arrV[24]	= "";
	$arrV[25]	= "";
	$arrV[26]	= "";
	$arrV[27]	= "";
	$arrV[28]	= "";
	$arrV[29]	= "";
	$arrV[30]	= "";
	$arrV[31]	= "";
	
	$arrV[32]	= "";
	$arrV[33]	= ""; //ciudad
	$arrV[34]	= ""; //estado
	$arrV[35]	= "";
	
	
	
	$idpersona	= 0;
	$EsNomina	= false;
	
	$xCred		= new cCredito($idcredito);
	if($xCred->init() == true){
		$xCred->initPagosEfectuados(false, $idfecha, true);
		//Plazo remanente
		$diasrem 		= round(($xCred->getDiasRemanente($idfecha) / 30.41666666666666),0);
		$arrV[13]		= $diasrem;
		$arrV[14]		= $xCred->getOPeriocidad()->getNombre();
		$arrV[15]		= $xCred->getMontoTotalPresumido($idfecha);
		$arrV[16]		= $xCred->getMontoDeParcialidad();
		$idpersona		= $xCred->getClaveDePersona();
		$EsNomina		= $xCred->getEsNomina();
		//$xCred->setDetermineDatosDeEstatus()
		$saldo			= $xCred->getSaldoActual($idfecha);
		if($xCred->isAtrasado() == true){
			$arrV[28]	= "Mora";
			$diasmora	= $xCred->getDiasDeMora($idfecha);
			$arrV[29]	=   round(($diasmora / SYS_FACTOR_DIAS_MES),0);
			
			//31 60 dias
			if($diasmora>=30 AND $diasmora <=60){
				$arrV[30]	= $saldo;
			}
			//32 90 dias
			if($diasmora>60 AND $diasmora<=90){
				$arrV[31]	= $saldo;
			}
			if($diasmora>90){
				$arrV[32]	= $saldo;
			}
		} else {
			$arrV[28]	= "Al Corriente";
			
		}
		$arrV[35]		= $xCred->getFechaDeSolicitud();
		$arrV[36]		= $xCred->getFechaDeMinistracion();
		$arrV[37]		= $xCred->getFechaDePrimerPago();
		$arrV[38]		= $xCred->getFechaDevencimientoLegal();
		$arrV[39]		= $idfecha;//fecha de corte
		
	}
	
	$arrV[17]	= $rw["ultimo_interes"];
	$arrV[18]	= $rw["ultimo_capital"];
	$arrV[19]	= 0;
	
	
	
	//Monto Ingreso mensual
	$xSoc				= new cSocio($idpersona);
	if($xSoc->init() == true){
		
		$arrV[20]	= $xSoc->getIngresosMensuales();
		//Other Deductions
		$arrV[21]	= 0;
		$arrV[22]	= $arrV[20]; //Total de Ingresos
		
		$xAE		= $xSoc->getOActividadEconomica();
		
		$arrV[25]	= "Privado"; //Sector publico / privado
		
		if($EsNomina == true){
			$xEmp		= $xSoc->getOEmpresa();
			if($xEmp !== null){
				$arrV[23]	= $xEmp->getNombre();
			}
		} else {
			$arrV[23]	= "";
		}
		if($xAE === null){
			$arrV[24]	= ""; //Industria
			$arrV[26]	= "";
		} else {
			
			$idsector	= $xAE->getSectorEconomico();
			$idscian	= $xAE->getClaveActividadSCIAN();
			$xSector	= new cPersonaActividadEconSector($idsector);
			$xSector->init();
			//cPersonaActividadEconomicaCatalogo
			$arrV[24]	= $xSector->getNombre(); //Industria
			
			
			if($EsNomina == true){
				$arrV[26]	= $xAE->getAntiguedadEnMeses($idfecha);
				$arrV[27]	= "Salario";
			} else {
				$arrV[26]	= 0;
				$arrV[27]	= "";
			}
		}
		
		$xDom	= $xSoc->getODomicilio();
		if($xDom === null){
			
		} else {
			$arrV[33]	= $xDom->getCiudad();
			$arrV[34]	= $xDom->getEstado();
		}
	}
	
	
	ksort($arrV);
	
	$xXls->addArray($arrV, $fila);
	$fila++;
}
$rs		= null;

$xXls->setExportar($titulo);

//return $xXls->getLinkDownload("TR.Descargar", "");
//var_dump($xXls->getNombreArchivo());
header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=\"Reporte_Vintage.xlsx\"; ");
readfile($xXls->getRutaArchivo());

/*$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

$xRPT->setProcessSQL();

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);*/



exit;

?>
<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
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
$xHP		= new cHPage("TR.Reporte Vintage", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
$xLayout	= new cReportes_Layout();

//============== Tareas previas de ejecucion



function jsaGetArchivo($idempresa, $idfrecuencia, $idfecha){
	$xF		= new cFecha();
	$xFreq	= new cPeriocidadDePago($idfrecuencia);
	$xFreq->init();
	$IDX	= $xFreq->getNombre();
	$IDX	= substr($IDX, 0,1);
	$idfecha= $xF->getFechaISO($idfecha);
	$xQL	= new MQL();
	$sql	= "SELECT   `creditos_solicitud`.`numero_solicitud` AS `id`,

	'Grupo Padio' AS `originador`,
	'Grupo Padio' AS `suboriginador`,
	'Grupo Padio' AS `administrador`,
	/*'' AS ``,*/
         `creditos_solicitud`.`monto_autorizado` AS `monto_original`,
         `creditos_solicitud`.`saldo_actual` AS `saldo_insoluto`,
         ROUND((`creditos_solicitud`.`tasa_interes` *100),2) AS `tasa_de_interes`,
         '0' AS `tasa_de_fondeo`,
         ROUND((`creditos_solicitud`.`tasa_interes` *100),2) AS `margen`,
         '0' AS `tasa_administracion`,
         '0' AS `tasa_otros`,
         '30/360' AS `dias_contar`,
         ROUND((`plazo_en_dias`/30.416666),0) AS `plazo_original`,
         
         `plazo_en_dias`,
         IF(DATEDIFF('$idfecha',`fecha_vencimiento_dinamico`) > 0, DATEDIFF('$idfecha',`fecha_vencimiento_dinamico`), 0) AS `dias_vencidos`,
         `fecha_vencimiento_dinamico`,
	
	
         
         `creditos_solicitud`.`numero_pagos`,
         `creditos_solicitud`.`monto_parcialidad`,
         `creditos_solicitud`.`recibo_ultimo_capital`,
         getUltimoMontoPagado(`numero_solicitud`, 120) AS  `ultimo_capital`,
         getUltimoMontoPagado(`numero_solicitud`, 140) AS  `ultimo_interes`/*,

		getMontoPagadoMexFecha(`numero_solicitud`, '$idfecha') AS  `monto_mes`*/
                  
FROM     `creditos_solicitud`


WHERE    ( `creditos_solicitud`.`saldo_actual` >0.99 ) AND ( `creditos_solicitud`.`estatus_actual` !=50 )
	";
	$xXls	= new cHExcelNew("Reporte Vintage");
	//	$xXls->setRenameSheet(0, "pruebas");
	$rs		= $xQL->getRecordset($sql);
	$fila	= 2;
	$strT	="LoanID|Originator|Sub Originator|Servicer|Original Loan Amount|Current Loan Amount|Rate|Funding Rate|Margin|Servicing fee|Other parts of rate|Day Count (30/360, Act/365, BUS/252)|Original Term|Remaining Term";
	$strT	.= "|Payment Frequency|Payment Amount|Payment per frequency|Interest Amount Last Payment|Principal Amount Last Payment|Other Fees|Monthly Income Amount|Other Deductions|Net Take Home";
	$strT	.= "|Employer|Industry|Public/Private Sector| Time with Employer (months)|Source of Payment";
	$strT	.= "|Current Status|Months Del|x30 (life)|x60 (life)|x90 (life)|City|State|Date Orig|Loan Disbursement Date|Date First Deduction|Date Maturity|Cut off Date";
	$tt		= explode("|", $strT);
	$xXls->addArray($tt, 1);
	
	while($rw = $rs->fetch_assoc()){
		$arrV	= array();
		//$arrV[]	=
		$idcredito	= $rw["id"];
		$arrV[0]	= $rw["id"];
		$arrV[1]	= $rw["originador"];
		$arrV[2]	= $rw["suboriginador"];
		$arrV[3]	= $rw["administrador"];
		$arrV[4]	= $rw["monto_original"];
		$arrV[5]	= $rw["saldo_insoluto"];
		$arrV[6]	= $rw["tasa_de_interes"];
		$arrV[7]	= $rw["tasa_de_fondeo"];
		$arrV[8]	= $rw["margen"];
		
		$arrV[9]	= $rw["tasa_administracion"];
		$arrV[10]	= $rw["tasa_otros"];
		$arrV[11]	= $rw["dias_contar"];
		$arrV[12]	= $rw["plazo_original"];
		
		$arrV[13]	= "";
		$arrV[14]	= "";
		$arrV[15]	= "";
		$arrV[16]	= "";
		$arrV[17]	= "";
		$arrV[18]	= "";
		$arrV[19]	= "";
		$arrV[20]	= "";
		$arrV[21]	= "";
		$arrV[22]	= "";
		$arrV[23]	= "";
		$arrV[24]	= "";
		$arrV[25]	= "";
		$arrV[26]	= "";
		$arrV[27]	= "";
		$arrV[28]	= "";
		$arrV[29]	= "";
		$arrV[30]	= "";
		$arrV[31]	= "";
		
		$arrV[32]	= "";
		$arrV[33]	= ""; //ciudad
		$arrV[34]	= ""; //estado
		$arrV[35]	= "";
		
		
		
		$idpersona	= 0;
		$EsNomina	= false;
		
		$xCred		= new cCredito($idcredito);
		if($xCred->init() == true){
			$xCred->initPagosEfectuados(false, $idfecha, true);
			//Plazo remanente
			$diasrem 		= round(($xCred->getDiasRemanente($idfecha) / 30.41666666666666),0);
			$arrV[13]		= $diasrem;
			$arrV[14]		= $xCred->getOPeriocidad()->getNombre();
			$arrV[15]		= $xCred->getMontoTotalPresumido($idfecha);
			$arrV[16]		= $xCred->getMontoDeParcialidad();
			$idpersona		= $xCred->getClaveDePersona();
			$EsNomina		= $xCred->getEsNomina();
			//$xCred->setDetermineDatosDeEstatus()
			$saldo			= $xCred->getSaldoActual($idfecha);
			if($xCred->isAtrasado() == true){
				$arrV[28]	= "Mora";
				$diasmora	= $xCred->getDiasDeMora($idfecha);
				$arrV[29]	=   round(($diasmora / SYS_FACTOR_DIAS_MES),0);

				//31 60 dias
				if($diasmora>=30 AND $diasmora <=60){
					$arrV[30]	= $saldo;
				}
				//32 90 dias
				if($diasmora>60 AND $diasmora<=90){
					$arrV[31]	= $saldo;
				}
				if($diasmora>90){
					$arrV[32]	= $saldo;
				}
			} else {
				$arrV[28]	= "Al Corriente";
				
			}
			$arrV[35]		= $xCred->getFechaDeSolicitud();
			$arrV[36]		= $xCred->getFechaDeMinistracion();
			$arrV[37]		= $xCred->getFechaDePrimerPago();
			$arrV[38]		= $xCred->getFechaDevencimientoLegal();
			$arrV[39]		= $idfecha;//fecha de corte 

		}

		$arrV[17]	= $rw["ultimo_interes"];
		$arrV[18]	= $rw["ultimo_capital"];
		$arrV[19]	= 0;
		

		
		//Monto Ingreso mensual
		$xSoc				= new cSocio($idpersona);
		if($xSoc->init() == true){
			
			$arrV[20]	= $xSoc->getIngresosMensuales();
			//Other Deductions
			$arrV[21]	= 0;
			$arrV[22]	= $arrV[20]; //Total de Ingresos
			
			$xAE		= $xSoc->getOActividadEconomica();

			$arrV[25]	= "Privado"; //Sector publico / privado
			
			if($EsNomina == true){
				$xEmp		= $xSoc->getOEmpresa();
				if($xEmp !== null){
					$arrV[23]	= $xEmp->getNombre();
				}
			} else {
				$arrV[23]	= "";
			}
			if($xAE === null){
				$arrV[24]	= ""; //Industria
				$arrV[26]	= "";
			} else {
				
				$idsector	= $xAE->getSectorEconomico();
				$idscian	= $xAE->getClaveActividadSCIAN();
				$xSector	= new cPersonaActividadEconSector($idsector);
				$xSector->init();
				//cPersonaActividadEconomicaCatalogo
				$arrV[24]	= $xSector->getNombre(); //Industria
				
				
				if($EsNomina == true){
					$arrV[26]	= $xAE->getAntiguedadEnMeses($idfecha);
					$arrV[27]	= "Salario";
				} else {
					$arrV[26]	= 0;
					$arrV[27]	= "";
				}
			}

			$xDom	= $xSoc->getODomicilio();
			if($xDom === null){
				
			} else {
				$arrV[33]	= $xDom->getCiudad();
				$arrV[34]	= $xDom->getEstado();
			}
		}
		
		
		ksort($arrV);
		
		$xXls->addArray($arrV, $fila);
		$fila++;
	}
	$rs		= null;
	
	$xXls->setExportar("Reporte Vintage");
	
	return $xXls->getLinkDownload("TR.Descargar", "");
}
$jxc ->exportFunction('jsaGetArchivo', array('idempresa', 'idperiocidad', 'idfecha'), "#getarchivoxls");
$jxc ->process();
$empresa		= parametro("empresa", SYS_TODAS);

$xQL->setCall("proc_creditos_abonos_parciales");
$xQL->setCall("proc_recibos_distrib");



$xHP->init();

$xFRM			= new cHForm("frmreportevintage", "./");
$xSel			= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

//$xFRM->addJsBasico();
$xFRM->OButton("TR.Obtener Archivo", "jsaGetArchivo()", $xFRM->ic()->EXCEL);
$xFRM->addToolbar("<span id='getarchivoxls'></span>");
$xFRM->addHElem($xSel->getListaDePeriocidadDePago("", false)->get("TR.FRECUENCIA", true));
$xFRM->ODate("idfecha", false, "TR.FECHA DE ENVIO");
$xFRM->OHidden("idempresa", $empresa);

echo $xFRM->get();
?>
<script>

</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();


?>
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
//=====================================================================================================
ini_set("max_execution_time", 900);
//ini_set("memory_limit", SAFE_MEMORY_LIMIT);
ini_set("memory_limit", "2048M");
//ini_set('xdebug.profiler_enable', "1");
//


$xHP					= new cHPage("", HP_REPORT);
$mql					= new cSQLListas();
$xVi					= new cSQLVistas();

$xF						= new cFecha();
$query					= new MQL();
$xLoc					= new cLocal();
$xLog					= new cCoreLog();
$xDB					= new cSAFEData();
$xT						= new cTipos();

$xCR					= new cReporteCirculoDeCredito_tipo();
$ClaveOtorgante			= $xCR->getClaveDeOtorgante();
$NombreOtorgante		= $xCR->getNombreOtorgante();

$xCache					= new cCache();

$ByPersona1				= "";
$ByPersona2				= "";
$ByPersona3				= "";

$FechaInicial			= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal				= parametro("off", fechasys(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

$toJson					= false;//parametro("beauty", false, MQL_BOOL);
$lineaJson				= array();
$itemJson				= array();

$FechaExtraccion		= date("Ymd", strtotime($FechaFinal));
$estatus_actual 		= parametro("f2", false, MQL_INT);
$persona				= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$creditoref				= parametro("creditoref", 0, MQL_INT);
$cualquiera				= parametro("cualquiera", false, MQL_BOOL);

if($creditoref > DEFAULT_CREDITO AND $persona <= DEFAULT_SOCIO){
	$xCredImp			= new cCredito($creditoref);
	if($xCredImp->init() == true){
		//$persona		= $xCredImp->getClaveDePersona();
		
		$ByPersona2			= " AND	(`creditos_solicitud`.`numero_solicitud` = $creditoref) ";
		$ByPersona1			= " AND (`creditos_abonos_parciales`.`docto_afectado` = $creditoref ) ";
		$ByPersona3			= " AND (`docto_afectado` = $creditoref ) ";
		$toJson				= true;
		
	}
}

if($persona > DEFAULT_SOCIO){
	$ByPersona2			= " AND	(`creditos_solicitud`.`numero_socio` = $persona) ";
	$ByPersona1			= " AND (`creditos_abonos_parciales`.`socio_afectado` = $persona ) ";
	$ByPersona3			= " AND (`socio_afectado` = $persona ) ";
	$toJson				= true;
}


$xLog->setNoLog();


//header("Content-type: text/plain");
//header("Content-type: application/csv");
if($toJson == true){
	//header("Content-type: text/plain");
	//memprof_enable();
} else {
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=" . urlencode($ClaveOtorgante . "_" . $NombreOtorgante . "_" . $FechaExtraccion) . ".txt");
}
//MontoUltimoPago|FechaIngresoCarteraVencida|MontoCorrespondienteIntereses|FormaPagoActualIntereses|DiasVencimiento|PlazoMeses|MontoCreditoOriginacion|CorreoElectronicoConsumidor
//Linea de Encabezados
$strHEAD				= strtoupper("ClaveOtorgante|NombreOtorgante|IdentificadorDeMedio|FechaExtraccion|NotaOtorgante|Version|ApellidoPaterno|ApellidoMaterno|ApellidoAdicional|Nombres|FechaNacimiento|RFC|CURP|NumeroSeguridadSocial|Nacionalidad|Residencia|NumeroLicenciaConducir|EstadoCivil|Sexo|ClaveElectorIFE|NumeroDependientes|FechaDefuncion|IndicadorDefuncion|TipoPersona|Direccion|ColoniaPoblacion|DelegacionMunicipio|Ciudad|Estado|CP|FechaResidencia|NumeroTelefono|TipoDomicilio|TipoAsentamiento|OrigenDomicilio|NombreEmpresa|Direccion|ColoniaPoblacion|DelegacionMunicipio|Ciudad|Estado|CP|NumeroTelefono|Extension|Fax|Puesto|FechaContratacion|ClaveMoneda|SalarioMensual|FechaUltimoDiaEmpleo|FechaVerificacionEmpleo|OrigenRazonSocialDomicilio|ClaveActualOtorgante|NombreOtorgante|CuentaActual|TipoResponsabilidad|TipoCuenta|TipoContrato|ClaveUnidadMonetaria|ValorActivoValuacion|NumeroPagos|FrecuenciaPagos|MontoPagar|FechaAperturaCuenta|FechaUltimoPago|FechaUltimaCompra|FechaCierreCuenta|FechaCorte|Garantia|CreditoMaximo|SaldoActual|LimiteCredito|SaldoVencido|NumeroPagosVencidos|PagoActual|HistoricoPagos|ClavePrevencion|TotalPagosReportados|ClaveAnteriorOtorgante|NombreAnteriorOtorgante|NumeroCuentaAnterior|FechaPrimerIncumplimiento|SaldoInsoluto|");
$strHEAD				.= strtoupper("MontoUltimoPago|FechaIngresoCarteraVencida|MontoCorrespondienteIntereses|FormaPagoActualIntereses|DiasVencimiento|PlazoMeses|MontoCreditoOriginacion|CorreoElectronicoConsumidor|");
$strHEAD				.= strtoupper("TotalSaldosActuales|TotalSaldosVecidos|TotalElementosNombreReportados|TotalElementosDireccionReportados|TotalElementosEmpleoReportados|TotalElementosCuentaReportados|NombreOtorgante|DomicilioDevolucion");
if($toJson == true){
	$itemJson			= explode("|", $strHEAD);
} else {
	echo $strHEAD . "\r\n";
}
$idx1			= "cc.rpt.rs1.$FechaFinal.$persona.$creditoref";
$idx2			= "cc.rpt.rs2.$FechaFinal.$persona.$creditoref";
$idx3			= "cc.rpt.rs3.$FechaFinal.$persona.$creditoref";

$DPagos			= $xCache->get($idx1);
if(!is_array($DPagos)){
	
//No cachea la consulta
	$DPagos			= array();
	$rsPagos		= $query->getDataRecord("SELECT * FROM `creditos_abonos_parciales` WHERE	(`creditos_abonos_parciales`.`fecha_de_pago` <='$FechaFinal') $ByPersona1  ORDER BY `socio_afectado`,`docto_afectado`,`fecha_de_pago` ");
	
	foreach ($rsPagos as $dpags ){
		$credito	= $dpags["docto_afectado"];
		$DPagos[$credito][]	= $dpags;
	}
	$rsPagos		= null;
	$xCache->set($idx1, $DPagos);

}

//$DCal			= $xCache->get($idx2);
//if(!is_array($DCal)){
	$DCal			= array();
	
	//$rsCal			= $query->getDataRecord("SELECT * FROM `letras` WHERE	(`fecha_de_pago` <='$FechaFinal') $ByPersona3");
	$rsCal			= $query->getDataRecord($xVi->getVistaLetras(false, false, false, false, " AND (`fecha_afectacion`<='$FechaFinal') $ByPersona3 "));
			//"SELECT * FROM `letras` WHERE	(`fecha_de_pago` <='$FechaFinal') $ByPersona3");
	
	foreach ($rsCal as $dscal ){
		$credito	= $dscal["docto_afectado"];
		$DCal[$credito][]	= $dscal;
	}
	$rsCal			= null;
	//$xCache->set($idx2, $DCal);
//}
/*
 TotalSaldosActuales
TotalSaldosVencidos
TotalElementosNombreReportados
TotalElementosDireccionReportados
TotalElementosEmpleoReportados
TotalElementosCuentaReportados
NombreOtorgante
DomicilioDevolucion
*/
$TotalSaldosActuales				= 1;			//TODO: Si es saldo vigente, reportar 1.- Es suma
$TotalSaldosVencidos				= 0;			//TODO: Suma.- saldo si es vencido
$TotalElementosNombres				= 1;
$TotalElementosDireccion			= 1;
$TotalElementosEmpleo				= 1;
$TotalElementosCuenta				= 1;


/*
ClaveOtorgante
NombreOtorgante
IdentificadorDeMedio
FechaExtraccion
NotaOtorgante
*/
$LimSQL					= "";//" LIMIT 0,500";


$IdentificadorMedio		= 1;
$NotaOtorgante			= ""; //Nota de otorgante .- texto libre.
$xSTb					= new cSQLTabla(TCREDITOS_REGISTRO);
$sql 			= $mql->getQueryInicialDeCreditos("", "", " 	AND (`creditos_solicitud`.`fecha_ministracion` <= '" . $FechaFinal . "' )
				AND
				(`creditos_solicitud`.`numero_socio` !=" . DEFAULT_SOCIO . ")
				AND
				(`creditos_solicitud`.`monto_autorizado` > " . TOLERANCIA_SALDOS .  ")
				AND	
				(`creditos_solicitud`.`estatus_actual` !=" . CREDITO_ESTADO_AUTORIZADO . " AND `creditos_solicitud`.`estatus_actual` !=" . CREDITO_ESTADO_SOLICITADO .")  
				$ByPersona2 $LimSQL
				");

//exit($sql);

$arrEquivTipoDom		= array(
						1 => "C", 2 => "N", 3 => "E", 99 => "O"
						);						
$arrEquivTipoRes		= array(1 => 1, 2=> 2, 3 => 4, 4 => 3, 99 => "");
$arrEquivEstadoCiv		= array(1 => "C" , 2 => "S", 3 => "D", 4 => "L", 5 => "", 6 => "V", 7 => "E",99 => "");
$arrEquivGenero			= array(1 => "M", 2=> "F", 99 => "");

$version				= 4;

$datos					= $xCache->get($idx3);
if(!is_array($datos)){
	$datos				= $query->getDataRecord($sql);
	$xCache->set($idx3, $datos);
}
$icnt					= 0;


foreach($datos as $rw){
	$linea					= "$ClaveOtorgante|$NombreOtorgante|$IdentificadorMedio|$FechaExtraccion|$NotaOtorgante|$version|";
	
	$idcredito				= $rw["numero_solicitud"];
	$idpersona				= $rw["numero_socio"];
	$xSoc					= new cSocio( $idpersona );
	
	
	$xSoc->init();
	//$DSoc					= $xSoc->getDatosInArray();
	//$xDom					= $xSoc->getDatosDomicilio();
	//$xDAEconom				= $xSoc->getDatosActividadEconomica();
	$ODom					= $xSoc->getODomicilio();
	$OActE					= $xSoc->getOActividadEconomica();
	$sucres					= $xSoc->getSucursal();
	
	$ApellidoPaterno		= $xCR->getText($xSoc->getApellidoPaterno());
	$ApellidoMaterno		= $xCR->getText($xSoc->getApellidoMaterno());
	if(trim($ApellidoPaterno) == ""){
		$ApellidoPaterno	= (trim($ApellidoMaterno) == "") ? "NO PROPORCIONADO" : $ApellidoMaterno;
		$ApellidoMaterno	= "NO PROPORCIONADO";	
	}
	if(trim($ApellidoMaterno) == ""){
		$ApellidoMaterno	= "NO PROPORCIONADO";
	}
	$ApellidoAdicional		= "";
	$Nombres				= $xCR->getText($xSoc->getNombre(), false,49 );
	$FechaNacimiento		= $xCR->getDate($xSoc->getFechaDeNacimiento() );
	if($xSoc->getEdad() < 15 ){
		$FechaNacimiento		= ""; //$xSoc->getFechaDeNacimiento() . "[" . $xSoc->getEdad() . "]";
	}
	$RFC					= $xCR->getText($xSoc->getRFC(true));
	$CURP					= $xCR->getText($xSoc->getCURP(true));
	$Nacionalidad			= "MX";//$xCR->getText($xSoc->getPaisDeOrigen() );
	$tipo_de_regimen		= ($ODom == null) ? DEFAULT_PERSONAS_REGIMEN_VIV : $ODom->getTipoDeRegimen() ;
	//setLog("A>>>>>" . $xLoc->DomicilioEstadoClaveSIC());
	$domicilio_entidad_fed	= ($ODom == null ) ? $xLoc->DomicilioEstadoClaveSIC():  $ODom->getClaveDeEstadoEnSIC();
	$Residencia				= $xCR->getETipoDeRegimenViv( $tipo_de_regimen );
	$licencia				= $xSoc->getClaveLicenciaConducir();
	$EstadoCivil			= $xCR->getETipoECivil( $xSoc->getEstadoCivil() );
	$Sexo					= $xCR->getETipoPGenero( $xSoc->getGenero() );
	$ClaveIFE				= $xCR->getText( $xSoc->getClaveDeIFE());
	$NumeroDependientes		= $xSoc->getNumeroDeDependientes();
	$FechaDefuncion			= "";
	$IndicadoDefuncion		= "Y";
	$TipoPersona			= $xCR->getETipoPersona( $xSoc->getPersonalidadJuridica() );	//fisica moral
	
	$NSS					= $xSoc->getNSS(true);
	
	/*
	ApellidoPaterno	
	ApellidoMaterno	
	ApellidoAdicional	
	Nombres	
	FechaNacimiento	
	RFC	
	CURP	
	Nacionalidad
	Residencia	
	NumeroLicenciaConducir	
	EstadoCivil	
	Sexo	
	ClaveElectorIFE	
	NumeroDependientes	
	FechaDefuncion	
	IndicadorDefuncion	
	TipoPersona
	*/
	//Linea de Socio
	
	$linea					.= "$ApellidoPaterno|$ApellidoMaterno|$ApellidoAdicional|$Nombres|$FechaNacimiento|$RFC|$CURP|$NSS|$Nacionalidad|$Residencia|$licencia|$EstadoCivil|$Sexo|";
	$linea					.= "$ClaveIFE|$NumeroDependientes|$FechaDefuncion|$IndicadoDefuncion|$TipoPersona|";		


		
	//==================================== Domicilio Particular ==================================
	//Calle, numero exterior, numero interior
	if($ODom == null){
	    $Direccion				= "DOMICILIO NO PROPORCIONADO";
	    $ColoniaPoblacion		= "NO PROPORCIONADO";
	    $DelegacionMunicipio	= $xCR->getText($xLoc->DomicilioMunicipio());
	    $Ciudad					= "";
	    $Estado					= $xCR->getText($xLoc->DomicilioEstadoClaveSIC());
	    $CP						= $xCR->getText($xLoc->DomicilioCodigoPostal(), 5);
	    $xSuc					= new cSucursal($sucres);
	    if($xSuc->init() == true){
	    	$DelegacionMunicipio	= $xCR->getText($xSuc->getMunicipio());
	    	$Estado					= $xCR->getText($xSuc->getClaveDeEstadoSIC());
	    	$CP						= $xCR->getText($xSuc->getCodigoPostal(),5);
	    }
	    
	    $FechaResidencia		= "";
	    $NumeroTelefono			= "";
	    $TipoDomicilio			= "";
	    $TipoAsentamiento		= "";
	    $OrigenDomicilio		= $xCR->getText(EACP_CLAVE_DE_PAIS);
	    
	    $linea					.= "$Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|$OrigenDomicilio|";
	    $xLog->add("ERROR\t$idpersona-$idcredito\t$sucres\tSin Domicilio $Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|$OrigenDomicilio|\r\n", $xLog->DEVELOPER);
	} else {
		$calle						= $ODom->getCalle();
		$numExterior				= ($ODom->getNumeroExterior() == "") ? "SN" : $ODom->getNumeroExterior();
		$numeroInterior				= $ODom->getNumeroInterior();
		$Direccion					= $xCR->getText(trim($xCR->getPurgueDomicilio("$calle $numeroInterior $numExterior")), false, 79);
		
		$ColoniaPoblacion			= $xCR->getText($ODom->getColonia() );
		$DelegacionMunicipio		= $xCR->getText($ODom->getMunicipio() );
		$Ciudad						= $xCR->getText($ODom->getCiudad() );
		$CP							= $xCR->getText($ODom->getCodigoPostal(), 5);
		$Estado						= $xCR->getText($ODom->getClaveDeEstadoEnSIC());
		//setLog("RRRRRR .. " . $ODom->getClaveDeEstadoEnSIC() . " ----  " . $xLoc->DomicilioEstadoClaveSIC());
		$xColonia					= new cDomiciliosColonias();
		if($Direccion == "" OR trim($Direccion) == "SN" ){ 
			$Direccion				= "DOMICILIO NO PROPORCIONADO";
			$DelegacionMunicipio	= $xCR->getText($xLoc->DomicilioMunicipio());
			$CP						= $xCR->getText($xLoc->DomicilioCodigoPostal());
			$ColoniaPoblacion		= "";
			$Estado					= $xCR->getText($xLoc->DomicilioEstadoClaveSIC());
			$xSuc					= new cSucursal($sucres);
			if($xSuc->init() == true){
				$DelegacionMunicipio	= $xCR->getText($xSuc->getMunicipio());
				$Estado					= $xCR->getText($xSuc->getClaveDeEstadoSIC());
				$CP						= $xCR->getText($xSuc->getCodigoPostal(),5);
			}

			$xLog->add("ERROR\t$idpersona-$idcredito\t$sucres\tEn datos de Domicilio $Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|$OrigenDomicilio|\r\n", $xLog->DEVELOPER);
		}
		$CP							= $xCR->getText($CP, 5);
		
		
		$xColonia->existe($CP, "", "", true);
		
		
		
		$FechaResidencia			= "";
		$NumeroTelefono				= $xSoc->getTelefonoPrincipal();
		$TipoDomicilio				= $xCR->getETipoDomicilio( $ODom->getTipoDeDomicilio() ); // (isset($arrEquivTipoDom[ $xDom["tipo_domicilio"] ])) ? $arrEquivTipoDom[ $xDom["tipo_domicilio"] ] : "";
		$TipoAsentamiento			= $xCR->getETipoColonia( trim($xColonia->getTipoDeAsentamiento()) );
		$OrigenDomicilio			= $xCR->getText($ODom->getClaveDePais());
		
		/*
		Direccion
		ColoniaPoblacion
		DelegacionMunicipio
		Ciudad
		Estado
		CP
		FechaResidencia
		NumeroTelefono
		TipoDomicilio
		TipoAsentamiento
		*/
		$linea					.= "$Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|$OrigenDomicilio|";
	}
	
	//==================================== Domicilio Trabajo ==================================
	/*
	NombreEmpresa
	Direccion
	ColoniaPoblacion
	DelegacionMunicipio
	Ciudad
	Estado
	CP
	NumeroTelefono
	Extension
	Fax
	Puesto
	FechaContratacion
	ClaveMoneda
	SalarioMensual
	FechaUltimoDiaEmpleo
	FechaVerificacionEmpleo
	*/
	$DomEmpresa			= true;
	
	if($OActE == null ){
	    $DomEmpresa		= false;
	} else {
	    $NombreEmpresa			= (trim($OActE->getNombreEmpresa()) == "") ? "NO PROPORCIONADO" : $xCR->getText($OActE->getNombreEmpresa(), false, 30);
	    $DireccionEmpresa		= $xCR->getText( $xCR->getPurgueDomicilio($OActE->getDomicilio()), false, 79);
	    if(strpos($NombreEmpresa, "NINGUNO") !== false){
	    	$NombreEmpresa		= "NO PROPORCIONADO";
	    }
	    //Valida  tamannio de elementos de la empresa
	    $memDEmp				= count(explode(" ", $DireccionEmpresa));
	    
	    if($memDEmp<=1){
	    	$xLog->add("ERROR\t$idpersona-$idcredito\t$sucres\tSin Domicilio Valido de Empresa $NombreEmpresa\r\n", $xLog->DEVELOPER);
	    	$DomEmpresa			= false;
	    }
	    //$OActE->init();
	    $ColoniaEmpresa			= $xCR->getText($OActE->getNombreColonia());
	    $MunicipioEmpresa		= $xCR->getText($OActE->getNombreMunicipio() );
	    $CiudadEmpresa			= "";//$xCR->getText($OActE->getLocalidad() );
	    $EstadoEmpresa			= $xCR->getText( $OActE->getClaveDeEstadoEnSIC() ); //parche
	    $CPEmpresa				= $xCR->getText($OActE->getCodigoPostal(), 5);
	    $OrigenRazonSocialDomicilio = $xCR->getText($OActE->getClaveDePais());
	    $OEmpresa				= $OActE->getOEmpresa();
	    
		if( setNoMenorQueCero($CPEmpresa) <= 0){
			$DomEmpresa		= false;
			//if($OEmpresa == null){
				//$DomEmpresa		= false;
			//} else {
				//copiar domicilio de la empresa
				//$OEmpresa->getDomicilio();
			//}
			$xLog->add("ERROR\t$idpersona-$idcredito\t$sucres\tSin Empresa $NombreEmpresa|$DireccionEmpresa|$ColoniaEmpresa|$MunicipioEmpresa|$CiudadEmpresa|$EstadoEmpresa|$CPEmpresa\r\n", $xLog->DEVELOPER);
		} else {
		    $xCR->getPurgueTel($OActE->getTelefono());
		    $TelefonoEmpresa		= $xCR->getETelefono();
		    $ExtensionEmpresa		= $xCR->getETelefonoExt();	    
		    
		    $FaxEmpresa				= "";
		    $PuestoEmpresa			= $xCR->getText($OActE->getPuesto(), false,  40);
		    $FContratoEmpresa		= "";
		    $ClaveMonedaEmp			= "MX";
		    $SalarioMensual			= $xCR->getMonto( $OActE->getSalarioMensual() );
		    if( setNoMenorQueCero($SalarioMensual) <= 0){
		    	$SalarioMensual		= "";
		    	$ClaveMonedaEmp		= "";
		    	$PuestoEmpresa		= "";
		    	$xLog->add("ERROR\t$idpersona-$idcredito\t$sucres\tSin Salario\r\n", $xLog->DEVELOPER);
		    } 
		    $FechaUltimoEmpresa		= "";
		    $FechaVerificaEmp		= "";
		    if( $OActE->getEstadoActual() == $OActE->ESTADO_VERIFICADO ){	$FechaVerificaEmp	= $OActE->getFechaVerificacion();	}
		}
	}
	if($DomEmpresa == false){
		$NombreEmpresa		= "NO PROPORCIONADO";
		$DireccionEmpresa		= "";
		$ColoniaEmpresa			= "";
		$MunicipioEmpresa		= "";
		$CiudadEmpresa			= "";
		$EstadoEmpresa			= "";
		$CPEmpresa				= ""; //
		$TelefonoEmpresa		= "";
		$ExtensionEmpresa		= "";
		$FaxEmpresa				= "";
		$PuestoEmpresa			= "";
		$FContratoEmpresa		= "";
		$ClaveMonedaEmp			= "";
		$SalarioMensual			= "";
		$FechaUltimoEmpresa		= "";
		$FechaVerificaEmp		= "";
		$OrigenRazonSocialDomicilio = "";
	}
	$linea			.= "$NombreEmpresa|$DireccionEmpresa|$ColoniaEmpresa|$MunicipioEmpresa|$CiudadEmpresa|$EstadoEmpresa|$CPEmpresa|$TelefonoEmpresa|";
	$linea			.= "$ExtensionEmpresa|$FaxEmpresa|$PuestoEmpresa|$FContratoEmpresa|$ClaveMonedaEmp|$SalarioMensual|$FechaUltimoEmpresa|$FechaVerificaEmp|$OrigenRazonSocialDomicilio|";	
	/*
	ClaveActualOtorgante
	NombreOtorgante
	CuentaActual
	TipoResponsabilidad
	TipoCuenta
	TipoContrato
	ClaveUnidadMonetaria
	ValorActivoValuacion
	NumeroPagos
	FrecuenciaPagos
	MontoPagar
	FechaAperturaCuenta
	FechaUltimoPago
	FechaUltimaCompra
	FechaCierreCuenta
	FechaCorte
	Garantia
	CreditoMaximo
	SaldoActual
	LimiteCredito
	SaldoVencido
	NumeroPagosVencidos
	PagoActual
	HistoricoPagos
	ClavePrevencion
	TotalPagosReportados
	ClaveAnteriorOtorgante
	NombreAnteriorOtorgante
	NumeroCuentaAnterior	
	*/
	$ClavePrevencion			= "";
	//obtener parametros extras en una array
	//su valor es del producto
//======================================================================= INICIAR CREDITO	
	$xCred						= new cCredito($idcredito, $rw["numero_socio"]);
	$xCred->init($rw);
	if( isset($DPagos[$idcredito]) ){
		$xCred->initPagosEfectuados( $DPagos[$idcredito] , $FechaFinal);
		unset($DPagos[$idcredito]);
		///$xLog->add("OK\t$icnt\tDatos del credito $idpersona|$idcredito SI existen \r\n", $xLog->DEVELOPER);
	} else {
		$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\t$icnt\tDatos de Pago del credito $idpersona|$idcredito no existen \r\n", $xLog->DEVELOPER);
	}
	$TipoEnSistema				= $xCred->getOProductoDeCredito()->getTipoEnSistema();
	
	$xOfic						= new cOficial($xCred->getOficialDeCredito());
	$xOfic->init();
	
	$ClaveActualOtorgante		= $xCR->getClaveDeOtorgante();// ENTIDAD_CLAVE_SIC;
	$NombreOtorgante			= $xCR->getNombreOtorgante();
	$CuentaActual				= $rw["numero_solicitud"];
	$xTConv						= new cProductoDeCredito($rw["tipo_convenio"]);
	$xTConv->init();
	$DTConv						= $xTConv->getOtrosParametrosInArray();
	
	$TipoDeResponsabilidad		= isset($DTConv["SIC_TIPO_DE_RESPONSABILIDAD"]) ? $DTConv["SIC_TIPO_DE_RESPONSABILIDAD"] : "I"; //individual
	$TipoDeCuenta				= isset($DTConv["SIC_TIPO_DE_CUENTA"]) ? $DTConv["SIC_TIPO_DE_CUENTA"] : "F";					//Pagos fijos
	$TipoDeContrato				= strtoupper($xCred->getOProductoDeCredito()->getTipoDeContratoCR());// isset($DTConv["SIC_TIPO_DE_CONTRATO"]) ? $DTConv["SIC_TIPO_DE_CONTRATO"] : "DESCONOCIDO";
	$ClaveUnidadMonetaria		= "MX";
	$ValorActivoValuacion		= 0;	//Monto total de las garantias
	$NumeroDePagos				= $xCred->getPagosAutorizados();
	$FrecuenciaDePagos			= $xCR->getEPeriocidad( $xCred->getPeriocidadDePago() );
	
		
	if($xCred->isAFinalDePlazo() == true){
		$MontoPagar			= $xCR->getMonto($xCred->getSaldoActual($FechaFinal));		//Acabar, valor de la letra actual o saldo?
		if($persona> DEFAULT_SOCIO){
			$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tEl Monto a pagar : $MontoPagar es por SALDO\r\n");
		}
	} else {
		$MontoPagar			= $xCR->getMonto($xCred->getMontoDeParcialidad());		//Va el monto de la parcialidad
		//setLog("Va el monto de la parcialidad $MontoPagar");
		if($persona> DEFAULT_SOCIO){
			$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tEl Monto a pagar : $MontoPagar es por LETRA\r\n");
		}
			
	}
	//====================== 2018-07-02
	$SSDO					= $xCR->getMonto($xCred->getSaldoActual($FechaFinal));
	if($SSDO < $MontoPagar){
		//setLog("Va el monto de la parcialidad $MontoPagar es menor de $SSDO");
		$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tEl Monto a pagar : $MontoPagar es mayor al Saldo $SSDO\r\n");
		$MontoPagar			= $SSDO;
	}
		
	
	$FechaAperturaCuenta		= $xCR->getDate($xCred->getFechaDeMinistracion() );
	$FechaUltimoPago			= $xCred->getFechaUltimoDePago();
	//setLog("1....$FechaUltimoPago");
	if($xF->getInt($xCred->getFechaUltimoDePago()) <= $xF->getInt( $xCred->getFechaDeMinistracion() )){
		//$FechaUltimoPago		= $FechaAperturaCuenta;
		$xF100					= new cFecha(); 
		$FechaUltimoPago		= $xF100->setSumarDias(1, $xCred->getFechaDeMinistracion()) ;
		//setLog("2....$FechaUltimoPago");
	}
	$FechaUltimaCompra			= $xCR->getDate($xCred->getFechaDeMinistracion() );
	$FechaCierreCuenta			= "";
	$FechaCorte					= $FechaExtraccion;
	$Garantia					= "";		//TODO: Acabar garantia
	$CreditoMaximo				= $xCR->getMonto($xSoc->getCreditoMaximo());
	$SaldoActual				= $xCR->getMonto($xCred->getSaldoActual($FechaFinal));
	//setLog("El saldo actual es $SaldoActual en $CuentaActual de $FechaFinal");
	$LimiteCredito				= $xCR->getMonto($xSoc->getCreditoMaximo());
	$SaldoVencido				= 0;
	$NumeroPagosVencidos		= 0;		//Modificado en el plan de pagos
	//obtener la letra pendiente
	$UltimaLetraPagada			= $xCR->getMonto($xCred->getPeriodoActual());
	//obtener datos de la letra
	$DPlanDePagos				= $xCred->getDatosDelPlanDePagos();
	$NumeroDePlan				= $xCred->getNumeroDePlanDePagos();
	
	$FechaDePrimerIncumplimiento = "";
	
	
	if($SaldoActual <= 0){
		$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tEl Monto a pagar : $MontoPagar se lleva a 0, el Saldo $SaldoActual\r\n");
		$MontoPagar					= 0;	//2018-07-09
		//setLog("Va el monto de la parcialidad $MontoPagar por $SaldoActual ---- $CuentaActual");
		//$xCred->getFechaUltimoDePago() 
		if($xF->getInt($FechaUltimoPago) <= $xF->getInt( $xCred->getFechaDeMinistracion() )){
			$FechaClave 			= $xF->setSumarDias(1, $xCred->getFechaDeMinistracion());
			$FechaCierreCuenta		= $FechaClave;
			$FechaAperturaCuenta	= $xCred->getFechaDeMinistracion();
			$FechaUltimoPago		= $FechaCierreCuenta;
			$xOfic->addNote(MEMOS_TIPO_PENDIENTE, false, $xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), "Fechas Incorrectas en Pagos($FechaUltimoPago) y Ministracion($FechaAperturaCuenta)");
			$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\t$icnt\tFechas Incorrectas en Pagos($FechaUltimoPago) y Ministracion($FechaAperturaCuenta)\r\n", $xLog->DEVELOPER);
			//setLog("3....$FechaUltimoPago");
		} else {
			$FechaCierreCuenta		= $FechaUltimoPago;
		}
		//$FechaUltimaCompra			= $xCR->getDate($xCred->getFechaDeMinistracion() );
		//$FechaAperturaCuenta		= $xCR->getDate($xCred->getFechaDeMinistracion() );
		$FechaCierreCuenta			= $xCR->getDate($FechaCierreCuenta);
	} else {
		if($MontoPagar<=0){
			$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tEl Monto a pagar es cero: $MontoPagar y el Saldo $SaldoActual\r\n");
			$MontoPagar				= $SaldoActual;
		}
	}
	if($xF->getInt($FechaAperturaCuenta) >= $xF->getInt($FechaUltimoPago)){
		$FechaClave = ($xF->getInt($FechaUltimoPago) > $xF->getInt($xCred->getFechaDeMinistracion()) ) ? $FechaUltimoPago : $xCred->getFechaDeMinistracion();
		$FechaAperturaCuenta	= $xCR->getDate( $xF->setRestarDias(3, $FechaClave) );
		$xOfic->addNote(MEMOS_TIPO_PENDIENTE, false, $xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), "Fechas Incorrectas en Pagos($FechaUltimoPago) y Ministracion($FechaAperturaCuenta)");
		$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\t$icnt\tFechas Incorrectas en Pagos($FechaUltimoPago) y Ministracion($FechaAperturaCuenta)\r\n", $xLog->DEVELOPER);
	}
	//=================================== Formatear
	
	
	$FechaAperturaCuenta	= $xCR->getDate($FechaAperturaCuenta);
	$FechaUltimoPago		= $xCR->getDate($FechaUltimoPago);
							
	$PagoActual					= "V";
	
	if($SaldoActual > (TOLERANCIA_SALDOS + 0.01) ){
		//AND ($TipoEnSistema == CREDITO_PRODUCTO_NOMINA) 2017-03-03
		$EstadoDeCredito	= $xCred->getEstadoActual();
		
		//Inicializar Notas SIC
		$xNotaSIC			= new cCreditosNotasSIC();
		if($xNotaSIC->initByCredito($xCred->getClaveDeCredito())  == true){
			$EstadoDeCredito	= $xNotaSIC->getEstadoForzado();
			$ClavePrevencion	= $xNotaSIC->getClaveDeNota();
		}
		
		
		if($EstadoDeCredito  == CREDITO_ESTADO_MOROSO OR $EstadoDeCredito == CREDITO_ESTADO_VENCIDO ){
			if($TipoEnSistema == CREDITO_PRODUCTO_NOMINA){
				$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tSe Ignora por ser Nomina\r\n", $xLog->DEVELOPER);
			} else {
				if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
					$PagoActual						= "01";
					$SaldoVencido					= $SaldoActual;
					$NumeroPagosVencidos			= "01";
					$FechaDePrimerIncumplimiento	= $xCR->getDate($xCred->getFechaDeVencimiento()); //TODO: Corregir .- Debe validarse el estatus y verificar si esta vencido, si no... reportarse vacio
					
				} else {
					if( setNoMenorQueCero($xCred->getNumeroDePlanDePagos()) > 0){
						$xPlan					= $xCred->getOPlanDePagos();// new cPlanDePagos($xCred->getNumeroDePlanDePagos());
						$data					= false;
						if(isset($DCal[$idcredito])){
							 $data				= $DCal[$idcredito];
							 unset($DCal[$idcredito]);
						}
						$xPlan->initParcsPendientes(0, $FechaFinal, $data);
						$NumeroPagosVencidos	= $xPlan->getPagosAtrasados();
						$SaldoVencido			= $xPlan->getMontoAtrasado();
						//Objeto Pago Actual
						$idpago_actual			= $xPlan->getPeriodoProximoSegunFecha();
						$idpago_actual			= ($idpago_actual == false) ? $xCred->getPeriodoActual() + 1 : $idpago_actual;
						if($idpago_actual == false){
							$PagoActual			= "V";
						} else {
							
							$xLetra				= $xPlan->getOLetra($idpago_actual);
							//calcular periodos vencidos
							$idletra_dias_vencidos	= setNoMenorQueCero( $xF->setRestarFechas($FechaFinal, $xLetra->getFechaDePago()) );
							$idletra_periodos		= floor( $idletra_dias_vencidos / $xCred->getPeriocidadDePago() );
							$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tFecha Proxima de Pago: " . $xLetra->getFechaDePago() . " Dias Vencidos $idletra_dias_vencidos, Pagos vencidos $idletra_periodos\r\n", $xLog->DEVELOPER);
							if($idletra_periodos > 0){
								$PagoActual			= sprintf("%02d", $idletra_periodos);
							} else {
								$PagoActual			= "V";
							}
						}
	
						$FechaDePrimerIncumplimiento	= $xCR->getDate($xPlan->getFechaPrimerAtraso());
					} else {
						$PagoActual					= "V";			//TODO: 2014-12-10
					}
				}
			}
		}
		$SaldoVencido				= $xCR->getMonto($SaldoVencido);
		if($SaldoVencido > (TOLERANCIA_SALDOS + 0.01)){
			$TotalSaldosVencidos	= 1;
		} else {
			$TotalSaldosVencidos	= 0;
			$PagoActual				= "V";
			$SaldoVencido			= 0;
			$NumeroPagosVencidos	= "";
			$FechaDePrimerIncumplimiento		= "";			
		}
	
	}

	/* 2014-08-03 */
	if($SaldoVencido > 1 AND $PagoActual == "V"){
		if(setNoMenorQueCero($NumeroPagosVencidos) <= 0){
			$PagoActual		= "01";
		} else {
			$PagoActual		= "$NumeroPagosVencidos";
		}
	}
	if($SaldoActual > (TOLERANCIA_SALDOS + 0.01) ){
		if(setNoMenorQueCero($PagoActual) > $xCred->getPagosAutorizados() ){
			//pago actual reset
			//$xMs	= new cNotificaciones();
			$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\t($PagoActual . $NumeroPagosVencidos)ERROR\tCredito " . $xCred->getNumeroDeCredito() . " saldo $SaldoActual\r\n", $xLog->DEVELOPER );
			$PagoActual		= "01";//sprintf("%02d", setNoMenorQueCero(($xCred->getPagosAutorizados() - $NumeroPagosVencidos)) );
		}	
	}
	/* 2018-07-09 */ //===================================================================================================================
	if($MontoPagar<=0 AND $SaldoActual>0){
		$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tError. El Monto a pagar es $MontoPagar y el Saldo $SaldoActual\r\n");
		$MontoPagar		= $SaldoActual;
	}
	if($persona > DEFAULT_SOCIO AND MODO_DEBUG == true){
		$xLog->add("OK\t$idpersona-$idcredito\t$sucres\tEl Monto a pagar es $MontoPagar y el Saldo $SaldoActual\r\n");
	}
	if($SaldoVencido > $SaldoActual){ $SaldoVencido = $SaldoActual; }

	$HistoricoPagos			= "";

	
	
	$TotalPagosReportados		= 0; //Total de pagos hechos por el cliente durante el periodo del reporte
	$ClaveOtorganteAnterior		= ""; //ENTIDAD_CLAVE_SIC;
	$NombreOtorganteAnterior	= ""; //
	$NumeroOtorganteAnterior	= "";
	
	$linea						.= "$ClaveActualOtorgante|$NombreOtorgante|$CuentaActual|$TipoDeResponsabilidad|$TipoDeCuenta|$TipoDeContrato|$ClaveUnidadMonetaria|$ValorActivoValuacion|";
	$linea						.= "$NumeroDePagos|$FrecuenciaDePagos|$MontoPagar|$FechaAperturaCuenta|$FechaUltimoPago|$FechaUltimaCompra|$FechaCierreCuenta|$FechaCorte|";
	$linea						.= "$Garantia|$CreditoMaximo|$SaldoActual|$LimiteCredito|$SaldoVencido|$NumeroPagosVencidos|$PagoActual|$HistoricoPagos|$ClavePrevencion|";
	$linea						.= "$TotalPagosReportados|$ClaveOtorganteAnterior|$NombreOtorganteAnterior|$NumeroOtorganteAnterior|";
	
	
	$SaldoInsoluto				= $xCR->getMonto($xCred->getSaldoActual($FechaFinal));
	
	$linea						.= "$FechaDePrimerIncumplimiento|$SaldoInsoluto|";
	

	$NombreOtorgante				= $xCR->getNombreOtorgante();
	$DomicilioDevolucion			= ""; //str_replace(",", ";",  EACP_DOMICILIO_CORTO);
	/* 2018-03-02 */
	
	//===== Nueva version
	//MontoUltimoPago|FechaIngresoCarteraVencida|MontoCorrespondienteIntereses|
	//FormaPagoActualIntereses|DiasVencimiento|PlazoMeses|MontoCreditoOriginacion|CorreoElectronicoConsumidor
	$MontoUltimoPago				= $xCR->getMonto($xCred->getMontoUltimoPago());
	$FechaIngresoCarteraVencida		= ($PagoActual == "V") ? "" : $xCR->getDate($xCred->getFechaDeMora());
	$MontoCorrespondienteIntereses	= "0";
	$FormaPagoActualIntereses		= "";
	$DiasVencimiento				= ($PagoActual == "V") ? "" : $xCred->getDiasDeMora($FechaCorte);
	$PlazoMeses						= $xCred->getPlazoEnMeses();
	$MontoCreditoOriginacion		= $xCR->getMonto( $xCred->getMontoSolicitado());
	$CorreoElectronicoConsumidor	= $xSoc->getCorreoElectronico();
	
	$linea							.= "$MontoUltimoPago|$FechaIngresoCarteraVencida|$MontoCorrespondienteIntereses|$FormaPagoActualIntereses|$DiasVencimiento|$PlazoMeses|$MontoCreditoOriginacion|$CorreoElectronicoConsumidor|";
	//============================== ELEMENTOS DE CONTROL
	$linea					.= "$TotalSaldosActuales|$TotalSaldosVencidos|$TotalElementosNombres|$TotalElementosDireccion|$TotalElementosEmpleo|$TotalElementosCuenta|$NombreOtorgante|$DomicilioDevolucion";
	//
	if($xSoc->getEsPersonaFisica() == true OR $cualquiera == true){
		if($toJson == true){
			$arrLinea		= explode("|", $linea);
			$jsonNew		= array();
			foreach ($itemJson as $ix => $item){
				if(!isset($jsonNew[$item])){
					$jsonNew[$item] = (isset($arrLinea[$ix])) ? $arrLinea[$ix] : "ERROR";
					
				}
				//eliminar no estudiados
				//unset($jsonNew[""]);
				unset($jsonNew["DOMICILIODEVOLUCION"]);
				unset($jsonNew["GARANTIA"]);
				unset($jsonNew["CLAVEOTORGANTE"]);
				unset($jsonNew["NOMBREOTORGANTE"]);
				unset($jsonNew["APELLIDOADICIONAL"]);
				unset($jsonNew["IDENTIFICADORDEMEDIO"]);
				unset($jsonNew["NOTAOTORGANTE"]);
				unset($jsonNew["VERSION"]);
				unset($jsonNew["RESIDENCIA"]);
				unset($jsonNew["NUMEROLICENCIACONDUCIR"]);
				unset($jsonNew["SEXO"]);
				unset($jsonNew["NACIONALIDAD"]);
				unset($jsonNew["TIPOPERSONA"]);
				unset($jsonNew["FAX"]);
				unset($jsonNew["EXTENSION"]);
				unset($jsonNew["TOTALPAGOSREPORTADOS"]);
				unset($jsonNew["CLAVEANTERIOROTORGANTE"]);
				unset($jsonNew["NOMBREANTERIOROTORGANTE"]);
				unset($jsonNew["NUMEROCUENTAANTERIOR"]);
				unset($jsonNew["TOTALELEMENTOSNOMBREREPORTADOS"]);
				unset($jsonNew["TOTALELEMENTOSDIRECCIONREPORTADOS"]);
				unset($jsonNew["TOTALELEMENTOSEMPLEOREPORTADOS"]);
				unset($jsonNew["TOTALELEMENTOSCUENTAREPORTADOS"]);
				unset($jsonNew["NUMERODEPENDIENTES"]);
				unset($jsonNew["CLAVEMONEDA"]);
				//Empleo
				unset($jsonNew["FECHAULTIMODIAEMPLEO"]);
				unset($jsonNew["FECHAVERIFICACIONEMPLEO"]);
				unset($jsonNew["ORIGENRAZONSOCIALDOMICILIO"]);
				unset($jsonNew["CLAVEUNIDADMONETARIA"]);
				unset($jsonNew["ORIGENDOMICILIO"]);
				unset($jsonNew["HISTORICOPAGOS"]);
							
			}
			$lineaJson[]		= $jsonNew;
		} else {
			echo $linea . "\r\n";
		}
		
	} else {
		//OMITIDO
		$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\tOmitir por ser Persona Moral " . $xSoc->getNombre() . "\r\n", $xLog->DEVELOPER );
	}
	$icnt++;
	/* 2017-03-08.- Elimina la memoria lo que no haga falta */
	
	$DCal[$idcredito]		= null;
	unset($DCal[$idcredito]);
	
	$DPagos[$idcredito] 	= null;
	unset($DPagos[$idcredito]);
	
	$linea					= null;
	$xSoc					= null;
	$xCred					= null;
	
	//setError($idcredito . " - " . getMemoriaLibre(true) . "M");
	/*if($icnt >= 500){ 
		//setError($_SESSION["memcache.errors"]);
		//memprof_dump_callgrind(fopen("/tmp/callgrind.out." . time() , "w"));
		break;
	}*/
}

//elimina el record
$DCal	= null;
$DPagos	= null;
$icnt	= null;


//

if(MODO_DEBUG AND $persona <= DEFAULT_SOCIO AND $creditoref<= DEFAULT_CREDITO){
	$xFil	= new cFileLog();
	$xFil->setWrite($xLog->getMessages());
	$xFil->setSendToMail($xHP->getTitle(), ADMIN_MAIL);
}

if($toJson == true){
	$xHP		= new cHPage("TR.VISOR CONSULTA", HP_FORM);
	$xHP->init();
	
	$xFRM		= new cHForm("frm", "./");
	$xDiv1		= new cHDiv("tx1", "idhvals");
	
	$data		= json_encode($lineaJson);
	
	$xFRM->addCerrar();
	
	$xFRM->addLog($xLog->getMessages());
	
	$xFRM->addHElem($xDiv1->get($data));
	

	echo $xFRM->get();
	?>
	    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Quicksand" />
	    <link rel="stylesheet" type="text/css" href="../css/pretty-json.css" />
	    <script type="text/javascript" src="../js/underscore-min.js" ></script>
	    <script type="text/javascript" src="../js/backbone-min.js" ></script>
	    <script type="text/javascript" src="../js/pretty-json-min.js" ></script>
	    
	<script>
	$(document).ready(function() {
	var el = {
	        solovals: $('#idhvals'),
	        hsolovals: $('#idhvals'),
	        /*idtodo: $('#idhtodo'),
	        idhtodo: $('#idhtodo')*/
	    };
	//function jsRender(){
		//$("#idtexto").val(session("var.serialize"));
	    var json1 = el.solovals.html();
	    //var json2 = el.idtodo.html();
	
	    var data1;
	    //var data2;
	    try{ 
	    	
	        data1 = JSON.parse(json1);
	        //data2 = JSON.parse(json2);
	    } catch(e){
	    	el.hsolovals.html(data1); 
	    	//el.idhtodo.html(data2);
			alert('JSON Incorrecto');
			return;
		}
	
	    var node = new PrettyJSON.view.Node({ 
	        el:el.hsolovals,
	        data: data1,
	        dateFormat:"DD/MM/YYYY - HH24:MI:SS"
	    });
	    /*var node = new PrettyJSON.view.Node({ 
	        el:el.idhtodo,
	        data: data2,
	        dateFormat:"DD/MM/YYYY - HH24:MI:SS"
	    });   */ 
	//}
	
	});
	</script>
	<style>
		#idtexto {
			min-height: 400px;
		}
	</style>
	<?php
	//$jxc ->drawJavaScript(false, true);
	$xHP->fin();
	//memprof_dump_callgrind(fopen("/tmp/cachegrind.out." . rand(0, 500), "w"));
}
?>
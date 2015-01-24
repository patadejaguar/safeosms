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
ini_set("max_execution_time", 600);

$xHP					= new cHPage("", HP_REPORT);
$mql					= new cSQLListas();
$xF						= new cFecha();
$query					= new MQL();
$xLoc					= new cLocal();
$xLog					= new cCoreLog();

$xCR					= new cReporteCirculoDeCredito_tipo();
$ClaveOtorgante			= $xCR->getClaveDeOtorgante();
$NombreOtorgante		= $xCR->getNombreOtorgante();

$ByPersona1				= "";
$ByPersona2				= "";
$ByPersona3				= "";

$FechaInicial			= (isset($_GET["on"])) ?  $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
$FechaFinal				= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
$toJson					= false;//parametro("beauty", false, MQL_BOOL);
$lineaJson				= array();
$itemJson				= array();

$FechaExtraccion		= date("Ymd", strtotime($FechaFinal));
$estatus_actual 		= parametro("f2", false, MQL_INT);
$persona				= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

if($persona != DEFAULT_SOCIO){
	$ByPersona2			= " AND	(`creditos_solicitud`.`numero_socio` = $persona) ";
	$ByPersona1			= " AND (`creditos_abonos_parciales`.`socio_afectado` = $persona ) ";
	$ByPersona3			= " AND (`socio_afectado` = $persona ) ";
	$toJson				= true;
}
$xDB					= new cSAFEData();
$xT						= new cTipos();
$xF						= new cFecha();




//header("Content-type: text/plain");
//header("Content-type: application/csv");
if($toJson == true){
	header("Content-type: text/plain");
} else {
	header("Content-type: text/x-csv");
	header("Content-Disposition: attachment; filename=" . urlencode($ClaveOtorgante . "_" . $NombreOtorgante . "_" . $FechaExtraccion) . ".txt");
}

//Linea de Encabezados
$strHEAD				= strtoupper("ClaveOtorgante|NombreOtorgante|IdentificadorDeMedio|FechaExtraccion|NotaOtorgante|Version|ApellidoPaterno|ApellidoMaterno|ApellidoAdicional|Nombres|FechaNacimiento|RFC|CURP|Nacionalidad|Residencia|NumeroLicenciaConducir|EstadoCivil|Sexo|ClaveElectorIFE|NumeroDependientes|FechaDefuncion|IndicadorDefuncion|TipoPersona|Direccion|ColoniaPoblacion|DelegacionMunicipio|Ciudad|Estado|CP|FechaResidencia|NumeroTelefono|TipoDomicilio|TipoAsentamiento|NombreEmpresa|Direccion|ColoniaPoblacion|DelegacionMunicipio|Ciudad|Estado|CP|NumeroTelefono|Extension|Fax|Puesto|FechaContratacion|ClaveMoneda|SalarioMensual|FechaUltimoDiaEmpleo|FechaVerificacionEmpleo|ClaveActualOtorgante|NombreOtorgante|CuentaActual|TipoResponsabilidad|TipoCuenta|TipoContrato|ClaveUnidadMonetaria|ValorActivoValuacion|NumeroPagos|FrecuenciaPagos|MontoPagar|FechaAperturaCuenta|FechaUltimoPago|FechaUltimaCompra|FechaCierreCuenta|FechaCorte|Garantia|CreditoMaximo|SaldoActual|LimiteCredito|SaldoVencido|NumeroPagosVencidos|PagoActual|HistoricoPagos|ClavePrevencion|TotalPagosReportados|ClaveAnteriorOtorgante|NombreAnteriorOtorgante|NumeroCuentaAnterior|FechaPrimerIncumplimiento|SaldoInsoluto|TotalSaldosActuales|TotalSaldosVencidos|TotalElementosNombreReportados|TotalElementosDireccionReportados|TotalElementosEmpleoReportados|TotalElementosCuentaReportados|NombreOtorgante|DomicilioDevolucion");
if($toJson == true){
	$itemJson			= explode("|", $strHEAD);
} else {
	echo $strHEAD . "\r\n";
}
//exit("SELECT * FROM `creditos_abonos_parciales` WHERE	(`creditos_abonos_parciales`.`fecha_de_pago` <='$FechaFinal')");
$rsPagos		= $query->getDataRecord( "SELECT * FROM `creditos_abonos_parciales` WHERE	(`creditos_abonos_parciales`.`fecha_de_pago` <='$FechaFinal') $ByPersona1");
$DPagos			= array();
foreach ($rsPagos as $dpags ){
	$credito	= $dpags["docto_afectado"];
	$DPagos[$credito][]	= $dpags;
}

$rsCal			= $query->getDataRecord("SELECT * FROM `letras` WHERE	(`fecha_de_pago` <='$FechaFinal') $ByPersona3");
$DCal			= array();
foreach ($rsCal as $dscal ){
	$credito	= $dscal["docto_afectado"];
	$DCal[$credito][]	= $dscal;
}
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
$TotalSaldosActuales				= 1;
$TotalSaldosVencidos				= 0;
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

$IdentificadorMedio		= 1;
$NotaOtorgante			= "";
$xSTb					= new cSAFETabla(TCREDITOS_REGISTRO);
$sql 			= $mql->getQueryInicialDeCreditos("", "", " 	AND (`creditos_solicitud`.`fecha_ministracion` <= '" . $FechaFinal . "' )
				AND
				(`creditos_solicitud`.`numero_socio` !=" . DEFAULT_SOCIO . ")
				AND
				(`creditos_solicitud`.`monto_autorizado` > " . TOLERANCIA_SALDOS .  ")
				AND	
				(`creditos_solicitud`.`estatus_actual` !=" . CREDITO_ESTADO_AUTORIZADO . " AND `creditos_solicitud`.`estatus_actual` !=" . CREDITO_ESTADO_SOLICITADO .")  
				$ByPersona2
				");

//exit($sql);

$arrEquivTipoDom		= array(
						1 => "C", 2 => "N", 3 => "E", 99 => "O"
						);						
$arrEquivTipoRes		= array(1 => 1, 2=> 2, 3 => 4, 4 => 3, 99 => "");
$arrEquivEstadoCiv		= array(1 => "C" , 2 => "S", 3 => "D", 4 => "L", 5 => "", 6 => "V", 7 => "E",99 => "");
$arrEquivGenero			= array(1 => "M", 2=> "F", 99 => "");

$version				= 2;

$datos					= $query->getDataRecord($sql);
$icnt					= 0;

foreach($datos as $rw){
	$linea					= "$ClaveOtorgante|$NombreOtorgante|$IdentificadorMedio|$FechaExtraccion|$NotaOtorgante|$version|";
	
	$idcredito				= $rw["numero_solicitud"];
	$idpersona				= $rw["numero_socio"];
	$xSoc					= new cSocio( $idpersona );
	
	
	$xSoc->init();
	$DSoc					= $xSoc->getDatosInArray();
	$xDom					= $xSoc->getDatosDomicilio();
	$xDAEconom				= $xSoc->getDatosActividadEconomica();
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
	$FechaNacimiento		= $xCR->getDate($xSoc->getFechaDeNacimiento() );// date("Ymd", strtotime($DSoc["fechanacimiento"]) );
	if($xSoc->getEdad() < 15 ){
		$FechaNacimiento		= ""; //$xSoc->getFechaDeNacimiento() . "[" . $xSoc->getEdad() . "]";
	}
	$RFC					= $xCR->getText($xSoc->getRFC(true));
	$CURP					= $xCR->getText($xSoc->getCURP(true));
	$Nacionalidad			= $xCR->getText($xSoc->getPaisDeOrigen() );
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
	$linea					.= "$ApellidoPaterno|$ApellidoMaterno|$ApellidoAdicional|$Nombres|$FechaNacimiento|$RFC|$CURP|$Nacionalidad|$Residencia|$licencia|$EstadoCivil|$Sexo|";
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
	    $linea					.= "$Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|";
	    $xLog->add("ERROR\t$idpersona-$idcredito\t$sucres\tSin Domicilio $Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|\r\n", $xLog->DEVELOPER);
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

			$xLog->add("ERROR\t$idpersona-$idcredito\t$sucres\tEn datos de Domicilio $Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|\r\n", $xLog->DEVELOPER);
		}
		$CP							= $xCR->getText($CP, 5);
		
		$xColonia->existe($CP, "", "", true);
		
		
		
		$FechaResidencia			= "";
		$NumeroTelefono				= $xSoc->getTelefonoPrincipal();
		$TipoDomicilio				= $xCR->getETipoDomicilio( $ODom->getTipoDeDomicilio() ); // (isset($arrEquivTipoDom[ $xDom["tipo_domicilio"] ])) ? $arrEquivTipoDom[ $xDom["tipo_domicilio"] ] : "";
		$TipoAsentamiento			= $xCR->getETipoColonia( trim($xColonia->getTipoDeAsentamiento()) );
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
		$linea					.= "$Direccion|$ColoniaPoblacion|$DelegacionMunicipio|$Ciudad|$Estado|$CP|$FechaResidencia|$NumeroTelefono|$TipoDomicilio|$TipoAsentamiento|";
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
	    //$OActE->init();
	    $ColoniaEmpresa			= $xCR->getText($OActE->getNombreColonia());
	    $MunicipioEmpresa		= $xCR->getText($OActE->getNombreMunicipio() );
	    $CiudadEmpresa			= "";//$xCR->getText($OActE->getLocalidad() );
	    $EstadoEmpresa			= $xCR->getText( $OActE->getClaveDeEstadoEnSIC() ); //parche
	    $CPEmpresa				= $xCR->getText($OActE->getCodigoPostal(), 5);
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
		    if( $xDAEconom["estado_actual"] == 1 ){	$FechaVerificaEmp	= $xDAEconom["fecha_de_verificacion"];	}
		}
	}
	if($DomEmpresa == false){
		$NombreEmpresa		= "NO PROPORCIONADO";
		$DireccionEmpresa		= "";
		$ColoniaEmpresa			= ""; //$xDAEconom["domicilio_ae"];
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
	}
	$linea			.= "$NombreEmpresa|$DireccionEmpresa|$ColoniaEmpresa|$MunicipioEmpresa|$CiudadEmpresa|$EstadoEmpresa|$CPEmpresa|$TelefonoEmpresa|";
	$linea			.= "$ExtensionEmpresa|$FaxEmpresa|$PuestoEmpresa|$FContratoEmpresa|$ClaveMonedaEmp|$SalarioMensual|$FechaUltimoEmpresa|$FechaVerificaEmp|";	
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
	//obtener parametros extras en una array
	//su valor es del producto
	
	$xCred						= new cCredito($idcredito, $rw["numero_socio"]);
	$xCred->init($rw);
	if( isset($DPagos[$idcredito]) ){
		$xCred->initPagosEfectuados( $DPagos[$idcredito] , $FechaFinal);
		///$xLog->add("OK\t$icnt\tDatos del credito $idpersona|$idcredito SI existen \r\n", $xLog->DEVELOPER);
	} else {
		$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\t$icnt\tDatos de Pago del credito $idpersona|$idcredito no existen \r\n", $xLog->DEVELOPER);
	}
	
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
	$ValorActivoValuacion		= 0;
	$NumeroDePagos				= $xCred->getPagosAutorizados();
	$FrecuenciaDePagos			= $xCR->getEPeriocidad( $xCred->getPeriocidadDePago() );
	$MontoPagar					= $xCR->getMonto($xCred->getSaldoActual($FechaFinal));		//Acabar, valor de la letra actual o saldo?
	$FechaAperturaCuenta		= $xCR->getDate($xCred->getFechaDeMinistracion() );
	$FechaUltimoPago			= $xCR->getDate( $xCred->getFechaUltimoDePago() );
	//setLog("1....$FechaUltimoPago");
	if($xF->getInt($xCred->getFechaUltimoDePago()) >= $xF->getInt( $xCred->getFechaDeMinistracion() )){
		//$FechaUltimoPago		= $FechaAperturaCuenta;
		$xF100					= new cFecha(); 
		$FechaAperturaCuenta	= $xCR->getDate( $xF100->setRestarDias(2, $xCred->getFechaDeMinistracion()) );
		$FechaUltimoPago		= $xCR->getDate( $xF100->setRestarDias(1, $xCred->getFechaDeMinistracion()) );
		//setLog("2....$FechaUltimoPago");
	}
	$FechaUltimaCompra			= $xCR->getDate($xCred->getFechaDeMinistracion() );
	$FechaCierreCuenta			= "";
	$FechaCorte					= $FechaExtraccion;
	$Garantia					= "";		//TODO: Acabar garantia
	$CreditoMaximo				= $xCR->getMonto($xSoc->getCreditoMaximo());
	$SaldoActual				= $xCR->getMonto($xCred->getSaldoActual($FechaFinal));
	$LimiteCredito				= $xCR->getMonto($xSoc->getCreditoMaximo());
	$SaldoVencido				= 0;
	$NumeroPagosVencidos		= 0;		//Modificado en el plan de pagos
	//obtener la letra pendiente
	$UltimaLetraPagada			= $xCR->getMonto($xCred->getPeriodoActual());
	//obtener datos de la letra
	$DPlanDePagos				= $xCred->getDatosDelPlanDePagos();
	$NumeroDePlan				= $xCred->getNumeroDePlanDePagos();
	
	$FechaDePrimerIncumplimiento		= "";
	
	if($SaldoActual <= 0){ 
		if($xF->getInt($xCred->getFechaUltimoDePago()) >= $xF->getInt( $xCred->getFechaDeMinistracion() )){
			$FechaCierreCuenta		= $xCR->getDate( $xCred->getFechaDeMinistracion() );
			$FechaAperturaCuenta	= $xCR->getDate( $xF->setRestarDias(3, $xCred->getFechaDeMinistracion()) );
			$FechaUltimoPago		= $xCR->getDate( $xF->setRestarDias(2, $xCred->getFechaDeMinistracion()) );
			//setLog("3....$FechaUltimoPago");
		} else {
			$FechaCierreCuenta		= $xCR->getDate( $xCred->getFechaUltimoDePago() );
		}
		//$FechaUltimaCompra			= $xCR->getDate($xCred->getFechaDeMinistracion() );
		//$FechaAperturaCuenta		= $xCR->getDate($xCred->getFechaDeMinistracion() );
	}

	$PagoActual					= "V";
	if($SaldoActual > (TOLERANCIA_SALDOS + 0.01) ){
		if($xCred->getEstadoActual() == CREDITO_ESTADO_MOROSO OR $xCred->getEstadoActual() == CREDITO_ESTADO_VENCIDO ){
			if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
				$PagoActual						= "01";
				$SaldoVencido					= $SaldoActual;
				$NumeroPagosVencidos			= "01";
				$FechaDePrimerIncumplimiento	= $xCR->getDate($xCred->getFechaDeVencimiento());
			} else {
				if( setNoMenorQueCero($xCred->getNumeroDePlanDePagos()) > 0){
					$xPlan					= $xCred->getOPlanDePagos();// new cPlanDePagos($xCred->getNumeroDePlanDePagos());
					$data					= (isset($DCal[$idcredito])) ? $DCal[$idcredito] : false;
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
			$xLog->add("WARN\t$idpersona-$idcredito\t$sucres\t($PagoActual . $NumeroPagosVencidos)ERROR\tCredito " . $xCred->getNumeroDeCredito() . " Saldo $SaldoActual\r\n", $xLog->DEVELOPER );
			$PagoActual		= "01";//sprintf("%02d", setNoMenorQueCero(($xCred->getPagosAutorizados() - $NumeroPagosVencidos)) );
		}	
	}
	if($SaldoVencido > $SaldoActual){ $SaldoVencido = $SaldoActual; }

	$HistoricoPagos			= "";

	
	$ClavePrevencion			= "";
	$TotalPagosReportados		= 0;
	$ClaveOtorganteAnterior		= ""; //ENTIDAD_CLAVE_SIC;
	$NombreOtorganteAnterior	= ""; //
	$NumeroOtorganteAnterior	= "";
	
	$linea						.= "$ClaveActualOtorgante|$NombreOtorgante|$CuentaActual|$TipoDeResponsabilidad|$TipoDeCuenta|$TipoDeContrato|$ClaveUnidadMonetaria|$ValorActivoValuacion|";
	$linea						.= "$NumeroDePagos|$FrecuenciaDePagos|$MontoPagar|$FechaAperturaCuenta|$FechaUltimoPago|$FechaUltimaCompra|$FechaCierreCuenta|$FechaCorte|";
	$linea						.= "$Garantia|$CreditoMaximo|$SaldoActual|$LimiteCredito|$SaldoVencido|$NumeroPagosVencidos|$PagoActual|$HistoricoPagos|$ClavePrevencion|";
	$linea						.= "$TotalPagosReportados|$ClaveOtorganteAnterior|$NombreOtorganteAnterior|$NumeroOtorganteAnterior|";
	
	
	$SaldoInsoluto				= $xCR->getMonto($xCred->getSaldoActual($FechaFinal));
	
	$linea					.= "$FechaDePrimerIncumplimiento|$SaldoInsoluto|";
	

	$NombreOtorgante				= $xCR->getNombreOtorgante();
	$DomicilioDevolucion				= ""; //str_replace(",", ";",  EACP_DOMICILIO_CORTO);
	
	
	//============================== ELEMENTOS DE CONTROL
	$linea					.= "$TotalSaldosActuales|$TotalSaldosVencidos|$TotalElementosNombres|$TotalElementosDireccion|$TotalElementosEmpleo|$TotalElementosCuenta|$NombreOtorgante|$DomicilioDevolucion";
	//
	if($xSoc->getEsPersonaFisica() == true){
		if($toJson == true){
			$arrLinea		= explode("|", $linea);
			$jsonNew		= array();
			foreach ($itemJson as $ix => $item){
				$jsonNew[$item] = (isset($arrLinea[$ix])) ? $arrLinea[$ix] : "ERORR";
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
}
if(MODO_DEBUG){
	$xFil	= new cFileLog();
	$xFil->setWrite($xLog->getMessages());
	$xFil->setSendToMail($xHP->getTitle(), ADMIN_MAIL);
}
if($toJson == true){
	echo _json_encode($lineaJson, JSON_PRETTY_PRINT);
}
?>

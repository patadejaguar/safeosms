<?php
/**
 * XML Declaracion de IDE mensual
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package taxs
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.captacion.inc.php");
include_once("../core/core.operaciones.inc.php");
ini_set("max_execution_time", 120);

$oficial = elusuario($iduser);

//$fecha_inicial 			= $_GET["on"];

$fecha_final 			= $_GET["off"];
$fecha_inicial			= date("Y-m-" , strtotime($fecha_final) ) . "01";

$NombreEntidad			= EACP_NAME;
$RFC_Declarante			= EACP_RFC;
$Representante_Nombre	= EACP_REP_LEGAL;
$RFC_Representante		= EACP_RFC_REP_LEGAL;
$CURP_Representante		= EACP_CURP_REP_LEGAL;

$ejercicio				= date("Y" , strtotime($fecha_final) );
$periodo				= date("m" , strtotime($fecha_final) );;
header("Content-type: text/plain");

$totalRemanenteDep	= 0;
$totalDeterminados	= 0;
$totalRecaudado		= 0;
$totalOperaciones	= 0;
$totalEnterado		= 0;
$totalExcedente		= 0;
$totalPendiente		= 0;
$fecha_de_corte     = $fecha_final;


echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>

<DeclaracionInformativaAnualIDE denominacion=\"$NombreEntidad\" rfcDeclarante=\"$RFC_Declarante\"
version=\"1.1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"ide_20080818.xsd\">
  <RepresentanteLegal curp=\"$CURP_Representante\" rfc=\"$RFC_Representante\">
    <Nombre>
      <NombreCompleto>$Representante_Nombre</NombreCompleto>
    </Nombre>
  </RepresentanteLegal>
  <Normal ejercicio=\"$ejercicio\"/>
  <InstitucionDeCredito>
    <RegistroDeDetalle>
	<!-- Datos de Personas Fisicas -->
      <PersonaFisica curp=\"\" rfc=\"\">
        <Nombre>
          <NombreCompleto>NombreCompleto</NombreCompleto>
        </Nombre>
        <Domicilio>
          <DomicilioCompleto>DomicilioCompleto</DomicilioCompleto>
        </Domicilio>
      </PersonaFisica>
	<!-- Declaracion de Movimientos por dia -->
      <DepositoEnEfectivo impuestoDeterminado=\"0\" impuestoRecaudado=\"0\" montoExcedente=\"0\" recaudacionPendiente=\"0\">
        <Cuenta cotitulares=\"0\" impuestoRecaudado=\"0\" numeroCuenta=\"\" proporcion=\"0.0\">
          <Movimiento fechaOperacion=\"2001-01-01\" montoOperacion=\"0\" tipoOperacion=\"deposito\"/>
        </Cuenta>
      </DepositoEnEfectivo>
    </RegistroDeDetalle>
    <Totales importeDeterminadoDepositos=\"0\" importeExcedenteDepositos=\"0\" importePendienteDepositos=\"0\" importeRecaudadoDepositos=\"0\" operacionesRelacionadas=\"0\"/>
  </InstitucionDeCredito>
</DeclaracionInformativaAnualIDE> ";


?>
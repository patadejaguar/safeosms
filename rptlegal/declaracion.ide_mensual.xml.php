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
include_once("../core/core.taxs.inc.php");

ini_set("max_execution_time", 120);

$oficial 				= elusuario($iduser);

$fecha	 				= $_GET["off"];
$fecha_inicial			= date("Y-m-" , strtotime($fecha) ) . "01";
$fecha_final			= date("Y-m-t" , strtotime($fecha) );
$NombreEntidad			= EACP_NAME;
$RFC_Declarante			= EACP_RFC;
$Representante_Nombre	= EACP_REP_LEGAL;
$DRepLegal				= explode(" ", $Representante_Nombre, 3);

$RepNom					= $DRelLegal[0];
$RepAP					= $DRelLegal[1];
$RepAM					= $DRelLegal[2];

$RFC_Representante		= EACP_RFC_REP_LEGAL;

$ejercicio				= date("Y" , strtotime($fecha) );
$periodo				= date("m" , strtotime($fecha) );

#header("Content-type: text/plain");
header ("content-type: text/xml");
header("Content-Disposition: attachment;");
header("Content-Disposition: filename=\"\"");


$totalRemanenteDep	= 0;
$totalDeterminados	= 0;
$totalRecaudado		= 0;
$totalOperaciones	= 0;
$totalEnterado		= 0;
$totalExcedente		= 0;
$totalPendiente		= 0;
$fecha_de_corte     = $fecha_final;

$xT					= new cTipos();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>

<DeclaracionInformativaMensualIDE denominacion=\"$NombreEntidad\" rfcDeclarante=\"$RFC_Declarante\" version=\"1.0\" xsi:noNamespaceSchemaLocation=\"ide_20080818.xsd\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
	<RepresentanteLegal rfc=\"$RFC_Representante\">
		<Nombre>
			<Nombres>$RepNom</Nombres>
			<PrimerApellido>$RepAP</PrimerApellido>
			</SegundoApellido>$RepAM</SegundoApellido>
		</Nombre>
	</RepresentanteLegal>
	<Normal ejercicio=\"$ejercicio\" periodo=\"$periodo\"/>

	<InstitucionDeCredito>
";



echo "
		<ReporteDeRecaudacionYEnteroDiaria fechaDeCorte=\"$fecha_de_corte\">
			<RegistroDeDetalle>
";
		$sqlCaracter = "SELECT
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							`operaciones_mvtos`.`socio_afectado`,
							SUM(`operaciones_mvtos`.`afectacion_real`) AS `monto`
						FROM
							`eacp_config_bases_de_integracion_miembros`
							`eacp_config_bases_de_integracion_miembros`
								INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
								ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
								`operaciones_mvtos`.`tipo_operacion`
						WHERE
							(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =7001)
							AND
							(
								(`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
							AND
								(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
							)
						GROUP BY
							`operaciones_mvtos`.`socio_afectado`
						ORDER BY
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` ";
		$rsMSoc			= mysql_query($sqlCaracter, cnnGeneral() );

while ($rx = mysql_fetch_array($rsMSoc) ){
	$socio				= $rx["socio_afectado"];
	$xSoc				= new cSocio($socio);
	$xSoc->init();
	$DSoc				= $xSoc->getDatosInArray();
	$curp				= $DSoc["curp"];
	$rfc				= $DSoc["rfc"];

	$xT					= new cImpuestoIDE();
	$xTi				= new cTipos();

	$nombre				= $xTi->setNoAcentos( $xSoc->getNombreCompleto() );
	$domicilio			= $xSoc->getDomicilio();
	//Ide Individual a la fecha de Corte
	$IDEPend			=  $xT->getIDENoPagado($socio, $fecha_final );

//		$xT				= new cImpuestoIDE();
//		$xF				= new cFecha(0, $fecha);
//		$FechaAnterior	= $xF->getFechaMesAnterior();

	//$IDEPerA		= $xSoc->getIDEPeriodoAnterior($fecha_final);//IDE Periodos anteriores
	//Remanente de periodos anteriores se refiere a la suma de Remanentes en los Meses Anteriores
	$RemPerA			= 0;

	$IDERet				= $xSoc->getIDEPagadoByPeriodo($fecha_final);
	$MontoGrav			= $xSoc->getBaseGravadaIDE($fecha_final);
	$IDECalc			= $xSoc->getIDECalculado($fecha_final);

	$totalExcedente		+= $MontoGrav;
	$totalOperaciones++;
	$totalPendiente		+= $IDEPend;
	$totalRecaudado		+= $IDERet;
	$totalDeterminados	+= $IDECalc;
	$totalRemanenteDep	+= $RemPerA;

echo "
				<PersonaFisica curp=\"$curp\" rfc=\"$rfc\">
					<Nombre>
						<NombreCompleto>$nombre</NombreCompleto>
					</Nombre>
					<Domicilio>
						<DomicilioCompleto>$domicilio</DomicilioCompleto>
					</Domicilio>
				</PersonaFisica>

				<DepositoEnEfectivo impuestoDeterminado=\"$IDECalc\"
                                        remanentePeriodosAnteriores=\"$RemPerA\"
                                        montoExcedente=\"$MontoGrav\"
                                        impuestoRecaudado=\"$IDERet\"
                                        recaudacionPendiente=\"$IDEPend\"
                                        />
";
}

echo "</RegistroDeDetalle>";
//Registra el Pago diario del IDE
$sqlPagos	= "SELECT
			`operaciones_mvtos`.`idoperaciones_mvtos`,
			`operaciones_mvtos`.`fecha_operacion`,
			`operaciones_mvtos`.`socio_afectado`,
			`operaciones_mvtos`.`docto_afectado`,
			`operaciones_mvtos`.`recibo_afectado`,
			`operaciones_mvtos`.`tipo_operacion`,
			`operaciones_mvtos`.`afectacion_real`
		FROM
			`operaciones_mvtos`
		WHERE
			(`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
			AND
			(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
			AND
			(`operaciones_mvtos`.`tipo_operacion` = 9301 )
		ORDER BY
			`operaciones_mvtos`.`fecha_operacion` ";
//echo $sqlPagos;
	$rsPagos		= mysql_query($sqlPagos, cnnGeneral() );

while ( $rwPagos	= mysql_fetch_array($rsPagos) ){
	$fecha_de_pago	= $rwPagos["fecha_operacion"];
	$monto			= $rwPagos["afectacion_real"];
	$numero_operac	= $rwPagos["idoperaciones_mvtos"];
	$recibo			= $rwPagos["recibo_afectado"];

	$sqlOps			= "SELECT
					        numero_de_documento
					        recibo_relacionado,
					        fecha_expedicion,
					        cuenta_bancaria
					    FROM bancos_operaciones
					WHERE recibo_relacionado = $recibo
						AND fecha_expedicion ='$fecha_de_pago'
						AND tipo_operacion = 'retiro'
					LIMIT 0,1 ";
	$DOps			= obten_filas($sqlOps);
	$operacion		= $DOps["numero_de_documento"];
	$banco			= $DOps["cuenta_bancaria"];
	$xBanc			= new cCuentaBancaria($banco);
	$DBanc			= $xBanc->getDatosInArray();

	$banco_nom		= $DBanc["nombre_de_la_entidad"];
	$banco_rfc		= $DBanc["rfc_de_la_entidad"];

	$totalEnterado	+= $monto;
	$totalOperaciones++;
echo "
			<EnteroPropio noOperacion=\"$operacion\" impuestoEnterado=\"$monto\" fechaEntero=\"$fecha_de_pago\" nombreInstitucion=\"$banco_nom\" rfcInstitucion=\"$banco_rfc\"/>
";
}

	echo "
		</ReporteDeRecaudacionYEnteroDiaria>
		<Totales importeRemanenteDepositos=\"$totalRemanenteDep\" importeCheques=\"0\" importeDeterminadoDepositos=\"$totalDeterminados\" importeRecaudadoDepositos=\"$totalRecaudado\"
		importePendienteRecaudacion=\"$totalPendiente\" operacionesRelacionadas=\"$totalOperaciones\" importeExcedenteDepositos=\"$totalExcedente\" importeEnterado=\"$totalEnterado\"/>
	</InstitucionDeCredito>
</DeclaracionInformativaMensualIDE>";

?>
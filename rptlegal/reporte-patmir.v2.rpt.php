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
$xHP		= new cHPage("REPORTE DE ", HP_REPORT);
$xHP->setIncludes();
	
$fechaDeInicio		= ( isset($_GET["on"]) ) ? $_GET["on"] : fechasys();
$fechaFinal			= ( isset($_GET["off"]) ) ? $_GET["off"] : fechasys();

$oficial = elusuario($iduser);

$xHP->setNoDefaultCSS();

$xHP->addCSS( CSS_REPORT_FILE );

$arrEquivGenero			= array(1 => "M", 2=> "F", 99 => "");

$sql 		= "SELECT
					`socios_cajalocal`.`clave_de_centro`,
					`socios_general`.`codigo`,
					`socios_general`.`fechanacimiento`,
					`socios_general`.`apellidopaterno`,
					`socios_general`.`apellidomaterno`,
					`socios_general`.`nombrecompleto`,
					`socios_general`.`fechaalta`,
					`socios_general`.`genero` 
				FROM
					`socios_general` `socios_general` 
						INNER JOIN `socios_cajalocal` `socios_cajalocal` 
						ON `socios_general`.`cajalocal` = `socios_cajalocal`.
						`idsocios_cajalocal` 
				WHERE
					(`socios_general`.`fechaalta` >='$fechaDeInicio')
					AND
					(`socios_general`.`fechaalta` <='$fechaFinal') ";

$rs			= getRecordset($sql);
header("Content-type: text/x-csv");
//header("Content-type: text/csv");
//header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=reporte-patmir-II.del_$fechaDeInicio.al_$fechaFinal.csv");
//echo $xHP->getHeader();

//echo $xHP->setBodyinit("initComponents();");


//echo getRawHeader();

/*
<!-- -->
<table       >
	<thead>
		<tr>
			<th colspan="3">REPORTE DE</th>
		</tr>
<!-- DATOS GENERALES DEL REPORTE  -->
		<tr>
			<td  >&nbsp;</td>
			<td width="20%">Fecha de Elaboracion:</td>
			<td width="30%"><?php echo fecha_larga(); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Preparado por:</td>
			<td><?php echo $oficial; ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		</tr>

	</thead>
</table> */

$cvs			= "";
$xT				= new cTipos();

$xFile			= new cFileLog("reporte-patmir-II.del_$fechaDeInicio.al_$fechaFinal", true);

while($r		= mysql_fetch_array($rs)){
	//pacceso-socio-cont
	$punto_de_acceso	= $r["clave_de_centro"];
	$socio				= $r["codigo"];
	$xSoc				= new cSocio($socio);
	$xSoc->init();
	
	$fechaNac			= date("Y/m/d" , strtotime($r["fechanacimiento"]));
	$tipoDato			= "PERSONA";
	$primerApellido		= $r["apellidopaterno"];
	$segundoApellido	= $r["apellidomaterno"];
	$primerApellido		= ( strlen($primerApellido) == 0) ? $segundoApellido : $primerApellido;
	$segundoApellido	= ( strlen($primerApellido) == 0) ? "" : $segundoApellido;
	$nombres			= explode(" ", $r["nombrecompleto"]);
	
	$primerNombre		= $nombres[0];
	$segundoNombre		= ( isset($nombres[1]) ) ? $nombres[1] : "";
	
	$tipoPersona		= "0";
	$fechaInscripcion	= date("Y/m/d" , strtotime($r["fechaalta"]));
	$genero				= $arrEquivGenero[ $r["genero"] ];
	
	//datos del Domicilio
	$DDom				= $xSoc->getDatosDomicilio();
	$calle				= $xT->getCSV( $DDom["calle"] );
	$numero				= $xT->getCSV( $DDom["numero_exterior"]);
	$colonia			= $xT->getCSV( $DDom["colonia"]);
	$xCol				= new cDomiciliosColonias();
	
	$codCol				= $xCol->getClavePorCodigoPostal($DDom["codigo_postal"]);
	$xCol->set($codCol);
	$xCol->init();
	$DCol				= $xCol->getDatosInArray();
	
	$cp					= $xT->cSerial(5, $DDom["codigo_postal"]);
	
	$claveMun			= $DCol["codigo_de_municipio"];
	$claveEnt			= $DCol["codigo_de_estado"];
	
	
	$localidad			= $DDom["localidad"];
	
	$xLoc				= new cDomicilioLocalidad("");
	$claveLoc			= $xLoc->setBuscar($localidad, $claveEnt, $claveMun);
	$xLoc->set($claveLoc);
	$DLoc				= $xLoc->getDatosInArray();	
	$claveLocInegi		= $DLoc["clave_de_localidad"];
	
	$DAports			= $xSoc->getDatosAportaciones();

	$montoParteSoc		= $xSoc->getAportacionesSociales();
	
	$linea				= "$punto_de_acceso,$socio,$tipoDato,$fechaNac,$primerApellido,$segundoApellido,$primerNombre,$segundoNombre,$tipoPersona,$fechaInscripcion";
	$linea				.= ",$genero,$calle,$numero,$colonia,$cp,$claveLocInegi,$montoParteSoc,\r\n";
	echo $linea;
}



//echo getRawFooter();
//echo $xHP->setBodyEnd();
/*
<script  >

function initComponents(){
	window.print();
}
</script>
 */

//$xHP->end(); 
?>
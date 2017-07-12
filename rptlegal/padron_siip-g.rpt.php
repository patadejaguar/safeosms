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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

$oficial = elusuario($iduser);
ini_set("max_execution_time", 360);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="<?php echo CSS_REPORT_FILE; ?>" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<?php
echo getRawHeader();
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<th colspan="3">PADRON DE BENEFICIARIOS SIIP-G</th>
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
</table>
<?php



$arrEstados			= array();
$arrEstadosNum		= array();
$arrTrunk			= array();

	$sqlEstados		= "SELECT * FROM general_estados";
	$rsE			= mysql_query($sqlEstados, cnnGeneral() );
	while($rwE = mysql_fetch_array($rsE) ){
		$arrEstados[ $rw["clave_alfanumerica"] ]	= $rw["nombre"];
		$arrTrunk[ $rw["clave_alfanumerica"] ]		=  substr($rw["nombre"], 0, 5);
		$elestado									= $rw["nombre"];
	}
$arrGenero	= array (
				"1" 	=> "H",
				"2" 	=> "M",
				"99" 	=> ""
					);
$arrEstadoCivil	= array(
					"1" => "2",
					"2" => "1",
					"3" => "4",
					"4" => "5",
					"5" => "9",
					"6" => "3",
					"7" => "7",
					"99" => "9"
);
$arrMarg		= array(
						"Bajo" => 4,
						"Muy bajo" => 5,
						"Muy alto" => 1,
						"Alto" => 2,
						"Medio" => 3
						);

$xTxt		= new cFileLog(false, true);

$sqlSoc	= "SELECT
	* 
FROM
	`socios_general` `socios_general` /*
		INNER JOIN `general_tmp` `general_tmp` 
		ON `socios_general`.`codigo` = `general_tmp`.`field_id1` */";
	$rs		= mysql_query($sqlSoc, cnnGeneral() );
	$tr		= "";
	while($rw		= mysql_fetch_array($rs) ){
		$curp		= strtoupper($rw["curp"]);
		$app1		= $rw["apellidopaterno"];
		$app2		= $rw["apellidomaterno"];
		$nombre		= $rw["nombrecompleto"];
		$socio		= $rw["codigo"];
		
		if ( strlen($curp) < 18 ){
			$curp 	= "";
		}
		if ( strpos( $curp, "IMPORT" ) >= 1){
			$curp	= "";
			$xTxt->setWrite("$socio\tCURP\tEs una CURP no valida\r\n");
		}
		$findEstado	= "";
		$fechaNac	= date("Ymd", strtotime($rw["fechanacimiento"]) );
		
		$estadoNac	= $rw["lugarnacimiento"];
		$xTxt->setWrite("$socio\tel estado de Nacimiento es $estadoNac\r\n");
			//Obtiene el Estado segun la CURP
			if ( strlen($curp) == 18 ){
				$findEstado		= substr($curp, 11,  2);
				$xTxt->setWrite("$socio\tCURP\tSe propone $tmpEstado de una CURP de $curp\r\n");
				//$fechaNac		= substr($curp, 5,  2);
			} else {
			
				//Purgar estado
				$estadoNac	= str_ireplace("SAN ", "SAN_", $estadoNac);
				$estadoNac	= str_ireplace("LA ", "LA_", $estadoNac);
				$estadoNac	= str_ireplace("EL ", "EL_", $estadoNac);
				$estadoNac	= str_ireplace("LOS ", "LOS_", $estadoNac);
				$estadoNac	= str_ireplace("LAS ", "LAS_", $estadoNac);
				$estadoNac	= str_ireplace("UIS PO", "UIS_PO", $estadoNac);
				
				$arrRep	= array(",", ".", "\t", "\n", ";", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
				$estadoNac	= str_replace($arrRep, "", $estadoNac);
				$estadoNac	= strtoupper($estadoNac);
				//
				$arrEdoNac	= explode(" ", $estadoNac);
				$parecido	= 0;
				$xVEstado	= "";
				
				
				$xTxt->setWrite("$socio\tPATRON: $estadoNac\r\n");
				
				$buscarItems	= count($arrEdoNac) - 1;
				for ($i = 0; $i <= $buscarItems; $i++){
					$MiEstado	= substr( $arrEdoNac[$i], 0,5);
					$MiEstado	= str_ireplace("_", " ", $MiEstado);
					
					$xTxt->setWrite("$socio\t$i\tBuscando $MiEstado\r\n");
					
					//if ( in_array($MiEstado, $arrTrunk) ){
						
						$sqlMiEstado	= "SELECT clave_alfanumerica, nombre FROM general_estados WHERE nombre LIKE \"%$MiEstado%\" LIMIT 0,1 ";
						$DFEstado		= obten_filas($sqlMiEstado);
						$xVEstado		= $DFEstado["clave_alfanumerica"];
						
						if ( strlen($xVEstado) == 2 ){
							$findEstado		= $xVEstado;
							$xTxt->setWrite("$socio\tSe encontro " . $DFEstado["nombre"] . " por la busqueda de $MiEstado\r\n");
						}
					//}
				}
			}
		$genero		= $arrGenero[ $rw["genero"] ];
		$cSoc		= new cSocio($socio);
		$dDom		= $cSoc->getDatosDomicilio(99);
		
		$codigo_postal	= $dDom["codigo_postal"];
		$telefono		= $dDom["telefono_residencial"];
		$nacionalidad	= "";
		$estadocivil	= $arrEstadoCivil[ $rw["estadocivil"] ];
		//Datos del Estado que ejerce el APOYO
		$sqlPostal	= "SELECT * FROM general_colonias WHERE codigo_postal = $codigo_postal LIMIT 0,1 ";
		$DPostal	= obten_filas($sqlPostal);
		$AEstado	= $DPostal["codigo_de_estado"];
		$AMunicipio	= $DPostal["codigo_de_municipio"];
		//SQL Marginaci�n
		$sqlMarg	= "SELECT
						`general_municipios`.`clave_de_entidad`,
						`general_municipios`.`clave_de_municipio`,
						`general_municipios`.`grado_de_marginacion` 
					FROM
						`general_municipios` `general_municipios` 
					WHERE
						(`general_municipios`.`clave_de_entidad` =$AEstado) AND
						(`general_municipios`.`clave_de_municipio` =$AMunicipio) LIMIT 0,1";
						
	$marginacion	=	trim(mifila($sqlMarg, "grado_de_marginacion"));
	$marginacion	= $arrMarg[$marginacion];
		$tr			.= "<tr>
							<td>$socio</td>
							<td>$curp</td>
							<td>$app1</td>
							<td>$app2</td>
							<td>$nombre</td>
							<td>$fechaNac</td>
							<td>$findEstado</td>
							<td>$genero</td>
							<td>$telefono</td>
							<td></td>
							<td></td>
							<td>$nacionalidad</td>
							<td>$estadocivil</td>
							<td></td>
							<td>$AEstado</td>
							<td>$AMunicipio</td>
							<td>9999</td>
							<td></td>
							<td>$marginacion</td>
						</tr>";
						
		
	}
	echo "<table with ='100%'>
	<tr>
		<th>#</th>
		<th>CURP</th>
		<th>Primer Apellido</th>
		<th>Segundo Apellido</th>
		<th>Nombre Completo</th>
		<th>Fecha de Nacimiento</th>
		<th>Estado de Nacimiento</th>
		<th>Sexo</th>
		<th>Telefono</th>
		<th>Fax</th>
		<th>Correo Electronico</th>
		<th>Nacionalidad</th>
		<th>Clave de Estado Civil</th>
		<th>Cantidad de Beneficio</th>
		<th>EStado</th>
		<th>Municipio</th>
		<th>Localidad</th>
		<th>Fecha de Beneficio</th>
		<th>Grado de Marginacion</th>
	</tr>
	$tr
	</table>";
	
echo getRawFooter();
?>
</body>
<script  >
<?php

?>
function initComponents(){
	window.print();
}
</script>
</html>
<?php
/**
 * Reporte de migracion de personas fisicas
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package migracion
 * @subpackage tcb
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


$oficial 		= elusuario($iduser);
$input 			= $_GET["out"];


if ($input!=OUT_EXCEL) {
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
			<th colspan="3">REPORTE DE MIGRACION DE PERSONAS FISICAS - TCB</th>
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

} else {
	$filename = $_SERVER['SCRIPT_NAME'];
	$filename = str_replace(".php", "", $filename);
	$filename = str_replace("rpt", "", $filename);
	$filename = str_replace("-", "", 	$filename);
  	$filename = "$filename-" . date("YmdHi") . "-from-" .  $iduser . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");

}
	//array que determina el genero (sexo) de la persona
	$arrGenero	= array (
		99	=> "D",
		1	=> "H",
		2	=> "M"
	);

	$arrECivil	= array(
				2 => "01",
				3 => "03",
				6 => "04",
				1 => "05",
				4 => "06",
				5 => "99",
				99 => "99"
			);
	$arrRegMat	= array(
				"BIENES_SEPARADOS" => 2,
				"SOCIEDAD_CONYUGAL" => 3,
				"NINGUNO" => 9
				);
	$arrELaboral	= array (
				10 => "01",
				11 => "01",
				12 => "01",
				13 => "01",
				20 => "01",
				40 => "02",
				50 => "02",
				99 => "99"
				);
	$tr		= "";
//TODO: validar por sucursal
$sql = "
	SELECT
		*
	FROM
		`socios_general` `socios_general`
	WHERE
			/* `socios_general`.`sucursal`	= '" . getSucursal() . "'
			AND */
			(`socios_general`.`personalidad_juridica` = 1
			OR
			`socios_general`.`personalidad_juridica` = 3
			OR
			`socios_general`.`personalidad_juridica` = 4
			OR
			`socios_general`.`personalidad_juridica` = 9
			OR
			`socios_general`.`personalidad_juridica` = 99)
			/* AND
			(`socios_general`.`estatusactual` != 20)*/
	ORDER BY
		`socios_general`.`fechaalta` DESC
";
$rs	= mysql_query($sql, cnnGeneral() );
while (  $rw = mysql_fetch_array($rs) ){
	$socio					= $rw["codigo"];
      //  $ife                            = $rw["documento_de_identificacion"]
	$nombre					= $rw["nombrecompleto"];
	$apellidopaterno		= $rw["apellidopaterno"];
	$apellidomaterno		= $rw["apellidomaterno"];
	$genero					= $arrGenero[ $rw["genero"] ];
	$sucursal				= $rw["sucursal"];
	$fecha_de_alta			= $rw["fechaalta"];
	$estado_civil			= $arrECivil[ $rw["estadocivil"] ];
	$regimen_matrimonial	= $arrRegMat[ $rw["regimen_conyugal"] ];
	$fecha_nacimiento		= $rw["fechanacimiento"];
	$rfc					= $rw["rfc"];
	$curp					= $rw["curp"];

	$hijos					= " ";

	if ( $rw["estadocivil"] != 1 ){
		$regimen_matrimonial	= "&nbsp;";
	}
       if( $rw["tipo_de_identificacion"]==1 ){
              $ife= $rw["documento_de_identificacion"];
       }else{
            $ife="&nbsp;";
        }
	/**
	 *composicion del estado laboral por consulta
	 **/
	$estado_laboral		= 99;
	//Obtener la informaci�n laboral
		$xSoc		= new cSocio($socio);
		$xSoc->init($rw);
		$DLab		= $xSoc->getDatosActividadEconomica();
		$estado_laboral	= $arrELaboral[ $DLab["tipo_aeconomica"]  ];
		//corrige valores vacios
		if ( !isset($estado_laboral) ){
			$estado_laboral		= 99;
		}
	//corrige la edad en a�os y asigna, si es menor de edad
	$edad 			= floor( (restarfechas(fechasys(), $rw["fechanacimiento"]) ) / 365);
		if ( $edad < 18 ){
			$estado_laboral		= "07";
		}
	$tr	.= "	<tr>
				<td>$socio</td>
				<td>$ife</td>
				<td>$nombre</td>
				<td>$apellidopaterno</td>
				<td>$apellidomaterno</td>
				<td>$genero</td>
				<td>01</td>
				<td>412</td>
				<td>$sucursal</td>
				<td>$fecha_de_alta</td>
				<td>$estado_civil</td>
				<td>$regimen_matrimonial</td>
				<td>$estado_laboral</td>
				<td>$fecha_nacimiento</td>
				<td>412</td>
				<td>$hijos</td>
				<td>&nbsp;</td>
				<td>$rfc</td>
				<td>$curp</td>
			</tr>";
}
echo "<table width='100%' >
	<tr>
		<th>Socio</th>
		<th>IFE</th>
		<th>Nombre</th>
		<th>Apellido Paterno</th>
		<th>Apellido Materno</th>
		<th>Sexo</th>
		<th>Idioma</th>
		<th>Nacionalidad</th>
		<th>Sucursal</th>
		<th>Fecha de Alta</th>
		<th>Estado Civil</th>
		<th>Regimen Matrimonial</th>
		<th>Estado Laboral</th>
		<th>Fecha de Nacimiento</th>
		<th>Pais de Nacimiento</th>
		<th>Numero de Hijos</th>
		<th>Fecha de Deceso</th>
		<th>R.F.C.</th>
		<th>C.U.R.P.</th>
	</tr>
		$tr
</table>";


if ($input!=OUT_EXCEL) {
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
<?php
}
?>

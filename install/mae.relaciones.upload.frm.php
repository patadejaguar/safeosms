<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package common
 *  Actualizacion
 * 		16/04/2008
 *		2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimeintos y recibos
 *
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
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.captacion.inc.php");

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);

//FIXME: Terminar carga de grupos
$action 			= $_GET["o"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Grupos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
//Si la Operacion es Configurar los Datos
if ( !isset($action) ){
?>
<form name="frmSendFiles" method="POST" action="mae.relaciones.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga batch de Relaciones&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO REFERENCIAS</th>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		</tbody>
	</table>
<?php

} elseif ( $action ==  "u" ) {

?>
<form name="frmConvs" method="POST" action="mae.relaciones.upload.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
<?php

$usrFiles	= array();
$usrFiles[0]	= $_FILES["cFile1"];
$msg			= "";


$prePath		= PATH_BACKUPS;
$lim			= 1; //sizeof($usrFiles) -1;
$arrRefTipo		= array(
				"" => DEFAULT_TIPO_RELACION,
				"F" => 4,
				"P" => 21
				);
for($i=0; $i<=$lim; $i++){
	if(isset($usrFiles[$i])==true){
		//Obtener Extension
		$DExt 	= explode(".", substr($usrFiles[$i]['name'], -6));
		$mExt	= $DExt[1];
		if($mExt == "csv"){
			$completePath	= $prePath . $usrFiles[$i]['name'];
			if(file_exists($completePath)==true){
				unlink($completePath);
				echo "<p class='aviso'> SE ELIMINO EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
			}
			if(move_uploaded_file($usrFiles[$i]['tmp_name'], $completePath )) {
				//echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
			} else {
				//echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
			}
			//analizar el Archivo
				$gestor = @fopen($completePath, "r");
				$grupo_solidario		= DEFAULT_GRUPO;
				$caja_local			= getCajaLocal();
				$iReg 	= 0;
				$cT		= new cTipos();
				//inicializa el LOG del proceso
				$aliasFil	= getSucursal() . "-carga -batch-de-socios-" . fechasys();
				$xLog		= new cFileLog($aliasFil, true);
				if ($gestor) {
					while (!feof($gestor)) {
						$bufer			= fgets($gestor, 4096);
						//$bufer			= stream_get_line($gestor, "\r\n");
						if (!isset($bufer) ){
							$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
						} else {
							$bufer			= $cT->setNoAcentos( trim($bufer) );
							$datos			= explode(",", $bufer, 30);
							$socio			= $cT->cInt($datos[1]);
							if($socio != 0 AND $socio != DEFAULT_SOCIO){
								$xSoc		= new cSocio($socio);
								
								$relNombre		= $cT->cChar(trim($datos[8]));
								$relApMat		= $cT->cChar(trim($datos[7]));
								$relApPat		= $cT->cChar(trim($datos[6]));
								$relTelP		= $cT->cChar(trim($datos[14]));
								$relMail		= $cT->cChar(trim($datos[20]));
								
								$relCalle		= $cT->cChar(trim($datos[12]));
								$relColonia		= $cT->cChar(trim($datos[6]));
								$relGenero		= DEFAULT_GENERO;
								$idRelSocio		= $socio +(100000*rand(2,9));
								$xRel			= new cSocio($idRelSocio);
								
								$numero			= "";
								$codigo_postal	= DEFAULT_CODIGO_POSTAL;
								$numero_interior= "";
								$referencia		= "";
								$telefono_fijo	= $cT->cChar(trim($datos[18]));
								$telefono_movil	= $cT->cChar(trim($datos[17]));
								$es_principal	= TIPO_DOMICILIO_PRINCIPAL;
								$regimen_vivienda	= TIPO_VIVIENDA_PROPIA;
								
								$relRFC			= "";
								$tipo_persona	= TIPO_JURIDICO_FISICA;
								$tipo_relacion	= $arrRefTipo[trim($datos[5])];
								
								$xRel->add($relNombre, $relApPat, $relApMat, $relRFC, "", getCajaLocal(),
								   false, "",
								   TIPO_INGRESO_RELACION, DEFAULT_ESTADO_CIVIL,
								   $relGenero, DEFAULT_EMPRESA, DEFAULT_REGIMEN_CONYUGAL,
								   $tipo_persona, $grupo_solidario, "", DEFAULT_TIPO_IDENTIFICACION, "", $idRelSocio,
									false, "$relTelP", "$relMail", 0);
								//Agregar domicilio
								$xRel->addVivienda($relCalle, $numero, $codigo_postal, $numero_interior, $referencia, $telefono_fijo, $telefono_movil,
											     $es_principal, $regimen_vivienda, TIPO_DOMICILIO_PARTICULAR, DEFAULT_TIEMPO,
											     $relColonia);
								$xRel->init();
								$xSoc->addRelacion($xRel->getCodigo(), $tipo_relacion, DEFAULT_TIPO_CONSANGUINIDAD,
									   1, "Importado");
								$msg	.= $xSoc->getMessages("txt");
								$msg	.= $xRel->getMessages("txt");
							}
							
						}
					$iReg++;
					}
				}
				fclose ($gestor);
				$xLog->setWrite($msg);
				echo $xLog->getLinkDownload("Archivo del proceso");
		}	else {
			echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
		}
	}
}

}
if ( !isset($iReg) ){
	$iReg	= 0;

}
?>
</fieldset>
</form>
</body>
<script  >
</script>
</html>

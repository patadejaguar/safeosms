<?php
/**
 * Modulo de Validacion con la existencia del Socio
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package common
 * Actualizacion
 * Se agrego el Campo cuenta
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

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);

//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$action 			= $_GET["o"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Archivos CompacW para Comparacion [V 1.1.02]</title>
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
<form name="frmSendFiles" method="POST" action="validar_con_socios.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga Automatica de Archivos Comparados CompacW&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="1">ARCHIVO COMPACW A ENVIAR</th>
			<td colspan="1"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th>Tipo de Salida a Generar</th>
			<td><select name="tOut">
				<option value="log">Texto de Mensajes</option>
				<option value="comparable">Texto Comparable</option>
			</select></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		</tbody>
	</table>
<?php

} elseif ( $action ==  "u" ) {

?>
<form name="frmConvs" method="POST" action="convenios_factura_rapida.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
<?php

		$usrFiles		= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$msg			= "";
		$txt			= "";

		$TipoDeSalida	= $_POST["tOut"];
		$SAhorro		= 0;
		$SCapital		= 0;
		$SInteres		= 0;
		$SIva			= 0;
		$STotal			= 0;
		$diferencias	= 0;

		$arrEstatus		= array(
						"1-3-01" => 10,
						"1-3-03" => 20
						);
		$arrTCuenta		= array (
						"2-1-01-01-01" => 1,
						"2-1-01-01-02" => 2
						);
		$prePath		= PATH_BACKUPS;
		$lim			= 1;
		for($i=0; $i<=$lim; $i++){
			if(isset($usrFiles[$i])==true){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[$i]['name'],-6));
				$mExt	= trim($DExt[1]);

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
						$gestor 	= @fopen( $completePath, "r" );

						$iReg 		= 0;
						$cT			= new cTipos();
						if ($gestor) {
							while (!feof($gestor)) {
								$bufer				= fgets($gestor, 4096);
								//$bufer			= stream_get_line($gestor, "\r\n");
								if ( !isset($bufer) ){
									$msg 			.= "$iReg\tLa Linea($iReg) no se leyo($bufer)\r\n";
								} else {
									//layout : socio , nombre, monto, cuenta
									$datos			= explode(",", trim($bufer), 4);
									$socio			= $cT->cInt( $datos[0] );
									$nombre			= ( isset( $datos[1] ) ) ? $cT->cChar( trim($datos[1]) ) : "";
									$monto			= ( isset( $datos[2] ) ) ? $datos[2] : 0;
									
									$cuenta			= ( isset( $datos[3] ) ) ? $datos[3] : 0;

									if ( $socio != 0 AND $socio != 0) {
										//
										$xSoc		= new cSocio($socio);
										if ( $xSoc->existe( $socio ) == false ){
											//
											$msg	.= "$iReg\tERROR\t$socio\tEl Socio NO EXISTE\r\n";
											$txt	.= "$socio,$nombre,$monto,$cuenta,NO_EXISTE\r\n";
										} else {
											$xSoc->init();
											$D			= $xSoc->getDatosInArray();
											$estatus	= $cT->cInt( $D["estatusactual"] );
											//
											switch ( $estatus ){
												case 99:
													$msg	.= "$iReg\tWARNING\t$socio\tEl Socio puede tener PROBLEMAS al migrar, su estatus es $estatus\r\n";
													break;
												case 50:
													$msg	.= "$iReg\tERROR\t$socio\tEl Socio TIENE PROBLEMAS al migrar, su estatus es $estatus\r\n";
													$txt	.= "$socio,$nombre,$monto,$cuenta,$estatus\r\n";
													break;
												default:
													$msg	.= "$iReg\tSUCESS\t$socio\tEl Socio ES VALIDO\r\n";
													break;
											}
											
										}
										
									} else {
										$msg	.= "$iReg\tOMITIDO\t$socio\tSocio[$socio] y Dato Omitido\r\n";
									}
									
								}
							$iReg++;
							}
						}
						fclose ($gestor);
						$html = new cHTMLObject();
						if ( $TipoDeSalida == "comparable" ){
							$msg		= "SOCIO,NOMBRE,MONTO,CUENTA,ESTATUS\r\n";
							$msg		.= $txt;
						}

							$cF = new cFileLog();
							$cF->setWrite($msg);
							$cF->setClose();
							echo $cF->getLinkDownload("Mostrar Datos del Proceso");

				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO ES SOPORTADO</p>";
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

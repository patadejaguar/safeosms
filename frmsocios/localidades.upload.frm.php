<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1.03
 * @package common
 * Actualizacion
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
//=====================================================================================================

	$xHP			= new cHPage("TR.Carga Automatizada de Localidades");
	
ini_set("max_execution_time", 600);
ini_set("upload_max_filesize", "30M");
ini_set("post_max_size", "30M");

//$locale = "es_MX.iso-8859-1";
setlocale(LC_ALL, "es_MX");

//post_max_size = 8M
//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$action 			= ( isset($_GET["o"]) ) ? $_GET["o"] : "x";

$xHP->init();

//Si la Operacion es Configurar los Datos
if ( $action == "x"){
?>
<form name="frmSendFiles" method="POST" action="localidades.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga Automatizada de Localidades&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO PARA CARGAR LOCALIDADES</th>
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

echo "<form name=\"frmConvs\" method=\"POST\" action=\"localidades.upload.frm.php?o=s\">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend> ";

		$usrFiles	= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$msg			= "";


		$SAhorro		= 0;
		$SCapital		= 0;
		$SInteres		= 0;
		$SIva			= 0;
		$STotal			= 0;
		$diferencias		= 0;

		$prePath		= PATH_BACKUPS;
		$lim			= 1; //sizeof($usrFiles) -1;
		for($i=0; $i<=$lim; $i++){
			if(isset($usrFiles[$i])==true){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[$i]['name'],-6));
				$mExt	= $DExt[1];

				if($mExt == "csv"){
					$completePath	= $prePath . $usrFiles[$i]['name'];
					if(file_exists($completePath)==true){
						unlink($completePath);
						echo "<p class='aviso'> SE ELIMINO EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					}
					if(move_uploaded_file($usrFiles[$i]['tmp_name'], $completePath )) {
						echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					} else {
						echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
					}
					//analizar el Archivo
						$gestor 		= fopen($completePath, "r");
						$xT 			= new cTipos;
						$iReg 			= 0;
						$IDEntidad		= 0;
						$IDMunicipio	= 3;
						$IDLocalidad	= 5;
						$IDNombre		= 6;
						
						$IDLatitud		= 7;
						$IDLongitud		= 8;
						$IDAltitud		= 9;
						
						$cT	= new cTipos();
						if ($gestor) {
							/*
							
							 */
							//array fgetcsv ( resource $handle [, int $length = 0 [, string $delimiter = ',' [, string $enclosure = '"' [, string $escape = '\\' ]]]] )
							while ($datos	= fgetcsv($gestor,0, ",", "\"")) {
								//CVE_ENT,CVE_MUN,CVE_LOC,NOM_LOC,AMBITO,LATITUD,LONGITUD,ALTITUD,CVE_CARTA
								if($iReg == 0){
									foreach ($datos as $id => $vl){
										if($vl == "CVE_ENT"){ $IDEntidad = $id; }
										if($vl == "CVE_MUN"){ $IDMunicipio = $id; }
										if($vl == "CVE_LOC"){ $IDLocalidad = $id; }
										if($vl == "NOM_LOC"){ $IDNombre = $id; }
										if($vl == "LATITUD"){ $IDLatitud = $id; }
										if($vl == "LONGITUD"){ $IDLongitud = $id; }
										if($vl == "ALTITUD"){ $IDAltitud = $id; }
									}
								} else {
									//CVE_ENT,NOM_ENT,NOM_ABR,
									//CVE_MUN,NOM_MUN,
									//CVE_LOC,NOM_LOC,
									//LATITUD,LONGITUD,ALTITUD,CVE_CARTA,AMBITO,PTOT,PMAS,PFEM,VTOT
									$ClaveUnica			= $xT->cInt( ($datos[$IDEntidad] . $datos[$IDMunicipio] . $datos[$IDLocalidad]), true);
									$ClaveEntidad		= $xT->cInt($datos[$IDEntidad], true);
									$claveMunicipio		= $xT->cInt($datos[$IDMunicipio], true);
									$ClaveLocalidad		= $xT->cInt($datos[$IDLocalidad], true);
									$NombreLocalidad	= $xT->cChar($datos[$IDNombre] ); //iconv("UTF-8", "ISO-8859-1", $datos[$IDNombre]);
			
									$latitud			= $datos[$IDLatitud];
									$altitud			= $datos[$IDAltitud];
									$longitud			= $datos[$IDLongitud];
									
									$sql				= "INSERT INTO catalogos_localidades(clave_unica, nombre_de_la_localidad, clave_de_estado, clave_de_municipio, clave_de_localidad, longitud, altitud, latitud) 
	    													VALUES($ClaveUnica, '$NombreLocalidad', $ClaveEntidad, $claveMunicipio, $ClaveLocalidad, '$longitud', '$altitud', '$latitud') ";
									if( $ClaveUnica != 0){
										my_query($sql);	
									}
									
									$msg				.= "$ClaveEntidad\t$claveMunicipio\t$ClaveLocalidad\tAgregando la Localidad $NombreLocalidad con codigo $ClaveUnica\r\n";
								}
							$iReg++;
							}
						}
						@fclose ($gestor);
						$html = new cHTMLObject();
						
							//$htmlmsg = $html->setInHTML($msg);
							$xlog		= new cFileLog( ("carga_batch-localidades-" . date("Ymd")), true);
							$xlog->setWrite($msg);
							
							$xBtn		= new cHButton("");
							echo $xBtn->getSalir();
							$xlog->setClose();
							echo $xlog->getLinkDownload("Archivo de Resultados de la Carga");
							//echo "<p class ='aviso'>$htmlmsg</p>";
						
						//echo $msg;
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

<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.2.03
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
	$xHP			= new cHPage("TR.Carga Automatizada de Localidades");

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);

//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$action 			= ( isset($_GET["o"]) ) ? $_GET["o"] : "x";

$xHP->init();

//Si la Operacion es Configurar los Datos
if ( $action == "x"){
?>
<form name="frmSendFiles" method="POST" action="colonias.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga Automatica de Colonias&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO PARA ANALIZAR COLONIAS</th>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th>Carga Global de Datos[Todas las colonias]?</th>
			<td colspan="1"><?php echo cBoolSelect("csResult", "idsResult"); ?></td>
		</tr>
					<tr>
			<th>Eliminar Todos Los Datos Existentes?</th>
			<td colspan="1"><?php echo cBoolSelect("cEliminar", "idEliminar"); ?></td>
		</tr>	
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		</tbody>
	</table>
<?php

} elseif ( $action ==  "u" ) {

echo "<form name=\"frmConvs\" method=\"POST\" action=\"colonias.upload.frm.php?o=s\">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend> ";


		$usrFiles	= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$msg			= "";
		$cargaGlobal	= $_POST["csResult"];
		$EliminarDatos	= ($cargaGlobal == 1) ? 1 : $_POST["cEliminar"];

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

				if($mExt == "txt"){
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
						$xT 	= new cTipos;
						/*echo "<table width=\"100%\">
								<tbody>
								<tr>
									<th width='5%'>ID</th>
									<th width='8%'>Socio</th>
									<th width='23%'>Nombre</th>
									<th width='8%'>Credito</th>
									<th width='4%'>Letra</th>
									<th width='8%'>Capital</th>
									<th width='8%'>Interes</th>
									<th width='8%'>I.V.A.</th>
									<th width='8%'>Ahorro</th>
									<th width='8%'>TOTAL</th>
									<th width='22%'>Observaciones</th>
								</tr>";*/
						$iReg = 0;
						if( $EliminarDatos == 1 ){
							my_query( "DELETE FROM general_colonias");
						}
						
						$cT	= new cTipos();
						if ($gestor) {
							while (!feof($gestor)) {
								$bufer			= $cT->setNoAcentos(fgets($gestor, 4096));
								//$bufer			= stream_get_line($gestor, "\r\n");
								if (!isset($bufer) ){
									$msg 		.= "La Linea($iReg) no se leyo($bufer)\r\n";
									$msg 		.= "DEL\tSe Eiminan todas las Colonias\r\n";
								} else {

								$datos			= explode("|", $bufer, 16);
								$cpostal		= $datos[0];
								$ncolonia		= $cT->cChar($datos[1]);
								$tcolonia		= $cT->cChar($datos[2]);
								$estado			= $cT->cChar($datos[4]);
								$municipio		= $cT->cChar($datos[3]);
								
								$numEstado		= $cT->cInt($datos[7]);
							
								$ciudad			= $cT->cChar($datos[5]);
								
									if (trim($ciudad) == ""){
										$ciudad	= $ncolonia;
									}
								$numMunicipio	= ( $cargaGlobal == 1) ? $cT->cInt($datos[11]) : $cT->cInt($datos[10]);

								$fecha		= fechasys();
								$sucursal	= getSucursal();

									$sql = "INSERT INTO general_colonias(codigo_postal, nombre_colonia, tipo_colonia,
														estado_colonia, ciudad_colonia, municipio_colonia,
														fecha_de_revision,
														codigo_de_estado, codigo_de_municipio, sucursal)
													VALUES($cpostal, '$ncolonia', '$tcolonia',
														'$estado', '$ciudad', '$municipio',
														'$fecha', $numEstado, $numMunicipio, '$sucursal')";
									
									$sqDC	= "DELETE FROM general_colonias WHERE codigo_postal = $cpostal AND fecha_de_revision != '$fecha' ";

									if ($cpostal != 0 AND trim($ncolonia)!="" ){
										if( $EliminarDatos == 0 ){	// si es falso, eliminar individualmente, modo actualizar
											$xd	= my_query($sqDC);
											$msg	.= $xd["info"] . "\r\n";
										}
										$xe	= my_query($sql);
										$msg	.= (trim($xe["info"]) == "") ? "" : $xe["info"] . "\r\n";
										
										$msg .= "$iReg\tSUCESS\t$cpostal\tSe Agrega colonia $ncolonia \r\n";
									} else {
										$msg .= "$iReg\tALERTA\tLa Linea($iReg) no se Imprimio [" . substr($bufer, 0, 20) . "]($cpostal :: $ncolonia)\r\n";
									}
								}
							$iReg++;
							}
						}
						fclose ($gestor);
						$html = new cHTMLObject();
						
							$xlog		= new cFileLog( ("carga_batch-colonias-" . date("Ymd")), true);
							$xlog->setWrite($msg);
													
							$htmlmsg = $html->setInHTML($msg);
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

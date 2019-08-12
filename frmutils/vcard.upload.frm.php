<?php
ini_set("max_execution_time", 600);

$action 			= ( isset($_GET["o"]) )? $_GET["o"] : false ;
$lang[""]			= "";

$select				= "<option value='N;CHARSET=UTF-8'>Nombre Completo</option>
				  <option value='TITLE;CHARSET=UTF-8'>Cargo</option>
				  <option value='TEL;HOME;CELL;CHARSET=UTF-8'>Telefono Celular</option>
				  <option value='TEL;HOME;VOICE;CHARSET=UTF-8'>Hogar. Telefono</option>
				  <option value='TEL;WORK;VOICE;CHARSET=UTF-8'>Trabajo. Telefono</option>
				  <option value='EMAIL;HOME;CHARSET=UTF-8'>Hogar. Correo</option>
				  <option value='EMAIL;WORK;CHARSET=UTF-8'>Trabajo. Correo</option>
				  <option value='ORG;CHARSET=UTF-8'>Empresa</option>
				  <option value='NONE' selected>--</option>
				  ";

//Si la Operacion es Configurar los Datos
if ( $action == false ){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Archivo CVS VCARD</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/tables.css" rel="stylesheet" type="text/css">
<link href="../css/forms.css" rel="stylesheet" type="text/css">
<link href="../css/grid960.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="frmSendFiles" method="POST" action="vcard.upload.frm.php?o=u" enctype="multipart/form-data" class='horizontal_form'>
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga de Conversion a VCARD&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='90%'   class='plain'>
		<tbody>

		<tr>
			<td>Seleccione un Archivo:</td>
			<td colspan="5"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th colspan="6">SELECCIONE UN CONTENIDO POR COLUMNA(HASTA 5 COLUMNAS POR ARCHIVO)</th>
		</tr>
		<tr>
			<th><select name="c1"><?php echo $select; ?></select></th>
			<th><select name="c2"><?php echo $select; ?></select></th>
			<th><select name="c3"><?php echo $select; ?></select></th>
			<th><select name="c4"><?php echo $select; ?></select></th>
			<th><select name="c5"><?php echo $select; ?></select></th>

		</tr>
		<tr>
			<th colspan="6"><input type="submit" value="Enviar Archivos" class='confirm_button' /></th>
		</tr>		
		</tbody>
	</table>
</fieldset>
</form>
</body>
<script  >
</script>
</html>

<?php

} elseif ( $action ==  "u" ) {

		$usrFiles		= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$msg			= "";
		$afectable		= $_POST["csResult"];
		//COLUMNAS
		$c1				= $_POST["c1"];
		$c2				= $_POST["c2"];
		$c3				= $_POST["c3"];
		$c4				= $_POST["c4"];
		$c5				= $_POST["c5"];

		$txt			= "";
		//===========================================================================================
		$prePath		= "/var/www/tmp/";
		$lim			= 1; //sizeof($usrFiles) -1;
		//Arrays de Control
	//==================================================================================================================
			if( isset($usrFiles[0]) ){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[0]['name'], -6));
				$mExt	= $DExt[1];

				if($mExt == "csv"){
					$completePath	= $prePath . $usrFiles[0]['name'];
					if(file_exists($completePath)==true){
						unlink($completePath);
						//echo "<p class='aviso'> SE ELIMINO EL ARCHIVO " . $usrFiles[0]['name'] . "</p>";
					}
					if(move_uploaded_file($usrFiles[0]['tmp_name'], $completePath )) {
						//echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					} else {
						//echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
					}
					//analizar el Archivo
						$gestor = @fopen($completePath, "r");
						$iReg 	= 0;

						//inicializa el LOG del proceso
						$aliasFil	= "";

						if ($gestor) {
							while (!feof($gestor)) {
								$bufer			= fgets($gestor, 4096);
								//$bufer			= stream_get_line($gestor, "\r\n");
								if (!isset($bufer) ){
									//$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
								} else {
									$bufer				= trim($bufer);
									$datos				= explode(",", $bufer, 6);
									//Error Raise
									$errRaise			= false;
									$vcard				= "BEGIN:VCARD\r\nVERSION:2.1\r\n";
									//mostrar columnas
									if ($c1 != "NONE"){
										$vcard			.= "$c1:" . $datos[0] . "\r\n";
									}
									if ($c2 != "NONE"){
										$vcard			.= "$c2:" . $datos[1] . "\r\n";
									}
									if ($c3 != "NONE"){
										$vcard			.= "$c3:" . $datos[2] . "\r\n";
									}
									if ($c4 != "NONE"){
										$vcard			.= "$c4:" . $datos[3] . "\r\n";
									}
									if ($c5 != "NONE"){
										$vcard			.= "$c5:" . $datos[4] . "\r\n";
									}
									if ( trim($datos[0]) == "" ){
										$vcard				= "";
									} else {
										$vcard				.= "END:VCARD\r\n";
									}
									//echo $vcard;
									$txt				.= $vcard;	
								}
							$iReg++;
							}
						}
						fclose ($gestor);
						//$xLog->setWrite($msg);
				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[0]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
				}
				    header("Content-type: text/plain");
				    //ISO-8859-1
				    header("Content-Disposition: attachment;");
				    header("Content-Disposition: filename=\"vcard-" .  date("Ymdhsi") .".vcf\"");
				    echo $txt;
			}

}
?>


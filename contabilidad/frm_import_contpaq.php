<?php
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

$oficial = elusuario($iduser);
//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");	
//$jxc ->process();
$operation		= $_GET["u"];
$usrFile		= $_FILES["txtArchive"];
$compare		= $_POST["onCompare"];
$setCompare		= $_POST["compDestination"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true); 
?>
<body>
<?php
if($operation!="1"){
?>
<form name="frmUploadFile" method="post" action="frm_import_contpaq.php?u=1" enctype="multipart/form-data">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Archivo a subir</td>
			<td><input type="hidden" name="MAX_FILE_SIZE" value="5120000" />
			<input type="file" name='txtArchive' value='' id="idArchive" accept="text/plain" /></td>
		</tr>
		</tbody>
	</table>
</fieldset>
<fieldset>
	<legend>Opciones</legend>
	<table    >
		<tbody>
			<tr>
				<td>Comparar Automaticamente y Mostrar resultados</td>
				<th><input name="onCompare" type="checkbox" /></th>
			</tr>			
			<tr>
				<th colspan="2"><input type="submit" value="enviar" /></th>
			</tr>
		</tbody>
	</table>
</fieldset>
<p class="aviso">Deber&aacute; subir un Arhivo por Rubro, para que la Comparacion sea Exitosa y rapida posible</p>
</form>
<?php
} else {
	if($usrFile){
		if($usrFile['type']=="text/plain"){
		//subir el Archivo
		$tmpMove 		= md5($iduser . date("YmdHis"));
		$tmpURI			= PATH_TMP . "$tmpMove.tmp";
		$txtFile		= "/dev/null";
		$nFile			= $usrFile['name'];
		$fecha_proceso	= fechasys();
		$i				= 0;
		$ImportLines	= 0;
		$ExcludeLines	= 0;
		$CompLines		= 0;
		$NoCompLines	= 0;
		$mayor_chars	= 6;
		//Determina los sufijos para mayor
		$arrMayor		= array(
							"1-1-05" => "creditos@automaticos",
							"1-1-06" => "creditos@solidarios",
							"2-1-01" => "captacion@a_la_vista",
							"2-1-02" => "captacion@inversion"
							);
			if(move_uploaded_file($usrFile['tmp_name'], $tmpURI)) {
				//Leer el Archivo
				$txtFile = fopen($tmpURI, "r");
					while (!feof($txtFile)) {
						$line = trim(fgets($txtFile));
						//Limpia la Linea de espacios
						//$line = str_replace(" ", "", $line);
						//$line = str_replace("\t", "", $line);
						//echo "<p>$line</p> \n";
						//$dfile = explode(" ", $line, 2);
						$sContent = explode("^", $line);
						//echo "<p>"  . sizeof($sContent) . " --- en $line</p>";
						if(sizeof($sContent)==9){
								//echo "<p class='aviso'>LINEA NUM $i IMPORTADA</p>";
								$cuenta 		= trim($sContent[1]);
								$nombre			= trim($sContent[2]);
								$PKey			= substr($cuenta, 0,6);
								$monto			= trim($sContent[7]);
								//Array segun los socios
								$dCuenta		= explode("-", $cuenta);
								$cajaLocal		= $dCuenta[4];
								$NSocio			= $dCuenta[5];
									if($NSocio<=999){
										$socio 	= substr("000" . $NSocio, -3);
									}
								$socio		= $cajaLocal . $socio;
								//array de las cuentas con filtros segun key
								$vDats			= $arrMayor[$PKey];
								$aDats			= explode("@", $vDats);
								$usr			= $iduser;
								$clasificacion	= $aDats[0];
								$subclase		= $aDats[1];
								
								$SQL_NCW = "INSERT INTO contable_contpaq_importados
								(numero_de_cuenta,  numero_de_socio, nombre_de_la_cuenta, 
								movimiento_asociado, subclasificacion, cantidad, 
								fecha_importacion, fecha_comparacion, 
								diferencia, usuario, notas) 
	    						VALUES('$cuenta', '$socio', '$nombre', 
    							'$clasificacion', '$subclase', $monto, 
    							'$fecha_proceso', '$fecha_proceso', $usr, 
    							0, 'Importado de $nFile a las" . date("H:i:s") . "')";
								//echo "<p class='aviso'>$SQL_NCW</p>";
								//my_query($SQL_NCW);
									//Comparar

								$ImportLines++;
						} else {
							//echo "<p class='warn'>NO SE IMPORTO LA LINEA NUM $i</p>";
							$ExcludeLines++;
						}
						$i++;
					}
				echo "<p class='aviso'>LINEAS IMPORTADAS $ImportLines</p>";
				echo "<p class='warn'>LINEAS EXCLUIDAS $ExcludeLines</p>";
				if($compare == "on"){
					switch ($subclase){
						case "automaticos":
							//
							$sql = "SELECT
	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`estatus_actual`
	SUM(`creditos_solicitud`.`saldo_conciliado`) AS 'monto',
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_tipoconvenio` 
		`creditos_tipoconvenio` 
		ON `creditos_solicitud`.`tipo_convenio` = 
		`creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
WHERE
	(`creditos_solicitud`.`saldo_conciliado` >=0.99) 
GROUP BY
	`creditos_solicitud`.`numero_socio`,
	`creditos_tipoconvenio`.`tipo_autorizacion`,
	`creditos_solicitud`.`estatus_actual`";
							//$cComp = new cQueryEsp();
							$ArrsCreds = 0;
							break;
						case "solidarios":
							//
							break;
						case "a_la_vista":
							break;
						case "inversion":
							break;
						}
				}
			} else {
				echo "<p class='aviso'>IMPORTACION FALLIDA</p>";
			}
		} else {
			echo "<p class='aviso'>TIPO DE ARCHIVO( " . $usrFile['type'] . " ) NO SOPORTADO</p>";
		}
	} else {
		echo "<p class='aviso'>NO HAY ARCHIVO A IMPORTAR</p>";
	}
}
?>
</body>
<script  >
</script>
</html>

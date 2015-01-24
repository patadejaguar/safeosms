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

$pred_action = "i";
$idsocio				= $_GET["id"];
$var_action				= $_GET['action']; 
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<title>Captaci&oacute;n.- Captura de Firmas</title> 
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head> 
<body> 
<?php

if(!$var_action){
?>
<form name ='frmcaptacionfirmas' action='frmcaptacionfirmas.php?action=u&i=<?php echo $idsocio; ?>' method='POST'>
<table width='100%' border='0'>
	<tr> 
		<th>FIRMA PRINCIPAL(Prueba 1)</th>
	</tr>
	<tr>
		<td><img src="frmgetfirm.php?i=<?php echo $idsocio . "&t=1" ?>" height="350px" width="350px" /></td>
 	</tr>
	<tr> 
		<th>FIRMA MANCOMUNADO 1</th>
	</tr>
	<tr>		
		<td><img src="frmgetfirm.php?i=<?php echo $idsocio . "&t=3" ?>" height="350px" width="350px" /></td>
 	</tr> 
	<tr> 
		<th>FIRMA MANCOMUNADO 2</th>
	</tr>
	<tr>
		<td><img src="frmgetfirm.php?i=<?php echo $idsocio . "&t=4" ?>" height="350px" width="350px" /></td>
	</tr>
</table>
<a class="button" onclick="frmcaptacionfirmas.submit();">Cargar Firmas</a>
</form>
<?php
} elseif($var_action == "u") {
	$idcuenta = $_GET["i"];
?>
<form name ='frmcaptacionfirmas' enctype="multipart/form-data"  
			action='frmcaptacionfirmas.php?action=i&i=<?php echo $idcuenta; ?>' method='POST' >
<fieldset>
<legend>Captaci&oacute;n.- Captura de Firmas</legend>
<input type="hidden" name="MAX_FILE_SIZE" value="512000">
<table width='100%' border='0'>
	<tr> 
		<td>FIRMA PRINCIPAL(Prueba 1)</td>
		<td><input  type='file'  name='firma_principal1' size="40" /></td>
 	</tr>
	<tr> 
		<td>FIRMA PRINCIPAL(Prueba 2)</td>
		<td><input  type='file'  name='firma_principal2' size="40" /></td>
 	</tr> 
	<tr> 
	<td>FIRMA MANCOMUNADO 1</td>
	<td><input  type='file'  name='firma_mancomunado1' size="40" /></td>
 	</tr> 
	<tr> 
		<td>FIRMA MANCOMUNADO 2</td>
		<td><input  type='file'  name='firma_mancomunado2' size="40" /></td>
	</tr>
</table>
<input type="button" name="cmdSend" value="Guardar Registro" onClick="frmcaptacionfirmas.submit();">

</fieldset>
</form>
<?php 

} elseif($var_action=="i"){

$var_numero_de_socio 	= $_GET['i']; 
$mFile1					= $_FILES["firma_principal1"];
$mFile2					= $_FILES["firma_principal2"];
$mFile3					= $_FILES["firma_mancomunado1"];
$mFile4					= $_FILES["firma_mancomunado2"];

if(isset($mFile1)){
	$retFile = new setSaveCaptacionImage($mFile1);
	$retFile->execute($var_numero_de_socio,1);
	echo "<p class='aviso'>" . $retFile->getMsgs() . "</p>";
}
if(isset($mFile2)){
	$retFile = new setSaveCaptacionImage($mFile2);
	$retFile->execute($var_numero_de_socio,2);
	echo "<p class='aviso'>" . $retFile->getMsgs() . "</p>";
}
if(isset($mFile3)){
	$retFile = new setSaveCaptacionImage($mFile3);
	$retFile->execute($var_numero_de_socio,3);
	echo "<p class='aviso'>" . $retFile->getMsgs() . "</p>";
}
if(isset($mFile4)){
	$retFile = new setSaveCaptacionImage($mFile4);
	$retFile->execute($var_numero_de_socio,4);
	echo "<p class='aviso'>" . $retFile->getMsgs() . "</p>";
}

}
class setSaveCaptacionImage{
	private $mFile		= null;
	private $mMsg		= "";
	function __construct($tmpFile){
		$this->mFile = $tmpFile;
	}	
	function execute($socio, $tipo){
		if(isset($this->mFile)) {
		//Mover los Archivos
			$destFile		=	PATH_TMP . $this->mFile['name'];
			$imgFile		=	"";
			$onProcess		= true;
			if(move_uploaded_file($this->mFile['tmp_name'], $destFile)) {
				$md5FIl	= md5_file($destFile);
				switch($this->mFile["type"]) {
					case "image/png":
						$imgFile	= imagecreatefrompng($destFile);
						break;
					case "image/jpeg":
						$imgFile	= imagecreatefromjpeg($destFile);
						break;
					case "image/gif":
						$imgFile	= imagecreatefromgif($destFile);
						break;
					default:
						$onProcess = false;
						$this->mMsg .= "<br /> EL TIPO DE ARCHIVO (" . $this->mFile["type"] . ") NO ES EL CORRECTO";
						break;
				}
				if($onProcess == true){
					ob_start();
					$txt = "[". date("Ydm H:i:s") . "] " . EACP_NAME;
					imagettftext($imgFile, 9,0, 10,10,20, "../fonts/arial.ttf", $txt);
					imagepng($imgFile);
					//Resize image

					$png	= ob_get_contents();
					ob_end_clean();
					$png	= str_replace('##','##', mysql_escape_string($png));
					$user	= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
					$eacp	= EACP_CLAVE;
					$sucursal	= getSucursal();
					$date		= fechasys();
					//Elimina las firmas anteriores
					$sqldel = "DELETE FROM socios_firmas WHERE numero_de_cuenta=$cuenta
					AND tipo=$tipo";
					my_query($sqldel);
					
					$rsUQ = "INSERT INTO socios_firmas
									(numero_de_socio, tipo, 
									firma, md5_src, idusuario, sucursal, fecha_carga, eacp) 
    								VALUES
    								($socio, $tipo, \"$png\", '$md5FIl', $user, '$sucursal', '$date', '$eacp')";
					$rsM = my_query($rsUQ);
					if($rsM["stat"] == false){
						$this->mMsg .= "<br /> ERROR AL GUARDAR EL ARCHIVO EN LA DB, EL SISTEMA DEVOLVIO: " . $rsM["error"];
					} else {
						$this->mMsg .= "<br /> PROCESO EXITOSO";
					}
				} else {
					$this->mMsg .= "<br /> EL PROCESO NO SE LLEVO A CABO";
				}
				//Elimina el Archivo
				unlink($destFile);
			} else {
				//no se Movio
				$this->mMsg .= "<br /> NO SE CARGO EL ARCHIVO " . $this->mFile["name"] . "(" . $this->mFile["error"] . ")";
			}
		} else {
			$this->mMsg .= "<br /> NO EXISTE EL ARCHIVO ";
		}
	}
	function getMsgs(){
		return $this->mMsg;
	}
}
?>
</body>
</html>
<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package common
 *  Actualizacion
 * 	16/04/2008
 *	2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimeintos y recibos
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

$xPage			= new cHPage();
$oficial        	= elusuario($iduser);
ini_set("max_execution_time", 600);

$action 			= ( isset($_GET["o"]) )? $_GET["o"] : false ;

echo $xPage->getHeader();

//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
//Si la Operacion es Configurar los Datos
if ( $action == false ){
?>
<form name="frmSendFiles" method="POST" action="logo.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga del Logo&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO DE LOGO</th>
		</tr>
		<tr>
			<td>Seleccione un Archivo:</td>
			<td colspan="1"><input type="file" name="cFile1" size="50" /></td>
		</tr>

		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		
		</tbody>
	</table>
<?php

} elseif ( $action ==  "u" ) {

echo "<form name=\"frmConvs\" method=\"POST\" action=\"logo.upload.frm.php?o=s\">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>";

		$usrFiles		= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$msg			= "";
		$prePath		= PATH_HTDOCS . "/images/";
	//==================================================================================================================
			if( isset($usrFiles[0]) ){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[0]['name'], -6));
				$mExt	= $DExt[1];

				if($mExt == "jpg" OR $mExt == "png"){
					$completePath	= $prePath . $usrFiles[0]['name'];
					if(file_exists($completePath)==true){
						unlink($completePath);
						echo "<p class='aviso'> SE ELIMINO EL ARCHIVO " . $usrFiles[0]['name'] . "</p>";
					}
					if(move_uploaded_file($usrFiles[0]['tmp_name'], $completePath )) {
						echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
					} else {
						echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
					}

				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[0]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
				}
			}


}
if ( !isset($iReg) ){ $iReg	= 0; }
?>
</fieldset>
</form>
</body>
</html>

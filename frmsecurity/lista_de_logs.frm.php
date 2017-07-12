<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 * 
 * 		-
 *		-
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
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
$jxc = new TinyAjax();
function jsaGetFiles($uLike){
//Crea Archivos a partir de un Read de Files en el Archivo
//principal
		$d = dir(PATH_TMP);
		$links		= "";
		
		while($entry=$d->read()) {
		
		
		$OName 		= $entry;
		$siPoint 	= strpos($OName, ".");
		$i			= 0;
				if (!$siPoint){
				$handle		=	opendir($d->path . "/" . $OName);
						while ($file = readdir($handle))
						{
								$siTXT = strpos($file, ".txt");
								$siLOG = strpos($file, "log");
								
								if( ($siLOG !== false) and ($siTXT !== false) ){
									//si no es linux
									//bkp y no es
									//texto
									
									$file 		= str_replace(".txt", "", $file);
									$xF			= new cFileLog($file);
									if ($uLike != ""){
										if ( strpos($file, $uLike) !== false ){
											
												$links		.= "<tr><td colspan='3'>" .$xF->getLinkDownload($file) . "</td></tr>";
												$i++;
										}
									} else {
										$links		.= "<tr><td colspan='3'>" .$xF->getLinkDownload($file) . "</td></tr>";
										$i++;
									}
									if ($i > 25){
										break;
									}
									
								}
						}
						closedir($handle);
				}
				
		}
		$d->close();
		echo	$links;
}
$jxc ->exportFunction('jsaGetFiles', array('idBuscar'), "#idlsFiles");
$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Listado de Bitacoras Generados por S.A.F.E.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jsb	= new jsBasicForm("", iDE_CAPTACION);
//$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<body>
<form name="frmShowLogs" method="POST" action="./lista_de_logs.frm.php">
<fieldset>
	<legend>Listado de Bitacoras Generados por S.A.F.E.</legend>
	<table border='0' width='100%'  >
		<thead>
		<tr>
			<td>Buscar Texto Parecido a:</td>
			<td><input type='text' name='cBuscar' value='' id="idBuscar" /></td>
			<td><input type="button" value="Obtener Archivos" onclick="jsaGetFiles()" /></td>
		</tr>
		</thead>
		<tbody id="idlsFiles">
				
		</tbody>
	</table>
</fieldset>
</form>
</body>
<script  >
</script>
</html>

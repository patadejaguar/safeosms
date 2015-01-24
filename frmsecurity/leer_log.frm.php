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
$xHP			= new cHPage("TR.Log reader");
$oficial = elusuario($iduser);
function jsaGetLog($txt){
	$xHT		= new cHObject();
	$log_file	= "grep error_log /etc/php.ini";
	$vars		= file_get_contents(SYS_LOG_FILE);
	$vars		= $xHT->Out($vars, OUT_HTML);
	return $vars;
}
$jxc = new TinyAjax();
$jxc ->exportFunction('jsaGetLog', array('logtype'), "#txtlog");
$jxc ->process();

$xHP->init();
?>
<body>
<form name="frmreadlog" method="post" action="leer_log.frm.php">
	<table border='0' width='100%'>
		<tr>
			<td>LOG a Leer</td>
			<td><select name='logtype' id='idlogtype'>
			<option value="php">PHP ERROR LOG FILE</option>
			<option value="mysql">MYSQL ERROR LOG FILE</option>
			<option value="apache">APACHE ERROR LOG FILE</option>
			</select></td>
		</tr>
	</table>
	
	<input type="button" value="Leer" onclick="jsaGetLog()" />
	<div id="txtlog"></div>
</form>
<?php

$jxc ->drawJavaScript(false, true);

$MLog = ( isset($_POST["logtype"]) ) ? $_POST["logtype"] : false;
if($MLog != false){
	$tds	= "";
	$arrF	= array(
			"php" => vELOG_PHP,
			"mysql" => vELOG_MYSQL,
			"apache" => vELOG_APACHE
			);
	$mFile	= $arrF[$MLog];
	
/*$partes_ruta = pathinfo($mFile);
echo $partes_ruta['dirname'] . "<br>";
echo $partes_ruta['basename'] . "<br>";
echo $partes_ruta['extension'] . "<br>";
echo $partes_ruta['filename'], "<br>"; // desde PHP 5.2.0*/

			$FSize 		= filesize($mFile) / 1024;
			
			if( $FSize > 1000 ){ 
				echo "<p class='warn'>EL SISTEMA NO PUEDE LEER UN FICHERO TAN GRANDE $FSize</p>";
			} else {
				if( is_readable($mFile) == true ){
					$fp = @fopen ($mFile,"r");
				} else {
					$fp = false;
					echo "<p class='warn'>EL SISTEMA NO TIENE PERMISOS PARA LEER EL FICHERO $mFile</p>";
				}
				$fp = fopen ($mFile,"r");
					//
				$i 		= 0;
				if($fp !== false ){
					while ( feof($fp) !== false) {
						
						if ( $i <= 200 ){
							
							$line 		= trim(fgets($fp));
	
							$derror 	= substr($line, 0,22);
							$dMas 		= trim( substr($line, 22) );
							$dother 	= explode(":", $dMas, 2);
							$cls 		= "";
							$errType 	= trim($dother[0]);
							$errText 	= trim($dother[1]);
							
							if( strpos( strtolower($errType), "warning" )>0){
								$cls 	= "_warn";
							}
							if( strpos( strtolower($errType), "error" )>0){
									$cls 	= "_error";
							}
							if( strpos( strtolower($errType), "note" )>0){
								$cls 	= "_key";
							}
							$tds  .= "
							<tr>
								<th>$derror</th>
								<td class=\"$cls\">$errType</td>
								<td>$errText</td>
							</tr>
							";
						$i++;
						} else {
							@fclose($fp);
							break;
						}
						
					}
				}
			@fclose($fp);
		}
			

	echo "
	<table align=\"center\" border=2>
  <tbody>
    <tr>
      <td></td>
      <td></td>
    </tr>
    $tds
  </tbody>
</table>";
}
?>
</body>
</html>
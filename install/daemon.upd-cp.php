<?php
if(isset($_REQUEST["path"])){
	$PTH		= $_REQUEST["path"];
	$PTH 		= base64_decode($PTH);
	
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=\"daemon.update.php\"; ");
	
	echo "<?php
	ini_set('include_path', ini_get('include_path') .':" . $PTH . "/reports:" . $PTH . "/libs:" . $PTH . "/core');
	include_once ('" . $PTH . "/core/core.config.inc.php');
	include_once ('" . $PTH . "/core/core.db.inc.php');
	\$xPatch = new cSystemPatch();
	\$xPatch->patch(true);
	\$xPatch->setActualizarToLocalhost(fechasys(), 0);
?>";
}
?>
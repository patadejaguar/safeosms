<?php
$mpath	= realpath(dirname("../../"));
ini_set('include_path', ini_get('include_path') .':' . $mpath . '/reports:' . $mpath . '/libs:' . $mpath . '/core'); 
include_once ( $mpath . '/core/core.db.inc.php');
$xPatch = new cSystemPatch();



$localVersion		= $xPatch->getDBLocalVersion();
$codeVersion		= $xPatch->getDBCodeVersion();

if(isset($_REQUEST)){
	if(isset($_REQUEST["version"])){
		$localVersion	= $_REQUEST["version"];
	}
}

echo "DB:Version:" . $localVersion . "\r\n";
echo "CODE:Version:". $codeVersion . "\r\n";

if($localVersion<=$codeVersion){
	$xPatch->patch(true);
}

?>
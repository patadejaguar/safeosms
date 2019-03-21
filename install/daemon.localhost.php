<?php

$mpath	= realpath(dirname("../../"));
ini_set('include_path', ini_get('include_path') .':' . $mpath . '/reports:' . $mpath . '/libs:' . $mpath . '/core'); 
include_once ( $mpath . '/core/core.config.inc.php');
include_once ( $mpath . '/core/core.db.inc.php');
if(SAFE_ON_DEV == true){
	$xPatch = new cSystemPatch();
	$xPatch->patch(true);
	$xPatch->setActualizarToLocalhost(fechasys(), 0);
}
?>
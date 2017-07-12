<?php
function isSecure() {
  return
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443;
}
if(isSecure() == false){
	$URL	= ($_SERVER["SERVER_NAME"] == "") ? $_SERVER['SERVER_ADDR'] : $_SERVER["SERVER_NAME"];
	header("Location: https://" . $URL . "/");
}
exit;
?>
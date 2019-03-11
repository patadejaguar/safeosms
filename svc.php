<?php 
include_once("libs/aes.php");
include_once("core/core.config.inc.php");
include_once("core/core.error.inc.php");
include_once("core/core.security.inc.php");
include_once("core/core.db.inc.php");
include_once("core/core.db.dic.php");

$svc		= new MQLService("", "");
$svc->setKey(getClaveCifradoTemporal());

$data		= (isset($_REQUEST["data"])) ? $svc->getDecryptData($_REQUEST["data"]) : null;
$command	= (isset($_REQUEST["cmd"])) ? $svc->getDecryptData($_REQUEST["cmd"]) : null;
$context	= (isset($_REQUEST["ctx"])) ? $svc->getDecryptData($_REQUEST["ctx"]) : null;

echo $data;
echo $command;
echo $context;

?>

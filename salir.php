<?php
	@session_start();
	include_once "./core/core.config.inc.php";
	include_once "./core/core.error.inc.php";
	include_once "./core/go.login.inc.php";
	include_once "./core/core.init.inc.php";
	include_once "./core/core.deprecated.inc.php";
	include_once "./core/core.security.inc.php";
	//$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]
	$xUsr		= new cSystemUser($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]);
	$xUsr->setEndSession();
	header('location:./inicio.php');	exit();
?>
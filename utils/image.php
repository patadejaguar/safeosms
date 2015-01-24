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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
/**
 * @see : File Download by Agata Report Team Project
 * header("Location: download.php?type=$mimetype&download=$download&file=$Output");
 */
if(isset($_GET) && is_array($_GET))
{
    foreach ($_GET as $key=>$val)
    {
        ${$key}=$val;
    }
}
if(isset($_POST) && is_array($_POST))
{
    foreach ($_POST as $key=>$val)
    {
        ${$key}=$val;
    }
}
    header("Content-type: image/$type");
    //ISO-8859-1
    header("Content-Disposition: attachment;");
    header("Content-Disposition: filename=\"$file.$type\"");

	$file	= PATH_TMP . "/" .  $file . "." . $type;

readfile($file);
//echo 'df';
?>

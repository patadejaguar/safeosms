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
	//$xUsr	= new cSystemUser(); $xUsr->init();
//=====================================================================================================
$xLog	= new cCoreLog();

$pabsoluto	= parametro("pathabsoluto", false, MQL_BOOL);
$type		= parametro("type", "");
$type		= strtolower($type);
$file		= parametro("file", "", MQL_RAW);

$download	= parametro("download", "", MQL_RAW);

$xDoc		= new cDocumentos();
if($download == ""){
	$download	= $xDoc->cleanNombreArchivo($file, true);
}

/**
 * @see : File Download by Agata Report Team Project
 * header("Location: download.php?type=$mimetype&download=$download&file=$Output");
 */
	/*if(isset($_GET) && is_array($_GET))
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
	}*/
	switch ($type)
	{
	    case ('txt'):
	        $content_type = 'text/plain';
	        break;
	    case ('csv'):
	        $content_type = 'text/plain';
	        break;
	    case ('sbk'):
	        $content_type = 'text/enriched';
	        break;
	    case ('gz'):
	    	$content_type = 'application/x-gzip';
	    	break;   
	    case 'xml':
	        $content_type = 'text/xml';
	        break;
	    case 'xlsx':
	        $content_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
	        	break;
	     case 'xls':
	     	$content_type = 'application/vnd.ms-excel';
	        break;
	    case 'pdf':
	        $content_type = 'application/pdf';
	        break;
	    case 'ps':
	        $content_type = 'application/postscript';
	        break;
	    case 'sxw':
	        $content_type = 'application/sxw';
	        break;
	    case 'dia':
	        $content_type = 'application/dia';
	        break;
	    case "sql":
	    	$content_type = 'application/octet-stream';
	    	break;
	}
	$largo				= strlen($file) - (strlen($type) + 1);
	$xLog->add("WARN\tDescargando el recurso $file de tipo $content_type\r\n");
	if($type == "sbk"){
		if(strpos($file, $type, $largo) === false){
			$file		= $file . "." . $type;
		}
		$file			= PATH_BACKUPS . $file;
	} else if(isset($tabla)){
		$xSys			= new cSystemTask();
		$file			= $xSys->setBackupTable($tabla);
		$content_type 	= 'application/octet-stream';
		$type			= "sql.gz";
		$date			= date("Ymd");
		$download		= "$tabla-$date";
		$xLog->add("WARN\tHaciendo Backup de la Tabla $tabla\r\n");
	} else {
		if(strpos($file, $type, $largo) === false){
			$file		= $file . "." . $type;
		}
		if($pabsoluto == true){
			
		} else {
			$file			= PATH_BACKUPS . $file;
		}
		
	}

	if ($type != 'html')
	{
		header("Content-type: $content_type");
		//ISO-8859-1
		header("Content-Disposition: attachment; filename=\"$download.$type\"; ");
		//header("Content-Disposition: ");
	}
	$xLog->guardar(4);
	
	readfile($file);
//echo 'df';
?>

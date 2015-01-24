<?php
$sucursal 			    	= "matriz";					//nombre de la Sucursal, re recomienda la misma que la base de datos
$db_de_trabajo				= "matriz";					//Nombre de la Base de datos MySQL, se recomienda la misma que la sucursal
$V_0a744893951e0d1706ff74	= "root";					//Nombre de usuario MySQL
$V_9003d1df22eb4d38200150	= "";						//Password del usuario MySQL
$SAFEPathRoot				= "C:\\server2go\\htdocs";	//Ruta PHP donde se ubica el DOCUMENT_ROOT
$V_cf1e8c14e54505f60aa10c	= "http://localhost:4001";	//Ruta HTTP del sistema
$V_67e92c8765a9bc7fb2d335	= "localhost";				//Nombre o IP del Servidor Mysql
/**
 * Archivo de Configuracion en WINDOWS
 */
 /**
 * @var string      Ruta absoluta de la ubicacion de los archivos de PHPReports
 * Esto puede variar segun el SO, por los slash \ pueden ser dos o tres
 **/
$os_path_phpreports_engine	= "$SAFEPathRoot\\reports";
/**
 * @var string      Rutas adicionales de los includes en php
 * El separador de path en windows es ; y linux es :
 **/
$os_path_includes_str		= ini_get("include_path").";$SAFEPathRoot\\reports;$SAFEPathRoot\\libs;$SAFEPathRoot\\core";
$os_path_php_log		    = "";
$os_path_mysql_log		  	= "";
$os_path_apache_log	  		= "";
/**
 * @var string      Ruta del htdocs o raiz del sistema
 **/
$os_path_htdocs				= "$SAFEPathRoot";
$os_path_ctw				= "C:\\CompacW\\Empresas\\EMP5";
/**
 * @var string      Ruta absoluta del directorio de backups
 **/
$os_path_bks				= "C:\\server2go\\tmp\\";
/**
 * @var string      Ruta absoluta del directorio temporal de archivos
 **/
$os_path_tmp				= "C:\\server2go\\tmp\\";

?>

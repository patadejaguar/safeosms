<?php
include_once ("core/global.inc.php");
include_once ("core/global.static.inc.php");
include_once ("core/lang.inc.php");
include_once ("core/html.inc.php");
$dir			= $_SERVER["DOCUMENT_ROOT"];
$privateconfig	= "$dir/core/core.config.os." . strtolower(substr(PHP_OS, 0, 3)) .  ".inc.php";

if ( file_exists($privateconfig) ){ header("location: ../index.php"); } else {  }

$msg			= "";
//======================== Checar
//si guardar iniciar al index
$usrmysql		= (isset($_REQUEST["idusuario"])) ?  $_REQUEST["idusuario"] : "";
$pwdmysql		= (isset($_REQUEST["idpassword"])) ?  $_REQUEST["idpassword"] : "";
$srvmysql		= (isset($_REQUEST["idservidor"])) ?  $_REQUEST["idservidor"] : "localhost";
$dbmysql		= (isset($_REQUEST["iddb"])) ?  $_REQUEST["iddb"] : "";

$sucursal		= (isset($_REQUEST["idsucursal"])) ?  $_REQUEST["idsucursal"] : "";
$urlsys			= (isset($_REQUEST["idurl"])) ?  $_REQUEST["idurl"] : $_SERVER['SERVER_NAME'];
$urlpath		= (isset($_REQUEST["idpath"])) ?  $_REQUEST["idpath"] : $dir;
if( trim("$usrmysql$pwdmysql") != "" AND trim("$srvmysql$dbmysql") != "" ){
	
	$cnn = new mysqli($srvmysql, $usrmysql, $pwdmysql, $dbmysql);
	if ($cnn->connect_errno) {
		$msg	.= "ERROR EN LA CONEXION : ". $cnn->connect_error . " \n";
	} else {
		$rs		= $cnn->query("SHOW TABLES IN $dbmysql");
		if($rs == false){
			$msg	.= "ERROR(". $cnn->error . ") \r\n";
		} else {
			$fileconfig		= "<?php\r\n";
			$fileconfig		.= "\$V_0a744893951e0d1706ff74	= \"$usrmysql\";\r\n";
			$fileconfig		.= "\$V_9003d1df22eb4d38200150	= \"$pwdmysql\";\r\n";
			$fileconfig		.= "\$sucursal 			= \"$sucursal\";\r\n";
			$fileconfig		.= "\$db_de_trabajo			= \"$dbmysql\";\r\n";
			$fileconfig		.= "\$SAFEPathRoot			= \"$urlpath\";\r\n";
			$fileconfig		.= "\$os_path_phpreports_engine	= \"\$SAFEPathRoot/reports\";\r\n";
			$fileconfig		.= "\$os_path_includes_str		= ini_get(\"include_path\").\":\$SAFEPathRoot/reports:\$SAFEPathRoot/libs:\$SAFEPathRoot/core\";\r\n";
			$fileconfig		.= "\$os_path_php_log		= \"/var/log/php.log\";\r\n";
			$fileconfig		.= "\$os_path_mysql_log		= \"/var/log/mysql/mysql-slow.log\";\r\n";
			$fileconfig		.= "\$os_path_apache_log		= \"/var/log/apache2/error.log\";\r\n";
			$fileconfig		.= "\$os_path_htdocs		= \"\$SAFEPathRoot\";\r\n";
			$fileconfig		.= "\$os_path_ctw			= \"\";\r\n";
			$fileconfig		.= "\$os_path_bks			= \"$dir/tmp\";\r\n";
			$fileconfig		.= "\$os_path_tmp			= \"$dir/tmp/\";\r\n";
			$fileconfig		.= "\$V_cf1e8c14e54505f60aa10c	= \"$urlsys\";\r\n";
			$fileconfig		.= "\$V_67e92c8765a9bc7fb2d335	= \"$srvmysql\";\r\n";
			$fileconfig		.= "//\$fecha_de_inicio_operaciones	= \"\$VFecha\";\r\n";
			$fileconfig		.= "\r\n";
			$fileconfig		.= "\r\n?>";
			if(file_put_contents($privateconfig, $fileconfig, FILE_TEXT | LOCK_EX) == false){
				$msg	.= "ERROR AL GUARDAR EL ARCHIVO  \r\n";
			}
		}	
	}
}


$xFRM			= new cFInit("TR.Instalacion", "idfrm");

$xFRM->Obj->setAction("install.php");

$xFRM->Obj->setHeaders();

$xFRM->Obj->button("TR.Guardar", "$('#idfrm').submit()", "floppy");

$xFRM->Obj->text("idusuario", "Usuario MYSQL", $usrmysql);
$xFRM->Obj->text("idpassword", "Password MYSQL", $pwdmysql);
$xFRM->Obj->text("iddb", "Base de Datos MYSQL", $dbmysql);

$xFRM->Obj->text("idservidor", "Servidor MYSQL", $srvmysql);
$xFRM->Obj->text("idsucursal", "Sucursal por defecto", $sucursal);

$xFRM->Obj->text("idurl", "URL del servidor", $urlsys);

$xFRM->Obj->text("idpath", "Path del servidor", $urlpath);

$xFRM->Obj->addContent("<p class='warning'>$msg</p>");

echo $xFRM->Obj->render();

//var_dump($_REQUEST);

?>
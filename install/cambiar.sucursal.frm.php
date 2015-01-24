<?php
$mSuc			= ( isset($_GET["s"]) ) ? $_GET["s"] : "";
if ( $mSuc != ""){
	$fConfig	= "/home/sipakal/htdocs/core/core.config.os.lin.inc.php";
	unlink($fConfig);
	$txt		= "<?php
\$sucursal 							= \"$mSuc\";
\$db_de_trabajo						= \$sucursal;
\$V_0a744893951e0d1706ff74			= \"root\";
\$V_9003d1df22eb4d38200150			= \"pakal300\";
\$SAFEPathRoot						= \"/home/sipakal/htdocs\";
\$V_cf1e8c14e54505f60aa10c			= \"http://localhost\";
\$V_67e92c8765a9bc7fb2d335			= \"localhost\";
\$os_path_phpreports_engine			= \"\$SAFEPathRoot/reports\";
\$os_path_includes_str				= ini_get(\"include_path\") .\":\$SAFEPathRoot/reports:\$SAFEPathRoot/libs:\$SAFEPathRoot/core\";
\$os_path_php_log					= \"/var/log/apache2/error.log\";
\$os_path_mysql_log					= \"/var/log/mysql/mysql-slow.log\";
\$os_path_apache_log					= \"/var/log/apache2/error.log\";
\$os_path_htdocs						= \"\$SAFEPathRoot\";
\$os_path_ctw						= \"\";
\$os_path_bks						= \"/var/www/tmp/\";
\$os_path_tmp						= \"/var/www/tmp/\";
\$fecha_de_inicio_operaciones		= \"2008-12-31\";
?> ";
	$fw								= fopen($fConfig, "a+");
	fwrite($fw, $txt);
	fclose($fw);
	exit($fConfig);
}
?>
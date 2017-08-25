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
$xHP			= new cHPage("TR.COTIZADOR", HP_REPORT);
$nombre			= parametro("nombre");
$logo			= parametro("logo", true, MQL_BOOL);

$xHP->addJsFile("../js/base64.js");
$xHP->init("importHTML()");
if($logo == true){
	echo getRawHeader();
}
//echo "<table><tbody><tr><th>At'n:</th><td>$nombre</td></tr></tbody></table>";
echo "<h2 style='text-align:left'>At'n: $nombre</h2>";
echo "<h3>Cotizacion</h3>";
echo "<div id=\"idheader\"></div>";
echo "<div id=\"idcalendar\"></div>";

echo "<p>Promotor de Ventas</p><br /><br /><br /><br /><p>___________________________</p>";

if($logo == true){
	echo getRawFooter();
}
?>
<script>
function importHTML(){
	var hd = (session("data.head") == null) ? "" : base64.decode(session("data.head")) ;
	$("#idheader").html( hd );
	$("#idcalendar").html( base64.decode(session("data.plan")) );
	window.print();
}
</script>
<?php
$xHP->fin();
?>
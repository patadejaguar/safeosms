<?php
if ( !file_exists(dirname(__FILE__) . "/core/core.config.os." . strtolower(substr(PHP_OS, 0, 3)) .  ".inc.php") ){ header("location: install/install.php"); } else { /*session_destroy();*/  }
//------------------------- includes -------------------------
	include_once("./core/core.config.inc.php");
	include_once("./core/entidad.datos.php");
	
	include_once("./core/core.db.inc.php");
	include_once("./core/core.db.dic.php");
	include_once("./core/core.lang.inc.php");
	include_once("./core/core.html.inc.php");
	require_once("./libs/TinyAjax.php");
//-------------------------------------------------------------
$jxc 		= new TinyAjax();
$funid 		= getClaveCifradoTemporal();


/*function fu_76e369257240ded4b1c059cf20e8d9a4($low) {
		$funid2 = getClaveCifradoTemporal();
		$tab 	= new TinyAjaxBehavior();
		$pwd	= password_hash($low, PASSWORD_DEFAULT);
		$tab->add(TabSetValue::getBehavior("t$funid2", $pwd ));
		return $tab -> getString();
}
function jsaSetSucursal($sucursal){ getSucursal($sucursal); }

$jxc ->exportFunction('fu_76e369257240ded4b1c059cf20e8d9a4', array("t$funid"));
$jxc ->exportFunction('jsaSetSucursal', array("idsucursal"), "#idavisos");*/
//$jxc ->process();
$xPatch		= new cSystemPatch();
//$xPatch->patch();
$xLng		= new cLang();

$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); 											// rfc2616 - Section 14.21
header('Last-Modified: ' . $now);
header('Cache-Control: no-store, no-cache, must-revalidate'); 	// HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');	// HTTP/1.1
header('Pragma: no-cache');

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="description" content="" />
<meta name="format-detection" content="telephone=no" />
<meta name="author" content="Luis Humberto Balam Gonzalez" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		
<!--  alta en Google -->
<meta name="google-site-verification" content="SqK5tVk5JReoW3FFNXc546UJPulr5Ed4ZUgWXG9laJ4" />
<?php
echo "<title>" . EACP_NAME . "</title>"; 
?>
<link href="css/inicio.css" rel="stylesheet" type="text/css">
</head>
<script type="text/javascript" src="./js/jquery.js"></script>
<script type="text/javascript" src="./js/md5.js"></script>
<script type="text/javascript" src="./js/base64.js"></script>
<script type="text/javascript" src="./js/jscrypt/aes.js"></script>
<body onload='validar_nav();' >
<?php
$version		= "1.01.02";
$msg			= ( isset( $_GET[SYS_MSG] ) ) ? $_GET[SYS_MSG] : "";

if(isset($_SESSION)){
	if(isset($_SESSION[SYS_MSG])){
		$msg	.= $_SESSION[SYS_MSG];
	}
}

$sc				= new cGeneral_sucursales();
$data			= $sc->query()->select()->exec();
$analitycs		= "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-58995001-1', 'auto');
  ga('send', 'pageview');
</script>";
$adsense	= (SAFE_PAY_VERSION == "") ? "<script async src=\"https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
<!-- SAFE-OSMS -->
<ins class=\"adsbygoogle\" style=\"display:inline-block;width:234px;height:60px\" data-ad-client=\"ca-pub-1005748569860531\" data-ad-slot=\"9760371821\"></ins>
		<script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>" : "";

$txt	= "";

foreach($data as $datos){
	$sc->setData($datos);
	
	$valor	= $sc->codigo_sucursal()->v();
	$nombre	= $sc->nombre_sucursal()->v();
	$sel	= "";
	if($valor == "matriz"){ $sel = " selected='true' "; }
	$txt	.= "<option value=\"$valor\"$sel>$nombre</option>";
	
}
$demohtml	= "";


if(MULTISUCURSAL == true){
	$txt	= "<select id='idsucursal' name='idsucursal'>$txt</select>";
} else {
	$txt	= "<input id='idsucursal' name='idsucursal' type='hidden' value='" . DEFAULT_SUCURSAL . "'/>";
}


//detect demo
//'', 
$allowed_hosts = array('localhost');
if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
	
} else {
	$adsense	= "";
	$analitycs	= "";	
}

$allowed_hosts = array('demo.sipakal.com', 'localhost','demo.opensourcemicrofinance.org', "test6.opensourcemicrofinance.org", "test5.opensourcemicrofinance.org", "test4.opensourcemicrofinance.org");
if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {

} else {
	$demohtml	= "<div class='alert-box success' id='doptions'>
		<table><caption>Demo Users</caption>
		<tr><td>root</td><th>root</th></tr>
		<tr><td>cumplimiento</td><th>cumplimiento</th></tr>
		<tr><td>credito</td><th>credito</th></tr>
		<tr><td>cajero</td><th>cajero</th></tr>
		<tr><td>contabilidad</td><th>contabilidad</th></tr>
		</table></div>";	
}

$msg		= ($msg == "") ? "" : "<div class='alert-box warning' id='idavisos'>$msg</div>";
echo "
$analitycs
<form id=\"login\" name=\"frm$funid\" method=\"post\" action=\"clslogin.php\">
	<input id='ugps' name='ugps' type='hidden' />    
    <fieldset id=\"inputs\">
    </h1><img src='images/logo.png' style='max-height:180px;' /></h1>
    <h3>" . EACP_NAME . "</h3>
    <h4>" . SAFE_FIRM . "</h4>
        <input name=\"u$funid\" id=\"k$funid\" type=\"text\" placeholder=\"Usuario\" autofocus required>
        <input name=\"p$funid\" id=\"t$funid\" type=\"password\" required autocomplete='off' onchange='this.value=nv(this.value);return false;' placeholder=\"Password\">
        $txt
    </fieldset>
    <fieldset id=\"actions\">
         <input type=\"submit\" id=\"submit\" value=\"" . $xLng->getT("TR.Iniciar") . "\">
		<a class=\"button\" href=\"public/recover-pass.frm.php\" >" . $xLng->getT("TR.RECPASS") . "</a>
    </fieldset>
    $msg
  $demohtml   
</form>
<div id=\"banner\">$adsense</div>";


//$jxc ->drawJavaScript(false, true);

//console.log("Inicio limpio!:" + window.location);
//console.log("Inicio limpio!:" + self.location);
?>
</body>
<script>
var semilla = "<?php echo $funid; ?>";
window.localStorage.clear();


function validar_nav() {
	var isGecko 	= true;
	var intIndex	= navigator.userAgent.indexOf("Gecko/");
	var mWin		= String(top.location).indexOf("index");

	console.log("Inicio limpio!:" + top.location);
	
	if (mWin != -1) {
		top.location		= "./inicio.php";
	}
	if (intIndex == -1) {
		isGecko		= false;
	}
	if (navigator.product != "Gecko"){
		isGecko		= false;
	}

    if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(showLocation);
    }else{ 
        alert('Geolocation is not supported by this browser.');
    }
    
}
function nv(str){

	str	= Aes.Ctr.encrypt(str, semilla, 256)
	str	= base64.encode(str);
	
	return str;
}
function showLocation(position){
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;
    $("#ugps").val(latitude + "," + longitude);
    //console.log($("#ugps").val());

}
</script>
</html>
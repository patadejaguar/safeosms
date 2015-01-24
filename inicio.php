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
$funid 		= ROTTER_KEY;

function fu_76e369257240ded4b1c059cf20e8d9a4($low, $form) {
		$funid2 = ROTTER_KEY;
		$tab 	= new TinyAjaxBehavior();
		$tab -> add(TabSetValue::getBehavior("t$funid2", md5($low)));
		return $tab -> getString();
}
function jsaSetSucursal($sucursal){ getSucursal($sucursal); }

$jxc ->exportFunction('fu_76e369257240ded4b1c059cf20e8d9a4', array("t$funid", "frm$funid"));
$jxc ->exportFunction('jsaSetSucursal', array("idsucursal"), "#idavisos");
$jxc ->process();
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
	<!--  alta en Google -->
<meta name="google-site-verification" content="SqK5tVk5JReoW3FFNXc546UJPulr5Ed4ZUgWXG9laJ4" />
<?php
echo "<title>" . EACP_NAME . "</title>"; 
?>
<style>
html, body{    height: 100%;}
form {
	margin-left: auto;
    margin-right: auto;
    }
body {
    font: 12px 'Lucida Sans Unicode', 'Trebuchet MS', Arial, Helvetica;    
    margin: 0;
    background-color: #d9dee2;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#ebeef2), to(#d9dee2));
    background-image: -webkit-linear-gradient(top, #ebeef2, #d9dee2);
    background-image: -moz-linear-gradient(top, #ebeef2, #d9dee2);
    background-image: -ms-linear-gradient(top, #ebeef2, #d9dee2);
    background-image: -o-linear-gradient(top, #ebeef2, #d9dee2);
    background-image: linear-gradient(top, #ebeef2, #d9dee2);    
}

/*--------------------*/

#login
{
    background-color: #fff;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#eee));
    background-image: -webkit-linear-gradient(top, #fff, #eee);
    background-image: -moz-linear-gradient(top, #fff, #eee);
    background-image: -ms-linear-gradient(top, #fff, #eee);
    background-image: -o-linear-gradient(top, #fff, #eee);
    background-image: linear-gradient(top, #fff, #eee);  
    height: 600px;
    width: 400px;
    padding: 30px;
    
    text-align: center;
    z-index: 0;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;  
    -webkit-box-shadow:
          0 0 2px rgba(0, 0, 0, 0.2),
          0 1px 1px rgba(0, 0, 0, .2),
          0 3px 0 #fff,
          0 4px 0 rgba(0, 0, 0, .2),
          0 6px 0 #fff,  
          0 7px 0 rgba(0, 0, 0, .2);
    -moz-box-shadow:
          0 0 2px rgba(0, 0, 0, 0.2),  
          1px 1px   0 rgba(0,   0,   0,   .1),
          3px 3px   0 rgba(255, 255, 255, 1),
          4px 4px   0 rgba(0,   0,   0,   .1),
          6px 6px   0 rgba(255, 255, 255, 1),  
          7px 7px   0 rgba(0,   0,   0,   .1);
    box-shadow:
          0 0 2px rgba(0, 0, 0, 0.2),  
          0 1px 1px rgba(0, 0, 0, .2),
          0 3px 0 #fff,
          0 4px 0 rgba(0, 0, 0, .2),
          0 6px 0 #fff,  
          0 7px 0 rgba(0, 0, 0, .2);
}

#login:before
{
    content: '';
    position: absolute;
    z-index: -1;
    border: 1px dashed #ccc;
    top: 5px;
    bottom: 5px;
    left: 5px;
    right: 5px;
    -moz-box-shadow: 0 0 0 1px #fff;
    -webkit-box-shadow: 0 0 0 1px #fff;
    box-shadow: 0 0 0 1px #fff;
}

/*--------------------*/


fieldset{    border: 0;    padding: 0;    margin: 0; }
#inputs input, #inputs select
{
    background: #f1f1f1 url("./images/login-sprite.png") no-repeat;
    padding: 15px 15px 15px 30px;
    margin: 0 0 10px 0;
    width: 353px; /* 353 + 2 + 45 = 400 */
    border: 1px solid #ccc;
    -moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
    -moz-box-shadow: 0 1px 1px #ccc inset, 0 1px 0 #fff;
    -webkit-box-shadow: 0 1px 1px #ccc inset, 0 1px 0 #fff;
    box-shadow: 0 1px 1px #ccc inset, 0 1px 0 #fff;
}

input[type='text'] {  background-position: 5px -2px !important;}
input[type='password'] { background-position: 5px -52px !important; }
#inputs select {  background-position: 5px -2px !important;   width: 100%; }
#inputs input:focus {   background-color: #fff;    border-color: #e8c291;    outline: none;  -moz-box-shadow: 0 0 0 1px #e8c291 inset;   -webkit-box-shadow: 0 0 0 1px #e8c291 inset;  box-shadow: 0 0 0 1px #e8c291 inset; }
#actions { margin: 25px 0 0 0;}

#submit
{		
    background-color: #ffb94b;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#fddb6f), to(#ffb94b));
    background-image: -webkit-linear-gradient(top, #fddb6f, #ffb94b);
    background-image: -moz-linear-gradient(top, #fddb6f, #ffb94b);
    background-image: -ms-linear-gradient(top, #fddb6f, #ffb94b);
    background-image: -o-linear-gradient(top, #fddb6f, #ffb94b);
    background-image: linear-gradient(top, #fddb6f, #ffb94b);
    
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    
    text-shadow: 0 1px 0 rgba(255,255,255,0.5);
    
     -moz-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
     -webkit-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
     box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;    
    
    border-width: 1px;
    border-style: solid;
    border-color: #d69e31 #e3a037 #d5982d #e3a037;

    float: left;
    height: 35px;
    padding: 0;
    width: 120px;
    cursor: pointer;
    font: bold 15px Arial, Helvetica;
    color: #8f5a0a;
}

#submit:hover,#submit:focus
{		
    background-color: #fddb6f;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#ffb94b), to(#fddb6f));
    background-image: -webkit-linear-gradient(top, #ffb94b, #fddb6f);
    background-image: -moz-linear-gradient(top, #ffb94b, #fddb6f);
    background-image: -ms-linear-gradient(top, #ffb94b, #fddb6f);
    background-image: -o-linear-gradient(top, #ffb94b, #fddb6f);
    background-image: linear-gradient(top, #ffb94b, #fddb6f);
}	

#submit:active { outline: none; -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;  -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset; }
#submit::-moz-focus-inner{  border: none;}
#actions a {    color: #3151A2; float: right; line-height: 35px; margin-left: 10px;}
#back { display: block;    text-align: center;    position: relative;    top: 60px;    color: #999; }
.alert-box { color:#555; border-radius:10px; font-family:Tahoma,Geneva,Arial,sans-serif;font-size:1em; padding:10px 10px 10px 36px;  margin:10px; }
.alert-box  span.close-tip:hover { text-shadow: 1px 1px 1px rgba(0, 0, 0, 1); color: white;	border-color: rgb(18, 52, 86);}
.error, .credito-estado-20 {   background:#ffecec url('./images/notification/error.png') no-repeat 10px 50%;   border:1px solid #f5aca6; }
.success, .credito-estado-10 {   background:#e9ffd9 url('./images/notification/success.png') no-repeat 10px 50%;   border:1px solid #a6ca8a; }
.warning, .credito-estado-30 {   background:#fff8c4 url('./images/notification/warning.png') no-repeat 10px 50%;    border:1px solid #f2c779; }
.notice {   background:#e3f7fc url('./images/notification/notice.png') no-repeat 10px 50%;   border:1px solid #8ed9f6; }
#banner { right: 0;	bottom: 0;	position:fixed !important; }
th {text-align: right;}
table {width:100%;}
</style>
</head>
<script type="text/javascript" src="./js/md5.js"></script>
<body onload='validar_nav();' >
<?php
$version		= "1.01.02";
$msg			= ( isset( $_GET[SYS_MSG] ) ) ? $_GET[SYS_MSG] : "";
$sc				= new cGeneral_sucursales();
$data			= $sc->query()->select()->exec();

$adsense	= (SAFE_PAY_VERSION == "") ? "<script async src=\"https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
<!-- SAFE-OSMS -->
<ins class=\"adsbygoogle\" style=\"display:inline-block;width:234px;height:60px\" data-ad-client=\"ca-pub-1005748569860531\" data-ad-slot=\"9760371821\"></ins>
		<script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>" : "";

$txt	= "";

foreach($data as $datos){
	$sc->setData($datos);
	
	$valor	= $sc->codigo_sucursal()->v();
	$nombre	= $sc->nombre_sucursal()->v();
	$txt	.= "<option value=\"$valor\">$nombre</option>";
	
}
$demohtml	= "<div class='alert-box success'>
				<table>
				<caption>Demo Users</caption>
				<tr>
				<td>root</td><th>root</th>
				</tr><tr>
				<td>cumplimiento</td><th>cumplimiento</th>
				</tr><tr>
				<td>credito</td><th>credito</th>
				
				</tr><tr>
				<td>cajero</td><th>cajero</th>
				</tr><tr>
				<td>contabilidad</td><th>contabilidad</th>
				</tr><tr>
				</table>'";
//detect demo
$allowed_hosts = array('demo.sipakal.com', 'localhost');
if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
	//header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
	//exit;
	$demohtml	= "";
	//$adsense	= "";
}
$msg		= ($msg == "") ? "" : "<div class='alert-box warning' id='idavisos'>$msg</div>";
echo "

<form id=\"login\" name=\"frm$funid\" method=\"post\" action=\"clslogin.php\">

    $msg
    <fieldset id=\"inputs\">
    </h1><img src='images/logo.png' style='max-height:180px;' /></h1>
    <h3>" . EACP_NAME . "</h3>
        <input name=\"u$funid\" id=\"k$funid\" type=\"text\" placeholder=\"Usuario\" autofocus required>   
        <input name=\"p$funid\" id=\"t$funid\" type=\"password\" required onblur='this.value = hex_md5(this.value);' placeholder=\"Password\"  >
        <select id='idsucursal' name='idsucursal'>$txt</select>
    </fieldset>
    <fieldset id=\"actions\">
         <input type=\"submit\" id=\"submit\" value=\"" . $xLng->getT("TR.Iniciar") . "\">

    </fieldset>

  $demohtml   
</form>
<div id=\"banner\">$adsense</div>";
if ( $msg != "" ){	echo "<p class='aviso' id='idavisos'>$msg</p>"; }

$jxc ->drawJavaScript(false, true);

//console.log("Inicio limpio!:" + window.location);
//console.log("Inicio limpio!:" + self.location);
?>
</body>
<script>
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
}
</script>
</html>

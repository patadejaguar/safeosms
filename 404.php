<?php
/**
*	Archivo de redireccion en paginas
*	//
*/
include_once("core/core.deprecated.inc.php");
include_once("core/core.error.inc.php");
$id = ( isset($_GET["i"]) ) ? $_GET["i"] : DEFAULT_CODIGO_DE_ERROR; 

$xErr	= new cError($id);
$xErr->init();
	
?>
<html>
<head>
<meta charset="utf-8" />
<title>S.A.F.E. OSMS</title>
<link href="css/global.css" rel="stylesheet" type="text/css">
</head>
<style>
body {
	text-align: center;
	font-family: monospace;
	height: 100%;
}
.error {
  max-width: 90%;
  width: 50%;
  margin:0 auto;
  min-height: 400px;
  border-radius: 20px;
  border-style: solid;
  border-width: 2px;
  display: inline-block;
}

.message {
	width:60%;
	display: inline-block;
	margin-top:10%;
}
h3 {font-size:2em;}
.num {
	font-size: 3em;
	display: inline-block;
	margin-left:.1em;
	padding:.1em;
	border-style: solid;
	
}
.code {
	width:38%;
	display: inline-block;
	margin-top:10%;
	background-image: none;
	border-bottom-left-radius: 0;
	border-bottom-right-radius: 0;	
}
.common-error, .common-error hr {
  background: #f2c779;
  color: #f57900;
  border-color: #f57900;
}
.common-num {
	border-color: #f57900;
	background-color: #F7B21D;
	color: #fff8c4;	
}
.security-error {
  background: #f2dede;
  color: #b94a48;
  border-color: #b94a48;
}
.security-num {
	border-color: #b94a48;
	background-color: #c76e6d;
	color: white;	
}
.developer-error{
  background: #f2dede;
  color: #b94a48;
  border-color: #b94a48;
}
.developer-num{
	border-color: #b94a48;
	background-color: #c76e6d;
	color: white;	
}
</style>
<body>
<?php echo $xErr->getFicha(); ?>
</body>
</html>

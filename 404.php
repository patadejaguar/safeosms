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
<title>S.A.F.E. OSMS</title>
<link href="css/global.css" rel="stylesheet" type="text/css">
</head>
<style>
    body {
	text-align: center;
	font-family: monospace;  }
.error {
  background: #f2dede;
  color: #b94a48;
  border-color: #b94a48;
  max-width: 400px;
  width: 50%;
  margin:0 auto;
  min-height: 400px;
  border-radius: 10px;
  border-style: solid;
  border-width: 2px;
}
header {
	min-height: 40px;
	background-color: #c76e6d;
	border-color: #b94a48;
	color: white;
	background-image: none;
	border-bottom-left-radius: 0;
	border-bottom-right-radius: 0;	
}
.error h3 { font-size: 2em; }
.error h1 { font-size: 3em; }
</style>
<body>
<?php echo $xErr->getFicha(); ?>
</body>
</html>

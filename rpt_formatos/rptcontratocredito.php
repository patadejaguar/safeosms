<?php
//header("Content-type: text/plain");
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
$xHP			= new cHPage("CONTRATO DE CREDITO", HP_RECIBO);

$oficial 		= elusuario($iduser);

$idsolicitud 	= parametro("i", DEFAULT_CREDITO, MQL_INT); $idsolicitud = parametro("credito", $idsolicitud, MQL_INT); $idsolicitud = parametro("solicitud", $idsolicitud, MQL_INT);
$formato		= parametro("forma", 4, MQL_INT);

//$xHP->setNoDefaultCSS();
echo $xHP->getHeader();
?>
<style>
	h4, h3, h2, table, p {
		
	}
	h1,h2,h3,h4{
		text-align: center;
	}
    caption {
        text-align: left;
        font-weight: bold;
    }
	p {
		text-align: justify;
		text-transform: none !important;
		text-indent: 0;
	}
	body, html {
		font: 8pt "Trebuchet MS", Arial, Helvetica, sans-serif !important;
		line-height: 1.1em !important;
		font-stretch: condensed;
		
	}
	th {
		text-align: left;
		max-width: 25%;
		min-width: 25%;
		width: 25%;
        
		border-width: 1px;
		border-style: solid;
		border-color: #808080;
        border-right-color: #FFFFFF !important;
	}
	body{
		margin-top:0.22in;
		margin-bottom:0.22in;
		margin-left:0.22in;
		margin-right:0.22in;
	}
	td {
		min-width: 25%;
		border-width: 1px;
		border-style: solid;
		border-color: #808080;
        text-align: justify;
        border-left-color: #FFFFFF !important;
        
	}
	table {
		border-width: 1px;
		border-style: solid;
		border-color: #808080;
        border-spacing: 0;
        border-collapse: collapse;
	}
@page {
	/*size: portrait;*/
}	
</style>
<body>
<?php
	
	
	$xFecha				= new cFecha();
	$xCred = new cCredito($idsolicitud);
	$xCred->initCredito();

	$DCred				= $xCred->getDatosDeCredito();
	$numero_de_socio	= $xCred->getClaveDePersona();

	$xForma						= new cFormato($formato);
	$xForma->setCredito($idsolicitud);
	$xForma->setProcesarVars();
	
	echo $xForma->get();
?>
</body>
</html>

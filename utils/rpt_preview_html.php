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
$xHP			= new cHPage("Vista previa", HP_REPORT);

$oficial = elusuario($iduser);
$credito		= ( isset($_REQUEST["credito"])) ? $_REQUEST["credito"] : false;

echo $xHP->getHeader();

echo $xHP->setBodyinit("importHTML()");
?>
<style media="all">
	#idMvtosEsp, input[type=button], input[type=submit], .button, .boton, button, nav { display: none; }
	
</style>
<!-- -->
<?php
echo getRawHeader();

if($credito != false ){
$xCred		= new cCredito($credito); $xCred->init();

echo $xCred->getFichaDeSocio();
echo $xCred->getFicha();

}

echo "<div id=\"iPhantom\"></div>";
echo getRawFooter();
?>
</body>
<script  >
function importHTML(){
	var h	= (typeof opener.serializeHTML != "undefined" ) ? opener.serializeHTML : opener.document.body.innerHTML;
	
	document.getElementById("iPhantom").innerHTML = h;
	//document.getElementById("iPhantom").( opener.document.body.innerText);;
	iFrms = document.forms.length - 1;
	for(i=0; i<=iFrms; i++){
		document.forms[i].disabled = true;
		iElm = document.forms[i].elements.length - 1;
		for (ie=0; ie<=iElm; ie++){
			document.forms[i].elements[ie].value = opener.document.forms[i].elements[ie].value;
			document.forms[i].elements[ie].disabled = true;
		}
	}

	window.print();
}
</script>
</html>
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

$xHP		= new cHPage("TR.Listado de Creditos");

$oficial 	= elusuario($iduser);
$persona 	= (isset($_GET["i"])) ? $_GET["i"] : DEFAULT_SOCIO;
$persona 	= (isset($_GET["socio"])) ? $_GET["socio"] : $persona;
$persona 	= (isset($_GET["persona"])) ? $_GET["persona"] : $persona;
$persona 	= (isset($_GET["socio"])) ? $_GET["socio"] : $persona;

$f 			= (isset($_GET["f"])) ? $_GET["f"] : false;
$ctrl 		= (isset($_GET["control"])) ? $_GET["control"] : "idsolicitud";

$a 			= (isset($_GET["a"])) ? $_GET["a"] : "";
$tipos 		= (isset($_GET["tipo"])) ? $_GET["tipo"] : SYS_TODAS;
$tipos		= ($tipos == "todos") ? SYS_TODAS :  $tipos;

$OtherEvent	= (isset($_GET["ev"])) ? $_GET["ev"]: "";	//Otro Evento Desatado
$tiny 		= (isset($_GET["tinybox"])) ? true : false;

$slimit 	= "";
if($a == ""){
	$slimit = " LIMIT 0,20";
}

echo $xHP->getHeader();

$lsql		= new cSQLListas();
$sql		= $lsql->getListadoDeCreditos($persona, true, $tipos, false, "", true);


$xFRM		= new cHForm("frmlistacreditos");
$xT			= new cTabla($sql);

$xT->setEventKey("setCredito");
$xFRM->addHTML( $xT->Show($xHP->getTitle()) );
//$xFRM->addHTML("<code>$sql</code>");
$xFRM->addSubmit("TR.aceptar", "jsEnd()");

echo $xHP->setBodyinit();

echo $xFRM->get();

echo $xHP->setBodyEnd();
?>
<script>
var msrc		= null;

function setCredito(id){
	var mopts	= {};
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
<?php
		
			echo "
			if(msrc == null){} else {
				if(msrc.getElementById('$ctrl')){
					var rmt	= msrc.getElementById('$ctrl');
					rmt.value 	= id;
					rmt.focus();
					rmt.select();
					if(typeof msrc.jsGetDescCredito != \"undefined\"){ msrc.jsGetDescCredito();	}
				}
			}";
			if( $OtherEvent != ""){
					echo "if(msrc == null){} else { msrc.$OtherEvent;}";
			} 
		
?>
jsEnd();
}
function jsEnd(){
	var xGen = new Gen();
	xGen.close();
}
</script>
</html>

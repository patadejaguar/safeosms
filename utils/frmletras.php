<?php
/**
 * Titulo:
 * Actualizado:
 * Responsable:
 * Funcion:
 */
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

$xHP		= new cHPage("LETRAS X CREDITO");

$oficial 	= elusuario($iduser);
$solicitud 	= $_GET["i"];		//Solicitud
$f 		= (isset($_GET["f"])) ? $_GET["f"] : "frm";
if (!$solicitud){ echo "<script languaje=\"javascript\">window.close();</script>"; }
echo $xHP->getHeader();
$xPlan		= new cPlanDePagos();
$xPlan->initByCredito($solicitud);
$xT			= $xPlan->getEnTabla(false);
$xT->setEventKey("setLetra");
$xT->setFootSum(array(
		2 => "monto"
		      ));
?>
<body>
<hr />

<form name="" method="post" action="">
	<?php
		echo $xT->Show();
	?>
	<p class="aviso"><input type="button" onclick="window.close();" value="cerrar ventana" /></p>
</form>
</body>
<script  >
var msrc		= null;

function setLetra(id){
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
	
	if(msrc == null){} else {
		if (msrc.<?php echo $f; ?>) {
			msrc.<?php echo $f; ?>.idparcialidad.value = id;
		}
	}
	jsEnd();
}
function jsEnd(){
	var xGen = new Gen();
	xGen.close();
}
</script>
</html>

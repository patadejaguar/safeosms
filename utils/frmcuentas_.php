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
$xHP		= new cHPage("TR.Lista de Cuentas");
$xL			= new cSQLListas();

$oficial = elusuario($iduser);
//$i 			= $_GET["i"];
$f 				= parametro("f", false, MQL_RAW);
$tipo 			= parametro("a", false, MQL_INT); $tipo = parametro("tipo", $tipo, MQL_INT); 
$c				= parametro("c", "idcuenta");
$subtipo		= parametro("s", SYS_TODAS, MQL_INT);
$OtherEvent		= parametro("ev"); //Otro Evento Desatado
$slimit 		= "";
$mrkSubproducto		= ( setNoMenorQueCero( $subtipo ) <= 0) ?  "" : " AND (`captacion_cuentas`.`tipo_subproducto` = $subtipo) ";
$xEquiv		= new cSistemaEquivalencias();
if($tipo == iDE_CAPTACION){ $tipo = false; }
if($tipo == iDE_CVISTA){ $tipo = CLAVE_A_LA_VISTA; }
if($tipo == iDE_CINVERSION){ $tipo = CLAVE_INVERSION_A_PLAZO; }
/*if (!$i){
	echo "<script languaje=\"javascript\"> window.close(); </script>";
}*/

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$persona	= parametro("i", $persona, MQL_INT);

$xHP->init();

$xFRM		= new cHForm("frmcuentascapta", "./");

$msg		= "";

$ssql	= $xL->getListadoDeCuentasDeCapt($persona, false, $tipo, $subtipo);

$tb = new cTabla($ssql);
$tb -> setEventKey("setCuenta");
$tb->setUsarNullPorCero();

//$tb->
$xFRM->addHTML( $tb -> Show() );


$xFRM->addCerrar();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);

?>
<script  >
var xGen = new Gen();
function setCuenta(id){
	msrc	= null;
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
<?php
echo "
		if(msrc == null){ } else {
			if(msrc.getElementById('$c')){
				var rmt	= msrc.getElementById('$c');
				rmt.value 	= id;
				rmt.focus();
				rmt.select();
			}
		}";
		if( $OtherEvent != ""){
			echo "if(msrc == null){} else { msrc.$OtherEvent;}";
		} 

?>
jsEnd();
}
function jsEnd(){		xGen.close(); }
</script>
<?php $xHP->fin(); ?>
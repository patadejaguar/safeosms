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
$xHP		= new cHPage("TR.Notas de Personas");
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$predef		= parametro("d");
$div		= STD_LITERAL_DIVISOR;
$defval		= "$persona" . $div . "$credito" . $div . "1" . $div . "99" . $div . "ANOTE_AQUI_SU_TEXTO";
if ( $predef == "" ){
	$defval		= $predef;
}
$D			= explode($div, $defval);
$mem		= (isset($D[4])) ? $D[4] : "";

$xHP->init();

//jsbasic("frmhistorial", "", ".");



$xHP->init();

$xFRM		= new cHForm("frmhistorial", "./frmhistorialdesocios.php?action=" . MQL_ADD);
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();
$msg		= "";

$xFRM->addJsBasico();
$xFRM->addCreditBasico();
$xFRM->addSubmit();

$xFRM->addHElem( $xSel->getListaDeTiposDeMemoPersonas()->get(true) );
$xFRM->addHElem( $xChk->get("TR.Notificar", "idnotificar") );
$xFRM->OTextArea("idmemo", $mem, "TR.Texto del memo");








	if($persona > DEFAULT_SOCIO){
	$idgrupo 		= parametro("idgrupo", DEFAULT_GRUPO, MQL_INT);
	$txtmemo 		= parametro("idmemo");
	$tipomemo 		= parametro("idtipodememo");
	$notificar		= parametro("idnotificar", false, MQL_BOOL);

	$fechamemo 		= fechasys();
		if(trim($txtmemo) != ""){
			$xSoc		= new cSocio($persona);
			$xSoc->init();
			$xSoc->addMemo($tipomemo, $txtmemo, $credito, $fechamemo, $notificar, $notificar);
			$xFRM->addAviso("EL REGISTRO SE HA HECHO SATISFACTORIAMENTE");
			if(MODO_DEBUG == true){
				$xFRM->addLog($xSoc->getMessages());
			}
		}
	}
	//.-
	echo $xFRM->get();	
?>

</body>
</html>

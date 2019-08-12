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
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$credito 	= parametro("docto", $credito, MQL_INT);

$tipomemo 	= parametro("idtipodememo",DEFAULT_TIPO_MEMO, MQL_INT); $tipomemo 	= parametro("tipo",$tipomemo, MQL_INT);
$predef		= parametro("d");
$txtmemo 	= parametro("idmemo");
$lista		= parametro("lista", false, MQL_BOOL);

$div		= STD_LITERAL_DIVISOR;

$xHP->init();

if($clave > 0){
	//$x
}

$xHP->init();

$xFRM		= new cHForm("frmhistorial", "./frmhistorialdesocios.php?action=" . MQL_ADD);
$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();
$msg		= "";
//$xW			= new cSeguimientoWathsApp();
//$xW->setRequerirRegistro();
//$xW->setConfirmarRegistro("977446");
if($persona > DEFAULT_SOCIO and $action == MQL_ADD){
	$idgrupo 		= parametro("idgrupo", DEFAULT_GRUPO, MQL_INT);
	$notificar		= parametro("idnotificar", false, MQL_BOOL);
	$notificarpush	= parametro("idnotificarpush", false, MQL_BOOL);
	
	$fechamemo 		= fechasys();
	if(trim($txtmemo) != ""){
		$xSoc		= new cSocio($persona);
		if($xSoc->init() == true){
			$xSoc->addMemo($tipomemo, $txtmemo, $credito, $fechamemo, $notificar, $notificar, $notificarpush);
			$xFRM->addAvisoRegistroOK( $xSoc->getMessages() );
			//$xFRM->addLog($xSoc->getMessages());
			if($notificar == true){
				$xFRM->addCerrar();
			}	else {
				$xFRM->addCerrar("", 5);
			}
			
		} else {
			$xFRM->addAvisoRegistroError($xSoc->getMessages());
		}
	}
} else {
	
	

if($credito > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$persona	= $xCred->getClaveDePersona();
		$xFRM->addHElem($xCred->getFichaMini());
	}
}
if($credito > DEFAULT_CREDITO AND $persona > 0){
	$xFRM->OHidden("credito", $credito);
	$xFRM->OHidden("persona", $persona);
} else {
	$xFRM->addJsBasico();
	$xFRM->addCreditBasico($credito, $persona);
}	

$xFRM->addSubmit();
if($tipomemo > 0){
	$xFRM->OHidden("idtipodememo", $tipomemo);
	$xFRM->setNoAcordion();
	
	$xTipoM	= new cPersonasTiposMemos($tipomemo);
	
	$xTipoM->init();
	$xFRM->addSeccion("idtit", $xTipoM->getNombre());
	$xFRM->endSeccion();
	$xFRM->OHidden("idnotificar", "false");
} else {
	$xFRM->addHElem( $xSel->getListaDeTiposDeMemoPersonas("", $tipomemo)->get(true) );
	$xFRM->OCheck("TR.Notificar", "idnotificar");
	if(SAFE_ON_DEV == true){
		$xFRM->OCheck("TR.Notificar Push", "idnotificarpush");
	}
}


$xFRM->OTextArea("idmemo", $txtmemo, "TR.Texto del memo");

//$xFRM->addHElem( $xChk->get("TR.Notificar", "idnotificar") );
if($lista == true){
	$xFRM->addSeccion("idnot", "TR.NOTAS ANTERIORES");
	
	$xLi	= new cSQLListas();
	$sql	= $xLi->getListadoDeNotas(false, $credito, $xTipoM->NOTA_COBRANZA);
	
	$xT		= new cTabla($sql);
	$xT->setOmitidos("tipo");
	$xT->setOmitidos("usuario");
	$xFRM->addHElem($xT->Show());
	$xFRM->endSeccion();
}




}
//.-
echo $xFRM->get();	
?>

</body>
</html>

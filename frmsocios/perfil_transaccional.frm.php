<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_FORM);

$DDATA		= $_REQUEST;
$jxc = new TinyAjax();

function jsaGuardarPerfil($persona, $tipo, $pais, $monto, $numero, $observaciones){
    $xAP	= new cAMLPersonas($persona);
    $xAP->init();
    $xAP->setGuardarPerfilTransaccional($tipo, $pais, $monto, $numero, $observaciones);
	$QL		= new cSQLListas();
	$xT		= new cTabla($QL->getListadoDePerfil($persona) );
	$xT->addTool(SYS_DOS);
    return $xT->Show() . $xAP->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaGuardarPerfil', array('idpersona', 'idtipotransaccion', 'idpais', 'idmonto', 'idnumero', 'idobservaciones'), "#idperfil");
$jxc ->process();

$persona	= (isset($DDATA["persona"])) ? $DDATA["persona"] : DEFAULT_SOCIO;
$persona	= (isset($DDATA["socio"])) ? $DDATA["socio"] : $persona;

$credito	= (isset($DDATA["credito"])) ? $DDATA["credito"] : DEFAULT_CREDITO;
$jscallback	= (isset($DDATA["callback"])) ? $DDATA["callback"] : "";

$tiny		= (isset($DDATA["tiny"])) ? $DDATA["tiny"] : "";

$form		= (isset($DDATA["form"])) ? $DDATA["form"] : "";

echo $xHP->getHeader();
$jsb		= new jsBasicForm("", iDE_CAPTACION);

echo $xHP->setBodyinit();

$xFRM		= new cHForm("frmperfiltransaccional", "perfil_transaccional.frm.php");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xTxt2		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

$jsb->setNameForm( $xFRM->getName() );
$xFRM->addHElem( $xSel->getListaDePerfilTransaccional()->get("TR.tipo de perfil", true  ) );
$xFRM->addHElem( $xSel->getListaDePaises()->get("TR.pais de origen", true  ) );
$xFRM->addHElem( $xTxt->getDeMoneda("idmonto", "TR.monto maximo de operaciones mensuales") );
$xFRM->addHElem( $xTxt->getDeMoneda("idnumero", "TR.numero maximo de operaciones mensuales") );
 
//$xFRM->addHElem(  );
$arr		= array_merge($xTxt->getIDs(), $xTxt2->getIDs(), $xSel->getIDs());

$xFRM->addHElem( $xTxt2->get("idobservaciones", "", "TR.observaciones") );
//$xFRM->addHElem( $xTxt->get("idnumero", 0, "TR.Numero maximo de operaciones mensuales"));
//$xFRM->addCreditBasico();
$QL		= new cSQLListas();
$xT		= new cTabla($QL->getListadoDePerfil($persona) );
$xT->addTool(SYS_DOS);

$xFRM->addHTML("<div id='idperfil'>" . $xT->Show() . "</div>");
$xFRM->addHTML("<input type='hidden' value='$persona' id='idpersona' />");
//$xFRM->addAviso( $xFRM->getProcessIDs($arr) );

$xFRM->addSubmit("", "setGuardarRegistro()");

echo $xFRM->get();

echo $xHP->setBodyEnd();
//$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
    function setGuardarRegistro() {
		jsaGuardarPerfil();
		document.getElementById("id-frmperfiltransaccional").reset();
    }
</script>
<?php
$xHP->end();
?>
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
$xHP		= new cHPage("TR.PERFIL_TRANSACCIONAL", HP_FORM);

$DDATA		= $_REQUEST;
$jxc = new TinyAjax();

function jsaGuardarPerfil($persona, $tipo, $pais, $monto, $numero, $observaciones, $origen, $finalidad){
    $xAP	= new cAMLPersonas($persona);
    $xAP->init();
    $xAP->setGuardarPerfilTransaccional($tipo, $pais, $monto, $numero, $observaciones, false, $origen, $finalidad);
	$QL		= new cSQLListas();
	$xT		= new cTabla($QL->getListadoDePerfil($persona) );
	$xT->addTool(SYS_DOS);
    return $xT->Show() . $xAP->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaGuardarPerfil', array('idpersona', 'idtipotransaccion', 'idpais', 'idmonto', 'idnumero', 'idobservaciones', 'idorigen', 'idfinalidad'), "#idperfil");
$jxc ->process();

$persona	= (isset($DDATA["persona"])) ? $DDATA["persona"] : DEFAULT_SOCIO;
$persona	= (isset($DDATA["socio"])) ? $DDATA["socio"] : $persona;

$credito	= (isset($DDATA["credito"])) ? $DDATA["credito"] : DEFAULT_CREDITO;
$jscallback	= (isset($DDATA["callback"])) ? $DDATA["callback"] : "";

$tiny		= (isset($DDATA["tiny"])) ? $DDATA["tiny"] : "";

$form		= (isset($DDATA["form"])) ? $DDATA["form"] : "";

echo $xHP->getHeader();


echo $xHP->setBodyinit();

$xFRM		= new cHForm("frmperfiltransaccional", "perfil_transaccional.frm.php");
$xFRM->setNoAcordion();
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
//$xNot		= new cHNotif();
//$btn		= $xNot->getNoticon("10", "alert('A')", "fa-save");

$xFRM->setTitle($xHP->getTitle());
$xFRM->addSeccion("idadd", "TR.AGREGAR PERFIL_TRANSACCIONAL");
$xFRM->addHElem( $xSel->getListaDePerfilTransaccional()->get("TR.tipo de perfil", true  ) );
$xFRM->addHElem( $xSel->getListaDePaises()->get("TR.pais de origen", true  ) );
$xTxt->setDivClass("");
$xFRM->addDivSolo($xTxt->get("idorigen", "", "TR.ORIGEN DE LOS RECURSOS"), $xTxt->get("idfinalidad", "", "TR.APLICACION DE LOS RECURSOS"), "tx24", "tx24");
$xFRM->OMoneda("idmonto", 0, "TR.monto maximo de operaciones mensuales");
$xFRM->OMoneda("idnumero", 0, "TR.numero maximo de operaciones mensuales");
$xFRM->addObservaciones();
$xFRM->endSeccion();
$QL		= new cSQLListas();
$xT		= new cTabla($QL->getListadoDePerfil($persona) );
$xT->addTool(SYS_DOS);
$xFRM->addSeccion("trlista", "TR.LISTA DE PERFIL_TRANSACCIONAL");
$xFRM->addHTML("<div id='idperfil'>" . $xT->Show() . "</div>");
$xFRM->endSeccion();
$xFRM->addHTML("<input type='hidden' value='$persona' id='idpersona' />");

$xFRM->addSubmit("", "setGuardarRegistro()");

echo $xFRM->get();


//$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
function setGuardarRegistro(){
	jsaGuardarPerfil();
	document.getElementById("id-frmperfiltransaccional").reset();
}
</script>
<?php
$xHP->fin();
?>
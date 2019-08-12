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
$xHP		= new cHPage("TR.REESTRUCTURAR Credito", HP_FORM);
$xSel		= new cHSelect();
$jxc		= new TinyAjax();
$xRuls		= new cReglaDeNegocio();

$BloquearCap	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_REEST_BLOQ_CAP);
$RequiereInt	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_REEST_REQ_INT);


function jsaGuardarReestructura($credito, $monto, $pagos, $periocidad, $tasa, $observaciones, $producto, $tipopago){
    $tasa	= $tasa / 100;
    
    $xCred	= new cCredito($credito); $xCred->init();
    $xCred->setReconvenido($monto, 0, $tasa, $periocidad, $pagos, $observaciones, false, false, $tipopago, $producto);
	//TODO: Cambiar a solo root
    $xF		= new cFileLog();
    $xF->setWrite($xCred->getMessages());
    $xF->setClose();
	
    return  $xF->getLinkDownload("Descarga de Log");
}

//$credito	= (isset($_REQUEST["credito"])) ? $_REQUEST["credito"] : false;
$credito	= parametro("credito", 0, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

$jxc ->exportFunction('jsaGuardarReestructura', array('idsolicitud', 'idmonto', 'idpagos', 'idperiocidad', 'idtasa', 'idobservaciones',
													'idpdto', 'idtipopago'), "#resultados");
$jxc ->process();

$xHP->init();

//$xHP->setTamannio(500,800);
//$jsb	= new jsBasicForm("frmrenegociar");


echo "<input type='hidden' id='idsolicitud' value='$credito'>";

$xCred	= new cCredito($credito); $xCred->init();
$xFRM	= new cHForm("frmrenegociar", "", "idfrmrenegociar");


 $oUL	= new cHUl();



 $xFRM->addSeccion("idinfocreddiv", "TR.CREDITO");
$xFRM->addHElem($xCred->getFichaMini());
$xFRM->endSeccion();
$xFRM->setTitle($xHP->getTitle());



$xFRM->addSeccion("idmodifdiv", "TR.Opciones de Reestructura");

$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("idpdto", $xCred->getClaveDeProducto())->get(true), true );


$xFRM->addHElem($xSel->getListaDePeriocidadDePago("idperiocidad",$xCred->getPeriocidadDePago())->get("TR.NUEVA PERIOCIDAD", true) );


$xFRM->addHElem($xSel->getListaDeTipoDePago("idtipopago", $xCred->getTipoDePago())->get(true));

if($BloquearCap == true){
	$xFRM->ODisabledM("idmonto", $xCred->getSaldoActual(), "TR.MONTO A REESTRUCTURAR");
} else {
	$xFRM->OMoneda2("idmonto", $xCred->getSaldoActual(), "TR.MONTO A REESTRUCTURAR");
}



$xFRM->OMoneda("idpagos", $xCred->getPagosAutorizados(), "TR.PAGOS NUEVO");
$xFRM->OMoneda("idtasa", ($xCred->getTasaDeInteres()*100), "TR.TASA NUEVA");

$xFRM->addObservaciones();

$xFRM->endSeccion();
$xFRM->addSeccion("idinfodiv", "TR.CAMBIOS");

$xFRM->addHTML(
			$oUL->li("Se Clona el Credito")->
			li("Eliminar Plan de Pagos")->
			li("Reestructurar SDPM")->
			li("Recalcular Intereses Devengados")->
			li("Cambiar el Numero de Pagos")->
			li("Generar Movimiento de Fin de Mes")->
			end()
			);
$xFRM->endSeccion();

$xFRM->addGuardar("jsGuardarCambios()");
$xFRM->addAviso("", "resultados");

echo $xFRM->get();


$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
var xG		= new Gen();
function jsGuardarCambios(){
	xG.confirmar({msg: "Desea Renovar este Credito?", callback:jsaGuardarReestructura, cancelar: jsCancelar});
}
function jsCancelar(){
	xG.close();
}
</script>
<?php
$xHP->fin();
?>
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
$xHP		= new cHPage("Renegociar Creditos", HP_FORM);

function jsaGuardarReestructura($credito, $monto, $pagos, $periocidad, $tasa, $observaciones, $producto, $tipopago){
    $tasa	= $tasa / 100;
    
    $xCred	= new cCredito($credito); $xCred->init();
    $xCred->setReconvenido($monto, 0, $tasa, $periocidad, $pagos, $observaciones, false, false, $tipopago, $producto);
	
    $xF		= new cFileLog();
    $xF->setWrite($xCred->getMessages());
    $xF->setClose();
	
    return  $xCred->getMessages(OUT_HTML) . $xF->getLinkDownload("Descarga de Log");
}

$credito	= (isset($_REQUEST["credito"])) ? $_REQUEST["credito"] : false;


$jxc = new TinyAjax();
$jxc ->exportFunction('jsaGuardarReestructura', array('idsolicitud', 'idmonto', 'idpagos', 'idperiocidad', 'idtasa', 'idobservaciones',
													'idpdto', 'idtipopago'), "#resultados");
$jxc ->process();

echo $xHP->getHeader();

//$xHP->setTamannio(500,800);
$jsb	= new jsBasicForm("frmrenegociar");
echo $xHP->setBodyinit();

echo "<input type='hidden' id='idsolicitud' value='$credito'>";

$xCred	= new cCredito($credito); $xCred->init();
$oBtn	= new cHButton(); $oTxt	= new cHText(); $oFech	= new cHDate(); $oSel	= new cSelect(""); $oUL	= new cHUl(); $oTa	= new cHTextArea();

$xCE	= new cCreditos_estatus();
$xSelEA	= $xCE->query()->html()->select( $xCE->descripcion_estatus()->get() );

$xCP	= new cCreditos_periocidadpagos();
$xSelCP	= $xCP->query()->html()->select( $xCP->descripcion_periocidadpagos()->get() );

$xCTP	= new cCreditos_tipo_de_pago();
$xSelTP	= $xCTP->query()->html()->select( $xCTP->descripcion()->get()  );

$xPP	= new cCreditos_tipoconvenio();
$xSelPP	= $xPP->query()->html()->select($xPP->descripcion_tipoconvenio()->get());

$oFRM	= new cHForm("frmrenegociar", "", "idfrmrenegociar");


$oFRM->addHElem( $xSelPP->get("idpdto", "Producto Actual", $xCred->getClaveDeProducto() ) );
$oFRM->addHElem( $xSelCP->get("idperiocidad", "Nueva Periocidad", $xCred->getPeriocidadDePago() ) );
$oFRM->addHElem( $xSelTP->get("idtipopago", "Nuevo Tipo de Pago", $xCred->getTipoDePago() ) );

$oFRM->addHElem( $oTxt->getDeMoneda("idmonto", "Monto a Renegociar", $xCred->getSaldoActual() ) );
$oFRM->addHElem( $oTxt->getDeMoneda("idpagos", "Pagos nuevos", $xCred->getPagosAutorizados() ) );
$oFRM->addHElem( $oTxt->getDeMoneda("idtasa", "Tasa Nueva", ($xCred->getTasaDeInteres()*100) ) );
//$oFRM->addHElem( $oTxt->getDeMoneda("idinteres", "Interes a Renegociar", $xCred->getInteresNormalPorPagar() ) );

$oFRM->addHElem( $oTa->get("idobservaciones", "", "Observaciones") );


$oFRM->addHTML("<p class='aviso' id='resultados'></p>");

	$oFRM->addHTML(
			$oUL->li("Se Clona el Credito")->
			li("Eliminar Plan de Pagos")->
			li("Reestructurar SDPM")->
			li("Recalcular Intereses Devengados")->
			li("Cambiar el Numero de Pagos")->
			li("Generar Movimiento de Fin de Mes")->
			end()
			);

$oFRM->addHElem( $oBtn->getBasic("Guardar", "jsGuardarCambios", "guardar", "idguardar" ) );
$oFRM->addHElem( $oBtn->getBasic("Cancelar", "jsCancelarCambios", "cancelar", "idcancelar" ) );

echo $oFRM->get();


echo $xHP->setBodyEnd();
$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
    var xGen	= new Gen();
    function jsGuardarCambios(){
        var si	= confirm("DESEA REESTRUCTURAR ESTE CREDITO?\nESTO NO PUEDE DESHACERSE.\nTENGA CUIDADO!.\n-----------------------------");
		if(si){
			jsaGuardarReestructura();
		}
    }
    function jsCancelarCambios(){
	//esconder qtip
	$(window).qtip("hide");
	//$(window).remove();
	//window.close();
	xGen.close();
    }
</script>
<?php
$xHP->end();
?>
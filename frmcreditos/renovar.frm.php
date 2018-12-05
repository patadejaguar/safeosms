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
$xHP		= new cHPage("TR.RENOVAR Credito", HP_FORM);
$xSel		= new cHSelect();
$jxc		= new TinyAjax();


$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

function jsaGuardarRenovacion($credito, $monto, $pagos, $periocidad, $tasa, $observaciones, $producto, $tipopago, $destino){
    $tasa		= $tasa / 100;   
    $xCred		= new cCredito($credito);
    $TipoAut	= CREDITO_TIPO_AUTORIZACION_RENOVACION;
    $xOrg		= new cCreditosDatosDeOrigen();
    $nuevoCred	= 0;
    if($xCred->init() == true){
    	
    	$persona	= $xCred->getClaveDePersona();
    	
    	$xCredN		= new cCredito(false, $persona);
    	$xCredN->add($producto, $persona, false, $monto, $periocidad, $pagos, 0, $destino,false,
    			false, "", $observaciones, false, false, $xCred->getTipoDePago(), $xCred->getTipoDeBaseDeInteres(), $tasa, false, $xCred->getClaveDeEmpresa(), $TipoAut, $xCred->getClaveDeCredito(), $xOrg->ORIGEN_RENOVACION);
    	$nuevoCred	= $xCredN->getClaveDeCredito();
    	//Agregar Nota de Renovacion
    	$xSoc	= new cSocio($persona);
    	if($xSoc->init() == true){
    		$xSoc->addMemo(MEMOS_TIPO_NOTA_RENOVACION, "Credito Renovado de $credito a $nuevoCred", $credito);
    	}
    }
    //$xCred->setReconvenido($monto, 0, $tasa, $periocidad, $pagos, $observaciones, false, false, $tipopago, $producto);

	//TODO: Cambiar a solo root
    $xF		= new cFileLog();
    
    $xF->setWrite($xCred->getMessages());
    $xF->setWrite($xCredN->getMessages());
    
    $xF->setClose();
	
    return  $xF->getLinkDownload("Descarga de Log");
}

//$credito	= (isset($_REQUEST["credito"])) ? $_REQUEST["credito"] : false;


$jxc ->exportFunction('jsaGuardarRenovacion', array('idsolicitud', 'idmonto', 'idpagos', 'idperiocidad', 'idtasa', 'idobservaciones',
													'idpdto', 'idtipopago', 'iddestino'), "#resultados");
$jxc ->process();

$xHP->init();

//$xHP->setTamannio(500,800);
//$jsb	= new jsBasicForm("frmrenegociar");





$xCred	= new cCredito($credito); $xCred->init();
$xFRM	= new cHForm("frmrenegociar", "", "idfrmrenegociar");
$oUL	= new cHUl();



$xFRM->OHidden("idsolicitud", $credito);
$xFRM->addHElem($xCred->getFichaMini());

$xFRM->setTitle($xHP->getTitle());

$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("idpdto", $xCred->getClaveDeProducto())->get(true), true );


$xFRM->addHElem($xSel->getListaDePeriocidadDePago("idperiocidad",$xCred->getPeriocidadDePago())->get("TR.NUEVA PERIOCIDAD", true) );

$xFRM->addHElem($xSel->getListaDeTipoDePago("idtipopago", $xCred->getTipoDePago())->get(true));

$xFRM->addHElem($xSel->getListaDeDestinosDeCredito("iddestino", $xCred->getClaveDeDestino())->get(true));



$xFRM->OMoneda("idpagos", $xCred->getPagosAutorizados(), "TR.PAGOS NUEVO");
$xFRM->OMoneda("idtasa", ($xCred->getTasaDeInteres()*100), "TR.TASA NUEVA");
$xFRM->OMoneda("idmonto", $xCred->getMontoAutorizado(), "TR.MONTO A RENOVAR");


$xFRM->addObservaciones();




$xFRM->addHTML(
			$oUL->li("Se Agrega un Nuevo Credito")->
			li("Se copian Algunos parametros")->
			end()
			);

$xFRM->addGuardar("jsGuardarCambios()");
$xFRM->addAviso("", "resultados");

echo $xFRM->get();


$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
var xG		= new Gen();
function jsGuardarCambios(){
	xG.confirmar({msg: "Desea Renovar este Credito?", callback:jsaGuardarRenovacion, cancelar: jsCancelar});
}
function jsCancelar(){
	xG.close();
}
</script>
<?php
$xHP->fin();
?>
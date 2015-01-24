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
    $theFile            = __FILE__;
    $permiso            = getSIPAKALPermissions($theFile);
    if($permiso === false){    header ("location:../404.php?i=999");    }
    $_SESSION["current_file"]    = addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.Agregar Producto", HP_FORM);
$xT			= new cTipos();
$ql			= new MQL();
$dSN		= array("1"=>"SI", "0"=>"NO");
$msg		= "";
$jxc = new TinyAjax();

function jsaSetClonarProducto($idclonado, $nuevoid, $nombre){
	$xProducto	= new cProductoDeCredito($idclonado);
	$xProducto->add($nuevoid, $nombre, $idclonado);
	return $xProducto->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaSetClonarProducto', array('idclonado', 'idnumero', 'iddescripcion'), "#fb_frm");
$jxc ->process();

$producto 	= parametro("producto", null, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO, MQL_RAW);
$opcion		= parametro("tema", SYS_NINGUNO, MQL_RAW);
$xHP->init();


$xSel		= new cHSelect();
$xFRM		= new cHForm("frm", "./");
$xProd		= new cCreditos_tipoconvenio();
$lastid		= $xProd->query()->getLastID();
$xProd->setData( $xProd->query()->initByID($producto) );

$nombre		= $xProd->descripcion_tipoconvenio()->v();
$xFRM->setTitle($xFRM->lang("duplicar") . " $nombre");
$xFRM->OMoneda("idnumero", $lastid, "TR.Codigo");
$xFRM->OText("iddescripcion", "", "TR.Nombre");
$xFRM->OHidden("idclonado", $producto);
$xFRM->addGuardar("jsaSetClonarProducto()");
$xFRM->addFooterBar("&nbsp;");


echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
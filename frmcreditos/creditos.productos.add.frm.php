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
$jxc 		= new TinyAjax();

function jsaSetClonarProducto($idclonado, $nuevoid, $nombre, $dupcostos, $dupdoctos, $dupreglas,$dupetapas,$dupprom, $dupreqs, $dupotros){
	$xProducto	= new cProductoDeCredito($idclonado);
	$xQL		= new MQL();
	$xT			= new cTipos();
	$dupcostos	= $xT->cBool($dupcostos);
	$dupdoctos	= $xT->cBool($dupdoctos);
	$dupreglas	= $xT->cBool($dupreglas);
	$dupetapas	= $xT->cBool($dupetapas);
	$dupprom	= $xT->cBool($dupprom);
	$dupreqs	= $xT->cBool($dupreqs);
	$dupotros	= $xT->cBool($dupotros);
	
	if($xProducto->add($nuevoid, $nombre, $idclonado) == true){
		//Duplicar Formatos
		$xQL->setRawQuery("INSERT INTO `creditos_prods_formatos` SELECT NULL, $nuevoid,`formato_id`,`estatus`,`etapa_id`,`opcional` FROM `creditos_prods_formatos` WHERE `estatus`=1 AND `producto_credito_id`=$idclonado");
		//
		if($dupcostos == true){
			$xQL->setRawQuery("INSERT INTO `creditos_productos_costos` SELECT NULL, $nuevoid, `clave_de_operacion`,`unidades`,`unidad_de_medida`,`editable`,`en_plan`,`exigencia`,`estatus`,`aplicar_desde`,`aplicar_hasta` FROM `creditos_productos_costos` WHERE `clave_de_producto`=$idclonado AND `estatus`=1");
		}
		if($dupdoctos == true){
			$xQL->setRawQuery("INSERT INTO `creditos_prods_doctos` SELECT NULL, $nuevoid,`documento_id`,`estatus`,`etapa_id`,`opcional` FROM `creditos_prods_doctos` WHERE `estatus`=1 AND `producto_credito_id`=$idclonado");
		}
		if($dupetapas == true){
			$xQL->setRawQuery("INSERT INTO `creditos_productos_etapas` SELECT NULL, $nuevoid,`etapa`,`nombre`,`tags`,`permisos`,`orden` FROM `creditos_productos_etapas` WHERE `producto`=$idclonado");
		}
		if($dupotros == true){
			$xQL->setRawQuery("INSERT INTO `creditos_productos_otros_parametros` 
						SELECT NULL, $nuevoid,`clave_del_parametro`,`valor_del_parametro`,`fecha_de_alta`,`fecha_de_expiracion` FROM `creditos_productos_otros_parametros` WHERE `clave_del_producto`=$idclonado");
		}
		if($dupprom == true){
			$xQL->setRawQuery("INSERT INTO `creditos_productos_promo` SELECT NULL, `tipo_promocion`,`fecha_inicial`,`fecha_final`,`tipo_operacion`,`condiciones`,`num_items`,`descuento`,`precio`,`sucursal`,`estatus`,$nuevoid FROM `creditos_productos_promo` WHERE `producto`=$idclonado AND `estatus`=1");
		}
		if($dupreglas == true){
			$xQL->setRawQuery("INSERT INTO `creditos_productos_reglas`
						SELECT NULL, $nuevoid,`tipo_regla`,`clave_Interna`,`evoluciona`,`contador`,`num_minimo`,`num_maximo`,`monto_min`,`monto_max`,`tasa_min`,`tasa_max`,`sujeto` FROM `creditos_productos_reglas` WHERE `producto_id`=$idclonado");
		}
		if($dupreqs == true){
			$xQL->setRawQuery("INSERT INTO `creditos_productos_req` SELECT NULL, $nuevoid,`tipo_req`,`descripcion`,`numero`,`ruta_validacion`,`escore`,`etapa`,`requerido`,`clave`,`etapa_id` FROM `creditos_productos_req` WHERE `producto`=$idclonado");
		}
	}
	return $xProducto->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaSetClonarProducto', array('idclonado', 'idnumero', 'iddescripcion','idduplicarcostos', 'idduplicardocumentos','idduplicarreglas',
		'idduplicaretapas', 'idduplicarpromociones', 'idduplicarrequerimientos', 'idduplicarotros'), "#fb_frm");
$jxc ->process();

$producto 	= parametro("producto", null, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO, MQL_RAW);
$opcion		= parametro("tema", SYS_NINGUNO, MQL_RAW);

$xHP->init();

$xSel		= new cHSelect();
$xFRM		= new cHForm("frmcredsprodadd", "./");
$xProd		= new cProductoDeCredito($producto);
$xTbl		= new cCreditos_tipoconvenio();


$lastid		= $xTbl->query()->getLastID();

if($xProd->init() == true){

	$xFRM->setTitle($xFRM->getT("TR.DUPLICAR ") . $xProd->getNombre());
	$xFRM->addSeccion("idavisocop1", "TR.DUPLICAR DE");
	$xFRM->addHElem( $xProd->getFicha() );
	$xFRM->endSeccion();
	
	$xFRM->addSeccion("idavisocop2", "TR.DESCRIPCION");
	$xFRM->OMoneda("idnumero", $lastid, "TR.Codigo");
	$xFRM->OText("iddescripcion", "", "TR.Nombre");
	$xFRM->OHidden("idclonado", $producto);
	$xFRM->addGuardar("jsaSetClonarProducto()");
	
	$xFRM->endSeccion();
	
	
	$xFRM->addSeccion("idavisocop2", "TR.OPCIONES");
	$xFRM->OCheck("TR.COPIAR COSTOS", "idduplicarcostos", true);
	$xFRM->OCheck("TR.COPIAR DOCUMENTOS", "idduplicardocumentos", true);
	$xFRM->OCheck("TR.COPIAR REGLAS", "idduplicarreglas", true);
	$xFRM->OCheck("TR.COPIAR ETAPAS", "idduplicaretapas", true);
	
	$xFRM->OCheck("TR.COPIAR OTROS", "idduplicarotros", true);
	
	$xFRM->OCheck("TR.COPIAR PROMOCIONES", "idduplicarpromociones", true);
	$xFRM->OCheck("TR.COPIAR REQUERIMIENTOS", "idduplicarrequerimientos", true);
	
	$xFRM->endSeccion();
	
	$xFRM->setFieldsetClass("fieldform frmpanel");
	
	$xFRM->addFooterBar("&nbsp;");
	
}




echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
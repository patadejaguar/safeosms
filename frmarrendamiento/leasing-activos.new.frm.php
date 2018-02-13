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
$xHP		= new cHPage("TR.AGREGAR FLOTA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$idleasing	= parametro("idleasing",0 , MQL_INT );

$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cLeasing_activos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmactivos", "leasing-activos.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

if($clave <= 0){
	$xTabla->idleasing_activos("NULL");
	$xTabla->clave_leasing($idleasing);
}

$xFRM->setNoAcordion();
$xFRM->OHidden("idleasing_activos", $xTabla->idleasing_activos()->v());

if($clave > 0 ){

	
} else {
	
	if($idleasing > 0){
		$xLeas	= new cCreditosLeasing($idleasing);
		if($xLeas->init() == true){
			$xTabla->persona($xLeas->getClaveDePersona());
			$xTabla->credito( $xLeas->getClaveDeCredito() );
			$xTabla->marca($xLeas->getVehiculoMarca() );
			$xTabla->valor_nominal($xLeas->getVehiculoValor());
			$xTabla->descripcion( $xLeas->getVehiculoDescripcion() );
			$xTabla->valor_residual( $xLeas->getValorResidual() );
			$xTabla->annio( $xLeas->getVehiculoAnnio() );
			$xTabla->valor_venta( $xLeas->getValorDeVenta() );
			
		}
	} else {

		$xFRM->addCreditBasico($xTabla->credito()->v(), $xTabla->persona()->v());

	}
}
if($xTabla->credito()->v() > DEFAULT_CREDITO){
	$xCred	= new cCredito($xTabla->credito()->v());
	if($xCred->init() == true ){
		$xFRM->addSeccion("idcd", "TR.CREDITO");
		$xFRM->addHElem($xCred->getFichaMini());
		$xFRM->endSeccion();
	}
}


$xFRM->addSeccion("idcd2", "TR.ACTIVO");



$xFRM->OHidden("persona", $xTabla->persona()->v());
$xFRM->OHidden("credito", $xTabla->credito()->v());
$xFRM->addHElem( $xSel->getListaDeProveedores("proveedor",$xTabla->proveedor()->v())->get(true) );
//==== Agregar aseguradoras
$xFRM->addHElem( $xSel->getListaDeAseguradoras("aseguradora",$xTabla->aseguradora()->v())->get(true) );

if($clave >= 0){
	$xFRM->OHidden("clave_leasing", $xTabla->clave_leasing()->v());
} else {
	if($idleasing > 0){
		$xFRM->OHidden("clave_leasing", $xTabla->clave_leasing()->v());
	} else {
		$xFRM->OMoneda("clave_leasing", $xTabla->clave_leasing()->v(), "TR.IDLEASING");
	}
}





$xFRM->OText_13("placas", $xTabla->placas()->v(), "TR.PLACAS");
$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.DESCRIPCION");




$xFRM->ODate("fecha_registro", $xTabla->fecha_registro()->v(), "TR.FECHA DE REGISTRO");

$xFRM->ODate("fecha_compra", $xTabla->fecha_compra()->v(), "TR.FECHA DE COMPRA");
$xFRM->ODate("fecha_mtto", $xTabla->fecha_mtto()->v(), "TR.PROXIMO MTTO");
$xFRM->ODate("fecha_seguro", $xTabla->fecha_seguro()->v(), "TR.PROXIMO AUTOSEGURO");

$xSelTA	= $xSel->getListaDeCatalogoGenerico("leasing_activos_tipos", "tipo_activo", $xTabla->tipo_activo()->v());
$xSelTA->setDivClass("tx4 tx18 blue");
$xSelTA->setLabel("TR.TIPO ACTIVO");
$xFRM->addHElem($xSelTA->get(true));

$xSelTS	= $xSel->getListaDeCatalogoGenerico("leasing_seguro_tipo", "tipo_seguro", $xTabla->tipo_seguro()->v());
$xSelTS->setLabel("TR.TIPO AUTOSEGURO");
$xSelTS->setDivClass("tx4 tx18 orange");
$xFRM->addHElem($xSelTS->get(true));
//$xFRM->OMoneda("tipo_activo", $xTabla->tipo_activo()->v(), "TR.TIPO ACTIVO");
//$xFRM->OMoneda("tipo_seguro", $xTabla->tipo_seguro()->v(), "TR.TIPO SEGURO");


$xFRM->OTasa("tasa_depreciacion", $xTabla->tasa_depreciacion()->v(), "TR.TASA DEPRECIACION");
$xFRM->OMoneda2("valor_nominal", $xTabla->valor_nominal()->v(), "TR.VALOR DE FACTURA");
$xFRM->OMoneda2("valor_venta", $xTabla->valor_venta()->v(), "TR.VALOR DE VENTA");
$xFRM->OMoneda2("valor_residual", $xTabla->valor_residual()->v(), "TR.VALOR RESIDUAL");




$xMarca	= $xSel->getListaDeVehiculosMarcas("marca", $xTabla->marca()->v());
$xFRM->addHElem($xMarca->get(true));

$xFRM->OText_13("factura", $xTabla->factura()->v(), "TR.FACTURA");

//$xFRM->OMoneda("marca", $xTabla->marca()->v(), "TR.MARCA");
$xFRM->OText("color", $xTabla->color()->v(), "TR.COLOR");
$xFRM->OText_13("serie", $xTabla->serie()->v(), "TR.SERIE");
$xFRM->OText_13("motor", $xTabla->motor()->v(), "TR.MOTOR");
$xFRM->OText_13("serie_nal", $xTabla->serie_nal()->v(), "TR.SERIENAL");

$xFRM->OText_13("annio", $xTabla->annio()->v(), "TR.ANNIO");

$xFRM->endSeccion();

$xFRM->addCRUD($xTabla->get(), true);
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();
?>
<script>

function jsEvaluarSalida(){
	$("#persona").val( $("#idsocio").val() );
	$("#credito").val( $("#idsolicitud").val() );
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
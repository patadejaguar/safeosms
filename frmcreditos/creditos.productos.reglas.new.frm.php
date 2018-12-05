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
$xHP		= new cHPage("TR.REGLAS AGREGAR", HP_FORM);
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
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);




$producto	= parametro("producto",0, MQL_INT);


$xHP->init();


/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cCreditos_productos_reglas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmpreglas", "creditos.productos.reglas.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

if($clave<=0){
	$xTabla->idcreditos_productos_reglas('NULL');
}
if($producto<=0){
	$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("producto_id", false, true)->get(true) );
} else {
	$xTabla->producto_id($producto);
	$xFRM->OHidden("producto_id", $xTabla->producto_id()->v());
}

$xFRM->OHidden("idcreditos_productos_reglas", $xTabla->idcreditos_productos_reglas()->v());


$xFRM->OText_13("clave_Interna", $xTabla->clave_Interna()->v(), "TR.CLAVE INTERNA");


//$xFRM->ONumero("tipo_regla", $xTabla->tipo_regla()->v(), "TR.TIPO REGLA");
//$xFRM->ONumero("evoluciona", $xTabla->evoluciona()->v(), "TR.EVOLUCIONA");
$xFRM->addHElem( $xSel->getListaDeCatalogoGenerico("cpdtos_t_evolucion", "evoluciona", $xTabla->evoluciona()->v())->get("TR.EVOLUCIONA", true) );
$xFRM->addControEvt("evoluciona", "jsDisEnCtrls0", "blur");

$xFRM->addHElem( $xSel->getListaDeCatalogoGenerico("cpdtos_t_sujetos", "sujeto", $xTabla->sujeto()->v() )->get("TR.SUJETO", true) );
$xFRM->addControEvt("sujeto", "jsDisEnCtrls", "blur");


$xFRM->addHElem( $xSel->getListaDeCatalogoGenerico("cpdtos_t_regla", "tipo_regla", $xTabla->tipo_regla()->v())->get("TR.TIPO", true) );
//$xSelF	= $xSel->getListaDeCamposPorTabla("sujeto", $xTabla->sujeto()->v(),  "TR.SUJETO", "creditos_solicitud");
//$xFRM->addHElem( $xSelF );

//$xFRM->OText("sujeto", $xTabla->sujeto()->v(), "TR.SUJETO");


$xFRM->OEntero("contador", $xTabla->contador()->v(), "TR.CONTADOR");

$xFRM->OEntero("num_minimo", $xTabla->num_minimo()->v(), "TR.MINNUMERO");
$xFRM->OEntero("num_maximo", $xTabla->num_maximo()->v(), "TR.MAXNUMERO");
$xFRM->OMoneda("monto_min", $xTabla->monto_min()->v(), "TR.MAXVALOR");
$xFRM->OMoneda("monto_max", $xTabla->monto_max()->v(), "TR.MINVALOR");

$xFRM->OTasaInt("tasa_min", $xTabla->tasa_min()->v(), "TR.TASA MINIMO");
$xFRM->OTasaInt("tasa_max", $xTabla->tasa_max()->v(), "TR.TASA MAXIMO");

$xFRM->addCRUD($xTabla->get(), true);

//$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();

?>
<script>
var xG	= new Gen();
function jsDisEnCtrls0(){
	var vv	= $("#evoluciona").val();
	if(vv != 2){ //Si no es Nivel
		xG.ver("contador");
	} else {
		xG.ver("contador", true);
	}
}
function jsDisEnCtrls(){
	var vv	= $("#sujeto").val();
	
	//console.log(vv);
	
	switch(vv){
		case "TASA_INTERES":
			xG.ver("num_minimo");
			xG.ver("num_maximo");
			xG.ver("monto_min");
			xG.ver("monto_max");
			xG.ver("tasa_max", true);
			xG.ver("tasa_min", true);
		break;
		case "TASA_MORATORIO":
			xG.ver("num_minimo");
			xG.ver("num_maximo");
			xG.ver("monto_min");
			xG.ver("monto_max");
			xG.ver("tasa_max", true);
			xG.ver("tasa_min", true);
		break;
		case "PERIODICIDAD":
			xG.ver("num_minimo", true);
			xG.ver("num_maximo");
			xG.ver("monto_min");
			xG.ver("monto_max");
			xG.ver("tasa_max");
			xG.ver("tasa_min");
			
		break;
		case "PAGOS":
			xG.ver("num_minimo", true);
			xG.ver("num_maximo", true);
			xG.ver("monto_min");
			xG.ver("monto_max");
			xG.ver("tasa_max");
			xG.ver("tasa_min");			
		break;
		case "MONTO_CREDITO":
			xG.ver("num_minimo");
			xG.ver("num_maximo");
			xG.ver("monto_min", true);
			xG.ver("monto_max", true);
			xG.ver("tasa_max");
			xG.ver("tasa_min");
		break;
		case "TIPO_CUOTA":
			xG.ver("num_minimo", true);
			xG.ver("num_maximo");
			xG.ver("monto_min");
			xG.ver("monto_max");
			xG.ver("tasa_max");
			xG.ver("tasa_min");			
		break;
	}

	
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
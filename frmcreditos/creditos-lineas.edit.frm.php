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
$xHP		= new cHPage("TR.EDITAR CREDITOS_LINEAS", HP_FORM);
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

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$pzomaximo	= 0;
$disponible = 0;

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCreditos_lineas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmcreditolineas", "creditos_lineas.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

$xFRM->OHidden("idcreditos_lineas", $xTabla->idcreditos_lineas()->v());

$persona	= $xTabla->numero_socio()->v();


if($persona > DEFAULT_SOCIO){
	$xFRM->OHidden("idsocio", $persona);
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->addHElem( $xSoc->getFicha(false, false, "", true) );
	}
} else {
	$xFRM->addPersonaBasico("", false,  $xTabla->numero_socio()->v());
}

if($clave <= 0){
	$xTabla->idusuario(getUsuarioActual());
	$xTabla->sucursal(getSucursal());
	$xTabla->eacp(EACP_CLAVE);
	$xTabla->fecha_de_alta(fechasys());
	
	$xFRM->OHidden("fecha_de_alta", $xTabla->fecha_de_alta()->v(), "TR.FECHA DE ALTA");
	$xFRM->OHidden("idusuario", $xTabla->idusuario()->v(), "TR.IDUSUARIO");
	$xFRM->OHidden("sucursal", $xTabla->sucursal()->v(), "TR.SUCURSAL");
	$xFRM->OHidden("eacp", $xTabla->eacp()->v(), "TR.EACP");
} else {
	$xDO				= new cCreditosDatosDeOrigen();
	$tipo_origen		= $xDO->ORIGEN_LINEA;
	$dispuesto 			= $xQL->getDataValue("SELECT getMontoActualPorOrigen($clave, $tipo_origen) AS 'monto' ", 'monto');
	$disponible 		= setNoMenorQueCero( ($xTabla->monto_linea()->v() - $dispuesto));
	$xTabla->saldo_disponible($disponible);
	$xFRM->OHidden("saldo_disponible", $xTabla->saldo_disponible()->v(), "TR.SALDO DISPONIBLE");
}

$xFRM->OHidden("numero_socio", $xTabla->numero_socio()->v(), "TR.NUMERO SOCIO");

$xFRM->ODate("fecha_de_vencimiento", $xTabla->fecha_de_vencimiento()->v(), "TR.FECHA DE VENCIMIENTO");

$xFRM->OMoneda2("monto_linea", $xTabla->monto_linea()->v(), "TR.MONTO LINEA");

$pzomaximo	= $xF->setRestarFechas($xTabla->fecha_de_vencimiento()->v(), fechasys() );
$pzomaximo	= floor(($pzomaximo / $xTabla->periocidad()->v()));

//$xFRM->OMoneda("periocidad", $xTabla->periocidad()->v(), "TR.PERIOCIDAD");
$xFRM->addHElem($xSel->getListaDePeriocidadDePago("periocidad", $xTabla->periocidad()->v())->get(true) );

$xFRM->OTasa("tasa", $xTabla->tasa()->v(), "TR.TASA");

$xFRM->addHElem( $xSel->getListaDeOficiales("oficial_de_credito", "", $xTabla->oficial_de_credito()->v())->get(true) );

$xFRM->OText("numerohipoteca", $xTabla->numerohipoteca()->v(), "TR.Datos de Garantia");
$xFRM->OMoneda2("monto_hipoteca", $xTabla->monto_hipoteca()->v(), "TR.monto de Garantia");

$xFRM->OText("observaciones", $xTabla->observaciones()->v(), "TR.OBSERVACIONES");

$xFRM->ODisabled_13("saldo_disponible", $xTabla->saldo_disponible()->v(), "TR.SALDO DISPONIBLE");

//$xFRM->OMoneda("estado", $xTabla->estado()->v(), "TR.ESTADO");

//$xFRM->OText("fecha_ultima_operacion", $xTabla->fecha_ultima_operacion()->v(), "TR.FECHA ULTIMA OPERACION");

//$xFRM->OMoneda("oficial_de_credito", $xTabla->oficial_de_credito()->v(), "TR.OFICIAL DE CREDITO");
//$xFRM->OText("fecha_de_cancelacion", $xTabla->fecha_de_cancelacion()->v(), "TR.FECHA DE CANCELACION");
//$xFRM->OText("razones_de_cancelacion", $xTabla->razones_de_cancelacion()->v(), "TR.RAZONES DE CANCELACION");

$xFRM->setValidacion("monto_linea", "validacion.nozero");
$xFRM->setValidacion("numerohipoteca", "validacion.novacio", "TR.MSG_DATA_REQUIRED", true);
$xFRM->setValidacion("monto_hipoteca", "validacion.nozero");

//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);

$xFRM->OButton("TR.AGREGAR CREDITO", "jsAgregarCredito()", $xFRM->ic()->CREDITO);
$xFRM->OButton("TR.REPORTE CREDITOS_LINEAS", "var xC=new CredGen(); xC.getReporteDeLinea({id:$clave})", $xFRM->ic()->REPORTE);

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>
var pzomaximo	= <?php echo $pzomaximo; ?>;
var xC			= new CredGen();
var vTipoOrigen	= Configuracion.credito.origen.lineas;

function jsAgregarCredito(){
	var idpersona	= $("#numero_socio").val();

	
	if(idpersona > DEFAULT_SOCIO){
		var producto	= Configuracion.credito.productos.lineas;//$("#producto").val();
		var periocidad	= $("#periocidad").val();
		var pagos		= pzomaximo; //$("#plazo").val();
		var monto		= 0;//$("#sal").val();
		//var aplicacion	= Configuracion.credito.destinos.arrendamientopuro;//$("#aplicacion").val(); destino:aplicacion,
		
		var idcontrol	= $("#idcreditos_lineas").val();
		
		var idoficial	= $("#oficial_de_credito").val();
		var tasa		= $("#tasa").val();
		var saldo		= $("#saldo_disponible").val();
		//origen 270 PRECLIENTES
		if(saldo > 0){
			xC.addCredito({persona: idpersona, monto: monto, producto:producto, origen:vTipoOrigen, idorigen:idcontrol, frecuencia: periocidad, pagos: pagos,  oficial:idoficial, tasa:tasa});
		} else {
			xG.alerta({msg: "CREDITOS_LINEAS SIN SALDO"});
		}		
	} else {
		xG.alerta({msg: "Debe Vincular o Agregar una Persona"});
	}
}
</script>
<?php
$xHP->fin();
?>
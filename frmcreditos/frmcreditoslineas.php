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
$xHP		= new cHPage("TR.CREDITOS_LINEAS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
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
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); 
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();


$xSel		= new cHSelect();
$xF			= new cFecha();


/* ===========		FORMULARIO		============*/
$clave		= parametro("idcreditos_lineas", null, MQL_INT);
$xTabla		= new cCreditos_lineas();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave		= parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->idcreditos_lineas($clave);
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmcreditos_lineas", "frmcreditoslineas.php?action=$step");
$xFRM->setTitle($xHP->getTitle());
$clave 		= parametro($xTabla->getKey(), null, MQL_INT);

if( ($action == MQL_ADD OR $action == MQL_MOD) AND ($clave != null) ){
	
	$xTabla->setData( $xTabla->query()->initByID($clave));
	$xTabla->setData($_REQUEST);
	$xTabla->numero_socio($persona);
	$xTabla->idusuario(getUsuarioActual());
	$xTabla->estado(SYS_UNO);
	$xTabla->sucursal(getSucursal());
	$xTabla->eacp(EACP_CLAVE);
	//$fecha	= $xF->getFechaISO($xTabla->fecha_de_alta()->v());
	//$xTabla->fecha_de_alta( $fecha);
	
	$xTabla->eacp(EACP_CLAVE);
	$xTabla->numero_socio($persona);
	$xTabla->idusuario(getUsuarioActual());
	$xTabla->sucursal(getSucursal());
	if($xTabla->tasa()->v() > 1){ //Es mayor al 100% anual
		$idtasa	= $xTabla->tasa()->v() / 100;
		$xTabla->tasa($idtasa);
	}
	if($action == MQL_ADD){
		$xTabla->fecha_ultima_operacion(fechasys());
		$xTabla->saldo_disponible($xTabla->monto_linea()->v());
		$xTabla->fecha_de_cancelacion($xF->getFechaMaximaOperativa());
		
		$xTabla->query()->insert()->save();
	} else {
		$xTabla->query()->update()->save($clave);
	}
	$xFRM->addAvisoRegistroOK();
	$xFRM->addCerrar();
} else {
	$xFRM->OHidden("idcreditos_lineas", $xTabla->idcreditos_lineas()->v(), "TR.idcreditos lineas");
	if($persona > DEFAULT_SOCIO){
		$xFRM->OHidden("idsocio", $persona);
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xFRM->addHElem( $xSoc->getFicha(false, false, "", true) );
		}
	} else {
		$xFRM->addPersonaBasico("", false,  $xTabla->numero_socio()->v());
	}
	//$xFRM->OMoneda("numero_socio", $xTabla->numero_socio()->v(), "TR.numero socio");
	
	$xFRM->ODate("fecha_de_alta", $xTabla->fecha_de_alta()->v(), "TR.fecha de registro");
	
	$xFRM->OMoneda2("monto_linea", $xTabla->monto_linea()->v(), "TR.MAXVALOR");
	$xFRM->setValidacion("monto_linea", "validacion.nozero");
	
	//$xFRM->OText("fecha_de_vencimiento", $xTabla->fecha_de_vencimiento()->v(), "TR.fecha de vencimiento");
	$xFRM->OFechaLarga("fecha_de_vencimiento", $xTabla->fecha_de_vencimiento()->v(), "TR.FECHA DE VENCIMIENTO");
	$xFRM->addHElem( $xSel->getListaDeOficiales("oficial_de_credito", SYS_USER_ESTADO_ACTIVO, $xTabla->oficial_de_credito()->v())->get(true) );
	
	$idtasa		= $xTabla->tasa()->v() * 100;
	$idmora		= $xTabla->tasa_mora()->v() * 100;
	
	//$xFRM->OTasa("tasa", $idtasa, "TR.TASA_ANUALIZADA");
	$xFRM->OTasaInt("tasa", $idtasa, "TR.TASA_ANUALIZADA");
	$xFRM->OTasaInt("tasa_mora", $idmora, "TR.TASAMORA");
	
	$xFRM->addHElem($xSel->getListaDePeriocidadDePago("periocidad", $xTabla->periocidad()->v())->get(true) );
	
	
	$xFRM->OText("numerohipoteca", $xTabla->numerohipoteca()->v(), "TR.Datos de Garantia");
	$xFRM->setValidacion("numerohipoteca", "validacion.novacio", "TR.MSG_DATA_REQUIRED", true);
	
	$xFRM->OMoneda2("monto_hipoteca", $xTabla->monto_hipoteca()->v(), "TR.monto de Garantia");
	$xFRM->setValidacion("monto_hipoteca", "validacion.nozero");
	
	//$xFRM->OHidden("idusuario", $xTabla->idusuario()->v(), "TR.idusuario");
	$xFRM->OText("observaciones", $xTabla->observaciones()->v(), "TR.observaciones");
	//$xFRM->OText("fecha_de_cancelacion", , "TR.fecha de cancelacion");
	//$xFRM->OText("razones_de_cancelacion", $xTabla->razones_de_cancelacion()->v(), "TR.razones de cancelacion");
	$xFRM->addGuardar();
}
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
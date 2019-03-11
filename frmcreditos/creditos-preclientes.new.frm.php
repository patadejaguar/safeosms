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
$xHP		= new cHPage("TR.AGREGAR PRECLIENTE", HP_FORM);
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

$topanel		= parametro("topanel", false, MQL_BOOL);


$soloIva		= true;

$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCreditos_preclientes();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmpreclientes", "creditos-preclientes.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();


if($clave<=0){
	$xTabla->idcontrol("NULL");
	$xTabla->idpersona($persona);
	$xTabla->idcredito($credito);
	$xTabla->idestado(SYS_UNO);
	$xTabla->fecha_de_registro(fechasys());
	$xTabla->idoficial(getUsuarioActual());
	if($persona > DEFAULT_SOCIO){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xTabla->nombres($xSoc->getNombre());
			$xTabla->apellido1($xSoc->getApellidoPaterno());
			$xTabla->apellido1($xSoc->getApellidoMaterno());
			$xTabla->email($xSoc->getCorreoElectronico());
			$xTabla->telefono($xSoc->getTelefonoPrincipal());
			$xTabla->rfc($xSoc->getRFC());
			$xTabla->curp($xSoc->getCURP());
		}
	}
}


$xFRM->OHidden("idcontrol", $xTabla->idcontrol()->v());

$xFRM->addSeccion("idpp", "TR.PERSONA");

if($persona> DEFAULT_SOCIO){
	$xFRM->OHidden("nombres", $xTabla->nombres()->v());
	$xFRM->OHidden("apellido1", $xTabla->apellido1()->v());
	$xFRM->OHidden("apellido2", $xTabla->apellido2()->v());
	$xFRM->OHidden("rfc", $xTabla->rfc()->v());
	$xFRM->OHidden("curp", $xTabla->curp()->v());
	
	$xFRM->OHidden("email", $xTabla->email()->v());
	$xFRM->OHidden("telefono", $xTabla->telefono()->v());
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->addHElem($xSoc->getFicha());
	}
} else {
	$xFRM->OText("nombres", $xTabla->nombres()->v(), "TR.NOMBRE_COMPLETO");
	$xFRM->OText_13("apellido1", $xTabla->apellido1()->v(), "TR.PRIMER_APELLIDO");
	$xFRM->OText_13("apellido2", $xTabla->apellido2()->v(), "TR.SEGUNDO_APELLIDO");
	$xFRM->OHidden("curp", "");
	$xFRM->OHidden("rfc", "");
	
	//$xFRM->OText_13("rfc", $xTabla->rfc()->v(), "TR.RFC");
	//$xFRM->OText_13("curp", $xTabla->curp()->v(), "TR.CURP");
	
	$xFRM->OMail("email", $xTabla->email()->v());
	$xFRM->OTelefono("telefono", $xTabla->telefono()->v(), "TR.TELEFONO");
}

$xFRM->endSeccion();
$xFRM->addSeccion("idpp", "TR.CREDITO");

$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("producto", $xTabla->producto()->v(), true)->get(true) );
$xFRM->addHElem( $xSel->getListaDePeriocidadDePago("periocidad", $xTabla->producto()->v(), false, true)->get(true));
$xFRM->addHElem( $xSel->getListaDeDestinosDeCredito("aplicacion", $xTabla->aplicacion()->v(), $soloIva)->get(true));

$xFRM->addHElem( $xSel->getListaDeTipoDePago("tipocuota_id", $xTabla->tipocuota_id()->v(),true )->get(true) );
$xFRM->OTasaInt("tasa_interes", 0, "TR.TASA");
//$xFRM->OMoneda("producto", $xTabla->producto()->v(), "TR.PRODUCTO");
//$xFRM->OMoneda("periocidad", $xTabla->periocidad()->v(), "TR.PERIOCIDAD");
//
//$xFRM->OMoneda("aplicacion", $xTabla->aplicacion()->v(), "TR.APLICACION");


$xFRM->OEntero("pagos", $xTabla->pagos()->v(), "TR.PAGOS",200);
$xFRM->OMoneda2("monto", $xTabla->monto()->v(), "TR.MONTO");
$xFRM->OText("notas", $xTabla->notas()->v(), "TR.NOTAS");





$xFRM->endSeccion();


$xFRM->OHidden("idorigen", $xTabla->idorigen()->v(), "TR.IDORIGEN");
$xFRM->OHidden("idpersona", $xTabla->idpersona()->v());
$xFRM->OHidden("idcredito", $xTabla->idcredito()->v());
$xFRM->OHidden("fecha_de_registro", $xTabla->fecha_de_registro()->v());
$xFRM->OHidden("idestado", $xTabla->idestado()->v());
$xFRM->OHidden("idoficial", $xTabla->idoficial()->v());
$xFRM->OHidden("idexterno", $xTabla->idexterno()->v());


if($topanel == false){
	$xFRM->addCRUD($xTabla->get(), true);
} else {
	$xFRM->addCRUD($xTabla->get(), false, "jsGoToPanel");
}


//$xFRM->OButton("TR.PLAN_DE_PAGOS", "", $xFRM->ic()->PLANDEPAGOS, "cmdgenplan", "whiteblue");
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsGoToPanel(dd){
	var id = entero(dd.id);
	if(id > 0){
		xG.go({url:"../frmcreditos/creditos-preclientes.panel.frm.php?clave=" + id});
	}	
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
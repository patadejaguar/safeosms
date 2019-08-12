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
$tipo			= parametro("tipo", 0, MQL_INT);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);


if($persona<= DEFAULT_SOCIO){
	
	$xHP->goToPageX("../utils/frmbuscarsocio.php?next=add.linea");
	$ready		= false;
	
} else {
	$xHP->init();
	
	$xFRM		= new cHForm("frmcreditoslineas", "frmcreditoslineas.php");
	$xSel		= new cHSelect();
	$xTabla		= new cCreditos_lineas();
	
	$xFRM->setTitle($xHP->getTitle());
	
	if($clave>0){
		$xTabla->setData( $xTabla->query()->initByID($clave));
	} else {
		$xTabla->estado(SYS_UNO);
		$xTabla->sucursal(getSucursal());
		$xTabla->fecha_de_alta(fechasys());
		$xTabla->eacp(EACP_CLAVE);
		$xTabla->numero_socio($persona);
		$xTabla->idusuario(getUsuarioActual());
		$xTabla->sucursal(getSucursal());
		$xTabla->fecha_ultima_operacion(fechasys());
		$xTabla->saldo_disponible($xTabla->monto_linea()->v());
		$xTabla->fecha_de_cancelacion($xF->getFechaMaximaOperativa());
		$xTabla->idcreditos_lineas("NULL");
		
	}
	
	if($action <= SYS_NINGUNO){
		$xFRM->setAction("frmcreditoslineas.php?action=" . MQL_ADD, true);
	} else {
		$xFRM->setAction("frmcreditoslineas.php?action=" . MQL_MOD, true);
	}
	
	if($action == MQL_MOD OR $action == MQL_ADD){
		$xTabla->setData($_REQUEST);
		
		
		if($xTabla->tasa()->v() > 1){ //Es mayor al 100% anual
			$idtasa	= $xTabla->tasa()->v() / 100;
			$xTabla->tasa($idtasa);
		}
		$res		= false;
		if($action == MQL_ADD){
			$res		= $xTabla->query()->insert()->save();
		} else {
			$res		= $xTabla->query()->update()->save($clave);
		}
		$xFRM->setResultado($res);
		
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
	
		$xFRM->OFechaLarga("fecha_de_vencimiento", $xTabla->fecha_de_vencimiento()->v(), "TR.FECHA DE VENCIMIENTO");
		$xFRM->addHElem( $xSel->getListaDeOficiales("oficial_de_credito", SYS_USER_ESTADO_ACTIVO, $xTabla->oficial_de_credito()->v())->get(true) );
		
		$xFRM->addHElem($xSel->getListaDePeriocidadDePago("periocidad", $xTabla->periocidad()->v())->get(true) );
		
		
	
		
		$idtasa		= $xTabla->tasa()->v() * 100;
		$idmora		= $xTabla->tasa_mora()->v() * 100;
		
		//$xFRM->OTasa("tasa", $idtasa, "TR.TASA_ANUALIZADA");
		$xFRM->OTasaInt("tasa", $idtasa, "TR.TASA_ANUALIZADA");
		$xFRM->OTasaInt("tasa_mora", $idmora, "TR.TASAMORA");
		
		$xFRM->OMoneda2("monto_linea", $xTabla->monto_linea()->v(), "TR.MAXVALOR");
		$xFRM->setValidacion("monto_linea", "validacion.nozero");
		
		
		$xFRM->OMoneda2("monto_hipoteca", $xTabla->monto_hipoteca()->v(), "TR.monto de Garantia");
		$xFRM->setValidacion("monto_hipoteca", "validacion.nozero");
		
		$xFRM->OText("numerohipoteca", $xTabla->numerohipoteca()->v(), "TR.Datos de Garantia");
		$xFRM->setValidacion("numerohipoteca", "validacion.novacio", "TR.MSG_DATA_REQUIRED", true);
		
	
	
		$xFRM->OText("observaciones", $xTabla->observaciones()->v(), "TR.observaciones");
	
		$xFRM->addGuardar();
	}
	
	echo $xFRM->get();
	
	//$jxc ->drawJavaScript(false, true);
	$xHP->fin();
}

exit;

?>
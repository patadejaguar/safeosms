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
$xHP		= new cHPage("TR.FLUJO_DE_EFECTIVO", HP_FORM);
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

$xFRM		= new cHForm("frmflujoefectivo", "../frmcreditos/frmcreditosflujoefvo.php");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();



if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
	$xFRM->setAction("../frmcreditos/frmcreditosflujoefvo.php");
	
} else {
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		
		$xFRM->addSeccion("idic", "TR.CREDITO");
		
		$xFRM->OHidden("credito", $credito);
		$xFRM->addHElem($xCred->getFichaMini());
		
		$xFRM->setAction("../frmcreditos/frmcreditosflujoefvo.php?action=" . MQL_ADD);
		
		if($action == MQL_ADD){
			if($monto > 0){
				$idfrecuenciaflujo	= parametro("idfrecuenciaflujo",0, MQL_INT);
				$idorigenflujo		= parametro("idorigenflujo",0, MQL_INT);
				$descripcion		= parametro("descripcion");
				
				$xOrigen			= new cCreditos_origenflujo();
				$xOrigen->setData( $xOrigen->query()->initByID($idorigenflujo) );
				
				$tipo				= $xOrigen->tipo()->v();
				$afectacion			= $xOrigen->afectacion()->v();
				$afectacionneta		= round((($monto / $idfrecuenciaflujo) * $afectacion),2);
				$xFlujo				= new cCreditos_flujoefvo();
				$xFlujo->afectacion_neta($afectacionneta);
				$xFlujo->descripcion_completa($descripcion);
				$xFlujo->fecha_captura($fecha);
				$xFlujo->idcreditos_flujoefvo("NULL");
				$xFlujo->idusuario(getUsuarioActual());
				$xFlujo->monto_flujo($monto);
				$xFlujo->observacion_flujo($observaciones);
				$xFlujo->origen_flujo($idorigenflujo);
				$xFlujo->periocidad_flujo($idfrecuenciaflujo);
				$xFlujo->socio_flujo($xCred->getClaveDePersona());
				$xFlujo->solicitud_flujo($xCred->getClaveDeCredito());
				$xFlujo->sucursal(getSucursal());
				$xFlujo->tipo_flujo($tipo);
				$res 	= $xFlujo->query()->insert()->save();
				$xFRM->setResultado($res);
				
			}
		}
		$xFRM->endSeccion();
		$xFRM->addSeccion("idic2", "TR.AGREGAR");
		//Agregar Formulario
		$xFRM->addGuardar();
		$xFRM->addHElem( $xSel->getListaDeFrecuenciaFlujoEfvo()->get(true) );
		$xFRM->addHElem($xSel->getListaDeOrigenDeFlujoEfvo("", 100)->get(true) );
		$xFRM->OText("descripcion", "", "TR.DESCRIPCION");
		$xFRM->addMonto();
		$xFRM->addObservaciones();
		
		$xFRM->endSeccion();
		$xFRM->addSeccion("idic3", "TR.LISTA");
		//Cargar Tabla
		$xTbl	= new cTabla( $xLi->getListadoDeFlujoEfvoCred($credito) );
		//if($xCred->getEsCreditoYaAfectado() == false){
			$xTbl->addEditar();
			$xTbl->addEliminar();
		//}
		$xTbl->setFootSum(array(6 => "neto") );
		$xFRM->addHElem( $xTbl->Show() );
		if($xTbl->getRowCount()>=1){
			$xFRM->OButton("TR.IMPRIMIR ACUSE", "var xG=new Gen();xG.w({url:'../rptcreditos/rpt_acuse_flujo_efvo.php?credito=$credito'})", $xFRM->ic()->IMPRIMIR);
		}
		
		$xFRM->endSeccion();
		
		
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>
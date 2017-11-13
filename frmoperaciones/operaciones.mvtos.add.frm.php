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
$xHP		= new cHPage("TR.AGREGAR OPERACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
//$xDic		= new cHDicccionarioDeTablas();
$xSel		= new cHSelect();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
//$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
//$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
//$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
//$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones= parametro("idobservaciones");


$tipo		= parametro("idtipo", OPERACION_CLAVE_PAGO_CAPITAL, MQL_INT); $tipo = parametro("tipo", $tipo, MQL_INT);
$periodo	= parametro("idperiodo", 0, MQL_INT); $periodo	= parametro("periodo", $periodo, MQL_INT);$periodo	= parametro("parcialidad", $periodo, MQL_INT);

$echale         = parametro("echale", false, MQL_BOOL);
$cuadre			= parametro("cuadre", false, MQL_BOOL);


$xHP->init();
$xFRM		= new cHForm("frm", "operaciones.mvtos.add.frm.php?action=" . MQL_ADD);
$xOP		= new cOperaciones_mvtos();
$xFRM->setTitle($xHP->getTitle() );
//$xFRM->addJsBasico();
if($action == SYS_NINGUNO){
	$xFRM->OHidden("idrecibo", $recibo);
	$xRec	= new cReciboDeOperacion(false, false, $recibo);
	
	if($xRec->init() == true){	
		$xSelOps		= $xSel->getListaDeTiposDeOperacion("idtipo", $tipo, false, false, true);
		if($echale == true){
			$arrSi		= array(OPERACION_CLAVE_PAGO_MORA => OPERACION_CLAVE_PAGO_MORA, 146 => 146, 156 => 156, 148 => 148);
			
			$xSelOps->show();
			
			$lop		= $xSelOps->getOptions();
			foreach ($lop as $iop => $txtop){
				if(!isset($arrSi[$iop])){
					$xSelOps->setEliminarOption($iop);
				}
			}
		}
		$xFRM->addHElem( $xSelOps->get(true) );
		$xFRM->OMoneda("idperiodo", $xRec->getPeriodo(), "TR.PERIODO");
		$xFRM->addMonto($monto);
		$xFRM->addObservaciones();
		$xFRM->addGuardar();
	} else {
		$xFRM->addCerrar();
	}
	
	
	if($echale == true ){
	    $xCred  = new cCredito($xRec->getCodigoDeDocumento());
	    $xFRM->OHidden("echale", "true");
	    
	    if($xCred->init() == true){
	        $OProducto      = $xCred->getOProductoDeCredito();
	        $OProducto->initOtrosCargos($xCred->getFechaDeMinistracion(), $xCred->getMontoAutorizado());
	        $arr_desglose	= $OProducto->getListaOtrosCargosEnParcsRaw();
	        $base           = $xCred->getMontoAutorizado();
	        $pagos          = $xCred->getPagosAutorizados();
	        $mtotal         = 0;
	        $dd				= $xQL->getDataRecord("SELECT `tipo_operacion`,`afectacion_real` FROM `operaciones_mvtos` WHERE `recibo_afectado`=$recibo");
	        $mivamo			= 0;//Monto Mora
	        $existeMora		= false;
	        
	        foreach($dd as $rw){
	            $idop      = $rw["tipo_operacion"];
	            $xmonto    = $rw["afectacion_real"];
	            $xTipo     = new cTipoDeOperacion($idop);
	            $xTipo->init();
	            if(isset($arr_desglose[$idop])){
	                $nn        = $xTipo->getNombre();
	                $omonto    = round(((($base*$arr_desglose[$idop])/100) / $pagos),2);
	                //$xFRM->addAviso("HAY $nn -- " . getFMoney($xmonto), "addn_$idop", false, "error"); setError($omonto);
	                if(round($xmonto,2) >= round($omonto,2)){
	                	$xFRM->addAviso("ELIMINAR $nn : " . getFMoney($xmonto), "addn_$idop", false, "error");
	                	unset($arr_desglose[$idop]);
	                } else {
	                	$pres						= (($xmonto*$pagos) / $base)*100;
	                	$depa						= $arr_desglose[$idop];
	                	$relog						= ($depa-$pres);
	                	//setLog($relog);
	                	$arr_desglose[$idop]		= $relog;
	                }
	            }
	            if($idop == OPERACION_CLAVE_PAGO_IVA_OTROS AND $existeMora == false){
	                $mivamo        = round(($xmonto/TASA_IVA),2);
	            }
	            if($idop == OPERACION_CLAVE_PAGO_MORA){
	                $mivamo			= 0;//Monto Mora
	                $existeMora		= true;
	                
	            }
	        }
            if($mivamo>0){
            	
                $arr_desglose[OPERACION_CLAVE_PAGO_MORA]    = $mivamo;
            }
	        foreach ($arr_desglose as $idx=> $vv){
	            $xTipo     = new cTipoDeOperacion($idx);
	            $xTipo->init();
	            $nn        = $xTipo->getNombre();
	            if($idx == OPERACION_CLAVE_PAGO_MORA){
	                $mmonto		= round($vv,2);
	            } else {
	               $mmonto    = round((($base*$vv) / $pagos),2);
	               
	            }
	            $xFRM->addAviso("AGREGAR $nn ($vv) : " . getFMoney($mmonto), "adds_$idx");
	            $mtotal       += $mmonto;
	            
	        }
	        $xFRM->addAviso("TOTAL : " . getFMoney($mtotal), "idxto", false, "warning");
	        $xFRM->addAviso("PAGOS : " . $xCred->getPagosAutorizados(), "idxtos", false, "success");
	        $xFRM->addAviso("CREDITO : " . $xCred->getMontoAutorizado(), "idxtons", false, "success");
	        $xFRM->addAviso("FACTOR : " .  getFMoney( ( ( ($monto*$pagos)/$base)* 100) ) . "%", "idxtons", false, "success");
	    }
	} else {
		$xCred  = new cCredito($xRec->getCodigoDeDocumento());
		if($xCred->init() == true){
			$OProducto      = $xCred->getOProductoDeCredito();
			$OProducto->initOtrosCargos($xCred->getFechaDeMinistracion(), $xCred->getMontoAutorizado());
			$arr_desglose	= $OProducto->getListaOtrosCargosEnParcsRaw();
			$base           = $xCred->getMontoAutorizado();
			$pagos          = $xCred->getPagosAutorizados();
			$mtotal         = 0;
			$dd				= $xQL->getDataRecord("SELECT `tipo_operacion`,`afectacion_real` FROM `operaciones_mvtos` WHERE `recibo_afectado`=$recibo");
			$mivamo			= 0;//Monto Mora
			$existeMora		= false;
			
			foreach($dd as $rw){
				$idop      = $rw["tipo_operacion"];
				$xmonto    = $rw["afectacion_real"];
				$xTipo     = new cTipoDeOperacion($idop);
				$xTipo->init();
				if(isset($arr_desglose[$idop])){
					$nn        = $xTipo->getNombre();
					$omonto    = round(((($base*$arr_desglose[$idop])/100) / $pagos),2);
					//$xFRM->addAviso("HAY $nn -- " . getFMoney($xmonto), "addn_$idop", false, "error"); setError($omonto);
					if(round($xmonto,2) >= round($omonto,2)){
						$xFRM->addAviso("ELIMINAR $nn : " . getFMoney($xmonto), "addn_$idop", false, "error");
						unset($arr_desglose[$idop]);
					} else {
						$pres						= (($xmonto*$pagos) / $base)*100;
						$depa						= $arr_desglose[$idop];
						$relog						= ($depa-$pres);
						//setLog($relog);
						$arr_desglose[$idop]		= $relog;
					}
				}
				if($idop == OPERACION_CLAVE_PAGO_IVA_OTROS AND $existeMora == false){
					$mivamo        = round(($xmonto/TASA_IVA),2);
				}
				if($idop == OPERACION_CLAVE_PAGO_MORA){
					$mivamo			= 0;//Monto Mora
					$existeMora		= true;
					
				}
			}
			if($mivamo>0){
				
				$arr_desglose[OPERACION_CLAVE_PAGO_MORA]    = $mivamo;
			}
			foreach ($arr_desglose as $idx=> $vv){
				$xTipo     = new cTipoDeOperacion($idx);
				$xTipo->init();
				$nn        = $xTipo->getNombre();
				if($idx == OPERACION_CLAVE_PAGO_MORA){
					$mmonto		= round($vv,2);
				} else {
					$mmonto    = round((($base*$vv) / $pagos),2);
					
				}
				$xFRM->addAviso("AGREGAR $nn ($vv) : " . getFMoney($mmonto), "adds_$idx");
				$mtotal       += $mmonto;
				
			}
			$xFRM->addAviso("TOTAL : " . getFMoney($mtotal), "idxto", false, "warning");
			$xFRM->addAviso("PAGOS : " . $xCred->getPagosAutorizados(), "idxtos", false, "success");
			$xFRM->addAviso("CREDITO : " . $xCred->getMontoAutorizado(), "idxtons", false, "success");
			$xFRM->addAviso("FACTOR : " .  getFMoney( ( ( ($monto*$pagos)/$base)* 100) ) . "%", "idxtons", false, "success");
		}
	}
} else {
	$xRec	= new cReciboDeOperacion(false, false, $recibo);
	$ready	= false;
	if($xRec->init() == true){
		$id	= $xRec->setNuevoMvto($xRec->getFechaDeRecibo(), $monto, $tipo, $periodo, $observaciones);
		
		if($echale == false){ //Si no es echale
		    $xRec->setForceUpdateSaldos(true);
		    $xRec->setFinalizarRecibo(true);
		}
		
		$ready	= ($id >0) ? true: false;
		$xFRM->addLog($xRec->getMessages());
		$xFRM->addCerrar("", 2);
	} else {
		$xFRM->addAtras();
	}
	$xFRM->setResultado($ready);
}


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
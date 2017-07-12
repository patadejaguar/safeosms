<?php
header('Access-Control-Allow-Origin: *');  //I have also tried the * wildcard and get the same response
//header("Access-Control-Allow-Credentials: true");
//header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
//header('Access-Control-Max-Age: 1000');
//header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
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
$xHP		= new cHPage("Servicio Remoto de Estados de Cuenta", HP_SERVICE);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");
$letra			= parametro("letra", false, MQL_INT);

$cmd			= parametro("cmd", "");
$email			= parametro("idmail", "");$email = parametro("email", "");
$senders		= getEmails($_REQUEST);
$via			= parametro("via", "");
$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "";
switch ($cmd){
	case "CONSULTA":
		//numero de credito
		
		if($credito > DEFAULT_CREDITO){
		$xCred	= new cCredito($credito);
			if($xCred->init() == true){
				$xSoc	= $xCred->getOPersona();
				$mMail	= $xSoc->getCorreoElectronico();
				
				$emailc	= md5(strtoupper($email));
				$emaila	= md5(strtoupper($mMail));
				
				if($emaila == $emailc){
					$xRul	= new cReglaDeNegocio();
					$xRul->addPersonasDestinatarios($xSoc->getClaveDePersona());
					$xRul->setVariables(array("credito" => $credito, "email" => $mMail));
					$xRul->setExecuteActions($xRul->reglas()->RN_CREDITOS_ESTADOCTA_EXEC);
					
					if(MODO_DEBUG == true){
						$rs["message"] .= $xRul->getMessages();
					} else {
						$rs["message"] .= "Su Estado de Cuenta se ha enviado a el Correo Electronico $email";
					}
					$rs["error"]	= false;
				} else {
					$rs["message"] .= "El Correo Electronico no es el mismo que el del Registro";
				}
			}
			if(MODO_DEBUG == true){
				$rs["message"] .= $xCred->getMessages();
			} else {
				$rs["message"] .= "Se requieren datos para esta accion";
			}
		} else {
			$rs["message"] .= "Se requieren datos para esta accion";
		}
		//cuenta
		if($cuenta > 0 AND $cuenta >DEFAULT_CUENTA_CORRIENTE){
			$xCta	= new cCuentaDeCaptacion($cuenta);
			if($xCta->init() == true){
				
			}
		}
		// response
		//sendto
		//
			
		break;
	case "PRECREDITO":
		$apellidos	= parametro("idapellidos");
		$apps		= explode(" ", $apellidos,2);
		$app1		= isset($apps[0]) ? $apps[0] : "";
		$app2		= isset($apps[1]) ? $apps[1] : "";
		$nombre		= parametro("idnombre");
		$telefono	= parametro("idtelefono"); $telefono = parametro("telefono", $telefono);
		$email		= parametro("idmail"); $email = parametro("idemail", $email); $email = parametro("email", $email);
		
		$condiciones= parametro("idcondiciones");
		$dcond		= explode("-", $condiciones);
		
		$frecuencia	= (isset($dcond[0])) ? setNoMenorQueCero($dcond[0]) : CREDITO_TIPO_PERIOCIDAD_MENSUAL;
		$pagos		= (isset($dcond[1])) ? setNoMenorQueCero($dcond[1]) : 12;
		
		$monto		= parametro("idmonto", 0, MQL_FLOAT);
		
		if($apellidos !== "" AND $nombre !== "" AND $monto >0){
			$xPr	= new cCreditosPreclientes();
			$res	= $xPr->add($app1, $app2, $nombre, $telefono, $email, $pagos, $frecuencia, $monto);
			$rs["message"] .= $xPr->getMessages();
		} else {
			$rs["message"] .= "Faltan algunos Datos para el Registro";
		}
		
		break;
	case "COTIZACION":
		$condiciones	= parametro("idcondiciones");
		$dcond			= explode("-", $condiciones);
		
		$frecuencia		= (isset($dcond[0])) ? setNoMenorQueCero($dcond[0]) : CREDITO_TIPO_PERIOCIDAD_MENSUAL;
		$pagos			= (isset($dcond[1])) ? setNoMenorQueCero($dcond[1]) : 12;
		
		$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
		$frecuencia 	= parametro("periocidad", $frecuencia, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
		$pagos			= parametro("pagos", $pagos, MQL_INT);
		$redondeo		= parametro("redondeo", true, MQL_BOOL);
		$siniva			= parametro("siniva", false, MQL_BOOL);
		$tasa			= parametro("tasa", 0.6, MQL_FLOAT);
		$anticipo		= parametro("anticipo", 0, MQL_FLOAT);
		$residual		= parametro("residual", 0, MQL_FLOAT);
		
		$tasaiva		= parametro("iva", TASA_IVA, MQL_FLOAT);
		$tasaiva		= ($siniva == true) ? 0 : $tasaiva;
		
		$tasa			= ($tasa/100);
		$soloint		= parametro("solointeres", false, MQL_BOOL);
		
		$redondeo		= ($redondeo == true) ? 100 : 0;
		
		$rs				= array();
		$rs["error"]	= true;
		$rs["message"]	= "Sin datos validos";
		$xGen			= new cPlanDePagosGenerador($frecuencia);
		$xGen->setPagosAutorizados($pagos);
		$xGen->setMontoActual($monto);
		$xGen->setMontoAutorizado($monto);
		$xGen->setPeriocidadDePago($frecuencia);
		$xGen->setTasaDeInteres($tasa);
		$xGen->setTasaDeIVA($tasaiva);
		$xGen->setFechaDesembolso(fechasys());
		
		
		if($soloint == true){
			$xGen->setTipoDePago(CREDITO_TIPO_PAGO_INTERES_COMERCIAL);
		} else {
			$xGen->setTipoDePago(CREDITO_TIPO_PAGO_PERIODICO);
		}
		
		$xGen->setSoloTest(true);
		$xGen->setAnticipo($anticipo);
		$xGen->setValorResidual($residual);
		$parcial 	= $xGen->getParcialidadPresumida($redondeo); //$redondeo, $idotros, $montootros, $primer_pago);
		$xGen->setCompilar(false);
		//$xGen->initPorCredito($idcredito);
		$cnnt		= base64_encode( $xGen->getVersionFinal() );
		
		$vence		= $xGen->getFechaDeUltimoPago();
		
		
		$rs["error"]	= false;
		$rs["message"]	= $xGen->getMessages();
		$rs[SYS_MONTO]	= $parcial;
		$rs[SYS_FECHA_VENCIMIENTO]	= $vence;
		$rs["html"]		= $cnnt;
		break;
	default:
		$rs["message"] .= "No se ha definido una opcion";
		break;
}
header('Content-type: application/json');
echo json_encode($rs);
?>
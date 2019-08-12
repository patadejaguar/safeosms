<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
//=====>	INICIO_H
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");
$theFile					= __FILE__;
$permiso					= getSIPAKALPermissions($theFile);
if($permiso === false){		header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
$xInit->cors();
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$xValid		= new cReglasDeValidacion();
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$letra		= parametro("letra", false, MQL_INT); $letra = parametro("periodo", $letra, MQL_INT); $letra = parametro("parcialidad", $letra, MQL_INT);


$rs			= array();

$xParc		= new cParcialidadDeCredito();

if($xParc->init(false, $credito, $letra) == true){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		
		$xPersona	= $xCred->getOPersona();
		$xProd		= $xCred->getOProductoDeCredito();
		
		//'metadata'    => array('test' => 'extra info'),
		$precioUnitario	= round((setNoMenorQueCero($xCred->getTotalPlanPendiente()) * 100),0);
		$arrPagos	= array();
		
		$infoCliente	= array(
				'name'  => $xPersona->getNombreCompleto(),
				'phone' => $xPersona->getTelefonoPrincipal()
		);
		//Necesita correo electronico
		if($xValid->email($xPersona->getCorreoElectronico())){
			$infoCliente["email"] = $xPersona->getCorreoElectronico();
		} else {
			$infoCliente["email"] = EACP_MAIL;
		}
		
		
		$montoPago	= round((setNoMenorQueCero($xParc->getTotal()) * 100),0);
		$expiraPago	= strtotime($xParc->getFechaDeVencimiento()) + 300;
		$expiraPago	= strtotime(date("Y-m-d H:i:s")) + 43200;
		
		if($montoPago>0){
			$arrPagos[]		= array(
					'payment_method' => array(
							'type'       => 'oxxo_cash',
							'expires_at' => $expiraPago
					),
					'amount' => $montoPago
			);
		}
		
		$orden =
		array(
				'line_items'=> array(
						array(
								'name'        => $xProd->getNombre(),
								'description' => $xCred->getDescripcion(),
								'unit_price'  => $montoPago,
								'quantity'    => 1,
								'sku'         => "$credito"
						)
				),
				'charges'     => $arrPagos,
				'currency'      => 'mxn',
				'customer_info' => $infoCliente
		);
		
		
		$xConk			= new cConekta();
		
		
		
		var_dump($xConk->sendOrder($orden));
		
		//var_dump($xConk->getMessages());
		
	}

	
}

exit;

header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);


exit;

// Solo puedes enviar una orden de pago

//=====================================================================================================
//=====>	INICIO_H
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");
$theFile					= __FILE__;
$permiso					= getSIPAKALPermissions($theFile);
if($permiso === false){		header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xInit      = new cHPage("Devuelve el Numero de Plan de Pagos", HP_SERVICE );
$xInit->cors();
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$xValid		= new cReglasDeValidacion();
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

$rs				= array();
$rs[SYS_ERROR]	= true;
$rs[SYS_MSG]	= "";
if($credito > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$xPlan	= new cPlanDePagos();
		$xPlan->initByCredito($credito);
		
		$pagos		= $xPlan->initParcsPendientes();
		
		$xPersona	= $xCred->getOPersona();
		$xProd		= $xCred->getOProductoDeCredito();
		//'metadata'    => array('test' => 'extra info'),
		$precioUnitario	= round((setNoMenorQueCero($xCred->getTotalPlanPendiente()) * 100),0);
		$iterPago	= 300;
		$arrPagos	= array();
		
		$infoCliente	= array(
				'name'  => $xPersona->getNombreCompleto(),
				'phone' => $xPersona->getTelefonoPrincipal() 
		);
		//Necesita correo electronico
		if($xValid->email($xPersona->getCorreoElectronico())){
			$infoCliente["email"] = $xPersona->getCorreoElectronico();
		} else {
			$infoCliente["email"] = EACP_MAIL;
		}
		
		
		foreach ($pagos as $indice => $datos){
			/*						array(
			 'payment_method' => array(
			 'type'       => 'oxxo_cash',
			 'expires_at' => strtotime(date("Y-m-d H:i:s")) + "36000"
			 ),
			 'amount' => 20000
			 )*/
			$montoPago	= round((setNoMenorQueCero($datos["neto"]) * 100),0);
			$expiraPago	= strtotime($datos[SYS_FECHA]) + ($indice * $iterPago);
			
			if($datos["neto"]>0){
				$arrPagos[]		= array(
						'payment_method' => array(
								'type'       => 'oxxo_cash',
								'expires_at' => $expiraPago
						),
						'amount' => $montoPago
				);
			}
		}
		
		$orden =
		array(
				'line_items'=> array(
						array(
								'name'        => $xProd->getNombre(),
								'description' => $xCred->getDescripcion(),
								'unit_price'  => $precioUnitario,
								'quantity'    => 1,
								'sku'         => "$credito"
						)
				),
				'currency'    => 'mxn',
				
				'charges'     => $arrPagos,
				'currency'      => 'mxn',
				'customer_info' => $infoCliente
		);
		var_dump($arrPagos);
		var_dump($orden);
		$xConk			= new cConekta();
		
		var_dump($xConk->sendOrder($orden));
		
		var_dump($xConk->getMessages());
		
	}
	$rs[SYS_MSG]	= $xCred->getMessages();
}
header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);
?>
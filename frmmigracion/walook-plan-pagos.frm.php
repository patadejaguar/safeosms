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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xVals		= new cReglasDeValidacion();
$xVis		= new cSQLVistas();
$xF			= new cFecha();

ini_set("max_execution_time", 1900);

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
$clean			= parametro("clean", false, MQL_BOOL);


$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$config = array(
		// required credentials
		
		'host'       => 'localhost',
		'user'       => 'gpd1601',
		'password'   => 'Gx450Ppadio',
		'database'   => 'gpd1601',
		
		// optional
		
		'fetchMode'  => \PDO::FETCH_ASSOC,
		'charset'    => 'utf8',
		'port'       => 5432,
		'unixSocket' => null,
);

// standard setupPostgres
$dbConn = new \Simplon\Postgres\Postgres(
		$config['host'],
		$config['user'],
		$config['password'],
		$config['database']
		);

$pgSqlManager = new \Simplon\Postgres\Manager\PgSqlManager($dbConn);
//=================================== Clean
if($clean == true){
	$result = $dbConn->executeSql('DELETE FROM detalle_plan_pago');
}

//===================================
$rs		= $xQL->getDataRecord("SELECT * FROM creditos_solicitud WHERE `numero_socio` != 1 ORDER BY `fecha_solicitud` DESC");//LIMIT 0,1000
$xT		= new cCreditos_solicitud();
$xTv	= new cLetrasVista();

$sqlPlanDePago	= 'SELECT "public"."credito_ministrado"."plan_pago_id" FROM "credito_ministrado" INNER JOIN "credito"  ON "credito_ministrado"."solicitud_origen_id" = "credito"."id" WHERE ( "public"."credito"."id" = :idcredito )';


foreach ($rs as $rw){
	$xT->setData($rw);
	$idcredito							= $rw[$xT->NUMERO_SOLICITUD];
	$idpersona							= $rw[$xT->NUMERO_SOCIO];
	
	$xCred								= new cCredito($idcredito); $xCred->init();
	$idplanactual						= $xCred->getNumeroDePlanDePagos();
	
	if($idplanactual>0){
	
		$rsLetras						= $xQL->getDataRecord($xVis->getVistaLetras($idcredito));
		$sqlBuilder 					= new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
		$sqlBuilder->setQuery($sqlPlanDePago)->setConditions(array('idcredito' => $idcredito));
		
		$idplandepago					= $pgSqlManager->fetchColumn($sqlBuilder);
		if($idplandepago>0){
			foreach ($rsLetras as $rwL){
				$xTv->setData($rwL);
				$interesMora									= $rwL[$xTv->INTERES_MORATORIO];
				$ivaMora										= round(($interesMora * TASA_IVA),2);
				$interes										= $rwL[$xTv->INTERES];
				$capital										= $rwL[$xTv->CAPITAL];
				$iva											= $rwL[$xTv->IVA];
				$fechaVence										= $rwL[$xTv->FECHA_DE_VENCIMIENTO];
				$idparcialidad									= $rwL[$xTv->PARCIALIDAD];
				$esVigente										= ( $xF->getInt($fechaVence) >= $xF->getInt(fechasys()) ) ? true : false;
				
				$arrDP											= array();
				$arrDP["fecha_vencimiento_pago"] 				= $fechaVence;
				$arrDP["monto_pago"] 							= $rwL[$xTv->TOTAL_SIN_OTROS];
				$arrDP["monto_pagado"] 							= 0;
				$arrDP["fecha_realizacion_pago"] 				= null;
				$arrDP["interes_pago"] 							= $interes;
				$arrDP["interes_devengado"] 					= 0;//$rwL[$xTv->INTERES_EXIGIBLE];
				$arrDP["amortizacion"] 							= $capital;
				$arrDP["por_pagar"] 							= 0;//A Pagar despues de la letra.- con intereses
				$arrDP["iva"] 									= $iva;//$rwL[$xTv->]

				$arrDP["numero_pago"] 							= $idparcialidad;
				$arrDP["plan_pago_id"] 							= $idplandepago;
				$arrDP["estado_pago"] 							= "NO_REALIZADO";
				$arrDP["saldo"] 								= $rwL[$xTv->TOTAL_SIN_OTROS];
				$arrDP["interes_moratorio"] 					= $interesMora;
				$arrDP["iva_interes_moratorio"] 				= $ivaMora;
				
				//========================== Obtener saldo del plan de pago
				$capitalamort									= $xQL->getDataValue("SELECT SUM(`capital`) AS `abonos` FROM `creditos_plan_de_pagos` WHERE `clave_de_credito`=$idcredito AND `numero_de_parcialidad`<=$idparcialidad", "abonos");
				$capitalvivo									= $xCred->getMontoAutorizado() - $capitalamort + $capital;
				
				$arrDP["capital_vivo"] 							= 0;//A Pagar Despues de la Letra.- Solo Capital;
				$arrDP["capital_amortizado"] 					= $capitalamort;//Total pagado a la fecha
				
				$arrDP["saldo_iva_interes_moratorio"] 			= $ivaMora;
				$arrDP["saldo_interes_moratorio"] 				= $interesMora;
				
				$arrDP["saldo_iva"] 							= $iva;
				$arrDP["saldo_interes"] 						= $interes;
				$arrDP["saldo_capital"] 						= $capital;
				
				$arrDP["es_vigente"] 							= $esVigente;
				
				$arrDP["porcentaje_recargo_credito_americano"] 	= 0;
				$arrDP["iva_interes_devengado"] 				= 0;
				
				$arrDP["otros_cargos"] 							= 0;
				
				$arrDP["saldo_otros_cargos"] 					= 0;
				$arrDP["iva_otros_cargos"] 						= 0;
				
				$arrDP["saldo_iva_otros_cargos"] 				= 0;
				$arrDP["saldo_iva_interes_devengado"] 			= 0;
				$arrDP["saldo_interes_devengado"] 				= 0;
			
				try {
					$identrega 				= $dbConn->insert('detalle_plan_pago', $arrDP);
				} catch (\Simplon\Postgres\PostgresException $e){
					$xFRM->addAviso($e->getMessage());
					setLog($arrDP);
				}
				
			}
		}
		$rsLetras											= null;
	
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
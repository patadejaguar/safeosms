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

ini_set("max_execution_time", 1800);



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

$xLog		= new cCoreLog();
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
	
	$result = $dbConn->executeSql('UPDATE public.credito SET plan_pago_id=null, recibo_credito_id=null, credito_ministrado_id=null');
	$result = $dbConn->executeSql('DELETE FROM desembolso');
	$result = $dbConn->executeSql('DELETE FROM orden_desembolso');
	
	$result = $dbConn->executeSql('DELETE FROM credito_ministrado');
	$result = $dbConn->executeSql('DELETE FROM credito');
	$result = $dbConn->executeSql('DELETE FROM plan_pago');
	$result = $dbConn->executeSql('DELETE FROM detalle_ficha');
	$result = $dbConn->executeSql('DELETE FROM ficha');
	$result = $dbConn->executeSql('DELETE FROM recibo_credito');
	$xQL->setRawQuery("CALL `proc_creditos_abonos_parciales`()");
	$xQL->setRawQuery("TRUNCATE general_log");
	getEnCierre(true);
}

//exit;

function migFecha($f){
	$xF	= new cFecha();
	//2018-03-17 13:35:51.901
	$d	= date("Y-m-d H:m:s.B", $xF->getInt($f));
	
	return $d;
}

function migPdtoCredito($idp){
	$id		= 0;
    $xQL	= new MQL();
    $id		= $xQL->getDataValue("SELECT `nuevo` FROM `mig_producto_cred` WHERE `original`=$idp LIMIT 0,1", "nuevo");
    $xQL	= null;
    if($id <=0){
    	$id = 2;//fallback
    }
    return $id;
}

class cVarMig {
	public $OP_MINISTRACION = 1;
	public $OP_PAGO 		= 2;
	
}

function migNuevoFichaPG2($id, $fecha, $monto, $nota = "", $EsPago = false){
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
	
	$xF									= new cFecha();
	$ejercicio							= $xF->anno($fecha);
	$fechaCreado						= migFecha($fecha);
	$arrFC								= array();
	$arrFC["id"] 						= $id;
	$arrFC["ejercicio"] 				= $ejercicio;
	$arrFC["creada_en"] 				= $fechaCreado;
	$arrFC["es_ficha_cancelada"] 		= false;
	$arrFC["cambio"] 					= 0;
	$arrFC["sucursal_id"] 				= 1;
	$arrFC["creada_por_id"] 			= 1;
	$arrFC["cancelada_por_id"]		 	= null;
	
	$runOp								= false;
	try {
		$idficha2 						= $dbConn->insert('ficha', $arrFC);
		
		
		//Insertar Operacion de Ministracion
		$runOp							= true;
		
		
	} catch (\Simplon\Postgres\PostgresException $e){
		//$xFRM->addAviso($e->getMessage());
		//setLog($arrFC);
		setLog($e->getMessage());
		//$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA del Credito\r\n");
		$runOp						= false;
	}
	if($runOp == true){
		$cuentaContable					= 1;// null;//148; //nothing
		$cargo							= null;
		$abono							= null;
		$iva							= null;
		$tipoServicio					= ($EsPago == true) ? 2 : 1;
		if($EsPago == true){
			$cargo						= $monto;
			$iva						= 0;
			
		} else {
			$abono						= $monto;
		}
		$arrFD							= array();
		//$arrFD["id"] 			= $id;
		
		$arrFD["ejercicio"] 			= $ejercicio;
		$arrFD["cargo"]					= $cargo;
		$arrFD["abono"] 				= $abono;
		$arrFD["referencia"] 			= null;
		$arrFD["concepto"] 				= $nota;
		$arrFD["importe_iva"] 			= $iva;
		$arrFD["creada_en"] 			= $fechaCreado;
		$arrFD["ficha_id"] 				= $id;
		$arrFD["sucursal_id"] 			= 1;
		$arrFD["tipo_transaccion_id"] 	= null;
		$arrFD["servicio_id"] 			= $tipoServicio;
		$arrFD["cuenta_contable_id"] 	= $cuentaContable;
		
		
		$arrFD2							= $arrFD;
		
		$arrFD2["cargo"]				= $abono;
		$arrFD2["abono"] 				= $cargo;
		
		try {
			$dficha						= $dbConn->insert('detalle_ficha', $arrFD);
			$dficha2					= $dbConn->insert('detalle_ficha', $arrFD2);
			$runOp						= true;
		} catch (\Simplon\Postgres\PostgresException $e){
			setLog($e->getMessage());
			//$xFRM->addAviso($e->getMessage());
			setLog($arrFD);
			//$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA del Credito\r\n");
			$runOp						= false;
		}
		
		
		
	}
	return $runOp;
	
}



function migNuevoFichaPG($id, $fecha, $monto, $nota = "", $EsPago = false){
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
	
	$xF									= new cFecha();
	$ejercicio							= $xF->anno($fecha);
	$fechaCreado						= migFecha($fecha);
	$arrFC								= array();
	$arrFC["id"] 						= $id;
	$arrFC["ejercicio"] 				= $ejercicio;
	$arrFC["creada_en"] 				= $fechaCreado;
	$arrFC["es_ficha_cancelada"] 		= false;
	$arrFC["cambio"] 					= 0;
	$arrFC["sucursal_id"] 				= 1;
	$arrFC["creada_por_id"] 			= 1;
	$arrFC["cancelada_por_id"]		 	= null;
	
	$runOp								= false;		
	try {
		$idficha2 						= $dbConn->insert('ficha', $arrFC);
		
		
		//Insertar Operacion de Ministracion
		$runOp							= true;
		
		
	} catch (\Simplon\Postgres\PostgresException $e){
		//$xFRM->addAviso($e->getMessage());
		//setLog($arrFC);
		setLog($e->getMessage());
		//$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA del Credito\r\n");
		$runOp						= false;
	}
	if($runOp == true){
		$cuentaContable					= 1;// null;//148; //nothing
		$cargo							= null;
		$abono							= null;
		$iva							= null;
		$tipoServicio					= ($EsPago == true) ? 2 : 1;
		if($EsPago == true){
			$cargo						= $monto;
			$iva						= 0;
			
		} else {
			$abono						= $monto;
		}
		$arrFD							= array();
		//$arrFD["id"] 			= $id;
		
		$arrFD["ejercicio"] 			= $ejercicio;
		$arrFD["cargo"]					= $cargo;
		$arrFD["abono"] 				= $abono;
		$arrFD["referencia"] 			= null;
		$arrFD["concepto"] 				= $nota;
		$arrFD["importe_iva"] 			= $iva;
		$arrFD["creada_en"] 			= $fechaCreado;
		$arrFD["ficha_id"] 				= $id;
		$arrFD["sucursal_id"] 			= 1;
		$arrFD["tipo_transaccion_id"] 	= null;
		$arrFD["servicio_id"] 			= $tipoServicio;
		$arrFD["cuenta_contable_id"] 	= $cuentaContable;
		
		try {
			$dficha						= $dbConn->insert('detalle_ficha', $arrFD);
			$runOp						= true;
		} catch (\Simplon\Postgres\PostgresException $e){
			setLog($e->getMessage());
			//$xFRM->addAviso($e->getMessage());
			setLog($arrFD);
			//$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA del Credito\r\n");
			$runOp						= false;
		}
		
		
		
	}
	return $runOp;
	
}
//$xPDB		= 

$arrCred	= array("monto" => null,
		"created_by" => "Administrador",
		"created_date" => null,
		"last_modified_by" => null,
		"las_modified_date" => null,
		"beneficiario_id" => null,
		"producto_credito_id" => null,
		"frecuencia_pago" => null,
		"tasa_interes" => null,
		"cantidad_pagos" => null,
		"garantia_tipo_cobro" => null,
		"garantia_monto_porcentaje" => null,
		"aplica_iva" => null,
		"porcentaje_iva" => null,
		"tipo_integracion" => null,
		"tipo_credito_id" => null,
		"cuenta_desembolso_id" => null,
		"tipo_interes_id" => null,
		"plazo" => null,
		"etapa_id" => null,
		"empleado_empresa_id" => null,
		"estado_credito_id" => 1,
		"nombre_motivo_rechazo" => null,
		"descripcion_motivo_rechazo" => null,
		"fecha_desembolso" => null,
		"monto_desembolsable" => null,
		"egreso_mensual" => null,
		"ingreso_mensual" => null,
		"identificador" => null,
		"fecha_expiracion" => null,
		"fecha_inicio_plan_pagos" => null,
		"sucursal_id" => null,
		"es_disposicion" => null,
		"tasa_interes_moratorio" => null,
		"destino_credito_id" => null,
		"plan_pago_id" => null,
		"recibo_credito_id" => null,
		"cat_calculado" => null,
		"cuenta_bancaria_id" => null,
		"credito_ministrado_id" => null,
		"credito_ministrado_identificador" => null,
		"credito_ministrado_saldo" => null,
		"origen_solicitud" => null,
		"tipo_cobro_id" => null,
		"vencido" => null,
		"referencia_pago" =>0);

$arrMin	= array("monto_solicitado"=> null,
		"monto_autorizado"=> null,
		"plazo_solicitado"=> null,
		"fecha_desembolso"=> null,
		"saldo_capital"=> null,
		"saldo_interes_normal"=> null,
		"saldo_interes_moratorio"=> null,
		"tasa_normal"=> null,
		"tasa_moratoria"=> null,
		"creado_por_id"=> null,
		"tipo_credito_id"=> null,
		"solicitud_origen_id"=> null,
		"beneficiario_id"=> null,
		"destino_credito_id"=> null,
		"saldo"=> null,
		"empleado_empresa_id"=> null,
		"numero_cheque"=> null,
		"fecha_primer_pago"=> null,
		"identificador"=> null,
		"monto_cotizado"=> null,
		"plan_pago_id"=> null,
		"estado_cartera_id"=> null,
		"frecuencia_pago"=> null,
		"cat_calculado"=> null,
		"cuenta_bancaria_id"=> null,
		"desvinculado"=> null,
		"estado_pago"=> null,
		"fecha_desvinculacion"=> null,
		"fecha_liquidacion"=> null,
		"numero_reestructura"=> null,
		"porcentaje_recargo_credito_americano"=> null,
		"producto_credito_id"=> null,
		"aplica_iva"=> null,
		"porcentaje_iva"=> null,
		"tipo_interes_id"=> null,
		"origen_solicitud_credito"=> null,
		"tipo_cobro_id"=> null,
		"url_pagare"=> null,
		"en_renovacion"=> null,
		"dias_atraso"=> null,
		"observaciones_transicion_cartera_judicial"=> null,
		"fecha_transicion_cartera_judicial"=> null,
		"fecha_transicion_cartera"=> null,
		"beneficiario_anterior_id"=> null,
		"observaciones_sustitucion_beneficiario"=> null,
		"fecha_sustitucion_beneficiario"=> null,
		"referencia_pago"=> null,
		"fecha_reestructuracion"=> null,
		"observaciones_reestructuracion"=> null,
		"tipo_registro"=> null,
		"creado_en_sucursal_id"=> null,
		"creado_en_fecha"=> null,
		"es_bloqueado" => null
);


$rs		= $xQL->getDataRecord("SELECT * FROM creditos_solicitud WHERE `numero_socio` != 1 ORDER BY `fecha_solicitud` DESC LIMIT 0,500");//LIMIT 0,1000
$xT		= new cCreditos_solicitud();


$sqlEmpleado	= 'SELECT "public"."empleado_empresa"."id", "public"."actividad_economica"."persona_id" FROM "empleado_empresa" INNER JOIN "actividad_economica"  ON "empleado_empresa"."actividad_economica_id" = "actividad_economica"."id" WHERE    ( "public"."actividad_economica"."persona_id" = :idpersona )';
$sqlProducto	= 'SELECT "public"."producto_credito"."tipo_credito_id" FROM "public"."producto_credito" WHERE    ( "public"."producto_credito"."id" = :idproducto )';



//$sqlBuilder3 					= new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
//$sqlBuilder3->setQuery('SELECT MAX( "public"."plan_pago"."id" )  AS "nuevo" FROM "public"."plan_pago"');
//$sqlBuilder3->setQuery("SELECT currval('plan_pago_id_seq')");
//$sqlBuilder3->setQuery("SELECT last_value FROM plan_pago_id_seq");
// SELECT currval('persons_id_seq')
//$nuevoIDPlanPago				= $pgSqlManager->fetchColumn($sqlBuilder3);
$nuevoIDPlanPago				= 1;//setNoMenorQueCero($nuevoIDPlanPago);

//if($nuevoIDPlanPago<=0){ $nuevoIDPlanPago=1; }

$nuevoCredMin		= 1;
$nuevaOrdenDes		= 1;
$nuevaFicha			= 1;
$nuevoRecCred		= 1;
/*


$sqlBuilder = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();

$sqlBuilder
    ->setQuery('SELECT id FROM names WHERE name = :name')
    ->setConditions(array('name' => 'Peter'));

$result = $pgSqlManager->fetchColumn($sqlBuilder);

// result
var_dump($result); // '1' || false
 */
foreach ($rs as $rw){
	$xT->setData($rw);
	
	$d									= $rw;
	$arr								= $arrCred;
	$idcredito							= $d[$xT->NUMERO_SOLICITUD];
	$idpersona							= $d[$xT->NUMERO_SOCIO];
	$xCred								= new cCredito($idcredito); $xCred->init();
	$xSoc								= new cSocio($idpersona); $xSoc->init();
	
	$xFreqPago							= new cPeriocidadDePago($d[$xT->PERIOCIDAD_DE_PAGO]); $xFreqPago->init();
	//Obtener el ID de Empleado en psql
	
	$sqlBuilder 						= new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
	$sqlBuilder->setQuery($sqlEmpleado)->setConditions(array('idpersona' => $idpersona));
	
	$idempleado							= $pgSqlManager->fetchColumn($sqlBuilder);
	//FetchRow
	$idempleado							= setNoMenorQueCero($idempleado);
	$productoeCredito					= migPdtoCredito($d[$xT->TIPO_CONVENIO]);
	$fechaDesembolso					= migFecha($d[$xT->FECHA_MINISTRACION]);
	
	$sqlBuilder2 					= new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
	$sqlBuilder2->setQuery($sqlProducto)->setConditions(array('idproducto' => $productoeCredito));
	$tipoDeCredito						= $pgSqlManager->fetchColumn($sqlBuilder2);
	$tipoDeCredito						= setNoMenorQueCero($tipoDeCredito);
	if($tipoDeCredito<=0){
		$tipoDeCredito					= 1;//
		$xLog->add("ERROR\t$idcredito\tTipo de Credito a fallback\r\n");
		
	}
	//var_dump($productoeCredito);	exit();
	
	//
	$destinoDeCredito					= 10;//TODO:Generar Equivalencias
	$numeroDeCheque						= null;
	$frecuenciaDePago					= ucfirst( strtolower($xFreqPago->getNombre()));
	$sucursal							= 1;
	$cuentaBancaria						= 1;
	$fechaLiquidacion					= null;
	
	$arr["id"]							= $idcredito;
	$arr["monto"] 						= $d[$xT->MONTO_AUTORIZADO];
	$arr["created_by"] 					= "Administrador";// migFecha($dd[$xT->FECHA_SOLICITUD]);
	$arr["created_date"] 				= migFecha($d[$xT->FECHA_SOLICITUD]);
	$arr["last_modified_by"] 			= "Administrador";
	$arr["las_modified_date"] 			= migFecha($d[$xT->FECHA_ULTIMO_MVTO]);
	$arr["beneficiario_id"] 			= $d[$xT->NUMERO_SOCIO];
	$arr["producto_credito_id"]			= $productoeCredito;//No es lo mismo producto que credito
	$arr["frecuencia_pago"] 			= $frecuenciaDePago;
	$arr["tasa_interes"] 				= $d[$xT->TASA_INTERES] * 100;
	$arr["cantidad_pagos"] 				= $d[$xT->NUMERO_PAGOS];
	//---------------------------------- Corrige final de Plazo
	if($xCred->isAFinalDePlazo() == true){
		$arr["frecuencia_pago"] 		= "Mensual";
		$arr["cantidad_pagos"] 			= $xCred->getPlazoEnMeses();
	}
	
	$arr["garantia_tipo_cobro"] 		= "monto"; // monto porcentaje
	$arr["garantia_monto_porcentaje"] 	= $xCred->getMontoAutorizado();
	$arr["aplica_iva"] 					= ($xCred->getTasaIVA()<=0) ? "false" : "true";
	$arr["porcentaje_iva"] 				= $xCred->getTasaIVA() * 100;
	$arr["tipo_integracion"] 			= "individual";
	$arr["tipo_credito_id"] 			= $tipoDeCredito;
	
	
	$idcuentbanc						= $xRecMin->getClaveCuentaBancaria();
	$xCtaBanc	= new cCuentaBancaria($idcuentbanc); $xCtaBanc->init();
	
	
	$arr["cuenta_desembolso_id"] 		= $idcuentbanc;
	
	$arr["tipo_interes_id"] 			= ($xCred->getPagosSinCapital() == true ) ? "1" : "2";
	$arr["plazo"] 						= $xCred->getPagosAutorizados();
	$arr["etapa_id"] 					= ($xCred->getEsAfectable() == true) ? "3" : "2";
	$arr["nombre_motivo_rechazo"] 		= "";
	$arr["descripcion_motivo_rechazo"] 	= "";
	
	if($idempleado>0){
		$arr["empleado_empresa_id"] 	= $idempleado;
	} else {
		$arr["empleado_empresa_id"] 	= null;
	}
	if($xCred->getEsAfectable() == true){
		$arr["estado_credito_id"] 			= 5;//Desembolsado
		$xRecMin	= new cReciboDeOperacion(false, false, $xCred->getNumeroReciboDeMinistracion());
		if($xRecMin->init() == true){
			$numeroDeCheque			= $xRecMin->getNumeroDeCheque();
		}
	} else {
		if($xCred->getEsRechazado() == true){
			$arr["estado_credito_id"] 		= 3;//Desembolsado
			$arr["descripcion_motivo_rechazo"] 	= $xCred->getNotaDeRechazo(); 
		} else {
			if($xCred->getEstadoActual() == CREDITO_ESTADO_SOLICITADO){
				$arr["estado_credito_id"] 	= 1;//Desembolsado
			} else if ($xCred->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO){
				$arr["estado_credito_id"] 	= 4;//Desembolsado
			}
		}
	}
	$ingresos							= $xSoc->getIngresosMensuales();
	$arr["egreso_mensual"] 				= setNoMenorQueCero( ($ingresos * 0.5) );
	$arr["ingreso_mensual"] 			= $ingresos;
	$arr["tasa_interes_moratorio"] 		= $xCred->getTasaDeMora() * 100;
	$arr["sucursal_id"] 				= $sucursal;
	$arr["es_disposicion"] 				= false;
	$arr["cat_calculado"] 				= $xCred->getCAT();
	$arr["destino_credito_id"] 			= $destinoDeCredito;
	$runMin								= false;
	if($xCred->getEsAfectable() == true){
		//Insertar 
		$runMin							= true;
	
	}
	$arr["origen_solicitud"] 			= "NUEVA"; //RENOVACION
	$arr["tipo_cobro_id"] 				= 1;//Cheque Cambiado
	$arr["vencido"] 					= ($xCred->getEsVencido() == true) ? true : false;
	$arr["referencia_pago"] 			= "";
	
	//====================================== Insertar Credito
	try {
		$idcredito_inn 					= $dbConn->insert('credito', $arr);
	} catch (\Simplon\Postgres\PostgresException $e){
		$xFRM->addAviso($e->getMessage());
		setLog($arr);
		$runMin							= false;
		$xLog->add("ERROR\t$idcredito\tNo se registro el Credito\r\n");
	}
	
	if($runMin == true){
		//================================ Nuevos
		

		
		
		
		$idplandepago					= $nuevoIDPlanPago+1;
		$idministrado					= $nuevoCredMin+1;
		$idrecibocredito				= $nuevoRecCred + 1;
		$iddesembolso					= 0;
		$idordendesembolso				= $nuevaOrdenDes+1;
		$identrega						= 0;
		$idficha						= $nuevaFicha+1;
		
		//Ejecutar Plan de Pago.
		$xCred->initPagosEfectuados();
		
		$arrPP							= array();
		$arrPP["id"]					= $idplandepago;
		$arrPP["monto_cotizado"]		= $xCred->getMontoTotalPresumido();
		$arrPP["monto_pago"]			= $xCred->getMontoDeParcialidad();
		$arrPP["total_parcialidades"]	= $xCred->getPagosAutorizados();
		$arrPP["parcialidad_actual"]	= $xCred->getPeriodoActual();
		$arrPP["parcialidades_pagadas"] = setNoMenorQueCero(($xCred->getPagosAutorizados() - $xCred->getPeriodoActual()));
		
		
		try {
			$idplandepago2 				= $dbConn->insert('plan_pago', $arrPP);
			$xLog->add("OK\t$idcredito\tAgregado el Plan $idplandepago\r\n");
			$nuevoIDPlanPago			= $idplandepago;		//Contador
		} catch (\Simplon\Postgres\PostgresException $e){
			$xFRM->addAviso($e->getMessage());
			setLog($arrPP);
			$runMin						= false;
			$xLog->add("ERROR\t$idcredito\tNo se registro el Plan de Pagos $idplandepago\r\n");
		}
		

		//========== Insertar recibo de Credito 
		if($runMin == true){
			
			
			$arrRM							= array();
			$arrRM["id"] 					= $idrecibocredito;
			$arrRM["numero_cheque"] 		= $xCred->getClaveDeCredito();
			$arrRM["aval"] 					= null;
			$arrRM["razon_social"] 			= ($xSoc->getEsPersonaFisica()== false) ? "" : $xSoc->getNombreCompleto();
			$arrRM["notaria"] 				= "NA";
			$arrRM["notario"] 				= "NA";
			$arrRM["garante_hipotecario"] 	= "NA";
			$arrRM["conyuge"] 				= "";
			$arrRM["clabe"] 				= "NA";
			$arrRM["banco"] 				= "NA";
			$arrRM["presidente"] 			= null;
			$arrRM["secretario"] 			= null;
			$arrRM["tesorero"] 				= null;
			$arrRM["tipo_pago_id"] 			= 1;
			$arrRM["cuenta"] 				= null;
			$arrRM["sucursal"] 				= 1;
			$arrRM["referencias"] 			= "[]";
			$arrRM["compareciente"] 		= ($xSoc->getEsPersonaFisica()== false) ? $xSoc->getNombreDelRepresentanteLegal() : $xSoc->getNombreCompleto();
			$arrRM["beneficiario"] 			= $xSoc->getNombreCompleto();
			$arrRM["tipo_persona"] 			= ($xSoc->getEsPersonaFisica()== false) ? "M" : "F";
			$arrRM["es_transferencia"] 		= false;
			try {
				$idrecibocredito2 			= $dbConn->insert('recibo_credito', $arrRM);
				$nuevoRecCred				= $idrecibocredito;
			} catch (\Simplon\Postgres\PostgresException $e){
				$xFRM->addAviso($e->getMessage());
				setLog($arrRM);
				$runMin						= false;
				$xLog->add("ERROR\t$idcredito\tNo se registro el Recibo de Credito $idrecibocredito\r\n");
			}
		}
		
		//Ejecutar Ministracion
		$arrMin["monto_solicitado"]				= $xCred->getMontoSolicitado();
		$arrMin["monto_autorizado"]				= $xCred->getMontoAutorizado();
		$arrMin["plazo_solicitado"]				= $xCred->getPagosSolicitados();
		$arrMin["fecha_desembolso"]				= $xCred->getFechaDeMinistracion();
		$arrMin["saldo_capital"]				= $xCred->getSaldoActual();
		$arrMin["saldo_interes_normal"]			= $xCred->getInteresNormalPorPagar();
		$arrMin["saldo_interes_moratorio"]		= 0;
		$arrMin["tasa_normal"]					= $xCred->getTasaDeInteres()*100;
		$arrMin["tasa_moratoria"]				= $xCred->getTasaDeMora()*100;
		$arrMin["creado_por_id"]				= 1;	//Usuario que crea
		$arrMin["tipo_credito_id"]				= $tipoDeCredito;
		$arrMin["solicitud_origen_id"]			= $xCred->getClaveDeCredito();
		$arrMin["beneficiario_id"]				= $xCred->getClaveDePersona();
		$arrMin["destino_credito_id"]			= $destinoDeCredito;
		$arrMin["saldo"]						= $xCred->getSaldoActual();
		if($idempleado>0){
			$arrMin["empleado_empresa_id"]		= $idempleado;
		} else {
			$arrMin["empleado_empresa_id"]		= null;
		}
		$arrMin["numero_cheque"]				= $numeroDeCheque;
		$arrMin["fecha_primer_pago"]			= $xCred->getFechaDePrimerPago();
		$arrMin["identificador"]				= $xCred->getClaveDeCredito(); //clave de credito mas disp
		$arrMin["monto_cotizado"]				= $xCred->getMontoSolicitado();
		$arrMin["plan_pago_id"]					= $idplandepago;
		
		$arrMin["estado_cartera_id"]			= 1;//Vigente 2.- Administrativa 5.- Pagada
		if($xCred->getEsPagado() == true){
			$arrMin["estado_cartera_id"]		= 5;
		}
		$arrMin["frecuencia_pago"]				= $frecuenciaDePago;
		
		if($xCred->isAFinalDePlazo() == true){
			$arrMin["frecuencia_pago"] 			= "Mensual";
			$arrMin["plazo_solicitado"] 			= $xCred->getPlazoEnMeses();
		}
		
		$arrMin["cat_calculado"]				= $xCred->getCAT();
		
		$arrMin["cuenta_bancaria_id"]			= $cuentaBancaria;
		
		$arrMin["desvinculado"]					= false;
		if( $xCred->getEsDeDespedido() == true){
			$arrMin["desvinculado"]				= true;
		}
		//PARCIAL
		$arrMin["estado_pago"]					= "PARCIAL";
		
		if($xCred->getEsAfectable() == true){
			if($xCred->getEsPagado() == true){
				$arrMin["estado_pago"]			= "TOTAL";
				$fechaLiquidacion				= $xCred->getFechaUltimoDePago();
			} else {
				if($xCred->getEsCreditoYaAfectado() == false){
					$arrMin["estado_pago"]		= "NO_REALIZADO";
				}
			}
			
		}
		$arrMin["fecha_desvinculacion"]			= null;
		if($xCred->getEsDeDespedido() == true){
			if($xCred->getFechaDesvinculo() !== false){
				$arrMin["fecha_desvinculacion"]			= $xCred->getFechaDesvinculo();
			}
		}
		$arrMin["fecha_liquidacion"]							= $fechaLiquidacion;
		$arrMin["numero_reestructura"]							= 0;
		$arrMin["porcentaje_recargo_credito_americano"]			= 0;
		$arrMin["producto_credito_id"]							= $tipoDeCredito;
		$arrMin["aplica_iva"]									= ($xCred->getTasaIVA() > 0) ? true : false;
		$arrMin["porcentaje_iva"]								= $xCred->getTasaIVA()*100;
		$arrMin["tipo_interes_id"]								= ($xCred->getPagosSinCapital() == true) ? 1 : 2;
		$arrMin["origen_solicitud_credito"]						= "NUEVA";
		
		$arrMin["tipo_cobro_id"]								= 1;		//1.- Cheque Cambiado
		$arrMin["url_pagare"]									= null;
		$arrMin["en_renovacion"]								= false;//
		$arrMin["dias_atraso"]									= $xCred->getDiasDeMora();
		
		$arrMin["observaciones_transicion_cartera_judicial"]	= null;
		$arrMin["fecha_transicion_cartera_judicial"]			= null;
		$arrMin["fecha_transicion_cartera"]						= null;
		
		$arrMin["beneficiario_anterior_id"]						= null;
		$arrMin["observaciones_sustitucion_beneficiario"]		= null;
		$arrMin["fecha_sustitucion_beneficiario"]				= null;
		$arrMin["referencia_pago"]								= null;
		$arrMin["fecha_reestructuracion"]						= null;
		$arrMin["observaciones_reestructuracion"]				= null;
		
		$arrMin["tipo_registro"]								= "CREDITO_CARTERA";
		$arrMin["creado_en_sucursal_id"]						= 1;
		$arrMin["creado_en_fecha"]								= migFecha($d[$xT->FECHA_SOLICITUD]);
		$arrMin["es_bloqueado"] 								= false;
		$arrMin["id"] 											= $idministrado;
		
		if($runMin == true){
			try {
				$idministrado2 				= $dbConn->insert('credito_ministrado', $arrMin);
				$nuevoCredMin				= $idministrado;
				
			} catch (\Simplon\Postgres\PostgresException $e){
				$xFRM->addAviso($e->getMessage());
				setLog($arrMin);
				$runMin		= false;
				$xLog->add("ERROR\t$idcredito\tNo se Guardo el Credito Como Ministrado  $idministrado\r\n");
			}
		}
		//=================== Guardar Orden de Desembolso
		if($runMin == true AND $idministrado>0){

			$arrOD								= array();
			$arrOD["id"] 						= $idordendesembolso;
			$arrOD["nombre_beneficiario"] 		= $xSoc->getNombreCompleto();
			$arrOD["monto_solicitado"] 			= $xCred->getMontoAutorizado();
			$arrOD["monto_desembolsar"] 		= $xCred->getMontoAutorizado();
			$arrOD["fecha_desembolso"] 			= $xCred->getFechaDeMinistracion();
			$arrOD["estado"] 					= "DESEMBOLSADO";
			$arrOD["credito_id"] 				= $idcredito;
			$arrOD["fondeo_recurso_id"] 		= null;
			$arrOD["fecha_real_desembolso"] 	= $xCred->getFechaDeMinistracion();
			$arrOD["fecha_inicio_plan_pagos"] 	= $xCred->getFechaPrimeraParc();
			try {
				$idordendesembolso2 			= $dbConn->insert('orden_desembolso', $arrOD);
				$nuevaOrdenDes					= $idordendesembolso;
			} catch (\Simplon\Postgres\PostgresException $e){
				$xFRM->addAviso($e->getMessage());
				setLog($arrOD);
				$runMin		= false;
				$xLog->add("ERROR\t$idcredito\tNo se Guardo la Orden de Desembolso\r\n");
			}
		}
		//Ejecutar Entrega
		if($runMin == true AND $idministrado>0){
			$nnota	= "MINISTRACION CREDITO " . $xCred->getClaveDeCredito();
			
			if(migNuevoFichaPG($idficha, $d[$xT->FECHA_MINISTRACION], $xCred->getMontoAutorizado(), $nnota)  == true){
				
				$totalPagado					= round(($xCred->getMontoAutorizado() - $xCred->getSaldoActual()),2);
				if($xCred->getEsPagado() == true){
					$totalPagado				= $totalPagado;
				}
				$fichaPago						= $idficha+1;
				//importar letras
				if(migNuevoFichaPG($fichaPago, $xCred->getFechaUltimoDePago(), $totalPagado, "AJUSTE CAPITAL" . $xCred->getClaveDeCredito(), true) == true){
					$nuevaFicha					= $fichaPago;
				} else {
					$runMin						= false;
					$nuevaFicha					= $idficha;
					$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA-PAGO del Credito\r\n");
				}
				
				$xMonto							= $xCred->getOMontos();
				//============================== Obtener Interes Pagado.
				$InteresPagado					= $xMonto->getInteresNormalPagado();
				$fichaPago						= $idficha+2;
				//importar letras
				if(migNuevoFichaPG($fichaPago, $xCred->getFechaUltimoDePago(), $InteresPagado, "AJUSTE INTERES" . $xCred->getClaveDeCredito(), true) == true){
					$nuevaFicha					= $fichaPago;
				} else {
					$runMin						= false;
					$nuevaFicha					= $idficha;
					$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA-PAGO-INTERES del Credito\r\n");
				}
				
				
			} else {
				$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA del Credito\r\n");
				$runMin					= false;
			}
			/*$arrFC								= array();
			$arrFC["id"] 						= $idficha;
			$arrFC["ejercicio"] 				= $xF->anno($d[$xT->FECHA_MINISTRACION]);
			$arrFC["creada_en"] 				= $fechaDesembolso;
			$arrFC["es_ficha_cancelada"] 		= false;
			$arrFC["cambio"] 					= 0;
			$arrFC["sucursal_id"] 				= 1;
			$arrFC["creada_por_id"] 			= 1;
			$arrFC["cancelada_por_id"]		 	= null;
			try {
				$idficha2 						= $dbConn->insert('ficha', $arrFC);
				$nuevaFicha						= $idficha;
				//Insertar Operacion de Ministracion
			} catch (\Simplon\Postgres\PostgresException $e){
				$xFRM->addAviso($e->getMessage());
				setLog($arrFC);
				$runMin					= false;
				$xLog->add("ERROR\t$idcredito\tNo se guardo la FICHA del Credito\r\n");
			}*/
		}
		if($runMin == true AND $idficha>0){
				$arrDes								= array();
				$arrDes["monto_entregado"]			= $xCred->getMontoAutorizado();
				$arrDes["credito_ministrado_id"]	= $idministrado;
				$arrDes["ficha_id"]					= $idficha;
				$arrDes["orden_desembolso_id"]		= $idordendesembolso;
				$arrDes["creado_por_id"]			= 1;//Usuario
				$arrDes["creado_en_sucursal_id"]	= 1;
				$arrDes["creado_en_fecha"]			= $fechaDesembolso;
				
				try {
					$identrega 				= $dbConn->insert('desembolso', $arrDes);
				} catch (\Simplon\Postgres\PostgresException $e){
					$xFRM->addAviso($e->getMessage());
					setLog($arrDes);
					$runMin					= false;
					$xLog->add("ERROR\t$idcredito\tNo se guardo la Entrega del Credito\r\n");
				}
		}
		
		if($runMin == true AND $idministrado > 0){
		//=================== Actualizar Credito Ministrado
			$conds 									= array('id' => $idcredito);
			$DUCred									= array();
			$DUCred["fecha_desembolso"] 			= $d[$xT->FECHA_MINISTRACION];
			$DUCred["monto_desembolsable"] 			= $d[$xT->MONTO_AUTORIZADO];
			$DUCred["identificador"] 				= $d[$xT->NUMERO_SOLICITUD];
			
			$DUCred["fecha_expiracion"] 			= $d[$xT->FECHA_VENCIMIENTO];
			$DUCred["fecha_inicio_plan_pagos"] 		= $d[$xT->FECHA_DE_PRIMER_PAGO];
			$DUCred["plan_pago_id"] 				= $idplandepago;		//$xCred->getNumeroDePlanDePagos();
			$DUCred["recibo_credito_id"] 			= $idrecibocredito;
			
			$DUCred["cuenta_bancaria_id"] 			= 1;//TODO: Revisar
			$DUCred["credito_ministrado_id"] 		= $idministrado;
			$DUCred["credito_ministrado_identificador"] = $xCred->getClaveDeCredito();
			$DUCred["credito_ministrado_saldo"] 	= $xCred->getSaldoActual();
			
			if($runMin == true){
				try {
					$result = $dbConn->update('credito', $conds, $DUCred);
				} catch (\Simplon\Postgres\PostgresException $e){
					$xFRM->addAviso($e->getMessage());
					setLog($DUCred);
				}
			}
		}
		//================================================== END
	}
	//Actualizar Registro
}
$xFRM->addLog($xLog->getMessages());


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
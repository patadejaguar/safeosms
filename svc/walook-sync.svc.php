<?php
//ini_set("display_errors","1");
/**
 * Modulo
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
$xHP		= new cHPage("", HP_SERVICE);
$xQL		= new MQL();
//$xLi		= new cSQLListas();
$xF			= new cFecha();
ini_set("max_execution_time", 1600);

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); $fecha = parametro("fecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action = parametro("cmd", $action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");
$letra			= parametro("letra", false, MQL_INT);

$action			= strtolower($action);

$xLog			= new cCoreLog();
$xSVC			= new MQLService($action, "");

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos $action";

$rs["importados"]	= array();
$rs["rechazados"]	= array();
$rs["existentes"]	= array();

//================================ Conexion
$config = array(
		// required credentials
		
		'host'       => AML_MIGRACION_SRV,
		'user'       => AML_MIGRACION_USR,
		'password'   => AML_MIGRACION_PWD,
		'database'   => AML_MIGRACION_DB,
		
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
//================================ Importar personas

$sqlBuilder = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
$sqlBuilder2 = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
$sqlBuilder3 = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
$sqlBuilder4 = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();


$FechaDeCorte	= $fecha;

$rs["fecha"] 	= $FechaDeCorte;

$arrConvTP		= array(1 => 101,
		2 => 102,
		3 => 151,
		4 => 152,
		5 => 201,
		6 => 202,
		7 => 301,
		8 => 302,
		9 => 303,
		10 => 399,
		11 => 401,
		12 => 402
);


$arrPersonas	= array();
$arrCreditos	= array();


if($action == "all" OR $action=="personas"){
	
	$xSoc	= new cSocio( false );
	//$xSoc->setOmitirAML(true);
	
	$sqlBuilder->setQuery("SELECT * FROM public.persona WHERE created_date>='$FechaDeCorte' ");
	
	foreach ($pgSqlManager->fetchRowManyCursor($sqlBuilder) as $result){
		$activo 					= $result["activo"];
		$carnet 					= $result["carnet"];
		$cliente 					= $result["cliente"];
		$correo_electronico 		= $result["correo_electronico"];
		$created_by 				= $result["created_by"];
		$created_date 				= $result["created_date"];
		$cuenta_contable_id 		= $result["cuenta_contable_id"];
		$curp 						= $result["curp"];
		$dependientes 				= $result["dependientes"];
		$empresa_empleadora 		= $result["empresa_empleadora"];
		$empresa_id 				= $result["empresa_id"];
		$estado_civil_id 			= $result["estado_civil_id"];
		$estado_id 					= $result["estado_id"];
		$fecha_nacimiento 			= $result["fecha_nacimiento"];
		$fecha_residencia 			= $result["fecha_residencia"];
		$fecha_vencimiento_carnet 	= $result["fecha_vencimiento_carnet"];
		$firma_electronica 			= $result["firma_electronica"];
		$genero 					= $result["genero"];
		$ha_estado_en_cartera_judicial = $result["ha_estado_en_cartera_judicial"];
		$id 						= $result["id"];
		$last_modified_by 			= $result["last_modified_by"];
		$last_modified_date 		= $result["last_modified_date"];
		$lugar_nacimiento 			= $result["lugar_nacimiento"];
		$nacionalidad_extranjera 	= $result["nacionalidad_extranjera"];
		$nivel_riesgo_pld_id 		= $result["nivel_riesgo_pld_id"];
		$nombre 					= $result["nombre"];
		$nombre_razon 				= $result["nombre_razon"];
		$numero_acta 				= $result["numero_acta"];
		$numero_documento 			= setCadenaVal($result["numero_documento"]);
		$observacion 				= setCadenaVal($result["observacion"]);
		$ocupo_cargo_publico 		= $result["ocupo_cargo_publico"];
		$pais_id 					= $result["pais_id"];
		$primer_apellido 			= $result["primer_apellido"];
		$profesion 					= $result["profesion"];
		$propiedades_en_extranjero 	= $result["propiedades_en_extranjero"];
		$razon_social 				= $result["razon_social"];
		$razones_no_firma 			= setCadenaVal($result["razones_no_firma"]);
		$regimen_fiscal_id 			= $result["regimen_fiscal_id"];
		$regimen_matrimonial_id 	= $result["regimen_matrimonial_id"];
		$rfc 						= $result["rfc"];
		$segundo_apellido 			= $result["segundo_apellido"];
		$sucursal_id 				= $result["sucursal_id"];
		$telefono 					= $result["telefono"];
		$tipo_identificacion_id 	= $result["tipo_identificacion_id"];
		$tipo_persona 				= $result["tipo_persona"];
		
		
		
		/*$nombre, $apellidopaterno = "", $apellidomaterno = "",
		 $rfc = "", $curp = "", $cajalocal = DEFAULT_CAJA_LOCAL,
		 $fecha_de_nacimiento = false, $lugar_de_nacimiento = "",
		 $tipo_de_ingreso = FALLBACK_PERSONAS_TIPO_ING, $estado_civil = DEFAULT_ESTADO_CIVIL,
		 $genero = DEFAULT_GENERO, $dependencia = FALLBACK_CLAVE_EMPRESA, $regimen_conyugal = DEFAULT_REGIMEN_CONYUGAL,
		 $personalidad_juridica = PERSONAS_FIGURA_FISICA, $grupo_solidario = DEFAULT_GRUPO, $observaciones = "",
		 $identificado_con = 1, $documento_de_identificacion = "0", $codigo = false, $sucursal = false,
		 $movil	= "", $correo = "", $dependientes = 0, $fecha = false, $riesgo = AML_PERSONA_BAJO_RIESGO, $clave_fiel = "",
		 $pais = EACP_CLAVE_DE_PAIS, $regimen_fiscal = DEFAULT_REGIMEN_FISCAL, $tituloPersonal = ""*/
		$EsMoral	= ($tipo_persona == "M") ? true : false;
		
		$xSoc2		= new cSocio($id);
		
		if($xSoc2->init() == true){
			$rs["existentes"]["personas"][$id]	= $id;
			
		}else {
			
			if($EsMoral == true){
				$nombre	= $nombre_razon;
				$tipo_persona	= PERSONAS_FIGURA_MORAL;
				$genero 		= DEFAULT_GENERO;
			} else {
				$tipo_persona	= PERSONAS_FIGURA_FISICA;
				if($genero == "F"){
					$genero 		= 2;
				} else {
					$genero 		= 1;
				}
			}
			$success	= $xSoc->add($nombre, $primer_apellido, $segundo_apellido, $rfc, $curp, DEFAULT_CAJA_LOCAL, $fecha_nacimiento, $lugar_nacimiento, 200, DEFAULT_ESTADO_CIVIL,
					$genero, FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL,
					$tipo_persona, DEFAULT_GRUPO, $observaciones, FALLBACK_CLAVE_DE_DOCTO, $numero_documento, $id);
			if($success == true){
				$rs["importados"]["personas"][$id]	= $id;
				$arrPersonas[$id]					= $id;
				
			} else {
				$rs["rechazados"]["personas"][$id]	= $xSoc->getMessages();
			}
		}
		
	}
	
//}
//if($action == "all" OR $action=="perfiltransaccional"){
	
	
	$sqlBuilder2->setQuery("SELECT * FROM public.perfil_transaccional");
	
	foreach ($pgSqlManager->fetchRowManyCursor($sqlBuilder2) as $result2){
		$idxP					= $result2["persona_id"];
		
		if(isset($arrPersonas[$idxP])){
			
			$activo					= $result2["activo"];
			$aplicacion_recurso_id	= $result2["aplicacion_recurso_id"];
			
			$id						= $result2["id"];
			$monto_maximo_operaciones_mensuales		= $result2["monto_maximo_operaciones_mensuales"];
			$numero_maximo_operaciones_mensuales	= $result2["numero_maximo_operaciones_mensuales"];
			$observaciones			=  setCadenaVal($result2["observaciones"]);
			$origen_recurso			= $result2["origen_recurso"];
			$pais_id				= $result2["pais_id"];
			$persona_id				= $result2["persona_id"];
			$tipo_perfil_id			= $result2["tipo_perfil_id"];
			$tipo_perfil_id			= (isset($arrConvTP[$tipo_perfil_id])) ? $arrConvTP[$tipo_perfil_id] : 101;
			
			$xSoc2		= new cSocio($persona_id);
			if($xSoc2->init() == false){
				$res				= false;
			} else {
				$xPerfil			= new cAMLPersonasPerfilTransaccional($persona_id);
				$res				= $xPerfil->add($tipo_perfil_id, "MX", $monto_maximo_operaciones_mensuales, $numero_maximo_operaciones_mensuales, $observaciones, false, $aplicacion_recurso_id);
			}
			if($res === false){
				$rs["rechazados"]["perfiltransaccional"][$id]	= $id;
			} else {
				$rs["importados"]["perfiltransaccional"][$id]	= $id;
			}
		}
		/*$xSoc	= new cSocio($persona_id);
		 if($xSoc->init() == true){
		 
		 }*/
	}
}


if($action == "all" OR $action=="perfiltransaccional"){
	$xQL->setRawQuery("DELETE FROM `personas_perfil_transaccional`");
	
	$sqlBuilder2->setQuery("SELECT * FROM public.perfil_transaccional");
	
	foreach ($pgSqlManager->fetchRowManyCursor($sqlBuilder2) as $result2){
		$idxP					= $result2["persona_id"];
		
		
			
			$activo					= $result2["activo"];
			$aplicacion_recurso_id	= $result2["aplicacion_recurso_id"];
			$id						= $result2["id"];
			$monto_maximo_operaciones_mensuales		= $result2["monto_maximo_operaciones_mensuales"];
			$numero_maximo_operaciones_mensuales	= $result2["numero_maximo_operaciones_mensuales"];
			$observaciones			=  setCadenaVal($result2["observaciones"]);
			$origen_recurso			= $result2["origen_recurso"];
			$pais_id				= $result2["pais_id"];
			$persona_id				= $result2["persona_id"];
			$tipo_perfil_id			= $result2["tipo_perfil_id"];
			$tipo_perfil_id			= (isset($arrConvTP[$tipo_perfil_id])) ? $arrConvTP[$tipo_perfil_id] : 101;
			
			$xSoc2		= new cSocio($persona_id);
			if($xSoc2->init() == false){
				$res				= false;
			} else {
				$xPerfil			= new cAMLPersonasPerfilTransaccional($persona_id);
				$res				= $xPerfil->add($tipo_perfil_id, "MX", $monto_maximo_operaciones_mensuales, $numero_maximo_operaciones_mensuales, $observaciones, false, $aplicacion_recurso_id);
			}
			if($res === false){
				$rs["rechazados"]["perfiltransaccional"][$id]	= $id;
			} else {
				$rs["importados"]["perfiltransaccional"][$id]	= $id;
			}

	}
}

$aperiodicidad	= array(
		"SEMANAL" => 7,
		"DECENAL" => 10,
		"CATORCENAL" => 14,
		"QUINCENAL" => 15,
		"MENSUAL" => 30
);
if($action == "all" OR $action=="creditos"){
	$xCred				= new cCredito();
	$ContratoCorriente	= 0;
	$producto			= 210;
	//TODO: Seleccionar solo los creditos mayores a CERO
	
	$sqlBuilder3->setQuery("SELECT * FROM public.credito_ministrado  WHERE creado_en_fecha>='$FechaDeCorte' ");
	
	foreach ($pgSqlManager->fetchRowManyCursor($sqlBuilder3) as $result3){
		
		
		$aplica_iva					= $result3["aplica_iva"];
		$beneficiario_anterior_id	= $result3["beneficiario_anterior_id"];
		$beneficiario_id			= $result3["beneficiario_id"];
		$cat_calculado				= $result3["cat_calculado"];
		$creado_en_fecha			= $result3["creado_en_fecha"];
		$creado_en_sucursal_id		= $result3["creado_en_sucursal_id"];
		$creado_por_id				= $result3["creado_por_id"];
		$cuenta_bancaria_id			= $result3["cuenta_bancaria_id"];
		$destino_credito_id			= $result3["destino_credito_id"];
		$desvinculado				= $result3["desvinculado"];
		$dias_atraso				= $result3["dias_atraso"];
		$empleado_empresa_id		= $result3["empleado_empresa_id"];
		$en_renovacion				= $result3["en_renovacion"];
		$es_bloqueado				= $result3["es_bloqueado"];
		$estado_cartera_id			= $result3["estado_cartera_id"];
		$estado_pago				= $result3["estado_pago"];
		$fecha_desembolso			= $result3["fecha_desembolso"];
		$fecha_desvinculacion		= $result3["fecha_desvinculacion"];
		$fecha_liquidacion			= $result3["fecha_liquidacion"];
		$fecha_primer_pago			= $result3["fecha_primer_pago"];
		$fecha_reestructuracion		= $result3["fecha_reestructuracion"];
		$fecha_sustitucion_beneficiario	=$result3["fecha_sustitucion_beneficiario"];
		$fecha_transicion_cartera	= $result3["fecha_transicion_cartera"];
		$fecha_transicion_cartera_judicial	=$result3["fecha_transicion_cartera_judicial"];
		$frecuencia_pago			= $result3["frecuencia_pago"];
		$id							= $result3["id"];
		$identificador				= $result3["identificador"];
		$monto_autorizado			= $result3["monto_autorizado"];
		$monto_cotizado				= $result3["monto_cotizado"];
		$monto_solicitado			= $result3["monto_solicitado"];
		$numero_cheque				= $result3["numero_cheque"];
		$numero_reestructura		= $result3["numero_reestructura"];
		$observaciones_reestructuracion	=$result3["observaciones_reestructuracion"];
		$observaciones_sustitucion_beneficiario	=$result3["observaciones_sustitucion_beneficiario"];
		$observaciones_transicion_cartera_judicial	=$result3["observaciones_transicion_cartera_judicial"];
		$origen_solicitud_credito	= $result3["origen_solicitud_credito"];
		$plan_pago_id				= $result3["plan_pago_id"];
		$plazo_solicitado			= $result3["plazo_solicitado"];
		$porcentaje_iva				= $result3["porcentaje_iva"];
		$porcentaje_recargo_credito_americano	=$result3["porcentaje_recargo_credito_americano"];
		$producto_credito_id		= $result3["producto_credito_id"];
		$referencia_pago			= $result3["referencia_pago"];
		$saldo						= $result3["saldo"];
		$saldo_capital				= $result3["saldo_capital"];
		$saldo_interes_moratorio	= $result3["saldo_interes_moratorio"];
		$saldo_interes_normal		= $result3["saldo_interes_normal"];
		$solicitud_origen_id		= $result3["solicitud_origen_id"];
		$tasa_moratoria				= $result3["tasa_moratoria"];
		$tasa_normal				= $result3["tasa_normal"];
		$tipo_cobro_id				= $result3["tipo_cobro_id"];
		$tipo_contrato_credito_id	= $result3["tipo_contrato_credito_id"];
		$tipo_credito_id			= $result3["tipo_credito_id"];
		$tipo_cuenta_credito_id		= $result3["tipo_cuenta_credito_id"];
		$tipo_interes_id			= $result3["tipo_interes_id"];
		$tipo_registro				= $result3["tipo_registro"];
		$tipo_responsabilidad_id	= $result3["tipo_responsabilidad_id"];
		$url_pagare					= $result3["url_pagare"];
		
		$periocidad					= $aperiodicidad[ strtoupper($frecuencia_pago) ];
		$credito					= $id;
		$tasa						= ($tasa_normal / 100);
		$dias						= ($plazo_solicitado * 30.416666666666666666666);
		$pagos						= floor( ($dias / $periocidad) );
		$aplicacion					= FALLBACK_CRED_TIPO_DESTINO;
		$descDestino				= "";
		$fechaSolicitado			= $xF->getFechaISO($fecha_desembolso);
		$ministracion				= $xF->getFechaISO($fecha_desembolso);
		$vencimiento				= $xF->setSumarDias($dias, $ministracion);
		$UltimaOperacion			= $ministracion;
		$socio						= $beneficiario_id;
		$monto						= $monto_autorizado;
		
		$xCred2			= new cCredito($credito);
		if($xCred2->init() == true){
			$rs["existentes"]["creditos"][$id]	= $xCred2->getMessages();
		} else {
			
			$rcred	 	= $xCred->add($producto, $socio, $ContratoCorriente, $monto, $periocidad, $pagos, $dias, $aplicacion, $credito,
					DEFAULT_GRUPO, $descDestino, "CREDITO IMPORTADO", DEFAULT_USER, $fechaSolicitado,
					CREDITO_TIPO_PAGO_PERIODICO,INTERES_POR_SALDO_INSOLUTO, $tasa);
			$credito	= $xCred->getNumeroDeCredito();
			$ok			= ($rcred === false) ? false: true;
			
			if($ok == true){
				$xCred		= new cCredito($credito);
				///Inicializar
				$xCred->init();
				
				$credito	= $xCred->getNumeroDeCredito();
				//autorizar
				$rauth 	= $xCred->setAutorizado($monto, $pagos, $periocidad, CREDITO_TIPO_AUTORIZACION_NORMAL, $ministracion,
						"", false, $ministracion,2, false,
						$vencimiento, CREDITO_ESTADO_AUTORIZADO, $monto, 0, $UltimaOperacion);
				//usleep(1000);
				$xCred->setCuandoSeActualiza();
				$xLog->add($xCred->getMessages());
				$ok		= ($rauth === false) ? false : true;
				
			}
			if($ok == true){
				$xCred		= new cCredito($credito);
				///Inicializar
				$xCred->init();
				$credito	= $xCred->getNumeroDeCredito();
				//ministrar
				$xCred->setForceMinistracion();
				$xCred->setMinistrar(DEFAULT_RECIBO_FISCAL, DEFAULT_CHEQUE, $monto, DEFAULT_CUENTA_BANCARIA, 0, DEFAULT_CUENTA_BANCARIA, "", $ministracion);
				
				
			}
			
			if($ok == true){
				$rs["importados"][$action][$id]	= $xCred->getMessages();
				$arrCreditos[$id]				= $id;
			} else {
				$rs["rechazados"][$action][$id]	= $xCred->getMessages();
			}
			
		}
	}
	$xCUtils		= new cUtileriasParaCreditos();
	$xCUtils->setCreditosCuadrarPlanes();
	
	
	
	/*							$rcred	 = $xCred->add($producto, $socio, $ContratoCorriente, $monto, $periocidad, $pagos, $dias, $aplicacion, $credito,
	 DEFAULT_GRUPO, $descDestino, "CREDITO IMPORTADO #$iReg", DEFAULT_USER, $fechaSolicitado,
	 CREDITO_TIPO_PAGO_PERIODICO,INTERES_POR_SALDO_INSOLUTO, $tasa);
	 $credito	= $xCred->getNumeroDeCredito();
	 $xLog->add($xCred->getMessages());
	 $ok		= ($rcred === false) ? false: true;
	 */
	
}
if($action == "all" OR $action=="soloplan"){
	$xCUtils		= new cUtileriasParaCreditos();
	$xCUtils->setCreditosCuadrarPlanes();
	$xCUtils->setRegenerarPlanPagosNoExistentes();
}
if($action == "all" OR $action=="operaciones"){
	
	
	
	$sqlBuilder4->setQuery("SELECT public.aplicacion_pago_bancario.*, public.credito_ministrado.id AS credito_ministrado_id, public.detalle_plan_pago.numero_pago AS numero_pago
FROM     aplicacion_pago_bancario 
INNER JOIN detalle_plan_pago  ON aplicacion_pago_bancario.detalle_plan_pago_id = detalle_plan_pago.id 
INNER JOIN plan_pago  ON detalle_plan_pago.plan_pago_id = plan_pago.id 
INNER JOIN credito_ministrado ON credito_ministrado.plan_pago_id = plan_pago.id  WHERE public.aplicacion_pago_bancario.creado_en_fecha>='$FechaDeCorte' ");
	
	
	/*setLog("SELECT public.aplicacion_pago_bancario.*, public.credito_ministrado.id AS credito_ministrado_id, public.detalle_plan_pago.numero_pago AS numero_pago
FROM     aplicacion_pago_bancario
INNER JOIN detalle_plan_pago  ON aplicacion_pago_bancario.detalle_plan_pago_id = detalle_plan_pago.id
INNER JOIN plan_pago  ON detalle_plan_pago.plan_pago_id = plan_pago.id
INNER JOIN credito_ministrado ON credito_ministrado.plan_pago_id = plan_pago.id  WHERE public.aplicacion_pago_bancario.creado_en_fecha>='$FechaDeCorte' ");*/
	
	foreach ($pgSqlManager->fetchRowManyCursor($sqlBuilder4) as $resultx){
		
		$aplicacion_general_pago_id		= $resultx["aplicacion_general_pago_id"];
		$beneficiario_id				= $resultx["beneficiario_id"];
		
		$creado_en_fecha				= $resultx["creado_en_fecha"];
		$creado_en_sucursal_id			= $resultx["creado_en_sucursal_id"];
		$creado_por_id					= $resultx["creado_por_id"];
		$cuenta_bancaria_id				= $resultx["cuenta_bancaria_id"];
		$desvinculado					= $resultx["desvinculado"];
		$detalle_plan_pago_id			= $resultx["detalle_plan_pago_id"];
		$dias_transcurridos_vencimiento	= $resultx["dias_transcurridos_vencimiento"];
		$empresa_id						= $resultx["empresa_id"];
		$es_pago_real					= $resultx["es_pago_real"];
		$fecha_deposito					= $resultx["fecha_deposito"];
		
		
		$ficha_id						= $resultx["ficha_id"];
		$id								= $resultx["id"];
		$recibo_id						= $resultx["id"];
		$identificador					= $resultx["identificador"];

		
		$origen_aplicacion_pago			= $resultx["origen_aplicacion_pago"];
		
		$producto_credito_id			= $resultx["producto_credito_id"];
		$retencion_id					= $resultx["retencion_id"];
		
		$saldo							= $resultx["saldo"];
		$saldo_capital					= $resultx["saldo_capital"];
		$saldo_insoluto					= $resultx["saldo_insoluto"];
		$saldo_interes_moratorio		= $resultx["saldo_interes_moratorio"];
		$saldo_interes_ordinario		= $resultx["saldo_interes_ordinario"];
		
		$saldo_iva_interes_moratorio	= $resultx["saldo_iva_interes_moratorio"];
		$saldo_iva_interes_ordinario	= $resultx["saldo_iva_interes_ordinario"];
		$saldo_iva_otros_cargos			= $resultx["saldo_iva_otros_cargos"];
		$saldo_otros_cargos				= $resultx["saldo_otros_cargos"];
		$tipo_cobro_id					= $resultx["tipo_cobro_id"];
		$tipo_credito_id				= $resultx["tipo_credito_id"];
	
		$fecha_valor					= $resultx["fecha_valor"];
		$interes_moratorio				= $resultx["interes_moratorio"];
		$interes_ordinario				= $resultx["interes_ordinario"];
		$iva_interes_moratorio			= $resultx["iva_interes_moratorio"];
		$iva_interes_ordinario			= $resultx["iva_interes_ordinario"];
		$iva_otros_cargos				= $resultx["iva_otros_cargos"];
		$capital						= $resultx["capital"];
		$monto							= $resultx["monto"];
		$otros_cargos					= $resultx["otros_cargos"];
		
		$credito						= $resultx["credito_ministrado_id"];
		$parcialidad					= $resultx["numero_pago"];
		
		$socio							= $beneficiario_id;
		
		$xRec2							= new cReciboDeOperacion();
		
		$TipoDeCobro					= ($tipo_cobro_id == 4) ? TESORERIA_COBRO_EFECTIVO : TESORERIA_COBRO_TRANSFERENCIA;
		
		if($xRec2->initByFolioExterno($recibo_id) == true){
			$rs["existentes"][$action][$recibo_id]	= "WARN\tOmitido el Recibo $recibo_id por $monto (Capital : $capital. Interes : $interes_ordinario. Mora: $interes_moratorio. Otros: $otros_cargos)\r\n" . $xRec2->getMessages();
		} else {
			$xRec							= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO);
			$xRec->setFolioExterno($recibo_id);
			$idnuevoRec						= $xRec->setNuevoRecibo($beneficiario_id, $credito, $fecha_deposito, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO, "", "", $TipoDeCobro);
			if($idnuevoRec > 0){
				
				$xCred				= new cCredito($credito);
				$robserva			= "";
				if($xCred->init() == true){
					if($capital>0){
						$xCred->setAbonoCapital($capital, $parcialidad, DEFAULT_CHEQUE, $TipoDeCobro, DEFAULT_RECIBO_FISCAL, $robserva, false, $fecha_deposito, $idnuevoRec);
					}
					if($interes_ordinario>0){
						$xCred->setAbonoInteres($interes_ordinario, $parcialidad, DEFAULT_CHEQUE, $TipoDeCobro, DEFAULT_RECIBO_FISCAL, $robserva, false, $fecha_deposito, $idnuevoRec);
					}
					
					if($interes_moratorio > 0){
						$xCred->setAbonoInteres($interes_moratorio, $parcialidad, DEFAULT_CHEQUE, $TipoDeCobro, DEFAULT_RECIBO_FISCAL, $robserva, false, $fecha_deposito, $idnuevoRec, true);
					}
					
					if($iva_interes_ordinario>0){
						$xRec->setNuevoMvto($fecha_deposito, $iva_interes_ordinario, OPERACION_CLAVE_PAGO_IVA_INTS, $parcialidad, $robserva);
					}
					if($otros_cargos > 0){
						$xRec->setNuevoMvto($fecha_deposito, $otros_cargos, OPERACION_CLAVE_PAGO_COM_VARIAS, $parcialidad, $robserva);
					}
					if($iva_interes_moratorio >0){
						$xRec->setNuevoMvto($fecha_deposito, $iva_interes_moratorio, OPERACION_CLAVE_PAGO_IVA_OTROS, $parcialidad, $robserva);
					}
					if($iva_otros_cargos >0){
						$xRec->setNuevoMvto($fecha_deposito, $iva_otros_cargos, OPERACION_CLAVE_PAGO_IVA_OTROS, $parcialidad, $robserva);
					}
					
					//$xRec->setForceUpdateSaldos(true);
					$xRec->setSumaDeRecibo($monto);
					$xRec->setDatosDePago(EACP_CLAVE_MONEDA_LOCAL, $monto, '', $TipoDeCobro);
					//$xCred->getORecibo()->setDatosDePago()
					$xRec->setFinalizarRecibo(true, true);
					
					$rs["importados"][$action][$recibo_id]	= "OK\tNuevo Recibo $idnuevoRec ($recibo_id) por $monto (Capital : $capital. Interes : $interes_ordinario. Mora: $interes_moratorio. Otros: $otros_cargos)\r\n" . $xRec->getMessages();
				} else {
					$rs["rechazados"][$action][$recibo_id]	= "ERROR\tCredito $credito no se inicia. No se agrega el Nuevo Recibo $idnuevoRec ($recibo_id) por $monto (Capital : $capital. Interes : $interes_ordinario. Mora: $interes_moratorio. Otros: $otros_cargos)\r\n" . $xRec->getMessages();
				}
				
			} else {
				$rs["rechazados"][$action][$recibo_id]	= "ERROR\tNo se agrega el Nuevo Recibo $idnuevoRec ($recibo_id) por $monto (Capital : $capital. Interes : $interes_ordinario. Mora: $interes_moratorio. Otros: $otros_cargos)\r\n" . $xRec->getMessages();
			}
			//End rec
		}
	}
}
/*
 $sqlBuilder = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
 $sqlBuilder->setQuery("SELECT tbls.table_name FROM information_schema.tables AS tbls INNER JOIN information_schema.columns AS cols ON tbls.table_name = cols.table_name WHERE tbls.table_catalog='gpd1601' AND tbls.table_schema='public' AND cols.column_name='id'");
 foreach ($pgSqlManager->fetchRowManyCursor($sqlBuilder) as $result){
 
 try {
 $tabla	= $result["table_name"];
 $xsql	= "SELECT setval('" . $tabla . "_id_seq', (SELECT MAX(id)+1 FROM $tabla), true)";
 $rez 	= $dbConn->executeSql($xsql);
 //$xLog->add($xsql . "\r\n");
 } catch (\Simplon\Postgres\PostgresException $e){
 
 $xFRM->addAviso($e->getMessage());
 $xLog->add( $e->getMessage() );
 //$xLog->add("ERROR\t$idcredito\tTipo de Credito a fallback\r\n");
 
 }
 
 }
 * */



header('Content-type: application/json');
echo json_encode($rs);


?>
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
$xLog		= new cCoreLog();
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
	$result = $dbConn->executeSql('DELETE FROM public.actividad_economica');
}
//================================== principales activos
$arrPrincipales			= array();

//===================================
$sql 	= "SELECT   `socios_aeconomica`.`idsocios_aeconomica` AS `id`,
         `socios_aeconomica`.`fecha_de_ingreso` AS `fecha_ingreso`,
         `socios_aeconomica`.`monto_percibido_ae` AS `ingreso_mensual`,
         `socios_aeconomica`.`puesto` AS `cargo`,
         `socios_aeconomica`.`departamento_ae` AS `departamento`,
         `socios_aeconomica`.`numero_empleado` AS `clave_empleado`,
         `socios_aeconomica`.`numero_de_seguridad_social` AS `numero_seg_soc`,
         IF(`socios_aeconomica`.`estado_actual`!=0, 'true', 'false') AS `principal`,

         `socios_aeconomica`.`descripcion` AS `observacion`,
         IF(`socios_aeconomica`.`estado_actual`!=0, 'false', 'true') AS `empleo_anterior`,
         getMigGetUIF(`socios_aeconomica`.`tipo_aeconomica`) AS `actividad_uif_id`,
         getMigGetSCIAN(`socios_aeconomica`.`clave_scian`) AS `actividad_scian_id`,
/*         `socios_aeconomica`.`domicilio_vinculado` AS `direccion_id`,*/
'1' AS `direccion_id`,
         `socios_aeconomica`.`socio_aeconomica` AS `persona_id`,
         getMigGetT4(`dependencia_ae`) AS `empresa_id`,
/* `socios_aeconomica`.`dependencia_ae` AS `empresa_id`, */
         getMigGetDisp(`socios_aeconomica`.`empleado_tipo_de_dispersion`) AS `dispersion_nomina_id`
FROM     `socios_aeconomica` ";

$xQL->setConTitulos();

$rs			= $xQL->getDataRecord($sql);
$xT			= new cSocios_aeconomica();
$arrTit		= $xQL->getTitulos();
foreach ($rs as $rw){
	$idactividad		= $rw["id"];
	$idpersona			= $rw["persona_id"];
	
	//$idpersona	= $rw[$xT->CODIGO];
	//$idpersona		= $rw["id"];
	$ingresomensual		= setNoMenorQueCero($rw["ingreso_mensual"]);
	if($rw["principal"] == "true" AND isset( $arrPrincipales[ $idpersona] ) ){
		$rw["principal"] = "false";
		$xLog->add("WARN\t$idpersona\t$idactividad\tPrincipal Duplicado\r\n");
	}
	if($rw["principal"] == "true" AND $ingresomensual > 0){
		$arrPrincipales[ $idpersona ]	= true;
		//$xLog->add("WARN\t$idpersona\t$idactividad\tPrincipal agregado\r\n");
	}
	if($ingresomensual<=0){
		$ingresomensual	= 1;
	}
			
	
	$arrD			= array();
	//setLog($arrTit);
	foreach ($arrTit as $twd){
		$campo			= $twd["N"];
		$valor			= $rw[$campo];

		
		if($valor == ""){
			$valor		= null;
		}
		$arrD[$campo]	= $valor;
	}

	//setLog($arrD);
	/*$xSoc		= new cSocio($idpersona);
	if($xSoc->init() == true){

	}*/
	try {
		$idx = $dbConn->insert('actividad_economica', $arrD);
	} catch (\Simplon\Postgres\PostgresException $e){
		$xFRM->addAviso($e->getMessage());
		$xLog->add( $e->getMessage() );
		//$xLog->add("ERROR\t$idcredito\tTipo de Credito a fallback\r\n");
		setLog($arrD);
	}
	
}

$xFRM->addLog($xLog->getMessages());

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
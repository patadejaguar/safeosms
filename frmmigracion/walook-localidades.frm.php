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


ini_set("max_execution_time", 1800);



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
	//================= Actualizar registros de Localidades a menor a 100
	$result = $dbConn->executeSql('UPDATE "public"."direccion" SET "localidad_id" = 1');
	$result = $dbConn->executeSql('DELETE FROM "localidad" WHERE id >1');
	//$result = $dbConn->executeSql('DELETE FROM public.direccion');
}

//===================================
$psql1	= 'SELECT "id" FROM "municipio" WHERE "clave_mpio" = :idmunicipio AND "estado_id" = :idestado';

$sql 	= "SELECT * FROM `catalogos_localidades`";

$xQL->setConTitulos();

$rs			= $xQL->getDataRecord($sql);
$xT			= new cCatalogos_localidades();

$arrTit		= $xQL->getTitulos();



foreach ($rs as $rw){
	$claveloc		= $rw[$xT->CLAVE_DE_LOCALIDAD];
	$clavemun		= $rw[$xT->CLAVE_DE_MUNICIPIO];
	$claveedo		= $rw[$xT->CLAVE_DE_ESTADO];
	$nombre			= $rw[$xT->NOMBRE_DE_LA_LOCALIDAD];
	
	$sqlBuilder2 	= new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
	$sqlBuilder2->setQuery($psql1)->setConditions(array('idmunicipio' => $clavemun, 'idestado' => $claveedo));
	$nidmunicipio	= $pgSqlManager->fetchColumn($sqlBuilder2);
	$nidmunicipio	= setNoMenorQueCero($nidmunicipio);
	
	if($nidmunicipio<=0){
		$nidmunicipio	= 1;
	}
	
	$arr			= array("clave_loc" => $claveloc , "municipio_id" => $nidmunicipio,	"nombre" => $nombre);
	//if($nidmunicipio>0)
	try {
		$idx = $dbConn->insert('localidad', $arr);
	} catch (\Simplon\Postgres\PostgresException $e){
		$xFRM->addAviso($e->getMessage());
		$xLog->add( $e->getMessage() );
		setLog($arr);
	}
	
}

$xFRM->addLog($xLog->getMessages());

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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
$sqlBuilder = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
$sqlBuilder->setQuery('SELECT   "public"."actividad_economica"."id" FROM "public"."actividad_economica" ');

foreach ($pgSqlManager->fetchRowManyCursor($sqlBuilder) as $result){
	$id		= $result["id"];
	$xAE	= new cPersonaActividadEconomica();
	$xAE->setID($id, true);
	//$xLog->add("ERROR\t$id\tVivienda $id\r\n");
	if($xAE->init() == true){
		$xLog->add("ERROR\t$id\tIniciando la Actividad $id\r\n");
		$iddomicilio	= $xAE->getClaveDeDomicilio();
		$xDom			= new cPersonasVivienda();
		$xDom->setID($iddomicilio);
		if($xDom->init() == true){
			$xLog->add("ERROR\t$id\tIniciando El Domicilio $iddomicilio\r\n");
			try {
				$xsql	= 'UPDATE "actividad_economica" SET	"direccion_id" = ' . $iddomicilio . ' WHERE "id" =' . $id . ' ';
				$rez 	= $dbConn->executeSql($xsql);
				//$xLog->add($xsql . "\r\n");
			} catch (\Simplon\Postgres\PostgresException $e){
				
				$xFRM->addAviso($e->getMessage());
				$xLog->add( $e->getMessage() );
				$xLog->add("ERROR\t$id\tNo se agrega el Domicilio$iddomicilio\r\n");
				
			}
		}
	}

	
}


$xFRM->addLog($xLog->getMessages());

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
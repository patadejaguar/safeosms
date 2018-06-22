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
	//$result = $dbConn->executeSql('');
	$result = $dbConn->executeSql('UPDATE "sucursal" SET "direccion_id" = NULL');
	$result = $dbConn->executeSql('DELETE FROM public.direccion WHERE id > 2');
}
//================================== principales activos
$arrPrincipales			= array();
//===================================
$sql 	= "SELECT   `socios_vivienda`.`idsocios_vivienda` AS `id`,
         `socios_vivienda`.`codigo_postal`,
         `socios_vivienda`.`calle` AS `nombre_acceso`,
         /*`socios_vivienda`.`numero_exterior`*/
         SUBSTR(`socios_vivienda`.`numero_exterior`,1,5) AS `numero_exterior`,
         /*`socios_vivienda`.`numero_interior`,*/
         SUBSTR(`socios_vivienda`.`numero_interior`,1,5) AS `numero_interior`,
         
'' AS `cruzamiento_1`,
'' AS `cruzamiento_2`,
         `socios_vivienda`.`telefono_residencial` AS `telefono_fijo`,
'' AS `extencion`,
`socios_vivienda`.`referencia`,

         IF(`socios_vivienda`.`principal` = '1', 'true', 'false') AS `principal`,
         getMigGetT2(`socios_vivienda`.`tipo_regimen`) AS `regimen_vivienda_id`,
         getMigGetT1(`socios_vivienda`.`tiempo_residencia`) AS `tiempo_residencia_id`,
         getMigGetT3(`socios_vivienda`.`tipo_de_acceso`) AS `tipo_acceso_id`,
         `socios_vivienda`.`socio_numero` AS `persona_id`,

         /*`socios_vivienda`.`clave_de_localidad` AS `localidad_id`,*/
1 AS `localidad_id`,

`socios_vivienda`.`colonia_id` AS `colonia_id`

FROM     `socios_vivienda` ";

$xQL->setConTitulos();

$rs			= $xQL->getDataRecord($sql);
$xT			= new cSocios_vivienda();
$arrTit		= $xQL->getTitulos();
foreach ($rs as $rw){
	//$idpersona	= $rw[$xT->CODIGO];
	$idpersona		= $rw["id"];
	$arrD			= array();
	
	$idpersona		= $rw["persona_id"];
	
	if($rw["principal"] == "true" AND isset( $arrPrincipales[ $idpersona] ) ){
		$rw["principal"] = "false";
		$xLog->add("WARN\t$idpersona\t\tPrincipal Duplicado\r\n");
	}
	if($rw["principal"] == "true"){
		$arrPrincipales[ $idpersona ]	= true;
	}
	
	
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
		$idx = $dbConn->insert('direccion', $arrD);
	} catch (\Simplon\Postgres\PostgresException $e){
		$xFRM->addAviso($e->getMessage());
		$xLog->add( $e->getMessage() . "\r\n" );
		//$xLog->add("ERROR\t$idcredito\tTipo de Credito a fallback\r\n");
		setLog($arrD);
	}
	
}

$xFRM->addLog($xLog->getMessages());

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
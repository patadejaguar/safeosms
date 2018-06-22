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
//===================================
$rs		= $xQL->getDataRecord("SELECT   `personas_perfil_transaccional`.*, `personas_perfil_transaccional_tipos`.`v_migrado`
FROM     `personas_perfil_transaccional` INNER JOIN `personas_perfil_transaccional_tipos`  ON `personas_perfil_transaccional`.`clave_de_tipo_de_perfil` = `personas_perfil_transaccional_tipos`.`idpersonas_perfil_transaccional_tipos`");
//$xT		= new cSocios_general();
$xT		= new cPersonas_perfil_transaccional();

$data	= array("origen_recurso" => "",
"monto_maximo_operaciones_mensuales" => "",
"numero_maximo_operaciones_mensuales" => "",
"observaciones" => "",
"activo" => "",
"pais_id" => "",
"aplicacion_recurso_id" => "",
"tipo_perfil_id" => "",
"persona_id" => "");

foreach ($rs as $rw){
	//$idpersona	= $rw[$xT->CODIGO];
	//$xSoc		= new cSocio($idpersona);
	//if($xSoc->init() == true){
	//$rw[$xT->]
		$data["origen_recurso"] 					= $rw[$xT->RECURSO_ORIGEN];
		$data["monto_maximo_operaciones_mensuales"] = $rw[$xT->CANTIDAD_MAXIMA];
		$data["numero_maximo_operaciones_mensuales"] = $rw[$xT->MAXIMO_DE_OPERACIONES];
		$data["observaciones"] 						= $rw[$xT->OBSERVACIONES];
		$data["activo"] 							= true;
		$data["pais_id"] 							= "153";
		$data["aplicacion_recurso_id"] 				= "1";
		$data["tipo_perfil_id"] 					= $rw["v_migrado"];
		$data["persona_id"]							= $rw[$xT->CLAVE_DE_PERSONA];
		
	//}
	
		try {
			$id = $dbConn->insert('perfil_transaccional', $data);
		} catch (\Simplon\Postgres\PostgresException $e){
			$xFRM->addAviso($e->getMessage());
			setLog($data);
		}
		
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
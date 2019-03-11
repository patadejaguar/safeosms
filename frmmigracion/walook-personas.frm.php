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
	$result = $dbConn->executeSql('DELETE FROM public.persona WHERE id>1000');
}

//===================================
$sql 	= "SELECT   `socios_general`.`codigo` AS `id`,
         /*`t_03f996214fba4a1d05a68b18fece8e71`.`f_28fb96d57b21090705cfdf8bc3445d2a` AS `created_by`,*/
	'admin' AS `created_by`,
         getMigFecha(`socios_general`.`fechaalta`) AS `created_date`,
         /*`t_03f996214fba4a1d05a68b18fece8e71`.`f_28fb96d57b21090705cfdf8bc3445d2a` AS `last_modified_by`,*/
	'admin' AS `last_modified_by`,
         getMigFecha(`socios_general`.`fechaalta`) AS `last_modified_date`,
                 
         IF(getMigFigJur(`personalidad_juridica`) = 'M', '', `socios_general`.`nombrecompleto`) AS `nombre`,
         `socios_general`.`apellidopaterno` AS `primer_apellido`, 
         `socios_general`.`apellidomaterno` AS `segundo_apellido`,
         IF(getMigFigJur(`personalidad_juridica`) = 'M', '', getMigGetGenero(`socios_general`.`genero`)) AS `genero`,
         `socios_general`.`fechanacimiento` AS `fecha_nacimiento`,
         `socios_general`.`correo_electronico`,
         `socios_general`.`lugarnacimiento` AS `lugar_nacimiento`,

         IF(getMigFigJur(`personalidad_juridica`) = 'M', '',`socios_general`.`titulo_personal`) AS `profesion`,
         IF(getMigFigJur(`personalidad_juridica`) = 'M', '',`socios_general`.`dependientes_economicos`) AS `dependientes`,
         IF(getMigFigJur(`personalidad_juridica`) = 'M', '',`socios_general`.`documento_de_identificacion`) AS `numero_documento`,
         IF(getMigFigJur(`personalidad_juridica`) = 'M', '',getUniqCURP(`socios_general`.`curp`)) AS `curp`,
         `socios_general`.`observaciones` AS `observacion`,
         IF(getMigFigJur(`personalidad_juridica`) = 'M', '',getMigEsPEP(`socios_general`.`codigo`))  AS `ocupo_cargo_publico`,
         IF(`socios_general`.`nacionalidad_extranjera` = 1, 'true', 'false') AS `nacionalidad_extranjera`,
         getMigEsEmp(`socios_general`.`codigo`)  AS `empresa_empleadora`,
         
         getUniqRFC(`socios_general`.`rfc`) AS `rfc`,
         `socios_general`.`clave_de_firma_electronica` AS `firma_electronica`,
         getMigRazFE(`socios_general`.`codigo`) AS `razones_no_firma`,

         IF(getMigFigJur(`personalidad_juridica`) = 'M', `socios_general`.`nombrecompleto`, '') AS `razon_social`,
         
         /*`socios_general`.`pais_de_origen` AS `pais_id`,*/
	153 AS `pais_id`,
         IF(getMigFigJur(`personalidad_juridica`) = 'M', '',getMigGetTipoId(`socios_general`.`tipo_de_identificacion`)) AS `tipo_identificacion_id`,
         getMigGetRegFis(`socios_general`.`regimen_fiscal`) AS `regimen_fiscal_id`,
         getMigEstadoID(`socios_general`.`codigo`) AS `estado_id`,
         
         `socios_general`.`telefono_principal` AS `telefono`,
         IF(`socios_general`.`estatusactual` = 20, 'false', 'true') AS `activo`,
         
         getMigGetRegMat(`socios_general`.`regimen_conyugal`) AS `regimen_matrimonial_id`,
         
         getMigFigJur(`personalidad_juridica`) AS `tipo_persona`,
         IF(getMigFigJur(`personalidad_juridica`) = 'F','', getMigNumeroActa(`socios_general`.`codigo`)) AS `numero_acta`,
         
         /*`socios_general`.`dependencia` AS `empresa_id`,*/
	IF(getMigFigJur(`personalidad_juridica`) = 'F','', getMigGetT4(`dependencia`)) AS `empresa_id`,
/*1 AS `empresa_id`,*/
         getMigGetEdoCiv(`socios_general`.`estadocivil`) AS `estado_civil_id`,
         /*`socios_general`.`sucursal` AS `sucursal_id`,*/
	1 AS `sucursal_id`,
	'' AS `carnet`,
	'' AS `fecha_residencia`,
	'' AS `fecha_vencimiento_carnet`,
	'false' AS `propiedades_en_extranjero`,
	'1' AS `cuenta_contable_id`,
	IF(getMigEsCliente(`socios_general`.`codigo`)='true', 'true', '') AS `cliente`,
         getMigGetRiskL(`nivel_de_riesgo_aml`) AS `nivel_riesgo_pld_id`,
         IF(getMigFigJur(`personalidad_juridica`) = 'M', `socios_general`.`nombrecompleto`, CONCAT(`socios_general`.`nombrecompleto`, ' ',`socios_general`.`apellidopaterno`,' ',`socios_general`.`apellidomaterno`)) AS `nombre_razon`,
         getMigEsCartJud(`socios_general`.`codigo`)  AS `ha_estado_en_cartera_judicial`
         
FROM     `socios_general` 

INNER JOIN `t_03f996214fba4a1d05a68b18fece8e71`  ON `socios_general`.`idusuario` = `t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` 

WHERE  `socios_general`.`codigo` >1 ";

$xQL->setConTitulos();

$rs			= $xQL->getDataRecord($sql);
$xT			= new cSocios_general();
$arrTit		= $xQL->getTitulos();

foreach ($rs as $rw){
	//$idpersona	= $rw[$xT->CODIGO];
	$idpersona		= $rw["id"];
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
		$idx = $dbConn->insert('persona', $arrD);
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
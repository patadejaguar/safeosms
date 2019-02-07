<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.2.03
 * @package common
 * Actualizacion
 * 		16/04/2008
 *		2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimeintos y recibos
 *
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP			= new cHPage("TR.Carga Automatizada de colonias");
$xFi			= new cFileImporter();
$xQL			= new MQL();
$xLis			= new cSQLListas();
$xF				= new cFecha();
$xLog			= new cCoreLog();
$xT				= new cTipos();


ini_set("max_execution_time", 600);

$fecha			= $xF->getFechaISO();

$xHP->init();

$xFRM			= new cHForm("frmcolonias", "colonias.upload.psql.frm.php");

$doc1			= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
$sieliminar		= parametro("ideliminar", false, MQL_BOOL);
$corregir		= parametro("idcorregir", false, MQL_BOOL);

$arrTC["PENDIENTE POR ACTUALIZAR"] = "1";
$arrTC["AEROPUERTO"] = "2";
$arrTC["BARRIO"] = "3";
$arrTC["BASE NAVAL"] = "4";
$arrTC["CAMPAMENTO"] = "5";
$arrTC["CENTRO"] = "6";
$arrTC["CENTRO URBANO"] = "7";
$arrTC["COLONIA"] = "8";
$arrTC["CONDOMINIO"] = "9";
$arrTC["CONGREGACION"] = "10";
$arrTC["CONJUNTO HABITACIONAL"] = "11";
$arrTC["CONJUNTO HABITACIONAL RESIDENCIAL"] = "12";
$arrTC["CONJUNTO HABITACIONAL URBANO"] = "13";
$arrTC["CONJUNTO POPULAR"] = "14";
$arrTC["CONJUNTO RESIDENCIAL"] = "15";
$arrTC["CONJUNTO URBANO"] = "16";
$arrTC["CONJUNTO URBANO POPULAR"] = "17";
$arrTC["EJIDO"] = "18";
$arrTC["ESCUELA"] = "19";
$arrTC["ESTACION DE RADIO"] = "20";
$arrTC["EX-HACIENDA"] = "21";
$arrTC["EX-RANCHO"] = "22";
$arrTC["FABRICA"] = "23";
$arrTC["FINCA"] = "24";
$arrTC["FRACCIONAMIENTO"] = "25";
$arrTC["FRACCIONAMIENTO INDUSTRIAL"] = "26";
$arrTC["FRACCIONAMIENTO RESIDENCIAL"] = "27";
$arrTC["GRANJA"] = "28";
$arrTC["HACIENDA"] = "29";
$arrTC["INGENIO"] = "30";
$arrTC["JUNTA AUXILIAR"] = "31";
$arrTC["MODULO HABITACIONAL"] = "32";
$arrTC["PARQUE"] = "33";
$arrTC["PARQUE INDUSTRIAL"] = "34";
$arrTC["CUADRILLA"] = "35";
$arrTC["POBLADO COMUNAL"] = "36";
$arrTC["PUEBLO"] = "37";
$arrTC["RANCHO O RANCHERIA"] = "38";
$arrTC["RESIDENCIAL"] = "39";
$arrTC["VILLA"] = "40";
$arrTC["UNIDAD HABITACIONAL"] = "41";
$arrTC["ZONA FEDERAL"] = "42";
$arrTC["ZONA HABITACIONAL"] = "43";
$arrTC["ZONA INDUSTRIAL"] = "44";
$arrTC["ZONA RESIDENCIAL"] = "45";
$arrTC["ZONA URBANA"] = "46";
$arrTC["ZONA URBANA EJIDAL"] = "47";
$arrTC["CAMPO MILITAR"] = "48";
$arrTC["VIVIENDA POPULAR"] = "49";
$arrTC["CLUB DE GOLF"] = "50";
$arrTC["COOPERATIVA"] = "51";
$arrTC["CIUDAD"] = "52";
$arrTC["OFICINA DE CORREOS"] = "53";
$arrTC["GRAN USUARIO"] = "54";
$arrTC["ZONA COMERCIAL"] = "55";
$arrTC["ZONA RURAL"] = "56";



$config = array(
		// required credentials
		
		'host'       => 'localhost',
		'user'       => 'gpd1601',
		'password'   => 'Gx450Ppadio',
		'database'   => 'gpd1601_dd',
		
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



class cTmp {
	public $CP		= 1;
	public $NOM		= 2;
	public $TIPO	= 3;
	public $EDO		= 5;
	public $MUN		= 4;
	public $ID_EDO	= 8;
	public $CIUDAD	= 6;
	public $ID_MUN	= 12; 
	/*$ncolonia		= $cT->cChar($datos[1]);
	$tcolonia		= $cT->cChar($datos[2]);
	$estado			= $cT->cChar($datos[4]);
	$municipio		= $cT->cChar($datos[3]);
	
	$numEstado		= $cT->cInt($datos[7]);
		
	$ciudad			= $cT->cChar($datos[5]);*/	
}
$tmp			= new cTmp();
$mFecha			= $xF->get();
$mSucursal		= getSucursal();
$xFx			= new cFileLog("lista-colonias-", true);
$xFcp			= new cFileLog("lista-cp-", true);
$xTi			= new cTipos();

$initCP			= 32500;
$initCol		= 150000;

$sql1			= 'SELECT x."id" FROM "public"."codigo_postal" x WHERE x."codigo_postal"= :idcodigopostal ';
$sql2			= 'SELECT x."id" FROM "public"."colonia" x WHERE "codigo_postal_id"= :idcodigopostal AND (("nombre"= :idcrc32) OR ("nombre"= :idcrc33) ) OFFSET 1';
$sql2			= 'SELECT   "public"."colonia"."id" FROM "colonia" INNER JOIN "codigo_postal"  ON "colonia"."codigo_postal_id" = "codigo_postal"."id" 
WHERE ( "public"."codigo_postal"."codigo_postal" = :idcodigopostal ) AND (( "public"."colonia"."nombre" = :idcrc32 ) OR ( "public"."colonia"."nombre" = :idcrc33 ))';
if($doc1 == false){
	$xFRM->setTitle($xHP->getTitle());
	$xFRM->OFileText("idarchivo", "TR.Archivo a Importar");
	$xFRM->OCheck("TR.Eliminar Datos", "ideliminar");
	$xFRM->OCheck("TR.Corregir Datos", "idcorregir");
	
	$xFRM->addGuardar();
} else {
	if($sieliminar == true){
		//$xQL->setRawQuery("DELETE FROM general_colonias");
	}
	$xFi->setType("txt");
	$xFi->setCharDelimiter("|");
	$xFi->setLimitCampos(15);
	
	$xFi->setToUTF8();
	$xFi->setProbarMB();
	//$xFi->setModoRAW();
	
	
	$sqlBuilder = new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
	
	if($xFi->processFile($doc1) == true){
		$data		= $xFi->getData();
		$conteo		= 1;
		$res		= false;
		foreach ($data as $rows){
			$xFi->setDataRow($rows);
			 $cp		= $xFi->getEntero($tmp->CP);
			 $nombre	= $xFi->getV($tmp->NOM);

			 /*public $CP		= 1;
			 public $NOM		= 2;
			 public $TIPO	= 3;
			 public $EDO		= 5;
			 public $MUN		= 4;
			 public $ID_EDO	= 8;
			 public $CIUDAD	= 6;
			 public $ID_MUN	= 12;*/ 
			 
			 
			 if($cp > 0 AND $nombre != ""){
			 	$xCol		= new cGeneral_colonias();
			 	$nnombre	= strtoupper($nombre); // $rows[$tmp->NOM];
			 	
			 	$estado		= $xFi->getEntero($tmp->ID_EDO);
			 	$municipio	= $xFi->getEntero($tmp->ID_MUN);
			 	$tipo		= strtolower($xFi->getV($tmp->TIPO));
			 	$tipo2		= strtoupper($xFi->getV($tmp->TIPO));
			 	$tipo3		= $xFi->getV($tmp->TIPO);
			 	//validar si existe el codigo postal
			 	
			 	$sqlBuilder->setQuery($sql1)->setConditions(array('idcodigopostal' => "$cp"));
			 	$idcp		= $pgSqlManager->fetchColumn($sqlBuilder);
			 	//--Si existe
			 	if($idcp > 0){
			 		//$xLog->add("ERROR\tAl agregar el CP $cp -- $nombre --\r\n");
			 		$idcrc	= crc32($nnombre);
			 		
			 		$sqlBuilder->setQuery($sql2)->setConditions(array('idcodigopostal' => "$cp", 'idcrc32' => $nnombre, 'idcrc33' => $nombre ) );
			 		$idcolonia		= $pgSqlManager->fetchColumn($sqlBuilder);
			 		
			 		//setLog($sqlBuilder->setQuery($sql2)->getConditionsQuery());
			 		
			 		if($idcolonia > 0){
			 			//$xLog->add("OK\tLa colonia $idcolonia existe $cp -- $nombre or $nnombre -- con ID CP $idcp\r\n");
			 		} else {
			 			$arrC							= array();
			 			$arrC["id"]						= $initCol;
			 			$arrC["nombre"]					= $nombre;
			 			$arrC["fecha_revision"]			= $fecha;
			 			$arrC["tipo_colonia"]			= $tipo;
			 			$arrC["codigo_postal_id"]		= $idcp;
			 			$arrC["activo"]					= "t";
			 			$arrC["tipo_asentamiento_id"]	= "1";
			 			
			 			if(isset($arrTC[$tipo2])){
			 				$arrC["tipo_asentamiento_id"] = $arrTC[$tipo2];
			 			}
			 			
			 			try {
			 				$idcol 				= $dbConn->insert('colonia', $arrC);
			 				$res				= true;
			 				$xFx->setWrite(implode("|", $arrC) . "\r\n");
			 				$xLog->add("OK\tSe agrega la colonia $nombre -$cp-$initCol- relacionado con idCP $idcp ---  Estado: $estado Municipio $municipio\r\n");
			 				$initCol++;
			 			} catch (\Simplon\Postgres\PostgresException $e){
			 				$xLog->add($e->getMessage());
			 				
			 			}
			 		}
			 		
			 	} else {
			 	//--No existe
			 		//$xLog->add("WARN\tNo existe el CP y Colonia $cp -- $nombre --\r\n");
			 		//
			 		$arrI	= array();
			 		$arrI["id"]						= $initCP; 
			 		$arrI["codigo_postal"]			= $xTi->cSerial(5, $cp);
			 		$arrI["estado_id"]				= $estado;
			 		$arrI["clave_mpio"]				= $municipio;
			 		$arrI["nivel_riesgo_pld_id"]	= 1;
			 		
			 		//Insertar Colonia
			 		try {
			 			$idcpN 				= $dbConn->insert('codigo_postal', $arrI);
			 			$xFcp->setWrite(implode("|", $arrI) . "\r\n");
			 			$xLog->add("OK\tSe agrega el CP $cp con nuevo id $idcpN o $initCP ----  Estado: $estado Municipio $municipio\r\n");
			 			$initCP++;
			 		} catch (\Simplon\Postgres\PostgresException $e){
			 			$xLog->add($e->getMessage());
			 		}
			 		if($idcpN > 0){
			 			
			 			$arrC							= array();
			 			
			 			$arrC["id"]						= $initCol;
			 			$arrC["nombre"]					= $nombre;
			 			$arrC["fecha_revision"]			= $fecha;
			 			$arrC["tipo_colonia"]			= $tipo;
			 			$arrC["codigo_postal_id"]		= $idcpN;
			 			$arrC["activo"]					= true;
			 			$arrC["tipo_asentamiento_id"]	= "1";
			 			
			 			if(isset($arrTC[$tipo2])){
			 				$arrC["tipo_asentamiento_id"] = $arrTC[$tipo2];
			 			}
			 			
			 			try {
			 				$idcol 				= $dbConn->insert('colonia', $arrC);
			 				$res				= true;
			 				$xFx->setWrite(implode("|", $arrC) . "\r\n");
			 				$xLog->add("OK\tSe agrega la colonia nueva $nombre -$cp-$initCol- relacionado con idCP nuevo $idcpN o $initCP --- Estado: $estado Municipio $municipio\r\n");
			 				$initCol++;
			 			} catch (\Simplon\Postgres\PostgresException $e){
			 				$xLog->add($e->getMessage());
			 				
			 			}
			 		}
			 		/*INSERT INTO public.colonia
			 		 
			 		(nombre, fecha_revision, tipo_colonia, codigo_postal_id, activo, tipo_asentamiento_id)
			 		
			 		VALUES('', '', '', 0, false, 0);*/
			 		
			 		
			 	}
			 	
			 	/*$sqlBuilder 					= new \Simplon\Postgres\Manager\PgSqlQueryBuilder();
			 	$sqlBuilder->setQuery($sqlPlanDePago)->setConditions(array('idcredito' => $idcredito));
			 	$idplandepago					= $pgSqlManager->fetchColumn($sqlBuilder);*/
			 	
			 	/*$xCol->ciudad_colonia( $xFi->getV($tmp->CIUDAD, "") );
			 	$xCol->codigo_de_estado($xFi->getEntero($tmp->ID_EDO) );
			 	$xCol->codigo_de_municipio($xFi->getEntero($tmp->ID_MUN) );
			 	$xCol->codigo_postal($cp);
			 	$xCol->estado_colonia($xFi->getV($tmp->EDO, ""));
			 	$xCol->fecha_de_revision($mFecha);
			 	$xCol->municipio_colonia($xFi->getV($tmp->MUN, ""));
			 	$xCol->nombre_colonia($nombre);
			 	$xCol->sucursal($sucursal);
			 	$xCol->tipo_colonia( $xT->setNoAcentos($rows[2]) );
			 	$xCol->idgeneral_colonia( $xCol->query()->getLastID() );
			 	$res = $xCol->query()->insert()->save();*/
			 	
			 	if($res == false){
			 		//$xLog->add("ERROR\tAl agregar el CP $cp  -$nombre-\r\n");
			 	} else {
			 		//$xLog->add("OK\tSe agrega el CP $cp con Nombre $nombre\r\n");
			 	}
			 } else {
				$xLog->add("ERROR\tAl agregar el CP $cp -- $nombre --\r\n");
			}
		}
	} else { 
		$xLog->add($xFi->getMessages(), $xLog->DEVELOPER);
	}
	
	$xFx->setClose();
	$xFcp->setClose();
	
	$xFRM->addHElem( $xFx->getLinkDownload("Archivo Colonias") );
	$xFRM->addHElem( $xFcp->getLinkDownload("Archivo CP") );
	
	if($corregir == true){
		//$xQL->setRawQuery("CALL `proc_colonias_activas`");
		//$xQL->setRawQuery("CALL `sp_correcciones`()");
	}
	$xFRM->addLog($xLog->getMessages());
}

echo $xFRM->get();

$xHP->fin();

?>

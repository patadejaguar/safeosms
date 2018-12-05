<?php
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
	include_once("../vendor/autoload.php");
	
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_SERVICE);
$xQL		= new MQL();
//$xLi		= new cSQLListas();
$xF			= new cFecha();
$xHSel		= new cHSelect();
$xTT		= new cTipos();

//$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$clave			= parametro("id", "", MQL_RAW); $clave		= parametro("clave", $clave, MQL_RAW);
$tipo			= parametro("tipo", "", MQL_RAW);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action = parametro("cmd", $action);

$action			= strtolower($action);

$xSVC			= new MQLService($action, "");

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos $action";

$xDB			= new cCouchDB();

if($action == SYS_NINGUNO){


$xDB->getCnn();
//var_dump($xDB->getCnn()); exit;
//$cc		= $xDB->cnn();

//===== Dump productos de Credito ======
$sql1	= $xHSel->getListaDeProductosDeCredito("", false, true)->getSQL();
$rs1	= $xQL->getRecordset($sql1);
foreach ($rs1 as $rw){
	$xObj				= new stdClass();
	$xObj->_id			= "creditos_productos:". $rw["idcreditos_tipoconvenio"];
	$xObj->clave		= $rw["idcreditos_tipoconvenio"];
	$xObj->descripcion	= $rw["descripcion"];
	$xObj->tabla		= "creditos_productos";

	$xDB->setDoc($xObj);
}

//===== Dump productos de Credito ======
$sql1	= $xHSel->getListaDePeriocidadDePago()->getSQL();
$rs1	= $xQL->getRecordset($sql1);
foreach ($rs1 as $rw){
	$xObj				= new stdClass();
	$xObj->_id			= "creditos_frecuencia:". $rw["idcreditos_periocidadpagos"];
	$xObj->clave		= $rw["idcreditos_periocidadpagos"];
	$xObj->descripcion	= $rw["descripcion_periocidadpagos"];
	$xObj->tabla		= "creditos_frecuencia";
	
	$xDB->setDoc($xObj);
}
//===== Dump Destinos de Credito ======
$sql1	= $xHSel->getListaDeDestinosDeCredito()->getSQL();
$rs1	= $xQL->getRecordset($sql1);
foreach ($rs1 as $rw){
	$xObj				= new stdClass();
	$xObj->_id			= "creditos_destinos:". $rw["idcreditos_destinos"];
	$xObj->clave		= $rw["idcreditos_destinos"];
	$xObj->descripcion	= $rw["destino"];
	$xObj->tabla		= "creditos_destinos";
	
	$xDB->setDoc($xObj);
}

//===== Dump de tipos de Documentos.
$rs1	= $xQL->getRecordset("SELECT `clave_de_control`,`nombre_del_documento`,`clasificacion`,`tags` FROM `personas_documentacion_tipos` WHERE `estatus`=1 AND (`tags` LIKE '%pf%' OR `clasificacion` = 'IP')");
foreach ($rs1 as $rw){
	$xObj				= new stdClass();
	$xObj->_id			= "documentos_tipos:". $rw["clave_de_control"];
	$xObj->clave		= $rw["clave_de_control"];
	$xObj->descripcion	= $rw["nombre_del_documento"];
	$xObj->tabla		= "documentos_tipos";
	$xObj->tags			= $rw["tags"];
	$xObj->clasificacion= $rw["clasificacion"];
	
	$xDB->setDoc($xObj);
}
/*var MTipoDeDocumentos = {
	clave : "",
	nombre : "",
	clasificacion : "",
	tags : ""
}*/

/*
var MTipoDeIdentificacion = {
 clave : "",
 nombre : "",
 clasificacion : "",
 tags : "",
 tabla : ""
 }
 */

//===== Dump de tipos de Identificacion.
$rs1	= $xQL->getRecordset("SELECT `clave_de_control`,`nombre_del_documento`,`clasificacion`,`tags` FROM `personas_documentacion_tipos` WHERE `estatus`=1 AND `clasificacion` = 'IP'");
foreach ($rs1 as $rw){
	$xObj				= new stdClass();
	$xObj->_id			= "identificacion_tipos:". $rw["clave_de_control"];
	$xObj->clave		= $rw["clave_de_control"];
	$xObj->descripcion	= $rw["nombre_del_documento"];
	$xObj->tabla		= "identificacion_tipos";
	$xObj->tags			= $rw["tags"];
	$xObj->clasificacion= $rw["clasificacion"];
	
	$xDB->setDoc($xObj);
}

//===== Dump productos de Usuarios ======

$rs2	= $xQL->getRecordset("SELECT   `t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios`,
         `t_03f996214fba4a1d05a68b18fece8e71`.`f_28fb96d57b21090705cfdf8bc3445d2a`,
         `t_03f996214fba4a1d05a68b18fece8e71`.`alias`,
         `t_03f996214fba4a1d05a68b18fece8e71`.`pin_app`,
         `t_03f996214fba4a1d05a68b18fece8e71`.`sucursal`,
         `t_03f996214fba4a1d05a68b18fece8e71`.`f_f2cd801e90b78ef4dc673a4659c1482d`
FROM     `t_03f996214fba4a1d05a68b18fece8e71`
WHERE    ( `t_03f996214fba4a1d05a68b18fece8e71`.`estatus` = 'activo' )");
$xT		= new cT_03f996214fba4a1d05a68b18fece8e71();
foreach ($rs2 as $rw){
	$xObj				= new stdClass();
	$xObj->_id			= "usuarios:" . $rw[$xT->F_28FB96D57B21090705CFDF8BC3445D2A];
	$xObj->clave		= $rw[$xT->IDUSUARIOS];
	$xObj->nombre		= $rw[$xT->F_28FB96D57B21090705CFDF8BC3445D2A];
	$xObj->pin			= $rw[$xT->PIN_APP];
	$xObj->sucursal		= $rw[$xT->SUCURSAL];
	$xObj->nivel		= $rw[$xT->F_F2CD801E90B78EF4DC673A4659C1482D];
	$xObj->alias		= $rw[$xT->ALIAS];
	$xObj->tabla		= "usuarios";
	$xDB->setDoc($xObj);
}

//===== Dump de Avisos para los usuarios
$maxFSync	= $xF->setRestarDias(7, fechasys() );
$rs7	= $xQL->getRecordset("SELECT * FROM `usuarios_web_notas` WHERE `estado`=10 AND `fecha`>='$maxFSync' ");
$xT		= new cUsuarios_web_notas();

foreach ($rs7 as $rw){
	$xT->setData($rw);
	
	$xObj				= new stdClass();
	$xObj->_id			= "mensajes:". $rw[$xT->IDUSUARIOS_WEB_NOTAS];
	$xObj->clave		= $rw[$xT->IDUSUARIOS_WEB_NOTAS];
	$xObj->tabla		= "mensajes";
	$xObj->user			= $rw[$xT->OFICIAL];
	$xUsr				= new cSystemUser($rw[$xT->OFICIAL_DE_ORIGEN]);
	$xUsr->init();
	
	$xObj->oficial_org	= $xUsr->getAlias();
	$xObj->mensaje		= $rw[$xT->TEXTO];
	$xObj->entidad		= EACP_CLAVE_CASFIN;
	$xObj->fecha		=$rw[$xT->FECHA];
	
	$xDB->setDoc($xObj);
}


$rs["message"]			= $xDB->getMessages();
$rs["message"]			.= "OK\tSync Terminado\r\n";
//$rs		= $xQL->get
//$rs		= $cc->getAllDocs();
$rs["error"]	= false;


} else {
	if($tipo == "personas"){
		$xF				= new cFecha();
		$xImp			= $xDB->getDoc($clave);

		$nombre			= $xImp->nombre;
		$primerapellido	= $xImp->primerapellido;
		$segundoapellido= $xImp->segundoapellido;
		$rfc			= "";
		$curp			= $xImp->idpoblacional;
		$fechanac		= $xImp->fechanacimiento;
		$lugarnac		= "";
		$tipoingreso	= TIPO_INGRESO_CLIENTE;
		$estadocivil	= DEFAULT_ESTADO_CIVIL;
		$genero			= ($xImp->genero == "F") ? 2 : 1;
		$clavedecentro	= DEFAULT_CAJA_LOCAL;
		$figuraJur		= PERSONAS_FIGURA_FISICA;
		$observaciones	= "";
		$tipoidentificacion	=  $xTT->cInt($xImp->idtipoidentificacion);//FALLBACK_PERSONAS_TIPO_IDENTIFICACION;
		$datosIdent		= $xImp->claveidentificacion;
		$codigo			= false;
		$sucursal		= $xImp->sucursal;
		$telefono		= $xImp->telefono;
		$email			= $xImp->email;
		$fecha			= false;
		$ocupacion		= $xImp->ae_descripcion;
		$fechanac		= strtotime($xImp->fechanacimiento);
		$fechanac		= $xF->getFechaByInt($fechanac);
		$usuarioOrigen	= $xTT->cInt($xImp->user);
		$xSoc			= new cSocio(false);
		$xPer			= new cSocios_general();
		//$xSoc->setOmitirAML(true);
		$stat			= $xSoc->add($nombre, $primerapellido, $segundoapellido, $rfc, $curp, $clavedecentro, $fechanac, $lugarnac, $tipoingreso, $estadocivil, $genero, FALLBACK_CLAVE_EMPRESA,
				DEFAULT_REGIMEN_CONYUGAL, $figuraJur, DEFAULT_GRUPO, $observaciones, $tipoidentificacion, $datosIdent, $codigo, $sucursal, $telefono, $email,0, $fecha, AML_PERSONA_BAJO_RIESGO, "",
				EACP_CLAVE_DE_PAIS, DEFAULT_REGIMEN_FISCAL, $ocupacion);
		if($stat == true){
			$regimen		= false;
			$tipovivienda	= false;
			$xP				= new cPersonaActividadEconomica();
			
			$xSoc->addVivienda($xImp->vivienda_calle, $xImp->vivienda_numero, $xImp->vivienda_codigopostal, "", $xImp->vivienda_referencia, 0, 0, true, $regimen, $tipovivienda,DEFAULT_TIEMPO, $xImp->vivienda_colonia);
			$ingreso		= setNoMenorQueCero($xImp->ae_ingresomensual);
			$fechaingreso	= strtotime($xImp->ae_fechaingreso);
			$fechaingreso	= $xF->getFechaByInt($fechaingreso);
			$tiempo			= $xP->getTiempoPorFecha($fechaingreso);
			//$xAE			= new cPersonaActividadEconomica();
			//$xAE->add($clave_de_actividad, $ingreso)
			$xSoc->addActividadEconomica($xImp->ae_empresa, $ingreso, $xImp->ae_descripcion, $fechaingreso);
			//$xSoc->setUpdate($aParam)
			//Importar Documentos
			$DDocs			= $xDB->getDoctosByIdInterno($xImp->_id);
			$idpersona		= $xSoc->getClaveDePersona();
			
			foreach ($DDocs as $odoc){
				//setLog(print_r($odoc, true));
				//setLog($odoc->id);
				//exit;
				$res		= $xDB->setImporDoctoByIDInterno($odoc->id, $idpersona);
				
			}
			//Actualizar usuario origen
			if($usuarioOrigen>0){
				$mArr			= array();
				$mArr[$xPer->IDUSUARIO]	= $usuarioOrigen; 
				$xSoc->setUpdate($mArr, true);
			}
			//Importar Credito
			$xCred			= new cCredito(false, $idpersona);
			$producto		= $xImp->producto;
			$monto			= $xImp->monto;
			$periocidad		= $xImp->frecuencia;
			$pagos			= $xImp->pagos;
			$destino		= $xImp->cred_destino;
			
			if($monto > TOLERANCIA_SALDOS){
				$xCred->add($producto, $idpersona, false, $monto, $periocidad, $pagos, false, $destino);
				$rs["message"] .= $xCred->getMessages();
			} else {
				$rs["message"] .= "ERROR\tNo se importa al Credito\r\n"; 
			}
			
			
			$rs["error"]	= false;
			
			//Actualizar ID de Entidad Registro
			$idinterno1		= setNoMenorQueCero($xImp->idtemp->entidad1);
			$idinterno2		= setNoMenorQueCero($xImp->idtemp->entidad2);
			
			if(SVC_VIEW_COUCHDB == $xDB->SYNC_VISTA1){
				$xImp->idtemp->entidad1	= $xSoc->getClaveDePersona();
			} else {
				$xImp->idtemp->entidad2	= $xSoc->getClaveDePersona();
			}
			$xDB->setDoc($xImp);
			$rs["persona"]	= $xSoc->getClaveDePersona();
		}
		
		$rs["message"]		.= $xSoc->getMessages();
		$rs["message"]		.= $xDB->getMessages();
		
	/*  public '_id' => string 'preclientes:patadejaguar@gmail.com:1516665558' (length=45)

  public 'user' => int 0
  public 'tabla' => string 'preclientes' (length=11)
  public 'vivienda_calle' => string 'Benito Juarez' (length=13)
  public 'vivienda_numero' => string '451' (length=3)
  public 'vivienda_referencia' => string 'Entre Pololo y Metros' (length=21)
  public 'vivienda_codigopostal' => int 97206
  public 'vivienda_coordinadas' => int 0
  public 'vivienda_colonia' => string 'Juarez' (length=6)
  public 'idtemp_relacionado' => string '' (length=0)
  public 'id_relacionado' => int 0
  
  public 'pagos' => int 24
  public 'frecuencia' => string '30' (length=2)
  public 'producto' => string '2012' (length=4)
  public 'monto' => int 45000
  public 'fecha' => int 0
  
  public 'ae_ingresomensual' => int 4500
  public 'ae_empresa' => string 'La empresa de Prueba' (length=20)
  public 'ae_descripcion' => string 'Actividad de Prueba' (length=19)
  public 'ae_fechaingreso' => string '2018-01-24T06:00:00.000Z' (length=24)
  public 'vivienda_coordenadas' => string '21.003895999999997,-89.6170257' (length=30)	*/
		/*if($primerapellido != "" OR $segundoapellido != "" OR $nombre != ""){
			$resultado = $xSoc->add($nombre, $primerapellido, $segundoapellido, $rfc, $curp, $clavedecentro, $fechanac, $lugarnac, DEFAULT_TIPO_INGRESO, $estadocivil, $genero, FALLBACK_CLAVE_EMPRESA,
					DEFAULT_REGIMEN_CONYUGAL, $figuraJur, DEFAULT_GRUPO, $observaciones, $tipoidentificacion, $datosIdent, $codigo, $sucursal, $telefono, $email,0, $fecha, AML_PERSONA_BAJO_RIESGO, "",
					EACP_CLAVE_DE_PAIS, DEFAULT_REGIMEN_FISCAL, $ocupacion);*/
	}
}

header('Content-type: application/json');
echo json_encode($rs);
?>
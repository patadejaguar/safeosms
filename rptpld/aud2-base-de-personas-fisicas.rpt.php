<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP		= new cHPage("TR.REPORTE DE PLD PERSONAS FISICAS", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT * FROM socios LIMIT 0,100";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
//$xT		= new cTabla($sql, 2);
//$xT->setTipoSalida($out);
ini_set("max_execution_time", 1600);
		

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

$xCats			= new cCatalogoDeDatos("");

$DActividades	= $xCats->get("clave_interna", "nombre_de_la_actividad", "personas_actividad_economica_tipos"); 
$DGenero		= $xCats->get("idsocios_genero", "descripcion_genero", "socios_genero");
$DPaises		= $xCats->initPorTabla(TCATALOGOS_PAISES);
$DRiesgo		= $xCats->initPorTabla(TCATALOGOS_GRADO_RIESGO);
//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$sql		= $xL->getInicialDePersonas() . " WHERE (`fechaalta`<='$FechaFinal') AND (`personalidad_juridica` != 2 AND `personalidad_juridica` != 5)";

$xRPT->setSQL($sql);
$rs			= $query->getDataRecord($sql);
$xTa		= new cHTabla();
$xDSoc		= new cSocios_general();
$xF			= new cFecha();

$xTa->initRow();
$xTa->addTH("Sucursal");
$xTa->addTH("Primer Apellido");
$xTa->addTH("Segundo Apellido");
$xTa->addTH("Nombre");
$xTa->addTH("Genero");
$xTa->addTH("Fecha de Nacimiento");
$xTa->addTH("Pais de Nacimiento");
$xTa->addTH("Lugar de Nacimiento");
$xTa->addTH("Nacionalidad");
$xTa->addTH("RFC");
$xTa->addTH("Actividad economica");
$xTa->addTH("Puesto o Ocupacion");
$xTa->addTH("Direccion");
$xTa->addTH("Telefono Principal");

$xTa->addTH("Es PEP");
$xTa->addTH("Lista Negra");
$xTa->addTH("Grado de Riesgo");
$xTa->addTH("Servicios");
$xTa->addTH("Monto Otorgado");
$xTa->addTH("Monto Actual");
$xTa->addTH("Perfil Transaccional");
$xTa->addTH("Numero Maximo de Operaciones");
$xTa->addTH("Monto Maximo de Operaciones");
$xTa->endRow();


$xT			= new cFileImporter();
$xClean		= new cTiposLimpiadores();

foreach ($rs as $rows){
	$xDSoc->setData($rows);
	$codigo_de_socio	= $xDSoc->codigo()->v();
	$xSoc				= new cSocio($codigo_de_socio);
	$xSoc->init($rows);
	$xSoc->getOEstats()->initDatosDeCredito(true);
	$saldoCred	= setNoMenorQueCero($xSoc->getCreditosComprometidos());

	//setLog("Creditos de $saldoCred en persona $codigo_de_socio");
	if($saldoCred > 0){
			
		
		$xTa->initRow();
		
		$xTa->addTD( $xSoc->getSucursal() );
		$xTa->addTD( htmlentities($xSoc->getApellidoPaterno()) );
		$xTa->addTD( htmlentities($xSoc->getApellidoMaterno()) );
		$xTa->addTD( $xSoc->getNombre() );
		$genero		= (isset($DGenero[$xSoc->getGenero()])) ? $DGenero[$xSoc->getGenero()] : "";
		$xTa->addTD( $genero );
	
		$xTa->addTD( $xF->getFechaMX( $xSoc->getFechaDeNacimiento() ) );
		$pais		= (isset($DPaises[$xSoc->getPaisDeOrigen()])) ? $DPaises[$xSoc->getPaisDeOrigen()] : "";
		$xTa->addTD( htmlentities($pais) );
		$xTa->addTD( htmlentities($xSoc->getLugarDeNacimiento()) );
		$xTa->addTD("MEXICANA");
		$xTa->addTD( $xSoc->getRFC() );
		$xOAE		= $xSoc->getOActividadEconomica();
		if($xOAE == null){
			$xTa->addTD(" ");
			$xTa->addTD(" ");
		} else {
			$idclave	= $xOAE->getClaveDeActividad();
			$actividad	= (isset($DActividades[$idclave])) ? htmlentities( $DActividades[$idclave] ): "";
			$actividad	= strtoupper($xT->cleanString($actividad));
			$actividad	= $xClean->cleanEmpleo($actividad);
			$actividad	= ($actividad == "") ? "EMPLEADO" : $actividad;
			$xTa->addTD( $actividad );
			$xTa->addTD( $xOAE->getPuesto(true, $actividad) );
		}
		$xViv		= $xSoc->getODomicilio();
		if($xViv == null){
			$xTa->addTD(" ");
		} else {
			$xTa->addTD( htmlentities($xViv->getDireccionBasica()) );
		}
		$xTa->addTD( $xSoc->getTelefonoPrincipal() );
		/*$xTa->addTH("Perfil Transaccional");*/
		$esPep		= ($xSoc->getEsPersonaPoliticamenteExpuesta() == false) ? "NO" : "SI"; 
		$xTa->addTD($esPep);
		$esSDN		= ($xSoc->getEsPersonaSDN() == false) ? "NO" : "SI";
		$xTa->addTD($esSDN);
		$griesgo	= (isset($DRiesgo[$xSoc->getNivelDeRiesgo()])) ? $DRiesgo[$xSoc->getNivelDeRiesgo()] : "";
		$xTa->addTD($griesgo);
		
	
		if($saldoCred > 0){
			$xTa->addTD("CREDITO");
			$xTa->addTD($xSoc->getOEstats()->getTotalCreditosActivosAutorizado());
			$xTa->addTD($saldoCred);
		} else {
			$xTa->addTD(" ");
			$xTa->addTD(" ");
			$xTa->addTD(" ");		
		}
		/*	`personas_perfil_transaccional`.`clave_de_tipo_de_perfil`         AS `tipo`,
					`personas_perfil_transaccional_tipos`.`nombre_del_perfil`         AS `perfil`,
					`personas_domicilios_paises`.`nombre_oficial`                     AS `pais`,
					`personas_perfil_transaccional`.`maximo_de_operaciones`           AS `numero`,
					`personas_perfil_transaccional`.`cantidad_maxima`                 AS `monto`*/
		$psql	= $xL->getListadoDePerfil($codigo_de_socio);
		$rsql	= $query->getDataRecord($psql);
		foreach ($rsql as $rperfil){
			$xTa->addTD( $rperfil["perfil"] );
			$xTa->addTD( $rperfil["numero"] );
			$xTa->addTD( $rperfil["monto"] );
			
		}
		unset($rsql);
		//perfil transaccional
		//$xAml	= new cAMLPersonas($codigo_de_socio);
		//$xAml->init($codigo_de_socio, $rows);
		//$xAml->getPerfilDeRiesgo();
		//$xTa->addTD(
				
		$xTa->endRow();
	}
}
unset($rs);
$xRPT->addContent( $xTa->get() );
//$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>
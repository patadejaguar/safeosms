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
$xHP		= new cHPage("TR.REPORTE DE PERSONAS", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();
$xUtils		= new cPersonasUtilerias();

$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);
$cajalocal		= parametro("cajalocal", SYS_TODAS ,MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false, MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false, MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);
$soloclientes	= parametro("soloclientes", false, MQL_BOOL);

$ByActivos		= "";
if($soloclientes == true){
	if(MODULO_CAPTACION_ACTIVADO == true){
		$ByActivos	= " AND ((`tmp_personas_estadisticas`.`creditos`>0) OR (`tmp_personas_estadisticas`.`cuentas`>0)) ";
	} else {
		//$ByActivos	= "  AND (`tmp_personas_estadisticas`.`creditos`>0) ";
		$ByActivos	= "  AND (`tmp_personas_estadisticas`.`creditos_con_saldo`>0) ";
	}
}


$ByCL			= $xFil->PersonasPorCajaLocal($cajalocal);
$ByEstatus		= $xFil->PersonasPorEstado($estatus);
$ByEmpresa		= $xFil->PersonasPorEmpresa($empresa);
$BySucursal		= $xFil->PersonasPorSucursal($sucursal);


$FEmpresa		= (PERSONAS_CONTROLAR_POR_EMPRESA == true) ? "`socios_aeconomica_dependencias`.`nombre_corto` AS `empresa`," : "";
$FGrupo			= (PERSONAS_CONTROLAR_POR_GRUPO == true) ? "`socios_grupossolidarios`.`nombre_gruposolidario`          AS `grupo_solidario`," : "";

$sql			= "SELECT SQL_CACHE
					`socios_general`.`codigo`,
					`socios_general`.`apellidopaterno`                         AS `apellido_paterno`,
					`socios_general`.`apellidomaterno`                         AS `apellido_materno`,
					`socios_general`.`nombrecompleto`                          AS `nombre`,
					`socios_general`.`sucursal`                       		   AS `sucursal`,
					`socios_general`.`correo_electronico`,`socios_general`.`telefono_principal`,
					`tmp_personas_domicilios`.`domicilio`						AS `domicilio`,
					`tmp_colonias_activas`.`nombre_estado` AS `estado`,
					`tmp_colonias_activas`.`nombre_municipio` AS `municipio`,
					`socios_genero`.`descripcion_genero`                     	AS `genero`,
					getFechaMX(`socios_general`.`fechanacimiento`) 						AS `fecha_de_nacimiento`,

					$FEmpresa
					$FGrupo
											
					`socios_general`.`curp`,
									
									
					`socios_estatus`.`nombre_estatus`                         	AS `estatusactivo`,
					`socios_estadocivil`.`descripcion_estadocivil`            	AS `ESTATUS_CIVIL`,
					`tmp_personas_estadisticas`.`ingreso_mensual`				AS `ingreso_mensual`,
					
					`tmp_personas_estadisticas`.`inf_creditos`					AS `cartera`,
					`socios_general`.`dependientes_economicos`,
					`tmp_personas_estadisticas`.`num_refpersonales`				AS `REFPERS`
		
FROM     `socios_general` 
INNER JOIN `socios_tipoingreso`  ON `socios_general`.`tipoingreso` = `socios_tipoingreso`.`idsocios_tipoingreso` 
INNER JOIN `socios_genero`  ON `socios_general`.`genero` = `socios_genero`.`idsocios_genero` 
INNER JOIN `usuarios`  ON `socios_general`.`idusuario` = `usuarios`.`idusuarios` 
INNER JOIN `socios_figura_juridica`  ON `socios_general`.`personalidad_juridica` = `socios_figura_juridica`.`idsocios_figura_juridica` 
LEFT OUTER JOIN `socios_aeconomica_dependencias`  ON `socios_general`.`dependencia` = `socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` 
LEFT OUTER JOIN `socios_grupossolidarios`  ON `socios_general`.`grupo_solidario` = `socios_grupossolidarios`.`idsocios_grupossolidarios` 
INNER JOIN `personas_documentacion_tipos`  ON `socios_general`.`tipo_de_identificacion` = `personas_documentacion_tipos`.`clave_de_control` 
INNER JOIN `socios_estadocivil`  ON `socios_general`.`estadocivil` = `socios_estadocivil`.`idsocios_estadocivil` 
INNER JOIN `socios_estatus`  ON `socios_general`.`estatusactual` = `socios_estatus`.`tipo_estatus` 
RIGHT OUTER JOIN `tmp_personas_estadisticas`  ON `tmp_personas_estadisticas`.`persona` = `socios_general`.`codigo` 
LEFT OUTER JOIN `tmp_personas_domicilios`  ON `socios_general`.`codigo` = `tmp_personas_domicilios`.`codigo` 
LEFT OUTER JOIN `tmp_colonias_activas`  ON `tmp_personas_domicilios`.`idcodigopostal` = `tmp_colonias_activas`.`codigo_postal` 

										WHERE
										(`socios_general`.`codigo` != " . DEFAULT_SOCIO . " )
										$ByCL $ByEstatus $ByEmpresa $BySucursal $ByActivos
									ORDER BY
										`socios_general`.`sucursal`,
										`socios_general`.`codigo`";
//setLog($sql);

$titulo			= "";
$archivo		= "";

$xUtils->setConstruirEstadisticas();

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);

$xRPT->setPreSQL("CALL `proc_personas_domicilios`()");

$xRPT->setTitle($xHP->getTitle());
//============ Reporte


/*$xT		= new cTabla($sql, 0);
$xT->setTipoSalida($out);
$xT->setColTitle("dependientes_economicos", "DEPENDIENTES_ECONOMICOS");*/

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);

$xRPT->setBodyMail($body);
$xRPT->addContent($body);

//$xRPT->setPreSQL("CALL `sp_personas_estadisticas`();");

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );

$xRPT->setColTitle("dependientes_economicos", "DEPENDIENTES_ECONOMICOS");
$xRPT->setColTitle("correo_electronico", "CORREO_ELECTRONICO");

$xRPT->addCampoContar("codigo");

//$xRPT->addCampoSuma("codigo");
$xRPT->addCampoSuma("dependientes_economicos");
$xRPT->addCampoSuma("REFPERS");



$xRPT->setFormato("ingreso_mensual", $xRPT->FMT_MONEDA);

$xRPT->setProcessSQL();

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
/*
// Listen for clicks on table originating from .delete element(s)
$("table").on("click", ".delete", function ( event ) {
    // Get index of parent TD among its siblings (add one for nth-child)
    var ndx = $(this).parent().index() + 1;
    // Find all TD elements with the same index
    $("td", event.delegateTarget).remove(":nth-child(" + ndx + ")");
}); 
 * */

?>

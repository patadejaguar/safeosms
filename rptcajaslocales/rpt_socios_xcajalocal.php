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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();


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

$ByCL			= $xFil->PersonasPorCajaLocal($cajalocal);
$ByEstatus		= $xFil->PersonasPorEstado($estatus);
$ByEmpresa		= $xFil->PersonasPorEmpresa($empresa);
$BySucursal		= $xFil->PersonasPorSucursal($sucursal);

$FEmpresa		= (PERSONAS_CONTROLAR_POR_EMPRESA == true) ? "`socios_aeconomica_dependencias`.`descripcion_dependencia` AS `empresa`," : "";
$FGrupo			= (PERSONAS_CONTROLAR_POR_GRUPO == true) ? "`socios_grupossolidarios`.`nombre_gruposolidario`          AS `grupo_solidario`," : "";
$sql			= "SELECT SQL_CACHE
									`socios_general`.`codigo`,

									`socios_general`.`apellidopaterno`                         AS `apellido_paterno`,
									`socios_general`.`apellidomaterno`                         AS `apellido_materno`,

									`socios_general`.`nombrecompleto`                          AS `nombre`,
									
									$FEmpresa
									$FGrupo
											
									`socios_general`.`curp`,
									`socios_genero`.`descripcion_genero`                     	AS `genero`,
									
									`socios_estatus`.`nombre_estatus`                         	AS `estatus`,
									`socios_estadocivil`.`descripcion_estadocivil`            	AS `ESTATUS_CIVIL`,
		
									`socios_general`.`fechanacimiento` 						AS `fecha_de_nacimiento` 

								FROM
									`socios_general` `socios_general`
										LEFT OUTER JOIN `socios_grupossolidarios` `socios_grupossolidarios`
										ON `socios_general`.`grupo_solidario` = `socios_grupossolidarios`.
										`idsocios_grupossolidarios`
											LEFT OUTER JOIN `socios_genero` `socios_genero`
											ON `socios_general`.`genero` = `socios_genero`.`idsocios_genero`
												LEFT OUTER JOIN `socios_estadocivil` `socios_estadocivil`
												ON `socios_general`.`estadocivil` = `socios_estadocivil`.
												`idsocios_estadocivil`
													LEFT OUTER JOIN `socios_aeconomica_dependencias`
													`socios_aeconomica_dependencias`
													ON `socios_general`.`dependencia` =
													`socios_aeconomica_dependencias`.
													`idsocios_aeconomica_dependencias`
														LEFT OUTER JOIN `socios_estatus` `socios_estatus`
														ON `socios_general`.`estatusactual` = `socios_estatus`.
														`tipo_estatus`
										WHERE
										(`socios_general`.`codigo` != " . DEFAULT_SOCIO . " )
										$ByCL $ByEstatus $ByEmpresa $BySucursal
									ORDER BY
										`socios_general`.`cajalocal`,
										`socios_general`.`codigo`";
setLog($sql);
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 0);
$xT->setTipoSalida($out);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

exit;
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

	$ide 		= $_GET["pa4"];			//Estatus
	$p3 		= $_GET["pa3"];			//Caja Local
	$out 		= $_GET["out"];			//Salida

	$ByCL		= " WHERE (`socios_cajalocal`.`idsocios_cajalocal` = $p3 ) ";
	$ByEstatus	= " AND (`socios_general`.`estatusactual` = $ide)  ";

	if ( $ide == "todas" ){
		$ByEstatus	= "";
	}
	if (  $p3 == "todas" ){
		$ByCL		= "";
	}


$oficial 	= elusuario($iduser);
$fieldset	= true;
if ( $out !=  OUT_EXCEL ){
$fieldset	= false;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Listado de Personas</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<!-- -->

<?php
echo getRawHeader();

echo "<p class='bigtitle'>SOCIOS INTEGRANTES DE LA CAJA LOCAL NUMERO $p3</p>
		<hr />";
} else {
	//ES EXCEL
	$filename = $_SERVER['SCRIPT_NAME'];
	$filename = str_replace(".php", "", $filename);
	$filename = str_replace("rpt", "", $filename);
	$filename = str_replace("-", "", 	$filename);
  	$filename = "$filename-" . date("YmdHi") . "-from-" .  $iduser . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
}
//tabla de Cajas Locales. . " WHERE socios_cajalocal.idsocios_cajalocal=$rw[0]"
	$sqlSoc 	= "SELECT
						`socios_cajalocal`.*,
						`socios_region`.*,
						`socios_cajalocal`.`idsocios_cajalocal`
					FROM
						`socios_cajalocal` `socios_cajalocal`
							INNER JOIN `socios_region` `socios_region`
							ON `socios_cajalocal`.`region` = `socios_region`.`idsocios_region`
					$ByCL
					";
	$rs 		= mysql_query($sqlSoc, cnnGeneral() );

		while($rw = mysql_fetch_array($rs)) {
			$cl = new cCajaLocal($rw["idsocios_cajalocal"]);
			$cl->init($rw);
			echo $cl->getFicha($fieldset);
				$cajalocal		= $rw["idsocios_cajalocal"];
				$sqlComplete 	= "SELECT SQL_CACHE
									`socios_general`.`codigo`,

									`socios_general`.`apellidopaterno`                         AS
									`apellido_paterno`,
									`socios_general`.`apellidomaterno`                         AS
									`apellido_materno`,

									`socios_general`.`nombrecompleto`                          AS `nombre`,

									`socios_general`.`curp`,
									`socios_genero`.`descripcion_genero`                       AS `genero`,
									`socios_aeconomica_dependencias`.`descripcion_dependencia` AS `dependencia`,
									`socios_estatus`.`nombre_estatus`                          AS `estatus`,
									`socios_estadocivil`.`descripcion_estadocivil`             AS `estado_civil`
									,
									`socios_grupossolidarios`.`nombre_gruposolidario`          AS
									`grupo_solidario`

								FROM
									`socios_general` `socios_general`
										LEFT OUTER JOIN `socios_grupossolidarios` `socios_grupossolidarios`
										ON `socios_general`.`grupo_solidario` = `socios_grupossolidarios`.
										`idsocios_grupossolidarios`
											LEFT OUTER JOIN `socios_genero` `socios_genero`
											ON `socios_general`.`genero` = `socios_genero`.`idsocios_genero`
												LEFT OUTER JOIN `socios_estadocivil` `socios_estadocivil`
												ON `socios_general`.`estadocivil` = `socios_estadocivil`.
												`idsocios_estadocivil`
													LEFT OUTER JOIN `socios_aeconomica_dependencias`
													`socios_aeconomica_dependencias`
													ON `socios_general`.`dependencia` =
													`socios_aeconomica_dependencias`.
													`idsocios_aeconomica_dependencias`
														LEFT OUTER JOIN `socios_estatus` `socios_estatus`
														ON `socios_general`.`estatusactual` = `socios_estatus`.
														`tipo_estatus`
										WHERE
										(`socios_general`.`cajalocal` = $cajalocal )
										$ByEstatus
									ORDER BY
										`socios_general`.`cajalocal`,
										`socios_general`.`codigo`
										";

				$ctbl = new cTabla($sqlComplete, 0);
				
				$ctbl->setTdClassByType();
				$ctbl->Show("", false);
				//$sumSoc	= $ctbl->getFieldsSum();
				$regs	= $ctbl->getRowCount();
				echo "<table width='100%'>
						<tr>
						<th>CLAVE DE PERSONAS</th>
						<th>$regs</th>
						</tr>
						</table>
				";
				
		}

if ( $out != OUT_EXCEL ){
echo getRawFooter();
?>
</body>
</html>
<?php
}
?>

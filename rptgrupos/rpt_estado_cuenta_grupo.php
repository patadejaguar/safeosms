<?php
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
//=====================================================================================================
$xHP			= new cHPage("TR.TR.Estado_de_cuenta de Grupo ", HP_REPORT);
$xL				= new cSQLListas();
$xF				= new cFecha();
$query			= new MQL();

$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$grupo			= parametro("id", $grupo, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT numero_socio, numero_solicitud FROM creditos_solicitud WHERE grupo_asociado=$grupo LIMIT 1,10";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
$xG = new cGrupo($grupo);
$xRPT->addContent($xG->getFicha());
$rs		= $query->getDataRecord($sql);
foreach ($rs as $rows){
	$credito		= $rows["numero_solicitud"];
	$xCred			= new cCredito($credito);
	$xRPT->addContent( $xCred->getFicha(false, "", false, true) );
}
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);


exit;

include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.common.inc.php";

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="javascript:window.print();">
<!-- -->
<p class="bigtitle">ESTADO DE CUENTA DE CREDITOS X GRUPOS SOLIDARIOS</p>
<?php
echo getRawHeader();

$id = $_GET["id"];

	//
	$sqlcred = "SELECT numero_socio, numero_solicitud FROM creditos_solicitud WHERE grupo_asociado=$id LIMIT 1,10";
	//echo $sqlcred; exit;

	
	$mycred = mysql_query($sqlcred);
		while($rwc = mysql_fetch_array($mycred)) {
			echo " <hr /> ";
			minificha(2, $rwc[1]);
			//$G = new cGrupo($rwc[""])
			//echo " <hr /> ";
			$sqlmvto = $sqlb18b . " AND docto_afectado=$rwc[1] AND estatus_mvto=30
							AND valor_afectacion!=0
							ORDER BY fecha_operacion, tipo_operacion, fecha_afectacion";
			$cTbl = new cTabla($sqlmvto);
			$cTbl->setWidth();
			$cTbl->setTdClassByType();
			$cTbl->Show("", false);
		}
	@mysql_free_result($mycred);

echo getRawFooter();
?>
</body>
</html>

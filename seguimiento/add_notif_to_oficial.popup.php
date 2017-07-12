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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.seguimiento.inc.php");
include_once("../core/core.riesgo.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial 	= elusuario($iduser);
$jxc 		= new TinyAjax();

function jsaSetEvent($numero, $socio, $solicitud, $grupo, $fecha, $hora, $observaciones, $oficial ){
setNuevaNotificacion($socio, $solicitud, $grupo = 99, $numero, $fecha, $hora, $observaciones, $oficial);

return "Se Agrego la Notificacion $numero al Socio $socio por el Credito $solicitud";
}

$i			= $_GET["i"];	//control id
$g			= $_GET["g"];	//Grupo
$t			= $_GET["t"];	//numero de notificacion


$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$s			= $persona;
$c			= $credito;

$oficial 	= elusuario($iduser);


$jxc ->exportFunction('jsaSetEvent', array('idnumero', "idsocio", "idsolicitud", "idgrupo", "idDateValue", "idHora", "idobservaciones", "idOficial"), "#idMsg");
$jxc ->process();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Selector de Reportes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
$jxc ->drawJavaScript(false, true);
?>
<link rel="stylesheet" type="text/css" media="all" href="../utils/jscalendar/skins/aqua/theme.css" title="Aqua" />
<!-- import the calendar script -->
<script type="text/javascript" src="../utils/jscalendar/calendar.js"></script>
<!-- import the language module -->
<script type="text/javascript" src="../utils/jscalendar/lang/calendar-es.js"></script>

<body onload="jsInit(); showFlatCalendar()">
<form name="frmAddEvent" method="POST" action="./">
<input type="hidden" id="idDateValue" value="<?php echo $fecha; ?>" />
<input type="hidden" id="idsocio" value="<?php echo $persona; ?>" />
<input type="hidden" id="idsolicitud" value="<?php echo $credito; ?>" />
<input type="hidden" id="idgrupo" value="<?php echo $grupo; ?>" />
<input type="hidden" id="idnumero" value="<?php echo $t; ?>" />
<fieldset>
	<legend>Agregar <?php echo $t; ?></legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Oficial Asignado</td>
				<td colspan='2'><?php
								$sqlTO = "SELECT id, nombre_completo FROM oficiales WHERE estatus='activo' ";

								$xTO = new cSelect("cOficial", "idOficial", $sqlTO);
								$xTO->setEsSql();
								$xTO->setOptionSelect($iduser);
								$xTO->show(false);
				?></td>
		</tr>
		<tr>
			<td>Hora</td>
			<td>Observaciones</td>
		</tr>
		<tr>
			<td><?php
				$xh = new cFecha(0);
				echo $xh->getSelectHour(false, "cHora", "idHora");
			?>
			</td>
			<td rowspan="3">
				<textarea id="idobservaciones" cols="30" rows="10" ></textarea>
				<br />
				<a class="button" onclick="jsaSetEvent(); setTimeout('window.close()', 1000)" >Guardar Notificacion <?php echo $t; ?></a>
				<p class="aviso" id="idMsg"></p>
			</td>
		</tr>
		<tr>
			<td>Fecha</td>
		</tr>
		<tr>
			<td><div id="display-cal"></div>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
</body>
<script  >
var MINUTE 	= 60 * 1000;
var HOUR 	= 60 * MINUTE;
var DAY 	= 24 * HOUR;
var WEEK 	= 7  * DAY;

function resizeMainWindow(){
	var mWidth	= 512;
	var mHeight	= 480;
	window.resizeTo(mWidth, mHeight);
}
function jsInit(){
	resizeMainWindow();
}
function showFlatCalendar() {
  var parent = document.getElementById("display-cal");
  var cal = new Calendar(0, null, flatSelected);

  cal.weekNumbers = false;
  cal.setDisabledHandler(isDisabled);
  //cal.setDateFormat("%A, %B %e");
  cal.setDateFormat("%Y-%m-%d");
  cal.create(parent);

  // ... we can show it here.
  cal.show();
}
function flatSelected(cal, date) {
	document.getElementById("idDateValue").value = date;
}
function isDisabled(date) {
  var today = new Date();
  return (Math.abs(date.getTime() - today.getTime()) / DAY) > 45;
}
</script>
</html>
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

$oficial = elusuario($iduser);
//require_once(TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");	
//$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Reporte Auxiliar de Polizas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true); 
?>
<body onload="initComponents()">

<form name="frmPolizasReport" method="post" action="">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0'  >
		<tbody>
		<tr>
  <td >

		<fieldset>
			<legend>Movimientos</legend>
			Del: <?php echo ctrl_date(0); ?> <br /> <br />
			 Al: <?php echo ctrl_date(1); ?>
		</fieldset>

		<fieldset>
			<legend>Forma</legend>
			Mostrar:
			<select name="cComoMostrar" id="idComoMostrar" size="3">
				<option value="detalle" selected>A Detalle</option>
				<option value="encabezados">Solo Encabezados</option>
				<option value="en_mayor">Como Mayor Afectado</option>
			</select>
			<br />

		</fieldset>
	</td>
  <td >
	<fieldset>
		<fieldset>
			<legend>Numeros</legend>
			Del: <input type='text' name='cNumeroInicial' value='0' id="iNumeroInicial" size='6' /> <br /> <br />
			 Al: <input type='text' name='cNumeroFinal' value='999999' id="iNumeroFinal" size='6' />
		</fieldset>
	<br />
	Tipos de Polizas:<br />
				<?php 	
					$cTPol = new cSelect("ctipopolizas", "idtipopolizas", "contable_polizasdiarios");
					$cTPol->addEspOption("todas", "Todas");
					$cTPol->setOptionSelect("todas");
					$cTPol->show(false);
			 ?>		
	</fieldset>

	</td>
		</tr>
		<tr>
			<th colspan="2"><input type="button" name="cmExecReport" onclick="execReport()"  value="Ver/Imprimir Reporte"/></th>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
</body>
<script  >
var mFRM = document.frmPolizasReport;
function execReport(){
	
	var mMostrar	= document.getElementById("idComoMostrar").value;
	var mTipo		= document.getElementById("idtipopolizas").value;
	var mFolios		= document.getElementById("iNumeroInicial").value + "|" + document.getElementById("iNumeroFinal").value;
	var dInicial	= mFRM.elanno0.value + "-" + mFRM.elmes0.value + "-" + mFRM.eldia0.value;
	var dFinal		= mFRM.elanno1.value + "-" + mFRM.elmes1.value + "-" + mFRM.eldia1.value;
	var mFecha		= dInicial + "|" + dFinal;
	var mRPT		= "./rpt_auxiliar_de_polizas.php?f=" + mFecha + "&n=" + mFolios + "&v=" + mMostrar + "&t=" + mTipo;
	
		var mIDWin = Math.random();
		var windowName = "myWin" + mIDWin;
		var winLeft		= parseInt(screen.width * 0.10);
	    var winTop		= parseInt((screen.height * 0.10) + 100);
		var winHeight	= parseInt((screen.height - (screen.height * 0.10) ) - 100);
		var winWidth	= parseInt((screen.width - (screen.width * 0.10)));
			
        var windowFeatures = "width=" + winWidth + ",height=" + winHeight + ",status,scrollbars,resizable,left=" + winLeft + ",top=" + winTop 
        newWindow = window.open(mRPT, windowName, windowFeatures);
		newWindow.focus();	
}
function resizeMainWindow(){
	var mWidth	= 520;
	var mHeight	= 390;
	window.resizeTo(mWidth, mHeight);	
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}
</script>
</html>
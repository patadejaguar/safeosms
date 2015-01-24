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
$xHP		= new cHPage("TR.Balanza de Comprobacion", HP_FORM);
$xHP->init();

$xSel		= new cHSelect();
$xHRPT		= new cPanelDeReportesContables(false, false);
$xHRPT->addFechaInicial();
$xHRPT->addFechaFinal();
$xHRPT->addCuentaInicial();
$xHRPT->addCuentaFinal();
$xHRPT->addTipoDeCuentas();
//$xHRPT->addNivelesDeCuentas();
$xHRPT->addEstadoDeMovimiento();

$xHRPT->addMoneda();

echo $xHRPT->render();
?>
<script>
var xG 	= new Gen();
function jsGetReporte(){
	var mFechaF		= $("#idfechafinal").val();
	var mFechaI		= $("#idfechainicial").val();
	var vMone		= $("#idcodigodemoneda").val();
	var vctainit	= $("#idcuentainicial").val();
	var vctafin		= $("#idcuentafinal").val();
	var vtipo		= $("#idtipodecuentacontable").val();
	//var vnivel		= $("#idniveldecuenta").val();
	var edomvto		= $("#idestadomvto").val();
	
	var urlrpt 		= "rpt_movimientos_de_auxiliares2.php?on=" + mFechaI + '&off=' + mFechaF + '&moneda=' + vMone + '&for=' + vctainit + '&to=' + vctafin + "&tipo=" + vtipo + "&estado=" + edomvto ;
	xG.w({ url : urlrpt });
}
</script>
<?php
$xHP->fin();
exit;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
echo jsBasicContable("frmrportauxiliares");
?>
<body onload="initComponents();">
<form name="frmrportauxiliares" method="post" action="">
<table  >
  <tbody>
  <tr>
  <td colspan="2">
	<fieldset>
		<fieldset>
			<legend>Movimientos</legend>
			Del: <input type='text' name='cFechaInicial' value='<?php echo "01-" . date("m-Y"); ?>' id="idFechaInicial" onchange="setFechaF_MX('idFechaInicial');" />
			 Al: <input type='text' name='cFechaFinal' value='<?php echo date("t-m-Y"); ?>' id="idFechaFinal" onchange="setFechaF_MX('idFechaFinal');" />
		</fieldset>
	</fieldset>
	</td>
	</tr>
    <tr>
      <td>




      <td>
		<fieldset>
			De la :<br />
			<input type='text' name='ci' value='<?php echo ZERO_EXO; ?>' id="idci" size="35" onchange="getCuentaFmt('idci');" />
			<input type="button" name="cmd1" id="idcmd1" value="..." onclick="mostrar_catalogo('idcmd1', 'idci');" /> <br />
			A la:<br />
			<input type='text' name='cf' value='<?php echo ZERO_EXO; ?>' id="idcf" size="35" onchange="getCuentaFmt('idcf');" onfocus="FSendIDtoID('idci', 'idcf');" />
			<input type="button" name="cmd2" id="idcmd2" value="..." onclick="mostrar_catalogo('idcmd2', 'idcf');" />
		</fieldset>
      </td>
    </tr>
    <tr>
    	<td colspan="2">
			<table style=""
 				     >
			<tbody>
				<tr>
					<!-- <td><img src="../images/common/tnuevo.png" id="tnuevo" onclick="cmdTNuevo(event);" /></td> -->
					<td><img src="../images/common/taceptar.png" id="taceptar" onclick="cmdTAceptar(event);" /></td>
					<!-- <td><img src="../images/common/teliminar.png" id="teliminar" onclick="cmdTEliminar(event);" /></td>
					<td><img src="../images/common/timprimir.png" id="timprimir" onclick="cmdTImprimir(event);" /></td> -->
				</tr>
			</tbody>
			</table>
    	</td>
    </tr>
  </tbody>
</table>

</form>
</body>
<script  >
function cmdTEliminar(evt){
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function cmdTNuevo(evt){
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function cmdTAceptar(evt){
	open_rpt_x_date();
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function cmdTImprimir(evt){
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function imgAsClicked(sId){

	mImg = document.getElementById(sId);

	sPATHImg = "<?php echo vIMG_PATH; ?>";
	var HayCadena = sId;
	var PATHImg = mImg.src;

	if(onAsClicked == true){
		if(PATHImg.indexOf("_")!= -1){
			var sNImg = HayCadena.replace("_", HayCadena);
			mImg.src = sPATHImg + "/common/" + sNImg + ".png"
			onAsClicked = false;
		} else {
			mImg.src = sPATHImg + "/common/" + HayCadena + "_.png"
			onAsClicked = true;
			setTimeout("imgAsClicked('" + HayCadena + "')", 100);
		}
	}
}
/**
**********************************
*/
var wfrm = document.frmreports;

function open_rpt_x_date() {
		//control de opciones
		var vf71 = 0;		//Frecuencia
		var vf70 = 0;		//Estatus
		var vf72 = 0;		//Convenio
		var vf73 = 0;		//Fechas
		/*if(wfrm.sifrecuencia.checked){
			vf71 = 1;
		}
		if(wfrm.siestatus.checked){
			vf70 = 1;
		}
		if(wfrm.siconvenio.checked){
			vf72 = 1;
		}
		if(wfrm.sifechas.checked){
			vf73 = 1;
		} */

		var sFechaInicial = document.getElementById("idFechaInicial").value;
		var sFechaFinal = document.getElementById("idFechaFinal").value;

		var DInicial = sFechaInicial.split("-");
		var DFinal = sFechaFinal.split("-");
		var anno0 = DInicial[2];
		var mes0 = DInicial[1];
		var dia0 = DInicial[0];
		var fi = new Date(anno0, mes0, dia0);
		//
		var anno1 = DFinal[2];
		var mes1 = DFinal[1];
		var dia1 = DFinal[0];
		var ff = new Date(anno1, mes1, dia1);
		//
		vfor = document.getElementById("idci").value;
		vto = document.getElementById("idcf").value;
		vf1 = document.getElementById("idtipocuentas").value;
		vf2 = document.getElementById("idecuentas").value;
		vf3 = 0;
		vOut = 0;

		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;

			var urlrpt = "rpt_movimientos_de_auxiliares2.php?" + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&out=' + vOut + '&f1=' + vf1 + '&f2=' + vf2;
				prep = window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars");
				prep.focus();
		}
}
function mostrar_catalogo(idCMD, idCTRL){
	var mBtn = document.getElementById(idCMD);
	//posicionar el Catalogo

	oTop = parseInt(mBtn.offsetParent.offsetTop) + 20;
	oLeft = mBtn.offsetLeft;
        var windowFeatures 	= "width=640,height=280,status,scrollbars,resizable,left=" + oLeft + ",top=" + oTop
        var windowName 		= "winCatalogo";
        var wmCtrl			= idCTRL;
        var mFile = "../contabilidad/frm_show_catalogo.php?c=" + wmCtrl + "&l=" + oLeft + "&t=" + oTop;
        CatWindow = window.open(mFile, windowName, windowFeatures);
		CatWindow.focus();
}
function resizeMainWindow(){
	var mWidth	= 640;
	var mHeight	= 320;
	window.resizeTo(mWidth, mHeight);
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}
</script>
</html>

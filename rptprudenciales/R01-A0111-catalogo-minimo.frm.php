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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.contable.inc.php";

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
echo jsBasicContable("");
?>
<body onload="initComponents();">
<form name="frmBalanceGeneral" method="post" action="">
<fieldset>
<legend>Reporte Regulatorio: R01 A0111 - Catalogo Minimo</legend>
<table  >
	<tbody>
		<tr>
			<td>
			<fieldset>
			<legend>Periodo</legend>
				<table border='0'  >
					<tr>
						<td>Ejercicio</td><td><input type='text' name='ejercicio' id="idejercicio" value='<?php echo date("Y"); ?>'></td>
					</tr>
					<tr>
						<td>Periodo</td><td><select name="periodo" id="idperiodo">
												<?php echo getMesesInSelectOptions(); ?>
											</select></td>
					</tr>
				</table>
	<!-- comparativo Real vs Presupuesto -->
	<!-- en Presentacion Especial UDIS/Pesos/Dolares -->
	</fieldset>
	<fieldset>
		<legend>Presentacion</legend>
				<table border='0'  >
					<tr>
						<td>Moneda</td>
						<td><select name="cMoneda" id="idMoneda">
								<option value="pesos">PESOS</option>
								<option value="miles">MILES DE PESOS</option>
								<option value="udis">UDIS</option>
								<option value="pesos">OTROS</option>
							</select></td>
					</tr>
					<tr>
						<td>Valoraci&oacute;n</td>
						<td><input type='text' name='cFactor' id="idFactor" value='0'></td>
					</tr>					
				</table>
	</fieldset>
			</td>
		</tr>
    <tr>
      <td>
		<fieldset>
			<legend>Tipo de Cuentas</legend>
			Tipo de Cuentas :<br />
			<?php 	$cCta = new cSelect("tipocuentas", "", "contable_catalogotipos");
					$cCta->addEspOption("algunas", "Algunas");
					$cCta->addEspOption("todas", "Todas");
					$cCta->addEspOption("cuadre", "Cuadre");
					$cCta->setOptionSelect("todas");
					$cCta->show(false);
			 ?>
			<br />
			Cuentas:<br />
			<select name="ecuentas" id="idecuentas">
				<option value="todas" selected>Todas</option>
				<!-- <option value="con_movimientos">Con Movimientos</option>		-->
				<option value="saldo_no_cero">Con Saldos Diferentes a Cero</option>
				<option value="saldo_no_cero_con_mvtos">Con Movimientos y Saldo Diferentes a Cero</option>
				<option value="saldo_no_cero_o_mvtos">Con Movimientos o Saldo Diferentes a Cero</option>
			</select>
		</fieldset>    
      </td>
</tr>
<tr>
      
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
					<td><img src="../images/common/taceptar.png" id="taceptar" onclick="cmdTAceptar(event);" /></td>
					<!--<td><img src="../images/common/timprimir.png" id="timprimir" onclick="cmdTImprimir(event);" /></td> -->
				</tr>
			</tbody>
			</table>    	
    	</td>
    </tr>
	</tbody>
</table>
</fieldset>
</form>

</body>
<script  >
function cmdTAceptar(evt){
	showReport();
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
function showReport(){
	var vEjercicio	= document.getElementById("idejercicio").value;
	var vPeriodo 	= document.getElementById("idperiodo").value;
	var vMoneda		= document.getElementById("idMoneda").value;
	var vFactor		= document.getElementById("idFactor").value;
	var mPer 		= vPeriodo;
	var mEjer 		= vEjercicio;
	var vCInit		= document.getElementById("idci").value;
	var vCFin		= document.getElementById("idcf").value;
	var	vf1 = document.getElementById("idtipocuentas").value;
	var	vf2 = document.getElementById("idecuentas").value;
			
	var urlrpt = "R01-A0111-catalogo-minimo.rpt.php?p=" + mPer + '&e=' + mEjer + '&m=' + vMoneda + '&f=' + vFactor +
				'&for=' + vCInit + '&to=' + vCFin + '&f1=' + vf1 + '&f2=' + vf2;
	var prep = window.open(urlrpt, "","width=800,height=600,resizable,scrollbars");
		prep.focus();	
}
function resizeMainWindow(){
	var mWidth	= 384;
	var mHeight	= 512;
	window.resizeTo(mWidth, mHeight);	
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}
function mostrar_catalogo(idCMD, idCTRL){
	var mBtn = document.getElementById(idCMD);
	//posicionar el Catalogo

	oTop = parseInt(mBtn.offsetParent.offsetTop) + 20;
	oLeft = mBtn.offsetLeft;
        var windowFeatures 	= "width=640,height=280,status,scrollbars,resizable,left=" + oLeft + ",top=" + oTop 
        var windowName 		= "winCatalogo";
        var wmCtrl			= idCTRL;
        var mFile = "../rptprudenciales/R01-A0111-catalogo-minimo.rpt.php?c=" + wmCtrl + "&l=" + oLeft + "&t=" + oTop;
        var CatWindow = window.open(mFile, windowName, windowFeatures);
		CatWindow.focus();	
}
</script>
</html>

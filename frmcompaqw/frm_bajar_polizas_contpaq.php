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
include_once("../core/core.common.inc.php");

$oficial = elusuario($iduser);
//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");	
//$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true); 
?>
<body onload="initComponents()">
<?php
$commad 		=		$_GET["c"];
if (!isset($commad)){
?>
<form name="frmOutVauchers" method="post" action="frm_bajar_polizas_contpaq.php?c=e">
<fieldset>
	<legend>Exportar Polizas al ContPaQ</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td colspan='2'>
				<fieldset>
					<legend>Fecha de Polizas</legend>
					Del: <?php echo ctrl_date(0); ?> <br /> <br />
					 Al: <?php echo ctrl_date(1); ?>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td><fieldset>
			Tipos de Polizas:<br />
				<?php 	
					$cTPol = new cSelect("ctipopolizas", "idtipopolizas", "contable_polizasdiarios");
					$cTPol->addEspOption("todas", "Todas");
					$cTPol->setOptionSelect("todas");
					$cTPol->show(false);
			 ?>		
		</fieldset></td>
		<td>
		<fieldset>
			<legend>Numeros</legend>
			Del: <input type='text' name='cNumeroInicial' value='0' id="iNumeroInicial" size='4' /> <br /> <br />
			 Al: <input type='text' name='cNumeroFinal' value='999999' id="iNumeroFinal" size='4' />
		</fieldset>		
		</td>
		</tr>
		
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><a class="boton" onclick="frmOutVauchers.submit();">- Iniciar Exportaci&oacute;n -</a></td>
			</tr>
		</tfoot>
	</table>
</fieldset>
</form>
<?php
} elseif($commad == "e") {
	$sucursal		= getSucursal();
	//Traducciones del ContPaqw
	$CWTipoMvto 	= array("1"=>1,"-1"=>"2");
	
	//Formato	:	polizas + fecha + sucursal;
	
	$mTmpFileAlias	= "$sucursal-polizas-" . date("Y-m-d") . "";
	$mNametmpFile 	= PATH_TMP . $mTmpFileAlias . ".txt";
	
	if(file_exists($mNametmpFile)) {
		$BKPFile = fopen($mNametmpFile, "a+");
	} else {
		//$mNametmpFile = tempnam (PATH_BACKUPS, "polizas" . date("Y-m-d") . $sucursal . ".sbk");
		$BKPFile = fopen($mNametmpFile, "a");
	}
	//filtros
	$wByTipo			= ( $_POST["ctipopolizas"] == "todas" ) ? "" : " AND (`contable_polizas`.`tipopoliza` =" . $_POST["ctipopolizas"] . ")  ";
	
	//Generar Polizas
	$fecha_inicial		= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
	$fecha_final		= $_POST["elanno1"] . "-" . $_POST["elmes1"] . "-" . $_POST["eldia1"];
	$FInicial			= $_POST["cNumeroInicial"];
	$FFinal				= $_POST["cNumeroFinal"];
	
	$sqlPol 			= "SELECT
						*
						FROM
							`contable_polizas` `contable_polizas` 
						WHERE
							(`contable_polizas`.`fecha` >='$fecha_inicial')
							AND
							(`contable_polizas`.`fecha` <='$fecha_final')
							AND
							(
							(`contable_polizas`.`numeropoliza` >=$FInicial) 
							AND
							(`contable_polizas`.`numeropoliza` <=$FFinal) 
							) $wByTipo ";
	//echo $sqlPol;
	
	$rs = mysql_query($sqlPol, cnnGeneral());
		if (!$rs){
			//Codigo de Control de Error
			saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|||Numero: " .mysql_errno() . "|||Instruccion SQL: \n ". $sqlPol);
		}
		while($rw = mysql_fetch_array($rs)){
			//Seleccionar los Movimientos
			$ejercicio			= $rw["ejercicio"];
			$periodo			= $rw["periodo"];
			$tipoPoliza			= $rw["tipopoliza"];
			$numeroPoliza		= $rw["numeropoliza"];
			$fechaPoliza		= $rw["fecha"];
			$conceptoPoliza		= $rw["concepto"];
			
			$WriteText	= "P " . date("Ymd", strtotime($fechaPoliza));
			$WriteText .= " " . $tipoPoliza;
			$WriteText .= " " . substr(str_pad($numeroPoliza, 8, "0", STR_PAD_LEFT), -8);
			$WriteText .= " 1 000 " . substr(str_pad($conceptoPoliza, 100, " " , STR_PAD_RIGHT),0, 100);
			$WriteText .= " 01 2 
";
			//Escribe la Poliza
			@fwrite($BKPFile, $WriteText);
			/*
3 Caracter para quien sabe.- Diario.- supongo
2 Caracter para quien sabe
1 Caracter Espacion
1 Caracter Para quien Sabe
1 Caracter Espacio
			*/
			$sqlMvtos = "SELECT
						`contable_movimientos`.* 
						FROM
							`contable_movimientos` `contable_movimientos` 
						WHERE
							(`contable_movimientos`.`ejercicio` =$ejercicio) AND
							(`contable_movimientos`.`periodo` =$periodo) AND
							(`contable_movimientos`.`tipopoliza` =$tipoPoliza) AND
							(`contable_movimientos`.`numeropoliza` =$numeroPoliza)
						ORDER BY `contable_movimientos`.`ejercicio`,
						`contable_movimientos`.`periodo`,
						`contable_movimientos`.`tipopoliza`,
						`contable_movimientos`.`numeropoliza`,
						`contable_movimientos`.`numeromovimiento`
						";
				$MRs = mysql_query($sqlMvtos, cnnGeneral());
					while($MRw = mysql_fetch_array($MRs)){
						$cuenta 		= $MRw["numerocuenta"];
						$referencia		= $MRw["referencia"];
						//Corrige la Cuenta de Cuadre
							if ($cuenta	== CUENTA_DE_CUADRE){
								$cuenta = "_CUADRE";
							}
							//Tipo M + espacio
							//Cuenta   20
							//Referencia 10
							//TipoMvto 2 espacios 1 Cargo 2 Abono
							//Importe 16 Alineado
							//espacio + 000 + espacio + "            0.00 "
							//concepto 30 + espacio
						$WriteMvto		 = "M " . substr(str_pad($cuenta, 20, " ", STR_PAD_RIGHT), 0, 20);
						$WriteMvto		.= " " . substr(str_pad($referencia, 10, " ", STR_PAD_RIGHT), 0, 10);
						$WriteMvto		.= " " . $CWTipoMvto[$MRw["tipomovimiento"]];
						$WriteMvto		.= " " . substr(str_pad($MRw["importe"], 16, " ", STR_PAD_LEFT), -16);
						$WriteMvto		.= " 000 " . "            0.00 " .  substr(str_pad($MRw["concepto"], 30, " ", STR_PAD_RIGHT), 0, 30) . " 
";
						@fwrite($BKPFile, $WriteMvto);
					}
			
		}
	@fclose($BKPFile);
	echo "<p class='aviso'>LA EXPORTACION SE EFECTUO</p>";
	echo "<a href=\"../utils/download.php?type=txt&download=$mTmpFileAlias&file=$mTmpFileAlias\" target=\"_blank\" class='boton'>Descargar Archivo</a>";
}
?>
</body>
<script  >
function resizeMainWindow(){
	var mWidth	= 512;
	var mHeight	= 512;
	window.resizeTo(mWidth, mHeight);
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}

</script>
</html>

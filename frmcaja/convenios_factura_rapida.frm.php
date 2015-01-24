<?php
/**
 * Modulo de Carga masiva de cobros
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package caja
 * 2011-12-01 cambios menores, validacion de socios y creditos
 */
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
include_once("../core/core.html.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.operaciones.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial        	= elusuario($iduser);
ini_set("max_execution_time", 900);


$action				= $_GET["o"];

function jsaNewRecibo($observaciones, $cheque){
	$fecha 	= fechasys();
	setFoliosAlMaximo();
	
	$xRec	= setNuevorecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, $fecha, 1, 200, $observaciones, $cheque, "foraneo",
							DEFAULT_RECIBO_FISCAL, DEFAULT_GRUPO );

		if ( isset($_SESSION["recibo_en_proceso"]) ){
 			unset($_SESSION["recibo_en_proceso"]);
			unset($_SESSION["total_recibo_en_proceso"]);
		}
 		$_SESSION["recibo_en_proceso"] 			= $xRec;
		$_SESSION["total_recibo_en_proceso"]	= 0;
}
function jsaGetReciboEnSesion($ctrl){
	if ( isset($_SESSION["recibo_en_proceso"]) ){
		$recibo = $_SESSION["recibo_en_proceso"];

		$xRec 	= new cReciboDeOperacion(200, false, $recibo);
		$xRec->setNumeroDeRecibo($recibo, true);

		$xRec->setGenerarPoliza();

		$xRec->setForceUpdateSaldos();
		$xRec->setFinalizarRecibo(true);
	}
	return "<input type=\"button\" value=\"Imprimir el Recibo # " . $_SESSION["recibo_en_proceso"] . "\" onclick=\"cImprimirRecibo(" . $_SESSION["recibo_en_proceso"] . ")\" id=\"idButtonGo\" />";
}
$jxc = new TinyAjax();
$jxc ->exportFunction('jsaNewRecibo', array("cObservaciones", "cCheque") );
$jxc ->exportFunction('jsaGetReciboEnSesion', array("idButtonGo"), "#idThGo" );

$jxc ->process();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga Automatica de Pagos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php

$jxc ->drawJavaScript(false, true);

?>

<body>
<?php
//Si la Operacion es Configurar los Datos
if ( !isset($action) ){
?>
<form name="frmSendFiles" method="POST" action="convenios_factura_rapida.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga Automatica de Pagos&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO PARA ANALIZAR FACTURA</th>
		</tr>
		<tr>
			<td colspan="2"><input type="file" name="cFile1" size="50" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Enviar Archivos" /></th>
		</tr>
		</tbody>
	</table>
<?php

} elseif ( $action ==  "u" ) {

echo "<form name=\"frmConvs\" method=\"POST\" action=\"convenios_factura_rapida.frm.php?o=s\">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
	";

		$usrFiles	= array();
		$usrFiles[0]	= $_FILES["cFile1"];
		$msg			= "";


		$SAhorro	= 0;
		$SCapital	= 0;
		$SInteres	= 0;
		$SIva		= 0;
		$STotal		= 0;
		$diferencias	= 0;

		$prePath	= PATH_BACKUPS;
		$lim		= 1; //sizeof($usrFiles) -1;
		for($i=0; $i<=$lim; $i++){
			if(isset($usrFiles[$i])==true){
				//Obtener Extension
				$DExt 	= explode(".", substr($usrFiles[$i]['name'],-6));
				$mExt	= $DExt[1];

				if($mExt == "csv"){
					$completePath	= $prePath . $usrFiles[$i]['name'];
					if(file_exists($completePath)==true){
						unlink($completePath);
						$msg	.= "SE ELIMINO EL ARCHIVO " . $usrFiles[$i]['name'] . "\r\n";
					}
					if(move_uploaded_file($usrFiles[$i]['tmp_name'], $completePath )) {
						//echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
						//obtener el hash del archivo md5
						$msg	.= "MD5\tEl HASH Criptografico es " . md5_file($completePath) . "\r\n";
					} else {
						//echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
					}
					//analizar el Archivo
						$gestor = @fopen($completePath, "r");
						$xT = new cTipos;
						echo "<table width=\"100%\">
								<tbody>
								<tr>
									<th width='5%'>ID</th>
									<th width='8%'>Socio</th>
									<th width='23%'>Nombre</th>
									<th width='8%'>Credito</th>
									<th width='4%'>Letra</th>
									<th width='8%'>Capital</th>
									<th width='8%'>Interes</th>
									<th width='8%'>I.V.A.</th>
									<th width='8%'>Ahorro</th>
									<th width='8%'>TOTAL</th>
									<th width='22%'>Observaciones</th>
								</tr>";
						$iReg = 0;
						if ($gestor) {
							while (!feof($gestor)) {
								$bufer			= fgets($gestor, 4096);
								//$bufer			= stream_get_line($gestor, "\r\n");
								if (!isset($bufer) ){
									$msg .= "La Linea($iReg) no se leyo($bufer)\r\n";
								}
								$socio			= DEFAULT_SOCIO;
								$credito		= DEFAULT_CREDITO;
								$letras			= 0;
								$capital		= 0;
								$interes		= 0;
								$ahorro			= 0;
								$iva			= 0;
								$cls			= '';
								$observaciones		= "";
								$cadena			= explode(",", trim($bufer), 8);
								//Depurar
								$socio			= $xT->cInt($cadena[0]);
								$credito		= $xT->cInt($cadena[1]);
								$letra			= $xT->cInt($cadena[2]);
								$capital		= round($xT->cFloat($cadena[3]), 2);
								$interes		= round($xT->cFloat($cadena[4]), 2);
								$iva			= round($xT->cFloat($cadena[5]), 2);
								$ahorro			= round($xT->cFloat($cadena[6]), 2);
								$observaciones		= $xT->cChar($cadena[7]);

								$total			= $capital + $ahorro + $interes + $iva;

								if ( isset($socio) AND isset($credito) AND !empty($socio) AND !empty($credito) ){
									$DSoc			= new cSocio($socio);
									
									$SocioExiste	= $DSoc->existe($socio);
									$CreditoExiste	= $DSoc->existeCredito($credito);
									
									if( $SocioExiste == true AND $CreditoExiste == true){
										$DSoc->init();
										$nombre			= $DSoc->getNombreCompleto();
										$nombre			= substr($nombre, 0,25);
										//Verificar el Saldo del Credito
										$CCred			= new cCredito($credito, $socio);
										 
										//echo "$credito ---- $socio<br>";
										$CCred->init();
										$DCred			= $CCred->getDatosDeCredito();
										$saldo			= $DCred["saldo_actual"];
										
										if ( !isset($saldo) OR ($saldo == 0) ){
											//$socio 		= "";
											if ($saldo != 0 && $capital != 0 ){
												$msg	.= "$iReg\t$socio\t$credito\tDIF1\tEL Saldo($saldo) es menor al abono($capital), difiere por " . getFMoney( ($saldo - $capital) ) . " \r\n ";
											}
											$saldo		= 0;
											$diferencias	+= $capital;
											$capital	= 0;
											$total		= $capital + $ahorro + $interes;
											$cls		= " class='warn' ";
										}
		
										if ( $saldo < $capital ){
											$msg		.= "$iReg\t$socio\t$credito\tDIF2\tEL Saldo($saldo) es menor al abono($capital) , difiere por " . getFMoney( ($saldo - $capital) ) . " \r\n ";
											if ($saldo < 0) {
												
												$msg		.= "$iReg\t$socio\t$credito\tSALDO\tEL Saldo($saldo) es menor a CERO, no se admiten negativos\r\n ";
												$saldo		= 0;
											}
											$diferencias	+= ($capital - $saldo);
											$capital	= $saldo;
											$total		= $capital + $ahorro + $interes + $iva;
											$cls		= " class='warn' ";
										}
		
										$SAhorro		+= $ahorro;
										$SCapital		+= $capital;
										$SInteres		+= $interes;
										$SIva			+= $iva;
										$STotal			+= $total;
										
	
										$td 			= "
														<tr id=\"tr-$iReg\">
															<th $cls>$iReg</th>
															<td><input type=\"hidden\" size='8' id=\"socio-$iReg\" value=\"$socio\" /> $socio</td>
															<td>$nombre</td>
		
															<td><input type=\"hidden\" size='8' id=\"credito-$iReg\" value=\"$credito\" />
															$credito </td>
		
															<td>
															<input type=\"text\" size='3' id=\"letra-$iReg\" value=\"$letra\" class=\"mny\" /></td>
		
															<td><input type=\"text\" size='8' id=\"capital-$iReg\" value=\"$capital\" class=\"mny\" /></td>
															<td><input type=\"text\" size='8' id=\"interes-$iReg\" value=\"$interes\" class=\"mny\" /></td>
															<td><input type=\"text\" size='8' id=\"iva-$iReg\" value=\"$iva\" class=\"mny\" /></td>
															<td><input type=\"text\" size='8' id=\"ahorro-$iReg\" value=\"$ahorro\" class=\"mny\" /></td>
															<td class='mny'>". getFMoney($total) . "<input type=\"hidden\" size='8' id=\"total-$iReg\" value=\"$total\" class=\"mny\" disabled=\"true\" /></td>
															<td><input type=\"text\" id=\"observaciones-$iReg\" value=\"$observaciones\" /></td>
														</tr>
										";
									} else { //el socio no existe
										$msg .= "$iReg\t$socio\t$credito\tERROR\tLa Persona o el Credito NO EXISTE, Verifique su informacion\r\n";
									}
								} else {
									$msg .= "$iReg\t$socio\t$credito\tERROR\tEl credito $credito de la persona $socio no se pudo leer\r\n";
								}

								if ( !$socio OR $socio == "" ){
									$td = "";
									$msg .= "$iReg\t.\t.\tALERTA\tLa Linea($iReg) no se Imprimio ($bufer)\r\n";
								} else {
									echo $td;
									$iReg++;
								}


							}
							fclose ($gestor);
						echo "
								<tr>
									<th />
									<th colspan='3'>SUMAS</th>
									<th />
									<th class='mny'>" . getFMoney($SCapital) . "</th>
									<th class='mny'>" . getFMoney($SInteres) . "</th>
									<th class='mny'>" . getFMoney($SIva) . "</th>
									<th class='mny'>" . getFMoney($SAhorro) . "</th>
									<th class='mny'>" . getFMoney($STotal) . "</th>
									<th />
								</tr>
								<tr>
									<th />
									<th colspan='8'>" . convertirletras($STotal) . "</th>
									<th>Diferencias</th>
									<th class='warn'>" . getFMoney($diferencias) . "</th>
								</tr>
								<tr>
									<th />
									<th colspan='2'>Numero de Cheque</th>
									<th><input type=\"text\" id=\"cCheque\" value=\"\" size='8' /></th>
									
									<th colspan='2'>Observaciones</th>
									<th colspan='3'><input type=\"text\" id=\"cObservaciones\" value=\"\" size='35' /></th>
									<th id=\"idThGo\"><input type=\"button\" value=\"Enviar Pago\" onclick=\"addNewMvto()\" id=\"idButtonGo\" /></th>
									<th />
								</tr>
								</tbody>

								</table>
								<input type=\"hidden\" name=\"maxCounts\" value=\"$iReg\" />";
								$html = new cHObject();
								
									$hmsg = $html->Out($msg, "html");
									echo "<p class ='aviso'>$hmsg</p>";
								
								//echo $msg;
						}
						$xLog	= new cFileLog("recibo_masivo_num_" . $_SESSION["recibo_en_proceso"]);
						$xLog->setWrite($msg);
				}	else {
					echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
				}
			}
		}

}
if ( !isset($iReg) ){
	$iReg	= 0;
}
?>
</fieldset>
</form>
</body>

<script language='javascript' src='../js/jsrsClient.js'></script>
<script  >
//Funciones de Guardado del Recibo
var Frm 					= document.frmConvs;
var jsrCreditosCommon		= "../js/creditos.common.js.php";
var divLiteral				= "<?php echo STD_LITERAL_DIVISOR; ?>";
var jsrsContextMaxPool 		= 300;

function addNewMvto(){


	  	var isLims 			= <?php echo $iReg; ?>;

		//Asegura que sea menos de 101
		if ( isLims >= 101 ){
			document.getElementById("idButtonGo").disabled = true;

		} else {

			jsaNewRecibo();

			for(i=0; i<=isLims; i++){


				if ( document.getElementById("socio-" + i) ) {

					var mSocio			= document.getElementById("socio-" + i).value;
					var mSolicitud		= document.getElementById("credito-" + i).value;
					var mLetra			= document.getElementById("letra-" + i).value;
					var mInteres		= document.getElementById("interes-" + i).value;
					var mIva		= document.getElementById("iva-" + i).value;
					var mAhorro			= document.getElementById("ahorro-" + i).value;
					var mCapital		= document.getElementById("capital-" + i).value;
					var mObservaciones	= document.getElementById("observaciones-" + i).value;
	
					jsrsExecute(jsrCreditosCommon, cNeutralMvto, "Common_c5fe0408555dbf392918c6f77a4d01b2", mSocio + divLiteral
								+ mSolicitud + divLiteral
								+ mLetra + divLiteral + mCapital + divLiteral + mInteres + divLiteral + mIva + divLiteral + mAhorro
								+ divLiteral + mObservaciones + divLiteral + i
								+ divLiteral + isLims);

				} else {
					alert("No Existe " + i);
				}
				jsaGetReciboEnSesion();
				document.getElementById("cObservaciones").disabled 	= true;
				document.getElementById("cCheque").disabled 		= true;
			}
		}

}
	function cImprimirRecibo(iRec) {
		var elUrl	= "../rptoperaciones/rpt_consulta_recibos_masivos.php?f10=" + iRec;
		rptrecibo 	= window.open( elUrl, "window");
		rptrecibo.focus();
	}
	function cMsg(sMsg){
		if ( sMsg != "" ){
			alert(sMsg);
		}
	}
	//neutralizar el movimiento
	function cNeutralMvto(cId){
		var var_cId							= new String(cId);
		document.getElementById("tr" + var_cId).innerHTML		= "";
		
	}
</script>
</html>

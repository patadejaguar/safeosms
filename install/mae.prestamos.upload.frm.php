<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package common
 *  Actualizacion
 * 		16/04/2008
 *		2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimeintos y recibos
 *
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

$xHP		= new cHPage("TR.Carga de Creditos", HP_FORM);

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);


$action 			= (isset($_GET["o"])) ? $_GET["o"] : "x";

echo $xHP->getHeader();

//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
//Si la Operacion es Configurar los Datos
if ( $action == "x" ){
$xFRM			= new cHForm("frmSendFiles", "mae.prestamos.upload.frm.php?o=u");
$xHSel			= new cHSelect();
//$xHFil			= new cHFile()
$xFRM->setTitle("TR.ENVIAR ARCHIVO DE Creditos");
$xFRM->setEnc("multipart/form-data");
$xFRM->OFile("cFile1", "", "TR.Archivo");
$xFRM->addHElem( $xHSel->getListaDeProductosDeCredito()->get(true) );
$xFRM->addSubmit();

echo $xFRM->get();



} elseif ( $action ==  "u" ) {
echo '<form name="frmConvs" method="POST" action="mae.prestamos.upload.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend> ';


$usrFiles	= array();
$usrFiles[0]	= $_FILES["cFile1"];
$msg			= "";
$arrPeriodos	= array (
	"D" => 10,
	"Q" => 15,
	"C" => 14,
	"S" => 7,
	"M" => 30,
	"" => 30,
	"O" => 1,
	"F" => 360
);

$prePath		= PATH_BACKUPS;
$lim			= 1; //sizeof($usrFiles) -1;

for($i=0; $i<=$lim; $i++){
if(isset($usrFiles[$i])==true){
	//Obtener Extension
	$DExt 		= explode(".", substr($usrFiles[$i]['name'], -6));
	$mExt		= $DExt[1];
	$producto	= parametro("idproducto", DEFAULT_TIPO_CONVENIO, MQL_INT);
	if($mExt == "csv"){
		$completePath	= $prePath . $usrFiles[$i]['name'];
		if(file_exists($completePath)==true){
			unlink($completePath);
			echo "<p class='aviso'> SE ELIMINO EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
		}
		if(move_uploaded_file($usrFiles[$i]['tmp_name'], $completePath )) {
			//echo "<p class='aviso'> SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "</p>";
		} else {
			//echo "<p class='aviso'> SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "</p>";
		}
		//analizar el Archivo
		$gestor = @fopen($completePath, "r");

		$iReg 	= 0;
		$cT		= new cTipos();
		//inicializa el LOG del proceso
		$aliasFil	= getSucursal() . "-carga -batch-de-creditos-" . fechasys();
		$xLog		= new cFileLog($aliasFil, true);
		if ($gestor) {
			while (!feof($gestor)) {
				$bufer			= fgets($gestor, 4096);
				//$bufer			= stream_get_line($gestor, "\r\n");
				if (!isset($bufer) ){
					$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
				} else {
					$bufer				= trim($bufer);
					$datos				= explode("|", $bufer, 18);
					$xF					= new cFecha();
					
					$socio				= $cT->cInt($datos[1]);
					$credito			= false; //$cT->cInt($datos[1]);

					$monto				= $cT->cFloat($datos[8]);
					$periocidad			= $cT->cInt( $arrPeriodos[trim($datos[7])] );
					$pagos				= $cT->cInt($datos[6]);
					$ministracion			= $xF->getFechaISO($datos[10]);
					$fechaSolicitado		= $xF->getFechaISO($datos[5]);
					$descDestino			= $cT->cChar($datos[9]);
					
					$tasa				= $cT->cFloat($datos[15]);
					$tasa				= $tasa /100;
					
					$dias				= $periocidad * $pagos;
					$aplicacion			= ($cT->cChar($datos[17]) == "S") ? 501 : 100;
					
					$vencimiento			= $xF->setSumarDias($dias, $ministracion); //$cT->cFecha($datos[5]);
														
					$saldo				= $cT->cFloat($datos[11]);
					$UltimaOperacion		= fechasys(); //$cT->cFecha($datos[9]);
					$ContratoCorriente		= CTA_GLOBAL_CORRIENTE; //$cT->cInt($datos[10]);
					//Eliminar créditos anteriores
					$Creds				= new cCreditos_solicitud();
					$rs				= $Creds->query()->select()->exec("numero_socio=$socio");
					foreach($rs as $data){
						/*$Creds->setData($data);
						$solicitud 	= $Creds->numero_solicitud()->v();
						$socio		= $Creds->numero_socio()->v();
						$xCred		= new cCredito($solicitud, $socio);
						$msg		.= $xCred->setDelete();*/
					}
					if($socio == 0){
						$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
					} else {
						$sqls			= "UPDATE socios_general SET dependencia=" . DEFAULT_EMPRESA . " WHERE codigo=$socio";
						my_query($sqls);
						
						$xCred				= new cCredito();
						$xConv				= new cProductoDeCredito($producto); $xConv->init();
						//Crear Contrato corriente si el producto tiene ahorro
						$DConv				= $xCred->getDatosDeProducto($producto);
						$tasaAhorro			= $xConv->getTasaDeAhorro(); // $cT->cFloat( $DConv["tasa_ahorro"] );
						if($ContratoCorriente == 0 AND $tasaAhorro > 0){
							/*$xCapta				= new cCuentaALaVista(false);
							$ContratoCorriente	= $xCapta->setNuevaCuenta(99, DEFAULT_SUBPRODUCTO_CAPTACION, $socio, "CUENTA POR IMPORTACION", $credito);
							$msg 			.= "$iReg\t$socio\t$credito\tAgregando una Cuenta Corriente $ContratoCorriente NUEVO\r\n";*/
						}
						//Agregar
						$msg .= "$iReg\t$socio\t$credito\t-----------------------------------------\r\n";
						$ok	 = $xCred->add($producto, $socio, $ContratoCorriente, $monto, $periocidad, $pagos, $dias, $aplicacion, $credito,
								DEFAULT_GRUPO, $descDestino, "CREDITO IMPORTADO #$iReg", DEFAULT_USER, $fechaSolicitado,
								CREDITO_TIPO_PAGO_PERIODICO,INTERES_POR_SALDO_INSOLUTO, $tasa);
						if($ok == true){
							///Inicializar
							//autorizar
							$xCred->setAutorizado($monto, $pagos, $periocidad, CREDITO_TIPO_AUTORIZACION_AUTOMATICA, $ministracion,
							"CREDITO IMPORTADO #$iReg", false, $ministracion,2, false, 
							$vencimiento, CREDITO_ESTADO_AUTORIZADO, $monto, 0, $UltimaOperacion);
							$xCred->init();
							//ministrar
							$xCred->setForceMinistracion();
							$xCred->setMinistrar(DEFAULT_RECIBO_FISCAL, DEFAULT_CHEQUE, $monto, DEFAULT_CUENTA_BANCARIA, 0, DEFAULT_CUENTA_BANCARIA, "CREDITO IMPORTADO #$iReg", $ministracion);
							
							if( $monto > $saldo ){
								$abono	= ($monto - $saldo);
								$msg 	.= "$iReg\t$socio\t$credito\tAgregando un Abono por $abono por el Saldo $saldo del Monto $monto\r\n";
								$xCred->setAbonoCapital($abono, 1, DEFAULT_CHEQUE, DEFAULT_RECIBO_FISCAL,
								"CREDITO IMPORTADO #$iReg", DEFAULT_GRUPO, $UltimaOperacion);
							}
						} else {
							$msg .= "$iReg\t$socio\t$credito\tEL Credito no se pudo agregar\r\n";
						}
						$msg		.= $xCred->getMessages("txt");
					}
				}
			$iReg++;
			}
		}
		@fclose ($gestor);
		$xLog->setWrite($msg);
		echo $xLog->getLinkDownload("Archivo del proceso");
	}	else {
		echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
	}
}
}
echo "</fieldset>
</form> ";
}
if ( !isset($iReg) ){
	$iReg	= 0;

}
?>

</body>
<script >
</script>
</html>

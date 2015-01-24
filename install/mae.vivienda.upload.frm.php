<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
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
$xHP		= new cHPage("TR.Importar Vivienda", HP_FORM);
$esqueleto	= "numero=1|nombre=2|naturaleza=3";
$xT			= new cTipos();
$xF			= new cFecha();
//C  101000000000000000             DISPONIBILIDADES                                                                                      100000000000000000             A 0 4 0 20140708 11    1    0 0    0
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "mae.vivienda.upload.frm.php?action=" . MQL_TEST);
$xFil		= new cHFile();
$xChk		= new cHCheckBox();
$msg		= "";
if($action == SYS_NINGUNO ){
	$xFRM->OFile("idarchivo");
	$xFRM->addHElem( $xChk->get("TR.Afectar Base de Datos", "idaplicar") );
	//$xFRM->OTextArea("idmascara", "$esqueleto", "TR.Formato");
} else {
	//
	$doc1					= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
	$xFi					= new cFileImporter();
	$aplicar				= parametro("idaplicar", false, MQL_BOOL);
	if($aplicar == true ){ $action = MQL_ADD; }
	
	//Cedula de Identidad
	class cTmp {
		public $SUCURSAL 	= 1;
		public $IDPERSONA 	= 2;
		public $PRINCIPAL	= 3;
		public $CALLE		= 4;
		public $NUMERO		= 5;
		public $CRUCE		= 6;
		public $FECHA		= 7;
		public $TEL1		= 8;
		public $MAIL		= 9;
		public $TEL2		= 10;
		public $TEL3		= 11;
		public $COL			= 12;
		public $CODPOS		= 13;
		public $CIUDAD		= 14;
	}
	$xFi->setCharDelimiter("|");
	$xFi->setLimitCampos(14);
	$xTmp	= new cTmp();
	//$xFi->setToUTF8();
	//var_dump($_FILES["f1"]);
	//$xFi->setExo($esqueleto);
	//var_dump($_FILES);
	if($xFi->processFile($doc1) == true){
		$data				= $xFi->getData();
		$conteo				= 1;
		foreach ($data as $rows){
			if($conteo > 1){
				$xFi->setDataRow($rows);
				$persona	= $xFi->getEntero($xTmp->IDPERSONA);
				$xSoc		= new cSocio($persona);
				if( $xSoc->init() == true){
					$calle			= $xFi->cleanCalle( $xFi->getV($xTmp->CALLE, ""));
					$numero			= $xFi->getV($xTmp->NUMERO, "");
					$codigo_postal	= $xFi->getEntero($xTmp->CODPOS, DEFAULT_CODIGO_POSTAL);
					$referencia		= $xFi->getV($xTmp->CRUCE, "");
					$es_principal	= setNoMenorQueCero($xFi->getEntero($xTmp->PRINCIPAL)) >= 1 ? TIPO_DOMICILIO_PRINCIPAL : TIPO_DOMICILIO_ORDINARIO;
					$telefono1		= $xFi->getEntero($xTmp->TEL1);
					$telefono2		= $xFi->getEntero($xTmp->TEL3);
					$tipo_dom		= setNoMenorQueCero($xFi->getEntero($xTmp->PRINCIPAL)) >= 1  ? TIPO_DOMICILIO_PARTICULAR : DEFAULT_TIPO_DOMICILIO;
					$fechaVivienda	= $xFi->getFecha($xTmp->FECHA);
					$tiempo_de_residir	= $xF->getEscalaTiempo($fechaVivienda);
					$colonia		= $xFi->getV($xTmp->COL);
					if( ($action == MQL_ADD) ){

						$xSoc->addVivienda($calle, $numero,  $codigo_postal, "", $referencia,
								$telefono1, $telefono2, $es_principal, TIPO_VIVIENDA_PROPIA, $tipo_dom, $tiempo_de_residir, $colonia);
						$xSoc->setUpdate(array(
								"correo_electronico" => strtolower($xFi->cleanMail($xFi->getV($xTmp->MAIL))),
								"telefono_principal" => $xFi->getEntero($xTmp->TEL2)
						 ));
					}
				}
				$msg				.= $xSoc->getMessages();
			} else {

			}
			$conteo++;
		}

		$msg		.= $xFi->getMessages(OUT_TXT);
		$xFRM->addLog($msg);
	}
}


//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();

$xFRM->addSubmit();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

exit;

$oficial        = elusuario($iduser);
ini_set("max_execution_time", 600);

//FIXME: Terminar carga de grupos
$action 			= $_GET["o"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carga de Grupos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
//Si la Operacion es Configurar los Datos
if ( !isset($action) ){
?>
<form name="frmSendFiles" method="POST" action="mae.vivienda.upload.frm.php?o=u" enctype="multipart/form-data">
<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;Carga batch de Vivienda&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<th colspan="2">ENVIAR ARCHIVO DE VIVIENDA</th>
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

?>
<form name="frmConvs" method="POST" action="mae.vivienda.upload.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend>
<?php

$usrFiles	= array();
$usrFiles[0]	= $_FILES["cFile1"];
$msg			= "";


$prePath		= PATH_BACKUPS;
$lim			= 1; //sizeof($usrFiles) -1;
$arrRefTipo		= array(
				"" => DEFAULT_TIPO_RELACION,
				"F" => 4,
				"P" => 21
				);
for($i=0; $i<=$lim; $i++){
	if(isset($usrFiles[$i])==true){
		//Obtener Extension
		$DExt 	= explode(".", substr($usrFiles[$i]['name'], -6));
		$mExt	= $DExt[1];

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
				$grupo_solidario		= DEFAULT_GRUPO;
				$caja_local			= getCajaLocal();
				$iReg 	= 0;
				$cT		= new cTipos();
				//inicializa el LOG del proceso
				$aliasFil	= getSucursal() . "-carga -batch-de-direcciones-" . fechasys();
				$xLog		= new cFileLog($aliasFil, true);
				if ($gestor) {
					while (!feof($gestor)) {
						$bufer			= fgets($gestor, 4096);
						//$bufer			= stream_get_line($gestor, "\r\n");
						if (!isset($bufer) ){
							$msg .= "$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
						} else {
							$bufer			= trim($bufer);
							$datos			= explode("|", $bufer, 17);
							$socio			= $cT->cInt($datos[1]);
							if($socio != 0 AND $socio != DEFAULT_SOCIO){
								$xSoc		= new cSocio($socio);
								$xSoc->init();
								
								$xF		= new cFecha();
								
								$tdom		= $cT->cInt($datos[5]);
								
								$calle		= $cT->cChar(trim($datos[6]));
								//tratar calle para sacar sumero
								$calle		= strtoupper($calle);
								$calle		= str_replace("C.", "CALLE", $calle);
								$calle		= str_replace("C ", "CALLE", $calle);
								$calle		= str_replace("#", "NUMERO", $calle);
								$calle		= str_replace("NUM.", "NUMERO", $calle);
								$calle		= str_replace("NUM ", "NUMERO", $calle);
								
								$calle		= str_replace("NO.", "NUMERO", $calle);
								$calle		= str_replace("NOM.", "NUMERO", $calle);
								$calle		= str_replace("LOTE", "NUMEROLOTE", $calle);
								
								$calle		= str_replace("NO ", "NUMERO", $calle);
								$calle		= str_replace("SIN NUMERO", "SN", $calle);
								$calle		= str_replace("SIN NIM", "SN", $calle);
								$calle		= str_replace("S/N", "SN", $calle);
								
								$calle		= str_replace("CALLE ", "", $calle);
								$calle		= str_replace("CALE ", "", $calle);
								$calle		= str_replace("CALLLE ", "", $calle);
								$calle		= str_replace("CALLE", "", $calle);
								$calle		= str_replace("SN", "NUMEROSN", $calle);
								//limpiar calle
								$DCalle		= split("NUMERO", $calle); //(strpos($calle, "NUMERO") === false) ? split(" ", $calle) :  split("NUMERO", $calle);
								$calle		= trim($DCalle[0]);
								$numero		= (isset($DCalle[1])) ? trim($DCalle[1]) : "SN";
								
								$codigo_postal	= $cT->cInt(trim($datos[15]));
								$colonia	= $cT->cChar(trim($datos[14]));
								$referencia	= ( isset($datos[7]) ) ? "ENTRE ". $cT->cChar(trim($datos[7])) . " Y " . $cT->cChar(trim($datos[8])) : "";
								$telefono1	= $cT->cChar(trim($datos[10]));
								$telefono2	= $cT->cChar(trim($datos[12]));
								
								$es_principal	= ($tdom == 1) ? TIPO_DOMICILIO_PRINCIPAL : TIPO_DOMICILIO_ORDINARIO;
								$tipo_dom	= ($tdom == 1) ? TIPO_DOMICILIO_PARTICULAR : DEFAULT_TIPO_DOMICILIO;
								$fechaVivienda	= $xF->getFechaISO($cT->cChar(trim($datos[9])));
								
								$tiempo_de_residir	= $xF->getEscalaTiempo($fechaVivienda);
								
								
								$xSoc->addVivienda($calle, $numero,  $codigo_postal, "", $referencia,
										   $telefono1, $telefono2, $es_principal, TIPO_VIVIENDA_PROPIA, $tipo_dom, $tiempo_de_residir, $colonia);
								$msg		.= $xSoc->getMessages("txt") . "\n";
							}
							
						}
					$iReg++;
					}
				}
				fclose ($gestor);
				$xLog->setWrite($msg);
				echo $xLog->getLinkDownload("Archivo del proceso");
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
<script  >
</script>
</html>

<?php
//=====================================================================================================
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
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
$xP		= new cHPage("");
$xP->setIncludes();

$oficial      = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Respaldar Archivos de la sucursal <?php  echo getSucursal(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
$operation		= $_GET["a"];

?>
<body>

<form name="frmExecBackup" method="post" action="sucursal.backup_offline.frm.php?a=1&s=1">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>

<?php
if($operation!="1"){
	$tmpFol = setFoliosAlMaximo();
?>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Fecha de Corte</td>
			<td><input type='text' name='cFechaDeCorte' value='<?php echo fechasys(); ?>' id="" /></td>
		</tr>
		<tr>
			<td>Ultimo Folio Obtenido</td>
			<td><input type='hidden' name='cLastFolio' value='<?php echo $tmpFol["recibos"]; ?>' id="idLastFolio" /></td>
		</tr>
		<tr>
			<th colspan="2"><input type="submit" value="Ejecutar Respaldo" /></th>
		</tr>
		</tbody>
	</table>
<p class="warn">Efect&uacute;e el Respaldo despu&eacute;s del ***CIERRE DEL DIA***</p>
</fieldset>
</form>
<?php
} else {

$step				= $_GET["s"];
$BkpSucursal		= getSucursal();					//Sucursal
$sucursal			= getSucursal();					//Sucursal
$chri				= STD_LITERAL_DIVISOR;							//Literal
$msg				= "=========================================================================================================\r\n";
$msg				.= "===================================== PASO $step\r\n";
$msg				.= "=========================================================================================================\r\n";

set_time_limit(900);
if ( isset($step) ){
	$aliasFils	= "evento-de-respaldo-$sucursal-" . date("Ymd");
	//Elimina el Archivo
	//unlink(PATH_TMP . $aliasFils . ".txt");
	//Abre Otro, lo crea si no existe
	$URIFil			= @fopen(PATH_TMP . $aliasFils . ".txt", "a+");
}
switch($step){
	case 1:

			$msg				.= "===================================== INICIANDO EL RESPALDO DE $BkpSucursal =============================\r\n";
	
			$FechaDeCorte 		= $_POST["cFechaDeCorte"];		//Fecha Inicial de Corte
			$LstFolio 			= $_POST["cLastFolio"];			//Ultimo Folio de Recibo
			$msg				.= date("H:i:s") . "\tLa Fecha de Corte es $FechaDeCorte\r\n";
			//$msg				.= "\tLa Fecha de Corte es $FechaDeCorte";
			//step one: Socios a sus sucursales
	
			$xSuc 				= new cSucursal( getSucursal() );
			$msg				.= $xSuc->setValidar();
      		$xCL 				= new cCajaLocal(getCajaLocal());
      		$msg				.= $xCL->setValidar();
			//step two: Folios al Maximo
			$msg				.= setFoliosAlMaximo();

			//Step_tree: Elimina los archivos en el tmp Backups
			$xop				= new cUtileriasParaOperaciones();
			//Genera un Recibo por los Mvtos Huerfanos de Recibo en Operaciones
			$msg				.= $xop->setGenerarRecibosGlobales();			
			//Elimina Recibos Duplicados
			$msg				.= $xop->setEliminarRecibosDuplicados();
			
			$sql_ttmp 			= "DELETE FROM general_tmp";
			my_query($sql_ttmp);
			$msg				.= "\tSe eliminan Registros Temporales\r\n";
			echo "
				<ol>
					<li>Se Actualizaron los Folios</li>
					<li>Se Actualizo el DEFAULT Socio para Operaciones que no tienen socio</li>
					<li>Se Actualizaron los Socios a su sucursal</li>
					<li>Se Actualizo el usuario ROOT para Operaciones que no tienen propietario</li>
					<li>Se Agrego el Recibo x para Mvtos Huerfanos</li>
					<li>Se Purgaron Recibos Duplicados</li>
				<li>Se Actualizo la sucursal de Creditos por usuario propietario</li>
				<li>Se Actualizo la sucursal de las Cuentas de Captacion por Usuario Propietario</li>
				<li>Se Actualizo la Sucursal de las Operaciones y recibos por Usuario Propietario</li>
				<li>La fecha de purga es a partir de $FechaDeCorte</li>					
				</lo>
				<br />
				<a href=\"./sucursal.backup_offline.frm.php?a=1&s=2&f=$FechaDeCorte&n=$LstFolio\" target=\"_self\">Siguiente</a>
			";
		//$x145 = "UPDATE socios_general SET sucursal = '" . $sucursal . "' WHERE codigo = " . DEFAULT_SOCIO;
		//$xQ		= my_query($x145);

		$msg	.= date("H:i:s") . "\tSe Marca el recibo $LstFolio como ultimo\r\n";

		$msg	.= date("H:i:s") . "\tSe Actualiza el Socio PUBLICO_GENERAL a la sucursal de corte para Operaciones sin Socio\r\n";
		$msg	.= date("H:i:s") . "\tSe Agrego el Recibo x para Mvtos Huerfanos\r\n";
		@fwrite($URIFil, $msg);
		break;

	case 2:
		$FechaDeCorte 		= $_GET["f"];		//Fecha Inicial de Corte
		$LstFolio 			= $_GET["n"];		//Ultimo Folio de Recibo
		$bkpath 			= PATH_BACKUPS;
		$fils 				= array();
		$querys				= array();
		$BySucursal 		= "  AND sucursal = '$sucursal' ";
		$SegundaCondicion 	= "";

		$xTablas			= new cSAFETabla();
		//Eliminar Archivos existentes
		$aFilesT			= $xTablas->getTablasConOperaciones();

		foreach ($aFilesT as $key => $value){
			$xTablas->init($value);
			$mFile		= $xTablas->getNombreRespaldo($FechaDeCorte);
			$rsXp 		= unlink($mFile);
			
			if ( !$rsXp ){
				$msg	.= "\tNo se Elimino $mFile por que NO Existe\r\n";
			} else {
				$msg	.= "\tSe Elimino el Archivo $mFile\r\n";
			}
		}
		//
//=========================== TABLAS QUE NO NECESITAN CONDICIONES PARA EXPORTARSE =================================
		//Respaldo	CUENTAS DE CAPTACION
		$xTablas->init("captacion_cuentas");
		$ix 		= 1;
		$fils[$ix]	= $xTablas->getNombreRespaldo($FechaDeCorte);
		$sql1 		= "SELECT * INTO OUTFILE '$fils[$ix]'
						FIELDS TERMINATED BY '$chri'
						LINES TERMINATED BY '\\r\\n'
						FROM captacion_cuentas
						WHERE fecha_afectacion>='$FechaDeCorte' $BySucursal $SegundaCondicion";
		$querys[$ix]	= my_query($sql1);
		
		$msg		.= ($querys[$ix]["stat"] == false) ? "ERROR\tSe Fallo al Crear el Archivo $fils[$ix]| " . $querys[$ix]["error"] . "\r\n" : "SUCESS\tSe creo el Archivo $fils[$ix]| " . $querys[$ix]["info"] . "\r\n"; 

		//SOLICITUDES
		$ix 		= 2;
		$xTablas->init("creditos_solicitud");
		$fils[$ix]	= $xTablas->getNombreRespaldo($FechaDeCorte);
		$sql3	 	= "SELECT * INTO OUTFILE '$fils[$ix]'
						FIELDS TERMINATED BY '$chri'
						LINES TERMINATED BY '\\r\\n'
						FROM creditos_solicitud
						WHERE fecha_ultimo_mvto>='$FechaDeCorte' $BySucursal $SegundaCondicion";
		$querys[$ix]	= my_query($sql3);

		
		$msg		.= ($querys[$ix]["stat"] == false) ? "ERROR\tSe Fallo al Crear el Archivo $fils[$ix]| " . $querys[$ix]["error"] . "\r\n" : "SUCESS\tSe creo el Archivo $fils[$ix]| " . $querys[$ix]["info"] . "\r\n";
						
		//SOCIOS
		$ix 		= 3;
		$xTablas->init("socios_general");
		$fils[$ix]	= $xTablas->getNombreRespaldo($FechaDeCorte);
		$sql5 		= "SELECT * INTO OUTFILE '$fils[$ix]'
						FIELDS TERMINATED BY '$chri'
						LINES TERMINATED BY '\\r\\n'
						FROM socios_general
						WHERE fechaentrevista>='$FechaDeCorte' $BySucursal $SegundaCondicion";
		$querys[$ix]	= my_query($sql5);
		
		$msg		.= ($querys[$ix]["stat"] == false) ? "ERROR\tSe Fallo al Crear el Archivo $fils[$ix]| " . $querys[$ix]["error"] . "\r\n" : "SUCESS\tSe creo el Archivo $fils[$ix]| " . $querys[$ix]["info"] . "\r\n";
		//Respaldo	GRUPOS SOLIDARIOS
		$ix 		= 4;
		$xTablas->init("socios_grupossolidarios");
		$fils[$ix]	= $xTablas->getNombreRespaldo($FechaDeCorte);
		$sql8 		= "SELECT * INTO OUTFILE '$fils[$ix]'
						FIELDS TERMINATED BY '$chri'
						LINES TERMINATED BY '\\r\\n'
						FROM socios_grupossolidarios
						WHERE fecha_de_alta>='$FechaDeCorte' $BySucursal $SegundaCondicion";
		$querys[$ix]	= my_query($sql8);
			
		$msg		.= ($querys[$ix]["stat"] == false) ? "ERROR\tSe Fallo al Crear el Archivo $fils[$ix]| " . $querys[$ix]["error"] . "\r\n" : "SUCESS\tSe creo el Archivo $fils[$ix]| " . $querys[$ix]["info"] . "\r\n";				

//=========================== TABLAS CON OPERACIONES ESPECIALES
		//Respaldo	OPERACIONES MOVIMIENTOS
		$ix 		= 5;
		$xTablas->init("operaciones_mvtos");
		$fils[$ix]	= $xTablas->getNombreRespaldo($FechaDeCorte);		
		$sql4 		= "SELECT
						fecha_operacion, fecha_afectacion,

						CONCAT(\"TMP_\", recibo_afectado),

						socio_afectado, docto_afectado, tipo_operacion, afectacion_real,
						afectacion_cobranza, afectacion_contable, valor_afectacion, fecha_vcto, estatus_mvto, codigo_eacp, periodo_socio,
						periodo_contable, periodo_cobranza, periodo_seguimiento, periodo_mensual, periodo_semanal, periodo_anual, saldo_anterior,
						saldo_actual, detalles, idusuario, afectacion_estadistica, docto_neutralizador, cadena_heredada, tasa_asociada,
 						dias_asociados, grupo_asociado, sucursal

	 					INTO OUTFILE '$fils[$ix]'
						FIELDS TERMINATED BY '$chri'
						LINES TERMINATED BY '\\r\\n'
						FROM operaciones_mvtos
						WHERE fecha_operacion>='$FechaDeCorte' $BySucursal $SegundaCondicion";
		$querys[$ix]	= my_query($sql4);

		$msg		.= ($querys[$ix]["stat"] == false) ? "ERROR\tSe Fallo al Crear el Archivo $fils[$ix]| " . $querys[$ix]["error"] . "\r\n" : "SUCESS\tSe creo el Archivo $fils[$ix]| " . $querys[$ix]["info"] . "\r\n";

		//Respaldo	OPERACIONES RECIBOS
		$ix 		= 6;
		$xTablas->init("operaciones_recibos");
		$fils[$ix]	= $xTablas->getNombreRespaldo($FechaDeCorte);
		
		$sql7 		= "SELECT
						CONCAT(\"TMP_\", idoperaciones_recibos), " . $xTablas->getCamposSinClaveUnica() . "
						INTO OUTFILE '$fils[$ix]'
						FIELDS TERMINATED BY '$chri'
						LINES TERMINATED BY '\\r\\n'
						FROM operaciones_recibos
						WHERE fecha_operacion>='$FechaDeCorte' $BySucursal $SegundaCondicion";
		$querys[$ix]	= my_query($sql7);

		$msg		.= ($querys[$ix]["stat"] == false) ? "ERROR\tSe Fallo al Crear el Archivo $fils[$ix]| " . $querys[$ix]["error"] . "\r\n" : "SUCESS\tSe creo el Archivo $fils[$ix]| " . $querys[$ix]["info"] . "\r\n";

		
		
//==================================================================================================
//=========================== TABLAS QUE **SI** NECESITAN CONDICIONES PARA EXPORTARSE =================================
		$TblsSinClaves		= $xTablas->getTablasConOperaciones();
		unset($TblsSinClaves["socios_general"]);
		unset($TblsSinClaves["socios_grupossolidarios"]);
		unset($TblsSinClaves["captacion_cuentas"]);
		unset($TblsSinClaves["creditos_solicitud"]);
		unset($TblsSinClaves["operaciones_recibos"]);
		unset($TblsSinClaves["operaciones_mvtos"]);
//=========================== TABLAS CON CLAVE PRINCIPAL OMITIDA
		foreach( $TblsSinClaves as $ky => $vals ){
		//Memos de Socios
			$ix++;
			$xTablas->init($vals);
			$fils[$ix]	= $xTablas->getNombreRespaldo($FechaDeCorte);
			$sql 		= "SELECT " . $xTablas->getCamposSinClaveUnica() . "
							INTO OUTFILE '$fils[$ix]'
							FIELDS TERMINATED BY '$chri'
							LINES TERMINATED BY '\\r\\n'
							" . $xTablas->getFrom() . "
							WHERE " . $xTablas->getCampoFechaPrincipal() . " >= '$FechaDeCorte' $BySucursal $SegundaCondicion
						";
			$querys[$ix]= my_query($sql);
			$msg		.= ($querys[$ix]["stat"] == false) ? "ERROR\tSe Fallo al Crear el Archivo $ix $fils[$ix]| " . $querys[$ix]["error"] . "\r\n" : "SUCESS\tSe creo el Archivo $fils[$ix]| " . $querys[$ix]["info"] . "\r\n";
		}		
//==================================================================================================

		//Genera un Log
		$lim = count($querys);
			for($i=1; $i<=$lim;$i++){
				if($querys[$i]["stat"] == false){
					echo "<p class='warn'>EL ARCHIVO PARA " . $fils[$i] . " NO SE GENERO<br />
						Y DEVOLVIO EL SIGUIENTE ERROR " . $querys[$i]["error"] . "</p>" ;
					$fils[$i] == false;
				}
			}
		$cFils	= count($fils);
		$ilFils	= "";
			for($i=0;$i<=$cFils;$i++){
				//si no es falso el archivo
				if($fils[$i]!=false){
					$DFil 		= explode(".", $fils[$i]);
					$DFilS 		= explode("/", $DFil[0]);
					$filename	= $DFilS[ ( count($DFilS) - 1 ) ];
					$ilFils 	.= "<li><a href=\"../utils/download.php?type=sbk&download=$filename&file=$filename\" target=\"_blank\">Descargar $fils[$i]</a></li> \n";
				}
			}
		//Crea un zip
		//include_once("../libs/ziplib.php");
		//$myZipFile = new zipfile();
		$lastFols 	= 	setFoliosAlMaximo();

		echo "
		ARCHIVOS PARA DESCARGAR: <br />
		<ol>
			$ilFils
		</ol>
		<br />
		<p class='aviso'>Proceso Concluido</p>
		<br />
			<ol>
				<li>El RESPALDO Termino Satisfactoriamente</li>
			</lo>
			<br />
		";
		@fwrite($URIFil, $msg);
	break;
}
}
if( isset($step) && ($step ==5) ){

echo "<a href=\"../utils/download.php?type=txt&download=$aliasFils&file=$aliasFils\" target=\"_blank\" class='boton'>Descargar Archivo de EVENTOS</a>";
echo "</fieldset>
</form>";
}
?>

</body>
<script  >
</script>
</html>

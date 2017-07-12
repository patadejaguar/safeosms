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
include_once( "../core/entidad.datos.php");
include_once( "../core/core.deprecated.inc.php");
include_once( "../core/core.fechas.inc.php");
include_once( "../libs/sql.inc.php");
include_once( "../core/core.config.inc.php");
include_once( "../reports/PHPReportMaker.php");
include_once( "../libs/excel.inc.php");

$oficial 			= elusuario($iduser);
//=====================================================================================================
/**
 * Filtrar si hay Fecha
 */
ini_set("max_execution_time", 1080);
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$estatus_actual 	= $_GET["f2"];
$si_es_por_fecha 	= "";
if ($fecha_inicial && $fecha_final){
		$si_es_por_fecha = " AND creditos_solicitud.fecha_ministracion<='$fecha_final' ";
}

//=====================================================================================================

$estatus 			= $_GET["estatus"];
$frecuencia 		= $_GET["frecuencia"];
$convenio 			= $_GET["convenio"];
$sucursal			= $_GET["sucursal"];

$es_por_estatus 	= "";
$periodo			= date("m", strtotime($fecha_final));
$ejercicio			= date("Y", strtotime($fecha_final));

$es_por_frecuencia 	= "";
$es_por_convenio 	= "";
$BySucursal			= "";

if( isset($estatus) AND ($estatus != "todas") ){
	$es_por_estatus 	= " AND creditos_solicitud.estatus_actual=$estatus ";
}

if( isset($frecuencia) AND ($frecuencia != "todas") ){
	$es_por_frecuencia 	= " AND creditos_solicitud.periocidad_de_pago=$frecuencia ";
}

if( isset($convenio) AND ($convenio != "todas") ){
	$es_por_convenio 	= " AND creditos_solicitud.tipo_convenio = $convenio ";
}

if ($fecha_inicial && $fecha_final){
	$rango_de_fechas 	= " AND creditos_solicitud.fecha_ministracion<='$fecha_final' ";
}
if ( MODO_DEBUG == true){
		$aliasFils		= "log-de-informe_mesual-al-" . date("Y-m-d");
	//Elimina el Archivo
	unlink(PATH_TMP . $aliasFils . ".txt");
	//Abre Otro, lo crea si no existe
	$URIFil		= fopen(PATH_TMP . $aliasFils . ".txt", "a+");
}
	$msg 	= 	"============\t\t\t\tINFORME MENSUAL DE CREDITOS \r\n";
	$msg 	.= 	"============\t\t\t\tCREADOR\t\t$oficial \r\n";
	$msg 	.= 	"============\t\t\t\tSUCURSAL\t\t" . getSucursal() . " \r\n";

$input = $_GET["out"];
	if (!$input) {
		$input = "default";
	}
  /**
  *	Arrrays de Datos Complementarios
  */
  $VFigura 			= array (
		  				1 => "FISICA",
		  				2 => "MORAL",
		  				3 => "COPROPIEDAD",
		  				99 => "DESCONOCIDO"
  					);
  $VClasificacion 	= array (
  						1 => "COMERCIAL",
  						2 => "VIVIENDA",
  						3 => "CONSUMO",
  						99 => "DESCONOCIDO"
  					);
  $VPeriocidad		= array (
  						7	=> "PAGO PARCIAL DE CAPITAL E INTER&Eacute;S",
  						15 	=> "PAGO PARCIAL DE CAPITAL E INTER&Eacute;S",
  						30 	=> "PAGO PARCIAL DE CAPITAL E INTER&Eacute;S",
  						60 	=> "PAGO PARCIAL DE CAPITAL E INTER&Eacute;S",
  						90 	=> "PAGO PARCIAL DE CAPITAL E INTER&Eacute;S",
  						360 => "CAPITAL E INTER&Eacute;S AL VENCIMIENTO"

  					);
  /**
   * Estatus del Credito para Efectos del Reporte
   */
  $VEstatus			= array(
  						10	=> "VIGENTE",
  						20	=> "VENCIDA",
  						30	=> "VIGENTE",
  						60	=> "VIGENTE",
  						50	=> "VENCIDA"
  					);
  /**
   * Estatus del Credito a Efectos del la DB
   *
   */
  $VDBEstatus		= array(
  						10	=> "VIGENTE",
  						20	=> "VENCIDA",
  						30	=> "MOROSA",
  						60	=> "NO_APLICA",
  						50	=> "CASTIGADA"
  					);
  /**
   * Tipo de Autorizacion segun la DB para efectos del Reporte
   */
  $VAutorizado		= array(
  						1	=> "NORMAL",
  						2	=> "NORMAL",
  						3	=> "RENOVADO",
  						4	=> "REESTRUCTURADO",
  						99	=> "NORMAL"
  					);
	/**
	 * @var $VI_Autorizado Tipo de Autorizacion para Efectos Internos de la DB
	 */
  $VI_Autorizado	= array(
  						1	=> "NORMAL",
  						2	=> "NORMAL_AUTOMATIZADA",
  						3	=> "RENOVADO",
  						4	=> "REESTRUCTURADO",
  						99	=> "NINGUNO"
  					);
  	$VAvaluo		= array(
  						1	=> "Persona Fisica o Moral NO Certificada para realizar avaluos",
  						2	=> "Persona Fisica o Moral Certificada para realizar avaluos",
  						3	=> "Persona Fisica o Moral NO Certificada para realizar avaluos",
  						99	=> "Otra"
  					);
		$VATipoIngreso		= array();
		$SQLtIng			= "SELECT * FROM socios_tipoingreso";
		$rsTipoIngreso		= mysql_query($SQLtIng, cnnGeneral() );
		  	while ( $TIRw = mysql_fetch_array($rsTipoIngreso) ){
		  		$VATipoIngreso[ $TIRw["idsocios_tipoingreso"] ] = $TIRw["descripcion_tipoingreso"];
		  	}
	/** ******************************************************************************/
		//Sql de Oficiales
		$sqlO 			= "SELECT * FROM oficiales";
		$rsO			= mysql_query($sqlO, cnnGeneral() );
		$arrOficiales	= array();
			while($rwO = mysql_fetch_array($rsO) ){
				$arrOficiales[ $rwO["id"] ] = $rwO["nombre_completo"];
			}
	/** ******************************************************************************/
		//Interes Devengado
		$SqlInteres		= "SELECT socio_afectado, docto_afectado, SUM(interes) AS 'devengado'
								FROM interes_normal_devengado
							WHERE
								periodo <= $periodo
								AND
								ejercicio <= $ejercicio
							GROUP BY
									socio_afectado,
									docto_Afectado ";

		$rsInt 			= mysql_query($SqlInteres, cnnGeneral());
		$ArrInts		= array();
		while($IRw = mysql_fetch_array($rsInt)){
			$ArrInts[$IRw["docto_afectado"]] = $IRw["devengado"] ;
		}
		//Garantia Liquida
		$ArrGtias		= array();
		$sqlGarantia 	= "SELECT * FROM garantia_liquida";
			$rsGtia		= mysql_query($sqlGarantia, cnnGeneral());
			while ($GRw = mysql_fetch_array($rsGtia)){
				$ArrGtias[ $GRw["docto_afectado"] ] = $GRw["garantia"];
			}

	$setSql = "
		SELECT
			`socios_general`.*,
			`creditos_solicitud`.*,
			`creditos_solicitud`.tasa_interes AS 'tasa_ordinaria',
			`creditos_solicitud`.tipo_autorizacion AS 'tipo_de_autorizacion',
			`creditos_tipoconvenio`.*,
			`creditos_causa_de_vencimientos`.*
		FROM
			`creditos_solicitud` `creditos_solicitud`
			INNER JOIN `socios_general` `socios_general`
			ON `creditos_solicitud`.`numero_socio` = `socios_general`.`codigo`
			INNER JOIN `creditos_causa_de_vencimientos`
			`creditos_causa_de_vencimientos`
			ON `creditos_solicitud`.`causa_de_mora` =
			`creditos_causa_de_vencimientos`.`idcreditos_causa_de_vencimientos`
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
			ON `creditos_solicitud`.`tipo_convenio` =
			`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
		WHERE
			creditos_solicitud.saldo_conciliado>=" . TOLERANCIA_SALDOS . "
			AND	creditos_solicitud.fecha_ministracion<='$fecha_final'
			AND
			(creditos_solicitud.estatus_actual!=50 )
			AND
			(creditos_solicitud.estatus_actual!=98 )
			AND
			(creditos_solicitud.estatus_actual!=99 )
			$rango_de_fechas
			$es_por_estatus
			$es_por_convenio
			$es_por_frecuencia
			ORDER BY
			creditos_solicitud.tipo_autorizacion DESC,
			creditos_solicitud.numero_socio,
			creditos_solicitud.fecha_ultimo_mvto
		/* LIMIT 0,100 */ ";
	//echo $setSql; exit;
	//-- Cabeceras HTML
	$tdHeader	= "
		<tr>
			<th>TIPO DE PERSONA</th>
			<th>NOMBRE DEL ACREDITADO</th>
			<th>CLAVE DE PERSONA</th>
			<th>NUMERO DE CREDITO</th>
			<th>TIPO DE CREDITO</th>
			<th>TIPO DE AUTORIZACION</th>
			<th>FUENTE DE PAGO</th>
			<th>CUMPLIO PAGO SOSTENIDO</th>
			<th>ORIGEN DEL FONDEO DEL RECURSO</th>
			<th>ENTIDAD QUE ASUME EL RIESGO</th>
			<th>CONDICION DE PAGO</th>
			<th>FECHA DE OTORGAMIENTO</th>
			<th>MONTO ORIGINAL</th>
			<th>FECHA DE VENCIMIENTO</th>
			<th>TASA ORDINARIA ANUAL</th>
			<th>NUMERO DE PAGOS</th>
			<th>FRECUENCIA</th>
			<th>CAPITAL INSOLUTO</th>
			<th>INTERES DEVENGADO</th>
			<th>FECHA DE ULTIMO PAGO</th>
			<th>ESTATUS DEL CREDITO</th>
			<th>MONTO DE LA GARANTIA LIQUIDA</th>
			<th>CON CONTRATO DE DEP.</th>
			<th>INDICA QUE EL DEP. PUEDA SER RETIRADO</th>
			<th>CON MANDATO PARA RETIROS</th>
			<th>EL SISTEMA BLOQUEA EL DEPOSITO EN GTIA</th>
			<th>BIENES INMUEBLES FORMALIZADOS</th>
			<th>LIBRE DE GRAVAMENES</th>
			<th>ASEGURADO A FAVOR DE LA ENTIDAD</th>
			<th>AVALUA ACTUALIZADO</th>
			<th>QUIEN REALIZA EL AVALUO</th>
			<th>VALOR DE LA GARANTIA HIPOTECARIA</th>
			<th>RESERVADAS CREADAS POR LA ENTIDAD</th>
			<th>DIAS VENCIDOS</th>
			<th>TIPO DE AUTORIZACIO</th>
			<th>TIPO DE INGRESO</th>
			<th>ESTATUS-DB</th>
			<th>TIPO DE CONVENIO</th>
			<th>OFICIAL A CARGO</th>
			<th>CAUSA DE MORA</th>
		</tr>
	";
	$tdBody	= "";
	//
	$rs = mysql_query($setSql, cnnGeneral() );
	while($rw = mysql_fetch_array($rs) ){
		$personalidad			= $VFigura[$rw["personalidad_juridica"]];
		$nombre					= $rw["apellidopaterno"] . " " . $rw["apellidomaterno"] . " " . $rw["nombrecompleto"];
		$codigo					= $rw["codigo"];
		$credito				= $rw["numero_solicitud"];
		$razon_garantia			= $rw["razon_garantia"];
		$tipo_credito			= $VClasificacion[$rw["tipo_credito"]];
		$dias_vencidos			= 0;
		$periocidad				= $rw["periocidad_de_pago"];
		$vencimiento_legal		= $rw["fecha_vencimiento_dinamico"];
		$MAutorizacion			= $VAutorizado[ $rw["tipo_de_autorizacion"] ];

			if ( $rw["estatus_actual"] == 20 ){
				//si la fecha de abono es despues de la de vencimiento
					$dias_vencidos	= restarfechas($fecha_final, $vencimiento_legal);
			}

			$sqlIngresos = "SELECT
						`creditos_origenflujo`.`descripcion_origenflujo`,
						`creditos_flujoefvo`.`solicitud_flujo`,
						SUM(`creditos_flujoefvo`.`afectacion_neta`) AS 'ingresos'
					FROM
						`creditos_flujoefvo` `creditos_flujoefvo`
							INNER JOIN `creditos_origenflujo` `creditos_origenflujo`
							ON `creditos_flujoefvo`.`origen_flujo` = `creditos_origenflujo`.
							`idcreditos_origenflujo`
					WHERE
						(`creditos_flujoefvo`.`solicitud_flujo` =$credito)
						AND
						(`creditos_flujoefvo`.`tipo_flujo` = 1)
					GROUP BY
						`creditos_flujoefvo`.`solicitud_flujo`,
						`creditos_flujoefvo`.`origen_flujo`
					ORDER BY
						`creditos_flujoefvo`.`afectacion_neta` DESC
					LIMIT 0,1";

		$msg .= date("H:i:s") . "\t$codigo\t$credito\tSe Inicia el Procesado del Credito \r\n";
		$DFlujo	= obten_filas($sqlIngresos);
		$IngresoPrioritario	= $DFlujo["descripcion_origenflujo"];
		if ( !isset($IngresoPrioritario) ){
			$IngresoPrioritario = "DESCONOCIDO_O_NO_REGISTRADO";
		}
		$cumplioPagoSostenido = "";
		if ($rw["estatus_actual"] == 60){
			$cumplioPagoSostenido = "SI CUMPLIO";
		}
		$CondicionDePago 		= $VPeriocidad[$rw["periocidad_de_pago"]];
		$FechaDeMinistracion	= $rw["fecha_ministracion"];
		$MontoOriginal			= $rw["monto_autorizado"];
		$FechaDeVencimiento		= $rw["fecha_vencimiento"];
		$TasaInteresAnual		= $rw["tasa_ordinaria"];
		$NumeroPagos			= $rw["pagos_autorizados"];
		$FrecuenciaDePagos		= $rw["periocidad_de_pago"];
		$dias_autorizados		= $rw["dias_autorizados"];
		$CapitalInsoluto		= $rw["saldo_conciliado"];
		$porcentaje_gtia_liq	= $rw["porciento_garantia_liquida"];
		$Interesdevengado		= $ArrInts[$credito];
			if (!isset($Interesdevengado)){
				$Interesdevengado = 0;
			}

		$FechaDeMvto			= $rw["fecha_conciliada"];
		$EstatusCredito			= $VEstatus[ $rw["estatus_actual"] ];
		$CausaDeMora			= $rw["descripcion_de_la_causa"];

		//FEATURE
		//$FechaDeMvto			= $rw["fecha_ultimo_mvto"];
		//$CapitalInsoluto		= $rw["saldo_actual"];

		//PATCH hasta Conciliarse las Cuentas de garantia Liquida
		//$MontoGarantia			= $MontoOriginal * $porcentaje_gtia_liq;
		$MontoGarantia 			= $ArrGtias[$credito];

		if ( !isset($MontoGarantia) ){
			$MontoGarantia		= 0;
		}
		if ( $MontoGarantia < 0){
			$msg .= date("H:i:s") . "\t$codigo\t$credito\tERROR la Garantia Liquida($MontoGarantia) es Menor a Cero, deberia ser de " . ($MontoOriginal * $porcentaje_gtia_liq) . " \r\n";
		} else {
			$msg .= date("H:i:s") . "\t$codigo\t$credito\tEl % de la Garantia es $porcentaje_gtia_liq y tiene un Monto de $MontoGarantia de " . ($MontoOriginal * $porcentaje_gtia_liq) . " \r\n";
		}
		//Garantia Hipotecaria
		$Hipoteca 				= "NO";
		$HipotecaValuacion		= "";
		$HipotecaGravamen		= "";
		$HipotecaSeguro			= "";
		$HipotecaActualizado	= "";
		$HipotecaMonto			= 0;

		$sqlHipoteca = "SELECT
	`creditos_garantias`.`tipo_garantia`,
	`creditos_garantias`.`estatus_actual`,
	`creditos_garantias`.`tipo_valuacion`,
	`creditos_garantias`.`solicitud_garantia`,
	SUM(`creditos_garantias`.`monto_valuado`) AS 'hipoteca'
FROM
	`creditos_garantias` `creditos_garantias`
WHERE
	(`creditos_garantias`.`tipo_garantia` =1)
	AND
	(`creditos_garantias`.`estatus_actual` =2)
	AND
	(`creditos_garantias`.`solicitud_garantia` =$credito)
GROUP BY
	(`creditos_garantias`.`tipo_garantia`),
	(`creditos_garantias`.`solicitud_garantia`)";
		if( $rw["aplica_gastos_notariales"] == 1){
			$DHipoteca	= obten_filas($sqlHipoteca);
			if ( isset($DHipoteca["solicitud_garantia"])  ){
				$HipotecaValuacion 		= $VAvaluo[$DHipoteca["tipo_valuacion"]];
				$HipotecaGravamen		= "SI";
				$HipotecaSeguro			= "NO";
				$HipotecaActualizado	= "NO";
				$HipotecaMonto			= $DHipoteca["hipoteca"];
				$msg .= date("H:i:s") . "\t$codigo\t$credito\tSe Agrega la Hipoteca por un Monto de $HipotecaMonto\r\n";
			} else {
				$msg .= date("H:i:s") . "\t$codigo\t$credito\tNo existe la Hipoteca, la Razon de Garantia Hipotecaria es $razon_garantia\r\n";
			}
		}
		if ( $rw["periocidad_de_pago"] == 360){
			$FrecuenciaDePagos	= $dias_autorizados;
		}
		$tdBody .= "
		<tr>
			<td>$personalidad</td>	<td>$nombre</td>
			<td>$codigo</td>
			<td>$credito</td>
			<td>$tipo_credito</td>
			<td>$MAutorizacion</td>
			<td>$IngresoPrioritario</td>
			<td>$cumplioPagoSostenido</td>
			<td>" . EACP_NAME . "</td>
			<td>Caja Solidaria</td>
			<td>$CondicionDePago</td>
			<td>$FechaDeMinistracion</td>
			<td>$MontoOriginal</td>
			<td>$FechaDeVencimiento</td>
			<td>$TasaInteresAnual</td>
			<td>$NumeroPagos</td>
			<td>$FrecuenciaDePagos</td>
			<td>$CapitalInsoluto</td>
			<td>$Interesdevengado</td>
			<td>$FechaDeMvto</td>
			<td>$EstatusCredito</td>
			<td>$MontoGarantia</td>
			<td>SI</td>
			<td>SI</td>
			<td>NO</td>
			<td>SI</td>
			<td>$Hipoteca</td>
			<td>$HipotecaGravamen</td>
			<td>$HipotecaSeguro</td>
			<td>$HipotecaActualizado</td>
			<td>$HipotecaValuacion</td>
			<td>$HipotecaMonto</td>
			<td>0</td>
			<th>$dias_vencidos</th>
			<th>" . $VI_Autorizado[$rw["tipo_autorizacion"]] . "</th>
			<th>" . $VATipoIngreso[ $rw["tipoingreso"] ] . "</th>
			<th>" . $VDBEstatus[ $rw["estatus_actual"] ] . "</th>
			<th>" . $rw["descripcion_tipoconvenio"] . "</th>
			<th>" . $arrOficiales[ $rw["oficial_credito"] ] . "</th>
			<th>" . $CausaDeMora . "</th>
		</tr>
		";
	}

if ($input!=OUT_EXCEL) {

/*	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report32.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
$html = new cHTMLObject("CEDULA DE DATOS DEL INFORME MENSUAL A LA FEDERACION");
$html->addCSS("../css/reporte.css");
echo $html->getHEAD();
	echo "<html>
	<body>
	<table>
	<tbody>
	$tdHeader
	$tdBody
	</tbody>
	</table>
	</body>
	</html>
	";
} else {
  $filename = "export_from_" . date("YmdHi") . "_to_uid-" .  $iduser . ".xls";
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo "
	<table>
	<tbody>
		$tdHeader
		$tdBody
	</tbody>
	</table>
	";

}
if ( MODO_DEBUG == true ){
	//Graba los Mensages del LOG y cierra el Archivo
	@fwrite($URIFil, $msg);
	@fclose($URIFil);
	//echo "<a href=\"../utils/download.php?type=txt&download=$aliasFils&file=$aliasFils\" target=\"_blank\" class='boton'>Descargar Archivo de EVENTOS</a>";
}
?>

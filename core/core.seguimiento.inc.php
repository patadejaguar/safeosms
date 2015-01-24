<?php
/**
 * Core Captacion File
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 *  Core Captacion File
 * 		10/04/2008 Iniciar Funcion de Notificaciones 360
 * 		29/04/2008 Termino de efectuar lamadas No 360
 */
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");
//include_once("../libs/sql.inc.php");
include_once("core.riesgo.inc.php");
include_once("core.common.inc.php");
include_once("core.html.inc.php");



//=====================================================================================================
function setNotificacionesDiariasCreditos360($fecha_operacion = false){

	if ($fecha_operacion == false){
		$fecha_operacion	= fechasys();
	}
	$msg	= "====================== GENERAR_NOTIFICACIONES_POR_CREDITOS_360 \r\n";
	//Generar Notificaciones de creditos por Vencer en los proximos 10 dias
	$msg	.= "====================== OMITIDOS \r\n";
	return $msg;
}
function setAvisosPorCreditos360($fecha_operacion = false){


if ($fecha_operacion == false){
	$fecha_operacion	= fechasys();
}
$sucursal		= getSucursal();
$fecha			= sumardias($fecha_operacion, 1);

$msg	= "====================== GENERAR_AVISOS_POR_CREDITOS_360 \r\n";
//Generar Notificaciones de creditos por Vencer en los proximos 10 dias
$msg	= "====================== GENERAR_AVISOS_POR_CREDITOS_NO_360 \r\n";
$msg	.= "====================== GENERAR AVISOS CON " . DIAS_A_ESPERAR_POR_NOTIFICACION . " DIAS DE ANTICIPACION \r\n";
$msg	.= "====================== GENERAR AVISOS PARA EL " . getFechaLarga(sumardias($fecha_operacion, DIAS_A_ESPERAR_POR_NOTIFICACION)) . "\r\n";
$msg	.= "\tSocio\tCredito\tObservaciones\r\n";



$sql = "SELECT
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`periocidad_de_pago`,
	`creditos_solicitud`.`grupo_asociado`,
	`creditos_solicitud`.`oficial_seguimiento`
FROM
	`creditos_solicitud` `creditos_solicitud`
WHERE
	(`creditos_solicitud`.`sucursal` ='$sucursal') AND
	(`creditos_solicitud`.`saldo_actual` >= " . TOLERANCIA_SALDOS . ") AND
	(`creditos_solicitud`.`fecha_vencimiento` = DATE_ADD('$fecha_operacion', INTERVAL " . DIAS_A_ESPERAR_POR_NOTIFICACION . " DAY))
	AND
	(`creditos_solicitud`.`periocidad_de_pago` = 360)";


$rs = mysql_query($sql, cnnGeneral());
	while($rw = mysql_fetch_array($rs)){
		$socio			= $rw["numero_socio"];
		$credito		= $rw["numero_solicitud"];
		$grupo			= $rw["grupo_asociado"];
		$hora			= "10:00";
		$oficial		= $rw["oficial_seguimiento"];
		$observaciones	= "AVISOS AUTOMATICOS DE FINAL DE PLAZO";
		$numero			= 0;

		$msg  		.= date("H:i:s") . "\t$socio\t$credito\tAgregando Aviso al Oficial $oficial \r\n";
		setNuevaNotificacion($socio, $credito, $grupo, $numero, $fecha, $hora, $observaciones, $oficial, "pago_unico");
	}

	return $msg;

}
function setNotificacionesDiariasCreditosNo360(){
$msg	= "====================== GENERAR_NOTIFICACIONES_POR_CREDITOS_NO_360 \r\n";

}
/**
 * Genera Llamadas por Creditos Morosos y Vencidos fon vencimiento a FINAL DE PLAZO
 *
 * @param date $fecha_operacion
 * @return string Mensaje del Log
 */
function setLlamadasDiariasCreditos360($fecha_operacion){
	//TODO: Terminar esta canija funcion
	$msg	= "====================== GENERAR_LLAMADAS_POR_CREDITOS_360 \r\n";
	$msg	.= "====================== SE OMITEN \r\n";
	return $msg;
}
/**
 * Funcion que agrega una Llamada por Credito con VENCIMIENTO EN PAGO PERIODICO
 * @param $fecha_operacion	fecha en que agrega la llamada
 * @param $recibo			numero de recibo al que se agrega[si aplica]
 * @return string			Log de acciones
 */
function setLlamadasDiariasCreditosNo360($fecha_operacion, $recibo = 0){
$msg	= "====================== GENERAR_LLAMADAS_POR_CREDITOS_NO_360 \r\n";
$msg	.= "====================== GENERAR LLAMADAS POR PRIMERA PARCIALIDAD \r\n";
$msg	.= "====================== GENERAR LLAMADAS CON " . DIAS_DE_ANTICIPACION_PARA_LLAMADAS . " DIAS DE ANTICIPACION \r\n";
$msg	.= "====================== GENERAR LLAMADAS PARA EL " . getFechaLarga(sumardias($fecha_operacion, DIAS_DE_ANTICIPACION_PARA_LLAMADAS)) . "\r\n";
$msg	.= "\tSocio\tCredito\tObservaciones\r\n";
//obtener una muestra de la letra
/**
 * seleccionar
 */
$sucursal		= getSucursal();

$sql = "SELECT
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`tipo_operacion`,
	`operaciones_mvtos`.`fecha_afectacion`,
	`operaciones_mvtos`.`periodo_socio`,
	`operaciones_mvtos`.`afectacion_real`,
	`operaciones_mvtos`.`docto_neutralizador`
FROM
	`operaciones_mvtos` `operaciones_mvtos`

WHERE
	(`operaciones_mvtos`.`tipo_operacion` = 410) AND
	(`operaciones_mvtos`.`fecha_afectacion` = DATE_ADD('$fecha_operacion', INTERVAL " . DIAS_DE_ANTICIPACION_PARA_LLAMADAS . " DAY) ) AND
	(`operaciones_mvtos`.`periodo_socio` =1) AND
	(`operaciones_mvtos`.`docto_neutralizador` = 1)
	AND
	(`operaciones_mvtos`.`sucursal` ='$sucursal')
ORDER BY
	`operaciones_mvtos`.`fecha_afectacion`,
	`operaciones_mvtos`.`socio_afectado`";
//$msg	.= "$sql\r\n";

$rs = mysql_query($sql, cnnGeneral());
	while($rw = mysql_fetch_array($rs)){
		$socio		= $rw["socio_afectado"];
		$credito	= $rw["docto_afectado"];
		$oficial	= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
		$txt		= setNewLlamadaBySocio($socio, $credito, $fecha_operacion, date("H:i:s"), "LLAMADAS AUTOMATICAS : $recibo" );
		$msg  		.= date("H:i:s") . "\t" . $txt . "\r\n";
	}

	return $msg;
}
function setNewLlamadaBySocio($socio, $solicitud, $fecha, $hora,
								$observaciones, $oficial = false){
	$xFecha		= new cFecha(0, $fecha);
	$x			= array();
	$fechaIn	= $xFecha->setRestarDias(DIAS_DE_INTERVALO_POR_LLAMADAS);
	$fechaFi	= $xFecha->setSumarDias(DIAS_DE_INTERVALO_POR_LLAMADAS);
	$msg		= "";
	$sqlL_Ant	= "SELECT
					COUNT(`seguimiento_llamadas`.`idseguimiento_llamadas`) AS 'pendientes',
					MAX(`seguimiento_llamadas`.`fecha_llamada`) AS 'por_aplicar'
				FROM
					`seguimiento_llamadas` `seguimiento_llamadas`
				WHERE
					(`seguimiento_llamadas`.`numero_solicitud` ='$solicitud') AND
					(`seguimiento_llamadas`.`estatus_llamada` ='pendiente')
					AND
					((`seguimiento_llamadas`.`fecha_llamada` >='$fechaIn')) AND
					(`seguimiento_llamadas`.`fecha_llamada` <='$fechaFi')
				GROUP BY
					`seguimiento_llamadas`.`numero_solicitud`
				";
	$DLAnt		= obten_filas($sqlL_Ant);
	$existentes	= $DLAnt["pendientes"];
	//Si las llamadas existen: ACTUALIZAR
	if ( isset($existentes) AND ($existentes > 0) ){
		//actualizar llamadas Anteriores todavia: PENDIENTES
		$sqlCA 		= "UPDATE seguimiento_llamadas
	    				SET fecha_llamada = '$fecha'
	    				WHERE
						(`seguimiento_llamadas`.`numero_solicitud` ='$solicitud') AND
						(`seguimiento_llamadas`.`estatus_llamada` ='pendiente')
						AND
						((`seguimiento_llamadas`.`fecha_llamada` >='$fechaIn')) AND
						(`seguimiento_llamadas`.`fecha_llamada` <='$fechaFi') ";
		$x = my_query($sqlCA);
		if ($x["stat"] == false ){
			$msg		= "$socio\t$solicitud\tERROR!\tLa llamada para el dia $fecha no fue actualizada ";
		} else {
			$msg		= "$socio\t$solicitud\tSUCESS!\tLa llamada para el dia $fecha fue actualizada ";
		}
	} else {
		$xSocio		= new cSocio($socio);
		$xSocio->init();
		$DSocio		= $xSocio->getDatosInArray();

		$grupo		= $DSocio["grupo_solidario"];

		$DDom		= $xSocio->getDatosDomicilio(99);
		$sucursal	= getSucursal();
		$eacp		= EACP_CLAVE;
		if ($oficial == false){
			$SqlSol		= "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$solicitud";
			$DSol		= obten_filas($SqlSol);
			$oficial	= $DSol["oficial_seguimiento"];
		}
		$telefono_fijo		= $DDom["telefono_residencial"];
		$telefono_movil		= $DDom["telefono_movil"];

		$sql = "INSERT INTO seguimiento_llamadas
							(numero_socio,	numero_solicitud, deuda_total,
							telefono_uno, telefono_dos,
							fecha_llamada, hora_llamada,
							observaciones, estatus_llamada,
							oficial_a_cargo, sucursal,
							eacp, grupo_relacionado)
	    			VALUES
							($socio, $solicitud, 0,
							'$telefono_fijo', '$telefono_movil',
							'$fecha', '$hora',
							'$observaciones', 'pendiente',
							$oficial, '$sucursal',
							'$eacp', $grupo)";
	        //evaluar fecha, si es inhabil o domingo se ignora
		$x = my_query($sql);
		if ($x["stat"] == false ){
			$msg		= "$socio\t$solicitud\tERROR!\tLa llamada para el dia $fecha no fue CREADA ";
		} else {
			$msg		= "$socio\t$solicitud\tSUCESS!\tLa llamada para el dia $fecha fue CREADA ";
		}
		//Eliminar Variables
		unset($DDom);
		unset($DSocio);
		unset($DSol);
	}
	return $msg;
}
/**
 * Funcion que devuelve una tabla en HTML representativo del compromiso
 * @param       $filter         Opcional: Filtro Sql
 * @return      string          Tabla con Datos del Compromiso
 * */
function getFichaCompromiso($filter = ""){

$sql = "SELECT socios.codigo, socios.nombre,
	seguimiento_compromisos.tipo_compromiso,
	seguimiento_compromisos.anotacion,
	seguimiento_compromisos.idseguimiento_compromisos AS 'id',
	seguimiento_compromisos.oficial_de_seguimiento,
	seguimiento_compromisos.tipo_compromiso,
	seguimiento_compromisos.credito_comprometido,
	seguimiento_compromisos.idseguimiento_compromisos,
	seguimiento_compromisos.estatus_compromiso,
	seguimiento_compromisos.fecha_vencimiento,
	socios.grupo

	FROM socios, seguimiento_compromisos

	WHERE seguimiento_compromisos.socio_comprometido=socios.codigo
	$filter ";

	$rs = mysql_query($sql, cnnGeneral());
	$exoFrm = "";

	while ($rw = mysql_fetch_array($rs)){
		$oficial_a_cargo = elusuario($rw[5]);
		$imgP = vIMG_PATH;
		$notes = "";

			$CTipo		= $rw["tipo_compromiso"];
			$credito	= $rw["credito_comprometido"];
			$dia		= $rw["fecha_vencimiento"];
			$sep		= STD_LITERAL_DIVISOR;
			$socio		= $rw["codigo"];
			$grupo		= $rw["grupo"];

			switch($CTipo){
				case "promesa_de_pago";
				/**
				 * Buscar un posible Pago
				 */
				 $sqlPP = "SELECT idoperaciones_recibos AS 'recibo'

							FROM operaciones_recibos

						WHERE

							fecha_operacion>=DATE_SUB('$dia', INTERVAL 5 DAY)
							AND
								fecha_operacion<=DATE_ADD('$dia', INTERVAL 5 DAY)
							AND
								docto_afectado='$credito'
					AND tipo_docto = 2
					LIMIT 0,1";
					$DPP = obten_filas($sqlPP);
					$recibo = $DPP["recibo"];
					if ( isset($recibo) ){
						$notes = "<a class='button' onclick='getConsultaRecibo($recibo)'>Posible Cumplimiento del Compromiso con el recibo $recibo</a>";
					}
				break;

				default:
				break;
			}

			$exoFrm = $exoFrm .	"
			<fieldset>
			<legend>| Compromiso de $rw[6] Num. $rw[8] |</legend>
					<table align=\"center\" width=\"80%\" id=\"tblC@$rw[8]\">
					<tbody>
						 <tr>
							<th class='izq'>Clave de Persona</th> <td>$rw[0]</td>
						</tr><tr>
							<th class='izq'>Nombre Completo</th> <td>$rw[1]</td>
						</tr><tr>
							<th class='izq'>Numero de Credito</th> <td>$rw[7]</td>
						</tr><tr>
							<th class='izq'>Oficial a Cargo</th> <td>$oficial_a_cargo</td>
						</tr><tr>
							<th class='izq'>Tipo de Compromiso</th><td>$rw[6]</td>
						</tr><tr>
							<th class='izq'>Detalles</th><td>$rw[3]</td>
						</tr>
						<tr>
							<td colspan='2'>$notes</td>
						</tr>
						<tr>
						<td colspan=\"2\">
							<table align=\"center\">
								<tbody>
									<tr>
										<td></td>
										<td onclick=\"rptLlamadas('$socio|$credito')\"><img src=\"$imgP/seguimiento/llamadas.rpt.png\" alt=\"Reporte de Llamadas\" />&nbsp; Reporte de Llamadas</td>
										<td onclick=\"rptCompromisos('" . $rw["codigo"] . "|" . $rw["credito_comprometido"] . "')\"><img src=\"$imgP/seguimiento/stock_add-bookmark.png\" alt=\"Reporte de compromisos\" />&nbsp; Reporte de Compromisos</td>
										<td onclick=\"rptNotificaciones('" . $rw["codigo"] . "|" . $rw["credito_comprometido"] . "')\"><img src=\"$imgP/seguimiento/notificaciones.rpt.png\" alt=\"Reporte de Notificaciones\" />&nbsp; Reporte de Notificaciones</td>
									</tr>
									<tr>
										<td onclick=\"addLlamadas('$sep$socio$sep$credito')\">
										<img src=\"$imgP/seguimiento/stock_landline-phone.png\" alt=\"Agregar llamadas\" />&nbsp; Agregar Llamadas</td>
										<td onclick=\"addCompromisos('$rw[0]|$rw[7]|" . $rw["fecha_vencimiento"] . "')\">
										<img src=\"$imgP/seguimiento/stock_edit-bookmark.png\" alt=\"Agregar Compromiso\" />&nbsp; Agregar Compromiso</td>
										<td onclick=\"addMemo('7$sep$socio$sep$credito$sep$grupo$sep')\">
										<img src=\"$imgP/seguimiento/stock_insert-note.png\" alt=\"Agregar Memo\" />&nbsp; Agregar Memo</td>
										<td onclick=\"addNotificacion('s=$socio&c=$credito&g=$grupo&t=0')\">
										<img src=\"$imgP/seguimiento/aviso.new.png\" alt=\"Agregar Aviso de Pago\" />&nbsp; Agregar Aviso de pago</td>									</tr>
									<tr>
										<td onclick=\"addNotificacion('s=$socio&c=$credito&g=$grupo&t=1')\"><img src=\"$imgP/seguimiento/notif1.png\" alt=\"\" />Agregar 1a Notificacion</td>
										<td onclick=\"addNotificacion('s=$socio&c=$credito&g=$grupo&t=2')\"><img src=\"$imgP/seguimiento/notif2.png\" alt=\"\" />Agregar 2a Notificacion</td>
										<td onclick=\"addNotificacion('s=$socio&c=$credito&g=$grupo&t=3')\"><img src=\"$imgP/seguimiento/notif3.png\" alt=\"\" />Agregar 3a Notificacion</td>
										<td onclick=\"addNotificacion('s=$socio&c=$credito&g=$grupo&t=4')\"><img src=\"$imgP/seguimiento/notife.png\" alt=\"\" />Agregar Not. Extrajudicial</td>
									</tr>
									<tr>
										<td colspan='4'>
											<fieldset id='fs-" . $rw["id"] .  "'>
												<legend>Cumplimiento</legend>
													<a onclick=\"setCumplido('" . $rw["id"] .  "')\" class='button'>
														<img src=\"$imgP/seguimiento/green_dot.png\" />
														Cumplido</a>
													<a onclick=\"setCancelado('" . $rw["id"] .  "')\" class='button'>
														<img src=\"$imgP/seguimiento/yellow_dot.png\" />
														Cancelado</a>
													<a onclick=\"setVencido('" . $rw["id"] .  "')\" class='button'>
														<img src=\"$imgP/seguimiento/red_dot.png\" />
														Vencido</a>

											</fieldset>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tbody>
					</table>
					</fieldset>
					\n";

	}
	@mysql_free_result($rs);
	return $exoFrm;
}
function setNuevaNotificacion($socio, $solicitud, $grupo = 99, $numero,
							$fecha, $hora, $observaciones = "",
							$oficial = false, $tipo_de_credito = "planes"){
	$arrProc = array(0 => "primera_notificacion",
					1 => "segunda_notificacion",
					2 => "tercera_notificacion",
					3 => "requerimiento_extrajudicial",
					4 => "citatorio_extrajudicial"
					);
	if ($oficial == false){
		$SqlSol		= "SELECT * FROM creditos_solicitud WHERE numero_solicitud = $solicitud";
		$DSol		= obten_filas($SqlSol);
		$oficial	= $DSol["oficial_seguimiento"];
	}
	$Dom		= getSocioDomicilio($socio); //getDatosDomicilio($socio, 99);
	$sucursal	= getSucursal();
	$eacp		= EACP_CLAVE;

	$vencimiento	= sumardias($fecha, DIAS_A_ESPERAR_POR_NOTIFICACION);
	//'pendiente','efectuado','comprometido','cancelado','vencido'
	$sqliNot = "INSERT INTO seguimiento_notificaciones(socio_notificado, numero_solicitud, numero_notificacion,
	fecha_notificacion, oficial_de_seguimiento, fecha_vencimiento, procedimiento_proximo,
	capital, interes, moratorio, otros_cargos, total,
	observaciones, estatus_notificacion, domicilio_completo, tipo_credito,
	sucursal, eacp, grupo_relacionado)
    VALUES($socio, $solicitud, $numero,
    '$fecha', $oficial, '$vencimiento', '" . $arrProc[$numero] . "',
    0, 0, 0, 0, 0,
    '$observaciones', 'pendiente', '$Dom', '$tipo_de_credito',
    '$sucursal', '$eacp', $grupo)";

    $x		= my_query($sqliNot);
    return $x["stat"];
}
function setLlamadasDiariasPorMora ($fecha_operacion, $recibo = 0){
$sucursal		= getSucursal();
$fecha			= sumardias($fecha_operacion, DIAS_DE_ANTICIPACION_PARA_LLAMADAS);

$msg	 = "====================== GENERAR_LLAMADAS_POR_CREDITOS_NO_360 \r\n";
$msg	.= "====================== GENERAR LLAMADAS POR CREDITOS MOROSOS_Y_VENCIDOS \r\n";
$msg	.= "====================== GENERAR LLAMADAS PARA EL " . getFechaLarga($fecha) . "\r\n";
$msg	.= "\tSocio\tCredito\tObservaciones\r\n";


$sql = "SELECT
	`creditos_solicitud`.*
FROM
	`creditos_solicitud` `creditos_solicitud`
WHERE
	(
		(`creditos_solicitud`.`estatus_actual` =30)
		OR
		(`creditos_solicitud`.`estatus_actual` =20)
	)
	AND
	(`creditos_solicitud`.`sucursal` ='$sucursal')
	AND
	(`creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . ")
	AND
	(`creditos_solicitud`.`periocidad_de_pago` != 360)
	";

	$fecha_operacion 	= sumardias($fecha_operacion, 1);	//ma�ana
	$rs 				= mysql_query($sql, cnnGeneral());
	while($rw = mysql_fetch_array($rs)){
		$socio		= $rw["numero_socio"];
		$credito	= $rw["numero_solicitud"];
		$oficial	= $rw["oficial_seguimiento"];
		$hora		= "8:00";
		$txt		= setNewLlamadaBySocio($socio, $credito, $fecha, $hora, "LLAMADAS DIARIAS AUTOMATICAS : $recibo" );
		$msg  		.= date("H:i:s") . "\t" . $txt . "\r\n";
	}

	return $msg;
}

class cLlamada{
	private $mIncludeVencidas	= false;
	private $mIncludeEfectuadas	= false;
	private $mIncludeCanceladas	= false;
	private $mOficialDeCredito	= false;
	private $mLimitRecords		= 20;

	function __construct(){

	}
	function setLimitRecords($records = 12){
		$this->mLimitRecords		= $records;
	}
	function setIncludeVencidas($Include = true){
		$this->mIncludeVencidas		= $Include;
	}
	function setIncludeCanceladas($Include = true ){
		$this->mIncludeCanceladas	= $Include;
	}
	function setIncludeEfectuadas($Include = true ){
		$this->mIncludeEfectuadas	= $Include;
	}
	function setCancelarLlamadasAnteriores($fecha = false){
		if ($fecha == false){
			$fecha = fechasys();
		}
		$xFecha = new cFecha(0, $fecha);
		$fecha	= $xFecha->setRestarDias(DIAS_DE_INTERVALO_POR_LLAMADAS, $fecha);
			//cancelar llamadas Anteriores
		$sqlCA 		= "UPDATE seguimiento_llamadas
	    				SET estatus_llamada='cancelado'
	    				WHERE
							(estatus_llamada='pendiente')
							AND (fecha_llamada < '$fecha') ";
		my_query($sqlCA);
	}
	function getLlamadas($fecha_inicial = false, $fecha_final = false, $De = 0 ){
		if ( $this->mOficialDeCredito	== false ){
			$this->mOficialDeCredito = $_SESSION["log_id"];
		}
		if ( $fecha_inicial == false ){
			$fecha_inicial 		= fechasys();
		}
		if ( $fecha_final == false ){
			$fecha_final		= $fecha_inicial;
		}

		$oficial_de_credito		= $this->mOficialDeCredito;
		$ImgP					= vIMG_PATH;

		$ByNoVenc				= " AND (`seguimiento_llamadas`.`estatus_llamada` !='vencido')";
		$ByNoCanc				= " AND (`seguimiento_llamadas`.`estatus_llamada` !='cancelado')";
		$ByNoEfec				= " AND (`seguimiento_llamadas`.`estatus_llamada` !='efectuado')";
		$ByOficial				= " AND (`seguimiento_llamadas`.`oficial_a_cargo` = $oficial_de_credito) ";
		$ByFecha				= " ( (`seguimiento_llamadas`.`fecha_llamada` >= '$fecha_inicial' )
									AND
									(`seguimiento_llamadas`.`fecha_llamada` <= '$fecha_final' ) ) ";

		if ( MODO_DEBUG == true) {
			$ByOficial			= "";
		}

		if ( $this->mIncludeVencidas == true ){
			$ByNoVenc			= "";
		}
		if ( $this->mIncludeCanceladas == true ){
			$ByNoCanc			= "";
		}
		if ( $this->mIncludeEfectuadas == true ){
			$ByNoEfec			= "";
		}

		$sql = "SELECT
		`socios`.`codigo`,
		`socios`.`nombre`,
		`seguimiento_llamadas`.`idseguimiento_llamadas` AS `control`,
		`seguimiento_llamadas`.`numero_solicitud`,
		`seguimiento_llamadas`.`telefono_uno`,
		`seguimiento_llamadas`.`telefono_dos`,
		`seguimiento_llamadas`.`fecha_llamada`,
		`seguimiento_llamadas`.`hora_llamada`,
		`seguimiento_llamadas`.`observaciones`,
		`seguimiento_llamadas`.`estatus_llamada`,
		`seguimiento_llamadas`.`oficial_a_cargo`,
		`seguimiento_llamadas`.`grupo_relacionado`,
		`seguimiento_llamadas`.`deuda_total`,
		`seguimiento_llamadas`.`observaciones`
	FROM
		`socios` `socios`
			INNER JOIN `seguimiento_llamadas` `seguimiento_llamadas`
			ON `socios`.`codigo` = `seguimiento_llamadas`.`numero_socio`
	WHERE

		$ByFecha
		$ByOficial
		$ByNoCanc
		$ByNoEfec
		$ByNoVenc
	ORDER BY
		`socios`.`codigo`,
		`seguimiento_llamadas`.`fecha_llamada`,
		`seguimiento_llamadas`.`hora_llamada`,
		`seguimiento_llamadas`.`idseguimiento_llamadas`
	LIMIT $De," . $this->mLimitRecords;

				$td		= "";
				$rs = mysql_query($sql, cnnGeneral());

				while($rw = mysql_fetch_array($rs)){
					$control	= $rw["control"];
					$socio		= $rw["codigo"];
					$nombre		= htmlentities($rw["nombre"]);
					$credito	= $rw["numero_solicitud"];
					$estatus	= $rw["estatus_llamada"];
					$grupo		= $rw["grupo_relacionado"];
					$hora		= $rw["hora_llamada"];
					$tel1		= $rw["telefono_uno"];
					$tel2		= $rw["telefono_dos"];

					$select		= "	<select id='ids-$control' name='s-$control' onchange=\"jsSetAction($control)\">
										<optgroup label='Acciones'>
											<option value='set-none' selected='true'>Seleccione una Operacion...</option>
											<option value='set-cumplido'>Marcar como Efectuada</option>
											<option value='set-cancelado'>Marcar como Cancelada</option>
											<option value='set-notes'>Agregar Resultados de la Llamada</option>
											<option value='set-vivienda'>Actualizar Datos de Vivienda</option>
										</optgroup>
										<optgroup label='Herramientas'>
											<option value='set-edit'>Editar Informacion de la LLamada</option>
											<option value='add-compromiso'>Agregar Compromiso</option>
											<option value='add-llamada'>Agregar Llamada</option>
											<option value='add-memo'>Agregar Memo</option>
											<option value='add-notif-1'>Agregar Notificacion 1a</option>
											<option value='add-notif-2'>Agregar Notificacion 2a</option>
											<option value='add-notif-3'>Agregar Notificacion 3a</option>
											<option value='add-notif-e'>Agregar Notificacion Extrajudicial</option>
										</optgroup>
										<optgroup label='Informacion'>
											<option value='info-moral'>Obtener Informacion Moral</option>

											<option value='info-llamadas'>Obtener Reporte de llamadas</option>
											<option value='info-compromisos'>Obtener Reporte de Compromisos</option>
											<option value='info-notificaciones'>Obtener Reporte de Notificaciones</option>
											<option value='info-creditos'>Obtener Estado de Cuenta de Creditos</option>
										</optgroup>
									</select>";
					switch ($estatus){
						case "vencido":
							$select		= "";
							break;
						case "efectuado":
							$select		= $rw["observaciones"];
							break;
						case "cancelado":
							$select		= $rw["observaciones"];
							break;
						default:
							break;
					}
					$td .= "	<tr id=\"tr-$control\">
								<td  class=\"$estatus\" >$socio<input type='hidden' id='socio-$control' value='$socio' /><br />
									$credito<input type='hidden' id='credito-$control' value='$credito' />
									<!-- $control -->
									<input type='hidden' id='grupo-$control' value='$grupo' /></td>
								<td  class=\"$estatus\">$nombre<br />
									<a>" . $tel1 . " &nbsp;&nbsp;|&nbsp;&nbsp; " . $tel2 . "</a></td>
								<td class=\"$estatus\">$hora</td>
								<td id=\"td-$control\"  class=\"$estatus\" >
									$select
								</td>

							</tr>";
				}

				return "<table width='100%' id='tbl-id'>
					<tbody>
						<tr>
							<th width=\"10%\">
								Socio<br />
								Credito
							</th>
							<th width=\"50%\">
								Nombre<br />
								Telefono Fijo &nbsp;&nbsp;|&nbsp;&nbsp; Telefono Movil
							</th>
							<th>Hora</th>
							<th width=\"30%\">
								Herramientas
							</th>
						</tr>
						$td
					</tbody>
					</table>";
	}
}


class cAlertasDelSistema {
	private $mFecha		= "";
	private $mMessages	= "";
	private $mAmails		= array();
	private $mAOficiales	= array();
	private $mAEmpresas		= array();
	private $mAPersonas		= array();
	private $mObProgAv		= null;
	private $mContrato		= null;
	private $mTipoCont		= null;
	private $mArrVars		= array();

	function __construct($fecha = false){
		$this->mFecha	= ($fecha == false) ? fechasys() : $fecha;

	}
	function getMessages($put = OUT_TXT){ $xH	= new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function getDay($fecha, $periocidad, $variable){
		$valor_dia	= 24*60*60;
		$fechaMark	= strtotime($fecha);
		$diaMark	= date("N", $fechaMark);
		//$diasSem	= array();

		$variable	= strtoupper($variable);
		$DVars		= explode(" ", $variable);

		$fechaDev	= false;
		$diff		= 0;

		switch($periocidad){
			case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
				if( in_array("LUNES", $DVars) !== false){
					$fechaDev = date("Y-m-d", strtotime("next Monday", $fechaMark));
				}
				if( in_array("MARTES", $DVars) !== false){
					$fechaDev = date("Y-m-d", strtotime("next Thursday", $fechaMark));
				}
				if( in_array("MIERCOLES", $DVars) !== false){
					$fechaDev = date("Y-m-d", strtotime("next Wendesday", $fechaMark));
				}
				if( in_array("JUEVES", $DVars) !== false){
					$fechaDev = date("Y-m-d", strtotime("next Tuesday", $fechaMark));
				}
				if( in_array("VIERNES", $DVars) !== false){
					$fechaDev = date("Y-m-d", strtotime("next Friday", $fechaMark));
				}
				if( in_array("SABADO", $DVars) !== false){
					$fechaDev = date("Y-m-d", strtotime("next Saturday", $fechaMark));
				}
				if( in_array("DOMINGO", $DVars) !== false){
					$fechaDev = date("Y-m-d", strtotime("next Sunday", $fechaMark));
				}
				break;
			default:
				break;
		}
	}

	function setProcesarProgramacion($id, $arrVars = false, $data = false, $fecha = false ){
		$xF					= new cFecha();
		$xT					= new cTipos();
		$xLog				= new cCoreLog();
		$arrVars			= ($arrVars == false) ? $this->mArrVars : $arrVars;
		
		//idprograma, nombre_del_aviso, forma_de_creacion, programacion, destinatarios, microformato, tipo_de_medios, intent_check, intent_command 
		$sql				= "SELECT *	FROM sistema_programacion_de_avisos WHERE idprograma=$id LIMIT 0,1";
		$d					= ($data == false) ? obten_filas($sql) : $data;
		$mOb				= new cSistema_programacion_de_avisos(); $mOb->setData($d);
		$emails				= array();
		$tels				= array();
		$enviar				= false;
		$fecha				= ($fecha == false) ? fechasys() : $fecha;
		$nombredia			= $xF->getDayName();
		$diadelmes			= date("j", $xF->getInt());
		
		$EnviarNota			= true;
		$EnviarMail			= true;
		$EnviarSMS			= false;

		$xLoc						= new cLocal();
		//procesar cuerpo del contenido
		$arrVars["fecha"]					= (!isset($arrVars["fecha"])) ? $fecha : $arrVars["fecha"];
		$arrVars["fecha_dia_siguiente"]		= $xF->setSumarDias(1, $fecha);
		$arrVars["fecha_inicio_de_semana"]	= $xF->getFechaDeInicioDeSemana($fecha);
		$arrVars["fecha_inicio_de_mes"]		= $xF->getDiaInicial($fecha);
		//Dia de la semana inicial
		$arrVars["hora"]					= date("H");
		$arrVars["usuario"]					= $xLoc->getNombreUsuario();
		$arrVars["clave_de_usuario"]		= (!isset($arrVars["clave_de_usuario"])) ? getUsuarioActual() : $arrVars["clave_de_usuario"]; 
		

		//interpretar DIA
		
		if( strtoupper($mOb->forma_de_creacion()->v()) == SYS_ALERTA_POR_EVENTO  ){
			$enviar	= true;
		} else {
			//if($mOb->programacion())
			$programacion 	=  strtoupper($mOb->programacion()->v());
			$periodo		= explode(":", $programacion);
			//INTERPRETAR DIA
			if($periodo[0] == "PROGRAMACION_SEMANAL"){
				if(strpos($nombredia, $programacion) !== false ){
					$enviar			= true;
				}
			} else if($periodo[0] == "PROGRAMACION_DIARIA"){
				$enviar				= true;
			} else {
				if(isset($periodo[1])){
					$dias		= explode(",", $periodo[1]);
					foreach ($dias as $dias => $iddia){
						$mes	= $xF->mes();
						$anno	= $xF->anno();
						if( date("Y-m-d", strtotime( "$anno-$mes-$iddia" ) ) == $fecha){	$enviar		= true;		}
					}
				}
			}
		}
		//1.- extraer emails
		$destinatarios	= explode("|", $mOb->destinatarios()->v() );
		foreach ($destinatarios as $key => $cnt){
			if(trim($cnt) != ""){
				//1.1 Desfragmentar destinos
				$DS		= explode(":", $cnt);
				$mdestino	= (isset($DS[0])) ? strtoupper( $DS[0]) : "";
				
				switch ( $mdestino ) {
					case "OFICIALES":
						if(isset($DS[1])){
							$oficiales		= explode(",", $DS[1]);
							foreach ($oficiales AS $ofc => $ofkey){
								$xOf		= new cOficial($ofkey); $xOf->init();
								$mail		= $xOf->getEmail();
								$emails[]	= $mail;
								$xLog->add("OK\tOFICIAL\tAgregar mail $mail  \r\n", $xLog->DEVELOPER);
							}
						}
						break;
					case "EMPRESAS":
						if(isset($DS[1])){
							$empresas		= explode(",", $DS[1]);
							foreach ($empresas AS $emp => $empkey){
								$xEmp		= new cEmpresas($empkey); $xEmp->init();
								//$mail		= $xEmp->getEmailsDeEnvio();
								//$emails[]	= $mail;
								$emails		= array_merge($emails, $xEmp->getEmailsDeEnvio());
								$xLog->add("OK\tEMPRESAS\tAgregar mail de la empresa $empkey  \r\n", $xLog->DEVELOPER);
							}
						}
						break;
					case "PERSONAS":
						if(isset($DS[1])){
							$personas		= explode(",", $DS[1]);
							foreach ($personas AS $ofc => $ofkey){
								$xSoc		= new cSocio($ofkey); $xSoc->init();
								$mail		= $xSoc->getCorreoElectronico();
								$emails[]	= $mail;
								$xLog->add("OK\tPERSONA\tAgregar mail $mail  \r\n", $xLog->DEVELOPER);
								if($xT->cNumeroTelefonico($xSoc->getTelefonoPrincipal()) != false){
									$EnviarSMS		= true;
									$tels[]			= $xT->cNumeroTelefonico($xSoc->getTelefonoPrincipal()); 
								}
							}
						}
						break;
					case "CORREO":
						if(isset($DS[1])){
							$personas		= explode(",", $DS[1]);
							foreach ($personas AS $ofc => $ofkey){
								if (filter_var($ofkey, FILTER_VALIDATE_EMAIL)){ $emails[]	= $ofkey; }
								$xLog->add("OK\tCORREO\tAgregar mail $ofkey  \r\n", $xLog->DEVELOPER);
							}
						}
						break;
				}
				//1.1.1 Validar oficiales, empresas, personas
				//if(strpos("OFICIALES:", $needle))
			}
		}

		$this->mMessages			.= $xLog->getMessages();
		$texto						= $mOb->microformato()->v();
		foreach ($arrVars as $variable => $valor){
			$texto	= str_replace("{" . $variable . "}", $valor, $texto);
		}
		if($enviar == true){
			$url	= $mOb->intent_command()->v(OUT_TXT);
			//2 procesado del comando
			if(trim($url) == ""){
				$titulo		=  strtoupper($mOb->nombre_del_aviso()->v());
				$xNot		= new cNotificaciones();
				//enviar mail normal
				if($EnviarSMS == true){
					foreach ($tels as $pitm => $ptel){
						$xNot->sendSMS($ptel, $texto);
					}
				}
				if($EnviarMail == true){
					
					foreach ($emails as $itm => $pmail){
						$xNot->sendMail($titulo, $texto, $pmail);
					}
				}
			} else {
				//execute command
				//rpttesoreria/corte_de_caja.rpt.php?on=2014-5-3&off=2014-5-3&cajero=todas&dependencia=todas
				$smail	= "";
				$xHO	= new cHObject();
				
				
				foreach ($arrVars as $variable => $valor){
					$url	= str_replace("{" . $variable . "}", $valor, $url);
				}
								
				foreach ($emails as $id => $rmail){
					//$smail	.= ($smail == "") ? "email$id=$rmail" : "&email$id=$rmail";
					$smail	.= "&email$id=$rmail";
				}
				//Iniciar session en contexto &on=$fecha&off=$fecha
				$xSysUser		= new cSystemUser();
				$xSysUser->init();
				$url		= $url . $smail . "&ctx=" . $xSysUser->getCTX();	
				if(MODO_DEBUG == true){  setLog($url); }
				$xHO->navigate($url);
			}
		} else {
			$this->mMessages	.= "OK\tNo e envia el reporte\r\n";
		}	
		
		setLog($this->mMessages);
		$this->mObProgAv	= $mOb;
		//return $this->mObProgAv;
	}
	function getTipoDeProgramacion(){
		$arrPers		= array(
				"PROGRAMACION_DIARIA" => "Todos los dias",
				"PROGRAMACION_SEMANAL" => "Cada dia de la semana",
				"PROGRAMACION_LIBRE" => "Programacion Libre"
		);
		return $arrPers;
	}
	function getATipoDeEvento(){
		$arrPers		= array(
				SYS_ALERTA_POR_EVENTO => "Por evento",
				SYS_ALERTA_AL_CIERRE => "Al cierre del Dia"
				/*"FIN_DE_SEMANAL" => "Fin de semana",
				"FIN_DE_MES" => "Fin de Mes"*/
		);
		return $arrPers;
	}

	function setAgregarProgramacion($titulo , $contenido, $destinatarios, $programacion, $generado = SYS_ALERTA_AL_CIERRE, $medios = SYS_TODAS, $comando = "", $checking = ""){
		
		$xAv			= new cSistema_programacion_de_avisos();
		$xAv->idprograma( $xAv->query()->getLastID() );
		$xAv->destinatarios($destinatarios);
		$xAv->forma_de_creacion($generado);
		$xAv->intent_check($checking);
		$xAv->intent_command($comando);
		$xAv->microformato($contenido);
		$xAv->nombre_del_aviso($titulo);
		$xAv->programacion($programacion);
		$xAv->tipo_de_medios($medios);
		$ins	= $xAv->query()->insert();
		$ins->save();
		if(MODO_DEBUG == true){ $this->mMessages	.= $ins->getMessages(OUT_TXT); }
	}
	function setGenerarAlCierre($fecha = false){
		$fecha	= ($fecha == false) ? $this->mFecha : $fecha;
		$sql	= "SELECT * FROM `sistema_programacion_de_avisos` WHERE	(`sistema_programacion_de_avisos`.`forma_de_creacion` ='" . SYS_ALERTA_AL_CIERRE . "')";
		$alerta	= new cSistema_programacion_de_avisos();
		$mql	= new MQL();
		$rw		= $mql->getDataRecord($sql);
		foreach ($rw as $rows){
			$id		= $rows["idprograma"];
			$this->setProcesarProgramacion($id, array(), $rows, $fecha);
		}
		return $this->getMessages();
	}

	function formatearAviso($texto, $valores){
		/*
		 * Faltan {dias} para Entrega de Aviso
		*/
		foreach($valores as $clave => $valor){
			$texto		= str_replace("[$clave]", $valor, $texto);
		}
		$xH		= new cHObject();
		return $xH->Out($texto, OUT_TXT);
	}
	function getAvisos($tipo){  }
	function getDatAlertasPorEvento($evento){
		$mql	= new MQL();
		$ql		= new cSQLListas();
		$sql	= $ql->getListadoDeProgramacionAlertas(SYS_ALERTA_POR_EVENTO, strtoupper($evento));
		return $mql->getDataRecord( $sql );
	}
}



class cNotificaciones {
	private $mClase		= false;
	private $mSMS_uri	= 'http://bulksms.vsms.net/eapi/submission/send_sms/2/2.0';
	private $mSMS_limit	= 120;

	private $mSMS_pwd	= "";
	private $mSMS_usr	= "";
	private $mTitulo	= "";
	private $mMessages	= "";
	private $mOriginalM	= "";

	private $mCanal		= "";
	//private $mOnMail	= true;
	//private $mOnSMS		= false;
	//private $mOnLocal	= false;

	//a sistema
	//a sms
	//a email
	//a cloud message
	//todos disponibles
	function  __construct(){
		$this->mSMS_pwd		= SMS_PWD;
		$this->mSMS_usr		= SMS_USR;
	}
	function setCanal($canal){ $this->mCanal	= $canal; }
	function setTitulo($txt){ $this->mTitulo = $txt; }
	function send($mensaje, $email = false, $telefono = false, $usuario = false, $mensaje_corto = "", $canal = false){
		$msg			= "";
		$mensaje		= $this->cleanString($mensaje);
		$mensaje_corto	= $this->cleanString($mensaje_corto);
		$mensaje_corto	= ($mensaje_corto == "") ? substr($mensaje, 0, $this->mSMS_limit) : substr($mensaje_corto, 0, $this->mSMS_limit);
		if($email == false){
				
		} else {
			$msg	.= $this->sendMail($this->mTitulo, $mensaje, $email);
		}
		if($telefono == false){
				
		} else {
			$telefono	= "52" . $telefono;
			$msg		.= $this->sendSMS($telefono, $mensaje_corto);
		}
		if($usuario == false){
			
		} else {
			$xOf	= new cOficial($usuario);
			$socio	= DEFAULT_SOCIO;
			$docto	= DEFAULT_CREDITO;
			$msg	.= $xOf->addNote(AVISOS_TIPO_RECORDATORIO, $usuario, $socio, $docto, $mensaje_corto);
		}
		if($canal == false){
				
		} else {
			$this->mCanal	= $canal;
			$msg			.= 	$this->sendCloudMessage($mensaje_corto);
		}
		$this->mMessages	.= $msg;
		return $msg;
	}
	function sendSMS($telefono, $mensaje) {
		$pwd				= $this->mSMS_pwd;
		$user				= $this->mSMS_usr;
		$res				= "";
		/*	$url = 'http://bulksms.vsms.net/eapi/submission/send_sms/2/2.0';
	$msisdn = '44123123123';
	$data = 'username=your_username&password=your_password&message='.urlencode('Testing SMS').'&msisdn='.urlencode($msisdn);*/
		if(trim($pwd) == "" OR trim($user) == ""){
			$res			.= "ERROR\tError con las credenciales\r\n";		
		} else {
			$optional_headers 	= 'Content-type:application/x-www-form-urlencoded';
			$data 				= "username=" . $user . "&password=" . $pwd . "&message=".urlencode($mensaje).'&msisdn='.urlencode($telefono);
			$url				= $this->mSMS_uri;
			
			$params = array('http'      => array(
					'method'       => 'POST',
					'content'      => $data,
			));
			if ($optional_headers !== null) {
				$params['http']['header'] = $optional_headers;
			}
	
			$ctx 		= stream_context_create($params);
			$response 	= @file_get_contents($url, false, $ctx);
			if ($response === false) {
				$res	.= "ERROR\tProblem reading data from $url, No status returned\r\n";
				//
			} else {
				$res	.= $response;
				
			}
		}
		if(MODO_DEBUG == true){setLog($res);}
		return $res;
	}
	function sendMail($subject = "", $body = "", $to = "", $arrFile = false){
		$omsg		= "";
		if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
			
			
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			//Tell PHPMailer to use SMTP
			$mail->IsSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug  = 0;
			$mail->Timeout    = 10;
			if(MODO_DEBUG == true){ /*$mail->SMTPDebug  = 2;*/	}
			
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host       = ADMIN_MAIL_SMTP_SERVER;
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port       = ADMIN_MAIL_SMTP_PORT;
			//Set the encryption system to use - ssl (deprecated) or tls
			if(ADMIN_MAIL_SMTP_TLS != ""){
				$mail->SMTPSecure = ADMIN_MAIL_SMTP_TLS;//'tls';
			}
			//Whether to use SMTP authentication
			$mail->SMTPAuth   = true;
			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username   = ADMIN_MAIL;//EACP_MAIL;
			//Password to use for SMTP authentication
			$mail->Password   = ADMIN_MAIL_PWD;
			//Set who the message is to be sent from
			$mail->SetFrom(ADMIN_MAIL, 'S.A.F.E. OSMS System Alert');
			//Set an alternative reply-to address
			//$mail->AddReplyTo('replyto@example.com','First Last');
			//Set who the message is to be sent to
			$mail->AddAddress($to, 'SAFE-OSMS');
			//Set the subject line
			$mail->Subject = $subject;
			//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
			//$mail->MsgHTML(file_get_contents('contents.html'), dirname(__FILE__));
			$mxMsg		= "";
			$mxMsg		.= $body;
	
			$mail->MsgHTML($mxMsg);
			//Replace the plain text body with one created manually
			//$mail->AltBody = 'This is a plain-text message body';
			//Attach an image file
			if ($arrFile != false AND is_array($arrFile) ){
				if(isset($arrFile["archivo1"])){
					if(is_array($arrFile["archivo1"])){
						foreach ($arrFile as $archivos){
							$mail->AddAttachment($archivos["path"]);
						}
					}
					$mail->Timeout       =   80;
				} else {
					//$m->Attach($arrFile["path"], $arrFile["mime"], "inline");
					$mail->AddAttachment($arrFile["path"]);
					$mail->Timeout       =   60;
				}
			}
			//$mail->AddAttachment('images/phpmailer-mini.gif');
	
			//Send the message, check for errors
			if(!$mail->Send()) {
				$omsg	.= "ERROR\t$to\t" . $mail->ErrorInfo . "\r\n";
			} else {
				$omsg	.= "OK\tMensaje Enviado a $to con exito.\r\n";
			}
		} else {
			$omsg	.= "ERROR\tInvalid email $to\r\n";
		}
		if(MODO_DEBUG == true){ setLog($omsg); }
		return $omsg;
	}
	function sendCloudMessage($mensaje){
		$res		= "";

		$PConf			= new parseConfig();
		$APPLICATION_ID = $PConf::APPID;
		$REST_API_KEY 	= $PConf::RESTKEY;

		$url = 'https://api.parse.com/1/push';

		$data = array(
				'channel' => $this->mCanal,
				'expiry' => 1451606400,
				'data' => array(
						'alert' => $mensaje
				),
		);
		$_data = json_encode($data);
		$headers = array(
				'X-Parse-Application-Id: ' . $APPLICATION_ID,
				'X-Parse-REST-API-Key: ' . $REST_API_KEY,
				'Content-Type: application/json',
				'Content-Length: ' . strlen($_data),
		);

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $_data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		$result		= curl_exec($curl);
		$result		= json_decode($result);

		$res	.= "OK\tMensaje $mensaje enviado con exito\r\n";

		$this->mMessages	.= $res;
		return $res;
	}
	function getMessages($put = OUT_TXT){ $xH	= new cHObject(); return $xH->Out($this->mMessages, $put);	}

	function cleanString($cadena, $otros = false){
		$cleanArr	= array('/\s\s+/', '/(\")/', '[\\\\]', '/(\')/');
		if(is_array($otros)){
			$cleanArr	= array_merge($cleanArr,$otros);
		}
		$cadena 		= preg_replace($cleanArr, ' ', $cadena); //dob
		$cadena			= str_replace("WARN\t", " ", $cadena);
		$cadena			= str_replace("ERROR\t", " ", $cadena);
		$cadena			= str_replace("OK\t", " ", $cadena);
		$cadena			= str_replace("\r\n", ".", $cadena);
		
		return $cadena;
	}
}


class cSeguimiento {
	private $mPersona		= false;
	function __construct($persona = false){
		$this->mPersona	= $persona;
	}
	function addCompromiso($credito , $tipo, $notas, $fecha = false, $usuario = false){
		$socios	= $this->mPersona;
		
		$sqlIC = "INSERT INTO seguimiento_compromisos(socio_comprometido, oficial_de_seguimiento, fecha_vencimiento, hora_vencimiento, 
		tipo_compromiso, anotacion, credito_comprometido, estatus_compromiso, sucursal, eacp)
		VALUES($socio, $iduser, '$fecha', '$hora', '$tipo', '$anotacion', $credito, '$estatus', '$sucursal', '$eacp')";
		
		$ms		= my_query($sqlIC);		
	}
	function addLlamada(){
		
	}
	
}
?>

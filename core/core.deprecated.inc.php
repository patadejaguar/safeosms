<?php
/**
 * Core Common Deprecated File
 * @author Balam Gonzalez Luis Humberto
 * @version 3.0.01
 * @package common
 */
//=====================================================================================================
include_once ("go.login.inc.php");
include_once ("core.config.inc.php");
include_once ("core.common.inc.php");
include_once ("core.creditos.inc.php");
include_once ("core.creditos.utils.inc.php");

include_once ("core.operaciones.inc.php");
include_once ("core.operaciones.utils.inc.php");

include_once ("core.error.inc.php");
include_once ("entidad.datos.php");
include_once ("core.fechas.inc.php");
include_once ("core.security.inc.php");
include_once ("core.contable.inc.php");
include_once ("core.contable.utils.inc.php");
include_once ("core.db.inc.php");
include_once ("core.init.inc.php");

@include_once ("../libs/sql.inc.php");

function cnnGeneral(){
	//$estat = mysql_stat();
	
		$CNX = mysql_connect(WORK_HOST, USR_DB, PWD_DB);

		if (!$CNX) {
			//saveError(2, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], " No se puede Conectar :" . mysql_error($CNX) . "|Numero: " . mysql_errno($CNX) . "|connect: \n " . $_SESSION["current_file"]);
			syslog(E_ERROR, " No se puede Conectar :" . mysql_error($CNX) . "|Numero: " . mysql_errno($CNX) . "|connect: \n " . $_SESSION["current_file"]);
		} else {
			mysql_set_charset('utf8',$CNX);
			if ( mysql_select_db(MY_DB_IN, $CNX) == false ) {
				//syslog("", "Z>ZZZZZZZZZZZZZZZZZZZ");
				//saveError(2, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], " No se puede seleccionar la Base de Datos :" . mysql_error($CNX) . "|Numero: " .mysql_errno($CNX) . "|SELECT DB \n " . $_SESSION["current_file"]);
				syslog(E_ERROR, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], " No se puede seleccionar la Base de Datos :" . mysql_error($CNX) . "|Numero: " .mysql_errno($CNX) . "|SELECT DB \n " . $_SESSION["current_file"]);
			}
		}
	return $CNX;
}

function getRecordset($SQL = "", $cnx = false){
	$rs		= false;
	$cnx	= ($cnx == false) ? cnnGeneral() : $cnx;
	$term	= (isset($_SERVER["REMOTE_ADDR"]) ) ? $_SERVER["REMOTE_ADDR"] : "DESCONOCIDO";
	$usx	= (isset($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]) ) ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : $term; 
	$filex	= isset( $_SESSION["current_file"] ) ? $_SESSION["current_file"] : "ARCHIVO_DESCONOCIDO";
	
	if ( isset($SQL) AND ( strlen($SQL) > 6 ) ){
		$rs		= mysql_query($SQL, $cnx);
		if ($rs == false ){
			saveError(2, $usx, "SQL: $SQL|" . mysql_error($cnx) . "|Numero: " .mysql_errno($cnx) . "|Archivo: " . $filex . "\r\n");
		}
	}
	//@mysql_free_result($rs);
	return $rs;
}
//.- Definir constantes de TABLAS<br>
$t_cd		= "creditos_destinos"; $t_ce 		= "creditos_estatus"; $t_cfe = "creditos_flujoefvo"; $t_ctf = "creditos_tflujo"; $t_cpf 		= "creditos_periocidadflujo";
$t_cg 		= "creditos_garantias"; $t_cge 		= "creditos_garantiasestatus"; $t_cl = "creditos_lineas"; $t_cm 		= "creditos_modalidades"; $t_cnr		= "creditos_nivelesriesgo";
$t_cpp 		= "creditos_periocidadpagos"; $t_cs = "creditos_solicitud"; $t_ctc = "creditos_tipoconvenio"; $t_srt 		= "socios_relacionestipos";  $t_se 		= "socios_estatus";
$t_sr 		= "socios_relaciones"; $t_sti 		= "socios_tipoingreso";

	// --------------------------------------- VALOR SQL DEL MVTO.-------------------------------------------------------
			// VALORES FIJOS
$smf	= "idusuario, codigo_eacp, socio_afectado, docto_afectado, recibo_afectado, fecha_operacion,
			periodo_contable, periodo_cobranza, periodo_seguimiento, periodo_anual, periodo_mensual, periodo_semanal,
			afectacion_cobranza, afectacion_contable, afectacion_estadistica,
			afectacion_real, valor_afectacion,
			idoperaciones_mvtos, tipo_operacion, estatus_mvto, periodo_socio,
			fecha_afectacion, fecha_vcto,
			saldo_anterior, saldo_actual, tasa_asociada, dias_asociados, detalles,
			sucursal ";
define("PK_MODEL_FIELDS", $smf);

if(MODO_DEBUG == true) { /*ini_set("display_errors", "on");*/ }
/* --------------------------------------------------------------------------------------------------------*/
/**
 * Obtiene el resultado de una fila
 * @param string $sql
 * @param string $lafila
 */
function mifila($sql, $lafila) {
	$mival 	= 0;
	$rw 	= obten_filas($sql);
	//$mival 	= $rw[$lafila];
	if ( isset(  $rw[$lafila]  )){
		$mival	= ( ( trim($rw[$lafila]) == "") OR empty($rw[$lafila]) ) ? 0 : $rw[$lafila];
		
	}

	return $mival;
}
/**
 * @deprecated @since 2015.01.01
 */
function getFila($sql, $fila) {
	$xQL	= new MQL(); $DD = $xQL->getDataRow($sql);
	return (isset($DD[$fila])) ? $DD[$fila] : false;
}
//.- -------------------- funcion para enviar sentencias SQL general. ---------------------------
/**
 * @deprecated @since 2015.01.01
 */
function db_query($sqlquery) {
	$cnx =	cnnGeneral();
	$resultq = mysql_unbuffered_query($sqlquery, $cnx);

	if (!$resultq){
		//.- Datos para depurar
		saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Func db_query : Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sqlquery . "|EN :" . $_SESSION["current_file"]);
	}

	@mysql_close($cnx);
	unset($resultq);
}
/*----------------------  Funciones de datos generales -------------------------- */
/**
 * @deprecated @since 2015.01.01
 */
function namesoc($idsocio) { return getNombreSocio($idsocio); }
/**
 * @deprecated @since 2015.01.01
 */
function getNombreSocio($codigo){ $xSoc = new cSocio($codigo); $xSoc->init(); return $xSoc->getNombreCompleto(); }
/**
 * @deprecated @since 2015.01.01
 */
function sociodom($idsocio){ $xSoc = new cSocio($idsocio); return $xSoc->getDomicilio(); }
/**
 * @deprecated @since 2015.01.01
 */
function domicilio($socio = 0){ $xSoc = new cSocio($socio); return $xSoc->getDomicilio(); }
/**
 * @deprecated @since 2015.01.01
 */
function getSocioDomicilio($socio = 0){	$xSoc = new cSocio($socio); return $xSoc->getDomicilio(); }
/**
 * @deprecated @since 2015.01.01
 */
function folios($idfol){ $myfol = getFolio($idfol); return $myfol; }

/* ---------------------- solo para tablas de tipos ---------------------------  */

//.- Funcion que devuelve un valor segun campo dado de las tablas de tipos
function eltipo($tabla, $filtro){
	$unval 		= 0;
	$unfield 	= "id" . $tabla;
	$unsql 		= "SELECT * FROM $tabla WHERE $unfield=$filtro LIMIT 0,1";
	$vals		= obten_filas($unsql);
	$unval		= $vals[1];
	unset($vals);
	return $unval;

}
/* ------------------------ verifica si existen registros -------------------------*/
function db_regs($sqlr) {
	$resulta = mysql_query($sqlr, cnnGeneral());
		if (!$resulta){
			return 0;
			exit;
		}
		if (mysql_num_rows($resulta) <1) {
			return 0;
			exit;
		} else {
			return mysql_num_rows($resulta);
		}
	@mysql_free_result($resulta);
} //end function
/* ---------------------------- FUNCION DE CONVERSION DE MONEDA --------------------- */
function cmoney($mvalue = 0){	return getFMoney($mvalue); } // end function
/* ----------------------------- DETERMINA UN VALOR DE TIPOS ----------------------------*/
function siexiste($idexiste, $idkey){
	if ($idexiste == 1) {			// PARA SOCIO
			$sqlex = "SELECT * FROM socios_general WHERE estatusactual=98 AND codigo=$idkey LIMIT 0,1";
	} elseif ($idexiste == 2) {		// PARA SOLICITUD
			$sqlex = "SELECT * FROM creditos_solicitud WHERE estatus_actual<>99 and numero_solicitud=$idkey, LIMIT 0,1";
	} elseif ($idexiste == 3) {	// PARA RECIBO
			$sqlex = "SELECT * FROM operaciones_recibos WHERE idoperaciones_recibos=$idkey LIMIT 0,1";
	} else {
		return 0;
		exit;
	}
	$rsexiste = mysql_query($sqlex, cnnGeneral());

		if (!$rsexiste){
			$existia = 0;
		}

		if (mysql_num_rows($rsexiste) <1) {
			$existia = 0;
		} else {
			$existia = mysql_num_rows($rsexiste);
		}

		return $existia;

	@mysql_free_result($rsexiste);

}
function rlinea($socio) {
		// obtiene la suma de las lineas de Credito
		$sqlsc		= "SELECT SUM(saldo_actual) As 'sdoscred' FROM creditos_solicitud WHERE numero_socio=$socio AND saldo_actual>0.99";
		$sqlsl		= "SELECT SUM(monto_linea) as 'sumalinea' FROM creditos_lineas WHERE estado=1 AND numero_socio=$socio";
		$remanente	= mifila($sqlsl, "sumalinea") - mifila($sqlsc, "sdoscred");

		return $remanente;

}
// Obtiene un camnpo de la Tablas solicitudes segun indice de filed dado
/**
 * @deprecated 1.9.42 Rev 47
 */
function volcarsol($docto, $index=0) {
	$sqltaw = "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$docto LIMIT 0,1";
	$rsla = mysql_query($sqltaw, cnnGeneral());
	$dov = 0;
		while($rw5 = mysql_fetch_array($rsla)) {
			$dov = $rw5[$index];
		}
		return $dov;
	@mysql_free_result($rsla);
}
/**
 * @deprecated 1.9.42 Rev 38
 */
function volcarsoc($socio, $index=0){
	$sqltw	= "SELECT * FROM socios_general WHERE codigo=$socio LIMIT 0,1";
	$rsls = mysql_query($sqltw, cnnGeneral());
	$dove = 0;
	$dSoc = obten_filas($sqltw);
	$dove	= $dSoc[$index];
	return $dove;
}
/**
 * @deprecated 1.9.42 Rev 38
 */
function sumarmvtodoc($docto, $concepto) {
}
/**
 * @deprecated 1.9.42 Rev 38
 */
function sumarmvtosoc($socio, $concepto, $options = "") {
}
// Funcion que actualiza un campo de la tabla movimiento segun solicitud y tipo de mvto
function updatemvto($iddocto, $tipomvto, $value=30, $campo="estatus_mvto") {
	$tresql = "UPDATE operaciones_mvtos SET $campo=$value WHERE docto_afectado=$iddocto AND tipo_operacion=$tipomvto";
	my_query($tresql);
}

// Funcion que actualiza un campo de la tabla movimiento segun solicitud y tipo de mvto, BASADO EN EL PERIODO DADO
function updatemvto_sp($iddocto, $tipomvto, $periodo=0, $value=30, $campo="estatus_mvto") {
	$thin = "";
	if ($periodo!=0) {
		$thin =  " AND periodo_socio=$periodo";
	}

	$trsql = "UPDATE operaciones_mvtos SET $campo=$value WHERE docto_afectado=$iddocto  $thin AND tipo_operacion=$tipomvto";

	my_query($trsql);
//	return $trsql;
}
/**
 * 
 * @deprecated @since 2016.0101
 */
function eliminar_mvtos($iddocto, $tipomvto, $periodo=0) {
	$thin = "";
	if ($periodo!=0) {
		$thin =  " AND periodo_socio = $periodo";
	}
	$trsql = "DELETE FROM operaciones_mvtos WHERE docto_afectado=$iddocto  $thin AND tipo_operacion=$tipomvto";

	my_query($trsql);
}
// Funcion que actualiza el Recibo de NEUTRALIZACION segun Docto Dado y Periodo Dado **** (OPCIONAL) ****
function updateneutral($iddocto, $idrecibo, $mvto, $periodo=0) {
	$thin = "";
	if ($periodo!=0) {
		$thin =  " AND periodo_socio=$periodo";
	}
	$uNSql = "UPDATE operaciones_mvtos SET docto_neutralizador=$idrecibo WHERE ";
	$uNSql .= "docto_afectado=$iddocto AND tipo_operacion=$mvto $thin";
	my_query($uNSql);
	//return $uNSql;
}
/**
 * funcion que devuelve el nombre del usuario o su puesto segun indice dado
 * @deprecated @since 2016.08.03
 */
function elusuario($eluser, $quequiere = false) {
	if(!isset($eluser) OR $eluser == false){
		$eluser		= getUsuarioActual();
	}
	$xUsr	= new cSystemUser($eluser);
	$nombre	= "";
	if($xUsr->init() == true){
		$nombre	= $xUsr->getNombreCompleto();
	}
	return $nombre;
}
function getSAFEUserInfoByName($user, $info = false){
	$val	= false;
	$sql	= "SELECT * FROM usuarios WHERE nombreusuario = '$user' ";
	$D		= obten_filas($sql);
		if($info == false){
			$val	= $D;
		} else {
			$val	= $D[$info];
		}

	unset($D);
	return $val;
}
/**
 * @deprecated 1.9.42 rev 38
 */
function latabla($db_tabla, $afiltrar="", $filtro="", $camposl="", $titlesl = "") {
}
/**
 * @deprecated @since 2015.01.01
 */
function volcartabla($tabla, $index=1, $where="") {
	$sqltr = "SELECT * FROM $tabla";
	if ($where != "") {
		$sqltr = $sqltr . " WHERE $where";
	}
		$sqltr = $sqltr . " LIMIT 0,1";
	$rettr = "*** NO EXISTEN DATOS ***";
	$rstr = mysql_query($sqltr, cnnGeneral());
	while ($twtr = mysql_fetch_array($rstr)) {
		$rettr = $twtr[$index];
	}
	return $rettr;
	@mysql_free_result($rstr);
}
// Devuelve una Tabla segun la Consulta dada
/**
 * @deprecated 1.9.42 Rev 47
 */
function sqltabla($sqlret, $camposl="", $titlesl = "", $addon = 0, $mykey = 0, $iswith="95%", $caption="", $hiddenkey = 0) {
	if ($titlesl == "") {
		$titlesl = "Identificador Descripcion";
	}

	$addonc		= "";
	$addont 	= "";
	if ($addon!=0) {
		$addont = "<th>Operaciones</th>";
	}
	$rsret = mysql_query($sqlret, cnnGeneral());
	if (!$rsret) {
		echo "ERROR AL MOSTRAR LA TABLA " . mysql_error() . "| " . mysql_errno() . "|$sqlret" . "|EN:" . $_SESSION["current_file"];
	}

	//
	if ($caption!= "") {
		$caption= "<caption>$caption</caption>";
	}
	//
		if ($camposl=="") {
			$lenmax = mysql_num_fields($rsret) - 1;
				for($ic = 0; $ic <= $lenmax; $ic++) {
/** ----------------------------- **/
					$camposl = $camposl . $ic . ";";	// obtiene el valor del campo
				}
		}
		// Obtiene las matrices para la tabla
		$campos = explode( ";", $camposl);	// Matriz de los campos
		// Cuenta los nombres de los campos
		$cntc = count($campos) -1; 	// cuenta los campos
		if ($titlesl == "fieldnames") {
			$titlesl = "";
			for($ic = 0; $ic < $cntc; $ic++) {
				$titlesl = $titlesl . mysql_field_name($rsret, $campos[$ic]) . " ";	// obtiene el valor del campo
			}
		}
		$titles = explode(" ", $titlesl);			// Obtiene la Matriz de Titulos
		$cntt = count($titles) - 1;	// cuenta los titulos
		echo "<table width='$iswith' border='0' align='center'>$caption";
		echo "<tr>";
		$sim = "";
		for ($it=0; $it <= $cntt; $it++) {
			$titleval = str_replace("_", " ", strtoupper($titles[$it]));
			$sim = $sim . "<th>$titleval</th>";
		}
			echo $sim . $addont;
		echo "</tr>";
		while($rwt = mysql_fetch_array($rsret)) {
			echo "<tr>";
			$lrows = "";
				switch ($addon) {
					case 1:
						$addonc = "<th><img src='images/common/edit.png' width='18' height='17' onClick='actualizame($rwt[$mykey]);' title='Editar Registro' align=\"middle\"/></th>";
					break;
					case 2:
						$addonc = "<th><img src='images/common/trash.png' width='18' height='17' onClick='eliminame($rwt[$mykey]);' title='Eliminar Registro' align=\"middle\"/></th>";
					case 3:
						$addonc = "<th><img src='images/common/edit.png' width='18' height='17' onClick='actualizame($rwt[$mykey]);' title='Editar Registro' align=\"middle\"/>
						<img src='images/common/trash.png' width='18' height='17' onClick='eliminame($rwt[$mykey]);' title='Eliminar Registro' align=\"middle\"/></th>";
					break;

					case 4:
						$addonc = "<th><img src='images/common/edit.png' width='18' height='17' onClick='actualizame($rwt[$mykey]);' title='Editar Registro' align=\"middle\"/>
						<img src='images/common/trash.png' width='18' height='17' onClick='eliminame($rwt[$mykey]);' title='Eliminar Registro' align=\"middle\"/>
						<img src='images/common/execute.png' width='18' height='17' onClick='the_action($rwt[$mykey]);' title='Ejecutar Una acci&oacute;n' align=\"middle\"/>
						</th>";
					break;

					case 5:
						$addonc = "<th><img src='images/common/execute.png' width='18' height='17' onClick='the_action($rwt[$mykey], $rwt[$hiddenkey]);' title='Ejecutar Una acci&oacute;n' align=\"middle\"/>
						</th>";
					break;
				}
			for ($ich = 0; $ich <= $cntc; $ich++) {
				if(isset($campos[$ich])){
					$insval = $campos[$ich];
					$lrows .= ( isset($rwt[$insval])) ? "<td>$rwt[$insval]</td>" : "";
				}
			}
			echo $lrows . $addonc;
			echo "</tr>";
		}
	echo "</table>";
	//
	//
	@mysql_free_result($rsret);
}
// Devuelve una Tasa SEGUN cantidad determinada
function obtentasa($monto, $tipocta=10, $dias = 7, $subproducto = '0') {
	
	$tasa	= 0;
	if($tipocta == CAPTACION_TIPO_VISTA){
		$BySub		= (CAPTACION_USE_TASA_DETALLADA == true) ? " AND subproducto=$subproducto " : "";
		$sqltasa = "SELECT tasa_efectiva
		FROM captacion_tasas
		WHERE modalidad_cuenta=$tipocta
		AND $monto>=monto_mayor_a AND $monto<monto_menor_a
		$BySub
		LIMIT 0,1";
	} else {
		$sqltasa = "SELECT tasa_efectiva
		FROM captacion_tasas
		WHERE modalidad_cuenta=$tipocta
			AND $monto>=monto_mayor_a AND $monto<monto_menor_a
			AND $dias>=dias_mayor_a AND $dias< dias_menor_a
		LIMIT 0,1";
	}
	
	$tasa 	= mifila($sqltasa, "tasa_efectiva");
	$tasa	= setNoMenorQueCero($tasa);
	return $tasa;
}
function psumarmvto($stringOrden) {
//xPOR,DOCTO|SOCIO,TIPOOPERACION,ESTATUSMVTO,PERIODO
	$orden = explode(",",  $stringOrden);
	$mirame = 0;
	$elcampo = "socio_afectado";
	if ($orden[0] == "xDoc") {
		$elcampo = "docto_afectado";
	}
	$sGore = "SELECT SUM(afectacion_real) AS 'sumamvto' FROM operaciones_mvtos ";
	$sGore .= "WHERE $elcampo=$orden[1] AND tipo_operacion=$orden[2] AND estatus_mvto=$orden[3] AND periodo_socio=$orden[4]";
		$mirame = mifila($sGore, "sumamvto");

		return $mirame;
}
/**
 * Muestra una Ficha Informativa segun tipo pedido
 * @deprecated v 1.9.42 rev 38
 */
function minificha($orden = 1, $filtro, $table , $showimage = "false", $ret = "false") {
	if (!isset($orden) ) {
		$exoFicha		=  "----------------NADA QUE MOSTRAR----------";
	}
//FICHA DE SOLICITUD
	if ($orden == 2) {
		$cF = new cFicha(iDE_CREDITO, $filtro);
		$cF->setTableWidth();
		$exoFicha = $cF->show(true);
// FICHA GRUPO SOLIDARIO
	} elseif ($orden == 3) {
		$cF = new cFicha(iDE_GRUPO, $filtro);
		$cF->setTableWidth();
		$exoFicha = $cF->show(true);
//FICHA GARANTIA DE CREDITO
	} elseif ($orden==4) {
		$cF = new cFicha(iDE_GARANTIA, $filtro);
		$cF->setTableWidth();
		$exoFicha = $cF->show(true);
//CUENTAS DE CAPTACION
	} elseif ($orden==5) {
		$cF	= new cFicha(iDE_CAPTACION, $filtro);
		$cF->setTableWidth();
		$exoFicha = $cF->show(true);

//FICHA DE RECIBO
	} elseif ($orden == 6) {
		$cF = new cFicha(iDE_RECIBO, $filtro);
		$cF->setTableWidth();
		$exoFicha = $cF->show(true);
//FICHA DE SOCIO
	} else {
		$cF = new cFicha(iDE_SOCIO, $filtro);
		$cF->setTableWidth();
		$exoFicha = $cF->show(true);
	}
	if($ret == "true"){
		return $exoFicha;
	} else {
		echo $exoFicha;
	}
}
/**
 * @author		Balam Gonzalez Luis
 * @package		common
 * @subpackage	common
 **/
class cFicha{
		private $mSql 				= "";
		private $mType 				= "";
		private $mKey 				= "";
		private $mTableWidth 		= "";
		private $mControl 			= "";
		private $mTool 				= array();
		private $mTitle 			= "";
		//private $mFValue = array();
		private $mSocioWithDomExt	= false;
		private $arrTit				= array (
											100 => "INFORMACI&Oacute;N DE LA PERSONA",
											200 => "INFORMACI&Oacute;N DEL CR&Eacute;DITO",
											101 => "INFORMACI&Oacute;N DEL GRUPO SOLIDARIO",
											220 => "INFORMACI&Oacute;N DE LA GARANT&Iacute;A",
											300 => "INFORMACI&Oacute;N DE LA CUENTA DE CAPTACI&Oacute;N",
											310 => "INFORMACI&Oacute;N DE LA CUENTA DE CAPTACI&Oacute;N",
											320 => "INFORMACI&Oacute;N DE LA CUENTA DE INVERSI&Oacute;N",
											400 => "INFORMACI&Oacute;N DEL RECIBO"
											);
	function __construct($tipo_de_ficha, $key){
		$this->mKey 	= $key;
		$this->mType 	= $tipo_de_ficha;
		$this->mTitle 	= $this->arrTit[$tipo_de_ficha];

	}
	function setTableWidth($width = "100%"){
		$this->mTableWidth = " width='$width' ";
	}
	function AddTool($event, $img, $title){
		$this->mTool[$event] = "$img@$title";
	}
	function addNewInfo($caption, $field){

	}
	function setSocioWithDomExt($aplicar = true){
		$this->mSocioWithDomExt = $aplicar;
	}
	function show($return = false){
		$exoFicha 	= "";
		$iNumTools	= sizeof($this->mTool);
		$trTool 	= "";
		$wTable 	= $this->mTableWidth;
		if($iNumTools>0){
			$iTools = "";
			foreach ($this->mTool as $key => $value) {
				$TParts 	= explode("@", $value);
				$iImg 		= $TParts[0];
				$iTitle 	= $TParts[1];
				$srcImg 	= vIMG_PATH . "/common/$iImg";
				$iTools 	= $iTools . "<td><img src=\"$srcImg\" onclick=\"$key(" . $this->mKey . ")\" />$iTitle</td>";
			}
			$trTool = "<tr>
			<td colspan=\"4\">
			<table width=\"100%\">
				<tbody>
					<tr>
					$iTools
					</tr>
				</tbody>
			</table>
			</td>
			</tr>";
		} // end iNumTools
		$cTyp	= new cTipos();
		switch ($this->mType){
			case iDE_SOCIO:				//INFO del Socio
				$cSoc		= new cSocio($this->mKey, true);
				$exoFicha	= $cSoc->getFicha($this->mSocioWithDomExt, false, $trTool);
				unset($cSoc);

			break;
			case iDE_CREDITO:			// Creditos iDE_CREDITO
				$cF = new cCredito($this->mKey);
				$cF->initCredito();
				$exoFicha = $cF->getFicha(false, $trTool);
				unset($cF);
			break;
			case 101:		//Grupos iDE_GRUPO
				$cG = new cGrupo($this->mKey);
				$exoFicha = $cG->getFicha(false, $trTool);
				unset($cG);
			break;
		case iDE_GARANTIA:
		$sql = "SELECT creditos_garantias.idcreditos_garantias,
				creditos_tgarantias.descripcion_tgarantias AS 'Tipo_de_Garantia',
				 creditos_tvaluacion.descripcion_tvaluacion AS 'Tipo_de_Valuacion',
				 creditos_garantias.fecha_recibo AS 'Fecha_de_Recibo',
				 FORMAT(creditos_garantias.monto_valuado, 2) AS 'Monto_valuado',
				 creditos_garantias.fecha_adquisicion AS 'Fecha_de_Adquisicion',
				 creditos_garantias.documento_presentado AS 'Documento_Presentado',
				 creditos_garantias.descripcion AS 'Descripcion',
				 creditos_garantias.propietario AS 'Propietario'
				 FROM creditos_tvaluacion, creditos_garantias, creditos_tgarantias
				 WHERE creditos_tgarantias.idcreditos_tgarantias=creditos_garantias.tipo_garantia
				 AND creditos_tvaluacion.idcreditos_tvaluacion=creditos_garantias.tipo_valuacion
		 AND creditos_garantias.idcreditos_garantias=" . $this->mKey;
		$tool = $trTool;
		$rwt = obten_filas($sql);

		$exoFicha =  "
	<table  $wTable border='0'>
	<tbody>
		<tr>
			<th class='izq'>Identificador</th><td>$rwt[0]</td>
			<th class='izq'>Tipo</th><td>$rwt[1]</td>
		</tr>
		<tr>
			<th class='izq'>Tipo de Valuacion</th><td>$rwt[2]</td>
			<th class='izq'>Fecha de Resguardo</th><td>$rwt[3]</td>
		</tr>
		<tr>
			<th class='izq'>Fecha de Adquisicion</th><td>$rwt[5]</td>
			<th class='izq'>Documento / Factura</th><td>$rwt[6]</td>
		</tr>
		<tr>
			<th class='izq'>Monto Valuado</th><td>" . getFMoney($rwt[4]) . "</td>
			<th class='izq'>Propietario</th><td>$rwt[8]</td>
		</tr>
		<tr>
			<th>Descripci&oacute;n</th>
			<td>$rwt[7]</td>
		</tr>
		$tool
	</tbody>
	</table>";
		break;
		case 300:
			//TODO: Change this line.- cambiar por la clase global
			$sqli = "
			SELECT
					`captacion_cuentas`.`numero_cuenta`,
					`captacion_cuentastipos`.`descripcion_cuentastipos` AS `modalidad`,
					`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
					`captacion_cuentas`.`fecha_apertura`                AS `fecha_de_apertura`,
					`captacion_cuentas`.`saldo_cuenta`                  AS `saldo_actual`,
					`captacion_cuentas`.`numero_grupo`                  AS `grupo_asociado`,
					`captacion_cuentas`.`numero_solicitud`              AS `credito_asociado`,
					`captacion_cuentas`.`tasa_otorgada`                 AS `tasa`,
					`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`
				FROM
					`captacion_cuentas` `captacion_cuentas`
						INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
						ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
						`idcaptacion_cuentastipos`
							INNER JOIN `captacion_subproductos` `captacion_subproductos`
							ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
							.`idcaptacion_subproductos`
				WHERE
					(`captacion_cuentas`.`numero_cuenta` =" . $this->mKey . ")";
			$rw		= obten_filas($sqli);
			$exoFicha =  "
				<table width='100%' border='0'>
				<tr>
					<th class='izq'>Numero de Cuenta</th  class='mny'><td>" . $rw["numero_cuenta"] . "</td>
					<th class='izq' >Fecha de Apertura</th><td>" . getFechaMediana($rw["fecha_de_apertura"])  . "</td>
				</tr>
				<tr>
					<th class='izq'>Modalidad</th><td>" . $rw["modalidad"] . "</td>
					<th class='izq'>Producto</th><td>" . $rw["subproducto"] . "</td>
				</tr>
				<tr>
					<th class='izq'>Tasa Actual</th><td class='mny'>% " . getFMoney( $rw["tasa"] * 100) . "</td>
					<th class='izq'>Saldo Actual</th>
					<td class='mny'>" . getFMoney( $rw["saldo_actual"] ) . "</td>
				</tr>
				<tr>
					<th class='izq'>Observaciones</th><td colspan='2'>" . $rw["observaciones"] . "</td>
				</tr>
				</table>";

			break;
		case 310:
			//Depositos a la vista iDE_CVISTA
			$cCapt		= new cCuentaALaVista($this->mKey);
			$cCapt->init();
			$exoFicha	= $cCapt->getFicha(false, $trTool);
		break;
		case 320:	//iDE_CINVERSION
			$xCI	= new cCuentaInversionPlazoFijo($this->mKey);
			$xCI->init();
			$exoFicha	= $xCI->getFicha(false, $trTool);
		break;
		case 400:		//RECIBO DE OPERACION
			$cRec 		= new cReciboDeOperacion(99, false, $this->mKey);
			//$cRec->initRecibo();
			$exoFicha 	= $cRec->getFicha(false, $trTool);
			unset($cRec);
		break;

		default:
		break;
		}
		//retoque fieldset
		if($this->mTitle!=""){
			$this->mTitle = "|&nbsp;&nbsp;&nbsp;" . $this->mTitle . "&nbsp;&nbsp;&nbsp;|";
		}

		$exoFicha = "<fieldset>
						<legend>" . $this->mTitle . "</legend>
							$exoFicha
					</fieldset>";

		if ($return == true){
			return  $exoFicha;
		} else {
			echo $exoFicha;
		}
	}
}
/**
 * @deprecated v 1.9.42 rev 38
 */
function tasadinamica($convenio=99, $periocidad=99, $tipoaut=99, $modalidad=99, $que=1) {
	return 0;
}
//tabla que divide campos y valores especificados y los convierte en campo=valor para hacer el update
function rsql_now($values, $fields, $separador=","){
	$elv	= explode($separador, $values);
	$elf	= explode($separador, $fields);
	$iL		= count($elf) -1;
	$strT	= "";

	for($i=0; $i <= $iL; $i++) {
		if ($strT== "") {
			$strT = trim($elf[$i]) . "=" . trim($elv[$i]);
		} else {
			$strT = $strT . ", " . trim($elf[$i]) . "=" . trim($elv[$i]);
		}
	}
	$strT = trim($strT);

	return $strT;
}
/**
 * @deprecated v 2012.03.01
 */
function ctrl_select($tabla, $opctrl = "", $filtro="", $es_sql = "no", $retornar = "no", $selected = "", $text = 1){
		$select = "";
		if ($es_sql == "yes"){
			$gssql 		= " $tabla $filtro";
		} else {
			$gssql		= "SELECT * FROM $tabla $filtro";
		}
		$gsres = mysql_query($gssql, cnnGeneral());
		$myoptions = "";
		while($gsrow = mysql_fetch_row($gsres)){
			if ($gsrow[0] == $selected) {
				$select = " selected ";
			} else {
				$select = "";
			}

			 $myoptions = $myoptions . "<option value='$gsrow[0]' $select>$gsrow[$text]</option> \n";
		}
		@mysql_free_result($gsres);
		if ($opctrl=="") {
			$opctrl="name='$tabla'";
		}
		//retornar o no
		if ($retornar == "yes"){
			return "<select $opctrl> \n " . $myoptions . "</select>";
		} else {
			echo "<select $opctrl> \n " . $myoptions . "</select>";
		}
}
function cuota_social($id) {
	$sqltre = "SELECT tipoingreso FROM socios_general WHERE codigo=$id";
	$eldia = mifila($sqltre, "tipoingreso");
	$sqlct = "SELECT (socios_tipoingreso.parte_social + socios_tipoingreso.parte_permanente) AS 'cuota_social' FROM socios_tipoingreso WHERE idsocios_tipoingreso=$eldia";
	$price = mifila($sqlct, "cuota_social");

	return $price;
}
/**
 * @deprecated v 1.9.42 rev 38
 */
function ficha($sql="", $nrows = 3, $with = "100", $limites = " LIMIT 0,1") {}
function check_cierre($prohibe=0 ){
	$horacierre = "SELECT hora_cierre FROM general_configuration LIMIT 0,1";
	$horacierre = mifila($horacierre, "hora_cierre");
	if (date("H") >= $horacierre) {
		$sqlcr = "select MAX(fecha_operacion) AS 'Ultimo_cierre' FROM operaciones_recibos WHERE tipo_docto=12";
		$tienecierre = mifila($sqlcr, "Ultimo_cierre");
			if ($tienecierre < date("Y-m-d")) {

			}

			if ($prohibe == 1) {
				return "NO SE PUEDE EFECTUAR";
			}
	}
}

/**
 * Funcion que Devuelve un Control Select con tipo de Pago
 * @param string $UsarEn			Uso en Egresos, Ingresos.
 * @param string $EspOptions		Opciones Especiales
 * @param string $events			Eventos como onclick='evento()'
 * @return string					Codigo HTML para el control
 * @deprecated			@sice 2014.11.01
 */

function ctrl_tipo_pago($UsarEn = TESORERIA_TIPO_INGRESOS, $name = "ctipo_pago",
						$id="idtipo_pago", $EspOptions = "", $events = ""){
	$xSel	= new cHCobros($name, $id);
	$xSel->setEvents($events);
	$xSel->setOptions($EspOptions);
	return  $xSel->getSelectTiposDePago($UsarEn);
}
function the_row( $sql="" ){
	if ($sql) {
		$rsr = mysql_query($sql, cnnGeneral());
		if (!$rsr) {
			saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL: ". $sql . "|" . $_SESSION["current_file"]);
		}
		$my_row = mysql_fetch_array($rsr);
		@mysql_free_result($rsr);
		return $my_row;
	}
}
/**
 * Ejecuta un Query  y devuelve una seria de informacion en un array
 * @param string $sqlMQ
 * @param string $debug_warns
 * @deprecated @since 2016.01.01
 * @return array
 */
function my_query($sqlMQ = "", $debug_warns = false) {

	$result					= array();
	$result["error"]		= "";		//mensaje de error
	$result["rows"]			= 0;		//numero de rows afectado
	$result[SYS_INFO]		= "";		//informacion total del query
	$result[SYS_MSG]		= "";		//mensajes de todo el query
	$result["query"]		= $sqlMQ;	//SQL
	$registros				= 0;
	$advertencias			= 0;
	$omitidos				= 0;
	$eliminados				= 0;
	$sucess  				= true;
	$msg					= "";
	
	if ( MODO_DEBUG == true ){
		$debug_warns 		= true;
	}
	if($sqlMQ!=""){
			$cnx			= cnnGeneral();
			$rsMain 		= mysql_query($sqlMQ, $cnx);
			if ( $rsMain == false ) {
				$errNotice	= mysql_error($cnx);
				$errNumber	= mysql_errno($cnx);
					saveError(2, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Error :$errNotice|Numero: $errNumber|SQL: ". $sqlMQ . "|" . $_SESSION["current_file"]);
					$result["error"]	.= date("H:s:i") . "\t" .  "ERROR: $errNotice|NUMERO: $errNumber\r\n";
					$sucess 			= false;
					$msg 				= date("H:s:i") . "\t" . "*** NO HAY INFORMACION ***\r\n";
			} else {
				//obtiene informacion sobre el query
				$aMsg	= trim( mysql_info($cnx) );
				if ( $aMsg != false AND MODO_DEBUG == true ){
					$xMsg				= explode(" ", $aMsg);
					//$result["rows"]	= $aMsg;
					//$errores			= $xMsg[];
					$registros			= (isset($xMsg[5])) ? $xMsg[5] : 0;
					$advertencias		= (isset($xMsg[7])) ? $xMsg[7] : 0;
					$omitidos			= (isset($xMsg[4])) ? $xMsg[4] : 0;
					$eliminados			= (isset($xMsg[3])) ? $xMsg[3] : 0;
					
					if ($debug_warns == true){		
					//Rows		matched:		11		Changed:		0		Warnings:		0
					//Records: 42 Deleted: 0 Skipped: 0 Warnings: 0
					//0      0 1  2      2 3        3 4 5       5 6
						if ( $advertencias > 0 ){
							$Wrs 			= 	mysql_query( "show warnings", cnnGeneral() );
							$msg			.=  "\r\nNivel\tcodigo\tMensaje\r\n";
							while ($WRw = mysql_fetch_array($Wrs)){
								$msg		.=  "$WRw[0]\t$WRw[1]\t$WRw[2]\r\n";
							}
						}
					} //end debug
				
				}
				$result["rows"]	= $registros;
				
			}
			@mysql_free_result($rsMain);
			unset($rsMain);
	} else {
		$result["error"]	.= "Sentencia SQL vacia";
		$sucess 			= false;
	}

	$result[SYS_MSG]			.= $result["error"];
	
	$result["stat"]			= $sucess;
	$result[SYS_ESTADO]	= $sucess;
	$result["info"]			.= ($msg != "" ) ? "========= MENSAJES\t$msg\r\n" : "";
	$result["info"]			.= ($result["error"] != "" ) ? "========= ERRORES\t" . $result["error"] . "\r\n" : "";
	
	return $result;
}
function obten_filas($sql = "") {
	$mFilas		= array();
	$xQL		= new MQL();
	$mFilas		= $xQL->getDataRow($sql);
		/*if ($sql != "" AND ( strlen($sql) > 6 ) ) {
			$rs 	= getRecordset($sql, cnnGeneral());
			if($rs != false) {
				$mFilas = mysql_fetch_array($rs);
			} else {
				$errNotice	= @mysql_error($cnx);
				$errNumber	= @mysql_errno($cnx);
				saveError(2, getUsuarioActual(), "Error :$errNotice|Numero: $errNumber|SQL: ". $sql. "|" . $_SESSION["current_file"]);
			}
			@mysql_free_result($rs);
		}*/
	$xQL	= null;
	return $mFilas;
}
function contrato($id, $peticion){
	$id	= setNoMenorQueCero($id);
	$sqlcontrato = "SELECT * FROM general_contratos WHERE idgeneral_contratos=$id LIMIT 0,1";
	return mifila($sqlcontrato,$peticion);
}
function info_campo($table, $field){
	$sql = "SELECT * FROM general_structure WHERE tabla='$table' AND campo='$field'";

			$rs = mysql_query($sql, cnnGeneral());
				if(!$rs) {
						return "**** NO EXISTEN DATOS ****";
				} else {
					$filas = mysql_fetch_array($rs);
					return $filas;
				}

			@mysql_free_result($rs);

}
function getSqlStored($id=0, $retornar = "stringsql"){
	$sql = "SELECT * FROM general_sql_stored WHERE sqlcode=$id LIMIT 0,1";
	$dsql = obten_filas($sql);
	return $dsql[$retornar];
}

function get_monto_planeacion_credito($idsocio){
	//Cuantos dias se le espera a una socia.- para eliminar un credito 30 DIAS
	$dias_espera 			= DIAS_ESPERA_CREDITO;
	$antiguedad_planeacion 	= restardias(fechasys(), $dias_espera);
	$elcred 				= 0;

	$sql = "SELECT grupo_solidario FROM socios_general WHERE codigo=$idsocio";
	$gpoasoc = mifila($sql, "grupo_solidario");

	$sqls = "SELECT SUM(afectacion_real) AS 'total' FROM operaciones_mvtos
					WHERE grupo_asociado=$gpoasoc AND tipo_operacion=112
					AND estatus_mvto=40 AND
					fecha_afectacion >'$antiguedad_planeacion'
					GROUP BY grupo_asociado ";
	if ( $gpoasoc !=99 ) {
		$elcred = mifila($sqls, "total");
	}

	return $elcred;
}
function set_crypt($char=""){
	//return mcrypt_ecb(MCRYPT_TripleDES, MY_KEY, $char, MCRYPT_ENCRYPT);
	return $char;
}
function set_decrypt($char=""){
	//return mcrypt_ecb(MCRYPT_TripleDES, MY_KEY, $char, MCRYPT_DECRYPT);
}
/**
 * Obtiene una fecha evaluada que es LABORABLE, seleccionando de una Tabla/DB datos previamente guardado
 * @deprecated 2012.03.02
 */
function set_no_festivo($dia_a_evaluar){
	$xF			= new cFecha(0, $dia_a_evaluar);
	$fecha		= $xF->getDiaHabil($dia_a_evaluar);
	return $fecha;
}

function get_convenio($tipo=99){
	$sqlconv = "SELECT *
		FROM creditos_tipoconvenio
		WHERE idcreditos_tipoconvenio=$tipo";
	$dconv = obten_filas($sqlconv);
	return  $dconv;
}
function getInfoConvenio($tipo=99){
	$sqlconv = "SELECT *
    FROM creditos_tipoconvenio
    WHERE idcreditos_tipoconvenio=$tipo";
	$dconv = obten_filas($sqlconv);
	return  $dconv;
}

function getDatosSocio($socio=1){
	$sql = "SELECT * FROM socios_general WHERE codigo=$socio";
	$rs = mysql_query($sql, cnnGeneral());
	$filas = mysql_fetch_array($rs);
	@mysql_free_result($rs);
	return $filas;
}
function getDatosDomicilio($socio=1, $tipo = 1){
	$FilterByType	= " AND principal='$tipo' ";
	if ($tipo  == 99){
		$FilterByType	= " ";
	}
	$sql = "SELECT * FROM socios_vivienda
				WHERE socio_numero=$socio $FilterByType
			ORDER BY principal DESC, fecha_alta DESC
			LIMIT 0,1";
	return obten_filas($sql);
}

function getFMoney($mvalue = 0){
	settype($mvalue, "float");
	$mvalue 	= round($mvalue, 2);
	$cval 		= number_format($mvalue, 2, '.', ',');
	//$cval		= $mvalue;
	return $cval;
}
class cMFilas{
	private $mVFila 	= array();
	private $mNFila 	= array();
	private $mStat 		= true;
	function  __construct($sql){
		if ($sql!="") {
			$rs = mysql_query($sql, cnnGeneral());
				if(!$rs) {
					$rs = mysql_query($sql, cnnGeneral());
					if(!$rs){
						saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL: ". $sql . "|" . $_SESSION["current_file"]);
						$this->mStat = false;
					}
				} else {
					$rws = mysql_num_fields($rs) - 1;
					$FValues = mysql_fetch_array($rs);
					for($i=0; $i<=$rws; $i++ ){
						$FName = mysql_field_name($rs, $i);
						$FValue = $FValues[$FName];
						$this->mNFila[$i] = $FName;
						$this->mVFila[$FName] = $FValue;
					}
				}

			@mysql_free_result($rs);
		}
	}
	function getMFila($name){
		return $this->mVFila[$name];
	}
	function getStat(){
		return $this->mStat;
	}
}
class cFormula{
	private $mEstruct 			= "";
	private $mInfoF 			= array();
	private $mClave				= "";
	private $mInit				= false;
	public $SQL_MORA_X_LETRA	= "sql_mora_x_letra";
	public $SQL_PENA_X_LETRA	= "sql_pena_x_letra"; //Sin uso
	public $PHP_PENA_X_LETRA	= "php_pena_x_letra";
	public $PHP_MORA_X_LETRA	= "php_mora_x_letra";
	public $PHP_MORA_TOLE_PID	= "php_mora_tolera_pid";
	public $PHP_INT_FLAT_MOD	= "php_interes_pago_flat_mod";
	public $JS_LEAS_COT_VARS	= "js_leasing_cot_vars";
	function __construct($aplicado_a = ""){
		$this->mClave	= $aplicado_a;
		if($this->mClave != ""){ $this->init($this->mClave); }
	}
	function init($clave = ""){
		$xCache	= new cCache();
		$clave	= ($clave == "") ? $this->mClave : $clave;
		$D		= $xCache->get("formula-$clave");
		if($D === null){
			$xQL	= new MQL();
			$D		= $xQL->getDataRow("SELECT * FROM general_formulas WHERE aplicado_a='" . $clave . "' LIMIT 0,1");
			$xCache->set("formula-$clave", $D);
		}
		if(isset($D["estructura_de_la_formula"] )){
			$this->mInfoF	= $D;
			$this->mEstruct	= $D["estructura_de_la_formula"];
			$this->mInit	= true;
			$this->mClave	= $clave;
		}
		return $this->mInit;
	}
	function setEval(){	eval($this->mEstruct);	}
	function getFormula(){
		return $this->mEstruct;
	}
}
function getInfTOperacion($type = 99){
	$sql = "SELECT * FROM operaciones_tipos WHERE idoperaciones_tipos=$type";
	$dOpera = obten_filas($sql);
	return  $dOpera;
}

function getInfoCaptacion($cuenta){
	$SQL = "SELECT * FROM captacion_cuentas WHERE numero_cuenta=$cuenta";
	$DC = obten_filas($SQL);
	return $DC;
}
/**
 * @deprecated @since 2015.07.01
 * */
function getFolio($tipo){
	//Obtener el Ultimo Folio
	//Sumar + 1 y Registrarlo
	$myfol 	= 0;
	$idfol	= $tipo;
	$add	= true;
	$xFol	= new cFolios();
	if ($idfol ==1 OR $idfol == iDE_CREDITO ){
		//Numero de Credito
		$campo	= "numerocredito";
		$f_sql 	= "SELECT MAX($campo) AS 'dev' FROM general_folios";
		$myfol 	= mifila($f_sql, 0);
		$nfolio = $myfol;
	} elseif ($idfol == 2 OR $idfol == iDE_OPERACION ){
		//Numero de Operacion
		$f_sql 	= "SELECT getUltimaOperacion() AS 'folio' ";
		$myfol 	= mifila($f_sql, "folio");
		$campo	= "numerooperacion";
		$nfolio = $myfol; 
		$add	= false;
	} elseif ($idfol ==3 OR $idfol == iDE_SOCIO){
		//Numero de Socio
		$campo 	= "numerosocio";
		$f_sql 	= "SELECT MAX($campo) AS 'dev' FROM general_folios";
		$myfol 	= mifila($f_sql, 0);
		$nfolio = $myfol + 1;
		$add	= false;

	} elseif (($idfol ==4) OR ($idfol == iDE_RECIBO) OR ($idfol == 5) ){
		//Numero de Recibo
		$f_sql 	= "SELECT getUltimoRecibo() AS 'folio' ";
		$myfol 	= mifila($f_sql, "folio");
		$campo	= "numerorecibo";
		$nfolio = $myfol;
		$add	= false;
	} elseif ($idfol ==6 OR $idfol == iDE_CAPTACION) {
		//Numero
		$campo 	= "numerocontrato";
		$f_sql 	= "SELECT MAX($campo) AS 'dev' FROM general_folios";
		$myfol 	= mifila($f_sql, 0);
		$nfolio = $myfol + 1;
	} else {
		//Numero
		$campo = "numeroestadistico";
		$f_sql = "SELECT MAX($campo) AS 'dev' FROM general_folios";
		$myfol = mifila($f_sql, 0);
	}
	if($add == true){
		$sql 	= "INSERT INTO general_folios ($campo) VALUES ($nfolio)";
		my_query($sql);
	}
		
	return $myfol;
}
/**
 * Funcion que Retorna un Numero de Poliza segun el Tipo
 * @param 	integer 	$ejercicio		Ejercicio de la Poliza
 * @param 	integer 	$periodo		Periodo de la Poliza
 * @param 	integer		$tipo			Tipo de Poliza(Egresos, Ingresos, Diario)
 * @param 	boolean		$salvar			Indica si se salva en la DB el Numero de Poliza
 * @return 	integer						Numero de Poliza Construido
 */
function getFolioPoliza($ejercicio, $periodo, $tipo, $salvar = true){
	$numero = 1;
	$SQL = "SELECT MAX(numero) + 1 AS 'id'
			FROM general_folios_poliza
			WHERE ejercicio=$ejercicio
			AND periodo=$periodo
			AND tipo=$tipo";

	$numero = mifila($SQL, 0);

		if( !isset($numero) OR ( is_null($numero) ) OR ( $numero == "NULL") OR ( $numero <= 0) ){
			$numero = 1;
		}
	return $numero;

}
/**
 * @deprecated		v2012.02
 */
function PolizaPorRecibo($recibo, $generador = false){
	$msg		= "";
	$xCUtil		= new cUtileriasParaContabilidad();
	$msg		.= $xCUtil->setPolizaPorRecibo($recibo, $generador);
	return $msg;
}

class cValorarFormulas{
	private $mTipoFormula 	= 0;
	private $mReturn		= "";
	private $mMessages		= "";
	function __construct(){

	}
	function getCuentaContable($socio, $documento, $formula, $cajero = false, $cheque = false, $cuenta_bancaria = false){
		$language 			= strpos($formula, "\$"); // SI hay $ el Lenguaje es PHP
		$cuenta				= CUENTA_DE_CUADRE;
		$numero_de_socio	= $socio;
		$cheque				= setNoMenorQueCero($cheque);
		$cajero				= setNoMenorQueCero($cajero);
		$cuenta_bancaria	= setNoMenorQueCero($cuenta_bancaria);
		//getCuentaContablePorBanco($cuenta_bancaria)
		$QL					= new MQL();
		$xLog				= new cCoreLog();
		//FIXME: Verificar movimientos de variables no existentes, ejemplo $cheque
		//Verificar validez y funcionamiento - verificado. dic 2011
		//2015-06-29.- 
		if($language !== false){
			//Obtiene si el documento es de Captacion
			$esCredito				= strpos($formula, "cartera");		
			$esCaptacion			= strpos($formula, "captacion");

			if( isset($esCredito) AND ( $esCredito!==false ) ){
				/**
				* Obtiene Informacion para Cartera de credito
				*/
				$sqliCartera 		= "SELECT * FROM creditos_datos_contables WHERE numero_solicitud=$documento LIMIT 0,1";
				/*AND numero_socio=$socio*/
				$cartera 			= obten_filas($sqliCartera);
				if(isset($cartera["numero_socio"])){
					$xLog->add( "OK\t$socio\t$documento\tCREDITOS\tLos Datos Contables se cargan por el documento $documento\r\n", $xLog->DEVELOPER );
					$cartera["contable_intereses_moratorios"]	= $cartera["moratorio_cobrado"]; 
					//$cartera["contable_intereses_moratorios"]	= $cartera["moratorio_cobrado"];
				} else {
					$xLog->add( "ERROR\t$socio\t$documento\tCREDITOS\tNo existen Datos contable para el documento $documento\r\n", $xLog->DEVELOPER );
				}
			}
			if(isset($esCaptacion) AND ( $esCaptacion !== false) ){
				/**
	 			* Obtiene Informacion para Cuentas Captacion
	 			*/
				$sqliCaptacion 		= "SELECT * FROM captacion_datos_contables WHERE numero_cuenta=$documento LIMIT 0,1";
				/*AND numero_socio=$socio*/
				$captacion 			= obten_filas($sqliCaptacion);
				if(isset($captacion["numero_socio"])){
					$xLog->add( "OK\t$socio\t$documento\tCAPTACION\tSe cargan datos del Documento $documento\r\n", $xLog->DEVELOPER);
				} else {
					$xLog->add( "ERROR\t$socio\t$documento\tCAPTACION\tNo existen Datos contable para el documento $documento\r\n", $xLog->DEVELOPER );
				}				
				
			}
			if(eval($formula) === false){
				$xLog->add("$socio\t$documento\tERROR\tError en la Formula\r\n", $xLog->DEVELOPER);
			}
			$this->mReturn 			= $cuenta;
			$xLog->add("$socio\t$documento\tEVALUAR\tLa Cuenta se EVALUA y queda en $cuenta\r\n");
		} else {
			$cartera	= array();
			$captacion	= array();
			/**
		 	* Busca si es credito o Captacion
		 	*/
			$esCredito			= strpos($formula, "APLICA_CREDITO");
			$esCaptacion		= strpos($formula, "APLICA_CAPTACION");
				if(isset($esCredito) AND ( $esCredito!== 0 ) ){

					$formula	= str_replace("APLICA_CREDITO", "", $formula);
					/**
	 				* Obtiene Informacion para Cartera de credito
	 				*/
						$sqliCartera = "SELECT * FROM creditos_datos_contables WHERE numero_solicitud=$documento AND numero_socio=$socio LIMIT 0,1";
						//setLog($sqliCartera);
						$creditos = obten_filas($sqliCartera);
						$sustituir["CREDITOS_CARTERA_VIGENTE"]  		= (isset($creditos["contable_cartera_vigente"])) ?  $creditos["contable_cartera_vigente"] : CUENTA_DE_CUADRE;
						$sustituir["CREDITOS_CARTERA_VENCIDA"]  		= (isset($creditos["contable_cartera_vencida"])) ? $creditos["contable_cartera_vencida"] : CUENTA_DE_CUADRE;
						$sustituir["CREDITOS_INTERESES_DEVENGADOS"]  	= (isset($creditos["contable_intereses_devengados"])) ? $creditos["contable_intereses_devengados"] : CUENTA_DE_CUADRE;
						$sustituir["CREDITOS_INTERESES_ANTICIPADOS"]  	= (isset($creditos["contable_intereses_anticipados"])) ? $creditos["contable_intereses_anticipados"] : CUENTA_DE_CUADRE;
						$sustituir["CREDITOS_INTERESES_COBRADOS"]  		= (isset($creditos["contable_intereses_cobrados"])) ? $creditos["contable_intereses_cobrados"] : CUENTA_DE_CUADRE;
						$sustituir["CREDITOS_INTERESES_MORATORIOS"]  	= $creditos["contable_intereses_moratorios"];
						$sustituir["CREDITOS_CARTERA_CASTIGADA"]  		= $creditos["contable_catera_castigada"];
				} elseif(isset($esCaptacion)  AND ( $esCaptacion !== 0)  ){
						$formula	= str_replace("APLICA_CAPTACION", "", $formula);

						/**
	 					* Obtiene Informacion para Cuentas Captacion
	 					*/
						$sqliCaptacion = "SELECT * FROM captacion_datos_contables WHERE numero_cuenta=$documento AND numero_socio=$socio LIMIT 0,1";
						$captacion = obten_filas($sqliCaptacion);
						
						$sustituir["CAPTACION_MOVIMIENTOS"]  			= $captacion["contable_movimientos"];
						$sustituir["CAPTACION_INTERESES_POR_PAGAR"]  	= $captacion["contable_intereses_por_pagar"];
						$sustituir["CAPTACION_GASTOS_POR_INTERESES"]  	= $captacion["contable_gastos_por_intereses"];
						$sustituir["CAPTACION_CUENTAS_CASTIGADAS"]  	= $captacion["contable_cuentas_castigadas"];
				}
	/**
	 *  Cambios generales
	 */
			$formula	= str_replace(";", "", $formula);
			$formula	= str_replace("#", "", $formula);
			$formula	= str_replace("\t", "", $formula);
			$formula	= str_replace("+", "", $formula);

			foreach ($sustituir as $key => $value) {
				$formula = str_replace($key, $value, $formula);
			}
			$formula	= str_replace(" ", "", $formula);
			$cuenta		= $formula;
			//Retorna un String para valorar
			$this->mReturn = $cuenta;
		}
		$this->mMessages	.= $xLog->getMessages();
		return $this->mReturn;
	}
	/**
	 * Mensajes de la Libreria
	 * @param string $put Formato de Salida
	 * @return string	Mesajes de Texto
	 */
	function getMessages($put = OUT_TXT){ $xH		= new cHObject(); return $xH->Out($this->mMessages, $put); }	
}

/**
 * Generar una Prepoliza de perfil
 * @deprecated @since 2014.09.09
 */
function setPolizaProforma($recibo, $tipo_mvto, $monto, $socio, $docto, $operacion = 1, $usuario = false){
	if(MODULO_CONTABILIDAD_ACTIVADO == true){
		$usuario		= ( $usuario == false ) ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : $usuario ;
		$sucursal		= getSucursal();
		
		if($monto != 0){
			$sqlI 		= "INSERT INTO contable_polizas_proforma
								(numero_de_recibo, tipo_de_mvto, monto, socio, documento, contable_operacion, idusuario, sucursal)
		    					VALUES($recibo, $tipo_mvto, $monto, $socio, $docto, '$operacion', $usuario, '$sucursal')";
			$xQL		= new MQL();
			$xQL->setRawQuery($sqlI);
		}
	}
	return (MODO_DEBUG == false) ? "" : "OK\tPROFORMA\t$socio\t$docto\t$recibo\t$tipo_mvto\t$monto\r\n";
}
function getNombreMvto($id){
	$SQLD = "SELECT descripcion_operacion FROM operaciones_tipos WHERE idoperaciones_tipos=$id";
	$DMv = obten_filas($SQLD);
	return $DMv["descripcion_operacion"];
}
function getNumeroPorCadena($cadena){
	$numero	= 0;
	$patron		= "/[[:alpha:]]|[%#$,]/";
	$numero		= preg_replace($patron, "", $cadena);
    $numero     = str_replace(" ", "", $numero);
return $numero;
}
//====================================== CHEQUES MIGRAR ====================================================================
//XXX: Migrar a cuenta de cheques
function getCuentaXCheque($cheque, $campo = "codigo_contable"){
	$cheque = getNumeroPorCadena($cheque);
	$sqlIC = "SELECT * FROM " . TBANCOS_OPERACIONES . " WHERE tipo_operacion='cheque'
			AND numero_de_documento LIKE '%$cheque'
			ORDER BY
			fecha_expedicion DESC
			LIMIT 0,1 ";

	$bcheque 	= mifila($sqlIC, "cuenta_bancaria");

	$sqlBC 		= "SELECT
						`bancos_cuentas`.*,
						`bancos_entidades`.`nombre_de_la_entidad`
					FROM
						`bancos_cuentas` `bancos_cuentas`
							INNER JOIN `bancos_entidades` `bancos_entidades`
							ON `bancos_cuentas`.`entidad_bancaria` = `bancos_entidades`.
							`idbancos_entidades`
					WHERE
						idbancos_cuentas=$bcheque ";

	$cuenta 	= mifila($sqlBC, $campo);
	if ( (!isset($cuenta)) or ($cuenta == "0") ){
		if($campo!="codigo_contable"){
			$cuenta	= MSG_NO_PARAM_VALID;
		} else {
			$cuenta = CUENTA_DE_CUADRE;
		}
	}
	return $cuenta;
}
/**
 * @deprecated @since 20170101
 */
function setUltimoCheque($CuentaBancaria, $cheque = 0){
	$CuentaBancaria	= setNoMenorQueCero($CuentaBancaria);
	$xCta			= new cCuentaBancaria($CuentaBancaria);
	if($xCta->init() == true){
		$xCta->setUltimoCheque($cheque);
	}
}
function getUltimoCheque($banco){
		$documento = 0;
		$sql = "SELECT consecutivo_actual FROM bancos_cuentas
		WHERE idbancos_cuentas=$banco LIMIT 0,1";
		$documento = getFila($sql, "consecutivo_actual");
		$documento = getNumeroPorCadena($documento);
		$documento = $documento + 1;

		return $documento;
}
function setNuevoCheque($cheque, $cuenta, $recibo, $beneficiario,  $monto,
						$fecha = false, $autorizo = false, $descuento = 0 ){
	if ($fecha == false)	{	$fecha = fechasys();	}
	if ($autorizo == false)	{	$autorizo = $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];	}

	$eacp 		= EACP_CLAVE;
	$sucursal 	= getSucursal();
	$usr		= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
	$tipo		= "cheque";
	$estatus	= "autorizado";

	$sql = "INSERT INTO bancos_operaciones(tipo_operacion, numero_de_documento,
				cuenta_bancaria, recibo_relacionado, fecha_expedicion,
				beneficiario, monto_descontado, monto_real, estatus,
				idusuario, usuario_autorizo, eacp, sucursal) VALUES ('$tipo', '$cheque',
			    $cuenta, $recibo, '$fecha', '$beneficiario', $descuento, $monto, '$estatus',
			    $usr, $autorizo, '$eacp', '$sucursal') ";
	my_query($sql);
	return $cheque;
}
//======================================================================================================================
function setNuevoMvto($socio, $solicitud, $recibo, $fecha_operacion,
							$monto, $tipo_operacion, $parcialidad, $observaciones,
							$signo_afectacion = 1){
	$xRec			= new cReciboDeOperacion(false, false, $recibo);
	$idoperacion	= 0;
	if($xRec->init() == true){
		if($monto !== 0){
			$idoperacion	= $xRec->setNuevoMvto($fecha_operacion, $monto, $tipo_operacion, $parcialidad, $observaciones, $signo_afectacion, false, $socio, $solicitud);
		}
	}
	$xRec	= null;
	return ($idoperacion >0 ) ? true: false;
}

function setNuevoRecibo($socio, $solicitud, $fecha_operacion, $parcialidad,
						$tipo_docto, $cadena, $cheque_afectador,
						$tipo_pago, $recibo_fiscal, $grupo_asoc, $total = 0){
	$xRec			= new cReciboDeOperacion($tipo_docto);
	$NumeroRecibo	= $xRec->setNuevoRecibo($socio, $solicitud, $fecha_operacion, $parcialidad, $tipo_docto, $cadena, $cheque_afectador, 
									$tipo_pago, $recibo_fiscal, $grupo_asoc);
	return $NumeroRecibo;
}
function setFoliosAlMaximo(){	$xD	= new cSAFEData(); return $xD->setFoliosAlMaximo(); }
function getSumMvtoBySocio($socio, $tipo){
	$SQL = "SELECT
	`operaciones_mvtos`.`tipo_operacion`,
	SUM(`operaciones_mvtos`.`afectacion_real`) AS 'total'
FROM
	`operaciones_mvtos` `operaciones_mvtos`
WHERE
	(`operaciones_mvtos`.`socio_afectado` =$socio)
	AND
	(`operaciones_mvtos`.`tipo_operacion`= $tipo)
GROUP BY
	`operaciones_mvtos`.`tipo_operacion`";
	return mifila($SQL, "total");
}
function setMvtoNeutralBySocio($socio, $tipo, $docto_neutralizador) {
	$defEstatus = 99;
	$uNSql = "UPDATE operaciones_mvtos SET
	docto_neutralizador=$docto_neutralizador,
	afectacion_real=0,
	afectacion_cobranza=0,
	afectacion_contable=0,
	valor_afectacion=0,
	estatus_mvto=$defEstatus
	WHERE
	(tipo_operacion=$tipo)
	AND
	(socio_afectado=$socio)";
	my_query($uNSql);
	//return $uNSql;
}
function getExistCodeByWhere($tableName, $where){
	$rows 	= 0;
	$sql 	= "SELECT * FROM $tableName WHERE $where";
	$rs 	= mysql_query($sql, cnnGeneral());
		if (!$rs){
			$rows = 0;
		} else {
			$rows = mysql_num_rows($rs);
		}
	@mysql_free_result($rs);
	unset($rs);
	return  $rows;
}

class cQueryEsp {
	private $mKey = 0;
	private $mSql = "";
	function __construct($sql, $key) {
		$this->mKey		= $key;
		$this->mSql		= $sql;
	}
	function setSql($NSql){

	}
	function ArrayByQuery($FieldRequired = 0){
		$mArr = array();
		$rs = mysql_query($this->mSql, cnnGeneral());
		while($rw = mysql_fetch_array($rs)){
			$mArr[$rw[$this->mKey]] = $rw[$FieldRequired];
		}
		@mysql_free_result($rs);
		unset($rs);
		return $mArr;
	}
}
function getSqlStoredByName($name="", $retornar = "stringsql"){
	$sql = "SELECT * FROM general_sql_stored WHERE sqlcode='$name' LIMIT 0,1";
	$dsql = obten_filas($sql);
	return $dsql[$retornar];
}

function getRndKey(){
	$Mit  		= rand();
	$DatRND 	= date("YmdHis");
	$tempKey 	= md5($Mit . $DatRND);
	return $tempKey;
}
function setSociosAlMaximo(){
	$sql = "UPDATE socios_cajalocal SET ultimosocio = (SELECT ( MAX(codigo) + 1) FROM socios_general
			WHERE cajalocal = socios_cajalocal.idsocios_cajalocal
			GROUP BY cajalocal)";
	//@mysql_unbuffered_query($sql, cnnGeneral());
	$xQL	= new MQL();
	//my_query($sql);
	return $xQL->setRawQuery($sql);
}
/**
 * @deprecated 1.9.42x
 * @param string $var
 */
function getPrivateVar($var){
	$dat = false;
	switch($var){
		case "CTA_GLOBAL_CORRIENTE":
			$dat = CTA_GLOBAL_CORRIENTE;
			break;
	}
	return $dat;
}
function getUserRules($nivel){
	$rules 		= array();
	$sqlNiv 	= "SELECT * FROM general_niveles WHERE idgeneral_niveles=$nivel" ;
		$dats 	= obten_filas($sqlNiv);
		$mRules = explode("\n", $dats["rules_by_user"]);
		$mLim 	= sizeof($mRules);
			for($i = 0; $i < $mLim; $i++){
				$rul = explode("=", $mRules[$i]);
				$rules[$rul[0]] = trim($rul[1]);
			}
	return $rules;
}




function interes_moroso_sobre_parcialidades($recibo){
	//FALTA SUCURSAL
	$msg	= "=================\t\tINTERES_MOROSO_SOBRE_PARCIALIDADES\r\n";
	$msg	.= "===================================== CALCULO OMITIDO\r\n ";
	return $msg;
}
/**
 * @deprecated 2012.03
 */
function setPurgeFromDuplicatedRecibos(){
	$xop				= new cUtileriasParaOperaciones();
	$msg				= $xop->setEliminarRecibosDuplicados();
	return $msg;
}

function cBoolSelect($name, $id = false, $arrEvents = false){
	if ($id == false ){
		$id = "id-" .$name;
	}
	$events	= "";
	if ($arrEvents != false AND is_array($arrEvents) ){
		foreach($arrEvents as $key=>$value){
			$events .= " $key=\"$value\" ";
		}
	}
	return "<select name=\"$name\" id =\"$id\" $events>
			<option value=\"1\">&nbsp;&nbsp;&nbsp;SI&nbsp;&nbsp;&nbsp;</option>
			<option value=\"0\" selected=\"true\">&nbsp;&nbsp;&nbsp;NO&nbsp;&nbsp;&nbsp;</option>
		</select>";
}
/**
 * Genera las Inversiones Automaticas
 * @param integer $recibo numero de recio al que se agrega los movimientos
 */
function inversiones_automaticas($recibo = false, $fecha = false){
	if ($fecha == false ){
		$fecha = fechasys();
	}
  $msg		= "=================INVERSIONES_AUTOMATICAS======================\n";
  $msg		.= date("Y-m-d H:i:s") . "\tLas Cuentas con Saldo Minimo a " . INVERSION_MONTO_MINIMO . " se ignorar�n\r\n";
  $cierre_sucursal 		= getSucursal();

  if ( $fecha == false ){
  	$fecha_operacion 	= date("Y-m-d");
  } else {
  	$fecha_operacion	= $fecha;
  }
  $sql_invs = "SELECT
  `captacion_cuentas`.`numero_cuenta`,
  `captacion_cuentas`.`numero_socio`,
  `captacion_cuentas`.`inversion_fecha_vcto`,
  `captacion_cuentas`.`inversion_periodo`,
  `captacion_cuentas`.`tasa_otorgada`,
  `captacion_cuentas`.`dias_invertidos`,
  `captacion_cuentas`.`saldo_cuenta`,
  `captacion_cuentas`.`eacp`
FROM
  `captacion_subproductos` `captacion_subproductos`
    INNER JOIN `captacion_cuentas` `captacion_cuentas`
    ON `captacion_subproductos`.
    `idcaptacion_subproductos` = `captacion_cuentas`.
    `tipo_subproducto`
WHERE
  (`captacion_cuentas`.`inversion_fecha_vcto` = '$fecha')
  AND
  (`captacion_cuentas`.`saldo_cuenta` >=" . INVERSION_MONTO_MINIMO . ") AND
  (`captacion_subproductos`.`metodo_de_abono_de_interes` =\"AL_VENCIMIENTO\")
	AND
	(`captacion_cuentas`.`sucursal` = '$cierre_sucursal')
";

  $rs = mysql_query($sql_invs, cnnGeneral());
    if(!$rs){
      $msg	.= "<p>LA CONSULTA NO SE EJECUTO (CODE: " . mysql_errno() . ")</p>";
    }
  while($rw = mysql_fetch_array($rs)){

    $socio 				= $rw["numero_socio"];
    $cuenta 			= $rw["numero_cuenta"];
    $dias  				= $rw["dias_invertidos"];
    $periodo  			= $rw["inversion_periodo"];
    $tasa_anterior 		= $rw["tasa_otorgada"];


						//$numero_de_cuenta, $dias_invertidos, $tasa = false
	$cInv				= new cCuentaInversionPlazoFijo($cuenta, $socio, $dias);
	$cInv->setReinversion($fecha, true);
	$msg	.= $cInv->getMessages();
  }
return $msg;
}

function setEliminarMvto360($mvto, $socio, $solicitud, $recibo=1){
	//Obtiene Politicas de los Movimientos
	$SQL_DM		= "";
	$sqlDM 		= "SELECT * FROM operaciones_tipos WHERE idoperaciones_tipos=$mvto";
	$DMvto 		= obten_filas($sqlDM);
	$preservar_mvto = $DMvto["preservar_movimiento"];
	if($preservar_mvto =='1'){
		$SQL_DM = "UPDATE operaciones_mvtos
				SET afectacion_estadistica=afectacion_real,
				afectacion_real = 0, afectacion_contable=0,
				afectacion_cobranza=0, valor_afectacion=0,
				estatus_mvto=99, docto_neutralizador=$recibo
				WHERE socio_afectado=$socio
				AND docto_afectado=$solicitud
				AND tipo_operacion=$mvto AND `recibo_afectado`!=$recibo 
				";
	} else {
	$SQL_DM = "DELETE FROM operaciones_mvtos
				WHERE docto_afectado=$solicitud
				AND socio_afectado=$socio
				AND tipo_operacion=$mvto AND `recibo_afectado`!=$recibo ";
	}
	//my_query($SQL_DM);
	$xQL	= new MQL(); $xQL->setRawQuery($SQL_DM);
}

function setEliminarMvto($mvto, $socio, $solicitud, $parcialidad, $recibo = 1){
	$SQL_DM		= "";
	//Obtiene Politicas de los Movimientos
	$sqlDM 		= "SELECT * FROM operaciones_tipos WHERE idoperaciones_tipos=$mvto";
	$DMvto 		= obten_filas($sqlDM);
	$preservar_mvto = $DMvto["preservar_movimiento"];
	if($preservar_mvto=='1'){
		$SQL_DM = "UPDATE operaciones_mvtos
				SET afectacion_estadistica=afectacion_real,
				afectacion_real = 0, afectacion_contable=0,
				afectacion_cobranza=0, valor_afectacion=0,
				estatus_mvto=99, docto_neutralizador=$recibo
				WHERE socio_afectado=$socio
				AND docto_afectado=$solicitud
				AND tipo_operacion=$mvto
				AND periodo_socio=$parcialidad AND `recibo_afectado`!=$recibo 
				";
	} else {
	$SQL_DM = "DELETE FROM operaciones_mvtos
				WHERE docto_afectado=$solicitud
				AND socio_afectado=$socio
				AND tipo_operacion=$mvto
				AND periodo_socio=$parcialidad AND `recibo_afectado`!=$recibo  ";

	}
	//setLog($SQL_DM);
	//my_query($SQL_DM);
	$xQL	= new MQL(); $xQL->setRawQuery($SQL_DM);
}

//SEGUIMIENTO
function vencer_notificaciones(){
  $msg		= "=================VENCIMIENTO_DE_NOTIFICACIONES======================\n";
  $sucursal		= getSucursal();

  $sql = "UPDATE
		  `seguimiento_notificaciones`
		SET
		  `seguimiento_notificaciones`.`estatus_notificacion` = \"vencido\"
		WHERE
		  /*`seguimiento_notificaciones`.`fecha_vencimiento`<=CURDATE()
		  AND*/ `seguimiento_notificaciones`.`estatus_notificacion` = \"pendiente\"
		  ";
  /*$x = my_query($sql);
    if ($x["stat"] == false){
      $msg	.= $x[SYS_MSG] . "\n";
    } else {
      $msg	.= $x["info"] . "\n";
    }*/
}
function vencer_llamadas(){
  $msg		= "=================VENCIMIENTO_DE_LLAMADAS======================\n";
  
  $sql = "UPDATE `seguimiento_llamadas` SET `seguimiento_llamadas`.`estatus_llamada`=\"vencido\"
		WHERE
		  `seguimiento_llamadas`.`estatus_llamada` = \"pendiente\"
		  AND DATE_ADD(`seguimiento_llamadas`.`fecha_llamada`, INTERVAL 2  DAY) < CURDATE()";
  $xQL	= new MQL();
  $x 	= $xQL->setRawQuery($sql);
  $x	= ($x === false) ? false : true;
    if ($x == false){
    	$msg	.= "ERROR\tAl vencer las llamadas\r\n";
    } else {
      $msg	.= date("Y-m-d H:i:s") . "\tVenciendo las Llamadas de Seguimiento Pendientes\r\n";
    }
    return $msg;
}
function vencer_compromisos(){
  $msg		= "=================VENCIMIENTO_DE_COMPROMISOS======================\n";
  
  $sql = "UPDATE `seguimiento_compromisos`
		  SET
		  `seguimiento_compromisos`.`estatus_compromiso` =\"no_cumplido\"
		WHERE
		  `seguimiento_compromisos`.`estatus_compromiso` = \"pendiente\"
		  AND DATE_ADD(`seguimiento_compromisos`.`fecha_vencimiento`, INTERVAL 1  DAY) < CURDATE()  ";
  $xQL	= new MQL();
  $x 	= $xQL->setRawQuery($sql);
  $x	= ($x === false) ? false : true;
  if ($x == false){
  	$msg	.= "ERROR\tAl vencer Compromisos\r\n";
  } else {
    $msg	.= date("Y-m-d H:i:s") . "\tVenciendo los Compromisos de Seguimiento Pendientes\r\n";
  }
    return $msg;
}

function clearCacheSessions(){
	$xQL	= new MQL();
	$sql 	= "DELETE FROM usuarios_web_connected";
	return $xQL->setRawQuery($sql);
}
/**
 * Congela los Saldos a peticion.
 * esta funcion solo es usada en los FINES DE MES
 * @return string Mensajes del proceso
 */
function CongelarSaldos($recibo = false, $fecha = false){
	$xF			= new cFecha();
	$fecha		= $xF->getFechaISO($fecha);
	$xQL		= new MQL();
	
	if ( $fecha == false ) {
		$fecha	= fechasys();
	}
	$msg	= "";

	$q_con_cred = "UPDATE creditos_solicitud SET saldo_conciliado=saldo_actual, fecha_conciliada=fecha_ultimo_mvto";
	$x 		= $xQL->setRawQuery($q_con_cred);

	if ($x === false){
		$msg	.= $x[SYS_MSG] . "\n";
	} else {
		$msg	.= date("Y-m-d H:i:s") . "\tSE HAN ACTUALIZADO LOS SALDOS CONCILIADOS DE CREDITOS \r\n";
	}
	$q_con_cap = "UPDATE captacion_cuentas SET saldo_conciliado=saldo_cuenta, fecha_conciliada=fecha_afectacion ";
	$x 		= $xQL->setRawQuery($q_con_cap);

	if ($x === false){
		$msg	.= $x[SYS_MSG] . "\r\n";
	} else {
		$msg	.= date("Y-m-d H:i:s") . "\tSE HAN ACTUALIZADO LOS SALDOS CONCILIADOS DE CAPTACION \r\n";
	}
	return $msg;
}


function justice_mvtos( $docto, $tipo, $other = ""){
	$sqldelete = " DELETE FROM operaciones_mvtos WHERE docto_afectado=$docto AND tipo_operacion=$tipo $other";
	my_query($sqldelete);

}

function getCuentaPorCajero($cajero = false){ $xUsr		= new cSystemUser($cajero); $xUsr->init();	return $xUsr->getCuentaContableDeCaja();}
function getCuentaContablePorBanco($numero_de_cuenta){ $xBc = new cCuentaBancaria($numero_de_cuenta); $xBc->init(); return  $xBc->getCuentaContable(); }

function getPersonaEnSession($socio = false){
	if($socio !== false){ $_SESSION[ SESSION_SOCIO] = $socio; }
	return (isset($_SESSION[ SESSION_SOCIO]) ) ? $_SESSION[ SESSION_SOCIO] : DEFAULT_SOCIO;
}


/**
 *
 * Dibuja funciones Javascript para Operaciones Comunes
 * @author Balam Gonzalez Luis Humberto
 *
 */
class jsBasicForm {
	private $mType 				= iDE_CREDITO;
	private $mForm				= null;
	//Funciones Incluidas
	public  $mIncludeCalendar	= false;
	public  $mIncludeSocio		= true;
	public	$mIncludeCaptacion	= true;
	public	$mIncludeCommon		= true;
	public 	$mIncludeCreditos	= true;
	public 	$mIncludeGrupos		= true;
	public 	$mIncludeRecibos	= true;
	
	public  $mSubPath			= "";
	protected $mTypeCaptacion	= 0;
	protected $mSubproducto		= "all";
	private $IncJQuery			= false;
	private $strJQueryIncs		= "";
	private $mLoadVals			= true;
	
	private $mArrCalendarJs		= array();
	protected $mInputs			= array();
	
	protected $mWidth			= 600;
	protected $mHeigth			= 480;
	
	
	private $mFiltroCreditos	= "todos";
	
	/**
	 * Dibuja funciones Javascript para Operaciones Comunes
	 * @param string $form		Nombre del Formulario
	 * @param integer $type		Tipo de Operaciones JavaScript [iDE_CREDITO]
	 * @param string $subPath	Path por defecto,  . o ..
	 */
	function __construct($form, $type = iDE_CREDITO, $subPath = "."){
		$this->mForm 												= $form;
		$this->mSubPath												= $subPath;
		$this->mInputs["descripcion_de_la_solicitud"]["name"]		= "nombresolicitud";
		$this->mInputs["descripcion_de_la_cuenta"]["name"]		= "nombrecuenta";
		$this->mInputs["descripcion_del_socio"]["name"]			= "nombresocio";
		
		$this->mInputs["codigo_de_solicitud"]["name"]			= "idsolicitud";
		$this->mInputs["codigo_de_solicitud"]["id"]			= "idsolicitud";
		
		$this->mInputs["codigo_de_socio"]["name"]			= "idsocio";
		$this->mInputs["codigo_de_socio"]["id"]				= "idsocio";
		
		$this->mInputs["codigo_de_recibo"]["name"]			= "idrecibo";
		$this->mInputs["codigo_de_recibo"]["id"]			= "idrecibo";
		
		$this->mInputs["codigo_de_grupo"]["name"]			= "idgrupo";
		$this->mInputs["codigo_de_grupo"]["id"]				= "idgrupo";
		
		$this->mInputs["codigo_de_cuenta"]["name"]			= "idcuenta";
		$this->mInputs["codigo_de_cuenta"]["id"]			= "idcuenta";
		
		
		switch ( $type ){
			case iDE_CAPTACION:
				$this->mIncludeCaptacion	= true;
				$this->mIncludeCreditos		= false;
				$this->mIncludeCommon		= true;
				break;
			case iDE_CINVERSION:
				$this->mIncludeCaptacion	= true;
				$this->mIncludeCreditos		= false;
				$this->mTypeCaptacion		= CAPTACION_TIPO_PLAZO;
				$this->mIncludeCommon		= true;
				break;
			case iDE_CREDITO:
				$this->mIncludeCreditos		= true;
				$this->mIncludeCaptacion	= false;
				$this->mIncludeCommon		= true;
				break;
			case iDE_OPERACION:
				$this->mIncludeCreditos		= true;
				$this->mIncludeCaptacion	= true;
				$this->mIncludeCommon		= true;
				break;
		}
		$this->mIncludeSocio	= true;
	}
	/**
	 * Personaliza una variable de controles Input
	 * @param string $input
	 * @param string $property
	 * @param string $value
	 */
	function setInputProp($input, $property, $value){ $this->mInputs[$input][$property] = $value; }
	function setConCaptacion($w = true){ $this->mIncludeCaptacion	= $w; }
	function setIncludeCaptacion($w = true){ $this->mIncludeCaptacion	= $w; }
	function setConCreditos($w = true){ $this->mIncludeCreditos	= $w; }
	function setIncludeCreditos($w = true){ $this->mIncludeCreditos	= $w; }
	function setConGrupos($w = true){ $this->mIncludeGrupos	= $w; }
	function setIncludeGrupos($w = true){ $this->mIncludeGrupos	= $w; }
	function setTypeCaptacion($type = CAPTACION_TIPO_PLAZO){ $this->mTypeCaptacion	= $type; }
	function setSubproducto($type = 99){ $this->mSubproducto		= $type; }
	function setConRecibos($w = true){ $this->mIncludeRecibos	= $w; }
	function setConCommon($w = true){ $this->mIncludeCommon	= $w; }
	function setNCtrlGrupo($control){	$this->mInputs["codigo_de_grupo"]["name"]	= $control;	}
	function setIncludeCalendar($toInclude = true){	$this->mIncludeCalendar = $toInclude;	}
	function setConSocios($w = true){	$this->mIncludeSocio	= $w;	}
	function setIncludeOnlyCommons(){
		$this->mIncludeCalendar		= false;
		$this->mIncludeCaptacion	= false;
		$this->mIncludeCommon		= true;
		$this->mIncludeCreditos		= false;
		$this->mIncludeGrupos		= false;
		$this->mIncludeRecibos		= false;
		$this->mIncludeSocio		= false;
	}
	function setNameForm($name){ $this->mForm = $name; }
	function setNombreCtrlRecibo($nombre){ $this->mInputs["codigo_de_recibo"]["name"] = $nombre;	}
	function setLoadDefaults($load = false){ $this->mLoadVals = $load; }
	function setEstatusDeCreditos($estatus){ $this->mFiltroCreditos	= $estatus;	}
	/**
	 * Agrega la Opcion del Calendario al Javascript
	 * @param string $id_control		ID en el Documento XHTML
	 * @param boolean $type
	 * @param string $cmdButton 		Nombre del Boton asociado
	 */
	function addSetupCalendar($id_control, $type = false, $cmdButton = "cmdCalendar", $format = "%Y-%m-%d"){
		if ($type == "multiple"){
			$this->mArrCalendarJs[] = "
	//Dia
	Calendar.setup({
        inputField     :    \"ideldia$id_control\",
        ifFormat       :    \"%d\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});
	//Mes
	Calendar.setup({
        inputField     :    \"idelmes$id_control\",	// id of the input field
        ifFormat       :    \"%m\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});
	//Anno
	Calendar.setup({
        inputField     :    \"idelanno$id_control\",	// id of the input field
        ifFormat       :    \"%Y\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});
	";
		} else {
			$this->mArrCalendarJs[] = "
	Calendar.setup({
        inputField     :    \"$id_control\",
        ifFormat       :    \"$format\",
        showsTime      :    false,
        button         :    \"$cmdButton\",
        singleClick    :    true,
	});";
		}
	}
	/*			<link rel=\"stylesheet\" href=\"" . $this->mSubPath . "./css/jquery.qtip.css\" media=\"all\" />
	
	<script  src=\"" . $this->mSubPath . "./js/jquery/jquery.js\"></script><script  src=\"" . $this->mSubPath . "./js/jquery/jquery.qtip.min.js\"></script>*/
	function setIncludeJQuery($ask = true){
		if ($ask == true ){
			$this->strJQueryIncs	= "<link rel=\"stylesheet\" href=\"" . $this->mSubPath . "./css/jquery-ui/jquery-ui.css\" media=\"all\" />	<script  src=\"" . $this->mSubPath . "./js/jquery/jquery.ui.js\"></script>";
		}
		return $this->strJQueryIncs;
	}
	
	function setDrawCalendar($cmdButton = "cmdCalendar"){
		return "<img style=\"width: 16px; height: 16px;\" alt=\"\" src=\"../images/common/calendar.gif\" align='middle' id=\"$cmdButton\" alt=\"Muestra el Calendario\" />";
		
	}
	function getJsSocios(){
		//================================	SOCIOS
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio		= $this->mInputs["codigo_de_socio"]["name"];
		$idcuenta		= $this->mInputs["codigo_de_cuenta"]["name"];
		$subtipo		= ($this->mSubproducto == "all") ? 0 : setNoMenorQueCero($this->mSubproducto);
		$jsSocios		= "
/** Funciones de Compatibilidad */
function envsoc(){ jsSetNombreSocio(); }

/* funcion que retorna el nombre de socio */
function jsSetNombreSocio(mObtenerI){
	var tipocuenta		= jsTypeCaptacion;
	mObtenerI		= (typeof mObtenerI == \"undefined\") ? true : mObtenerI;
	if (jsWorkForm.$idsocio) {
		var mSocio	= entero(jsWorkForm.$idsocio.value);
		if( mSocio > 0 ) {
			jsrsExecute(jsrGeneralCommon, jsReturnSocio, \"Common_695bad33e1f2af343f99c6a4ceb9d045\", jsWorkForm.$idsocio.value);
			if(mObtenerI == true && mLoadDefs == true){
				/** Busca la Solicitud de Credito con Prioridad */
				if(jsWorkForm.$idsolicitud) {
					if(typeof jsReturnPrioriCredit != \"undefined\"){
						jsrsExecute(jsrCreditsCommon, jsReturnPrioriCredit, 'Common_86d8b5015acb366cec42bf1556d8258a', jsWorkForm.$idsocio.value + vSEPARATOR + mFiltroCred);
					}
				}
				/** Busca la Cuenta de captacion con Prioridad */
				if(jsWorkForm.$idcuenta){
					var mOr		= mSocio  + '|' + tipocuenta + '|" . $this->mSubproducto . "';
					var xCG	= new CaptGen(); xCG.getPrincipal({ persona: mSocio, tipo: tipocuenta, subtipo : $subtipo, control : jsWorkForm.$idcuenta.id });
				}
			}
		} else { goSocio_(); }
	}
}
/** Retorna El Numero de Socio */
function jsReturnSocio(mRetNombre){
	var xRetNombre	= mRetNombre
		if( (xRetNombre == \"" . MSG_NO_PARAM_VALID . "\") || (xRetNombre == '0' ) ){
					goSocio_();
		} else {
			try{
				jsWorkForm." . $this->mInputs["descripcion_del_socio"]["name"] . ".value = xRetNombre;
			} catch (err) {	}
		}
}
function jsPersonaRegresarCaptura(){ jsWorkForm.$idsocio.value = " . DEFAULT_SOCIO . "; jsWorkForm.$idsocio.focus(); jsWorkForm.$idsocio.select(); }
function goSocio_(){
	jsPersonaRegresarCaptura();
	var isoc 	= jsWorkForm.$idsocio.value;
	var pfSoc 	= \"../utils/frmbuscarsocio.php?i=\";
	try {
		var xurl = pfSoc + isoc + \"&f=" . $this->mForm . "\";
		mGlo.w({ url: xurl, h: 600, w : 800, tiny : true});
	} catch (e){}
}";
		return $jsSocios;
	}
	function getJsCreditos(){
		//================================	CREDITOS
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio		= $this->mInputs["codigo_de_socio"]["name"];
		$jsCreditos		= "
		var isCredit		= (typeof jsWorkForm.$idsolicitud != \"undefined\") ? true : false;
		var mCredit		= (typeof jsWorkForm.$idsolicitud != \"undefined\") ? jsWorkForm.$idsolicitud : null;
		var MG			= (typeof Gen  != \"undefined\") ? new Gen(): {};
		var MCRED		= (typeof CredGen  != \"undefined\") ? new CredGen(): null;
    /** Funciones de compatibilidad */
	function envsol(){ jsGetDescCredito(); }
	/** Retorna la Descripcion de la Solicitud	*/
	function jsGetDescCredito(){
		esGuardable=false;
		if( mCredit != null){
			if(MCRED == null){
				if( entero(mCredit.value) > 0 ) { jsrsExecute(jsrCreditsCommon, jsReturnDescCredito,'Common_b05dfbfaf8125673c6dc350143777ee1', mCredit.value); }
			} else {
				if( entero(mCredit.value) > 0 ) { MCRED.getDescripcion(mCredit.value, '$nombresolicitud'); }
			}
		}
	}
	function jsReturnPrioriCredit(idsolicitud){ if(isCredit == true){jsWorkForm.$idsolicitud.value=idsolicitud; setTimeout(\"jsGetDescCredito()\", 1000); }	}
	/** Returna una Descripcion del Credito */
	function jsReturnDescCredito(stringDescription){
		var mDescription = stringDescription;
		if (mDescription == '" . MSG_NO_PARAM_VALID . "'||$.trim(mDescription) == '') {
			jsWorkForm.$idsocio.focus();
			//var siBuscar = confirm(\"EL CREDITO SOLICITADO NO EXISTE \\n O ESTA INACTIVO. DESEA BUSCARLO?\");
			//if(siBuscar){ goCredit_(); } else { jsCredRegresarCaptura(); }
		} else {
			jsWorkForm.$nombresolicitud.value = mDescription; esGuardable=true;
		}
	}
	function jsCredRegresarCaptura(){
		jsWorkForm.$nombresolicitud.focus(); jsWorkForm.$nombresolicitud.select();
	}
	function goCredit_(){
		var isoc 	= jsWorkForm.$idsocio.value;
		var pfcred 	= \"../utils/frmscreditos_.php?i=\";
		var xurl	= pfcred + isoc + \"&f=" . $this->mForm . "&tipo=\" + mFiltroCred;
		mGlo.w({ url: xurl, h: 600, w : 800, tiny: true});
	}
	function envparc() {
		if (jsWorkForm.$idsolicitud) {
			if( entero(jsWorkForm.$idsolicitud.value) > 0){
				var misol = jsWorkForm.$idsolicitud.value;
				jsrsExecute(jsrFile, darparc,'damecredito', misol + ' 27');
			}
		}
	}
	function darparc(laparc)  {
		var uparc = parseInt(laparc)+1;
		if (jsWorkForm.idparcialidad){
			jsWorkForm.idparcialidad.value = uparc;
		}
	}
	
	function goLetra_(){
		var isoc 	= jsWorkForm." . $this->mInputs["codigo_de_solicitud"]["name"] . ".value;
		var urlLetra 	= \"../utils/frmletras.php?i=\" + isoc + \"&f=" . $this->mForm . "\";
		mGlo.w({ url: urlLetra, tiny: true});
	}
	";
		return $jsCreditos;
	}
	function getJsRecibos(){
		$jsRecibos		= "
			function goRecibos_(){
				var iRec 	= jsWorkForm." . $this->mInputs["codigo_de_recibo"]["name"] . ".value;
				var pfRec 	= \"../utils/frmbuscarrecibos.php?i=\";
				mGlo.w({ url: pfRec + iRec + \"&f=" . $this->mForm . "\" + \"&c=" . $this->mInputs["codigo_de_recibo"]["name"] . "\", h: 600, w : 800, tiny : true});
			}";
		return $jsRecibos;
	}
	function get(){ return $this->show(true); }
	function show($Devolver = false){
		$token					= SAFE_VERSION . SAFE_REVISION;
		$jsCalendarBody		= "";
		$jsJQueryUI			= $this->strJQueryIncs;
		$jsCalendarIncludes	= "";
		$jsLoadDef			= ($this->mLoadVals == true) ? "true" : "false";
		//$jsCaptacion
		$jsCalendarIncludes = "	<link rel=\"stylesheet\"  media=\"all\" href=\"" . $this->mSubPath . "./js/jscalendar/calendar-green.css\" title=\"green\" />
								<script  src=\"" . $this->mSubPath . "./js/jscalendar/calendar.js\"></script>
								<script  src=\"" . $this->mSubPath . "./js/jscalendar/lang/calendar-es.js\"></script>
								<script  src=\"" . $this->mSubPath . "./js/jscalendar/calendar-setup.js\"></script>";
		$jsMD5Include		= "<script  src='" . $this->mSubPath . "./js/md5.js'></script>";
		/**
		 * Include Segmentados
		 */
		$jsRecibos			= ($this->mIncludeRecibos == false) ? "" : $this->getJsRecibos();
		$jsSocios			= ($this->mIncludeSocio == false ) ? "" : $this->getJsSocios();
		$jsCreditos			= ($this->mIncludeCreditos == false) ? "" : $this->getJsCreditos();
		$jsCaptacion		= ($this->mIncludeCaptacion == false) ? "" : $this->getJsCaptacion();
		$jsCommon			= ($this->mIncludeCommon == false) ? "" : $this->getJsCommon();
		$jsGrupos			= ($this->mIncludeGrupos == false ) ? "" : $this->getJsGrupos();
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio			= $this->mInputs["codigo_de_socio"]["name"];
		$idcuenta			= $this->mInputs["codigo_de_cuenta"]["name"];
		$nombrecuenta		= $this->mInputs["descripcion_de_la_cuenta"]["name"];
		$claveSocio		= getPersonaEnSession();
		foreach ($this->mArrCalendarJs AS $key=>$value){
			$jsCalendarBody 		.= $value;
		}
		if ($this->mIncludeCalendar == false){
			$jsCalendarIncludes	= "";
			$jsCalendarBody		= "";
		}
		//================================	PRINCIPAL
		$js = "
			$jsCalendarIncludes
			$jsJQueryUI
			<script src='" . $this->mSubPath . "./js/jsrsClient.js'></script>
			<!-- <script src='" . $this->mSubPath . "./js/general.js?$token'></script> -->
			<script>
			var jsrFile 				= \"" . $this->mSubPath . "./clsfunctions.inc.php\";
			var jsWorkForm				= document." . $this->mForm . ";
			var jsrCreditsCommon		= \"" . $this->mSubPath . "./js/creditos.common.js.php\";
			var jsrCaptacionCommon		= \"" . $this->mSubPath . "./js/captacion.common.js.php\";
			var jsrGeneralCommon		= \"" . $this->mSubPath . "./js/general.common.js.php\";
			var jsrSeguimientoCommon	= \"" . $this->mSubPath . "./js/seguimiento.common.js.php\";
					
			var jsTypeCaptacion			= " . $this->mTypeCaptacion . ";
			var setToGo					= true;
			var mInputsCheck			= new Array();
			var mFiltroCred			= \"" . $this->mFiltroCreditos . "\";
			var vSEPARATOR			= \"" . STD_LITERAL_DIVISOR . "\";
			var mLoadDefs			= $jsLoadDef;
			var mGlo			= new Gen();
			var esGuardable			= false;
			var autoEjecutar		= true;
			var enBusqueda			= false;
			function frmSubmit( evaluate ){
				//Valida que los Campos
				evaluate = (typeof evaluate != \"undefined\" ) ? evaluate : false;
				if ( evaluate == false ){
					setToGo = jsEvaluarFormulario(false);
					if(setToGo == false){
						alert(\"Su Formulario contiene errores\");
					} else {
						var mGoSubmit	= confirm(\"Quiere Guardar los Datos Capturados?\");
						if( mGoSubmit == false ){
							setToGo = false;
						} else {
							jsWorkForm.submit();
						}
					}
				} else {
					jsWorkForm.submit();
				}
			}
			$jsSocios
			$jsCreditos
			$jsGrupos
			$jsCommon
			$jsRecibos
			$jsCaptacion
			$jsCalendarBody
			function out(msg){ if(typeof msg != \"undefined\"){ console.log(msg); }	}
			function jsLoadNombreValores(){
				if(autoEjecutar == true){
					if(jsWorkForm.$idsocio){
						if($.trim(jsWorkForm.$idsocio.value) == \"\"){ jsWorkForm.$idsocio.value = $claveSocio; }
						jsWorkForm.$idsocio.focus();
						if( entero(jsWorkForm.$idsocio.value) > 0 ){ jsSetNombreSocio(); }
					}
					if(jsWorkForm.$idsolicitud){
						if( entero(jsWorkForm.$idsolicitud.value) > 0 ){ jsGetDescCredito(); }
					}
				}
			}
			/*function jsEvaluarSalida(evt){ if(evt.id =='$idcuenta'){ envcta(); } }*/
			jsLoadNombreValores();
			</script>";
			
			if( $Devolver == false){
				echo $js;
			} else {
				return $js;
			}
	}
	function getJsGrupos(){
		//================================	GRUPOS
		$jsGrupos		= "/** FUNCION QUE RETORNA EL NOMBRE DEL GPO SOLIDARIO */
	function envgpo() {
		var idgpo = jsWorkForm." . $this->mInputs["codigo_de_grupo"]["name"] . ".value;
		jsrsExecute(jsrFile, jsGetNombreGrupo,'mostrargrupo', idgpo + ' 1');
	}
	function jsGetNombreGrupo(nombredev) {
		if(jsWorkForm.nombregrupo){ jsWorkForm.nombregrupo.value = nombredev;  }
	}
	function goGrupos_(){
		var iGrp 	= jsWorkForm." . $this->mInputs["codigo_de_grupo"]["name"] . ".value;
		var pfGrp 	= \"../utils/frmsgrupos.php?i=\";
		frmGrp 	= window.open(pfGrp + iGrp + \"&f=" . $this->mForm . "\", \"\", \"width=600,height=600,scrollbars,dependent=yes\");
		frmGrp.focus();
	}
	";
		return $jsGrupos;
	}
	function getJsCaptacion(){
		$markSubproducto	= "";
		if ( $this->mSubproducto != "all"){
			$markSubproducto	= "&s=" .$this->mSubproducto;
		}
		$idsolicitud		= $this->mInputs["codigo_de_solicitud"]["name"];
		$nombresolicitud	= $this->mInputs["descripcion_de_la_solicitud"]["name"];
		$idsocio			= $this->mInputs["codigo_de_socio"]["name"];
		$idcuenta			= $this->mInputs["codigo_de_cuenta"]["name"];
		$nombrecuenta		= $this->mInputs["descripcion_de_la_cuenta"]["name"];
		
		$jsCaptacion	= "
	//.- FUNCION OBTIENE DETALLES DE LA CUENTA DE CAPTACION
	/** Funcion de Compatibilidad */
	function envcta(iTipo) {
		vTipoC	= (typeof iTipo != \"undefined\") ? iTipo : jsTypeCaptacion;
		jsGetCuenta(iTipo);
	}
	function jsGetCuenta(inttipo) {
		if(jsWorkForm.$idcuenta){
			var lacta = jsWorkForm.$idcuenta.value;
				if (lacta!='' || lacta!=NaN || lacta!=0) {
					jsrsExecute(jsrCaptacionCommon, jsSetCuenta,'Common_82cbe75762e2714baaf92926f0d26d6b', lacta);
				}
		}
	}
	/** Obtiene una Descripcion de la Cuenta */
	function jsSetCuenta(depcta)  {
		var ccta = depcta;
		jsWorkForm.$nombrecuenta.value = ccta;
	}
	function jsReturnPrioriCaptacion(escta) {
		if (escta!='' || escta!=NaN || escta!=0) {
			var micta = escta;
			if(jsWorkForm.$idcuenta){
				jsWorkForm.$idcuenta.value = micta;
			}
		}
	}
	
	function goCuentas_(tipoc){
		var vTipoC	= \"\";
		if(typeof tipoc == 'undefined'){
			if(jsTypeCaptacion == 0){ } else { vTipoC	= \"&a=\" + jsTypeCaptacion; }
		} else { vTipoC	= \"&a=\" + tipoc; }
		var isoc 	= jsWorkForm.$idsocio.value;
		var urlcap 	= \"../utils/frmcuentas_.php?i=\" + isoc + \"&c=$idcuenta" . "$markSubproducto&f=" . $this->mForm . "\" + vTipoC;
		console.log(urlcap);
		mGlo.w({ url: urlcap, tiny: true});
	}";
		return $jsCaptacion;
	}
	function getJsCommon(){
		$EventOnLoad = " jsResizeWindow(); ";
		$jsCommon		= "
	// funcion que checa que el valor no sea cero
	function chkmonto(eValue){ return isNumber(eValue); }
	function notnan(isthis){ return isNotEmpty(isthis); }
	function muestralo(id_e) {
		var mist_s = document.getElementById(id_e);
		mist_s.style.visibility='visible';
	}
	function ocultalo(id_e) { var mist_e = document.getElementById(id_e); mist_e.style.visibility='hidden';	}
	function msgbox(string_alert) { alert (string_alert);	}
				
	function cierrame(){ window.close(); }
	/** function que cambia una propiedad de un elemento */
	function jsChangeProperty(id, prop, val){
		document.getElementById(id).removeAttribute(prop);
		document.getElementById(id).setAttribute(prop, val);
	}
	function jsRestarFechas(date1, date2) {
	    var DSTAdjuste 	= 0;
	    // ------------------------------------
	    oneMinute 		= 1000 * 60;
	    var oneDay 		= oneMinute * 60 * 24;
	    // ------------------------------------
	    date1.setHours(0);
	    date1.setMinutes(0);
	    date1.setSeconds(0);
				
	    date2.setHours(0);
	    date2.setMinutes(0);
	    date2.setSeconds(0);
	    // ------------------------------------
	    if (date2 > date1) {
	        DSTAdjuste =
	            (date2.getTimezoneOffset() - date1.getTimezoneOffset()) * oneMinute;
	    } else {
	        DSTAdjuste =
	            (date1.getTimezoneOffset() - date2.getTimezoneOffset()) * oneMinute;
	    }
	    var diff = Math.abs(	date2.getTime() - date1.getTime()	) - DSTAdjuste;
	    return Math.ceil(diff/oneDay);
	}
	function jsSumarDias(vFecha, days){
	    var mDays   = parseInt(days);
	    var vFecha	= new String(vFecha);
	    var sDays	= 86400000 * mDays;
	    var sDate   = vFecha.split('-');
	    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );
				
	    var vDate	= varDate.getTime()+sDays;
		varDate.setTime( vDate );
				
	    var mMonth  = varDate.getMonth()+1;
	    var mDate	= varDate.getDate()+1;
	    if (mMonth == 0){
	        alert('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
	    }
		return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
	}
	function jsRestarDias(vFecha, days){
				
	    var mDays   = new Number(days);
	    var vFecha	= new String(vFecha);
	    var sDays	= 86400000 * mDays;
	    var sDate   = vFecha.split('-');
	    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );
				
	    var vDate	= varDate.getTime()-sDays;
				
		varDate.setTime(vDate);
	    var mMonth  = varDate.getMonth()+1;
	    var mDate	= varDate.getDate()+1;
				
	    if (mMonth == 0){
	        alert('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
	    }
		return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
	}
	function setCheckForm(vFrm){ return jsEvaluarFormulario(); }
	function jsEvaluarFormulario(enviar){
		vFrm				= jsWorkForm;
		var isLims 			= vFrm.elements.length - 1;
		enviar				= (typeof enviar == 'undefined') ? true : enviar;
				
			setToGo			= true;
	  		for(i=0; i<=isLims; i++){
				var elem	= vFrm.elements[i];
				var mTyp 	= elem.getAttribute(\"type\");
				var mCls	= elem.getAttribute(\"class\");
				
				if ( (mTyp == \"text\" || mTyp == \"textarea\") ){
					/* Validar si no esta vacio */
				
					if ( /(req)/.test(mCls) ){
							setToGo = isNotEmpty(elem);
					}
					//validar que los numeros sean numeros , siempre que no este vacio
					if ( /(mny)/.test(mCls) ){
							setToGo = isNumber(elem);
					}
					if ( (setToGo == false) && (mTyp!=\"hidden\") ){
							elem.focus();
							break;
					}
				}//eval
	  		}
		if( setToGo == true&& enviar == true ){
			var mGoSubmit	= confirm(\"Quiere Guardar los Datos Capturados?\");
			if( mGoSubmit == false ){
				setToGo = false;
			} else {
				vFrm.submit();
			}
		}
		return setToGo;
	}
				
	function isLenX(elem, mLen) {
		var str 	= elem.value;
		var sucess	= true;
		var mTit	= elem.getAttribute(\"title\");
	    var re 		= /\b.{mLen}\b/;
	    if (!str.match(re)) {
	        alert(\"[ERROR]El Campo no tiene la Numero de Entradas Aceptadas.\");
	        sucess	= false;
	    } else {
	        sucess 	= true;
	    }
	    return sucess;
	}
				
	// validates that the field value string has one or more characters in it
	function isNotEmpty(elem) {
	    var str 	= elem.value;
	    var mTit	= elem.getAttribute(\"title\");
	    var sucess	= true;
	    if( str == null || str.length == 0 || /^\s+$/.test(str) ) {
	        alert(\"[ERROR]El Valor de [\" + mTit + \"] no debe quedar vacio\");
	        sucess	= false;
	    } else {
	        sucess	= true;
	    }
	    return sucess;
	}
				
	//validates that the entry is a positive or negative number
	function isNumber(elem) {
	    var str 	= elem.value;
	    var sucess	= true;
	    var mTit	= elem.getAttribute(\"title\");
    	var re 		= /^[-]?\d*\.?\d*$/;
    	str 		= str.toString( );
    	if (!str.match(re)) {
	        alert(\"[ERROR]El Valor de [\" + mTit + \"] debe ser un Numero\");
	        sucess	= false;
	    }
	    return sucess;
	}
				
				
				
				
	function jsResizeWindow(){
			top.resizeTo(" . $this->mWidth . "," . $this->mHeigth . ");
			
	}
	function jsRoundPesos(mCantidad){
		var mStrCantidad	= new String(mCantidad);
		var rF = new RegExp(\",\" , \"g\");
		var rF2 = new RegExp(\"$\" , \"g\");
		var rF3 = new RegExp(\" \" , \"g\");
		
		mStrCantidad = mStrCantidad.replace(rF, \"\");
		mStrCantidad = mStrCantidad.replace(rF2, \"\");
		mStrCantidad = mStrCantidad.replace(rF3, \"\");
		mStrCantidad = mStrCantidad.replace(\"$\", \"\");
		
			mStrCantidad	+= \".00\";
		var arrCantidad		= mStrCantidad.split(\".\");
		
		return arrCantidad[0] + \".\" + arrCantidad[1];
	}
	
	function jsInitComponents(){
	$EventOnLoad
	}
	";
	return $jsCommon;
	}
}



class cMigracion {
	function __construct(){
		
	}
}
class cMigracionTCB extends cMigracion {
	function Creditos_EliminarNoExistentes(){
		$msg	= "";
		$sql	= "
				SELECT
					`creditos_solicitud`.*,
					`creditos_solicitud`.`estatus_actual`,
					`creditos_solicitud`.`saldo_actual`
				FROM
					`creditos_solicitud` `creditos_solicitud`
				WHERE
					(`creditos_solicitud`.`estatus_actual` =50)
					OR
					(`creditos_solicitud`.`saldo_actual` <=0.99)
			";
		$rs	= mysql_query($sql, cnnGeneral() );
		while( $rw = mysql_fetch_array($rs) ) {
			$credito 	= $rw["numero_solicitud"];
			$socio		= $rw["numero_socio"];
			$sqlDE		= " DELETE FROM tcb_prestamos_movimientos WHERE numero_de_credito=$credito ";
			$x			= my_query($sqlDE, true);
			$msg	.= "$socio\t$credito\tELIMINAR\Eliminar -- " . $x["rows"] . " -- Movimientos de TCB\r\n";
		}
		return $msg;
	}
	function TCB_GenerarLetras(){
		//TODO: Revisar v 1.9.42 rev 42 2011-09-24
		$msg	    = "============================ GENERANDO TABLAS DE AMORTIZACION TCB \r\n ";
		my_query("DELETE FROM tcb_prestamos_movimientos ");
		//crear tabla de amortizaciones pagadas
		$msg	    .= "============================ IMPORTANDO MOVIMIENTOS DE SAFE \r\n ";
		$sql	= "SELECT SQL_CACHE
					`operaciones_mvtos`.`socio_afectado`       AS `socio`,
					`operaciones_mvtos`.`docto_afectado`       AS `credito`,
					`operaciones_mvtos`.`tipo_operacion`       AS `operacion`,
					`operaciones_mvtos`.`fecha_operacion`      AS `fecha`,
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					SUM(`operaciones_mvtos`.`afectacion_real`) AS `monto`
				FROM
					`eacp_config_bases_de_integracion_miembros`
					`eacp_config_bases_de_integracion_miembros`
						INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
						ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
						`operaciones_mvtos`.`tipo_operacion`
				WHERE
					(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =8002)
					AND
					(`operaciones_mvtos`.`docto_afectado` != 1)
				GROUP BY
					`operaciones_mvtos`.`docto_afectado`,
					`operaciones_mvtos`.`tipo_operacion`,
					`operaciones_mvtos`.`fecha_operacion`
				ORDER BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`operaciones_mvtos`.`docto_afectado`,
					`operaciones_mvtos`.`fecha_operacion`,
					`operaciones_mvtos`.`tipo_operacion` /* LIMIT 0,100 */ ";
		$rs	= mysql_query($sql, cnnGeneral() );
		$MarkCredito	= false;
		$MarkFecha	= false;
		while( $rw = mysql_fetch_array($rs) ) {
			$credito	= $rw["credito"];
			$socio		= $rw["socio"];
			$fecha		= $rw["fecha"];
			$operacion	= $rw["operacion"];
			$monto		= $rw["monto"];
			
			$iva_pagado	= 0;
			$capital_pagado	= 0;
			$IM_pagado	= 0;
			$IN_pagado	= 0;
			$IvaMPagado	= 0;
			$comisiones	= 0;
			$iva_comisiones	= 0;
			
			switch ( $operacion ){
				case 120:
					$capital_pagado += $monto;
					break;
				case 140:
					$IN_pagado += $monto;
					$iva_pagado	+= $monto * 0.15;
					break;
				case 141:
					$IM_pagado += $monto;
					$IvaMPagado	+= $monto * 0.15;
					break;
				case 146:
					$comisiones += $monto;
					break;
				case 145:
					$comisiones += $monto;
					break;
				case 351:
					$IN_pagado += $monto;
					$iva_pagado	+= $monto * 0.15;
					break;
				case 143:
					$IM_pagado += $monto;
					$IvaMPagado	+= $monto * 0.15;
					break;
				case 142:
					$IN_pagado += $monto;
					$iva_pagado	+= $monto * 0.15;
					break;
			}
			$sql		= "UPDATE tcb_prestamos_movimientos
								SET
							    capital_pagado=(capital_pagado + $capital_pagado),
							    interes_pagado= (interes_pagado + $IN_pagado),
							    iva_pagado=(iva_pagado + $iva_pagado),
							    interes_moratorio= (interes_moratorio + $IM_pagado),
							    iva_interes_moratorio=(iva_interes_moratorio  + $IvaMPagado),
							    comisiones=(comisiones + $comisiones),
							    iva_comisiones=(iva_comisiones + $iva_comisiones)
							WHERE
							    (numero_de_cliente=$socio)
							    AND
							    (numero_de_credito=$credito)
							    AND
							    (fecha_de_amortizacion='$fecha') ";
			$x		= my_query($sql, true);
			
			if ( ($x["stat"] == false) OR ($x["rows"] <= 0) ){
				$msg		.= "$socio\t$credito\t$operacion\tWARN\tSe fallo al actualizar el registro(" . $x["rows"] . "), se intenta uno nuevo\r\n";
				$sql	= "INSERT INTO tcb_prestamos_movimientos
								(numero_de_cliente, numero_de_credito, numero_de_pago, fecha_de_amortizacion,
								capital_a_pagar, interes_a_pagar, iva_por_el_interes_a_pagar,
								capital_pagado, interes_pagado, iva_pagado,
								interes_moratorio, iva_interes_moratorio, comisiones, iva_comisiones)
								VALUES
								($socio, $credito, 0, '$fecha',
								0, 0, 0,
								$capital_pagado, $IN_pagado, $iva_pagado,
								$IM_pagado, $IvaMPagado, $comisiones, $iva_comisiones) ";
								
								$x		= my_query($sql);
								if ( $x["stat"] == false){
									$msg	.= "$socio\t$credito\t$operacion\tERROR\tSe fallo al agregar el registro\r\n";
								}
			} else {
				$msg	.= "$socio\t$credito\t$operacion\tOK\tRegistro actualizado\r\n";
			}
		}
		$msg	    .= "============================ IMPORTANDO LETRAS PARA SISBANCS \r\n ";
		//separar de un pago
		//separar de pagos varios
		//acumular operaciones por pagar
		//acumular conceptos pagados
		$sqlIS	= "SELECT socio, credito, parcialidad,
					fecha_de_vencimiento, fecha_de_abono,
					saldo_vigente, saldo_vencido, interes_vigente, interes_vencido, saldo_interes_vencido,
					interes_moratorio, estatus, iva_interes_normal, iva_interes_moratorio
					FROM sisbancs_amortizaciones ";
		
		$rs	= mysql_query($sqlIS, cnnGeneral() );
		while( $rw = mysql_fetch_array($rs) ) {
			$credito	= $rw["credito"];
			$socio		= $rw["socio"];
			$fecha		= $rw["fecha_de_vencimiento"];
			//$monto		= $rw["monto"];
			$letra		= $rw["parcialidad"];
			$capital	= $rw["saldo_vigente"] + $rw["saldo_vencido"];
			$interes	= $rw["interes_vigente"] + $rw["interes_vencido"];
			$iva		= $rw["iva_interes_normal"];
			
			$sqlIM =  "INSERT INTO tcb_prestamos_movimientos
							(numero_de_cliente, numero_de_credito, numero_de_pago, fecha_de_amortizacion, capital_a_pagar,
							interes_a_pagar, iva_por_el_interes_a_pagar, capital_pagado, interes_pagado, iva_pagado,
							interes_moratorio, iva_interes_moratorio, comisiones, iva_comisiones)
							VALUES($socio, $credito, $letra, '$fecha', $capital,
							$interes, $iva, 0, 0, 0, 0, 0, 0, 0)";
							$xim = my_query($sqlIM);
							$msg	.= "$socio\t$credito\t$letra\tParcialidad de fecha $fecha por $capital; $interes; $iva IMPORTADA\r\n";
		}
		return $msg;
	}
}
class cMigracionSIBANCS extends cMigracion {
	function CompararPlanesDePago(){
		$msg	= "============================ COMPARANDO PLANES DE PAGO SISBANCS\r\n";
		//Efectua una Comparacion con los Datos del Plan de Pagos
		$sqlSC = "SELECT
								`creditos_solicitud`.*,
								`sisbancs_suma_amorizaciones`.*
							FROM
								`creditos_solicitud` `creditos_solicitud`
									INNER JOIN `sisbancs_suma_amorizaciones` `sisbancs_suma_amorizaciones`
									ON `creditos_solicitud`.`numero_solicitud` =
									`sisbancs_suma_amorizaciones`.`credito`
							/* WHERE
								 (`creditos_solicitud`.`saldo_actual` >" . TOLERANCIA_SALDOS . ") */ ";
		$rs 	= mysql_query($sqlSC, cnnGeneral() );
		$contar	= 0;
		$NetoDisminuir  = 0;
		$NetoCap        = 0;
		$NetoLetra      = 0;
		//Eliminar Letras cuyo capital es Cero o menor a cero
		$sql	= " DELETE FROM sisbancs_amortizaciones WHERE saldo_vigente < 0.99 ";
		$tx		= my_query($sql);
		$msg	.= "ELIMINANDO LETRAS CUYO CAPITAL ES MENOR A CERO (" . $tx["info"] . ")\r\n";
		
		while ( $rw = mysql_fetch_array($rs) ){
			$credito			= $rw["numero_solicitud"];
			$socio				= $rw["numero_socio"];
			$saldoActual		= $rw["saldo_actual"];
			
			$saldoSISBANCS		= $rw["capital_vigente"];
			$LimitLetras		= $rw["pagos_autorizados"];
			$diferencia			= ($saldoActual - $saldoSISBANCS);
			$PeriocidadDePago	= $rw["periocidad_de_pago"];
			//Datos del PLAN DE PAGOS
			$letraInicial		= $rw["letra_inicial"];
			$letraFinal			= $rw["letra_final"];
			$AEliminar			= $diferencia;
			
			$NetoCap            += $saldoActual;
			$NetoLetra          += $rw["capital_vigente"];
			$NetoDisminuir      += $diferencia;
			//TODO: Verificar la Validez de la Condicion
			if ( $diferencia < (TOLERANCIA_SALDOS * -1) ){
				$msg		.= "$contar\t$credito\tOBJETIVO\tLa Diferencia($diferencia) no es tolerable \r\n";
				$AEliminar	= ($diferencia * -1);
				//
				for ( $i = $letraInicial; $i <= $letraFinal; $i ++ ){
					$sqLetra = "SELECT
																`sisbancs_amortizaciones`.*
															FROM
																`sisbancs_amortizaciones` `sisbancs_amortizaciones`
															WHERE
																(`sisbancs_amortizaciones`.`credito` =$credito) AND
																(`sisbancs_amortizaciones`.`parcialidad` =$i)";
					$DLetra		= obten_filas($sqLetra);
					$LMonto		= $DLetra["saldo_vigente"];
					
					$PercTrunk	= 0;
					//Si eliminar es Mayor a la Letra, y la Letra es Mayor a 0.99
					if ( ($AEliminar >= $LMonto) AND ($LMonto > TOLERANCIA_SALDOS) AND ($AEliminar > 0) ){
						//Eliminar la Letra
						$sqlDL = "DELETE FROM
																`sisbancs_amortizaciones`
															WHERE
																(`sisbancs_amortizaciones`.`credito` =$credito) AND
																(`sisbancs_amortizaciones`.`parcialidad` =$i) ";
						$x	= my_query($sqlDL);
						
						$msg	.= "$contar\t$credito\tELIMINAR\tLetra $i (Disminuir $AEliminar / Letra $LMonto)\r\n";
						$AEliminar	-= $LMonto;
						//Si a eliminar es Menor a la Letra, y la Letra es mayor a 0.99
					} elseif ( ( $AEliminar < $LMonto ) AND ($LMonto > TOLERANCIA_SALDOS) AND ($AEliminar > 0) ) {
						//$LMonto		= $LMonto - $AEliminar;
						$PercTrunk	= ($AEliminar / $LMonto);
						
						$sqlUL = "UPDATE sisbancs_amortizaciones
																		SET saldo_vigente=saldo_vigente - (saldo_vigente * $PercTrunk),
																			saldo_vencido=saldo_vencido - (saldo_vencido * $PercTrunk),
																			interes_vigente=interes_vigente - (interes_vigente * $PercTrunk),
																			interes_vencido=interes_vencido - (interes_vencido * $PercTrunk),
																			saldo_interes_vencido=saldo_interes_vencido - (saldo_interes_vencido * $PercTrunk),
																			interes_moratorio=interes_moratorio - (interes_moratorio * $PercTrunk),
																			iva_interes_normal=iva_interes_normal - (iva_interes_normal * $PercTrunk),
																			iva_interes_moratorio=iva_interes_moratorio - (iva_interes_moratorio * $PercTrunk)
																		WHERE
																	credito=$credito AND parcialidad=$i ";
						$x = my_query($sqlUL); //(" . $x["info"] . ")
						$msg	.= "$contar\t$credito\tACTUALIZAR\tLetra $i con el Factor $PercTrunk ( LETRA:$LMonto / ELIMINAR:$AEliminar)\r\n";
						//$msg	.= $x["info"];
						
						$AEliminar	= 0;
					}
					if ($AEliminar < TOLERANCIA_SALDOS){
						$AEliminar	= 0;
					}
				}
			} elseif ( $diferencia > TOLERANCIA_SALDOS ){
				$sqLetra = "SELECT
																`sisbancs_amortizaciones`.*
															FROM
																`sisbancs_amortizaciones` `sisbancs_amortizaciones`
															WHERE
																(`sisbancs_amortizaciones`.`credito` = $credito)
																AND
																(`sisbancs_amortizaciones`.`parcialidad` = $letraInicial)";
				$DLetra		= obten_filas( $sqLetra );
				$fechaIn	= restardias( $DLetra["fecha_de_vencimiento"], $PeriocidadDePago);
				
				$nuevaLetra	= $letraInicial - 1;
				$msg		.= "$contar\t$credito\tAGREGAR\tEl Plan de Pagos es menor al saldo del Credito, se agrega la letra $nuevaLetra por $diferencia \r\n";
				$sqlIS		= "INSERT INTO sisbancs_amortizaciones(socio, credito, parcialidad, fecha_de_vencimiento,
														saldo_vigente, saldo_vencido, interes_vigente, interes_vencido, saldo_interes_vencido, interes_moratorio,
														estatus, iva_interes_normal, iva_interes_moratorio)
																VALUES ($socio, $credito, $nuevaLetra, '$fechaIn',
														$diferencia, 0, 0, 0, 0, 0,
														1, 0, 0)";
														$x		= my_query($sqlIS);
														//$msg	.= $x["info"];
			}
			
			$contar++;
		}
		$msg .=	"\t\t=============\tCAPITAL SAFE\t$NetoCap\r\n";
		$msg .=	"\t\t=============\tCAPITAL SISBANCS\t$NetoLetra\r\n";
		$msg .=	"\t\t=============\tDIFERENCIA NETA\t$NetoDisminuir\r\n";
		$msg .=	"\tFIN\t=================================================================\r\n";
		return $msg;
	}
	function setCrearLetras($EsSucursal, $EnDetalle, $Avisar){
		
		//Construir la Array de Letras
		
		$BySucursal		= "";
		$sucursal		= getSucursal();
		$arrLetras		= array();
		$arrFechas		= array();
		
		if ( $EsSucursal == "si"){
			$BySucursal	= " AND sucursal = '$sucursal' ";
		}
		//Eliminar las letras
		$sqlDSB		= "DELETE FROM `sisbancs_amortizaciones` ";
		my_query($sqlDSB);
		$msg		= "\t\tEliminar todas las letras\r\n";
		
		
		$sqlLetras	= "SELECT
							`operaciones_mvtos`.`socio_afectado`,
							`operaciones_mvtos`.`docto_afectado`,
							`operaciones_mvtos`.`fecha_afectacion`,
							`operaciones_mvtos`.`tipo_operacion`,
							`operaciones_mvtos`.`periodo_socio`,
							(`operaciones_mvtos`.`afectacion_real` *
							`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'
				
						FROM
							`operaciones_mvtos` `operaciones_mvtos`
								INNER JOIN `eacp_config_bases_de_integracion_miembros`
								`eacp_config_bases_de_integracion_miembros`
								ON `operaciones_mvtos`.`tipo_operacion` =
								`eacp_config_bases_de_integracion_miembros`.`miembro`
						WHERE
							(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2601)
							AND
							(`operaciones_mvtos`.`afectacion_real` >0)
							AND
							(`operaciones_mvtos`.`tipo_operacion` !=413)
				
						ORDER BY
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							`operaciones_mvtos`.`socio_afectado`,
							`operaciones_mvtos`.`docto_afectado`,
							`operaciones_mvtos`.`periodo_socio` ";
		$rsA		= getRecordset( $sqlLetras );
		while( $rw = mysql_fetch_array($rsA)){
			$arrLetras[ $rw["docto_afectado"] . "-" . $rw["periodo_socio"] . "-" . $rw["tipo_operacion"] ] = $rw["monto"];
			
			if ( !isset($arrFechas[ $rw["docto_afectado"] . "-" . $rw["periodo_socio"] . "-fecha" ] ) ){
				$arrFechas[ $rw["docto_afectado"] . "-" . $rw["periodo_socio"] . "-fecha" ] = $rw["fecha_afectacion"];
			}
		}
		$sqlCreds	= "SELECT
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*,
					`creditos_periocidadpagos`.*,
					`creditos_estatus`.*,
					`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
					`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
                    `creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
				FROM
					`creditos_tipoconvenio` `creditos_tipoconvenio`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
						= `creditos_solicitud`.`tipo_convenio`
							INNER JOIN `creditos_periocidadpagos`
							`creditos_periocidadpagos`
							ON `creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` =
							`creditos_solicitud`.`periocidad_de_pago`
								INNER JOIN `creditos_estatus`
								`creditos_estatus`
								ON `creditos_estatus`.`idcreditos_estatus` =
								`creditos_solicitud`.`estatus_actual`
				WHERE
					(`creditos_solicitud`.`saldo_actual` >0.99)
					AND (`creditos_solicitud`.`estatus_actual` !=50)
					$BySucursal";
					$rsC		= mysql_query($sqlCreds, cnnGeneral() );
					$contar		= 0;
					$NetoDisminuir  = 0;
					$NetoCap        = 0;
					$NetoLetra      = 0;
					
					while ( $rw = mysql_fetch_array($rsC) ) {
						//Validar el Credito
						$socio					= $rw["numero_socio"];
						$credito				= $rw["numero_solicitud"];
						$oficial				= $rw["oficial_credito"];
						$numero_pagos			= $rw["pagos_autorizados"];
						$TasaIVA				= $rw["tasa_iva"];
						$saldo_actual			= $rw["saldo_actual"];
						$periocidad_de_pago		= $rw["periocidad_de_pago"];
						$fecha_de_vencimiento	= $rw["fecha_vencimiento"];
						$interes_pagado			= $rw["interes_normal_pagado"];
						$interes_devengado		= $rw["interes_normal_devengado"];
						
						$NetoCap                += $saldo_actual;
						$TotalCap		        = 0;
						$TotalInt		        = 0;
						if ($periocidad_de_pago == 360){
							$numero_pagos	= 1;
						}
						$xc				= new cCredito($credito, $socio);
						$xc->initCredito($rw);
						//$msg .=	"$contarINICIO\t$credito\t=============\tSALDO\t$saldo_actual\r\n";
						
						for ($i=1; $i <= $numero_pagos; $i++){
							$capital	= 0;
							$interes	= 0;
							$lkey		= $credito . "-" . $i . "-";
							$fecha		= ( isset($arrFechas[$lkey . "fecha"]) ) ? $arrFechas[$lkey . "fecha"] : fechasys();
							$txtLog		= "";
							
							if ( $periocidad_de_pago != 360 ){
								
								//Si el Capital Existe
								if ( isset( $arrLetras[$lkey . 410] ) ){
									$capital	= $arrLetras[$lkey . 410];
								}
								//Si el Interes Existe
								if ( isset( $arrLetras[$lkey . 411] ) ){
									$interes	= $arrLetras[$lkey . 411];
								}
							} else {
								$fecha		= $fecha_de_vencimiento;
								$capital	= $saldo_actual;
								$interes	= setNoMenorQueCero( ($interes_devengado -  $interes_pagado) );
							}
							//recompocision a 2 digitos por letra
							$capital		= round($capital, 2);
							$interes		= round($interes, 2);
							$iva			= round( ($interes	* $TasaIVA), 2);
							//SUMAS
							$total_letra	= $capital + $interes + $iva;
							$TotalCap		+= $capital;
							$TotalInt		+= $interes;
							//Global
							$NetoLetra      += $capital;
							
							if ( $total_letra > TOLERANCIA_SALDOS ){
								$sqlI = "INSERT INTO sisbancs_amortizaciones
										(socio, credito, parcialidad, fecha_de_vencimiento, saldo_vigente, saldo_vencido,
										interes_vigente, interes_vencido,
										saldo_interes_vencido, interes_moratorio,
										estatus, iva_interes_normal, iva_interes_moratorio,
										fecha_de_abono)
										VALUES
										($socio, $credito, $i, '$fecha', $capital, 0,
										$interes, 0,
										0, 0, 1, $iva, 0,
										'$fecha')";
										my_query($sqlI);
										if ( $EnDetalle == "si" ){
											$msg			.= "$contar\tLETRA\t$credito\t$i\tAGREGANDO PARCIALIDAD POR $total_letra\r\n";
										}
							}
						}
						
						if ( ($TotalCap > ($saldo_actual + TOLERANCIA_SALDOS)) OR ($TotalCap < ($saldo_actual - TOLERANCIA_SALDOS) ) ){
							$txtLog .=	"$contar\tERROR\t$credito\tERROR EL SALDO($saldo_actual)ES DIFERENTE A LA SUMA DE LETRAS($TotalCap)\r\n";
							if ( $Avisar == "si" ){
								$xo			= new cOficial();
								$xo->addNote(iDE_CREDITO, $oficial, $socio, $credito, $txtLog);
							}
							$msg	.= $txtLog;
						}
						$msg .=	"$contar\t$credito\t=============\tCAPITAL\t$TotalCap\r\n";
						$msg .=	"$contar\t$credito\t=============\tINTERES\t$TotalInt\r\n";
						$msg .=	"$contar\tFIN\t=================================================================\r\n";
						$contar++;
					}
					return $msg;
	}
	function setCrearCaptacionNoExistente(){
		$msg	= "";
		$sql	= "SELECT * FROM sisbancs_temp_depositos WHERE
								(SELECT count(numero_cuenta) FROM captacion_cuentas WHERE numero_socio = sisbancs_temp_depositos.numero_de_socio
								 AND saldo_cuenta > 0.99) = 0";
		$rs		= getRecordset( $sql );
		while( $rw = mysql_fetch_array($rs) ){
			$cuenta		= "10" . $rw["numero_de_socio"] . "01";
			$socio		= $rw["numero_de_socio"];
			$cCta		= new cCuentaALaVista($cuenta);
			$cuenta		= $cCta->setNuevaCuenta(5, 1, $socio, "CUENTA_POR_AJUSTE_SISBANCS");
			//$cuenta	= 	$cCuenta->setNuevaCuenta(5, 1, $socio, "CUENTA_POR_AJUSTE");
			$msg		.= "$socio\t$cuenta\tCreando nueva cuenta\r\n";
		}
		return $msg;
	}
	function setEliminarCuentasNoExistentes(){
		$msg			= "";
		//Crear un nuevo Recibo de Ajuste
		$cRec		= new cReciboDeOperacion(10);
		$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_CAPTACION");
		$msg		.= "\t\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
		$cRec->setNumeroDeRecibo($xRec, true);
		//2011-01-15
		$sql 			= "SELECT
							`captacion_cuentas`.*,
							`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
							`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
							`captacion_cuentas`.`fecha_afectacion`              AS `apertura`,
							`captacion_cuentas`.`inversion_fecha_vcto`          AS `vencimiento`,
							`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
							`captacion_cuentas`.`tasa_otorgada`                 AS `tasa`,
										`captacion_cuentas`.`dias_invertidos`               AS `dias`,
										`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`,
										`captacion_cuentas`.`saldo_cuenta` 			        AS `saldo`,
										`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
										`captacion_subproductos`.`algoritmo_de_premio`,
										`captacion_subproductos`.`algoritmo_de_tasa_incremental`,
										`captacion_subproductos`.`metodo_de_abono_de_interes`,
										`captacion_subproductos`.`destino_del_interes`,
										`captacion_subproductos`.`nombre_del_contrato`,
										`captacion_subproductos`.`algoritmo_modificador_del_interes`
										FROM
										`captacion_cuentas` `captacion_cuentas`
											INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
											ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
											`idcaptacion_cuentastipos`
												INNER JOIN `captacion_subproductos` `captacion_subproductos`
												ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
												.`idcaptacion_subproductos`
										WHERE
											(
                                                SELECT COUNT(numero_de_socio) FROM sisbancs_temp_depositos WHERE numero_de_socio = captacion_cuentas.numero_socio ) = 0
											AND
											(`captacion_cuentas`.`tipo_cuenta` =10)
											AND
											(`captacion_cuentas`.`saldo_cuenta` > 0)
										ORDER BY
											`captacion_cuentas`.`saldo_cuenta`,
											`captacion_cuentas`.`fecha_afectacion` ";
		$rs			= getRecordset( $sql );
		$contar 	= 0;
		while( $rw = mysql_fetch_array($rs) ){
			$cuenta		= $rw["numero_cuenta"];
			$socio		= $rw["numero_socio"];
			$monto      = $rw["saldo_cuenta"];
			
			$cCuenta	= new cCuentaALaVista($cuenta);
			
			$cCuenta->init();
			
			$cCuenta->setReciboDeOperacion($xRec);
			$cCuenta->set($cuenta);
			$cCuenta->setForceOperations();
			$cCuenta->init($rw);
			$cCuenta->setRetiro($monto);
			
			$NuevoSaldo	= $cCuenta->getNuevoSaldo();
			$msg	.= "$contar\t$socio\t$cuenta\tACTUALIZAR\tActualizar la Cuenta a $NuevoSaldo, Anteriormente $monto\r\n";
			$msg	.= $cCuenta->getMessages("txt");
			//$msg	.= "$contar\t$socio\t$cuenta\tLa cuenta quedo en $NuevoSaldo\r\n";
			$contar++;
		}
		return $msg;
	}
	function setConciliarCuentas($AppSucursal){
		$msg	= "";
		$AppSucursal	= strtoupper($AppSucursal);
		$BySucursal		= " AND (`sucursal` ='" . getSucursal() . "')  ";
		if ( $AppSucursal != "SI" ){
			$BySucursal	= "";
		}
		//Crea un Array de los saldos de la Cuenta
		$arrSdoCtas		= array();
		$arrNumCtas		= array();
		$arrSdoCW		= array();
		//
		$sqlCW			= "SELECT
								COUNT(`sisbancs_temp_depositos`.`numero_de_socio`) AS `existentes`,
								`sisbancs_temp_depositos`.`numero_de_socio`
							FROM
								`sisbancs_temp_depositos` `sisbancs_temp_depositos`
				
							GROUP BY
								`sisbancs_temp_depositos`.`numero_de_socio` ";
		$rsA		= getRecordset($sqlCW );
		while( $rw = mysql_fetch_array($rsA)){
			$arrSdoCW[ $rw["numero_de_socio"] ] = $rw["existentes"];
			
		}
		unset($rsA);
		unset($rw);
		// obtiene las cuentas tipo 10[A LA VISTA] en safe y crea un array
		$sqlSdoCta		= " SELECT SQL_CACHE
							`captacion_cuentas`.`numero_socio`         AS `socio`,
							`captacion_cuentas`.`tipo_cuenta`	AS `tipo`,
							COUNT(`captacion_cuentas`.`numero_cuenta`) AS `cuentas`,
							SUM(`captacion_cuentas`.`saldo_cuenta`)    AS `suma`
						FROM
							`captacion_cuentas` `captacion_cuentas`
						WHERE
							(`captacion_cuentas`.`estatus_cuenta` != 99)
							AND
							(`captacion_cuentas`.`tipo_cuenta` = 10)
							$BySucursal
						GROUP BY
							`captacion_cuentas`.`numero_socio`,
							`captacion_cuentas`.`tipo_cuenta`
						ORDER BY
							`captacion_cuentas`.`tipo_cuenta` ";
							$rsA		= getRecordset($sqlSdoCta);
							while( $rw = mysql_fetch_array($rsA)){
								$msocio			= $rw["socio"];
								$arrSdoCtas[ $msocio . "-" . $rw["tipo"] ] = round($rw["suma"], 2);
								//OK: Verificar
								if (!isset( $arrSdoCW[ $rw["socio"] ] ) OR is_null( $arrSdoCW[ $rw["socio"] ] )  ){
									$msg	.= "\t$msocio\tAgregando un cuadre al socio " . $msocio  . " A COMPACW para Verificacion\r\n";
									$sqltmp	= "INSERT INTO sisbancs_temp_depositos(numero_de_socio, cuenta_contable, nombre, tipo_de_saldo, monto, sucursal)
    																			VALUES($msocio, '', '_AGREGADO_PARA_CUADRE_MONTO_" . $rw["suma"] . "', 0, 0, 'matriz')";
									my_query($sqltmp);
								}
							}
							unset($rsA);
							unset($rw);
							//============================================================================================================================
							$sqlCuentasSISBANCS	= "SELECT SQL_CACHE
										`temp_captacion_por_socio`.`numero_socio`,
										`temp_sisbancs_depositos`.`numero_de_socio`,
										`temp_captacion_por_socio`.`tipo_cuenta`,
										ROUND(`temp_captacion_por_socio`.`monto`, 2) AS `saldo_safe`,
										`temp_sisbancs_depositos`.`total`,
										`temp_sisbancs_depositos`.`cuentas`,
										ROUND((`temp_sisbancs_depositos`.`total`  - `temp_captacion_por_socio`.`monto`), 2) AS 'diferencia'
										
									FROM
										`temp_captacion_por_socio` `temp_captacion_por_socio`
											INNER JOIN `temp_sisbancs_depositos` `temp_sisbancs_depositos`
											ON `temp_captacion_por_socio`.`numero_socio` = `temp_sisbancs_depositos`
											.`numero_de_socio`
									WHERE
										(`temp_captacion_por_socio`.`tipo_cuenta` =10)
										$BySucursal
									HAVING
										(diferencia > 0.02)
										OR
										(diferencia < -0.02)
									ORDER BY
										diferencia
								  /* LIMIT 0,600 */ ";
										$rs				= getRecordset($sqlCuentasSISBANCS );
										$contar			= 0;
										
										//Crear un nuevo Recibo de Ajuste
										$cRec		= new cReciboDeOperacion(10);
										$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_CAPTACION");
										$msg	.= "\t\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
										$cRec->setNumeroDeRecibo($xRec, true);
										//$cRec->initRecibo();
										$msg	.= "\t\t============= \r\n";
										$msg	.= "\t\t============= APLICANDO CUENTAS DESDE COMPACW\r\n";
										$msg	.= "\t\t============= \r\n";
										while ( $rw = mysql_fetch_array($rs) ){
											
											$socio		= $rw["numero_de_socio"];
											$ahorro		= round($rw["total"], 2);
											$NCuentas	= $rw["cuentas"];
											$Monto		= 0;
											
											//Si el saldo EXISTE Y es Diferente a NULL
											if ( isset($arrSdoCtas["$socio-10"]) AND !is_null($arrSdoCtas["$socio-10"] ) ){
												$Monto	= $arrSdoCtas["$socio-10"];
											}
											
											//SI es mayor el Monto que el Ahorro, entonces esta inflado la parte Operativa.- Saldo Negativo
											$diferencia	= $ahorro - $Monto;
											//Si la Difrencia es menor a -0.99 entonces
											if ( $diferencia < (TOLERANCIA_SALDOS * (-1) ) ){
												$diferencia		= $diferencia * (-1);
												$msg			.= "$contar\t$socio\tEXCESO\tExiste un monto en exceso de $diferencia en SAFE, debe tener $ahorro segun COMPACW\r\n";
												//FIXME: globalizar 5
												//TODO: Cambiar esta linea
												$sqlCSoc	= "SELECT
											`captacion_cuentas`.*,
											`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
											`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
											`captacion_cuentas`.`fecha_afectacion`              AS `apertura`,
											`captacion_cuentas`.`inversion_fecha_vcto`          AS `vencimiento`,
											`captacion_cuentas`.`tasa_otorgada`                 AS `tasa`,
											`captacion_cuentas`.`dias_invertidos`               AS `dias`,
											`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`,
											`captacion_cuentas`.`saldo_cuenta` 			        AS `saldo`,
											`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
											`captacion_subproductos`.`algoritmo_de_premio`,
											`captacion_subproductos`.`algoritmo_de_tasa_incremental`,
											`captacion_subproductos`.`metodo_de_abono_de_interes`,
											`captacion_subproductos`.`destino_del_interes`,
											`captacion_subproductos`.`nombre_del_contrato`,
											`captacion_subproductos`.`algoritmo_modificador_del_interes`
										FROM
										`captacion_cuentas` `captacion_cuentas`
											INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
											ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
											`idcaptacion_cuentastipos`
												INNER JOIN `captacion_subproductos` `captacion_subproductos`
												ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
												.`idcaptacion_subproductos`
										WHERE
											(`captacion_cuentas`.`numero_socio` =$socio)
											AND
											(`captacion_cuentas`.`tipo_cuenta` =10)
											AND
											(`captacion_cuentas`.`saldo_cuenta` != 0)
										ORDER BY
											`captacion_cuentas`.`saldo_cuenta`,
											`captacion_cuentas`.`fecha_afectacion` ";
												
												$rsCSoc			= getRecordset( $sqlCSoc );
												while ( $CRw = mysql_fetch_array($rsCSoc) ){
													$cuenta		= $CRw["numero_cuenta"];
													$saldo		= $CRw["saldo_cuenta"];
													//Si la Diferencia es mayor al saldo de la cuenta, entonces
													if ( ($diferencia >= $saldo) AND ($diferencia > 0) ){
														//Retirar el saldo de la cuenta
														$cCuenta	= new cCuentaALaVista($cuenta);
														$cCuenta->setReciboDeOperacion($xRec);
														$cCuenta->set($cuenta);
														$cCuenta->setForceOperations();
														$cCuenta->init($CRw);
														$cCuenta->setRetiro($saldo);
														//Quitar el saldo de la cuenta de la diferencia
														$diferencia	= $diferencia - $saldo;
														//Mensaje
														$msg	.= "$contar\t$socio\t$cuenta\tELIMINAR\tEliminando el saldo de la cuenta por $saldo, queda $diferencia\r\n";
														$msg	.= $cCuenta->getMessages("txt");
														
													} elseif ( ($diferencia < $saldo) AND ($diferencia > 0) ){
														//Restar la diferencia y dejar el saldo de la cuenta con el saldo de la cuenta
														$NSaldo		= $saldo - $diferencia;
														
														$cCuenta	= new cCuentaALaVista($cuenta);
														$cCuenta->setReciboDeOperacion($xRec);
														$cCuenta->set($cuenta);
														$cCuenta->setForceOperations();
														$cCuenta->init($CRw);
														$cCuenta->setRetiro($diferencia);
														$msg	.= "$contar\t$socio\t$cuenta\tACTUALIZAR\tActualizar la Cuenta a $NSaldo, Anteriormente $saldo\r\n";
														$NuevoSaldo	= $cCuenta->getNuevoSaldo();
														$msg	.= $cCuenta->getMessages("txt");
														
														$msg	.= "$contar\t$socio\t$cuenta\tSALDO\tLa cuenta quedo en $NuevoSaldo\r\n";
														//Llevar a Cero la Diferencia
														$diferencia	= 0;
														
													} else {
														$msg	.= "$contar\t$socio\tIGNORAR\tNo efectuo ninguna accion (SAFE: $Monto / CW: $ahorro)\r\n";
													}
													if ( $diferencia <= TOLERANCIA_SALDOS){
														$diferencia		= 0;
													}
												}
												
												$msg	.= "$contar\t$socio\tFIN_RET\t------\t------\t------\t------\t------\t------\t------\r\n";
												//Diferencia:	Si la Diferencia es Mayor a 0.99
											} elseif ($diferencia > TOLERANCIA_SALDOS) {
												$msg	.= "$contar\t$socio\tINSUFICIENCIA\tExiste Insuficiencia de $diferencia en SAFE (SAFE: $Monto / CW: $ahorro)\r\n";
												//Obtener una Cuenta
												//FIXME: Globalizar 6
												//TODO: Actualizar esta linea
												$sqlCSoc	= "SELECT
										`captacion_cuentas`.*,
										`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
										`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
										`captacion_cuentas`.`fecha_afectacion`              AS `apertura`,
										`captacion_cuentas`.`inversion_fecha_vcto`          AS `vencimiento`,
										`captacion_cuentas`.`tasa_otorgada`                 AS `tasa`,
										`captacion_cuentas`.`dias_invertidos`               AS `dias`,
										`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`,
										`captacion_cuentas`.`saldo_cuenta` 			        AS `saldo`,
										`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
										`captacion_subproductos`.`algoritmo_de_premio`,
										`captacion_subproductos`.`algoritmo_de_tasa_incremental`,
										`captacion_subproductos`.`metodo_de_abono_de_interes`,
										`captacion_subproductos`.`destino_del_interes`,
										`captacion_subproductos`.`nombre_del_contrato`,
										`captacion_subproductos`.`algoritmo_modificador_del_interes`
										FROM
										`captacion_cuentas` `captacion_cuentas`
											INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
											ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
											`idcaptacion_cuentastipos`
												INNER JOIN `captacion_subproductos` `captacion_subproductos`
												ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
												.`idcaptacion_subproductos`
										WHERE
											(`captacion_cuentas`.`numero_socio` =$socio)
											AND
											(`captacion_cuentas`.`tipo_cuenta` =10)
										ORDER BY
											`captacion_cuentas`.`saldo_cuenta` DESC,
											`captacion_cuentas`.`fecha_afectacion` DESC
										LIMIT 0,1 ";
												$DCta			= obten_filas($sqlCSoc);
												$cuenta			= $DCta["numero_cuenta"];
												$cCuenta		= new cCuentaALaVista($cuenta);
												$NSaldo			= 0;
												//Si la cuenta no existe, crear una nueva
												if ( !isset($cuenta) OR ($cuenta == 0) OR ($cuenta == false) ){
													$cuenta	= 	$cCuenta->setNuevaCuenta(5, 1, $socio, "CUENTA_POR_AJUSTE");
													$msg	.= 	"$contar\t$socio\t$cuenta\tNUEVA\tNO Existe la Cuenta, se crea una NUEVA\r\n";
													$DCta	= false;
												}
												$cCuenta->set($cuenta);
												$cCuenta->init($DCta);
												$cCuenta->setReciboDeOperacion($xRec);
												$cCuenta->setDeposito($diferencia);
												$NSaldo	= $cCuenta->getNuevoSaldo();
												$msg	.= "$contar\t$socio\t$cuenta\tAGREGAR\tSe Agrega la Cuenta un monto de $diferencia, Saldo de $NSaldo\r\n";
												$msg	.= $cCuenta->getMessages("txt");
												$diferencia = 0;
											}
											//$msg	.= "==========================================================================\r\n";
											$contar++;
										}
										
										$cRec->setFinalizarRecibo();
										$msg	.= $cRec->getMessages("txt");
										return $msg;
	}
	function setConciliarCreditos (){
		$msg		= "";
		$cRec		= new cReciboDeOperacion(10);
		$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_PLAN_DE_PAGOS");
		$msg	.= "\t\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
		$cRec->setNumeroDeRecibo($xRec, true);
		
		//Concilia Creditos sin Planes de Pago a SISBANCS
		$sql = "SELECT
								`migracion_creditos_por_socio`.`numero_socio`,
								`migracion_creditos_por_socio`.`creditos`,
								`migracion_creditos_por_socio`.`saldo`,
								getCreditosCompac(numero_socio) AS `saldo_compac`,
								( `migracion_creditos_por_socio`.`saldo` -  getCreditosCompac(numero_socio)) AS 'diferencia'
							FROM
								`migracion_creditos_por_socio` `migracion_creditos_por_socio`
				
							HAVING
								(diferencia >0.99
								OR
								diferencia < -0.99)";
		$rs			= getRecordset($sql );
		while ($rw = mysql_fetch_array($rs)) {
			$socio		 	= $rw["numero_socio"];
			$sqlCred			= "SELECT
													`creditos_solicitud`.*,
													`creditos_tipoconvenio`.*,
													`creditos_periocidadpagos`.*,
													`creditos_estatus`.*,
													`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
													`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
								                    `creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
																	FROM
														`creditos_tipoconvenio` `creditos_tipoconvenio`
															INNER JOIN `creditos_solicitud` `creditos_solicitud`
															ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
															= `creditos_solicitud`.`tipo_convenio`
																INNER JOIN `creditos_periocidadpagos`
																`creditos_periocidadpagos`
																ON `creditos_periocidadpagos`.
																`idcreditos_periocidadpagos` =
																`creditos_solicitud`.`periocidad_de_pago`
																	INNER JOIN `creditos_estatus`
																	`creditos_estatus`
																	ON `creditos_estatus`.`idcreditos_estatus` =
																	`creditos_solicitud`.`estatus_actual`
																	WHERE
																		(`creditos_solicitud`.`numero_socio` = $socio )
														ORDER BY fecha_vencimiento ASC, saldo_actual DESC,
														fecha_solicitud DESC
														
														";
			$ajuste		= $rw["diferencia"];
			$SdoCW		= $rw["saldo_compac"];
			$SdoSAFE	= $rw["saldo"];
			
			$rsC		= getRecordset($sqlCred );
			while ( $rwC= mysql_fetch_array($rsC) ){
				$credito	= $rwC["numero_solicitud"];
				$saldo		= $rwC["saldo_actual"];
				
				$cCredito	= new cCredito($credito, $socio);
				$cCredito->init($rwC);
				
				$DCred		= $cCredito->getDatosDeCredito();
				
				$TAjustar	= 0;
				
				//Generar un abono a Capital
				//si el ajuste es mayo a 0.09 o menor que -0.99 proseguir::
				if ( ($ajuste > 0.09) OR ($ajuste < -0.09) ){
					
					//Si 100 > 0.09 Y 0 < 0.09
					if ( ($ajuste > 0.09) AND ($saldo <= 0.09) ){
						$msg	.= "$socio\t$credito\tSe ignora el Credito por no tener Saldo (COMPACW $SdoCW / Credito $saldo)\r\n";
					} else {
						// 50 > 30
						//500 > -140
						if ( $ajuste > $saldo ){
							//saldo <= 0
							if ( $saldo <= 0 ){
								//justar	= 500
								$TAjustar	= $ajuste;
								//xajustar	= 0
								$ajuste		= 0;
							} else {
								//ajuste	= 30;
								$TAjustar	= $saldo;
								//xajustar	= 50 - 30 = 20;
								$ajuste		= $ajuste - $saldo;
							}
							//80 < 100
						} elseif( $ajuste < $saldo ) {
							//ajuste	= 80;
							$TAjustar	= $ajuste;
							//xajustar	= 0;
							$ajuste		= 0;
						} elseif( $ajuste == $saldo ) {
							//80 == 80
							//ajustar	= 80
							$TAjustar	= $ajuste;
							//xajustar	= 0;
							$ajuste		= 0;
						}
						$cCredito->setReciboDeOperacion($xRec);
						$cCredito->setAbonoCapital($TAjustar);
						$msg	.= "$socio\t$credito\tRealizando un Ajuste de $TAjustar (COMPACW $SdoCW / Credito $saldo)\r\n";
						$msg	.= $cCredito->getMessages("txt");
					}
				} else {
					$msg	.= "$socio\t$credito\tNo se Realizan NINGUN ajuste (SAFE $SdoSAFE / COMPACW $SdoCW / Ajuste $ajuste)\r\n";
				}
				
			}
			$msg	.= "=============================\t$socio\t===========================\r\n";
			//$msg	.=  $cCredito->getMessages("txt");
		}
		$cRec->setFinalizarRecibo(true);
		$msg			.= $cRec->getMessages("txt");
		return $msg;
	}
	function setGenerarPlanDePagos(){
		$msg	= "";
		$cRec		= new cReciboDeOperacion(10);
		$xRec		= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_AJUSTES_DE_PLAN_DE_PAGOS");
		$msg	.= "\t\tRECIBO\tEl Recibo de Operacion es $xRec\r\n";
		$cRec->setNumeroDeRecibo($xRec, true);
		
		//Concilia Creditos sin Planes de Pago a SISBANCS
		$sql = "SELECT * FROM creditos_solicitud WHERE (SELECT
								COUNT(credito) FROM sisbancs_suma_amorizaciones
								WHERE credito = creditos_solicitud.numero_solicitud) = 0
								AND saldo_actual > 0
								AND estatus_actual != 50 ";
		$rs			= getRecordset( $sql );
		while ($rw = mysql_fetch_array($rs)) {
			$socio		 	= $rw["numero_socio"];
			$credito	 	= $rw["numero_solicitud"];
			$saldo_actual	= $rw["saldo_actual"];
			$letra			= $rw["ultimo_periodo_afectado"] + 1;
			$fecha			= sumardias($rw["fecha_ultimo_mvto"], $rw["periocidad_de_pago"]);
			$monto			= $saldo_actual;
			
			$msg			.= "$socio\t$credito\tAGREGAR\tUnica Letra por el SALDO de $saldo_actual \r\n";
			
			$sqlIS			= "INSERT INTO sisbancs_amortizaciones(socio, credito, parcialidad, fecha_de_vencimiento,
														saldo_vigente, saldo_vencido, interes_vigente, interes_vencido, saldo_interes_vencido, interes_moratorio,
														estatus, iva_interes_normal, iva_interes_moratorio)
																VALUES ($socio, $credito, $letra, '$fecha',
														$saldo_actual, 0, 0, 0, 0, 0,
														1, 0, 0)";
														$cRec->setNuevoMvto($fecha, $monto, 410, $letra, "", 1, false, $socio, $credito);
														$x		= my_query($sqlIS);
														
														
														if ( $x["stat"] == false ){
															$msg		.= "$socio\t$credito\tERROR\t   \r\n";
														}
		}
		$msg			.= $cRec->getMessages("txt");
		return $msg;
	}
	function setRepararPlanDePagos(){
		$msg		= "";
		$msg	.= "============= RECONSTRUYENDO LETRAS SISBANCS \r\n";
		
		//Selecciona todo los pagos segun letra, en una base
		
		$arrFechas		= array();
		$arrMontos		= array();
		
		$sqlLetras	= "SELECT SQL_CACHE
							`operaciones_mvtos`.`docto_afectado`,
							`operaciones_mvtos`.`periodo_socio`,
							MAX(`operaciones_mvtos`.`fecha_afectacion`) AS 'fecha',
							SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
						FROM
							`operaciones_mvtos` `operaciones_mvtos`
								INNER JOIN `eacp_config_bases_de_integracion_miembros`
								`eacp_config_bases_de_integracion_miembros`
								ON `operaciones_mvtos`.`tipo_operacion` =
								`eacp_config_bases_de_integracion_miembros`.`miembro`
						WHERE
							(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2003)
							AND
							(`operaciones_mvtos`.`afectacion_real` >0)
						GROUP BY
							`operaciones_mvtos`.`docto_afectado`,
							`operaciones_mvtos`.`periodo_socio`
						ORDER BY
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							`operaciones_mvtos`.`socio_afectado`,
							`operaciones_mvtos`.`docto_afectado`,
							`operaciones_mvtos`.`periodo_socio` ";
		$rsA		= getRecordset( $sqlLetras );
		while( $rw = mysql_fetch_array($rsA)){
			$arrFechas[ $rw["docto_afectado"] . "-" . $rw["periodo_socio"] ] = $rw["fecha"];
			$arrMontos[ $rw["docto_afectado"] . "-" . $rw["periodo_socio"] ] = $rw["monto"];
		}
		$fecha_de_migracion		= fechasys();
		//DELETE FROM sisbancs_amortizaciones WHERE credito = 0 AND parcialidad = 1
		$sql = "SELECT
								`sisbancs_amortizaciones`.*
							FROM
								`sisbancs_amortizaciones` `sisbancs_amortizaciones` ";
		$rs			= getRecordset( $sql );
		$contar		= 0;
		while ($rw = mysql_fetch_array($rs) ){
			$credito			= $rw["credito"];
			$socio				= $rw["credito"];
			$parcialidad		= $rw["parcialidad"];
			$vencimiento		= $rw["fecha_de_vencimiento"];
			$saldo_vigente		= $rw["saldo_vigente"];
			$saldo_vencido		= 0;
			$interes_vigente	= $rw["interes_vigente"];
			$interes_vencido	= 0;
			$interes_moratorio	= 0;
			$dias_en_mora		= 0;
			
			$estatus			= $rw["estatus"];
			$fecha_de_abono		= $rw["fecha_de_abono"];
			$iva_normal			= 0;
			$iva_moratorio		= 0;
			$tasa_normal		= 0;
			$tasa_moratorio		= 0;
			$monto_abonado		= 0;
			$saldo_teorico		= 0;
			
			$DCredito			= array();
			//TODO: Actualizar sentencia de obtencion de IVA
			$msg	.= "$contar\t$credito\t$parcialidad\t\t=================================================\r\n";
			//Actualizar le fecha de Pago
			if ( isset($arrFechas["$credito-$parcialidad"] ) ){
				$fecha_de_abono		= $arrFechas["$credito-$parcialidad"];
				$monto_abonado		= $arrMontos["$credito-$parcialidad"];
				
				//Corrige las idioteces de reestructuras
				
				if ( strtotime($vencimiento) > strtotime($fecha_de_abono) ){
					$fecha_de_abono	= $vencimiento;
					$msg	.= "$contar\t$credito\t$parcialidad\tERROR_DE_FECHA\tLa fecha de abono(" . getFechaMediana($fecha_de_abono) . ") es menor a la de vencimiento " . getFechaMediana($vencimiento) . " \r\n";
				}
				$saldo_teorico		= $saldo_vigente - $monto_abonado;
				$msg	.= "$contar\t$credito\t$parcialidad\tFECHA_DE_ABONO\tLa fecha de Abono Existente es " . getFechaMediana($fecha_de_abono) . " y suma de $monto_abonado (saldo teorico $saldo_teorico)\r\n";
			}
			
			if ( strtotime($vencimiento) < strtotime($fecha_de_migracion) ){
				$msg	.= "$contar\t$credito\t$parcialidad\tFECHA_DE_VCTO\tLa Vencimiento (" . getFechaMediana($vencimiento) . ") es Menor a la Fecha de Migracion\r\n";
				$estatus			= 2;
				$saldo_vencido		= $saldo_vigente;
				$saldo_vigente		= 0;
				$interes_vencido	= $interes_vigente;
				$interes_vigente	= 0;
				$xCred				= new cCredito($credito, $socio);
				$xCred->init();
				$DCredito			= $xCred->getDatosDeCredito();
				$tasa_moratorio		= $DCredito["tasa_moratorio"];
				
				$dias_morosos		= setNoMenorQueCero( restarfechas($fecha_de_migracion, $fecha_de_abono) );
				$interes_moratorio	= ($saldo_vencido * $dias_morosos * $tasa_moratorio) / EACP_DIAS_INTERES;
				$msg	.= "$contar\t$credito\t$parcialidad\tINTERES_MORATORIO\tEl Interes Moratorio es $interes_moratorio, por $dias_morosos dias en Mora y Capital $saldo_vencido\r\n";
			}
			$iva_normal				= ($interes_vigente + $interes_vencido)	* 0.15;
			$iva_moratorio			= $interes_moratorio * 0.15;
			$sqlUD			= "UPDATE sisbancs_amortizaciones
												    SET  fecha_de_abono='$fecha_de_abono', saldo_vigente=$saldo_vigente,
													saldo_vencido=$saldo_vencido, interes_vigente=$interes_vigente, interes_vencido=$interes_vencido,
													saldo_interes_vencido=0, interes_moratorio=$interes_moratorio, estatus=$estatus,
													iva_interes_normal=$iva_normal, iva_interes_moratorio=$iva_moratorio
												    WHERE
													credito=$credito, parcialidad=$parcialidad ";
			my_query($sqlUD);
			$contar++;
		}
		return $msg;
	}
}

?>
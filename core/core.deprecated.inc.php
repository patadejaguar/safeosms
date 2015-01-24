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
			periodo_contable, periodo_cobranza, periodo_seguimiento,
			periodo_anual, periodo_mensual, periodo_semanal,
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
function getFila($sql, $fila) {
	$mival = 0;
		$reslf = mysql_query($sql, cnnGeneral());
			if(!$reslf){
				saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Func getFila: Depurar :" . mysql_error() . "|||Numero: " .mysql_errno() . "|||Instruccion SQL: \n ". $sql . " EN:" . $_SESSION["current_file"]);
			}
	$rw = mysql_fetch_array($reslf);
		$mival = $rw[$fila];

		if($mival == "" or (!isset($mival))  ) {
			$mival = 0;
		}

	return $mival;
	@mysql_free_result($reslf);
}
//.- -------------------- funcion para enviar sentencias SQL general. ---------------------------
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
function namesoc($idsocio) { return getNombreSocio($idsocio); }
function getNombreSocio($codigo){ $xSoc = new cSocio($codigo); $xSoc->init(); return $xSoc->getNombreCompleto(); }
function sociodom($idsocio){ $xSoc = new cSocio($idsocio); return $xSoc->getDomicilio(); }
function domicilio($socio = 0){ $xSoc = new cSocio($socio); return $xSoc->getDomicilio(); }
function getSocioDomicilio($socio = 0){	$xSoc = new cSocio($socio); return $xSoc->getDomicilio(); }
function folios($idfol){ $myfol = getFolio($idfol); return $myfol; }
/**
 * funcion que grava los ultimos folios
 * @deprecated 1.5 - 22/03/2009
 */
function wfolios($idw, $sval){}
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
 * Elimina Mvtos segun Parametro dado
 * @param long $iddocto
 * @param int $tipomvto
 * @param int $periodo
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
// funcion que devuelve el nombre del usuario o su puesto segun indice dado
function elusuario($eluser, $quequiere = false) {
	if(!isset($eluser) OR $eluser == false){
		$eluser		= getUsuarioActual();
	}
	/*$xUsr			= new cSystemUser($eluser);
	$xUsr->init();*/
	
	settype($eluser, "string");
	$sqlusr 		= "SELECT * FROM usuarios WHERE idusuarios=$eluser";
	$rsusr 			= mysql_query($sqlusr, cnnGeneral());
	$loquepedia 	="";
	if(!$rsusr){
		saveError(2,getUsuarioActual(), "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL: \n ". $sqlusr . "|EN:" . $_SESSION["current_file"]);
	} else {
		while ($rwu = mysql_fetch_array($rsusr)) {
			if($quequiere == false) {
				$loquepedia= "$rwu[4] $rwu[5] $rwu[3]";
			} elseif ($quequiere == 3) {
				$loquepedia= "NO AUTORIZADO";
			} else {
				$loquepedia= "$rwu[$quequiere]";
			}
		}
	}
	return $loquepedia;
	@mysql_free_result($rsusr);
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
		$sqltasa = "SELECT tasa_efectiva
		FROM captacion_tasas
		WHERE modalidad_cuenta=$tipocta
		AND $monto>=monto_mayor_a AND $monto<monto_menor_a
		AND subproducto=$subproducto
		LIMIT 0,1";
		//setLog($sqltasa);
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
 * @param sttring $sqlMQ
 * @param string $debug_warns
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
		if ($sql != "" AND ( strlen($sql) > 6 ) ) {
			$rs 	= getRecordset($sql, cnnGeneral());
			if($rs != false) {
				$mFilas = mysql_fetch_array($rs);
			} else {
				$errNotice	= @mysql_error($cnx);
				$errNumber	= @mysql_errno($cnx);
				saveError(2, getUsuarioActual(), "Error :$errNotice|Numero: $errNumber|SQL: ". $sql. "|" . $_SESSION["current_file"]);
			}
			@mysql_free_result($rs);
		}
	return $mFilas;
}
function contrato($id, $peticion){
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
 * @param	date	$dia_a_evaluar		Fecha que se evalua
 * @return	date	Dia Laborable
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
	$mvalue = round($mvalue, 2);
	$cval = number_format($mvalue, 2, '.', ',');
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
	private $mEstruct = "";
	private $mInfoF = array();
	function __construct($aplicado_a){
		$sql = "SELECT * FROM general_formulas WHERE aplicado_a='$aplicado_a' LIMIT 0,1";

		$rs = mysql_query($sql, cnnGeneral());
			if( !$rs ){
				saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL: \n ". $sql . "|EN:" . $_SESSION["current_file"]);
			}
			$this->mInfoF = mysql_fetch_array($rs);
			$this->mEstruct = $this->mInfoF["estructura_de_la_formula"];
			@mysql_free_result($rs);
	}
	function setEval(){
		eval($this->mEstruct);
	}
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

function getFolio($tipo){
	//Obtener el Ultimo Folio
	//Sumar + 1 y Registrarlo
	$myfol 	= 0;
	$idfol	= $tipo;
	$add	= true;

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
		//FIXME: Verificar movimientos de variables no existentes, ejemplo $cheque
		//Verificar validez y funcionamiento - verificado. dic 2011
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
				
				$this->mMessages	.= "$socio\t$documento\tCREDITOS\tLos Datos Contables se cargan\r\n";
			}
			if(isset($esCaptacion) AND ( $esCaptacion !== false) ){
				/**
	 			* Obtiene Informacion para Cuentas Captacion
	 			*/
				$sqliCaptacion 		= "SELECT * FROM captacion_datos_contables WHERE numero_cuenta=$documento LIMIT 0,1";
				/*AND numero_socio=$socio*/
				$captacion 			= obten_filas($sqliCaptacion);
				$this->mMessages	.= "$socio\t$documento\tCAPTACION\tLos Datos Contables se cargan\r\n";
			}
			//setLog($formula);
			eval($formula);
			$this->mReturn 			= $cuenta;
			$this->mMessages		.= "$socio\t$documento\tEVALUAR\tLa Cuenta se EVALUA y queda en $cuenta\r\n";
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
						setLog($sqliCartera);
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
			$this->mReturn = 		$cuenta;
		}
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
 * @param integer 	$recibo		Numero de Recibo
 * @param integer 	$tipo_mvto	Tipo de Operacion
 * @param float 	$monto		Monto de la Operacion
 * @param integer	$socio		Numero de Socio
 * @param integer 	$docto		Numero de documento
 * @param integer 	$operacion	Tipo de Operacion Contable CARGO/ABONO
 * @param integer 	$usuario	Usuario de la Operacion
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
			my_query($sqlI);
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
function setUltimoCheque($banco, $cheque = 0){
	$documento = 1;
	if($cheque==0) {
		//Obtiene el Cheque de un Conteo SQL
		$sql = "SELECT numero_de_documento
		FROM bancos_operaciones
		WHERE cuenta_bancaria = $banco
		ORDER BY idcontrol ASC, fecha_expedicion ASC
		LIMIT 0,1";
		$documento = getFila($sql, "numero_de_documento");
		$documento = getNumeroPorCadena($documento);
		$documento = $documento + 1;
	} else {
		$documento = getNumeroPorCadena($cheque);
	}

	$sqlD = "UPDATE bancos_cuentas SET consecutivo_actual = $documento
	WHERE idbancos_cuentas = $banco";
	my_query($sqlD);

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
			$sucess		= false;
			// --------------------------------------- VALOR SQL DEL MVTO.-------------------------------------------------------
				// VALORES FIJOS
			$smf	= "idusuario, codigo_eacp, socio_afectado, docto_afectado, recibo_afectado, fecha_operacion, ";
				// PERIODOS
			$smf	.= "periodo_contable, periodo_cobranza, periodo_seguimiento, ";
			$smf	.= "periodo_anual, periodo_mensual, periodo_semanal, ";
				// AFECTACIONES
			$smf	.= "afectacion_cobranza, afectacion_contable, afectacion_estadistica, ";
			$smf	.= "afectacion_real, valor_afectacion, ";
				// FECHAS Y TIPOS
			$smf	.= "idoperaciones_mvtos, tipo_operacion, estatus_mvto, periodo_socio, ";
			$smf	.= "fecha_afectacion, fecha_vcto, ";
				// SALDOS
			$smf	.= "saldo_anterior, saldo_actual, detalles, sucursal, tasa_asociada, dias_asociados";
			//
			$iduser 		= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];
			$eacp 			= EACP_CLAVE;
											// PERIODOS
			$percont 		= EACP_PER_CONTABLE;										// Periodo Contable
			$percbza 		= EACP_PER_COBRANZA;										// Periodo Cobranza.
			$perseg 		= EACP_PER_SEGUIMIENTO;										// Period de Seguimiento.
			$permens 		= date("m", strtotime($fecha_operacion) );					// Periodo mes
			$persem 		= date("W", strtotime($fecha_operacion) );					// Periodo de dias en la semana.
			$peranual 		= date("Y", strtotime($fecha_operacion) );					// A�o Natural.
			$persoc 		= $parcialidad;												// periodo del Socio.
			$estatus_mvto 	= 30;

			//$signo_afectacion = 1;
			//Obtiene la Afectacion por la afectacion el recibo
				//$sqlTMvto 	= "SELECT afectacion_en_recibo FROM operaciones_tipos WHERE idoperaciones_tipos=$tipo_operacion LIMIT 0,1";
				//$dFils		= obten_filas($sqlTMvto);
				//$signo_afectacion = $dFils["afectacion_en_recibo"];
				if(!$signo_afectacion){
					$signo_afectacion = 1;
				}

			$fecha_vcto = sumardias($fecha_operacion, DIAS_PAGO_VARIOS);
			$sucursal 	= getSucursal();

			if($monto < 0){
				//$signo_afectacion = -1;
				//$monto = $monto  * -1;
			}

			$afect_cbza		= $monto;
			$afect_seg		= $monto;
			$afect_cont		= $monto;
			$afect_esta		= $monto;
			$idoperacion 	= folios(2);
			$smv = "$iduser, '$eacp', $socio, $solicitud, $recibo, '$fecha_operacion',
					$percont, $percbza, $perseg, $peranual, $permens, $persem,
					$afect_cbza, $afect_cont, $afect_esta,
					$monto, $signo_afectacion,
					$idoperacion, $tipo_operacion, $estatus_mvto, $persoc,
					'$fecha_operacion', '$fecha_vcto',
					0, $monto, '$observaciones', '$sucursal', 0, 0
					";
			$SQl_comp = "INSERT INTO operaciones_mvtos($smf) VALUES ($smv)";
			if($monto!=0){
					$exec = my_query($SQl_comp);
					if ( $exec["stat"] == false ){
						$sucess	= false;
					} else {
						$sucess	= true;
					}
			}
	return $sucess;
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
	my_query($sql);
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
				AND tipo_operacion=$mvto
				";
	} else {
	$SQL_DM = "DELETE FROM operaciones_mvtos
				WHERE docto_afectado=$solicitud
				AND socio_afectado=$socio
				AND tipo_operacion=$mvto";
	}
	my_query($SQL_DM);
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
				AND periodo_socio=$parcialidad
				";
	} else {
	$SQL_DM = "DELETE FROM operaciones_mvtos
				WHERE docto_afectado=$solicitud
				AND socio_afectado=$socio
				AND tipo_operacion=$mvto
				AND periodo_socio=$parcialidad";

	}
	my_query($SQL_DM);
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
		  `seguimiento_notificaciones`.`fecha_vencimiento`<=CURDATE()
		  AND `seguimiento_notificaciones`.`estatus_notificacion` = \"pendiente\"
		  AND sucursal='$sucursal' ";
  $x = my_query($sql);
    if ($x["stat"] == false){
      $msg	.= $x[SYS_MSG] . "\n";
    } else {
      $msg	.= $x["info"] . "\n";
    }
}
function vencer_llamadas(){
  $msg		= "=================VENCIMIENTO_DE_LLAMADAS======================\n";
  $sucursal		= getSucursal();
  $sql = "UPDATE `seguimiento_llamadas` SET `seguimiento_llamadas`.`estatus_llamada`=\"vencido\"
		WHERE
		  `seguimiento_llamadas`.`estatus_llamada` = \"pendiente\"
		  AND DATE_ADD(`seguimiento_llamadas`.`fecha_llamada`, INTERVAL 2  DAY) < CURDATE()
		  AND sucursal='$sucursal'";
  $x = my_query($sql);
    if ($x["stat"] == false){
      $msg	.= $x[SYS_MSG] . "\n";
    } else {
      $msg	.= date("Y-m-d H:i:s") . "\tVenciendo las Llamadas de Seguimiento Pendientes\r\n";
    }
    return $msg;
}
function vencer_compromisos(){
  $msg		= "=================VENCIMIENTO_DE_COMPROMISOS======================\n";
  $sucursal		= getSucursal();
  $sql = "UPDATE `seguimiento_compromisos`
		  SET
		  `seguimiento_compromisos`.`estatus_compromiso` =\"no_cumplido\"
		WHERE
		  `seguimiento_compromisos`.`estatus_compromiso` = \"pendiente\"
		  AND DATE_ADD(`seguimiento_compromisos`.`fecha_vencimiento`, INTERVAL 1  DAY) < CURDATE()
		  AND sucursal='$sucursal'";
  $x = my_query($sql);
    if ($x["stat"] == false){
      $msg	.= $x[SYS_MSG] . "\n";
    } else {
      $msg	.= date("Y-m-d H:i:s") . "\tVenciendo los Compromisos de Seguimiento Pendientes\r\n";
    }
    return $msg;
}

function clearCacheSessions(){
  $sql = "DELETE FROM usuarios_web_connected";
  my_query($sql);
}
/**
 * Congela los Saldos a peticion.
 * esta funcion solo es usada en los FINES DE MES
 * @return string Mensajes del proceso
 */
function CongelarSaldos($recibo = false, $fecha = false){
	if ( $fecha == false ) {
		$fecha	= fechasys();
	}
	$msg	= "";

	$q_con_cred = "UPDATE creditos_solicitud SET saldo_conciliado=saldo_actual, fecha_conciliada=fecha_ultimo_mvto";
	$x = my_query($q_con_cred);

		if ($x["stat"] == false){
			$msg	.= $x[SYS_MSG] . "\n";
		} else {
			$msg	.= date("Y-m-d H:i:s") . "\tSE HAN ACTUALIZADO LOS SALDOS CONCILIADOS DE CREDITOS \r\n";
		}
	$q_con_cap = "UPDATE captacion_cuentas SET saldo_conciliado=saldo_cuenta, fecha_conciliada=fecha_afectacion ";
	$x = my_query($q_con_cap);

		if ($x["stat"] == false){
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
?>
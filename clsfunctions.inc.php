<?php
/**
 * @author Balam Gonzalez Luis H
 * @since 2007-01-01
 * @version 2008-04-09 1.01.2
 *  Cambios en la version
 * 		2008-04-09 - Agregar alguna correciones en consultas
 * 					- Correccion en la funcion returnundat
 *					- core common support
 *		2008-07-22	- Mejoras en seguridad.- Esta Libreria esta fatal. en la proxima version será remozada
 *		2008-08-08	- Mejora en la funcion devolvergl, a partir del credito
 */
//=====================================================================================================
	include_once("core/go.login.inc.php");
	include_once("core/core.error.inc.php");
	include_once("core/core.html.inc.php");
	include_once("core/core.init.inc.php");	
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
	@require_once("./libs/jsrsServer.inc.php");
	include_once("./core/core.deprecated.inc.php");
	include_once("./core/entidad.datos.php");
	include_once("./core/core.fechas.inc.php");
	include_once("./core/core.config.inc.php");
	include_once("./core/core.common.inc.php");
	include_once("./core/core.creditos.inc.php");

jsrsDispatch("nombre, dsolicitud, damesocio, domprincipal, remlinea, listarcreditos, listargarantias, listarparcialidades, mostrargrupo, damecredito, sumarmvto, resgarantialiq, interesanticipado, mostrarcuenta, devolver_ia, devolvergl, listarcuentas, dametasa, gposolcred, esautomatico, esdegpo, prioricred, oregion, prioricta, sumarparc, raindat, cuotasocial, solicitudexistente, misolicitud, dametasa2, dat_convenio");

/** ---------------------------- Devuelve el Nombre del Socio---------------------------- */
function nombre($idsoc){
	$nombre	= "";
	if (isset($idsoc) ){
		$idsoc = trim($idsoc);
		settype($idsoc, "integer");
		$xSoc	= new cSocio($idsoc); $xSoc->init(); $nombre	= $xSoc->getNombreCompleto();
	}
	return $nombre;
}
/** ------------------------------ Devuelve la Descripcion del Credito----------------------- */
function dsolicitud($idsol){
	$descripcion	= "NO_EXISTEN_DATOS";
	if ( isset($idsol) ){
		$cCred 		= new cCredito($idsol);
		$cCred->initCredito();
		$descripcion    = $cCred->getShortDescription();
	}
	return $descripcion;
}
/** -------------------- SQl Instruccion for query.- Funcion General ----------------------------------------- */
function mydat($msql, $mcampo){
	$jinger	= mifila($msql, $mcampo);
	return $jinger;

}
/** Returna un tipo de  */
function mytypev($ttab, $ffilt){

	$idtab = "id" . $ttab;
	$mtsql = "SELECT * FROM $ttab WHERE $idtab=$ffilt LIMIT 0,1";
	$rsv = mysql_query($mtsql, cnnGeneral());

		while($irow =  mysql_fetch_array($rsv)){
			 $ixval = $irow[1];
		}

	return $ixval;
	@mysql_free_result($rsv);
}
/** Devuelve cualquier datos de un Socio segun Indice dado */
function damesocio($jambo) {
	$idsocio 	= trim(substr($jambo, 0, (strlen($jambo) -3)));
	$indexdate 	= trim(substr (strrchr ($jambo, " "), 2));
	$datossql 	= "SELECT * FROM socios_general WHERE codigo=$idsocio";
	$rsdame = mysql_query($datossql, cnnGeneral());
		while($rwd = mysql_fetch_array($rsdame)) {
			$tomalo = $rwd[$indexdate];
		}
		return $tomalo;
	@mysql_free_result($rsdame);
}
/** Devuelve cualquier dato de la tabla segun indice e id dado */
function damecredito($jimbo) {
	//SOLICITUD,IDCAMPO
	$kimbo = explode(" ", $jimbo);
	if ( isset($kimbo[0]) AND trim($kimbo[0]) != "" ){
		$sqlbind = "SELECT * FROM creditos_solicitud
					WHERE numero_solicitud=$kimbo[0] LIMIT 0,1";
		$tirar = 0;
		$rsbind = mysql_query($sqlbind, cnnGeneral());
		if (!$rsbind) {
			saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sqlbind);
		} else {
			while ($rw = mysql_fetch_array($rsbind)) {
				$tirar = $rw[$kimbo[1]];
			}
		}
		return $tirar;
		@mysql_free_result($rsbind);
	} else {
		//saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :SQL Fallido o sin algun dato");
		return 0;
	}
}
/** Suma el Total de Garantias para verificar si cubre el total del credito	*/
function sumagarantias($idsolicitud) {
	$sqlsgar = "SELECT SUM(monto_valuado) AS 'sumagarantizada' FROM creditos_garantias WHERE solicitud_garantia=$idsolicitud";
	$sumagarantizada = mydat($sqlsgar, "sumagaratizada");
	return $sumagarantizada;
}
/** retorna el domicilio principal del socio	*/
function domprincipal($elsocio) {
	$sqldom = "SELECT * FROM socios_vivienda WHERE socio_numero=$elsocio AND principal='1'";
	$rsdom = mysql_query($sqldom, cnnGeneral());

		while($rwi = mysql_fetch_array($rsdom)) {
			$dtomalo = "Calle $rwi[3], Num. $rwi[4], Num. Int. $rwi[5], Colonia $rwi[6]; $rwi[8], $rwi[9], C. P. $rwi[17]";
		}

		if (!$rsdom) {
			$dtomalo = "NO EXISTE DATOS DE VIVIENDA DE LA PERSONA";
		}
		return $dtomalo;
	@mysql_free_result($rsdom);
}
// obtiene la suma de los creditos
function remlinea($socio) {
		// obtiene la suma de las lineas de Credito
		$sqlsc = "SELECT SUM(saldo_actual) As 'sdoscred' FROM creditos_solicitud WHERE numero_socio=$socio AND saldo_actual>0.99";
		$sqlsl = "SELECT SUM(monto_linea) as 'sumalinea' FROM creditos_lineas WHERE estado=1 AND numero_socio=$socio";
		$remanente = mydat($sqlsl, "sumalinea") - mydat($sqlsc, "sdoscred");

		return $remanente;

}
// enlista las los creditos con saldo mayor a 0
function listarcreditos($idsocio) {

}
/** @DEPRECATED lista las garantias	*/
function listargarantias($idsolicitud) {

}
// Lista las Parcialidades segun solicitud de credito dada
function listarparcialidades($idsolicitud) {

}
// muestra un Grupo segun Id Dado, la cadena es id de grupo + id de field a mostrar
function mostrargrupo($isdate= "99 1") {
	$dgr 			= explode(" ", $isdate);
	if ( !isset($dgr[0]) ){
		$dgr[0]		= DEFAULT_GRUPO;
	}
	$sqlgpo 		= "SELECT * FROM socios_grupossolidarios WHERE idsocios_grupossolidarios=$dgr[0] LIMIT 0,1";
	$rsgpo 			= mysql_query($sqlgpo, cnnGeneral());
	$elgrupo 		= "NO EXISTEN DATOS";
		while($rwe	= mysql_fetch_array($rsgpo)) {
			$elgrupo= $rwe[$dgr[1]];
		}
	return $elgrupo;
	@mysql_free_result($rsgpo);
}
// Suma el Mvto, segun Iddocto  + Id Mvto
function sumarmvto($sJamba) {
	$rhino = explode(" ", $sJamba);
	$sGore = "SELECT SUM(afectacion_real) AS 'sumamvto' FROM operaciones_mvtos WHERE docto_afectado=$rhino[0] AND tipo_mvto=$rhino[1] AND docto_neutralizador=1";
		$cred = mydat($sGore, "sumamvto");
		return $cred;
}
// Retorna la Garantia y el residuo de la garantia, segun Socio + Solicitud
function resgarantialiq($sGinger) {
/*/ ----------------------------------------------------------------------------------------
			Este Dato Debe actualizarse a la par de la libreria entidad.datos.php
// ---------------------------------------------------------------------------------------- */
	$aGin 			= explode(" ", $sGinger);
	$xTip = new cTipos();

	$idsolicitud 	= $xTip->cInt($aGin[1]);
	$idsocio 		= $xTip->cInt($aGin[0]);
	$a_pagar		= 0;
	if ( isset($idsocio) and isset($idsolicitud) ){
		$xCred			= new cCredito($idsolicitud, $idsocio);
		$montoPag		= $xCred->getGarantiaLiquidaPagada();
		$montoGar		= $xCred->getGarantiaLiquida();
		$a_pagar		= ($montoGar -  $montoPag);
	}
return $a_pagar;
}
//funcion privada que suma un movimiento segun socio o Solicitud + tipo movimiento + estatus
// tipo,socio_o_solicitud,tipo_movimiento,estatus
function priv_sumarmvto($stringOrden) {
	$orden = explode(",",  $stringOrden);
	$mirame = 0;
	$elcampo = "socio_afectado";
	if ($orden[0] == "xSol") {
		$elcampo = "docto_afectado";
	}
	$sGore = "SELECT SUM(afectacion_real) AS 'sumamvto' FROM operaciones_mvtos ";
	$sGore .= "WHERE $elcampo=$orden[1] AND tipo_operacion=$orden[2] AND estatus_mvto=$orden[3]";
		$mirame = mydat($sGore, "sumamvto");

		return $mirame;
}
function priv_damecredito($jimbo) {
	$kimbo = explode(" ", $jimbo);
	$sqlbind = "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$kimbo[0] LIMIT 0,1";
	$id = $kimbo[1];
	$tirar = 0;
	$rsbind = mysql_query($sqlbind, cnnGeneral());
	while ($rw0 = mysql_fetch_array($rsbind)) {
		$tirar = $rw0[$id];
	}
	return $tirar;
	@mysql_free_result($rsbind);
}
/* ------------------------------------ */
function interesanticipado($idsolicitud) {
/*/ ----------------------------------------------------------------------------------------
			Este Dato Debe actualizarse a la par de la libreria entidad.datos.php
// ---------------------------------------------------------------------------------------- */
			$tasa_interes_ant = 0.5;
// ----------------------------------------------------------------------------------------
$idsolicitud = trim($idsolicitud);
	$sql 				= "SELECT * FROM creditos_solicitud WHERE numero_solicitud=$idsolicitud";
	$mysolicitud 	= obten_filas($sql);
	$socio 			= $mysolicitud["numero_socio"];					// Numero de Socio
	$diasaut 		= $mysolicitud["numero_solicitud"];				// Dias Autorizados
	$intdiario 		= $mysolicitud["interes_diario"];				// Interes Diario
	$intneto 		= 0;
	$inttotal 		= 0;

	/**
	 * Consulta todos los creditos que aplican Interes Anticipado
	 */

	$sql_creditos_con_ica = "SELECT interes_diario, dias_autorizados, sdo_int_ant FROM creditos_solicitud WHERE numero_socio=$socio AND tipo_autorizacion=2 AND saldo_actual>=0.99 LIMIT 0,100";
		$rs_creditos_con_ica = mysql_query($sql_creditos_con_ica, cnnGeneral());
		while ($rowica = mysql_fetch_array($rs_creditos_con_ica)) {
			$inttotal = $inttotal + ($rowica[0] * $rowica[1] * $tasa_interes_ant);
		}

	//$inttotal = (($diasaut * $intdiario)  * $tasa_interes_ant)  - 0.99;
	/**
	 * Obtiene el Interes Correctamente @var int $intpagado
	 */
	$intpagado = priv_sumarmvto("xSoc,$socio,351,30");

	if (!$intpagado) {
		$intpagado = 0;
	}
	if ($intpagado < $inttotal) {	// Pagamos el remanente
		$intneto = ($inttotal + 0.99) - $intpagado;
	}
	return $intneto;
}

function mostrarcuenta($str_pedido){
	$datos 		= explode("|", $str_pedido);
	$idcuenta 	= $datos[0];
	$xC			= new cCuentaDeCaptacion($idcuenta);
	return $xC->getDescription();
}
function devolver_ia($ordename) {
// suma los creditos segun Socio, Solicitud
// ****************************************** FUNCION INVALIDADA ***************************************************
	$ordenado = explode(",", $ordename);
	$sqlr = "SELECT SUM(saldo_actual) AS 'sumas' FROM creditos_solicitud WHERE numero_socio=$ordenado[0] AND saldo_actual>0.99";
	$creds = mydat($sqlr, "sumas");
	$intdev = 0;
	// Obtiene la Suma de Intereses Pagados x Ant segun socio
	$intpagado = priv_sumarmvto("xSoc,$ordenado[0],351,30");
	// Obtiene la Suma de Intereses Pagados x Ant segun Solicitud
	$intamort = priv_sumarmvto("xSol,$ordenado[1],451,40");
	if ($creds <= 0.99) {
		$intdev = $intpagado - $intamort;
		if ($intdev <= 0) {
			$intdev = 0;
		}
	}
	return 0;
}
function devolvergl($numero_de_credito) {
	$a_devolver	= 0;
	if ( isset($numero_de_credito) ){
		$xCred			= new cCredito($numero_de_credito);
		$montoPag		= $xCred->getGarantiaLiquidaPagada();
		$montoGar		= $xCred->getGarantiaLiquida(true);
		$a_devolver		= ($montoPag - $montoGar);
	}

	return $a_devolver;
}
function listarcuentas($str_pedido) {

}
function dametasa($sOrden) {
$ordenes = explode(",", $sOrden);
	$sqltasa = "SELECT tasa_efectiva FROM captacion_tasas
	WHERE
	modalidad_cuenta=$ordenes[1]
	AND $ordenes[0] >= monto_mayor_a
	AND $ordenes[0] < monto_menor_a";
	return mydat($sqltasa, "tasa_efectiva");
}
function returnundat($strorden) {
	//tabla|campostr|where|
	// Orden de Function tabla,indice,filtro:
	$data 	= 0;
	$ixx 	= explode(",", $strorden);
	$sqlxx 	= "SELECT * FROM $ixx[0] WHERE $ixx[2] LIMIT 0,1";
	$rsxx 	= mysql_query($sqlxx, cnnGeneral());

	 while($rwx = mysql_fetch_array($rsxx)) {
	 	$data = $rwx[1];
	 }
	 return $data;
	@mysql_free_result($rsxx);
}
function gposolcred($strGreen) {
	//Cuantos dias se le espera a una socia.- para eliminar un credito 30 DIAS
	$dias_espera 			= 30;
	$antiguedad_planeacion 	= restardias(date("Y-m-d"), $dias_espera);
	$elcred					= 0;
	$SQL 					= "SELECT grupo_solidario FROM socios_general WHERE codigo=$strGreen";
	$gpoasoc 				= mydat($SQL, "grupo_solidario");
	$sqls 					= "SELECT SUM(afectacion_real) AS 'total' FROM operaciones_mvtos WHERE grupo_asociado=$gpoasoc 
								AND tipo_operacion=112 AND estatus_mvto=40 
								AND fecha_afectacion > '$antiguedad_planeacion'";
	if ($gpoasoc != DEFAULT_GRUPO ) {
		$elcred				= mydat($sqls, "total");
	}

	return $elcred;
}
/** Devuelve el Monto Maximo Otorgable de Forma Automatizada.- solo Aportaciones Voluntarias */
function esautomatico($string_orden) {
	$elfondo = 0;

	$datos = explode("|", $string_orden);
	if ($datos[1]==10){
					$sql_aports = "SELECT operaciones_tipos.constituye_fondo_automatico, SUM(operaciones_mvtos.afectacion_real) AS ";
					$sql_aports .= " 'total' FROM operaciones_tipos, operaciones_mvtos WHERE operaciones_mvtos.tipo_operacion=operaciones_tipos.idoperaciones_tipos ";
					$sql_aports .= "AND operaciones_tipos.constituye_fondo_automatico=1 AND operaciones_mvtos.socio_afectado=$datos[0] ";
					$sql_aports .= "GROUP BY operaciones_tipos.constituye_fondo_automatico LIMIT 0,100";
					$totalaport = mydat($sql_aports, 'total');
					$sql110 = "SELECT SUM(saldo_actual) AS 'sdoneto' FROM creditos_solicitud WHERE numero_socio=$datos[0] AND saldo_actual>=1";
    				$loscreds = mydat($sql110, "sdoneto");
					    // Resta los creditos otorgados de Aportacion
    					$elfondo = $totalaport - $loscreds;
    					if ($elfondo<0) {
    						$elfondo=0;
    					}
    					//$elfondo = $totalaport;
	} elseif ($datos[1]==14) {
					/**
					 * 50% maximo en Convenios de Ahorro
					 * @var $sql_aports
					 */
		$sql_aports =" SELECT SUM(saldo_cuenta) as 'sumas' FROM captacion_cuentas WHERE  numero_socio=$datos[0]";
		$totalaport = mydat($sql_aports, 'sumas') * 0.50;
					$sql110 = "SELECT SUM(saldo_actual) AS 'sdoneto' FROM creditos_solicitud WHERE numero_socio=$datos[0] AND saldo_actual>=1 AND tipo_convenio=14";
    				$loscreds = mydat($sql110, "sdoneto");
					    // Resta los creditos otorgados de Aportacion
    					$elfondo = $totalaport - $loscreds;
    					if ($elfondo<0) {
    						$elfondo=0;
    					}
	}
   return $elfondo;
}
function esdegpo($idsocio) {
	$sqlgpo = "SELECT COUNT(representante_numerosocio) AS 'contado'
					FROM socios_grupossolidarios
					WHERE representante_numerosocio=$idsocio";
	$cuentame = mydat($sqlgpo, 'contado');
	return $cuentame;
}

function prioricred( $idsocio ) {
	$miro = 0;
	if ( isset($idsocio) ){
	$sqllc  = "SELECT numero_solicitud  FROM creditos_solicitud
					WHERE
					numero_socio=$idsocio
					/* AND monto_autorizado>0 */
					ORDER BY saldo_actual DESC,
					fecha_solicitud DESC,
					fecha_vencimiento DESC
					 LIMIT 0,1 ";

		$miro = mifila($sqllc, "numero_solicitud");
		//$cSoc	= new cSocio($idsocio);
		//$cSoc->init();
		//$miro	= $cSoc->getListadoDeCreditos("select", "idsolicitud", "idsolicitud");
	}
		return $miro;
}
function oregion($idcl) {
	$myregi = 0;
	$sqlregi = "SELECT region FROM socios_cajalocal WHERE idsocios_cajalocal=$idcl LIMIT 0,1";
	$myregi = mydat($sqlregi, "region");
	return $myregi;
}
function prioricta($idchar) {
	$xT			= new cTipos(0);
	$datos		= explode("|", $idchar);
	$idsocio 	= $xT->cInt($datos[0]);
	$tipo 		= $xT->cInt($datos[1]);
	if ( $idsocio != 0 AND $tipo != 0  ){
		$cSoc	= new cSocio($idsocio);
		
		return $cSoc->getCuentaDeCaptacionPrimaria($tipo);
	}
}
function sumarparc($cadena) {
		$micad = explode("|", $cadena);
		$iddoc = $micad[0];
		$idparc = $micad[1];
		$sqlmvtos_s = "SELECT SUM(operaciones_mvtos.afectacion_real * operaciones_tipos.afectacion_en_recibo) AS 'monto' FROM operaciones_mvtos NATURAL JOIN operaciones_tipos ";
		$sqlmvtos_s .= " WHERE  docto_afectado=$iddoc AND operaciones_tipos.afectacion_en_recibo!=0 AND operaciones_mvtos.docto_neutralizador=1 AND operaciones_mvtos.periodo_socio=$idparc  GROUP BY operaciones_mvtos.docto_afectado";

		$lasum = mydat($sqlmvtos_s, "monto");
		return $lasum;
}
function raindat($strorden) {
	// Orden de Function tabla,indice,filtro:
	$data = "000";
	$ixx = explode("//", $strorden);
	$sqlxx = "SELECT $ixx[1] FROM $ixx[0] WHERE $ixx[2] LIMIT 0,1";
	$data = mydat($sqlxx, "$ixx[1]");
	return $data;

}
function cuotasocial($id) {
	$xpagar = 0;
	$sqltre = "SELECT socios_general.codigo, (socios_tipoingreso.parte_social + socios_tipoingreso.parte_permanente) AS 'cuota_social'FROM socios_general,";
	$sqltre .= " socios_tipoingreso WHERE socios_tipoingreso.idsocios_tipoingreso=socios_general.tipoingreso ";
	$sqltre .= " AND socios_general.codigo=$id";
	$micuota = mydat($sqltre, "cuota_social");

	$sql = "SELECT SUM(afectacion_real) AS 'afect' FROM operaciones_mvtos WHERE tipo_operacion=701 AND socio_afectado=$id";
	$cuota_pagada = mydat($sql, "afect");
	$xpagar = $micuota - $cuota_pagada;
	if ($xpagar<=0) {
		$xpagar = 0;
	}
	return $xpagar;
}
function solicitudexistente($id) {
	$ifsol = "SELECT numero_solicitud FROM creditos_solicitud WHERE numero_solicitud=$id LIMIT 0,1";
	$is = mydat($ifsol, "numero_solicitud");
	if (!$is){
		$is = 0;
	}
	return $is;

}
function misolicitud($idsocio) {
	$sql = "SELECT COUNT(numero_solicitud) AS 'id' FROM creditos_solicitud WHERE numero_socio=$idsocio";
	$ids = mydat($sql, "id");
	if ($ids == 0) {
		$ids = $idsocio . "01";
	} else {
		$ids = $idsocio . "$ids";
	}

	return $ids;
}
function dametasa2($sOrden) {
$ordenes = explode(",", $sOrden);
// Dias es explode 2
	$sqltasa = "SELECT tasa_efectiva FROM captacion_tasas
					WHERE modalidad_cuenta=$ordenes[1]
					AND $ordenes[0] >= monto_mayor_a AND $ordenes[0] < monto_menor_a
					AND $ordenes[2]>=dias_mayor_a AND $ordenes[2]< dias_menor_a";

	 $iko = mydat($sqltasa, "tasa_efectiva");
	 return $iko;
}
function dat_convenio($str_pedimiento ="99|1") {
	$info = "NO HAY INFORMACION";
	$array_str = explode("|", $str_pedimiento);
	$idconvenio = $array_str[0];
	$id = $array_str[1];
	$sql_convenio = "SELECT * FROM creditos_tipoconvenio WHERE idcreditos_tipoconvenio=$idconvenio";
	$rsconv = mysql_query($sql_convenio, cnnGeneral());
		while ($row = mysql_fetch_array($rsconv )){

			$info = $row[$id];
		}
	@mysql_free_result($rsconv);
	return $info;
}
?>

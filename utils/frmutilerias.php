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
$xHP		= new cHPage("TR.Utilerias del Sistema");


$oficial 	= elusuario($iduser);

ini_set("max_execution_time", 2400);
//ini_set("display_errors", "on");
$chri 		= "@";
$jsExtra	= "";

$rmtCmd		= (isset($_GET["r"])) ? $_GET["r"] : 0;

function jsinfo_util($id, $frm){
	$sqlInfo = "SELECT * FROM general_utilerias WHERE idgeneral_utilerias =$id";
	$rw = obten_filas($sqlInfo);
	$tab = new TinyAjaxBehavior();

	//$tab -> add(TabSetValue::getBehavior("i_3193c", $rw["nombre_utilerias"]));
	$tab -> add(TabSetValue::getBehavior("imsg", $rw["descripcion_utileria"]));
	$tab -> add(TabSetValue::getBehavior("iID1", $rw["describe_param_1"]));
	$tab -> add(TabSetValue::getBehavior("iID2", $rw["describe_param_2"]));
	$tab -> add(TabSetValue::getBehavior("iID3", $rw["describe_param_3"]));
	$tab -> add(TabSetValue::getBehavior("id_de",$rw["describe_init"]));
	$tab -> add(TabSetValue::getBehavior("id_a", $rw["describe_end"]));

	return $tab -> getString();
}


$jxc = new TinyAjax();
$jxc ->exportFunction('jsinfo_util', array('idClaves', "mycast"));
$jxc ->process();

$xHP->init();

?>
	<fieldset>

		<legend>Utilerias de la Base de Datos</legend>


		<form name="frmUtils" method="post" action="frmutilerias.php">

			<table>

				<tr>
					<td>Codigo del Proceso</td>
					<td colspan="4"><?php
					if ( MODO_DEBUG == true ){
						echo "<input type='text' name='c-clave' value='$rmtCmd' size='4' id='id-clave' onchange='setValExch()' onkeyup='charEventUp(event);' /> ";
						$jsExtra	.= "document.getElementById('id-clave').value = document.getElementById('idClaves').value; ";
					}
					$sqlcs = "SELECT idgeneral_utilerias, CONCAT('[', idgeneral_utilerias, '] - ', nombre_utilerias) AS 'mUtils' FROM general_utilerias WHERE isdisabled='false' ORDER BY nombre_utilerias, idgeneral_utilerias ";
					
					$ctr = new cSelect("sClaves", "idClaves", $sqlcs);
					$ctr->setEsSql();
					$ctr->addEvent("onchange", "myInfoUtil");
					$ctr->show(false);
					?>
					</td>
				</tr>
				<tr>
					<td>Primer Codigo</td>
					<td><input type='text' name='cID1' id='iID1' value='0' size="40"
						onkeyup='charEventUp(event);' /></td>
				</tr>
				<tr>
					<td>Segundo Codigo</td>
					<td><input type='text' name='cID2' id='iID2' value='0' size="40"
						onkeyup='charEventUp(event);' /></td>
				</tr>
				<tr>
					<td>Tercer Codigo</td>
					<td><input type='text' name='cID3' id='iID3' value='0' size="40"
						onkeyup='charEventUp(event);' /></td>
				</tr>
				<tr>
					<td>Cuarto Codigo</td>
					<td><input type='text' name='de' id='id_de' value='0' size="40" />
					</td>
				</tr>
				<tr>
					<td>Quinto Codigo</td>
					<td><input type='text' name='a' id='id_a' value='1' size="40" /></td>
				</tr>
				<tr>
					<td colspan="4"><textarea id="imsg" cols="120" rows="2" readonly></textarea>
					</td>
				</tr>
				<tr>
					<th colspan='4'><input type="button" value="Ejecutar Utileria"
						onclick="frmUtils.submit();"  /></th>
				</tr>
			</table>

		</form>
		
		<?php

		$command 		= ( isset($_POST["sClaves"]) ) ? $_POST["sClaves"] : false;
		$command		= ( isset($_POST["c-clave"]) ) ? $_POST["c-clave"] : $command;

		$id 			= parametro("cID1", false, MQL_RAW);
		$id2 			= parametro("cID2", false, MQL_RAW);
		$id3 			= parametro("cID3", false, MQL_RAW);

		$de 			= 0;//parametro("de"];
		$a 				= 0;//$_POST["a"];

		$csucursal		= getSucursal();
		$sucursal 		= getSucursal();
		$eacp			= EACP_CLAVE;
		$fechaop		= fechasys();
		$msg			= "============================  LOG DE EVENTOS DE UTILERIAS =========================\r\n";
		$msg			.= "============================  GENERADO POR $oficial / COMANDO $command \r\n";
		$msg			.= "============================  FECHA " . date("Y-m-d H:s:i") . " \r\n";

		if ( $command != false ){
			$prefijo    = substr(getRndKey(), 0, 4);

			$aliasFils	= "$sucursal-log-de-utileria-num-$command-$fechaop-$prefijo";
			//Elimina el Archivo
			$xFLog		= new cFileLog($aliasFils, true);
			saveError(10,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "EL Usuario $oficial Utilizo la Utileria $command, Params $id|$id2|$id3");
		}

		switch ($command) {

			case 200:
				//Actualiza las Tasas de las Cuentas de Captacion
				$sql 	= "SELECT * FROM captacion_tasas";
				$ql		= new MQL();

				$rs 	= $ql->getDataRecord($sql);
				$xTasa	= new cCaptacion_tasas();
				foreach ($rs as $row){
					$xTasa->setData($row);
					$subproducto		= $xTasa->subproducto()->v();
					$tipocuenta			= $xTasa->modalidad_cuenta()->v();
					$tasa				= $xTasa->tasa_efectiva()->v();
					$maximo				= $xTasa->monto_menor_a()->v();
					$minimo				= $xTasa->monto_mayor_a()->v();
					if($tipocuenta==CAPTACION_TIPO_PLAZO){
						
						$sql_cap = "UPDATE captacion_cuentas  
						SET tasa_otorgada=$tasa 
						WHERE tipo_cuenta=$tipocuenta
						AND saldo_cuenta>=$minimo 
						AND saldo_cuenta<=$maximo 
						AND	dias_invertidos>" . $xTasa->dias_mayor_a()->v() . " 
						AND dias_invertidos<=" . $xTasa->dias_menor_a()->v() . "
						";
						$x	= my_query($sql_cap);
						if($x[SYS_ESTADO] == true){
							$msg		.= "OK\tSE HA ACTUALIZADO LAS CUENTAS TIPO $tipocuenta  DE CANTIDADES MAYORES A $minimo Y MENORES A $maximo\r\n";
						} else {
							$msg		.= $x[SYS_MSG];
						}
												
					} else {
						if(setNoMenorQueCero($subproducto) == 0){
							
						} else {
							$sql_cap = "UPDATE captacion_cuentas  
							SET tasa_otorgada=$tasa
							WHERE tipo_cuenta=$tipocuenta 
							AND  saldo_cuenta>=$minimo
							AND saldo_cuenta<=$maximo
							AND tipo_subproducto=$subproducto";
							$x	= my_query($sql_cap);
							if($x[SYS_ESTADO] == true){
								$msg		.= "OK\tSE HA ACTUALIZADO LAS CUENTAS TIPO $tipocuenta/$subproducto  DE CANTIDADES MAYORES A $minimo Y MENORES A $maximo\r\n";
							} else {
								$msg		.= $x[SYS_MSG];
							}							
						}
						
						setLog($sql_cap);
					
					}
				}	
				
				break;

				//========================================================================================================================
			case 203:
				$msg	.= "============== DEPURANDO LA CUENTA GLOBAL CORRIENTE ========\r\n";
				$cuenta	= ( $id == "NO_APLICA" ) ? CTA_GLOBAL_CORRIENTE : $id;
				$sqlDM	= "DELETE FROM operaciones_mvtos WHERE docto_afectado=$cuenta";
				$xDM	= my_query($sqlDM);
				$msg	.= $xDM[SYS_MSG];

				$sqlDS	= "DELETE FROM `captacion_sdpm_historico` WHERE cuenta=$cuenta";
				$xDS	= my_query($sqlDS);
				$msg	.= $xDS[SYS_MSG];
				//obtiene la cuenta con mas antiguedad
				$sqlCA	= "SELECT MIN(fecha_apertura) AS 'fecha' FROM captacion_cuentas WHERE numero_cuenta=$cuenta";
				$DCA	= obten_filas($sqlCA);

				$fecha	= $DCA["fecha"];

				$sqlCA	= "DELETE FROM captacion_cuentas WHERE fecha_apertura!='$fecha' AND numero_cuenta=$cuenta";
				$xDA	= my_query($sqlCA);
				$msg	.= $xDA[SYS_MSG];
				$sqlUC	= "UPDATE captacion_cuentas SET saldo_cuenta=0, saldo_conciliado=0, ultimo_sdpm=0 WHERE numero_cuenta=$cuenta";
				my_query($sqlUC);

				break;
				//Limpiar la Base de Datos
			case 666:
				$x 		= new cSAFEData();	$msg	.= $x->setPurgueDB();

				echo "<p class='kill'>AS BORRADO TODO REGISTRO EXISTENTE</p>";
				break;
			case 667:
					$x 		= new cSAFEData();
					$msg	.= $x->setCrearEjemplos();
				echo "<p class='kill'>GENERAR EJEMPLOS</p>";
					break;
			case 668:
					//$x 		= new cLang(); $msg	.= $x->toDatabase();
				break;
			case 669:
					$Forzar	= (strtoupper($id) == "SI") ? true : false;
					$xPatch	= new cSystemPatch(); $msg	.= $xPatch->patch($Forzar);
					break;
			case 700:
				$solicitud 	= $id;
				$socio		= $id2;
				$xCred		= new cCredito($solicitud, $socio);
				$msg		= $xCred->setDelete();
				break;
				/**
				 * Actualiza los Movimientos de Captacion
				 */
			case 701:
				//Actualizar las cuentas GLOBALES
				$xCUtils		= new cUtileriasParaCreditos();
				$msg			.= $xCUtils->setCleanCreditosConAhorro();
				break;
			case 702:
				/**
				 * Actualiza los Movimientos Huerfanos
				 */
				$msg	.= "============== INICIANDO LA DEPURACION DE DEPOSITOS HUERFANOS ========\r\n";
				$sqlUS = "UPDATE operaciones_mvtos, creditos_solicitud
						SET operaciones_mvtos.docto_afectado = creditos_solicitud.contrato_corriente_relacionado
					WHERE
						operaciones_mvtos.docto_afectado = creditos_solicitud.numero_solicitud
					AND
						operaciones_mvtos.tipo_operacion = 220";
				$x = my_query($sqlUS, cnnGeneral() );
				if( $x["stat"] == false ){
					$msg	.= date("Y-m-d H:i:s") . "\tERROR : EL SISTEMA DEVOLVIO . " . $x["error"] . " \r\n";
				} else {
					$msg	.= date("Y-m-d H:i:s") . "\tACTUALIZACION SATISFACTORIA (" . $x["info"] . ")\r\n";
				}
				break;
			case 703:
				$xUCapt	= new cUtileriasParaCaptacion();
				$msg	.= $xUCapt->setCuadrarCuentasByMvtos($id);
				break;
			case 704:
				$msg	.= "============== ACTUALIZANDO DOCTO AFECTADO EN MVTOS HUERFANOS ========\r\n";

				$sqlUS 	= "UPDATE operaciones_mvtos
					SET docto_afectado = (SELECT docto_afectado FROM operaciones_recibos
										WHERE
										idoperaciones_recibos = operaciones_mvtos.recibo_afectado
										AND numero_socio = operaciones_mvtos.socio_afectado
										AND (tipo_docto = 2
											OR
											tipo_docto = 3)
										 LIMIT 0,1)
										WHERE tipo_operacion = 220
										AND ( (docto_afectado = " . CTA_GLOBAL_CORRIENTE . ")
											OR
											(docto_afectado = '')
											OR
											(docto_afectado = 0)
											OR
											(docto_afectado = 1)
											OR
 											((SELECT count(numero_cuenta) FROM captacion_cuentas
							WHERE numero_cuenta=operaciones_mvtos.docto_afectado) = 0)
											)
										";

				//$msg	.= date("Y-m-d H:i:s") . "\t\r\n";
				$x = my_query($sqlUS);
				if( $x["stat"] == false ){
					$msg	.= date("H:i:s") . "\tERROR : EL SISTEMA DEVOLVIO . " . $x["error"] . "\r\n";
				} else {
					$msg	.= date("H:i:s") . "\tACTUALIZACION SATISFACTORIA (" . $x["info"] . ")\r\n";
				}

				break;
			case 705:
				//Actualiza una Cuenta de Captacion segun Socio + Numero de Cuenta
				$socio 		= $id;
				$cuenta		= $id2;
				$NCuenta	= $id3;
				$xC			= new cCuentaDeCaptacion($cuenta, $socio);
				$msg		.= $xC->setCambiarCodigo($NCuenta);
				break;
			case 706:
				//Elimina una Cuenta de Captacion segun Socio + Numero de Cuenta
				$socio 		= 	$id;
				$cuenta		= 	$id2;
				$cCuenta	= 	new cCuentaALaVista($cuenta, $socio);
				$msg		.= 	$cCuenta->setDelete();
				break;

			case 901:
				/**
				 * Comvierte de ISAM a INNODB o viceversa
				 */
				$sucursal			= getSucursal();
				$querytbl 			= "SHOW TABLES";
				$rsquerytbl 		= mysql_query($querytbl, cnnGeneral() );
				$new_engine			= "MyISAM";
				while($rwquery = mysql_fetch_array($rsquerytbl)) {
					$sql_change 	= "ALTER TABLE $sucursal.$rwquery[0] ENGINE = $new_engine";
					my_query($sql_change);
					echo "<p class='aviso'>$sql_change </p> ";
				}
				break;
			case 803:

				$socio	= $id;
				$cSoc	= new cSocio($socio);
				$msg	.= $cSoc->setDeleteSocio();
				break;
			case 804:
				$sql_corrr_cast = "SELECT * FROM general_import";
				$rs_corr_cast = mysql_query($sql_corrr_cast, cnnGeneral());
				while ($rowc = mysql_fetch_array($rs_corr_cast)) {
					$sql_corr = "/** Actualiza creditos a Final de Plazo a Castigados */
			UPDATE creditos_solicitud SET estatus_actual=20
			WHERE saldo_actual>0 AND periocidad_de_pago=360
			AND fecha_vencimiento<='2003-04-30' AND tipo_autorizacion=2
			AND numero_socio=$rowc[0] AND saldo_actual=$rowc[1]";
					my_query($sql_corr);
				}
				break;
			case 805:
				$sql_corrr_cast = "SELECT * FROM general_import";
				$rs_corr_cast = mysql_query($sql_corrr_cast, cnnGeneral());
				while ($rowc = mysql_fetch_array($rs_corr_cast)) {
					$sql_corr = "/** Actualiza creditos Solidarios a Castigados */
			UPDATE creditos_solicitud SET estatus_actual=20
			WHERE saldo_actual>0 AND periocidad_de_pago!=360
			AND fecha_vencimiento<='2006-08-02'
			AND tipo_autorizacion=1
			AND numero_socio=$rowc[0]";
					//my_query($sql_corr);
				}
				break;

			case 810:

				/**
				 *  Actualiza el Expediente por Oficial de Credito segun general Import
				 */
				$sql = "SELECT * FROM general_import";
				$rs = mysql_query($sql, cnnGeneral());
				while ($rw = mysql_fetch_array($rs)){
					$sql_update = " UPDATE creditos_solicitud SET oficial_seguimiento=$rw[1] WHERE saldo_actual>0 AND numero_socio=$rw[0] AND estatus_actual=20 ";
					//my_query($sql_update);
				}
				@mysql_free_result($rs);

				break;
			case 813:
				$sql_ctw = "SELECT numero_socio, tipo_convenio,  estatus_actual AS 'estatus',
				MAX(fecha_ultimo_mvto) AS 'afectado_el', sum(saldo_actual) AS 'sdo'
				FROM creditos_solicitud
				WHERE saldo_actual!=0 AND  fecha_ministracion<CURDATE()
				AND estatus_actual!=50
				GROUP BY numero_socio, tipo_convenio, estatus_actual ";
				$rs = mysql_query($sql_ctw, cnnGeneral());
				while($rw = mysql_fetch_array($rs)){
					$cta_ctw = cwcuenta($rw[0], $rw[1], $rw[2]);
					$sql_compacw = "SELECT * FROM compacw_importados WHERE cuenta='$cta_ctw'";
					$dctw = obten_filas($sql_compacw);
					if (!$dctw[1]){
						$dctw[1] = $rw[4];
					}
					if ($rw[4]!= $dctw[1]){
						$sql_u_sdo = "UPDATE creditos_solicitud SET saldo_conciliado=$dctw[1], notas_auditoria='Actualiza de $rw[4] A $dctw[1]' WHERE numero_socio=$rw[0] AND tipo_convenio=$rw[1] AND estatus_actual=$rw[2]";
						//echo "$sql_u_sdo <br />";
						my_query($sql_u_sdo);
					}
				}

				break;
				//Actualiza un Credito, Sus Operaciones relacionadas segun el Socio y Numero
			case 814:
				// Actualiza el Credito
				$socio		= $id;
				$credito	= $id2;
				$NCredito	= $id3;
				//Guarda los Datos
				$cCred		= new cCredito($credito, $socio);
				$msg		.= $cCred->setChangeNumeroDeSolicitud($NCredito);
				break;
			case 815:
				$ATable = $id;
				$CTable = $id2;
				$inLiteral = strpos($ATable, ",");
				if ($inLiteral === false){
			  //ejecucion normal
			  //
			  setStructureTableByDemand($ATable, $CTable);
				} else {
			  //Ejecucion por array
			  //Limite de tablas
			  $ATable	= explode(",", $id);
			  $iLim    = sizeof($ATable) - 1;
			  for($i = 0; $i<=$iLim; $i++){
			  	setStructureTableByDemand(trim($ATable[$i]), $CTable);
			  }
				}
				break;
			case 816:
				/**
				 * Actualiza los Ahorros segun Mvtos Dados
				 */
				$sql_u_captacion = "SELECT * FROM general_import";
				$rs = mysql_query($sql_u_captacion, cnnGeneral());
				while($rw = mysql_fetch_array($rs)) {
					$sql_go_cap = "UPDATE captacion_cuentas SET inversion_fecha_vcto='$rw[2]', inversion_periodo=$rw[5],
					tasa_otorgada=$rw[3], saldo_cuenta=$rw[1], fecha_afectacion='$rw[4]' WHERE numero_cuenta=$rw[0]";
					//my_query($sql_go_cap);
				}
				break;
			case 817:
				/**
				 * Actualiza los Folios a Maximos
				 */
				$xD		= new cSAFEData();
				$xFol	= $xD->setFoliosAlMaximo();
				$msg	.= "Recibos al Folio: " . $xFol["recibos"] . "\r\n";
				$msg	.= "Operaciones al Folio: " . $xFol["operaciones"] . "\r\n";
				break;
			case 821;
			//Elimina Cuentas de Captacion Basura de la DB
			$sql_troll = "DELETE FROM captacion_cuentas  WHERE (SELECT nums_ops FROM num_operaciones_por_docto WHERE docto_afectado=captacion_cuentas.numero_cuenta)=0 AND saldo_cuenta<=0 ";
			my_query($sql_troll);
			break;
			case 822:
				$xUtilDB    =   new cSAFEData();
				$msg	    .=  $xUtilDB->setPurgueSucursal();
				break;
			case 827:
				//reconstruir el sdpm de cuentas en una fecha dada
				//Generar Interes
				$GenerarInteres		= strtoupper($id3);
				$GenerarInteres		= ( $GenerarInteres == "SI") ? true : false;
				$incluirSinSaldo	= ( strtoupper($de) == "SI") ? true : false;
				$NumeroCuenta		= ( strtoupper($a) != "NO_APLICA") ? $a : false;
				$xUC				= new cUtileriasParaCaptacion();
				$FInicial           = $id;
				$FFinal             = $id2;
				$msg				.= $xUC->setRegenerarSDPM($FInicial, $FFinal, $GenerarInteres, $incluirSinSaldo, $NumeroCuenta);
					
				break;
			case 828:
				//Actualiza a cero las Afectacion a SDPM
				$sql ="UPDATE operaciones_mvtos, operaciones_tipos SET operaciones_mvtos.afectacion_estadistica=0
					WHERE operaciones_mvtos.tipo_operacion=operaciones_tipos.idoperaciones_tipos
					AND operaciones_tipos.afectacion_en_sdpm=0";
				my_query($sql);
				break;
			case 829:
				//Asocia una Cuenta de Captacion a los Creditos con tasa de credito mayor a cero
				$sqlrev = "SELECT numero_socio, MAX(numero_cuenta) AS 'cuenta', COUNT(numero_cuenta) AS 'numctas' FROM captacion_cuentas
					WHERE tipo_cuenta=10
					GROUP BY numero_socio
					ORDER BY fecha_apertura, numero_socio";
				$rs = mysql_query($sqlrev, cnnGeneral());
				while($rw = mysql_fetch_array($rs)){
					$socio 		= $rw["numero_socio"];
					$cuenta 	= $rw["cuenta"];
					$date 		= fecha_corta();
					$nota 		= "INPUT UPDATE CAPTACION $date BY $iduser";
					$sqlupcred  = "UPDATE creditos_solicitud SET contrato_corriente_relacionado=$cuenta, notas_auditoria='$nota'
				WHERE numero_socio=$socio AND tasa_ahorro>0 AND (contrato_corriente_relacionado=" . CTA_GLOBAL_CORRIENTE . " OR contrato_corriente_relacionado=0)";
					my_query($sqlupcred);
				}
				@mysql_free_result($rs);

				break;
			case 830:
				//CREAR CUENTAS DE CAPTACION A CREDITOS HUERFANOS
				$sqlcred_no_capt = "SELECT * FROM creditos_solicitud WHERE tasa_ahorro>0 AND (contrato_corriente_relacionado=" . CTA_GLOBAL_CORRIENTE . " OR contrato_corriente_relacionado=0)";
				$rs = mysql_query($sqlcred_no_capt, cnnGeneral());
				$tipo = 10;

				while($rw = mysql_fetch_array($rs)){
					$idsocio 	= $rw["numero_socio"];
					$idgrupo	= $rw["grupo_asociado"];
					$fapert 	= $rw["fecha_ministracion"];
					$fafect 	= $rw["fecha_ultimo_mvto"];

					$cuenta 	= $tipo . $idsocio . "01";
					$sql_n_cta 	= "SELECT COUNT(numero_cuenta) AS 'cuentas' FROM captacion_cuentas WHERE numero_socio=$idsocio";
					$datos 		= obten_filas($sql_n_cta);
					if ($datos[0]) {
						$cuenta = $tipo . $idsocio . $datos[0];
					}
					$sql_i_capt = "INSERT INTO captacion_cuentas(numero_cuenta, numero_socio, numero_grupo, numero_solicitud, tipo_cuenta, fecha_apertura, fecha_afectacion, fecha_baja, estatus_cuenta, saldo_cuenta, eacp, idusuario, inversion_fecha_vcto, inversion_periodo, tasa_otorgada, dias_invertidos, observacion_cuenta, origen_cuenta, tipo_titulo, tipo_subproducto, nombre_mancomunado1, nombre_mancomunado2, minimo_mancomunantes, saldo_conciliado, fecha_conciliada, sucursal, ultimo_sdpm)
							    VALUES($cuenta, $idsocio, $idgrupo, 1, $tipo, '$fapert', '$fafect', '2099-12-31', 10, 0, '$eacp_codigo_cnbv', $iduser, '$fafect',
								0, 0, 0, 'CUENTA ABIERTA POR CREDITO', 2, 99, 99, '', '', 0, 0, '$fapert', '$sucursal', 0)";
					my_query($sql_i_capt);
				}
				@mysql_free_result($rs);
				break;

			case 831:
				$sql_cred_capt = "SELECT
						`operaciones_mvtos`.`idoperaciones_mvtos`,
						`creditos_solicitud`.`numero_solicitud`,
						`creditos_solicitud`.`contrato_corriente_relacionado`
					FROM
						`creditos_solicitud` `creditos_solicitud`
							INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
							ON `creditos_solicitud`.`numero_solicitud` = `operaciones_mvtos`.
							`docto_afectado`
					WHERE
						(`operaciones_mvtos`.`tipo_operacion`=220) ";
				$rs = mysql_query($sql_cred_capt, cnnGeneral());
				while($rw = mysql_fetch_array($rs)){
					$docto = $rw["numero_solicitud"];
					$cuenta = $rw["contrato_corriente_relacionado"];
					$mvto = $rw["idoperaciones_mvtos"];
					$sql_up_m = "UPDATE operaciones_mvtos SET docto_afectado=$cuenta
			WHERE idoperaciones_mvtos=$mvto";
					my_query($sql_up_m);

				}
				@mysql_free_result($rs);
				break;
			case 833:
				//IMPORTA TABLAS DESDE GENERAL IMPORT
				if ($id =="root"){
					$rs = mysql_query("SELECT * FROM general_import", cnnGeneral() );

					while($rw = mysql_fetch_array($rs)){
						$sql = "UPDATE creditos_solicitud SET saldo_actual=$rw[1], fecha_ultimo_mvto='$rw[2]',
					saldo_conciliado=$rw[1], fecha_conciliada=$rw[2] WHERE  numero_solicitud=$rw[0]";
						my_query($sql);
					}
					@mysql_free_result($rs);
				}
				break;
			case 834:

				//Crea Archivos a partir de un Read de Files en el Archivo principal
				$d = dir(PATH_HTDOCS);
				echo "Handle: ".$d->handle."<br>\n";
				echo "Path: ".$d->path."<br>\n";
				while($entry=$d->read()) {
					$OName = $entry;
					$siPoint = strpos($OName, ".");
					if (!$siPoint){

						$handle=opendir($d->path . "/" . $OName);

						while ($file = readdir($handle)) {
							$siPHP = strpos($file, ".php");
							$siBKP = strpos($file, "~");
							if(!$siBKP && $siPHP>0){
								//echo "$OName/$file <br />";
								$entPath = "$OName/$file";
								$sqlF = "SELECT count(idgeneral_menu) AS 'sihay' FROM general_menu
    									WHERE menu_file LIKE '%$entPath'";
								$inf = obten_filas($sqlF);

								if ($inf["sihay"]>=1){
									echo "<p class='aviso'> LA ENTRADA PARA $entPath YA EXISTE</p>";
								} else {
									//99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro
									$sqlIM = "INSERT INTO general_menu
    									(menu_parent, menu_title, menu_file,
    									menu_destination, menu_rules)
    									VALUES(
    									9999, 'PERMISO_PARA_$file', '$entPath', '_NULL',
    									'99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro')";
									my_query($sqlIM);
									echo "<p class='warn'>SE AGREGO LA ENTRADA PARA $entPath</p>";
								}
							}

						}
						closedir($handle);
					}

				}
				$d->close();



				break;
				//Reestruturar ICA

			case 835:

				$fecha_corte 			= $id;
				$xCred					= new cUtileriasParaCreditos();
				$msg .= $xCred->setReestructurarICA($fecha_corte);
				break;
			case 837:

				//Parchea las parcialidades para Obtener el IVA
				$useKey			= md5("NO_RETURN");
				$fecha_inicial	= $id;
				$fecha_inicial	= date("Y-m", strtotime($fecha_inicial)) . "-01";
				//$fecha_final	= $id2;
				$fecha_final = date("Y-m", strtotime($fecha_inicial)) . "-" . date("t", strtotime($fecha_inicial));
				$sql	= "SELECT
                        `creditos_solicitud`.`numero_solicitud`,
                        `creditos_tipoconvenio`.`iva_incluido`,
                        `creditos_tipoconvenio`.`tasa_iva`,
                        `operaciones_mvtos`.*
                    FROM
                        `operaciones_mvtos` `operaciones_mvtos`
                            INNER JOIN `creditos_solicitud` `creditos_solicitud`
                            ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
                            `numero_solicitud`
                                INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
                                ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
                                `idcreditos_tipoconvenio`
                    WHERE
                        (`operaciones_mvtos`.`tipo_operacion` =411) AND
                        (`creditos_solicitud`.`saldo_actual` >0.99)
                        AND
                        (`operaciones_mvtos`.`docto_neutralizador` =1)
                        AND
                        (`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
                        AND
                        (`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
                        /* AND
                        (`operaciones_mvtos`.`detalles` NOT LIKE '%$useKey') */
                    ";
				//echo $sql;
				$recibo = setNuevoRecibo(1, 1, fechasys(), 0, 10, "AJUSTE_DE_IVA", "NA", "ninguno", "NA", 99, 0);
				$txt = "GENERADO_POR_CALCULO_AUTOMATICO AL " . date("Y-m-d H");
				$rs = mysql_query($sql, cnnGeneral());
				if(!$rs){
					saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
				} else {
					while($rw = mysql_fetch_array($rs)){
						//obtiene el Numero de Solicitud
						$solicitud 		= $rw["docto_afectado"];
						$socio			= $rw["socio_afectado"];
						$fecha			= $rw["fecha_afectacion"];
						$parcialidad	= $rw["periodo_socio"];
						$iva			= 0;
						$monto			= $rw["afectacion_real"];
						$tasa_iva		= $rw["tasa_iva"];
						$operacion		= $rw["idoperaciones_mvtos"];
						if($rw["iva_incluido"]==1){
							$NuevoMonto = $monto * (1/(1 +  $tasa_iva)); //1000 * 1/1.15
							$iva		= $monto - $NuevoMonto;

							setNuevoMvto($socio, $solicitud, $recibo,
							$fecha, $iva, 413, $parcialidad, $txt);
							//Si el IVA esta Incluido Crear IVA y Descontar Monto
							$sqlNuevoM = "UPDATE operaciones_mvtos
    										SET afectacion_real=$NuevoMonto,
    										afectacion_cobranza=$NuevoMonto,
    										afectacion_contable=$NuevoMonto,
    										afectacion_estadistica=$NuevoMonto /*,
    										detalles=  CONCAT(detalles, '; $useKey')*/
    										WHERE idoperaciones_mvtos=$operacion
    										AND tipo_operacion =411";
							$tmp = my_query($sqlNuevoM);
							if ($tmp["stat"] == false){
								echo "<p class='aviso'>LA ACTUALIZACION CON CODIGO $operacion, NO SE EFECTUO; EL SISTEMA DEVOLVIO " . $tmp[SYS_MSG] ."</p>";
							} else {
								//echo "<p class='aviso'>LA OPERACION FUE EXITOSA, EL SISTEMA DIJO " . $tmp["info"] ."</p>";
							}
						} else {

							$iva		= $monto * $tasa_iva;

							setNuevoMvto($socio, $solicitud, $recibo,
							$fecha, $iva, 413, $parcialidad, txt);

						}
					}
				}//nd if
				@mysql_free_result($rs);
				//unset($rs);
				//saveError(10,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "EL Usuario $oficial Utilizo la Utileria 837");
				break;
				//Elimina Entradas de Archivos Inexistentes
			case 838:
				break;
				/**
				 * Depura las sucursales segun su caja local
				 */
			case 839:
				$xSuc 		= new cSucursal( getSucursal() );
				$msg		.= $xSuc->setValidar();
				$xCL 		= new cCajaLocal();
				$msg		.= $xCL->setValidar();

				break;
			case 841:
				//Eliminar socios Duplicados
				$SQLRep	= "SELECT codigo, nombrecompleto, apellidopaterno, apellidomaterno,
				COUNT(codigo) AS 'repetido', 
				MAX(fecha_de_revision) AS 'auditado',
				MAX( CHAR_LENGTH(curp) ) as 'mcurp',
				MAX(grupo_solidario) AS 'grupo'
						FROM socios_general
					GROUP BY
						codigo
				HAVING repetido > 1";

				$rsRep = mysql_query($SQLRep, cnnGeneral() );

				while($rw = mysql_fetch_array($rsRep)){
					$socio	= $rw["codigo"];
					$fecha	= $rw["auditado"];
					$grupo	= $rw["grupo"];
					$reo	= $rw["repetido"];
					$nombre	= $rw["nombrecompleto"] . " " . $rw["apellidopaterno"] . " " . $rw["apellidomaterno"];
					$tcurp	= $rw["mcurp"];
					$sqlDEL = "DELETE FROM socios_general WHERE codigo=$socio AND fecha_de_revision!='$fecha' ";
					$x = my_query($sqlDEL);

					$sqlDEL2 = "DELETE FROM socios_general WHERE codigo=$socio AND CHAR_LENGTH(curp) < $tcurp ";
					$x2 = my_query($sqlDEL2);

					if ($x["stat"] == false AND $x2["stat"] == false ){
						$msg	.= date("H:i:s") . "\t$socio\tSe **FALLO** AL ELIMINAR (" . $x["error"] . ") \r\n";
					} else {
						$msg	.= date("H:i:s") . "\t$socio\tSe **ELIMINA** por estar $reo veces, se Omite el Registro con fecha $fecha y Grupo $grupo \r\n";
					}

				}
				break;
			case 842:
				$xCred		= new cUtileriasParaCreditos();
				$msg		.= $xCred->setCuadrarCreditosByMvtos();
				break;
			case 844:
				/**
				 * Elimina la cache de sessiones
				 */
				$sqlDel = "DELETE FROM usuarios_web_connected";
				$cmd = my_query($sqlDel);
				if ($cmd["stat"] == false ){

				} else {
					$msg	.= date("H:i:s") . "\t\t\t SE BORRO LA CACHE DE SESSIONES \r\n";
				}
				break;

			case 845:
				//modifcando la numeracion de un socio a otro

				$numero_socio 	= $id;
				$numero_nuevo	= $id2;
				$cSoc			= new cSocio($numero_socio);
				//$cSoc->init();
				$msg	.= $cSoc->setChangeCodigo($numero_nuevo);

				break;

			case 846:
				$fecha_inicial = $id;

				/**
				 * Genera un Array Multidimensional entre fechas
				 */
				$aDSoc	= array();

				/* Actualiza el Ingreso del Socio segun la fecha de pago de la PP */
				$sqlPP = "SELECT socio_afectado, fecha_operacion, recibo_afectado
						FROM operaciones_mvtos WHERE
						tipo_operacion = 703
						AND fecha_operacion >='$fecha_inicial' ";

				$msg .= "================== MODIFICANDO LA FECHA DE ALTA DE LA PERSONA SEGUN PAGO\r\n ";
				$msg .= "HORA\tSOCIO\tRECIBO\tINFORMACION \r\n";


				$rs = mysql_query($sqlPP, cnnGeneral() );

				while ( $rw = mysql_fetch_array($rs) ){
					$socio	= $rw["socio_afectado"];
					$fecha	= $rw["fecha_operacion"];
					$recibo	= $rw["recibo_afectado"];

					$sqlUS = "UPDATE socios_general
						SET estatusactual=10,
							fechaalta = '$fecha'
					 WHERE  codigo = $socio";
					my_query($sqlUS);

					$msg	.= date("H:i:s") . "\t$socio\t$recibo\t Actualizando la fecha de Alta a $fecha \r\n";
					$aDSoc[$socio]["fecha"]		= $fecha;
					$aDSoc[$socio]["recibo"]	= $recibo;

				}
				@mysql_free_result($rs);

				$msg .= "================== MODIFICANDO LA FECHA DE ALTA DE LA PERSONA POR MODIFICACIONES\r\n ";
				$msg .= "HORA\tSOCIO\tRECIBO\tINFORMACION \r\n";

				//Actualizar Socios segun su fecha de Ingreso.
				$sqlS	= "SELECT * FROM socios_general
					WHERE fechaalta>='$fecha_inicial'
					/* AND estatus_actual = 10 */";
				$SRs	= mysql_query($sqlS, cnnGeneral());
				while($rw = mysql_fetch_array($SRs)){
					$socio	= $rw["codigo"];
					if ( isset($aDSoc[$socio]["fecha"]) ){
						$msg	.= date("H:i:s") . "\t$socio\t" . $aDSoc[$socio]["recibo"] . "\tSe Omite el Socio, ya se modifico\r\n";
					} else {
						$sqlPPS = "SELECT socio_afectado, fecha_operacion, recibo_afectado
							FROM operaciones_mvtos
							WHERE
							(tipo_operacion = 703 OR tipo_operacion = 701)
							AND socio_afectado=$socio LIMIT 0,1 ";
						$DxS	= obten_filas($sqlPPS);
						$fecha	= $DxS["fecha_operacion"];
						$recibo	= $DxS["recibo_afectado"];

						if ( isset($fecha)){
							$sqlUS = "UPDATE socios_general
								SET estatusactual=10,
									fechaalta = '$fecha'
					 				WHERE  codigo = $socio";
							my_query($sqlUS);

							$msg	.= date("H:i:s") . "\t$socio\t$recibo\t Actualizando la fecha de Alta a $fecha \r\n";
						} else {
							$sqlUS = "UPDATE socios_general
								SET estatusactual=10,
									fechaalta = fechaentrevista
					 				WHERE  codigo = $socio";
							my_query($sqlUS);
							$msg	.= date("H:i:s") . "\t$socio\tERROR\tNo Existen Datos para Actualizar, se toma la fecha de Entrevista \r\n";
						}

					}
				}
				break;
			case 847:
				/**
				 * Eliminar Recibos de Pendientes
				 */
				$fecha_inicial = $id;


				$msg .= "================== ELIMINANDO LOS RECIBOS DE PENDIENTES\r\n ";
				$msg .= "================== USUARIO: $oficial \r\n ";
				$msg .= "HORA\tSOCIO\tDOCUMENTO\tRECIBO\tINFORMACION \r\n";

				//Eliminar Operaciones
				$sqlEO			= "DELETE FROM operaciones_mvtos WHERE (SELECT tipo_docto FROM operaciones_recibos
									WHERE idoperaciones_recibos = operaciones_mvtos.recibo_afectado
									AND
									numero_socio = operaciones_mvtos.socio_afectado LIMIT 0,1) = 22
									AND (fecha_operacion>='$fecha_operacion') ";
				$y	= my_query($sqlEO);

				$msg		.= date("H:i:s") . "\t\t\t\tEliminando Movimientos Relacionado(" . $y["info"] . ")\r\n";

				$sql = "SELECT * FROM operaciones_recibos WHERE tipo_docto=22 AND fecha_operacion>='$fecha_inicial' ";
				$rs = mysql_query($sql, cnnGeneral());
				while($rw = mysql_fetch_array($rs)){
					$socio		= $rw["numero_socio"];
					$credito	= $rw["docto_afectado"];
					$recibo		= $rw["idoperaciones_recibos"];

					//Eliminar Recibos
					$sqlER			= "DELETE FROM operaciones_recibos WHERE idoperaciones_recibos = $recibo AND numero_socio=$socio";
					$x 				= my_query($sqlER);

					$msg		.= date("H:i:s") . "\t$socio\t$credito\t$recibo\tEliminando Recibo y Operaciones\r\n";

				}

				break;
			case 849:
				/**
				 * generar esquema de prestamos reestructurados
				 */
				break;
			case 850:
				$validar	= strtolower($id);
				$setVal		= false;
				if ( $validar == "si" ){
					$setVal	= true;
				}
				$fecha		= fechasys();
				$cUtils		= new cUtileriasParaCreditos();

				$msg 		.= $cUtils->setValidarCreditos($fecha, false, $setVal);
				break;
				/**
				 * Genera el anno fiscla, mes fiscal y dia del anno en Operaciones Mvtos
				 */
			case 851:
				$sql = "UPDATE operaciones_mvtos
						SET
							periodo_anual = DATE_FORMAT(fecha_afectacion, '%Y'),
							periodo_semanal = DATE_FORMAT(fecha_afectacion, '%w'),
							periodo_mensual = DATE_FORMAT(fecha_afectacion, '%m')";
				$x = my_query($sql);
				$msg .= $x["info"];
				break;
				/**
				 * Purga Recibos Duplicados
				 */
			case 852:
				$msg		.= date("H:i:s") . "\tEliminando Recibos Duplicados\r\n";

				$msg        .= setPurgeFromDuplicatedRecibos();
				setFoliosAlMaximo();
				break;
				/**
				 * Actualiza el Estatus de los Creditos sin distinguir sucursal
				 */
			case 853:
				$fecha_inicial 	= setFechaValida($id);
				$xRec			= new cReciboDeOperacion();
				$recibo			= $xRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, $fecha_inicial, 1, RECIBOS_TIPO_ESTADISTICO, "AJUSTE_DE_ESTATUS_DE_CREDITOS");
				//$recibo 		= setNuevoRecibo(1, 1, fechasys(), 0, 10, "AJUSTE_DE_ESTATUS_DE_CREDITOS", "NA", "ninguno", "NA", 99, 0);
				$cUtils 	= new cUtileriasParaCreditos();
				$msg        .= $cUtils->setEstatusDeCreditos($recibo, $fecha_inicial, false, true);
				break;
				//Valida Recibos
			case 856:
				$afectar    = strtolower($id);
				$sqlMvtos 	= " SELECT
							`operaciones_mvtos`.`recibo_afectado`            AS `recibo`,
							COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `operaciones`
						FROM
							`operaciones_mvtos` `operaciones_mvtos`
						GROUP BY
							`operaciones_mvtos`.`recibo_afectado`";
				$arrCM		= array();
				$arrO		= array();

				$rsM 		= mysql_query($sqlMvtos, cnnGeneral() );
				while( $rw = mysql_fetch_array($rsM) ){
					$arrCM[ $rw["recibo"] ] = $rw["operaciones"];
				}
				$sqlRecs	= "SELECT * FROM operaciones_recibos ";

				$msg        .= "=================\tVALIDANDO RECIBOS\t=================\r\n";
				$msg        .= "#OP.ID\tRECIBO\tMENSAJE\r\n";

				$rsO		= mysql_query($sqlRecs, cnnGeneral() );
				$i			= 0;

				while($rwO = mysql_fetch_array($rsO) ){
					$recibo		= $rwO["idoperaciones_recibos"];
					$tipo       = $rwO["tipo_docto"];
					//Si no existe el recibo
					if ( !isset( $arrCM[$recibo] ) OR $arrCM[$recibo] == false ){
						$sqlDRecs	= "DELETE FROM operaciones_recibos WHERE idoperaciones_recibos = $recibo " ;
						if ($afectar == "si"){
							if ( $tipo != 12 ){
								my_query($sqlDRecs);
							} else {
								$msg        .= "$i\t$recibo\tEl recibo no se elimina por que es un CIERRE DEL DIA \r\n";
							}

						}
						$msg        .= "$i\t$recibo\tRecibo Eliminado por no Contener Movimientos \r\n";
					} else {
						//Enriquecer el Array
						$arrO[ $recibo ]	= 1;
					}
					$i++;
				}
				$msg        .= "==========\tVALIDANDO MOVIMIENTOS\r\n";
				//Verificar Movimientos
				$cRec		= new cReciboDeOperacion(10, false);
				$i			= 0;
				if ($afectar == "si"){
					$RecHuerf	= $cRec->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, fechasys(), 1, 10, "RECIBO_DE_MVTOS_HUERFANOS", "na", "ninguno", "na", DEFAULT_GRUPO, false);
				} else {
					$RecHuerf   = DEFAULT_RECIBO;
				}
				$arrHuerf	= array();
				$strWH		= "";
				$io			= 0;
				foreach ( $arrCM as $key=>$value ){
					if ( !isset($arrO[ $key ] ) OR $arrO[ $key ] == false ) {
						$msg        .= "$i\t$key\tEl Recibo no Existe, sin embargo tiene $value Movimientos activos \r\n";
						//Agregado a la Matriz
						//XXX: Depurar, mvtos que no podran ser huerfanos Ministraciones, pagos, depositos
						//Generar uno por dia, una vez generado agregar a la Matriz de Recibos
						$arrHuerf[ $key	] = $value;
						if ( $io == 0 ){
							$strWH .= "$key";
						} else {
							$strWH .= ",$key";
						}
						$io++;
						//fin del Agregado
					}
					$i++;
				}
				//Ejecuta la Consulta de Actualizacion
				if ($strWH != "" ){
					$sqlUS = "UPDATE operaciones_mvtos
							SET recibo_afectado = $RecHuerf
							WHERE FIND_IN_SET(recibo_afectado, \"$strWH\" ) ";
					if ($afectar == "si"){
						$a      = my_query($sqlUS);
					}
					$msg	.= $a["info"];
				}
				if ($afectar == "si"){
					$cRec->setFinalizarRecibo(true);
					$msg	.= $cRec->getMessages();
				}

				break;
			case 857:
				$ForzarCorreccion		= strtoupper($id);
				$Forzar					= ( $ForzarCorreccion == "SI") ? true : false;
				$msg .= "================== REESTRUCTURANDO SALDOS DE INTERESES EN CREDITOS\r\n ";
				$msg .= "================== USUARIO: $oficial \r\n ";
				//Actualiza los Intereses Devengados y Pagados, asi  como los no pagados en base a los movimientos que tengan.
				//lleva a cero los registros
				$xCUtils			= new cUtileriasParaCreditos();
				$msg				.= $xCUtils->setAcumularIntereses($Forzar);

				break;
			case 858:
				$validar	= strtolower($id);
				$sqlSocs    = "SELECT
								`socios_general`.*
							FROM
								`socios_general` `socios_general`
							WHERE
								(`socios_general`.`estatusactual` !=20) ";
				$rs = mysql_query($sqlSocs, cnnGeneral() );

				while($rw = mysql_fetch_array($rs) ){
					$xSoc 	= new cSocio( $rw["codigo"] );
					$xSoc->init($rw);
					//$DSoc = $xSoc->
					$msg	.= $xSoc->getValidacion();
				}
				break;

			case 859:
				$clx 	=  new cCajaLocal(99);
				$msg 	.= $clx->setReestablecerNumeracion();
				break;
				//Creditos.- Elimina Movimientos Estadisticos de Creditos Pagados
			case 860:

				//Construir la Array de Letras
				$EsSucursal		= strtolower($id);
				$EnDetalle		= strtolower($id2);
				$Avisar			= strtolower($id3);
				$xSI			= new cMigracionSIBANCS();
				$msg			.= $xSI->setCrearLetras($EsSucursal, $EnDetalle, $Avisar);
				break;
			case 861:
				$xSI	= new cMigracionSIBANCS();
				$msg	.= $xSI->CompararPlanesDePago();
				break;
			case 862:
				$xSI	= new cMigracionSIBANCS();
				$msg	.= $xSI->setRepararPlanDePagos();
				break;
				//Concilia cuentas sisbancs vs cuentas SAFE
			case 863:
				$AppSucursal	= strtoupper($id);
				$xMig			= new cMigracionSIBANCS();
				$msg			.= $xMig->setConciliarCuentas($AppSucursal);
					
				break;
				//Elimina cuentas con saldos Negativos
			case 864:
				$msg	.= "============= ELIMINANDO CUENTAS NEGATIVAS \r\n";

				$AppSucursal		= strtoupper($id);
				//$fecha_de_migracion	= $id2;
				$BySucursal			= true;
				$msg	.= "============= DISTINGUIR POR SUCURSAL?:  $AppSucursal\r\n";
				if ( $AppSucursal != "SI" ){
					$BySucursal	= false;
				}
				$xUc	= new cUtileriasParaCaptacion();
				$msg	.= $xUc->setCleanCuentasMenoresACero_ALaVista($BySucursal);
				break;
				//===========		Reestructura la Informaci�n Dinamica de los Creditos
			case 865:
				//Crear una Matriz de  Pagos
				$arrPagos		= array();

				$sqlMvtosMark	= "SELECT
								`operaciones_mvtos`.`docto_afectado`,
								`operaciones_mvtos`.`tipo_operacion`,
								`operaciones_mvtos`.`fecha_operacion`,
								SUM(`operaciones_mvtos`.`afectacion_real`) AS `monto`
							FROM
								`operaciones_mvtos` `operaciones_mvtos`
									INNER JOIN `eacp_config_bases_de_integracion_miembros`
									`eacp_config_bases_de_integracion_miembros`
									ON `operaciones_mvtos`.`tipo_operacion` =
									`eacp_config_bases_de_integracion_miembros`.`miembro`
							WHERE
								(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2005)
							GROUP BY
								`operaciones_mvtos`.`docto_afectado`,
								`operaciones_mvtos`.`tipo_operacion`,
								`operaciones_mvtos`.`fecha_operacion` ";
				$rsA		= mysql_query($sqlMvtosMark, cnnGeneral() );

				while( $rw = mysql_fetch_array($rsA)){
					$arrPagos[ $rw["docto_afectado"] .  "-" . $rw["fecha_operacion"] . "-" . $rw["tipo_operacion"] ] = $rw["monto"];
				}
				//Seleccionar los creditos
				$BySucursal		= "";
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
					$BySucursal
					";
					$rs		= mysql_query($sqlCreds, cnnGeneral() );
					while( $rw = mysql_fetch_array($rs) ){
						//socio
						$socio					= $rw["numero_socio"];
						$credito				= $rw["numero_solicitud"];
						$fecha_de_ministracion	= $rw["fecha_ministracion"];
						$dias					= $rw["dias_autorizados"];
						$tipo_de_pago			= $rw["tipo_de_pago"];
						$tipo_de_calculo_int	= $rw["tipo_de_calculo_de_interes"];
						$monto_ministrado		= $rw["monto_ministrado"];

						//credito
						//fecha de ministraci�n
						//fecha de
						$dias_transcurridos		= restarfechas($fecha_de_migracion, $fecha_de_ministracion);
						$abonos					= 0;
						$intereses				= 0;
						$moratorios				= 0;

						for($i = 0; $i <= $dias_transcurrridos; $i++ ){
							$fecha_de_corte = sumardias($fecha_de_ministracion, $i);
							$isKey					= "$credito-$fecha_de_corte";
							$capital		= round($arrPagos[$isKey . "-120"], 2);
							$interes		= round($arrPagos[$isKey . "-140"], 2) + round($arrPagos[$isKey . "-142"], 2);
							$mora			= round($arrPagos[$isKey . "-141"], 2);

							$abonos			+=$capital;
							$intereses		+=$interes;
							$moratorios		+=$mora;
							$saldo			= $monto_ministrado - $abonos;
							$estatus		= 10;

							$sqlIEvent	= "INSERT INTO creditos_datos_dinamicos
												(numero_de_credito, numero_de_socio, fecha_de_corte, saldo,
												numero_de_parcialidad, estatus_del_credito, estatus_de_la_parcialidad,
												fecha_de_mora, fecha_de_vencimiento,

												abono_interes_normal,
												abono_interes_moratorio,
												interes_normal_devengado,
												interes_moratorio_devengado,

												suma_interes_normal_devengado,
												suma_interes_normal_pagado,
												suma_interes_moratorio_devengado,
												suma_interes_moratorio_pagado
												)
												VALUES
												($credito, $socio, '$fecha_de_corte',
												$saldo, 1, $estatus, 10,
												'2009-3-7', '2009-3-7',
												$interes, $mora, 0, 0,
												0, 0, 0, 0) ";
						}
					}
					//Iniciar dia uno, sumar cada caso

					//Determinar el Estatus
					break;
					//Actualiza las Sucursal a
			case 867:
				$cDB 	= new cSAFEData();
				$xCL	= new cCajaLocal();
				$msg   	.= $cDB->setLowerSucursal();
				$msg	.= $xCL->setValidar();
				break;
				//crear cuentas de ahorros no existentes en sisbancs
			case 868:
					
				$xMig		= new cMigracionSIBANCS();
				$msg		.= $xMig->setCrearCaptacionNoExistente();
					
				break;
				//Eliminar cuentas no existentesn en SISBANCS
			case 869:
				$xMig		= new cMigracionSIBANCS();
				$msg		.= $xMig->setEliminarCuentasNoExistentes();
					
				break;
			case 870:
				$cUCredit 		= new cUtileriasParaCreditos();
				$msg			.= $cUCredit->setEliminarCreditosNegativos();
				break;

			case 871:
				$xMig		= new cMigracionSIBANCS();
				$msg		.= $xMig->setGenerarPlanDePagos();
				break;
				//Ajusta las diferencias entre los creditos COMPACW
			case 872:
				$xMig		= new cMigracionSIBANCS();
				$msg		.= $xMig->setConciliarCreditos();
				break;
				//=======================================================================================================================================================================================
			case 873:
				//Utileria Eliminada y cambiada por otra
				$msg		.= "0\t\tUTILERIA DEPRECIADA\r\n";
				$msg		.= "1\t\tUSE LA UTILERIA 888\r\n";
				$msg		.= "2\t\tUSE LA UTILERIA 889\r\n";
				$msg		.= "3\t\tUSE LA UTILERIA 900\r\n";
				$msg		.= "4\t\tUSE LA UTILERIA 857\r\n";
				break;
				//=======================================================================================================================================================================================
			case 874:
				$incluirSinSaldo		= strtoupper($id);
				$cUCredit 		= new cUtileriasParaCreditos();
				$msg			.= $cUCredit->setRegenerarCreditosMinistraciones($incluirSinSaldo);
				break;
			case 875:
				$fecha			= $id;
				$cUCredit 		= new cUtileriasParaCreditos();
				$msg			.= $cUCredit->setRegenerarCreditosAMora( $fecha );

				break;
			case 876:
				$fecha			= $id;
				$cUCredit 		= new cUtileriasParaCreditos();
				$msg			.= $cUCredit->setRegenerarCreditosAVencidos( $fecha );
					
				break;
				//verifica si los grupos solidarios son validos
			case 877:
				$sql = "SELECT * FROM socios_grupossolidarios ";
				$rs = mysql_query($sql, cnnGeneral() );

				while ( $rw  = mysql_fetch_array($rs) ){
					$codigo	= $rw["idsocios_grupossolidarios"];
					$xG	= new cGrupo($codigo, false);
					$xG->init($rw);
					$msg	.= $xG->setVerificarValidez(false, true);
				}

				break;
			case 878:
				$xMig		= new cMigracionTCB();
				$msg		= $xMig->TCB_GenerarLetras();
				break;
				//ajusta las cuentas de ahorro por saldo actual y no por movimientos
			case 879:
				$msg	    .= "============================ GENERANDO AJUSTES DE CUENTA A LA VISTA \r\n ";

				$CRecibo 	= new cReciboDeOperacion(10, true);
				$recibo 	= $CRecibo->setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO,
				fechasys(), 1, 10,
										"POLIZA_MASIVA_DE_AJUSTE", "NA", "ninguno", "NA", DEFAULT_GRUPO );

				$sql = "SELECT SQL_CACHE * FROM captacion_saldos_comparados WHERE saldo_cuenta != saldo_obtenido /* LIMIT 0,500 */ ";
				$rs		= mysql_query($sql, cnnGeneral() );
				while( $rw = mysql_fetch_array($rs) ) {
					$cuenta			= $rw["numero_cuenta"];
					$socio			= $rw["numero_socio"];
					$saldo			= round($rw["saldo_cuenta"], 2);
					$sdoOperativo	= round($rw["saldo_obtenido"], 2);
					if ( $saldo != $sdoOperativo ){
						$cCta		= new cCuentaALaVista($cuenta, $socio);
						$cCta->init();
						$cCta->setForceOperations();
						$cCta->setReciboDeOperacion($recibo);

						if ( $saldo > $sdoOperativo ){
							//depositar
							$diferencia	= $saldo - $sdoOperativo;
							$msg	.= "$socio\t$cuenta\tDEPOSITO\tExiste diferencia ( $diferencia ) entre el saldo de la cuenta ( $saldo ) y el obtenido por Movimientos ( $sdoOperativo) \r\n";
							$cCta->setDeposito($diferencia, "NA", "ninguno");
						} else {
							//retirar
							$diferencia	= $sdoOperativo - $saldo;
							$msg	.= "$socio\t$cuenta\tRETIRO\tExiste diferencia ( $diferencia ) entre el saldo de la cuenta ( $saldo ) y el obtenido por Movimientos ( $sdoOperativo) \r\n";
							$cCta->setRetiro($diferencia, "NA", "ninguno");
						}
						$msg	.= $cCta->getMessages("txt");
					} else {
						$msg	.= "$socio\t$cuenta\tNO_DIF\tNo Existe diferencia ( $diferencia)( $saldo | $sdoOperativo) \r\n";
					}
				}
				break;
			case 880:
				$ForzarCorreccion	= ( strtoupper($id) == "SI") ? true : false;
				$xPerUtils			= new cPersonas_utils();
				$msg				.= $xPerUtils->setCorregirDomicilios($ForzarCorreccion);

				break;
				//purga cr�ditos no existentes o no migrables a TCB
			case 881:
				$xTCB	= new cMigracionTCB();
				$msg	.= $xTCB->Creditos_EliminarNoExistentes();
				break;
			case 882:
				$fecha		= $id;
				$cUCredit 	= new cUtileriasParaCreditos();
				$msg		.= $cUCredit->setCuadrarCreditosBySaldo($fecha);
				break;
				//Elimina saldo de interes de creditos pagados
			case 883:
				$xCred		= new cUtileriasParaCreditos();
				$msg		.= $xCred->setEliminarInteresesDeCreditosPagados();
				break;
			case 884:
				$sql	= "SELECT
					`captacion_cuentas`.*,
					`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
					/* heredado de inversion */
					`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
					`captacion_cuentas`.`fecha_afectacion`              AS `apertura`,
					`captacion_cuentas`.`inversion_fecha_vcto`          AS `vencimiento`,
					`captacion_cuentas`.`tasa_otorgada`                 AS `tasa`,
					`captacion_cuentas`.`dias_invertidos`               AS `dias`,
					`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`,
					`captacion_cuentas`.`saldo_cuenta` 			        AS `saldo`,
					/*heredado de captacion a la vista */
					`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
					`captacion_subproductos`.`algoritmo_de_premio`,
					`captacion_subproductos`.`algoritmo_de_tasa_incremental`,
					`captacion_subproductos`.`metodo_de_abono_de_interes`,
					`captacion_subproductos`.`destino_del_interes`
				FROM
					`captacion_cuentas` `captacion_cuentas`
						INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
						ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
						`idcaptacion_cuentastipos`
							INNER JOIN `captacion_subproductos` `captacion_subproductos`
							ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
							.`idcaptacion_subproductos`
				WHERE
					`captacion_cuentas`.tipo_cuenta = 20";
				$rs	= mysql_query($sql, cnnGeneral() );
				while (  $rw	= mysql_fetch_array($rs) ){
					$socio	= $rw["numero_socio"];
					$cuenta	= $rw["numero_cuenta"];
					$xInv	= new cCuentaInversionPlazoFijo($cuenta, $socio);
					$xInv->init($rw);
					$msg	.= $xInv->setDelete();
				}
				break;
			case 885:
				$fecha	= $id;
				$xUC	= new cUtileriasParaCaptacion();
				$msg	.= $xUC->getGenerarBaseGravadaMensualIDE($fecha);
				break;
			case 886:
				$ForzarCorreccion		= strtoupper( $id );
				$Forzar					= ( $ForzarCorreccion == "SI") ? true : false;
				$xCUtils				= new cUtileriasParaCaptacion();
				$msg					.= $xCUtils->setValidarCuentas($Forzar);
				break;
				//TODO: Terminar
			case 887:
				$xop				= new cUtileriasParaOperaciones();
				$msg				.= $xop->setPurgarMovimientos();
				break;
			case 888:
				//Generar los Movimientos del Fin de Mes
				$fecha_inicial		= $id;
				$fecha_final		= $id2;
				$NumeroDeCredito	= ( $id3 == "NUMERO_DE_CREDITO") ? false : $id3;
				$ForzarCorreccion	= ( strtoupper($de) == "SI") ? true : false;
				$xCUtils		= new cUtileriasParaCreditos();
				$msg			.= "============ GENERANDO MOVIMIENTOS DEL FIN DE MES V1.04\r\n";
				$msg			.= $xCUtils->setGenerarMvtoFinDeMes($fecha_inicial, $fecha_final, $NumeroDeCredito, $ForzarCorreccion);

				break;
			case 889:
				$xCUtils		= new cUtileriasParaCreditos();
				$msg			.= "============ GENERANDO SALDOS SPM DE CREDITOS HISTORICOS \r\n";
				$msg			.= $xCUtils->setReestructurarSDPM_Planes();
				
				$msg			.= $xCUtils->setReestructurarSDPM(false, false, false, false, false, false);
				
				break;
			case 900:
				$NumeroDeCredito	= ( $id == "NUMERO_DE_CREDITO") ? false : $id;
				$FechaInicial		= ( $id2 == "FECHA_INICIAL" ) ? false : $id2 ;
				$FechaFinal			= ( $id3 == "FECHA_FINAL" ) ? false : $id3 ;
					
				$xCUtils			= new cUtileriasParaCreditos();
				$msg				.= "============ GENERANDO INTERESES SOBRE SDPM HISTORICOS \r\n";
				$msg				.= $xCUtils->setRegenerarInteresDevengado( $NumeroDeCredito, $FechaInicial, $FechaFinal );
				break;
			case 1101:
				//Contabilidad.- generar Saldos del Ejercicio
				$ejercicio	= $id;
				$xUCont		= new cUtileriasParaContabilidad();
				$msg		.= $xUCont->setGenerarSaldosDelEjercicio($ejercicio);
				break;
			case 1102:
				//regenerar perfil contable de polizas
				$Fecha		= $id;
				$xUCont		= new cUtileriasParaContabilidad();
				$msg		.= "============ GENERAR PREPOLIZAS CONTABLES AL $Fecha \r\n";
				$msg		.= $xUCont->setRegenerarPrepolizaContable($Fecha);
				break;
			case 1103:
				//numero de recibo
				$Recibo		= $id;
				$Regenerar	= ( strtoupper($id2) == "SI") ? true : false;
				$xUCont		= new cUtileriasParaContabilidad();
					
				if ( $Regenerar == true ){
					$msg		.= $xUCont->setRegenerarPrepolizaContable(false, $Recibo);
				}
				$msg		.= "============\t\r\n";
				$msg		.= "============\tGENERAR POLIZA CONTABLE DEL RECIBO $Recibo \r\n";
				$msg		.= "============\t\r\n";
				$msg		.= $xUCont->setPolizaPorRecibo($Recibo);
				//

				break;
			case 1104:
				//
				$cajero				= $id;
				$fecha				= $id2;
				$numero_de_poliza	= ( $id3 == "NUMERO_DE_POLIZA") ? false : $id3;
				$xUCont				= new cUtileriasParaContabilidad();
				//$msg				.= $xUCont->setPolizaPorCajero( $cajero, $fecha, $numero_de_poliza );
				break;
			case 501:
				$xop				= new cUtileriasParaOperaciones();
				$msg				.= $xop->setGenerarRecibosGlobales();
				break;
			case 13001:
				$ForzarCorreccion	= ( strtoupper($id) == "SI") ? true : false;
				$xPerUtils			= new cPersonas_utils();
				$msg				.= $xPerUtils->setCorregirActividadEconomica($ForzarCorreccion);
				
				break;
				//===============genera colonias por localidades
			case 13002:
				$ql				= new MQL();
				$rs				= $ql->getDataRecord("SELECT * FROM `catalogos_localidades`");
				$xLoc			= new cCatalogos_localidades();
				$xCol			= new cGeneral_colonias();
				foreach ($rs as $rw){
					$xLoc->setData($rw);
					$xCol->ciudad_colonia( $xLoc->nombre_de_la_localidad()->v() );
					$xCol->codigo_de_estado( $xLoc->clave_de_estado()->v() );
					$xCol->codigo_de_municipio( $xLoc->clave_de_municipio()->v() );
					$xCol->codigo_postal( $xLoc->clave_de_localidad()->v() );
					$DEstado		= $ql->getDataRow("SELECT * FROM `general_estados` WHERE idgeneral_estados= " . $xLoc->clave_de_estado()->v());
					$NEstado		= "";
					if( isset($DEstado["nombre"] )){
						$NEstado = $DEstado["nombre"];	
						
					}
					$xCol->estado_colonia($NEstado);
					$xCol->fecha_de_revision(fechasys());
					$DMun			= $ql->getDataRow("SELECT * FROM `general_municipios` WHERE `clave_de_municipio`= " . $xLoc->clave_de_municipio()->v() . " AND `clave_de_entidad`=" . $xLoc->clave_de_estado()->v());
					$NMun			= "";
					if(isset($DMun["nombre_del_municipio"])){
						$NMun		= $DMun["nombre_del_municipio"];
					}
					$xCol->municipio_colonia( $NMun );
					$xCol->nombre_colonia( $xLoc->nombre_de_la_localidad()->v() );
					$xCol->sucursal(getSucursal());
					$xCol->tipo_colonia("Colonia");
					$xCol->idgeneral_colonia( $xLoc->clave_de_localidad()->v() );
					$xCol->query()->insert()->save();
				}
				break;
			case 21101:
				$xUtils				= new cUtileriasParaCreditos();
				$msg				.= $xUtils->setActualizarPrimerPago();
				break;
			case 8201:
				$xUAml		= new cUtileriasParaAML();
				$msg		.= $xUAml->setGenerarPerfilesPorActividadEconomica();
				break;
			case 8202:
				$ForzarCorreccion	= ( strtoupper($id) == "SI") ? true : false;
				$xUAml		= new cUtileriasParaAML();
				$msg		.= $xUAml->setActualizarNivelDeRiesgo($ForzarCorreccion);
				break;
			case 8203:
				$xUPers		= new cPersonas_utils();
				$xUPers->setCrearArbolRelaciones();
				
				break;
			case 9001:
				$desucursal		= $id;
				$asucursal		= $id2;				
				$ql				= new MQL();
				$sql			= "SHOW TABLES IN " . MY_DB_IN;
				$rs				= $ql->getDataRecord($sql);
				foreach ($rs as $row){
					$tabla		= $row["Tables_in_" . MY_DB_IN];
					$isql		= "UPDATE $tabla SET sucursal='$asucursal' WHERE sucursal='$desucursal' ";
					$ql->setRawQuery($isql);
					$ql->setRawQuery("UPDATE $tabla SET sucursal=LOWER(sucursal)");
				}
				break;
		}

		if ( $command != false ){
			//Graba los Mensajes del LOG y cierra el Archivo
			$xFLog->setWrite($msg);
			$xFLog->setClose();
			echo $xFLog->getLinkDownload("Registro de Eventos");
		}


		?>
	</fieldset>
<?php
$jxc ->drawJavaScript(false, true); 
?>
<script  >
var mFrm	= document.frmUtils;

function myInfoUtil(){
	
	jsinfo_util();
	<?php
	echo $jsExtra; 
	?>
}
function setValExch(){
	document.getElementById("idClaves").value =  document.getElementById("id-clave").value;
	jsinfo_util();
}
function charEventUp(evt) {
    evt=(evt) ? evt:event;

    var charCode = (evt.charCode) ? evt.charCode :   ((evt.which) ? evt.which : evt.keyCode);

    

        switch(charCode){
        	case 121:	// F10
    			//Salvar Movimiento Editado
				mFrm.submit();
        	break;
        	default:

        	break;
        }

}
</script>
<?php
$xHP->fin(); 
?>

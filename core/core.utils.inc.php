<?php
/**
 * Core Captacion File
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * Core Captacion File
 * 		10/04/2008 Iniciar Funcion de Notificaciones 360
 */
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.common.inc.php");
include_once("core.operaciones.inc.php");
include_once("core.creditos.inc.php");
include_once("core.captacion.inc.php");
include_once("core.html.inc.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");

include_once("core.db.dic.php");
@include_once("../libs/sql.inc.php");
//@include_once("../libs/libmail.php");
@include_once '../libs/phpmailer/class.phpmailer.php';
@include_once '../libs/parse/EnhanceTestFramework.php';
@include_once '../libs/parse/parse.php';

/*
 * CANALES
 * 
 * aml = AML
 * creditos
 * captacion
 * 
 * */

//exec('%systemroot%\system32\shutdown.exe -r -t 0');
/*
 * # registering a service

win32_create_service(array(
service => myservice, # the name of your service
display => sample dummy PHP service, # description
params => c:\path\to\script.php run, # path to the script and parameters
));

# un-registering a service

win32_delete_service(myservice);

# code run as a service

if ($argv[1] == 'run') {
win32_start_service_ctrl_dispatcher('myservice');

while (WIN32_SERVICE_CONTROL_STOP != win32_get_last_control_message()) {
# write script here
# as a general rule, keep it below 30 seconds through each loop iteration
}
}

 */

class cSystemTask{
	private $mSystemCommands = array();
	private $mBackupFile	= "";
	function __construct(){

	//Crea el Nombre del Backup File
	$this->mBackupFile		= PATH_BACKUPS .  MY_DB_IN . "_" . date("Y-m-d") . ".sql.gz";
//"apagar_el_servidor" => "sudo /sbin/shutdown -P 0",
	$this->mSystemCommands	= array (
							"apagar_el_servidor" => "/usr/bin/apagar_desde_php",
							"reiniciar_el_servidor" => "reboot -n",
							"respaldar_la_base_de_datos" => "mysqldump --opt --add-drop-table --skip-triggers -h " . WORK_HOST . " -u " . USR_DB ." --password=" . PWD_DB . " " . MY_DB_IN . "| gzip > " . $this->mBackupFile . "",
							"respaldar_todas_las_bases_de_datos" => "",
							"instalar_cierre_automatico"
							);
	//mysqldump db_name table_name > table_name.sql
	}
	function setBackupDB_WithMail(){
		$msg		= "";
		$fecha		= date("Y-m-d");
		$lns		= exec($this->mSystemCommands["respaldar_la_base_de_datos"]);
		//Enviar el Mail SAFE-OSMS Respaldo de la Base de Datos
		$subject	= "SAFE-OSMS Respaldo de la Base de Datos $fecha";
		$body		= "<h3>S.A.F.E. OSMS</h3><h4>Demonio CRON</h4><p>Se Anexa repaldo de la Fecha $fecha</p><hr /><h5>SysAdmin</h5>";
		$file		= array( "path"  => $this->setBackupDB(), "mime" => "multipart/x-gzip");

		$msg		.= $this->sendMailToAdminWithFile($subject, $body, $file);
		return $msg;
	}
	function setBackupDB(){
		$msg		= "";
		$fecha		= date("Y-m-d");
		$lns		= exec($this->mSystemCommands["respaldar_la_base_de_datos"]);
		
		return $this->mBackupFile;
	}
	function setBackupTable($table){
		$file		= PATH_BACKUPS .  MY_DB_IN . "_$table_" . date("Y-m-d") . ".sql.gz";
		$ce			= exec("mysqldump --opt -h " . WORK_HOST . " -u " . USR_DB ." --password=" . PWD_DB . " " . MY_DB_IN . " $table| gzip > $file");
		return $file;
	}
	function setPowerOff(){
		exec($this->mSystemCommands["apagar_el_servidor"]);
	}
	/**
	 * funcion que envia un Correo Electronico al Admin con un Archivo
	 *
	 * @param string $subject
	 * @param string $body
	 * @param array $arrFile
	 * @return string
	 * el parametro $aarrFile Indica un array compuesto asi array ("path" =>
	 * "rutal al archivo", "mime" => "MIME/TYPE").
	 *
	 */
	function sendMailToAdminWithFile($subject = "", $body = "", $arrFile = false){
		
			$omsg	= "";

			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			//Tell PHPMailer to use SMTP
			$mail->IsSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug  = 0;
			$mail->Timeout    = 30;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host       = ADMIN_MAIL_SMTP_SERVER;
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port       = ADMIN_MAIL_SMTP_PORT;
			//Set the encryption system to use - ssl (deprecated) or tls
			//$mail->SMTPSecure = ADMIN_MAIL_SMTP_TLS;
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
			$mail->SetFrom(ADMIN_MAIL, 'S.A.F.E. OSMS System Backup');
			//Set an alternative reply-to address
			//$mail->AddReplyTo('replyto@example.com','First Last');
			//Set who the message is to be sent to
			$mail->AddAddress(ADMIN_MAIL, 'SAFE-OSMS Admin');
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
				//$m->Attach($arrFile["path"], $arrFile["mime"], "inline");
				$mail->AddAttachment($arrFile["path"]);
			}
			//$mail->AddAttachment('images/phpmailer-mini.gif');
		
			//Send the message, check for errors
			if(!$mail->Send()) {
				$omsg	.= "Error: " . $mail->ErrorInfo;
			} else {
				$omsg	.= "Mensaje Enviado con exito.";
			}
		
			return $omsg;
	}
	function sendMail($subject = "", $body = "", $to = "", $arrFile = false){
		$xNot		= new cNotificaciones();
		return $xNot->sendMail($subject, $body, $to, $arrFile);
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

/**
 * Funcion que crea o actualiza una tabla en el sistema
 * @param $NTable	Nombre de la Tabla la cual desea trabajar
 * @param $TCond	Tipo de Operacion 0 = nueva Estructura, 1 = Actaulizacion de la estructura
 * @return	null
 **/
function setStructureTableByDemand($NTable, $TCond = 0, $options = array() ){
	//$TCond 1 = Actualizar, 0 = Nuevo
	/**
		 * Crea la Estructura de una Tabla Determinada
		 */
	$msg	= "";
	$xSt	= new cTableStructure($NTable);
	$msg	= $xSt->setStructureTableByDemand($TCond, $options);
	return $msg;
}
/**
 * @author Son Nguyen
 * @since 11/18/2005
 * @package Framework.Data
 * @subpackage Math
 */
class cRegressionLineal {
	private $mDatas;
	/** constructor */
	function __construct($pDatas){
		$this->mDatas = $pDatas;
	}

	/** get the coefficients */
	function calculate() {
		$n 	= count($this->mDatas);
		$vSumXX = $vSumXY = $vSumX = $vSumY = 0;
		foreach ($this->mDatas AS $x=>$y) {
			$vSumXY 	+= $x*$y;
			$vSumXX 	+= $x*$x;
			$vSumX 		+= $x;
			$vSumY 		+= $y;
		} // rof
		$a = ($n*$vSumXY - $vSumX*$vSumY)/($n*$vSumXX - $vSumX*$vSumX);
		
		$b = ($vSumY - $a*$vSumX)/$n;
		return array($a,$b);
	}
	/** given x, return the prediction */
	function predict($x) {
		list($a,$b) = $this->calculate();
		$y = $a*$x+$b;
		return $y;
	}
}
class cMath {
	function irr ($investment, $flow) {
	    for ($n = 0; $n < 100; $n += 0.00001) {
		$pv = 0;
		$it = count($flow);
		for ($i = 0; $i < $it; $i++) {
		    $pv = $pv + ($flow[$i] / pow(1 + $n, $i + 1));
		    //echo "$pv + $flow[$i] / pow(1 + $n, $i + 1);<br />";
		}
		if ($pv <= $investment) {
		    return $n;
		}
	    }
	}
	function cat($capital, $flujo, $periodos){
		$tir        = $this->irr($capital, $flujo);
		$tri        = pow((1 + $tir), $periodos) - 1;
		$tri        = round(($tri * 100), 1);
		return $tri;
	}
}

class cFileImporter {
	private $mFecha		= "";
	private $mMessages	= "";
	private $mData		= array();
	private $mDelimiter	= ",";
	private $mType		= "csv";
	private $mLimitCampos	= 12;
	private $mPriLineaCol	= true;
	private $mDataRow		= false;
	private $mForceUTF		= false;
	private $mExo			= "";
	
	function __construct(){  }
	
	function processFile($file){
		$sucess	= true;
		if( isset($file) AND $file != false ){
			//Obtener Extension
			$DExt 	= explode(".", substr($file['name'], -6));
			$mExt	= $DExt[1];
		
			if($mExt == $this->mType){
				$completePath	= PATH_TMP . $file['name'];
				if(file_exists($completePath)==true){
					unlink($completePath);
					$this->mMessages	.= "WARN\tSE ELIMINO EL ARCHIVO " . $file['name'] . "\r\n";
				}
				if(move_uploaded_file($file['tmp_name'], $completePath )) {
					$this->mMessages	.= "OK\tSE GUARDO EXITOSAMENTE EL ARCHIVO " . $file['name'] . "\r\n";
				} else {
					$this->mMessages	.= "ERROR\tSE FALLO AL GUARDAR (" . $file['name'] . ")\r\n";
					$sucess				= false;
				}
			}	else {
				$this->mMessages		.= "ERROR\tEL TIPO DE ARCHIVO DE " . $file['name'] . "(" .$mExt . ") NO SE ACEPTA\r\n";
				$sucess					= false;
			}
		} else {
			$this->mMessages		.= "ERROR\tEL ARCHIVO NO ES VALIDO $file\r\n";
			$sucess					= false;			
		}
		if($sucess == true){
			//analizar el Archivo
			$gestor = @fopen($completePath, "r");
			
			$iReg 	= 0;
			//$cT		= new cTipos();
			//inicializa el LOG del proceso
			//$aliasFil	= getSucursal() . "-carga -batch-de-creditos-" . fechasys();
			//$xLog		= new cFileLog($aliasFil, true);
			if ($gestor) {
				while (!feof($gestor)) {
					$bufer			= fgets($gestor, 4096);
					if (!isset($bufer) ){
						$msg .= "ERROR\t$iReg\tLa Linea($iReg) no se leyo($bufer)\r\n";
						//$this->mData[]= array(); //Array Vacio
					} else {
						$bufer		= trim($bufer);
						$datos		= array();
						if($this->mExo	== ""){
							if($this->mLimitCampos > 0){
								$datos		= explode($this->mDelimiter, $bufer, $this->mLimitCampos);
							}							
						} else {
							//delimitar por X  echo 
							//$del			= substr_count($this->mExo, "|");
							$dex			= explode("|", $this->mExo);
							$init			= 0;
							foreach ($dex as $snipts){
								$tlen		= strlen($snipts) + 1;
								$datos[]	= trim(substr($bufer, $init, $tlen));
								$init		+= $tlen;	
							}
						}
						$this->mData[]		= $datos;
					}
					$iReg++;
				}
			}
		}
		return $sucess;
	}
	function setExo($str){ $this->mExo	= $str; }
	function setLimitCampos($campos){ $this->mLimitCampos = $campos;}
	function setCharDelimiter($char){ $this->mDelimiter	= $char; }
	function setType($tipo){ $this->mType = $tipo; }
	function getData(){ return $this->mData; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function setPrimeraLinea(){  }
	function setDataRow($data){ return $this->mDataRow	= $data; }
	function getFlotante($indice, $fallback = 0 ){	return $this->getV($indice, $fallback, MQL_FLOAT); }
	function getEntero($indice, $fallback = 0 ){	return $this->getV($indice, $fallback, MQL_INT); }
	function getFecha($indice, $fallback = false ){	return $this->getV($indice, $fallback, MQL_DATE); }
	function getV($indice, $fallback = null, $tipo = MQL_STRING, $equiv = false){
		$valor		= null;
		$row		= $this->mDataRow;
		//CORREGIR ID
		$indice		= $indice - 1;
		if(is_array($equiv)){
			$vtmp	= strtoupper($row[$indice]);
			if(isset($equiv[ $vtmp ])){
				$row[$indice]	= $equiv[ $vtmp ]; //cambiar indice por equivalente
			} else {
				$row[$indice]	= $fallback;
				$this->mMessages	.= "ERROR\tNo hay equivalente para " . $vtmp . " del Indice $indice  \r\n";
			}
		}
		if($this->mForceUTF == true){
			if(isset($row[$indice])){
				if(iconv('UTF-8', 'UTF-8//IGNORE', $row[$indice])){
					$row[$indice]	= iconv('UTF-8', 'UTF-8//IGNORE', $row[$indice]);
				} else {
					$row[$indice]	= iconv(mb_detect_encoding($row[$indice]), 'UTF-8//IGNORE', $row[$indice]);
				}
				//if($this->mForceUTF == true){ $cadena	= iconv('UTF-8', 'UTF-8//IGNORE', $cadena); }
				//$dato	= iconv(mb_detect_encoding($dato), 'UTF-8//IGNORE', $dato);				
			}
		}
		if(isset($row[$indice])){
			//$row[$indice]	= str_replace("/", -)
		}
		return parametro($indice, $fallback, $tipo, $row);
	}
	function setToUTF8(){	$this->mForceUTF		= true;	}
	function cleanCalle($valor = ""){
		$valor		= strtoupper($valor);
		$arr		= array("AVENIDA", "CALLE", "CALE ", "CALLLE", "AVE.", "AVE ", "C.", "C ", "NUM.", "NUM ", "NO ", "NOM.", "SIN NUMERO", "SN", "S/N", "SIN NIM", "LOTE ", "#", "NO.");
		$valor		= str_replace($arr, " ", $valor);
		return 		trim(preg_replace('!\s+!', ' ', $valor));
	}
	function cleanMail($email){
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				
		} else {
			$email	= "";
		}
		return $email;
	}
	function cleanString($cadena, $otros = false){
		$cleanArr	= array('/\s\s+/', '/(\")/', '[\\\\]', '/(\')/');
		if(is_array($otros)){
			$cleanArr	= array_merge($cleanArr,$otros);
		}
		$cadena 		= preg_replace($cleanArr, ' ', $cadena); //dob
		return trim($cadena);
	}
}
//Eimina datos no validos
class cTiposLimpiadores {
	
	function cleanString($cadena, $otros = false){
		$cleanArr	= array('/\s\s+/', '/(\")/', '[\\\\]', '/(\')/');
		if(is_array($otros)){
			$cleanArr	= array_merge($cleanArr,$otros);
		}
		$cadena 		= preg_replace($cleanArr, ' ', $cadena); //dob
		return $cadena;
	}
	function cleanEmpleo($cadenas, $PorDefecto = ""){
		$cadenas	= str_replace("/", "", $cadenas);
		$cadenas	= $this->cleanString($cadenas, array("/DESCONOCIDO_MIGRADO/","/EMPLEADO_MIGRADO/", "/empleado_migrado/", "/NA/", "/POR_REGISTRAR/", "/DESCONOCIDO/"));
		if($cadenas == "" AND $PorDefecto != ""){ $cadenas	= $PorDefecto;	}
		return $cadenas;
	}		
}

class cDocumentos {
	private $mNombreArchivo	= "";
	private $mTipo			= "";
	private $mEsImagen		= false;
	private $mEsDocto		= false;
	private $mExt			= "";
	private $mCnnFTP		= null;
	private $mPersona		= false;
	private $mMessages		= "";
	
	function __construct($nombre = ""){ $this->mNombreArchivo = $nombre; $this->getTipo();	}
	function getTipo($documento = false){
		$documento			= ($documento == false) ? $this->mNombreArchivo : $documento;
		$ext		= strtoupper(substr($documento, -3));
		
		switch ($ext){
			case "PNG":
				$this->mEsImagen	= true;
				break;
			case "JPG":
				$this->mEsImagen	= true;
				break;
			case "PDF":
				$this->mEsDocto		= true;
				break;
		}
		$this->mExt		= strtolower($ext);
		return $ext;
	}
	function isImagen(){ return $this->mEsImagen; }
	function isDocto(){ return $this->mEsDocto; }
	function getNombreArchivo(){ return $this->mNombreArchivo; }
	function getEmbed($documento = false, $persona = false){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$documento			= ($documento == false) ? $this->mNombreArchivo : $documento;
		$mfile				= $this->FTPGetFile($documento, $persona);
		
		
		
		$d64				= base64_encode($mfile);
		//<img alt="Embedded Image" src="data:image/png;base64,
		$rs					= "";
		$ext				= $this->getTipo($documento);
		if($ext == "PDF"){
			$rs				= "<embed src=\"data:application/pdf;base64,$d64\" width=\"600\" height=\"500\" alt=\"pdf\" pluginspage=\"http://www.adobe.com/products/acrobat/readstep2.html\" />";
		} else {
			$ext			= strtolower($ext);
			$rs				= "<embed src=\"data:image/$ext;base64,$d64\" width=\"600\" height=\"500\" alt=\"photo\" />";
		}
		return $rs;
	}
	function FTPGetFile($documento = false, $persona = false){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$documento			= ($documento == false) ? $this->mNombreArchivo : $documento;
		
		if($persona != false){ ftp_chdir($this->mCnnFTP, $persona);	}
		$mark				= ($persona == false) ? "" : "$persona-";
		$ruta_completa		= PATH_TMP . "/$mark" . $documento;
		//TODO: 01/01/2015 Modificar 2014Nov19 mejorar en cache.- validar mejoras
		if(is_file($ruta_completa)){
			
		} else {
			$flocal 			= fopen( $ruta_completa, 'w');
			if (ftp_fget($this->mCnnFTP, $flocal, $documento, FTP_BINARY, 0)) {
				//setLog( "Se ha escrito satisfactoriamente sobre $flocal");
			} else {
				setLog( "Ha habido un problema durante la descarga de $documento en $flocal");
			}
		}
		$data 				= file_get_contents($ruta_completa);
		return $data;
	}
	function FTPConnect(){
		$conn_id 		= ftp_connect(SYS_FTP_SERVER);
		// iniciar sesión con nombre de usuario y contraseña
		$login_result 	= ftp_login($conn_id, SYS_FTP_USER, SYS_FTP_PWD);
		$this->mCnnFTP	= $conn_id;
		return $conn_id;		
	}
	function FTPListFiles(){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		//Obtener los archivos contenidos en el directorio actual
		$contents 		= ftp_nlist($this->mCnnFTP, ".");
		return $contents;		
	}
	function FTPMakeDir($nombre){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		ftp_mkdir ( $this->mCnnFTP , $nombre );
	}
	function FTPMove($documento = false, $persona = false){
		$ready				= true;
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$documento			= ($documento == false) ? $this->mNombreArchivo : $documento;
		$this->mPersona		= $persona;
		if(!ftp_chdir($this->mCnnFTP, $persona)){
			$this->FTPMakeDir($persona);
			ftp_chdir($this->mCnnFTP, $persona);
		}
		if(!ftp_rename($this->mCnnFTP, "../$documento", "./$documento")){
			$ready			= false;
		}
		return $ready;
	}
	function FTPUpload($documento){
		$sucess			= true;
		$completePath	= "";
		if( is_array($documento) ){
			//Obtener Extension
			$DExt 	= explode(".", substr($documento['name'], -6));
			$mExt	= (isset($DExt[1])) ? $DExt[1] : "";
		
			if( ($mExt == "pdf") OR ($mExt == "png") OR ($mExt == "jpg")){
				$this->mNombreArchivo		= $documento['name'];
				$completePath	= PATH_TMP . $documento['name'];
				if(file_exists($completePath)==true){
					unlink($completePath);
					$this->mMessages	.= "WARN\tSE ELIMINO EL ARCHIVO " . $this->mNombreArchivo . "\r\n";
				}
				if(move_uploaded_file($documento['tmp_name'], $completePath )) {
					$this->mMessages	.= "OK\tSE GUARDO EXITOSAMENTE EL ARCHIVO " . $this->mNombreArchivo . "\r\n";
				} else {
					$this->mMessages	.= "ERROR\tSE FALLO AL GUARDAR (" . $this->mNombreArchivo . ")\r\n";
					$sucess				= false;
				}
			}	else {
				$this->mMessages		.= "ERROR\tEL TIPO DE ARCHIVO DE " .$this->mNombreArchivo . "(" .$mExt . ") NO SE ACEPTA\r\n";
				$sucess					= false;
			}
		} else {
			$this->mMessages		.= "ERROR\tEL ARCHIVO NO ES VALIDO " . $this->mNombreArchivo . "\r\n";
			$sucess					= false;
		}
		if($sucess == true){
			if($this->mCnnFTP == null){ $this->FTPConnect(); }
			if (ftp_put($this->mCnnFTP, $this->mNombreArchivo, $completePath, FTP_BINARY)) {
			} else {
				$this->mMessages	.= "ERROR\tNo se pudo enviar el archivo " . $this->mNombreArchivo . "\r\n";
				$sucess				= false;
			}
		}	
		return $sucess;
	}
	function add($tipo, $pagina, $observaciones, $contrato = false, $persona = false, $documento = false, $fecha = false){
		
		$xF			= new cFecha();
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		$documento	= ($documento	== false) ?$this->mNombreArchivo : $documento;
		$persona	= ($persona == false) ? $this->mPersona : $persona;
		$contrato	= ($contrato == false) ? DEFAULT_CREDITO : $contrato;
		$fecha		= $xF->getInt($fecha);
		$user		= getUsuarioActual();
		$suc		= getSucursal();
		$ent		= EACP_CLAVE;
		$sql 		= "INSERT INTO personas_documentacion(
			clave_de_persona, tipo_de_documento, fecha_de_carga, observaciones, archivo_de_documento, valor_de_comprobacion, 
			estado_en_sistema, fecha_de_verificacion, oficial_que_verifico, 
			resultado_de_la_verificacion, notas, version_de_documento, numero_de_pagina, usuario, sucursal, entidad, documento_relacionado)
		VALUES($persona, $tipo, $fecha, '$observaciones', '$documento', '',
		 1, 0, 0, 0, '', '', $pagina, $user, '$suc', '$ent', $contrato)";
			$rs		= my_query($sql);
		if($rs[SYS_ESTADO] == false){
			$this->mMessages		.= "ERROR\t \r\n";
		} else {
			$this->mMessages		.= "OK\t \r\n";
		}
		return $rs[SYS_ESTADO];
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put);	}
}


class cSistemaEquivalencias {
	private $mTabla	= "";
	private $mEquiv	= array();
	public $PLD_OPERACIONES		= "PLD.operaciones";
	
	function __construct($tabla = ""){
		$this->mTabla	= $tabla;
		if($tabla != ""){ $this->init(); }
	}
	function init($clasificacion = ""){
		$cls	= ($clasificacion == "") ? "" : " AND (`sistema_equivalencias`.`clasificacion` ='$clasificacion') ";
		$ql		= new MQL();
		$sql	= "SELECT * FROM `sistema_equivalencias` WHERE (`sistema_equivalencias`.`tabla` ='" .  $this->mTabla . "') $cls";
		$rs		= $ql->getDataRecord($sql);
		foreach ($rs as $row){
			$this->mEquiv[ strtolower($row["original"])]	= strtolower($row["equivalencia"]);
		}
	}
	function get($valor){
		$valor	= strtolower($valor);
		$equiv	= (isset($this->mEquiv[$valor])) ? $this->mEquiv[$valor] : null;
		
		return $equiv;
	}
	
}

//================================================================ JSON

if (!defined('JSON_UNESCAPED_SLASHES'))
	define('JSON_UNESCAPED_SLASHES', 64);
if (!defined('JSON_PRETTY_PRINT'))
	define('JSON_PRETTY_PRINT', 128);
if (!defined('JSON_UNESCAPED_UNICODE'))
	define('JSON_UNESCAPED_UNICODE', 256);

function _json_encode($data, $options = 448)
{
	if (version_compare(PHP_VERSION, '5.4', '>='))
	{
		return json_encode($data, $options);
	}

	return _json_format(json_encode($data), $options);
}

function _pretty_print_json($json)
{
	return _json_format($json, JSON_PRETTY_PRINT);
}

function _json_format($json, $options = 448)
{
	$prettyPrint = (bool) ($options & JSON_PRETTY_PRINT);
	$unescapeUnicode = (bool) ($options & JSON_UNESCAPED_UNICODE);
	$unescapeSlashes = (bool) ($options & JSON_UNESCAPED_SLASHES);

	if (!$prettyPrint && !$unescapeUnicode && !$unescapeSlashes)
	{
		return $json;
	}

	$result = '';
	$pos = 0;
	$strLen = strlen($json);
	$indentStr = ' ';
	$newLine = "\n";
	$outOfQuotes = true;
	$buffer = '';
	$noescape = true;

	for ($i = 0; $i < $strLen; $i++)
	{
	// Grab the next character in the string
		$char = substr($json, $i, 1);

		// Are we inside a quoted string?
		if ('"' === $char && $noescape)
		{
		$outOfQuotes = !$outOfQuotes;
	}

		if (!$outOfQuotes)
		{
		$buffer .= $char;
		$noescape = '\\' === $char ? !$noescape : true;
			continue;
		}
			elseif ('' !== $buffer)
			{
					if ($unescapeSlashes)
					{
					$buffer = str_replace('\\/', '/', $buffer);
	}

					if ($unescapeUnicode && function_exists('mb_convert_encoding'))
						{
							// http://stackoverflow.com/questions/2934563/how-to-decode-unicode-escape-sequences-like-u00ed-to-proper-utf-8-encoded-cha
									$buffer = preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
								function ($match)
								{
								return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
							}, $buffer);
							}

            $result .= $buffer . $char;
            $buffer = '';
            continue;
            }
            	elseif(false !== strpos(" \t\r\n", $char))
            	{
            	continue;
            }

            if (':' === $char)
            {
            // Add a space after the : character
            $char .= ' ';
	}
	elseif (('}' === $char || ']' === $char))
	{
	$pos--;
	$prevChar = substr($json, $i - 1, 1);

	if ('{' !== $prevChar && '[' !== $prevChar)
									{
									// If this character is the end of an element,
									// output a new line and indent the next line
										$result .= $newLine;
										for ($j = 0; $j < $pos; $j++)
										{
										$result .= $indentStr;
										}
										}
										else
										{
										// Collapse empty {} and []
										$result = rtrim($result) . "\n\n" . $indentStr;
										}
										}

										$result .= $char;

										// If the last character was the beginning of an element,
										// output a new line and indent the next line
										if (',' === $char || '{' === $char || '[' === $char)
											{
											$result .= $newLine;

											if ('{' === $char || '[' === $char)
											{
											$pos++;
	}

	for ($j = 0; $j < $pos; $j++)
	{
	$result .= $indentStr;
	}
	}
	}
	// If buffer not empty after formating we have an unclosed quote
	if (strlen($buffer) > 0)
	{
	//json is incorrectly formatted
	$result = false;
								}

								return $result;
								}
								
?>
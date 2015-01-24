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
  
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.captacion.inc.php");
include_once("../core/core.riesgo.inc.php");
include_once("../core/core.seguimiento.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.utils.inc.php");
include_once("../core/core.operaciones.inc.php");
include_once("../core/core.db.inc.php");
include_once("../core/core.lang.inc.php");
include_once("../core/core.sys.inc.php");

    ini_set("display_errors", "off");
    ini_set("max_execution_time", 900);
    
    $key		 	= (isset($_GET["k"]) ) ? true : false;
    $parser			= (!isset($_GET["s"]) ) ? false : $_GET["s"];
	
    //Obtiene la llave del
//if ($key == MY_KEY) {
	$messages		= "";
	$fechaop		= parametro("f", fechasys());
	//2011-01-26 ; manejar fechas
	$xF				= new cFecha(0, $fechaop);
	$xSuc			= new cSucursal();
	$ql				= new MQL();
	$xLi			= new cSQLListas();
	$xSuc->init();
	
	$aliasFil		= getSucursal() . "-eventos-al-cierre-de-sistema-del-dia-$fechaop";

	$xLog			= new cFileLog($aliasFil);

	$idrecibo		= DEFAULT_RECIBO;

	$xRec			= new cReciboDeOperacion(12);
	$xRec->setGenerarPoliza();
	$xRec->setForceUpdateSaldos();
	$idrecibo		=  $xRec->setNuevoRecibo(DEFAULT_SOCIO,DEFAULT_CREDITO,$fechaop, 1, 12, "CIERRE_DE_SISTEMA_$fechaop", "NA", "ninguno", "NA", DEFAULT_GRUPO);
	$xRec->setNumeroDeRecibo($idrecibo);
	//======================= cancelar todas las cajas a 0
	$sqlCa			= $xLi->getListadoDeCajasConUsuario(TESORERIA_CAJA_ABIERTA);
	$rs				= $ql->getDataRecord($sqlCa);
	foreach ($rs as $rw){
		$xCaja		= new cCaja();
		$xCaja->init($rw["codigo"]);
		if($xCaja->setActualizaFondosCobrados() > TOLERANCIA_SALDOS){
			$messages	.= "ERROR\t Caja No cerrada por tener fondos pendientes \r\n";
		} else {
			$xCaja->setCloseBox(getUsuarioActual(), 0);
		}
		
		$messages	.= $xCaja->getMessages(OUT_TXT);
	}
	
	//Verificar lo Valores por defecto
	if ( $xSuc->existeSocio(DEFAULT_SOCIO) == false ){
		$cajaLocalR		= $xSuc->getCajaLocalResidente();
		$xSoc			= new cSocio(DEFAULT_SOCIO);
		$xSoc->add("", "PUBLICO_GENERAL", "","POR_REGISTRAR","POR_REGISTRAR", $cajaLocalR, false, "DESCONOCIDO",
			99, 99, 99, 99, 99, 1, DEFAULT_GRUPO, "", 1, "0", DEFAULT_SOCIO, getSucursal());
		$messages		.= $xSoc->getMessages(OUT_TXT);
	}	
	if ( $xSuc->existeCredito(DEFAULT_CREDITO) == false ){
		$xCred		= new cCredito(DEFAULT_CREDITO, DEFAULT_SOCIO);

		$xCred->add(DEFAULT_TIPO_CONVENIO, DEFAULT_SOCIO, DEFAULT_CUENTA_CORRIENTE, 0, CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO, 
					1, 1, CREDITO_DEFAULT_DESTINO, DEFAULT_CREDITO, 
					DEFAULT_GRUPO, "CREDITO POR DEFECTO");
		$messages	.= $xCred->getMessages(OUT_TXT);
	}
	if ( $xSuc->existeCuenta(DEFAULT_CUENTA_CORRIENTE) == false ) {
		$xCta		= new cCuentaALaVista(DEFAULT_CUENTA_CORRIENTE);
		$xCta->setNuevaCuenta("99", "99", DEFAULT_SOCIO);
		$messages	.= $xCta->getMessages(OUT_TXT);
	}
	$ql				= new MQL();
	
	/*
	 * ====================================================================================================================================================
	 * Agregar codigo de actualizar menores a personas fisicas
	 * ====================================================================================================================================================
	 */
	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		" . EACP_NAME . " \r\n";
	$messages 		.= "=========================		" . getSucursal() . " \r\n";
	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		INICIANDO EL CIERRE DE SISTEMA     ====================\r\n";
	$messages 		.= "=========================		RECIBO: $idrecibo				   ====================\r\n";
//2011-01-26 : Agrega un recibo estadistico de control diario
	$xNRec		= new cReciboDeOperacion(10);
	$diaSig		= $xF->setSumarDias(1);
	$xIdNRec	= $xNRec->setNuevoRecibo(DEFAULT_SOCIO,DEFAULT_CREDITO, $diaSig, 1, 10, "MOVIMIENTOS_ESTADISTICOS_DEL_DIA", "NA", "ninguno", "NA", DEFAULT_GRUPO);
	//actualiza la configuracion del sistema
	$xCx	    = new cConfiguration();
	$xUtil		= new cUtileriasParaOperaciones();
	$xCx->set("numero_de_recibo_por_defecto", $xIdNRec);
	$messages		.= date("Y-m-d") . "\tSe Agrego el Recibo $xIdNRec  para ESTADISTICOS del proximo dia( $diaSig )\n";
	//=================================
	$messages 		.= $xUtil->setEliminarRecibosDuplicados();
	
	$x20			= setFoliosAlMaximo();
	$messages		.= date("Y-m-d") . "\tSe llevaron Folios al Maximo, los recibos quedaron en " . $x20["recibos"] . "  \n";

	$messages		.= date("Y-m-d") . "\tSe llevaron la Cuenta de Polizas Contables al Maximo \n";
					setSociosAlMaximo();
					clearCacheSessions();
	$messages		.= date("Y-m-d") . "\tSe limpio la Cache de sessiones\n";
	$messages		.= date("Y-m-d") . "\tSe llevaron los socios al Maximo \n";
	
	if (  $xF->getDiaFinal()  == $xF->get()  ){
		$messages	.= CongelarSaldos($idrecibo);
	} else {
		$messages	.= date("Y-m-d") . "\tNO SE CONGELAN SALDOS, NO ES FIN DE MES\r\n";
	}
	
	/**
	 * Actualiza Recibos de Operaciones
	 **/
	$sqlS = "UPDATE operaciones_recibos SET tipo_pago = \"ninguno\" WHERE tipo_pago = \"\" OR IsNULL(tipo_pago) ";
	my_query($sqlS);
	
	/**
	 * Actualiza los Periodos a Formatos Validos en el Caso que el sistema les haya asigando otro valor
	 */
	$sqlPeriodosCorrectos = "UPDATE operaciones_mvtos
								SET periodo_mensual = DATE_FORMAT(fecha_afectacion, '%c'),
								periodo_anual = DATE_FORMAT(fecha_afectacion, '%Y'),
								periodo_semanal = DATE_FORMAT(fecha_afectacion, '%w')
								";
	$x1 	= my_query($sqlPeriodosCorrectos);
	$messages	.= $x1[SYS_INFO];
	
	$xSys	= new cSystemTask();
	$xDB	= new cSAFEData();

	//crear backup //
	if (  $xF->getDiaFinal()  == $xF->get() OR date("N", $xF->getInt()) == 5  ){
		$messages	.= "BACKUP\tRespaldo a la fecha " .$xF->getFechaDDMM() . "\r\n";
		$xDB->setCheckDatabase();
		$messages	.= $xSys->setBackupDB_WithMail();
	}

	//cerrar el log
	$xLog->setWrite($messages);
	$xLog->setClose();
	if(ENVIAR_MAIL_LOGS == true){ $xLog->setSendToMail("TR.Eventos del Cierre del Sistema"); }
	//Stored procedures
	$ql->setRawQuery("CALL `proc_listado_de_ingresos` ");
	//$ql->setRawQuery("CALL `proc_historial_de_pagos` ");
	$ql->setRawQuery("CALL `sp_clonar_actividades` ");
	$ql->setRawQuery("CALL `proc_perfil_egresos_por_persona` ");
	$ql->setRawQuery("CALL `proc_creditos_letras_pendientes` ");
	//
	//Limpiar el Cache
//$xSys->setPowerOff();
//apagar el sistema
	$xCache			= new cCache(); $xCache->clean();
	
	if ( $parser != false ){
		$log		= $aliasFil;
		$xPage		= new cHPage("TR.Cierre del Dia", HP_FORM);
		$xBtn		= new cHButton("iact");
		$oFRM		= new cHForm("frmSubmit", "");
		$oFRM->setElementByLine(2);
		$oFRM->setTitle($xPage->getTitle() . " " . $xF->getFechaCorta() );
		echo $xPage->getHeader();
		echo $xPage->setBodyinit();
		
		
		$oFRM->addHTML( "<a href=\"../utils/download.php?type=txt&download=$log&file=$log\" target=\"_blank\" class='button'>Descargar Archivo de EVENTOS DEL SISTEMA</a><br /><br />");
		
		$log		= getSucursal() . "-eventos-al-cierre-de-colocacion-del-dia-$fechaop";
		$oFRM->addHTML("<a href=\"../utils/download.php?type=txt&download=$log&file=$log\" target=\"_blank\" class='button'>Descargar Archivo de EVENTOS de CIERRE DE COLOCACION</a><br /><br />");
		
		if(MODULO_SEGUIMIENTO_ACTIVADO == true){
			$log		= getSucursal() . "-eventos-al-cierre-de-seguimiento-del-dia-$fechaop";
			$oFRM->addHTML("<a href=\"../utils/download.php?type=txt&download=$log&file=$log\" target=\"_blank\" class='button'>Descargar Archivo de EVENTOS de CIERRE DE SEGUIMIENTO</a><br /><br />");
		}
		if(MODULO_CONTABILIDAD_ACTIVADO == true){
			$log		= getSucursal() . "-eventos-al-cierre-de-contabilidad-del-dia-$fechaop";
			$oFRM->addHTML("<a href=\"../utils/download.php?type=txt&download=$log&file=$log\" target=\"_blank\" class='button'>Descargar Archivo de EVENTOS de CIERRE DE CONTABILIDAD</a><br /><br />");
		}

		if(MODULO_CAPTACION_ACTIVADO == true){
			$log		= getSucursal() . "-eventos-al-cierre-de-captacion-del-dia-$fechaop";
			$oFRM->addHTML("<a href=\"../utils/download.php?type=txt&download=$log&file=$log\" target=\"_blank\" class='button'>Descargar Archivo de EVENTOS de CIERRE DE CAPTACION</a><br /><br />");
		}
		if(MODULO_AML_ACTIVADO == true){
			$log		= getSucursal() . "-eventos-al-cierre-de-riesgos-del-dia-$fechaop";
			$oFRM->addHTML("<a href=\"../utils/download.php?type=txt&download=$log&file=$log\" target=\"_blank\" class='button'>Descargar Archivo de EVENTOS de CIERRE DE RIESGOS</a><br /><br />");
		}
		//Inicio
		$oFRM->addFootElement( $xBtn->getRegresar() . $xBtn->getSalir()  );
		
		echo $oFRM->get();
		
		$xPage->fin();
  }

//}

?>

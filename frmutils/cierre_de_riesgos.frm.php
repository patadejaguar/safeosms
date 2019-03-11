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
include_once("../core/core.operaciones.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.db.inc.php");

include_once("../core/core.contable.inc.php");
include_once("../core/core.contable.utils.inc.php");

ini_set("display_errors", "off");
ini_set("max_execution_time", 900);
    
$key			= parametro("k", true, MQL_BOOL);
$parser			= parametro("s", false, MQL_BOOL);

$messages		= "";
$fechaop		= parametro("f", false, MQL_DATE);
$xF				= new cFecha(0, $fechaop);
$fechaop		= $xF->getFechaISO($fechaop);

$xCierre		= new cCierreDelDia($fechaop);
$EsCerrado		= $xCierre->checkCierre($fechaop);
$forzar			= parametro("forzar", false, MQL_BOOL);

$xRuls			= new cReglaDeNegocio();
$venceDocs		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_VENCEN_DOCTOS );

$next			= "./cierre_de_sistema.frm.php?s=true&k=" . $key . "&f=$fechaop";
$next			= ($forzar == true) ? $next . "&forzar=true" : $next;
if($EsCerrado == true AND $forzar == false){
	setAgregarEvento_("Cierre De Riesgos Existente", 5);
	if ($parser == true){
		header("Location: $next");
	}
} else {
	getEnCierre(true);
	/**
	 * Generar el Archivo HTMl del LOG
	 * eventos-del-cierre + fecha_de_cierre + .html
	 *
	 */
	$aliasFil	= getSucursal() . "-eventos-al-cierre-de-riesgos-del-dia-$fechaop";
	$xLog		= new cFileLog($aliasFil);
	$idrecibo	= DEFAULT_RECIBO;
	$xL			= new cSQLListas();
	$xRuls		= new cReglaDeNegocio();
	
	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		" . EACP_NAME . " \r\n";
	$messages 		.= "=========================		" . getSucursal() . " \r\n";
	$messages 		.= "=======================================================================================\r\n";
	$messages 		.= "=========================		INICIANDO EL CIERRE DE RIESGOS ===================\r\n";
	$messages 		.= "=========================		RECIBO: $idrecibo				   ====================\r\n";
	$messages 		.= "=======================================================================================\r\n";
	
	if (MODULO_AML_ACTIVADO == true){
		$NoValRisk 	= $xRuls->getValorPorRegla($xRuls->reglas()->AML_CIERRE_NV_RIESGO);		//regla de negocio
		$xQL		= new MQL();
		//crear arbol de relaciones
		$xUtils	= new cPersonasUtilerias();
		$xUtils->setCrearArbolRelaciones();
		//Actualizar Nivel de Riesgo
		$xUAml		= new cUtileriasParaAML();
		if($NoValRisk == false){
			$messages	.= $xUAml->setActualizarNivelDeRiesgo(true);
		}
		//Validar perfiles transaccionales
		//Validar Documentos
		//TODO: Agregar cierre de riesgos
		//=========== Vencer Documentos
		if($venceDocs == true){
			$xQL->setRawQuery("UPDATE `personas_documentacion`,`personas_documentacion_tipos`
				SET `personas_documentacion`.`estatus`=0,`personas_documentacion`.`estado_en_sistema`=0 , `personas_documentacion`.`vencimiento`= NOW()
				WHERE `personas_documentacion`.`estatus`=1 AND `personas_documentacion`.`tipo_de_documento`=`personas_documentacion_tipos`.`clave_de_control`
				AND getFechaByInt((`personas_documentacion`.`fecha_de_carga`+(`personas_documentacion_tipos`.`vigencia_dias`*84600)) )<= NOW()");
		}
		//checar documentos de todos los socios
		/*$OSoc		= new cSocios_general();
		$rs			= $OSoc->query()->select()->exec();
		foreach ($rs as $data){
			$OSoc->setData($data);
			$xAml	= new cAMLPersonas($OSoc->codigo()->v());
			$xAml->init();
			//$xAml->setForceAlerts();
			$xAml->setVerificarDocumentosCompletos($fechaop);
			$xAml->setVerificarDocumentosVencidos($fechaop);
			$messages		.= $xAml->getMessages(OUT_TXT);	
			//envio de informes
			//TODO: Agregar envio de informes
			//checar perfil transaccional mensual
		}*/
		//verificar operaciones de 6 meses excedidas de maximo permitido
		
		/*$sql2 = "SELECT
		`operaciones_recibos`.`fecha_operacion`              AS `fecha`,
		`operaciones_recibos`.`numero_socio`                 AS `persona`,
		COUNT(`operaciones_recibos`.`idoperaciones_recibos`) AS `operaciones`,
		SUM(`operaciones_recibos`.`total_operacion`)         AS `monto`
		FROM
		`operaciones_recibos` `operaciones_recibos`
		WHERE
		(`operaciones_recibos`.`fecha_operacion` = '$fechaop')
		GROUP BY
		`operaciones_recibos`.`numero_socio`";
		$rs1			= $mql->getDataRecord($sql2);
		foreach($rs1 as $rw1){
			$xAml			= new cAMLPersonas($rw1["persona"]);
			$xF				= new cFecha();
			$fecha_inicial	= $xF->setRestarMeses(6, $fechaop);
			$obj			= $xAml->getOAcumuladoDeOperaciones($fecha_inicial, $fechaop);
			
		}*/
		//Relaciones Recursivas
		$rs1				= null;
		//$xUtils				= new cAMLUtils();
		$xCML				= new cAML();
		
	} else {
		$messages		.= "=========================\tNO ACTIVADO\t====================\r\n";
	}

$xLog->setWrite($messages);
$xLog->setClose();
	if(ENVIAR_MAIL_LOGS == true){ $xLog->setSendToMail("TR.Eventos del Cierre de Riesgos"); }
	if ($parser == true){
		header("Location: $next");
	}
	getEnCierre(false);
//}
}


?>

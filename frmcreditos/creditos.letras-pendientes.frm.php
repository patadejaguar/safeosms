<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();

function jsaGetLetras($idcredito, $idfecha){
	$xCred		= new cCredito($idcredito); $xCred->init();
	//$xPlas	= $xCred->getPlanDePago();
	$xF			= new cFecha();
	$idfecha	= $xF->getFechaISO($idfecha);
	$xQL		= new MQL();
	//$xQL->setRawQuery("SET @fecha_de_corte:='$idfecha';");
	my_query("SET @fecha_de_corte:='$idfecha';");
	$sql		= "SELECT
	`letras`.`socio_afectado` AS `persona`,
	`letras`.`docto_afectado` AS `credito`,
	`letras`.`periodo_socio`  AS `parcialidad`,
	`letras`.`fecha_de_pago`,

	`letras`.`capital`,
	`letras`.`interes`,
	`letras`.`iva`,
	`letras`.`ahorro`,
	`letras`.`otros`,
	`letras`.`letra`,	
	
	(`creditos_solicitud`.`tasa_moratorio`*100) AS `tasa_de_mora`,
	(`creditos_solicitud`.`tasa_interes`*100)   AS `tasa_de_interes` ,
	DATEDIFF(getFechaDeCorte(), fecha_de_pago) AS 'dias',
	 ((letras.capital * DATEDIFF(getFechaDeCorte(), fecha_de_pago) * (`creditos_solicitud`.`tasa_moratorio` + `creditos_solicitud`.`tasa_interes`))/getDivisorDeInteres()) AS 'mora'
	FROM
		`creditos_solicitud` `creditos_solicitud` 
			INNER JOIN `letras` `letras` 
			ON `creditos_solicitud`.`numero_solicitud` = `letras`.`docto_afectado`
	
	 WHERE capital >0 AND docto_afectado=$idcredito AND fecha_de_pago <= getFechaDeCorte()";
	$xT		= new cTabla($sql);
	$xT->setFootSum(array(
		4 => "capital", 5 => "interes", 6 => "iva", 7 => "ahorro",
			8 => "otros", 9 => "letra", 13 => "mora"
	));
	return $xT->Show(); 
}

$jxc ->exportFunction('jsaGetLetras', array('idcredito', 'idfechadecalculo'), "#idlistado");
$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "./");

$msg		= "";
$xFRM->OButton("TR.Obtener", "jsaGetLetras()", $xFRM->ic()->CARGAR);
$xFRM->OHidden("idcredito", $credito);
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
$xFRM->ODate("idfechadecalculo", false, "TR.Fecha de Calculo");
$xFRM->addHElem("<div id='idlistado'></div>");
//$xFRM->addSubmit();
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
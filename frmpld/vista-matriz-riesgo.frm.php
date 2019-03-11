<?php
//ini_set("display_errors","1");
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
$xHP		= new cHPage("TR.VISTAMATRIZ", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->init();

$xFRM		= new cHForm("frmvistariesgosmatriz", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xTag		= new cHNotif();
$xFRM->setFieldsetClass("");

$xFRM->addCerrar();

$rsx1		= $xQL->getDataRecord("SELECT * FROM `riesgos_consecuencias` ORDER BY  `rms_eq` DESC ");
$itmImp		= $xQL->getNumberOfRows();
$rsy1		= $xQL->getDataRecord("SELECT * FROM `riesgos_probabilidad` ");
$itmProb	= $xQL->getNumberOfRows();


$xRI			= new cRiesgosImpacto(0);
$maxImpacto		= (100 / $xRI->getMaximoRMS());
$xRP			= new cRiesgosProbabilidad(0);
$maxProb		= (100 / $xRP->getMaximoRMS());


$xForm			= new cFormula();
$formula		= $xForm->getFormula($xForm->AML_RMS_RIESGO);

$xHT			= new cHTabla("idtriesgosreporte");
$itmImp2		= $itmImp + 2;
$itmProb2		= $itmProb + 1;

$xHT->addRaw("<tr><td rowspan='$itmImp2' class='title alert-whiteblue'>Impacto</td></tr>");

foreach ($rsx1 as $x0){
	$idx		= $x0["idriesgos_consecuencias"];
	$xT1		= new cRiesgosImpacto($idx);
	$idimpacto	= $idx;//$xT1->getClave();
	
	if($xT1->init() == true){
		$nombreImp	= $xT1->getNombre();
		
		$xHT->initRow();
		
		
		$xHT->addTD($xT1->getNombre(), " class='alert-gwhite' ");
		$xHT->addTD(round($xT1->getRiesgoRMS(),1), " class='mny' ");
		
		foreach ($rsy1 as $y0){
			$idy	= $y0["idriesgos_probabilidad"];
			$xT2	= new cRiesgosProbabilidad($idy);
			if($xT2->init() == true){
				$idprobabilidad	= $xT2->getClave();
				$nombreprob		= $xT2->getNombre();
				
				$sql		= "SELECT   `aml_risk_catalog`.`clave_de_control` AS `clave`,
         `aml_risk_catalog`.`descripcion` AS `nombre`,
         `aml_risk_types`.`nombre_del_riesgo` AS `tipo`,
         COUNT( `aml_alerts`.`clave_de_control` )  AS `eventos`,
         AVG( `aml_alerts`.`riesgo_calificado` )  AS `riesgo`,
         `riesgos_probabilidad`.`nombre_probabilidad` AS `probabilidad`,
         `riesgos_consecuencias`.`nombre_consecuencia` AS `impacto`
FROM     `aml_alerts`
INNER JOIN `aml_risk_catalog`  ON `aml_alerts`.`tipo_de_aviso` = `aml_risk_catalog`.`clave_de_control`
INNER JOIN `aml_risk_types`  ON `aml_risk_types`.`clave_de_control` = `aml_risk_catalog`.`tipo_de_riesgo`
INNER JOIN `riesgos_consecuencias`  ON `riesgos_consecuencias`.`idriesgos_consecuencias` = `aml_risk_catalog`.`impacto_id`
INNER JOIN `riesgos_probabilidad`  ON `riesgos_probabilidad`.`idriesgos_probabilidad` = `aml_risk_catalog`.`probabilidad_id`
WHERE `aml_alerts`.`estado_en_sistema`=1 AND
         `aml_risk_catalog`.`impacto_id`  = $idimpacto AND
         `aml_risk_catalog`.`probabilidad_id` = $idprobabilidad
GROUP BY tipo_de_aviso ";
				$txt			= "";
				$rsP			= $xQL->getDataRecord($sql);
				$color			= "inherit";
				$xLU			= new cHUl("idul-$idimpacto-$idprobabilidad");

				$RIESGO			= 1;
				$PROBABILIDAD	= $xT2->getRiesgoRMS() * $maxProb;
				$IMPACTO		= $xT1->getRiesgoRMS() * $maxImpacto;
				$RIESGO			= ($PROBABILIDAD + $IMPACTO)/2;
				
				//$RIESGO			= round( ($RIESGO  * 10) );
				
				//setError("$RIESGO ($itmImp ++  $itmProb)  $PROBABILIDAD + $IMPACTO");
				//setError("$nombreprob / $nombreImp $RIESGO ($PROBABILIDAD + $IMPACTO) ");
				//eval( $formula );
				
				$xNR		= new cAMLRiesgosNiveles();
				if($xNR->initByRiesgo($RIESGO) == true){
					$color	= $xNR->getColor();
				}
				
				$alto			= 4;
				$sumEventos		= 0;
				foreach ($rsP as $rw){
					//$xTag->getTag($txt)
					$nombre		= $rw["nombre"];
					$eventos	= $rw["eventos"];
					$riesgo		= $rw["riesgo"];
					$clave		= $rw["clave"];
					$sumEventos	+= $eventos;
					
					//$xTag->setNoClose();
					//$txt		.= $xTag->getTag("$nombre($eventos)");
					$alto		= $alto + 2;
					//$txt		.= "<div class='alert-box form_tag' style='background-color:$color'>$nombre($eventos)</div>";
					$xLU->li("$nombre ($eventos)", " onclick=\"var xG=new Gen();xG.w({url:'../frmpld/vista_de_alertas.frm.php?tipo=$clave'});\" ");
				}
				if($alto <= 8){
					$alto		= 8;
				}
				$cnt			= $xTag->getNoticon($sumEventos, "", "fa-hdd-o", "fa-2x") . $xLU->get();
				if($sumEventos<=0){
					$color		= "inherit";
					$cnt		= "";
				}
				$xHT->addTD($cnt, " style='height:" . $alto . "em;width:32em;background-color:$color;border-width:2px;border-style:solid;' ");
			}
		}
		$xHT->endRow();
	}
}
$xHT->initRow();

$xHT->addTD("Probabilidad", " colspan='2' class='title alert-whiteblue' ");

foreach ($rsy1 as $y0){
	$idy	= $y0["idriesgos_probabilidad"];
	$xT2	= new cRiesgosProbabilidad($idy);
	
	if($xT2->init() == true){
		$xHT->addTD($xT2->getNombre(), " class='alert-whiteblue' ");
	}
}
$xHT->endRow();



$xFRM->addHElem( $xHT->get() );

echo $xFRM->get();
?>
<script>

</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
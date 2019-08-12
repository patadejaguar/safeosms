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
$xDic		= new cHDicccionarioDeTablas();
$xNot		= new cHNotif();

//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frmcredsproyeccion", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xTabla		= new cHTabla();
$xTxt		= new cHText();
$xTxt->setDivClass("");
$xTxt->addEvent("jsCalcular()", "onchange");


?> <style> input, #cuota7, #cuota15, #cuota30 { font-size : 1.3em !important; font-weight: bold; } </style> <?php


if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	$xCred	= new cCredito($credito);

	$xTabla->addTH("TR.PERIOCIDAD");
	$xTabla->addTH("TR.MONTO DEL CREDITO");
	$xTabla->addTH("TR.NUMERO DE PAGOS");
	$xTabla->addTH("TR.MONTO DE LA PARCIALIDAD");
	
	$numpago1	= 0;
	$numpago2	= 0;
	$numpago3	= 0;
	
	$xFRM->addCerrar();
	
	if($xCred->init() == true){
		
		$monto1		= $xCred->getMontoSolicitado();
		$monto2		= $xCred->getMontoSolicitado();
		$monto3		= $xCred->getMontoSolicitado();
		$periodos	= $xCred->getPagosSolicitados();
		$tasaiva	= ($xCred->getTasaDeInteres()+($xCred->getTasaDeInteres()*$xCred->getTasaIVA()))*100;
		
		$xFRM->OHidden("tasaconiva", $tasaiva);
		
		switch ($xCred->getPeriocidadDePago()){
			case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
				$numpago1	= $periodos;
				$numpago2	= ceil($periodos/2);
				$numpago3	= ceil($periodos/4);
				
				break;
			case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
				
				$numpago1	= $periodos*2;
				$numpago2	= $periodos;
				$numpago3	= ceil($periodos*2);
				break;
			default:
				$numpago1	= $periodos * 4;
				$numpago2	= $periodos * 2;
				$numpago3	= $periodos;
				//setError();
				break;
		}
		
		
		//Semanal
		$xTabla->initRow();
		$xTabla->addTD("SEMANAL", " class='title key' ");
		
		$xTabla->addTD($xTxt->getDeMoneda2("monto7", "", $monto1));
		
		$xTabla->addTD($xTxt->getDeConteo("pagos7", "", $numpago1));
		
		$xTabla->addTD($xNot->get("", "cuota7", $xNot->NOTICE));
		$xTabla->endRow();
		//Quicenal
		$xTabla->initRow();
		$xTabla->addTD("QUINCENAL", " class='title key' ");
		
		$xTabla->addTD($xTxt->getDeMoneda2("monto15", "", $monto2));
		$xTabla->addTD($xTxt->getDeConteo("pagos15", "", $numpago2));
		$xTabla->addTD($xNot->get("", "cuota15", $xNot->NOTICE));
		
		
		$xTabla->endRow();
		//Mensual
		$xTabla->initRow();
		$xTabla->addTD("MENSUAL", " class='title key' ");
		
		$xTabla->addTD($xTxt->getDeMoneda2("monto30", "", $monto3));
		$xTabla->addTD($xTxt->getDeConteo("pagos30", "", $numpago3));
		$xTabla->addTD($xNot->get("", "cuota30", $xNot->NOTICE));
		
		$xTabla->endRow();
		
		$xFRM->addDisabledInit("monto7");
		$xFRM->addDisabledInit("monto15");
		$xFRM->addDisabledInit("monto30");
	}
	
	$xFRM->addHElem($xTabla->get());
	$xFRM->addJsInit("jsCalcular();");
}



echo $xFRM->get();
?>
<script>
var xG	= new Gen();
var xC	= new CredGen();

function jsCalcular(){
	xG.aMonedaForm();
	
	var tasa	= $("#tasaconiva").val();
	var cap7	= $("#monto7").val();
	var cap15	= $("#monto15").val();
	var cap30	= $("#monto30").val();

	var pagos7	= $("#pagos7").val();
	var pagos15	= $("#pagos15").val();
	var pagos30	= $("#pagos30").val();
	
	//semanal
	var v7		= xC.getCuotaDePago({capital: cap7, tasa: tasa, frecuencia: 7, pagos: pagos7});
	$("#cuota7").html(getFMoney(v7));
	//Quincenal
	var v15		= xC.getCuotaDePago({capital: cap15, tasa: tasa, frecuencia: 15, pagos: pagos15});
	$("#cuota15").html(getFMoney(v15));
	//mensual
	var v30		= xC.getCuotaDePago({capital: cap30, tasa: tasa, frecuencia: 30, pagos: pagos30});
	$("#cuota30").html(getFMoney(v30));

}

</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
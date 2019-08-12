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
$xHP		= new cHPage("TR.Riesgo de Credito", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); 
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->addChartSupport();

//$xHP->addCSS("../css/material/bootstrap.min.css");
//$xHP->addCSS("../css/material/material-dashboard.css");
//$xHP->addCSS("../css/material/demo-documentation.css");


$xHP->init();

$xFRM		= new cHForm("frmriesgocreds", "./");
$xFRM->setTitle($xHP->getTitle());


$xSoc			= new cSocio($persona);
if($xSoc->init() == true){
	$sql		= $xLi->getListadoDeRelacionesInversa($persona);
	$xT			= new cTabla($sql);
	$xRiesgo	= new cPersonasRiesgosDeCredito($persona);
	$D1			= $xRiesgo->getDatosDeScoringCredito1();
	$xTiles		= new cHNotif();

	if($D1[SYS_ESTADO] == true){
		$remRec		= setNoMenorQueCero((100 - $D1["recuperacion"]));
		$remPunt	= setNoMenorQueCero((100 - $D1["puntualidad"]));
		$remAtra	= setNoMenorQueCero((100 - $D1["atraso"]));
		
		$xGr1		= new cChart("idrecuperachart");
		$xGr1->setTipo($xGr1->PIE);
		$xGr1->addData($D1["recuperacion"], "TR.Pagado");
		$xGr1->addData($remRec, "TR.Pendiente");
		$xGr1->setAutoWith(); $xGr1->setOnPercent();
		$xFRM->addHElem( $xTiles->getDash("TR.RECUPERACION", $xGr1->getDiv(), "fa-info", "error" ) );
		$xFRM->addJsInit( $xGr1->getJs() );
		
		
		$xGr1		= new cChart("idpuntualidadchart");
		$xGr1->setTipo($xGr1->PIE);
		$xGr1->addData($D1["puntualidad"], "TR.Puntual");
		$xGr1->addData($remPunt, "TR.Inpuntual");
		$xGr1->setAutoWith(); $xGr1->setOnPercent();
		$xFRM->addHElem( $xTiles->getDash("TR.PUNTUALIDAD", $xGr1->getDiv(), "fa-info", "warning" ) );
		$xFRM->addJsInit( $xGr1->getJs() );
		
		
		$xGr1		= new cChart("idatrasochart");
		$xGr1->setTipo($xGr1->PIE);
		$xGr1->addData($D1["atraso"], "TR.Retraso");
		$xGr1->addData($remAtra, "TR.Remanente");
		$xGr1->setAutoWith(); $xGr1->setOnPercent();
		$xFRM->addHElem( $xTiles->getDash("TR.Retrasos en Pago", $xGr1->getDiv() ) );
		$xFRM->addJsInit( $xGr1->getJs() );
	}
	
	$xRiesgo->getBalancePatrimonial();
	$D2		= $xRiesgo->getDataInArray();
	
	$xGr1		= new cChart("idbpatrimchart");
	$xGr1->setTipo($xGr1->PIE);
	$sum	= $D2["activo"] + $D2["pasivo"] + $D2["capital"];
	if($sum >0){
		$dx1	= ($D2["activo"]/$sum) * 100;
		$dx2	= ($D2["pasivo"]/$sum) * 100;
		$dx3	= ($D2["capital"]/$sum) * 100;
		
		$xGr1->addData($dx1, "TR.Activo");
		$xGr1->addData($dx2, "TR.Pasivo");
		$xGr1->addData($dx3, "TR.Capital");
		
		$xGr1->setAutoWith(); $xGr1->setOnPercent();
		
		$xFRM->addHElem( $xTiles->getDash("TR.Patrimonio", $xGr1->getDiv() ) );
		$xFRM->addJsInit( $xGr1->getJs() );
	}
	/*			$datos["activo"]		= 0;
			$datos["pasivo"]		= 0;
			$datos["capital"]		= 0;
			$datos["fecha"]			= fechasys();
			$datos["articulos"]		= 0;
			$datos[SYS_MONTO]		= 0;*/
	
	$xFRM->addHTML($xT->Show( PERSONAS_TITULO_PARTES ));
	$xFRM->addHTMl($xRiesgo->getEndeudamientoPorAvalesOtorgados(true));
	$xFRM->addHTMl($xRiesgo->getEndeudamientoPorFamilia(true));
	//$xRiesgo->getBalancePatrimonial(true)$xFRM->addHTMl($xRiesgo->getBalancePatrimonial(true));
	
}
$xFRM->addCerrar();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
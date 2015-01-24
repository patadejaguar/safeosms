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
$xHP				= new cHPage("TR.Cierre del dia");

$eacp				= EACP_CLAVE;
$observaciones 		= parametro("idobservaciones");
$fecha				= parametro("idfecha-0", false, MQL_RAW);	// fecha de Trabajo

$xF					= new cFecha();
$fecha				= $xF->getFechaISO($fecha);
$action 			= parametro("action", SYS_NINGUNO);
$msg				= "";
$xCierre			= new cCierreDelDia($fecha);

setFoliosAlMaximo();

$msg		= "EL CIERRE SE EFECTUARA UNA VEZ POR DIA, SI EL CIERRE YA ESTA HECHO NO SE ADMITIRAN MAS OPERACIONES.\r\nTENGA CUIDADO. EL EQUIPO SE APAGARA DESPUES DEL CIERRE.\r\nEL PROCESO TARDARA UNOS MINUTOS\r\n";
$msg		.= "FECHA DE CIERRE $fecha .LA FECHA DE INICIO EN EL SISTEMA ES " . FECHA_INICIO_OPERACIONES_SISTEMA. "\r\n";

$jxc = new TinyAjax();
function jsaGetListadoCierres($fecha){
	$xLi		= new cSQLListas();	
	$xF			= new cFecha();
	$fecha		= $xF->getFechaISO($fecha);
	
	$sqlList	= $xLi->getListadoDeRecibos(12, "", "", $fecha);
	$xTab		= new cTabla($sqlList);
	$xTab->OButton("TR.Reporte", "var xR = new RecGen(); xR.reporte(" . HP_REPLACE_ID . ")", $xTab->ODicIcons()->REPORTE);
	$xTab->OButton("TR.Panel", "var xR = new RecGen(); xR.panel(" . HP_REPLACE_ID . ")", $xTab->ODicIcons()->CONTROL);
	$xBtn		= new cHButton();
	$xSel		= new cHSelect();
	$xCA		= $xSel->getListaDeCajasAbiertas();
	$T2			= new cTabla($xCA->getSQL() . " AND `tesoreria_cajas`.`fecha_inicio` = '$fecha' ");
	$T2->addEspTool($xBtn->getBasic("", "jsToCerrarCorte('$fecha')", "bloquear", "idcerrar", true));
	
	
	$html		= $xTab->Show("TR.LISTADO DE CIERRES");
	if(MODULO_CAJA_ACTIVADO == true){ 
		$html 		.= $T2->Show("TR.Cajas Abiertas");
		$itemsAbier	= $T2->getRowCount();
		$html		.= "<input type='hidden' id='idabiertas' value='$itemsAbier' />"; 
	} else {
		$html		.= "<input type='hidden' id='idabiertas' value='0' />";
	}
	
	return  $html; 	

}
$jxc ->exportFunction('jsaGetListadoCierres', array('idfecha-0'), "#listados");
$jxc ->process();


if($action == SYS_UNO){
	if($xCierre->checkCierre($fecha) == true ){
		$msg	.= $xCierre->getMessages();
	} else {
		$status		= $xCierre->check5Cierres($fecha, true);
		if($status[SYS_ESTADO] == false AND MODO_DEBUG == false){
			$msg	.= $xCierre->getMessages();
		} else {
			header("Location: ../frmutils/cierre_de_colocacion.frm.php?k=" . MY_KEY . "&s=true&f=" . $fecha);
			exit;
		}
	}
}



	$xHP->init('jsaGetListadoCierres()');
	
	$xFRM		= new cHForm("frmcierre", "frmcierredeldia.php?action=1");
	$xBtn		= new cHButton();
	$xTxt		= new cHText();
	$xDate		= new cHDate();
	$xSel		= new cHSelect();	
	
	$xFRM->addSubmit("TR.Cerrar Dia", "jsChecarAbiertas()");
	$xFRM->OButton("TR.Salir", "var xG = new Gen(); xG.salir();", "salir");
	$xFRM->addJsBasico();
	

	$xDate->addEvents(" onchange='jsGetListaDeCierres()' ");
	$xFRM->addHElem( $xDate->get("TR.Fecha de corte", $fecha) );
	
	$xFRM->addHElem( $xTxt->getNumero("periodo_actual", EACP_PER_SOLICITUDES, "TR.Periodo Actual de Creditos") );
	
	$xFRM->addObservaciones();
	$xFRM->addHTML("<div id='listados'></div>");
	
	$xFRM->addAviso($msg);

	echo $xFRM->get();
?>
<script>
	function jsToCerrarCorte(f){ var xT = new TesGen(); xT.goCerrarCaja(f);  }
	function jsGetListaDeCierres() {   jsaGetListadoCierres();  }
	
	function jsChecarAbiertas(){
		var itms	= $("#idabiertas").val();
		if(entero(itms) > 0){
			alert("EXISTEN CAJAS ABIERTAS!!");
		} else {	
			frmcierre.submit();
		}
	}
</script>
<?php
$jxc ->drawJavaScript(false, true); 

$xHP->fin();
?>
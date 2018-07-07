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
$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
function jsaActualizarEmpresa($idempresa){
	$xEmp	= new cEmpresas($idempresa);
	$xEmp->init();
	$xEmp->setActualizarPorPersona();
	return $xEmp->getMessages(OUT_HTML);
}
function jsaAddDescuentoDesdeEmpresa($idpersona, $descuento){
	$xSoc		= new cSocio($idpersona);
	if($xSoc->init() == true){
		$xSoc->setMontoAhorroPreferente($descuento);
	}
	return $xSoc->getMessages();
}

$jxc ->exportFunction('jsaAddDescuentoDesdeEmpresa', array('idmodificado', 'idcantidad'), "#idavisos");
$jxc ->exportFunction('jsaActualizarEmpresa', array('idempresa' ), "#idavisos");
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->process();
$empresa	= parametro("id", 0, MQL_INT); $empresa		= parametro("clave", $empresa, MQL_INT);
$empresa	= parametro("empresa", $empresa, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);


$xHP->init();

$xFRM		= new cHForm("frmempresaspanel", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xEmp		= new cEmpresas($empresa);
if($xEmp->init() == true){

	$xFRM->addHElem( $xEmp->getFicha() );
	
	$xT2		= new cHTabs("idcomoempresa");
	
	$xFRM->addEmpresaComandos($empresa);
	
	$xTCreds	= new cTabla($xLi->getListadoDeCreditos(false, false, false, false, " AND (`creditos_solicitud`.`persona_asociada` = $empresa) ", false), 2 );
	$xTPers		= new cTabla($xLi->getListadoDeSocios(" (`socios_general`.`dependencia` = $empresa)  ") );
	$xTAhorro	= new cTabla($xLi->getListadoDeIncidenciasAhorro($empresa));
	//========================== Tabla de periodos para empresas
	$xTPeriodo	= new cTabla($xLi->getListadoDePeriodoPorEmpresa($empresa,false, false, false, false, false, false, true) );
	$xTPeriodo->setTdClassByType();
	$xTPeriodo->OButton("TR.Panel", "var xG = new EmpGen(); xG.getTablaDeCobranza(" . HP_REPLACE_ID . ")", $xFRM->ic()->CONTROL);
	$xTPeriodo->addEditar(USUARIO_TIPO_CAJERO);
	$xTPeriodo->setColTitle("periodo", "PERIODOEMPRESA" );
	$xTPeriodo->setOmitidos("periocidad");
	$xTPeriodo->setUsarNullPorCero();
	
	$xTCreds->setTdClassByType(); $xTPers->setTdClassByType(); $xTAhorro->setTdClassByType();
	$xTPers->setWidthTool("200px");
	if(MODULO_CAPTACION_ACTIVADO == true){
		$xModAhorro	= "\$xS=new cSocio(_REPLACE_ID_,true);PHP::\"<input value='\" . \$xS->getAhorroPreferente() . \"' type='number' id='id_REPLACE_ID_' onchange='jsModificarAhorro(this,_REPLACE_ID_)' />\";";
		$xTPers->addEspTool($xModAhorro);
	}
	if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
		//=================== Envios
		
		$xTPerE	= new cTabla($xLi->getListadoDePeriodoPorEmpresaMix($empresa,1, true) );
		
		$xTPerE->setTdClassByType();
		$xTPerE->OButton("TR.Panel", "var xE = new EmpGen(); xE.getTablaDeCobranza(" . HP_REPLACE_ID . ")", $xFRM->ic()->CONTROL);
		$xTPerE->addEditar(USUARIO_TIPO_CAJERO);
		$xTPerE->setOmitidos("periocidad");
		$xTPerE->setOmitidos("fecha_de_cobro");
		$xTPerE->setColTitle("periodo", "PERIODOEMPRESA" );
		$xTPerE->setOmitidos("unid");
		//$xTPerE->addSubQuery($xLi->getListadoDePeriodoPorEmpresaMix($empresa,-1), "unid", "Pago en {{fecha_de_operacion}} por {{saldo}} : {{observaciones}}");
		
		$xTPerE->setUsarNullPorCero();
		
		$xHTPer	= $xTPerE->Show();
		$xT2->addTab("TR.ENVIOS", $xHTPer);
		
		//=================== Cobros
		
		$xTPerP	= new cTabla($xLi->getListadoDePeriodoPorEmpresaMix($empresa,-1) );
		
		$xTPerP->setTdClassByType();
		$xTPerP->OButton("TR.Panel", "var xG = new EmpGen(); xG.getTablaDeCobranza(" . HP_REPLACE_ID . ")", $xFRM->ic()->CONTROL);
		$xTPerP->addEditar(USUARIO_TIPO_CAJERO);
		$xTPerP->setOmitidos("periocidad");
		$xTPerP->setOmitidos("saldo");
		$xTPerP->setOmitidos("unid");
		
		$xTPerP->setOmitidos("fecha_de_cobro");
		$xTPerP->setColTitle("periodo", "PERIODOEMPRESA" );
		
		$xTPerP->setUsarNullPorCero();
		
		$xHPPer	= $xTPerP->Show();
		
		$xT2->addTab("TR.PAGOS", $xHPPer);
		

		$xT2->addTab("TR.Periodos de Empresa", $xTPeriodo->Show());
		
		
		
		$xT2->addTab("TR.Empleados", $xTPers->Show());
		
		$xTCreds->setFootSum(array(8 => "saldo"));
		$xT2->addTab("TR.Creditos por empresa", $xTCreds->Show());
		if(MODULO_CAPTACION_ACTIVADO == true){
			//Ahorro por Empresa
			$xT2->addTab("TR.Ahorro por empresa", $xTAhorro->Show());
		}
		
	}
	//$xHTabs->addTab("TR.empresa $empresa", $xT2->get() ); //tab4
	
	if(PERSONAS_CONTROLAR_POR_APORTS == true){
		//== reporte de pagos pendientes por comisiones
		$xT		= new cTabla($xLi->getListadoDePresupuestoPorPagar($xEmp->getClaveDePersona()));
		$xT->setFootSum(array(
				10 => "monto_de_cheque"
		));
		$xT2->addTab("TR.Comisiones Pendientes", $xT->Show());
	}
	
	$xFRM->addHElem( $xT2->get() );
	
	if(MODULO_CAPTACION_ACTIVADO == true){
		$xFRM->OButton("TR.Cedula de Ahorro", "jsGetCedulaDeAhorro()", $xFRM->ic()->AHORRO);
		$xFRM->OButton("TR.Orden de Ahorro", "jsGetEmpresaCaptacion()", $xFRM->ic()->TAREA);
		$xFRM->OButton("TR.Excel de Ahorro", "jsCedulaAhorroExcel()", $xFRM->ic()->EXCEL);
	}
	$xFRM->OButton("TR.Actualizar Empresa", "jsaActualizarEmpresa()", $xFRM->ic()->EJECUTAR);
	$xFRM->OHidden("idempresa", $empresa);
	$xFRM->OHidden("idsocio", $xEmp->getClaveDePersona()); $xFRM->OHidden("idmodificado", ""); $xFRM->OHidden("idcantidad", "0");
	
	$xFRM->OButton("TR.AGREGAR EMPLEADO", "jsAddEmpleado()", $xFRM->ic()->PERSONA, "idaddnewempleado", "persona");

}
$xFRM->addAviso("", "idavisos");
echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var EmpG	= new EmpGen();
var xP		= new PersGen();

function jsCancelarAccion(){	$(window).qtip("hide");    }
function jsAddDescuento(){ getModalTip(window, $("#iddivdescuento"), xG.lang(["actualizar", "descuento"]));	}
function jsListaDeNominas(idnomina){ var EmpG	= new EmpGen(); EmpG.getOrdenDeCobranza(idnomina);	}
function jsCedulaAhorroExcel(){ 
	var idemp	= $("#idempresa").val();
	var xrl		= "../frmempresas/layout-cedula.frm.php?empresa=" + idemp;
	xG.w({ url: xrl, tiny : true }); 	
}
function jsAddDescuento(){ getModalTip(window, $("#iddivdescuento"), xG.lang(["actualizar", "descuento"]));	}
function jsGuardarDescuento(){	jsaAddDescuento();	setTimeout("jsCancelarAccion()", 2000);	}
function jsGetCedulaDeAhorro(){
	
	var idemp	= $("#idempresa").val();
	EmpG.getCedulaAhorro(idemp);
}
function jsGetEmpresaCaptacion(){
	var EmpG	= new EmpGen();
	var idemp	= $("#idempresa").val();
	EmpG.getTablaDeCaptacion(idemp);
}
function jsModificarAhorro(evt, idpersona){
	if(flotante(evt.value) >= 0 ){
		$("#idmodificado").val(idpersona);
		$("#idcantidad").val(flotante(evt.value));
		var siguarda	= confirm("DESEA GUARDAR EL DESCUENTO PREFERENTE POR " + evt.value);
		if(siguarda){ jsaAddDescuentoDesdeEmpresa();	}
	}
}
function jsAddEmpleado(){
	var idemp	= entero($("#idempresa").val());
	xP.getFormaBusqueda({args : "&empresa=" + idemp, next : "addempresa"});	
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
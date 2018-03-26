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
$xHP		= new cHPage("TR.PANEL DE ALERTAS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
function jsaCompletar($id){
	$xAlert	= new cAMLAlertas($id);
	$msg	= "";
	if($xAlert->init() == true){
		$persona	= $xAlert->getPersonaDeOrigen();
		//$xSoc		= new cSocio($persona);
		//if($xSoc->init() == true){
		$xCon	= new cAMLListasProveedores();
		$xCon->setNoGuardar();
		
		$xCon->getConsultaInterna("", "", "", $persona);
		$msg	= $xCon->getMessages();
		$xAlert->setActMensajesDelSistema($msg);
		//}
	}
	return $msg;
}
$jxc ->exportFunction('jsaCompletar', array('idalerta'), "#idmsgs");
$jxc ->process();

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

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xAlert		= new cAMLAlertas($clave);
if($xAlert->init() == true){
	$persona		= $xAlert->getPersonaDeOrigen();
	
	$xFRM->OHidden("persona", $xAlert->getPersonaDeOrigen() );
	$xFRM->OHidden("personadestino", $xAlert->getPersonDeDestino() );
	
	$xFRM->OButton("TR.PANEL DE PERSONA", "var xP=new PersGen();xP.goToPanel(" . $xAlert->getPersonaDeOrigen() . ")", $xFRM->ic()->PERSONA, "cmdpanelpersona", "persona");
	
	$xFRM->OButton("TR.MODIFICAR ESTATUS", "jsModificarEstatus($clave)", $xFRM->ic()->EDITAR, "cmdeditaralerta", "editar");
	
	if($xAlert->getEsEnviadoRMS() == false){
		$xFRM->OButton("TR.ENVIAR A RMS", "jsEnviarRMS($clave)", $xFRM->ic()->EXPORTAR, "cmdenviarrms", "yellow");
	} else {
	    $xFRM->addAviso($xFRM->getT("MS.ALERTA_ENVIADO_RMS"), "idmsgs", false, "warning");
	}

	$xFRM->addHElem($xAlert->getFicha());
	
	//901001
	if($xAlert->getTipoDeAlerta() == 901001){
		$xT		= new cTabla($xLi->getListadoDePersonasConsultasL($persona));
		$xFRM->addHElem( $xT->Show() );
		$xFRM->OButton("TR.COMPLETAR", "jsCompletar($clave)", $xFRM->ic()->LLENAR);
		
		
		//$xLista		= new cPersonasConsultaEnListas();
		//$xLi->getListadoDeCompromisosSimple()
		//$xLista->
		//$xListaC	= new cAMLListasProveedores();
	}
	
}

$xFRM->OHidden("idalerta", $clave);


echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var xG		= new Gen();

function jsCompletar(){
	jsaCompletar();
}
function jsModificarEstatus(id){
	xG.w({ url : "estatus_de_alerta.frm.php?codigo=" +id , w: 800, h: 800, tiny : true });
}

function jsEnviarRMS(id){
	xG.svc({url:"send-to-rms.svc.php?clave=" + id, callback: jsResultEnviarRMS});
}
function jsResultEnviarRMS(obj){
	var id = $("#idalerta").val();
	xG.alerta({msg: obj.message });
	xG.go({url: "../frmpld/alertas-panel.frm.php?clave=" + id });
}
</script>
<?php
$xHP->fin();
?>
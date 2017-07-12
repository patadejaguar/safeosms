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
$xHP		= new cHPage("TR.AGREGAR COLONIA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
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
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();


/* ===========		FORMULARIO		============*/
$clave		= parametro("idgeneral_colonia", null, MQL_INT);
$xTabla		= new cGeneral_colonias();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave		= parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
$xLoc		= new cLocal();

if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->idgeneral_colonia($clave);
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmcolonias", "colonias.frm.php?action=$step");
$xFRM->setTitle($xHP->getTitle());

if($step == MQL_MOD){ $xFRM->addGuardar(); } else { $xFRM->addSubmit(); }
$clave 		= parametro($xTabla->getKey(), null, MQL_INT);

if( ($action == MQL_ADD OR $action == MQL_MOD) AND ($clave != null) ){
	$xTabla->setData( $xTabla->query()->initByID($clave));
	$xTabla->setData($_REQUEST);	

	if($action == MQL_ADD){
		$xCol	= new cDomiciliosColonias();
		$xCol->add($xTabla->nombre_colonia()->v(), $xTabla->codigo_postal()->v(), $xTabla->tipo_colonia()->v(), $xTabla->ciudad_colonia()->v(), $xTabla->codigo_de_estado()->v(), $xTabla->codigo_de_municipio()->v() );
	} else {
		$xTabla->query()->update()->save($clave);
	}
	$xFRM->addAvisoRegistroOK();
	//
	$xQL->setRawQuery("CALL `proc_colonias_activas`()");
} else {
	$SelTCol	= $xSel->getListadoGenerico("SELECT `tipo_colonia`, `tipo_colonia` AS `tipocolonia` FROM `general_colonias` GROUP BY `tipo_colonia`", "tipo_colonia");
	$SelTCol->setNoMayus();
	$SelTCol->setOptionSelect($xTabla->tipo_colonia()->v());
	$xFRM->addHElem($SelTCol->get(true));
	//$xFRM->OText("tipo_colonia", $xTabla->tipo_colonia()->v(), "TR.TIPO COLONIA");
		
	
	$xFRM->OMoneda("codigo_postal", $xTabla->codigo_postal()->v(), "TR.CODIGO POSTAL");
	$xFRM->OText("nombre_colonia", $xTabla->nombre_colonia()->v(), "TR.NOMBRE COLONIA");
	
	

	
	$xFRM->OText("ciudad_colonia", $xTabla->ciudad_colonia()->v(), "TR.CIUDAD COLONIA");
	
	if($step == MQL_MOD){
		$xFRM->addHElem($xSel->getListaDeEntidadesFed("idestados", true, $xTabla->codigo_de_estado()->v())->get(true));
		$xFRM->addHElem($xSel->getListaDeMunicipios("idmunicipios", $xTabla->codigo_de_estado()->v(), $xTabla->codigo_de_municipio()->v())->get(true) );
		$xFRM->OHidden("municipio_colonia", $xTabla->municipio_colonia()->v());
		$xFRM->OHidden("estado_colonia", $xTabla->estado_colonia()->v());
		$xFRM->OHidden("sucursal", $xTabla->sucursal()->v());
		$xFRM->OHidden("fecha_de_revision", $xTabla->fecha_de_revision()->v());
		$xFRM->OHidden("codigo_de_estado", $xTabla->codigo_de_estado()->v());
		$xFRM->OHidden("codigo_de_municipio", $xTabla->codigo_de_municipio()->v());		
	} else {
		$xFRM->addHElem($xSel->getListaDeEntidadesFed("idestados", true, $xLoc->DomicilioEstadoClaveNum())->get(true));
		$xFRM->addHElem($xSel->getListaDeMunicipios("idmunicipios", $xLoc->DomicilioEstadoClaveNum(), $xLoc->DomicilioMunicipioClave())->get(true) );
		$xFRM->OHidden("municipio_colonia", $xLoc->DomicilioMunicipio());
		$xFRM->OHidden("estado_colonia", $xLoc->DomicilioEstado());
		$xFRM->OHidden("sucursal", getSucursal());
		$xFRM->OHidden("fecha_de_revision", fechasys());
		$xFRM->OHidden("codigo_de_estado", $xLoc->DomicilioEstadoClaveNum());
		$xFRM->OHidden("codigo_de_municipio", $xLoc->DomicilioMunicipioClave());		
	}
	
	$xFRM->setValidacion("idestados", "jsGetMunicipios");
	$xFRM->setValidacion("idmunicipios", "jsSetDatosMunicipio");
	
	
	$xFRM->OHidden("idgeneral_colonia", $xTabla->idgeneral_colonia()->v());
}
echo $xFRM->get();
//$("#yourdropdownid option:selected").text();
//$jxc ->drawJavaScript(false, true);
?>
<script>
var xGen	= new Gen();
var xVal	= new ValidGen();

function jsGetMunicipios(){
	var ide	= $("#idestados").val();
	xGen.DataList({url:"../svc/municipios.svc.php?e=" + ide, id:"idmunicipios",key:"clave_de_municipio",label:"nombre_del_municipio"});
	$("#codigo_de_estado").val($("#idestados").val());
	$("#estado_colonia").val($("#idestados option:selected").text());
	
	return true;
}
function jsSetDatosMunicipio(){
	$("#codigo_de_municipio").val($("#idmunicipios").val());
	$("#municipio_colonia").val($("#idmunicipios option:selected").text());
	return true;
}
</script>
<?php
$xHP->fin();
?>
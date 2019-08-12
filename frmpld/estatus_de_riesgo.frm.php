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
$xHP		= new cHPage("TR.estatus de riesgo", HP_FORM);
$xF			= new cFecha();
$xlistas	= new cSQLListas();
$jxc 		= new TinyAjax();


$clave 		= parametro("clave_de_riesgo", null, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xTabla		= new cAml_risk_register();
if( setNoMenorQueCero($clave) > 0){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);

$clave		= parametro("codigo", null, MQL_INT);
$codigo		= $clave;
$xSel		= new cHSelect();

if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}

$xFRM			= new cHForm("frmestatusriesgo", "estatus_de_riesgo.frm.php?action=$step&codigo=$codigo");
$xFRM->addSubmit();
$xFRM->setTitle($xHP->getTitle());

$xFRM->endSeccion();

if($action == MQL_ADD){		//Agregar
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);
		$xTabla->query()->insert()->save();
		$xFRM->addAvisoRegistroOK();
	}
} else if($action == MQL_MOD){		//Modificar
	//iniciar
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);
		$xTabla->query()->update()->save($clave);
		$xFRM->addAvisoRegistroOK();
	}
}


$xHP->init();

$msg		= "";
$xAlert		= new cAMLAlertas($codigo);

$xAlert->init();
$xFRM->addSeccion("iddiv", $xHP->getTitle());

$xFRM->OTextArea("razones_de_reporte", $xTabla->razones_de_reporte()->v(), "TR.AML_TEXTO_A");
$xFRM->OTextArea("acciones_tomadas", $xTabla->acciones_tomadas()->v(), "TR.AML_TEXTO_B");
$xFRM->OTextArea("notas_de_checking", $xTabla->notas_de_checking()->v(), "TR.Observaciones de la operacion");
$xFRM->OHidden("clave_de_riesgo", $xTabla->clave_de_riesgo()->v(),"");
$xFRM->OHidden("fecha_de_checking", $xF->getInt() ,"");
$xFRM->endSeccion();
$xFRM->addSeccion("iddivmsg", "TR.AVISO DEL SISTEMA");
$xFRM->addAviso( $xAlert->getDescripcion() );
$xFRM->endSeccion();
echo $xFRM->get();

?>
<script>
var xG		= new Gen();
//function jsDescartaRiesgo(){ xG.confirmar({ msg : "Desea Descartar la Alerta como Riesgo?", callback : "jsaDescartaRiesgo()", evaluador : jsRazonNoVacia(), alert : "La observacion no puede quedar vacia"}); }
//function jsConfirmaRiesgo(){ xG.confirmar({ msg : "Desea Confirmar la Alerta como Riesgo?", callback : "jsaConfirmaRiesgo()", evaluador : jsRazonNoVacia(), alert : "La observacion no puede quedar vacia" }); }
function jsRazonNoVacia(){
	var valid	= new ValidGen();
	xG.cleanText("#notas_de_checking");
	xG.cleanText("#razones_de_reporte");
	xG.cleanText("#acciones_tomadas");

	return valid.NoVacio( $("#notas_de_checking").val() );
}

//function jsSalir(){ xG.close(); }
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
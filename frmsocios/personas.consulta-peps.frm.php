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
$xHP		= new cHPage("TR.Consulta PEPS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
//$jxc 		= new TinyAjax();

//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM		= new cHForm("frmconsultapeps", "./personas.consulta-peps.frm.php?action=". MQL_ADD);
$xFRM->setTitle($xHP->getTitle());
//$xSoc		= new cSocio($persona);
//if( $xSoc->init()== true){
	$xAml	= new cAMLPersonas($persona);
	$xAml->init();
	if($action == MQL_ADD){
		$ln		= $xAml->getBuscarEnListaPEP();
		$xFRM->OButton("TR.Descargar Consulta", "jsGetConsulta()", $xFRM->ic()->DESCARGAR);
		$xFRM->addCerrar();
	} else {
		$xFRM->addEnviar("TR.NUEVA CONSULTA");
	}
	$xFRM->OHidden("persona", $persona);
	
	
	//$xFRM->addHElem("<a href=\"" . $xAml->getReporteConsultaListaNegra() . "\">CDescargar</a>" );
	
	$xFRM->addAviso( $xAml->getMessages() );
	
	
	$xT		= new cTabla($xLi->getListadoDePersonasConsultasL($persona, $xAml->TIPO_PEPS));
	$xT->OButton("TR.Dictaminar", "var xA=new AmlGen();xA.setDictamenConsulta(" . HP_REPLACE_ID. ")", $xFRM->ic()->VALIDAR);
	$xFRM->addHElem($xT->Show());
	$xFRM->endSeccion();
	
	
	
	
	
//}

$msg			= "";
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();
echo $xFRM->get();
?>
<script>
var xURL		= "<?php echo $xAml->getReporteConsultaListaNegra(); ?>";
var xG			= new Gen();
function jsGetConsulta(){
	xG.w({ url : xURL });
}

</script>
<?php
//$jxc ->drawJavaScript(false, true);
//$xHP->fin();
?>
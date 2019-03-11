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
$xHP		= new cHPage("TR.REGISTRO PERSONA_MORAL", HP_FORM);
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

$xFRM		= new cHForm("frmpersonasdjuridc", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();









//$xFRM->addJsBasico();
if($persona <= DEFAULT_SOCIO){
	$xFRM->addPersonaBasico();
	$xFRM->addSubmit();
} else {
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		
		$xFRM->OHidden("persona", $persona);
		
		$xOPM						= new cPersonasMoralesDatosExt();
		$xOPM->initByPersona($persona);
		
		$idnumerodocumento			= parametro("idnumerodocumento", $xSoc->getClaveDeIdentificacion() );
		$idfechanacimiento			= parametro("idfechanacimiento", $xSoc->getFechaDeNacimiento(), MQL_DATE);
		$idnotarioconst				= parametro("idnotarioconst", $xOPM->getNombreNotario());
		$idnotariaconst				= parametro("ididnotariaconst", $xOPM->getNumeroNotaria());
		$idfolioconst				= parametro("idfolioconst", $xOPM->getIDRegistroPublico());
		

		
		$idpodernotarial			= parametro("idpodernotarial", $xOPM->getPoderClave());
		$fechapoder					= parametro("idfechapoder", $xOPM->getPoderFecha());
		
		$idnotariopoder				= parametro("idnotariopoder", $xOPM->getPoderNotario());
		$idnotariapoder				= parametro("ididnotariapoder", $xOPM->getPoderNotaria());
		
		
		
		if($action == SYS_NINGUNO){
			$xFRM->addGuardar();
			
			$xFRM->addSeccion("idddacta", "TR.ACTACONSTITUTIVA");
			
			$xFRM->OText_13("idnumerodocumento",$idnumerodocumento, "TR.idescritura");
			
			$xFRM->setValidacion("idnumerodocumento", "validacion.novacio", "Necesita un Documento de Identificacion", true);
			
			$xFRM->ODate("idfechanacimiento", $idfechanacimiento,"TR.fecha constitucion");
			
			$xFRM->OText("idnotarioconst", $idnotarioconst, "TR.NOMBRE NOTARIO constitucion");
			$xFRM->OText_13("ididnotariaconst", $idnotariaconst, "TR.Numero NOTARIA Constitucion");
			$xFRM->OText_13("idfolioconst", $idfolioconst, "TR.IDREGISTROPUB");
			$xFRM->endSeccion();
			
			$xFRM->addSeccion("iddreplegal", "TR.PODERNOTARIAL");
			
			//$xFRM->setValidacion("idsocio2", "validacion.persona", 'Necesita Capturar un Numero de Representante Legal', true);
			//$xFRM->addPersonaBasico("2");
			//$xFRM->addHTML("<datalist id=\"dlBuscarPersona\" ><option /></datalist>");
			
			$xFRM->OText_13("idpodernotarial",$idpodernotarial, "TR.idpoder");
			$xFRM->ODate("idfechapoder", $fechapoder, "TR.FECHA PODERNOTARIAL");
			$xFRM->OText("idnotariopoder", $idnotariopoder, "TR.NOMBRE NOTARIO PODERNOTARIAL");
			$xFRM->OText_13("ididnotariapoder", $idnotariapoder, "TR.Numero NOTARIA");
			//$xFRM->OText("idfoliopoder", "", "TR.FOLIO");
			$xFRM->endSeccion();
			
			
			$xFRM->setAction("./personas.datos-pjuridicas.frm.php?action=" . MQL_ADD, true);
			
		} else {
			
			
			
			$res 	= $xSoc->setDatosPersonasMorales($idfolioconst, $idnumerodocumento, $idfechanacimiento, $idnotarioconst, $idnotariaconst, $idpodernotarial, $fechapoder, $idnotariopoder, $idnotariapoder);
			
			
			$xFRM->setResultado($res, "", "", true);
			
		}
	}
}

echo $xFRM->get();
?>
<script>
var xPer	= new PersGen();
var xG		= new Gen();
var idxpersona		= "<?php echo setNoMenorQueCero($persona); ?>";
function jsGuardarExtranjero(){
	xG.confirmar({msg:"CONFIRMA GUARDAR LOS DATOS_EXTRANJEROS", callback: jsSiGuardarExtranjero});
}


</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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
$xHP		= new cHPage("TR.FORMASIMPLE ALTADE PERSONA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$OtherEvent	= "";
$jxc = new TinyAjax();

function jsaSetSocioEnSession($socio){	getPersonaEnSession($socio); }
$jxc ->exportFunction('jsaSetSocioEnSession', array("idsocio"));
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->process();
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

$nombre			= parametro("idnombre");
$primerapellido	= parametro("idprimerapellido");
$segundoapellido	= parametro("idsegundoapellido");
$control		= parametro("control", "idsocio", MQL_RAW);

$xHP->init();

$xFRM		= new cHForm("frmpersonaregistrosimp", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

//$xFRM->addJsBasico();
if($action == SYS_NINGUNO){
	$xFRM->setAction("personas.registro-simple.frm.php?control=$control&action=".MQL_ADD);
	$xFRM->OText("idnombre", "", "TR.NOMBRE_COMPLETO");
	$xFRM->OText("idprimerapellido", "", "TR.APELLIDO_PATERNO");
	$xFRM->OText("idsegundoapellido", "", "TR.APELLIDO_MATERNO");
	$xFRM->addGuardar();
} else {
	$xSoc	= new cSocio(false);
	$res	= $xSoc->add($nombre, $primerapellido, $segundoapellido);
	
	if($res == true){
		$xSoc->setEsCliente();
		$idpersona	= $xSoc->getClaveDePersona();
		$xFRM->addAvisoRegistroOK("PERSONA REGISTRADA CON EL ID $idpersona");
		$xFRM->addCerrar("", 2);
		$xFRM->addJsInit("setSocio($idpersona);");
		$xFRM->OHidden("idsocio", $idpersona);
	} else {
		$xFRM->addAvisoRegistroError("NO SE REGISTRO LA PERSONA");
		$xFRM->addAtras();
		$xFRM->addCerrar();
	}
}


echo $xFRM->get();
$jxc ->drawJavaScript(false, true);

?>
<script>
var mFecha			= "<?php echo fechasys(); ?>";
var xG				= new Gen();
var idsoc			= "<?php echo $control; ?>";
function setSocio(id){
	var msrc	= null;	
	$("#idsocio").val(id);
	jsaSetSocioEnSession();
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
	if(msrc == null){} else {
	<?php
	if($OtherEvent != ""){
		echo "if(msrc.$OtherEvent != \"undefined\"){ msrc.$OtherEvent(id); }";
	} else {
	?>		
		if(msrc.getElementById(idsoc)){
			oid			=  msrc.getElementById(idsoc);
			oid.value	= id;
			oid.focus();
			oid.select();
			session(ID_PERSONA, id);
			if(typeof msrc.jsSetNombreSocio != "undefined"){ msrc.jsSetNombreSocio(); }
			xG.close();		
		}
	<?php
	}

		/*if($tiny == true) {
			echo "
			window.parent.document.$f.idsocio.value 	= id;
			window.parent.document.$f.idsocio.focus();
			window.parent.document.$f.idsocio.select();
			if(typeof window.parent.jsSetNombreSocio != \"undefined\"){
				window.parent.jsSetNombreSocio();
			}
			$OtherEvent
			window.parent.TINY.box.hide();
			";
		} else if( $f !== false ){
			echo "
			opener.document.$f.idsocio.value 	= id;
			opener.document.$f.idsocio.focus();
			opener.document.$f.idsocio.select();
			if(typeof opener.jsSetNombreSocio != \"undefined\"){
				opener.jsSetNombreSocio();
			}
			$OtherEvent
			window.close();
			";
		}*/
	?>
	}
}
<!--

//-->
</script>
<?php
$xHP->fin();
?>
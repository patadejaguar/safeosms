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

$DDATA		= $_REQUEST;
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


echo $xHP->getHeader();

echo $xHP->setBodyinit();

$xFRM		= new cHForm("frmpugarentidades", "purgar-entidadesfederativas.frm.php?action=next");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();
$msg		= "";

$xFRM->setTitle("TR.purgar de entidadades federativas");
$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
$mql		= new MQL();
//Obtener lista de estados
$sql		= "SELECT * FROM `general_estados` ";
$data		= $mql->getDataRecord($sql);
$cEs		= new cGeneral_estados();
foreach($data as $rows){
	$cEs->setData($rows);
	if($action == SYS_NINGUNO){
		$xFRM->addHTML( $xChk->get($cEs->nombre()->v(), "estado" . $cEs->clave_numerica()->v() ) );
	} else {
		$eliminar		= parametro("estado" . $cEs->clave_numerica()->v(), false, MQL_BOOL);
		$estado			= $cEs->clave_numerica()->v();
		if($eliminar == true){
			my_query("DELETE FROM `general_colonias` WHERE `codigo_de_estado` = $estado ");
			my_query("DELETE FROM `general_municipios` WHERE `clave_de_entidad` = $estado ");
			//my_query("DELETE FROM `catalogos_localidades` WHERE `clave_de_estado` = $estado ");
		}
	}
}
$xFRM->addSubmit();

echo $xFRM->get();

echo $xHP->setBodyEnd();

//$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
    function jsEnd(){
		<?php if($tiny == true){ echo "window.parent.TINY.box.hide();"; }	?>
		if (typeof opener != "undefined") { window.close(); }
    }
</script>
<?php
$xHP->end();
?>
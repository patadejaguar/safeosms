<?php
/**
 * Alta a Tipos de Operacion
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
$xHP		= new cHPage("TR.TIPO_DE OPERACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$idnombre	= parametro("idnombre");
$idalias	= parametro("idalias");
$idclonarde	= parametro("idclonarde", 0, MQL_INT);
$idoegresos	= parametro("idotrosegresos", false, MQL_BOOL);
$idoingresos= parametro("idotrosingresos", false, MQL_BOOL);
$precio		= parametro("idprecio", 0, MQL_FLOAT);

$xHP->init();


$xFRM		= new cHForm("frmoperacionestipos", "./");
$xSel		= new cHSelect();

$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();
if($action == SYS_NINGUNO AND $clave <= 0){
	$xOp	= new cOperaciones_tipos();
	$clave	= $xOp->query()->getLastID();
	$xFRM->addSeccion("idnew", "TR.AGREGAR OPERACION");
	$xFRM->addGuardar();
	$xFRM->setAction("operaciones_tipos.frm.php?action=" . MQL_ADD);	
	$xFRM->OMoneda("id", $clave, "TR.CLAVE");
	$xFRM->OText("idnombre", "", "TR.Nombre");
	$xFRM->OText("idalias", "", "TR.NOMBRE_CORTO");
	$xFRM->OMoneda("idprecio", 0, "TR.PRECIO");
	
	$xFRM->endSeccion();
	//$xFRM->addSeccion("iddclon", "TR.CLONAR DE");
	//$xFRM->endSeccion();
	$xFRM->addSeccion("idopts", "TR.OPCIONES");
	$xSClon	= $xSel->getListaDeTiposDeOperacion("idclonarde");
	$xSClon->addEspOption("0", "NO CLONAR");
	
	$xFRM->addHElem($xSClon->get("TR.CLONAR DE",true));
	$xFRM->OCheck("TR.ES OTROSINGRESOS", "idotrosingresos");
	$xFRM->OCheck("TR.ES OTROSEGRESOS", "idotrosegresos");
	$xFRM->endSeccion();	
} else {
	if($action == SYS_NINGUNO AND $clave > 0){
		$xFRM->addSeccion("idnew", "TR.MODIFICAR OPERACION");
		//carga datos de actualizacion
		$xFRM->addGuardar();
		$xFRM->setAction("operaciones_tipos.frm.php?action=" . MQL_MOD);
		$xFRM->endSeccion();
	} else {
		switch ($action){
			case MQL_ADD:
				$xOp	= new cTipoDeOperacion();
				$xFRM->setResultado( $xOp->add($idnombre, $clave, $idalias, $idclonarde, $precio));
				if($idoingresos == true){ $xOp->setEsOtrosIngresos(); }
				if($idoegresos == true){
					$xOp->setEsOtrosEgresos();
				}
				$xFRM->addCerrar();				
				break;
			case MQL_MOD:
				if($clave > 0){
					
				}
				break;
		}
	}
	
}
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
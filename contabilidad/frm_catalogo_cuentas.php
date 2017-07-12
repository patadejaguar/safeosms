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
$xHP				= new cHPage("TR.Registro de Catalogo_contable");
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
function jsaHeredarDatos($idcuenta){
	$tab 		= new TinyAjaxBehavior();
	$xLog		= new cCoreLog();
	$xCta		= new cCuentaContable($idcuenta);
	
	$cuenta		= "";
	$nombre		= "";
	$superior	= "";
	$nombresupe	= "";
	$tipo		= "";
	$nivel		= "";
	$centro		= "";
	$superiorf	= "";
	$operar		= 0;
	$equivale	= "";
	if($xCta->init() == true ){
		$cuenta		= $xCta->get();
		$nombre		= $xCta->getNombre();
		$superior	= $xCta->getInmediatoSuperior();
		$nivel		= $xCta->getDigitoAgrupador();
		$centro		= $xCta->getCentroDeCosto();
		$tipo		= $xCta->getTipoDeCuenta();
		$operar		= 1;
		$xLog->add("OK\tModificar la cuenta $idcuenta - $operar - $superior\r\n");
		$equivale	= $xCta->getEquivalencia();
	} else {
		$xEsq		= new cCuentaContableEsquema($idcuenta);
		$cuenta		= $xEsq->CUENTA;
		$superior	= $xEsq->CUENTA_SUPERIOR;
		$nivel		= $xEsq->NIVEL_ACTUAL;
		$xLog->add("OK\tAgregar Nueva cuenta $idcuenta ($cuenta) - $superior\r\n");
	}
	$xLog->add( $xCta->getMessages(), $xLog->DEVELOPER);
	//inicializar superior
	if($nivel > 1){
		$xSup		= new cCuentaContable($superior); 
		if($xSup->init() == true){
			$tipo		= ($tipo == "") ? $xSup->getTipoDeCuenta() : $tipo;
			$centro		= ($centro == "") ? $xSup->getCentroDeCosto() : $centro;
			$superiorf	= $xSup->getCuentaCompleta($superior, true);
			$nombresupe	= $xSup->getNombre();
			if($operar == 0){
				$equivale	= $xSup->getEquivalencia();
			}
		}
		$xLog->add( $xSup->getMessages(), $xLog->DEVELOPER);
	}
	$tab -> add(TabSetvalue::getBehavior('idcuentacontable', $cuenta));
	$tab -> add(TabSetvalue::getBehavior('idnombrecuenta', $nombre));
	$tab -> add(TabSetvalue::getBehavior('idtipodecuentacontable', $tipo));
	$tab -> add(TabSetvalue::getBehavior('idcentrodecosto', $centro));
	$tab -> add(TabSetvalue::getBehavior('idcuentasuperior', $superiorf));
	$tab -> add(TabSetvalue::getBehavior('idnombresuperior', $nombresupe));
	$tab -> add(TabSetvalue::getBehavior('idoperacion', $operar));
	$tab -> add(TabSetvalue::getBehavior('idequivalencia', $equivale));
	if(MODO_DEBUG == true){
		$tab -> add(TabSetvalue::getBehavior('idmsg3', $xLog->getMessages() ));
	}
	
	return $tab -> getString();	
}
function jsaGuardarDatos($idcuenta, $nombre, $tipo, $centro, $equivalencia, $operacion){
	//no se puede cambiar naturaleza, superior
	$xLog			= new cCoreLog();
	if($operacion == SYS_CERO){
		$xEsq		= new cCuentaContableEsquema($idcuenta);
		$superior	= $xEsq->CUENTA_SUPERIOR;
		$idcuenta	= $xEsq->CUENTA;
		$nivel		= $xEsq->NIVEL_ACTUAL;
		$xCta			= new cCuentaContable($idcuenta);
		$xCta->add($nombre, $tipo, $centro, false, $nivel, false, $equivalencia, $superior);
		$xLog->add($xCta->getMessages(), $xLog->DEVELOPER);
		$xLog->add("OK\tAgregar Nueva cuenta $idcuenta\r\n");
	} else {
		$xCta			= new cCuentaContable($idcuenta);
		if($xCta->init() == true){
			$xCta->setActualizar($nombre, $equivalencia, $centro);
		}
		$xLog->add($xCta->getMessages(), $xLog->DEVELOPER);
		$xLog->add("OK\tActualizar $idcuenta $nombre $equivalencia $centro\r\n");
	}
	return $xLog->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaHeredarDatos', array('idcuentacontable'));
$jxc ->exportFunction('jsaGuardarDatos', array('idcuentacontable', 'idnombrecuenta','idtipodecuentacontable', 'idcentrodecosto', 'idequivalencia', 'idoperacion'), "#idmsgs");

$jxc ->process();

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xFRM->setFieldsetClass("fieldform frmpanel");
$xSel		= new cHSelect();
$xTxt		= new cHText(); $xTxt->setDivClass("");
$xTxt->addEvent("jsaHeredarDatos()", "onblur");
$xTxt2		= new cHText(); $xTxt2->setDivClass("");
$msg		= "";
$xFRM->setTitle($xHP->getTitle());

$xFRM->addGuardar("jsaGuardarDatos()");
$xFRM->OButton("TR.Panel", "jsGoPanel()", $xFRM->ic()->EJECUTAR);
$xFRM->addDivSolo($xTxt->getDeCuentaContable("idcuentacontable", "", false), $xTxt2->getNormal("idnombrecuenta", "", "TR.Nombre de la Cuenta"), "tx14", "tx34" );
$xFRM->addDivSolo("<input type='text' id='idcuentasuperior' disabled='true' />", "<input type='text' id='idnombresuperior' disabled='true' />", "tx14", "tx34" );

//$xFRM->addHElem( $xSel->getListaDeNivelesDeCuentasContables()->get(true) );
$xFRM->addHElem( $xSel->getListaDeTiposDeCuentasContables()->get(true) );
$xFRM->addHElem( $xSel->getListaDeTiposDeCentrosDeCosto()->get(true) );

$xFRM->OText("idequivalencia", "", "TR.Equivalente");

$xFRM->OHidden("idoperacion", "0", "TR.operacion");	//0 = nuevo, 1 = Actualizar
if(MODO_DEBUG == true){
	$xFRM->OTextArea("idmsg3", "", "TR.Texto");
}
$xFRM->addAviso(" ");
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
function jsGoPanel(){
	var idcuenta	= $("#idcuentacontable").val();
	var xC 			= new ContGen(); xC.goToPanel(idcuenta);
}
</script>
<?php
$xHP->fin();
?>
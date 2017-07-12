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
$xHP		= new cHPage("TR.CONSULTA EN LISTA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
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
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones	= parametro("idobservaciones");

$insafe			= parametro("idsafe", false, MQL_BOOL);
$gws			= parametro("idgws", false, MQL_BOOL);
$qq				= parametro("idqq", false, MQL_BOOL);
$interna		= parametro("idinterna", false, MQL_BOOL);

$xHP->init();

$xFRM		= new cHForm("frm", "./personas.consulta-en-listas.new.frm.php?action=" . MQL_ADD);
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addCerrar();


if($persona > DEFAULT_SOCIO){
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
		$xFRM->OHidden("persona", $persona);
		
		$nombre		= $xSoc->getNombre();
		$papellido	= $xSoc->getApellidoPaterno();
		$sapellido	= $xSoc->getApellidoMaterno();
		
		if($action == SYS_NINGUNO){
			$xFRM->OSiNo("TR.CONSULTA EN SAFE-LISTAS", "idsafe");
			if(AML_GWS_TOKEN !== ""){
				$xFRM->OSiNo("TR.CONSULTA EN GWS", "idgws");
			}
			//$xFRM->OSiNo("TR.CONSULTA EN QUIENESQUIEN", "idqq");
			$xFRM->OSiNo("TR.CONSULTA INTERNA", "idinterna");
			$xFRM->addEnviar("TR.NUEVA CONSULTA");
		} else {
			$xFRM->addCerrar();
			
			$xAProv	= new cAMLListasProveedores();
			$xNot		= new cHNotif();
			$xBtn		= new cHButton();
			$xTab		= new cHTabla();
			
			$xLProv		= new cAMLListasProveedores();
			
			$xTab->addTH("Proveedor");
			$xTab->addTH("Texto coincidente");
			$xTab->addTH("Reporte");
			
			
			if($gws == true){
				$res = $xLProv->getConsultaGWS($nombre, $papellido, $sapellido, $persona);
				if($res == false){
					$xTab->initRow();
					$xTab->addTD($xNot->get($xLProv->getMessages(), "idnogws", $xNot->SUCCESS), " colspan='3' ");
					$xTab->endRow();
						
				} else {
					$data	= $xLProv->getDataBusqueda();
						
					foreach ($data as $idx => $dat){
						$xTab->initRow();
						$xTab->addTD("GWS");
						$xTab->addTD($dat["nombre"] . " " . $dat["primerapellido"] . " " . $dat["segundoapellido"]);
						$xTab->addTD("");
						$xTab->endRow();
					}
					if(MODO_DEBUG){
						$xTab->initRow();
						$xTab->addTD($xNot->get($xLProv->getMessages(), "idsigws", $xNot->ERROR), " colspan='3' ");
						$xTab->endRow();
					}
				}
			}
			if($insafe == true){
				//Consultar PEPS
				$res = $xLProv->getConsultaInterna($nombre, $papellido, $sapellido, $persona, true);
				if($res == false){
					$xTab->initRow();
					$xTab->addTD($xNot->get($xLProv->getMessages(), "idnogws", $xNot->SUCCESS), " colspan='3' ");
					$xTab->endRow();
			
				} else {
					$data	= $xLProv->getDataBusqueda();
					foreach ($data as $idx => $dat){
						$xTab->initRow();
						$xTab->addTD("SAFE-LISTAS.- PEPS");
						$xTab->addTD($dat["nombres"] . " " . $dat["primerapellido"] . " " . $dat["segundoapellido"]);
						$url		= base64_encode($xLProv->getLinkReporte());
						$xBtn		= new cHButton();
						$xTab->addTD($xBtn->getBasic("TR.REPORTE", "jsGetConsultaSAFE('$url')", $xBtn->ic()->REPORTE));
						$xTab->endRow();
					}
					if(MODO_DEBUG){
						$xTab->initRow();
						$xTab->addTD($xNot->get($xLProv->getMessages(), "idsigws", $xNot->ERROR), " colspan='3' ");
						$xTab->endRow();
					}
				}
				//Consultar ListaNegra
				$res = $xLProv->getConsultaInterna($nombre, $papellido, $sapellido, $persona);
				if($res == false){
					$xTab->initRow();
					$xTab->addTD($xNot->get($xLProv->getMessages(), "idnogws", $xNot->SUCCESS), " colspan='3' ");
						
					$xTab->endRow();
			
				} else {
					$data	= $xLProv->getDataBusqueda();
					foreach ($data as $idx => $dat){
						$xTab->initRow();
						$xTab->addTD("SAFE-LISTAS.- BLOQUEADOS");
						$xTab->addTD( $dat["nombres"] . " " . $dat["primerapellido"] . " " . $dat["segundoapellido"]);
						$url		= base64_encode($xLProv->getLinkReporte());
						$xBtn		= new cHButton();
						$xTab->addTD($xBtn->getBasic("TR.REPORTE", "jsGetConsultaSAFE('$url')", $xBtn->ic()->REPORTE));
						$xTab->endRow();
					}
					if(MODO_DEBUG){
						$xTab->initRow();
						$xTab->addTD($xNot->get($xLProv->getMessages(), "idsigws", $xNot->ERROR), " colspan='3' ");
						$xTab->endRow();
					}
				}
			}
			
			
			$xFRM->addHElem( $xTab->get() );
			
			
			//Lista de omitidos
		}
		
	}
}


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsGetConsultaSAFE(str){
	str	= base64.decode(str);
	xG.w({url:str+"&report=true"});
}
function jsConsultaPorId(id){
	xG.w({url: "../frmpld/visor-consulta.frm.php?clave=" + id});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
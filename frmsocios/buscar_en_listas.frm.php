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
$xHP		= new cHPage("TR.Buscar PEPS y LISTA_NEGRA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();

function jsaBuscarPrevios($nombre, $papellido, $sapellido, $gws, $insafe){
	$p1	= utf8_encode($papellido);
	$p2	= utf8_encode($sapellido);
	$n		= utf8_encode($nombre);
	$sql		= "SELECT
	`personas_consulta_lista`.`idpersonas_consulta_lista` AS `clave`,
	`personas_consulta_lista`.`fecha`,
         `personas_consulta_lista`.`persona`,
         `personas_consulta_lista`.`tipo`,
         `personas_consulta_lista`.`proveedor`,
         /*`personas_consulta_lista`.`coincidente` AS ,*/
         `personas_consulta_lista`.`textocoincidente` AS `texto_coincidente`,
         `personas_consulta_lista`.`url`
FROM     `personas_consulta_lista`

WHERE
(`personas_consulta_lista`.`textocoincidente` LIKE '%$n $p1 $p2%') OR
(`personas_consulta_lista`.`textocoincidente` LIKE '%$n $p1%') OR
(`personas_consulta_lista`.`textocoincidente` LIKE '%$n $p2%') LIMIT 0,20";
	
	$xT			= new cTabla($sql);
	//$xT->setWithMetaData();
	$xT->setOmitidos("url");
	$xT->setEventKey("jsConsultaPorId");
	return $xT->Show();
}
function jsaBuscar($nombre, $papellido, $sapellido, $gws, $insafe){
	$xT		= new cTipos();
	$gws	= $xT->cBool($gws);
	$insafe	= $xT->cBool($insafe);
	$papellido	= utf8_encode($papellido);
	$sapellido	= utf8_encode($sapellido);
	$nombre		= utf8_encode($nombre);
	
	$xAml		= new cAMLPersonas(DEFAULT_SOCIO);
	$xNot		= new cHNotif();
	$xBtn		= new cHButton();
	$xTab		= new cHTabla();
	
	$xLProv		= new cAMLListasProveedores();
	
	$xTab->addTH("Proveedor");
	$xTab->addTH("Texto coincidente");
	$xTab->addTH("Reporte");
	
	//setLog("$nombre, $papellido, $sapellido");
	//return "$nombre, $papellido, $sapellido";
	if($gws == true){
		$res = $xLProv->getConsultaGWS($nombre, $papellido, $sapellido);
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
		$res = $xLProv->getConsultaInterna($nombre, $papellido, $sapellido, false, true);
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
		$res = $xLProv->getConsultaInterna($nombre, $papellido, $sapellido, false);
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


	return $xTab->get();
}

$jxc ->exportFunction('jsaBuscar', array('idnombre', 'idpapellido', 'idsapellido', 'idgws', 'idsafe'), "#busqueda");
$jxc ->exportFunction('jsaBuscarPrevios', array('idnombre', 'idpapellido', 'idsapellido', 'idgws', 'idsafe'), "#busqueda");
$jxc ->process();

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

$msg		= "";

$xTxt		= new cHText();
$xChk		= new cHCheckBox();
$xTxt->setDivClass("tx1");

$xFRM->OButton("TR.Buscar en Archivo", "jsaBuscarPrevios()", $xFRM->ic()->ARCHIVAR);
$xFRM->OButton("TR.Nueva Busqueda", "jsaBuscar()", $xFRM->ic()->BUSCAR);
//$xFRM->addHElem($xTxt->get("idbuscar", "", "TR.Nombre"));
$xFRM->OText("idnombre", "", "TR.Nombre");
$xFRM->OText("idpapellido", "", "TR.PRIMER_APELLIDO");
$xFRM->OText("idsapellido", "", "TR.SEGUNDO_APELLIDO");

$xFRM->addHElem($xChk->get("TR.GWS", "idgws") );
$xFRM->addHElem($xChk->get("TR.SAFE-LISTAS", "idsafe", true) );
$xFRM->addHElem("<div id='busqueda' class='tx1'></div>");

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
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
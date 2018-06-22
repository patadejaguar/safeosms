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
$xHP		= new cHPage("TR.PERSONAS FORMS_Y_DOCS", HP_FORM);
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
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xSoc		= new cSocio($persona);
if($xSoc->init() == true){
	//`tags` LIKE '%$xorigen%' OR 
	/*(`tags` LIKE '%" . SYS_TODAS .  "%') AND*/
$sql				= "SELECT `idgeneral_contratos`,`titulo_del_contrato`,`ruta` FROM `general_contratos` WHERE `estatus`='alta' AND `tipo_contrato`=" . iDE_SOCIO;
$rs					= $xQL->getDataRecord($sql);

$persona			= $xSoc->getClaveDePersona();
$xFRM->addCerrar();

$xTt				= new cHTabla();

$xTt->addTH("TR.NOMBRE");
$xTt->addTH("TR.HERRAMIENTAS");
$it		= 0;



//Formatos Nuevos
foreach ($rs as $rw){
	$cssTag	= ($it == 0) ? "tags blue" : "tags green";
	$cssTr	= ($it == 0) ? "" : "trOdd";
	$xForma	= new cFormatosDelSistema($rw["idgeneral_contratos"]); $xForma->init();
	if($it ==1 ){
		$it = 0;
	} else {
		$it++;
	}
	
	$xTt->initRow($cssTr);
	
	$url			= $rw["ruta"] . "&persona=" . $persona;
	$url2			= $rw["ruta"] . "&persona=" . $persona . "&out=" . OUT_PDF;
	$url3			= $rw["ruta"] . "&persona=" . $persona . "&out=" . OUT_DOC;
	$idint			= $rw["idgeneral_contratos"];
	
	
	$xBtn	= new cHButton();
	$xHl	= new cHUl("", "ul", $cssTag);
	
	$xHl->setTags("");
	$xHl->li($xBtn->getBasic("TR.IMPRIMIR", "var xG=new Gen();xG.w({url:'$url',blank:true})", $xFRM->ic()->IMPRIMIR, "", false, true));
	$xHl->li($xBtn->getBasic("TR.PDF", "var xG=new Gen();xG.w({url:'$url2',blank:true})", $xFRM->ic()->PDF, "", false, true));
	$xHl->li($xBtn->getBasic("TR.WORD", "var xG=new Gen();xG.w({url:'$url3',blank:true})", $xFRM->ic()->REPORTE5, "", false, true));
	
	if(MODO_DEBUG == true){
		$xHl->li($xBtn->getBasic("TR.EDITAR", "var xG=new Gen();xG.editForm({id:$idint})", $xFRM->ic()->EDITAR, "", false, true));
	}
	if($xForma->getEsTodas() == true){
		$xTt->addTD($xForma->getNombre());
		$xTt->addTD($xHl->get(), " class='toolbar-24' ");
	} else {
		if($xSoc->getEsPersonaFisica() == true AND $xForma->getEsPersonaFisica() == true){
			$xTt->addTD($xForma->getNombre());
			$xTt->addTD($xHl->get(), " class='toolbar-24' ");
		}
		if($xSoc->getEsPersonaFisica() == false AND $xForma->getEsPersonaMoral() == true){
			$xTt->addTD($xForma->getNombre());
			$xTt->addTD($xHl->get(), " class='toolbar-24' ");
		}
	}
	
	
	
	//$xFRM->OButton($rw["titulo_del_contrato"], "var xG=new Gen();xG.w({url:'$url',blank:true, precall:getOArgs})", $xFRM->ic()->REPORTE5, "", "white");
	$xTt->endRow();
}
$xFRM->addHElem($xTt->get() );
$xFRM->endSeccion();

}
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
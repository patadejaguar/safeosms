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
$xHP			= new cHPage("TR.LISTA DOCUMENTOS", HP_FORM);
$xQL			= new MQL();
$xLi			= new cSQLListas();
$xF				= new cFecha();
$xDic			= new cHDicccionarioDeTablas();
$xRuls			= new cReglaDeNegocio();
$useRequisitos	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_LISTA_REQUISITOS);

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
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->addJTableSupport();
$xHP->init();


//$ByType			= "";

if($persona<= DEFAULT_SOCIO AND $credito> DEFAULT_CREDITO){
	$xCred		= new cCredito($credito);
	if($xCred->init() == true){
		$idcontrato	= $credito;
		$persona	= $xCred->getClaveDePersona();
	}
}




$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

if($persona > DEFAULT_SOCIO){
	$xSoc	= new cSocio($persona);
	$xSoc->init();
	//$ByType	= ($xSoc->getEsPersonaFisica() == true) ? BASE_DOCTOS_PERSONAS_FISICAS : BASE_DOCTOS_PERSONAS_MORALES;
	//$xSelTD	= $xSel->getTiposDeDoctosPersonalesArch("", $ByType, $xSoc->getClaveDePersona());
	
/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivlstdoctos",$xHP->getTitle());

if($xSoc->getEsPersonaFisica() == true){
	$ByTipo	= " AND ((`personas_documentacion_tipos`.`clasificacion` ='IP') OR (`personas_documentacion_tipos`.`clasificacion` ='DG')) ";
} else {
	$ByTipo	= " AND ((`personas_documentacion_tipos`.`clasificacion` ='IPM') OR (`personas_documentacion_tipos`.`clasificacion` ='DG')) ";
}

$xHG->setSQL("SELECT `clave_de_control`, `nombre_del_documento`, IF(getEsDoctoEntregadoByP($persona,`clave_de_control`)=false,0,1) AS `entregado` FROM personas_documentacion_tipos WHERE `estatus`=1 AND `almacen`=1 $ByTipo");

$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("clave_de_control");

$xHG->col("nombre_del_documento", "TR.NOMBRE DEL DOCUMENTO", "25%");
$xHG->OColSiNo("entregado", "TR.EN ARCHIVO", "10%");


if($credito <= DEFAULT_CREDITO AND ($cuenta <= 0 OR $cuenta== DEFAULT_CUENTA_CORRIENTE)){
	$xHG->OToolbar("TR.AGREGAR", "jsAdd($persona)", "grid/add.png");
	$xHG->OButton("TR.CARGAR", "jsUpload($persona, ' + data.record.clave_de_control + ',' + data.record.entregado + ')", "upload.png");
	$xHG->OButton("TR.VER", "jsVer($persona, ' + data.record.clave_de_control + ',' + data.record.entregado + ')", "view.png");
} else {
	if($credito>DEFAULT_CREDITO){
		$xHG->OToolbar("TR.AGREGAR", "jsAddCont($persona, $credito)", "grid/add.png");
		$xHG->OButton("TR.CARGAR", "jsUploadCont($persona,$credito,' + data.record.clave_de_control + ',' + data.record.entregado + ')", "upload.png");
		$xHG->OButton("TR.VER", "jsVerCont($persona,$credito,' + data.record.clave_de_control + ',' + data.record.entregado + ')", "view.png");
	}

}



//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave_de_control +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave_de_control +')", "delete.png");
//$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.clave_de_control +')", "undone.png");


if(MODO_DEBUG == true){
	$xHG->OButton("TR.PROPIEDADES", "jsEditProps('+ data.record.clave_de_control +')", "process.png");
}

$xFRM->addHElem("<div id='iddivlstdoctos'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();

}

?>

<script>
var xG    	= new Gen();


function jsEdit(id){
    xG.w({url:"../frm/.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivlstdoctos});
}
function jsEditProps(id){
	xG.w({url:"../frmsocios/catalogo-documentacion.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivlstdoctos});
}

function jsDel(id){
    xG.rmRecord({tabla:"personas_documentacion_tipos", id:id, callback:jsLGiddivlstdoctos });
}
function jsDeact(id){
    xG.recordInActive({tabla:"personas_documentacion_tipos", id:id, callback:jsLGiddivlstdoctos, preguntar:true });
}
function jsAdd(idx){
    xG.w({url:"../frmsocios/personas_documentos.add.frm.php?persona=" + idx, tiny:true, callback: jsLGiddivlstdoctos});
}
function jsUpload(idp, tipodocto, entregado){
	if(entregado == 1){
		xG.alerta({msg: "MSG_CONCEPTO_EXISTE"});
	} else {
		xG.w({url:"../frmsocios/personas_documentos.add.frm.php?persona=" + idp + "&tipo=" + tipodocto, tiny:true, callback: jsLGiddivlstdoctos});
	}
}
function jsAddCont(idx, idcont){
    xG.w({url:"../frmsocios/personas_documentos.add.frm.php?persona=" + idx + "&contrato=" + idcont, tiny:true, callback: jsLGiddivlstdoctos});
}
function jsUploadCont(idp, idcont, tipodocto, entregado){
	if(entregado == 1){
		xG.alerta({msg: "MSG_CONCEPTO_EXISTE"});
	} else {
		xG.w({url:"../frmsocios/personas_documentos.add.frm.php?persona=" + idp + "&tipo=" + tipodocto + "&contrato=" + idcont, tiny:true, callback: jsLGiddivlstdoctos});
	}
}
function jsVer(idp, tipodocto, entregado){
	if(entregado == 0){
		xG.alerta({msg: "MSG_CONCEPTO_NOEXIS"});
	} else {
		var xP=new PersGen();xP.getDocumento({persona:idp, tipo: tipodocto});
	}
}
function jsVerCont(idp, idcont, tipodocto, entregado){
	if(entregado == 0){
		xG.alerta({msg: "MSG_CONCEPTO_NOEXIS"});
	} else {
		var xP=new PersGen();xP.getDocumento({persona:idp, tipo: tipodocto, contrato:idcont});
	}
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
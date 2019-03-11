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
$xHP		= new cHPage("TR.PERMISOS DE ACCIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();


function jsaDenegarNivel($clave, $nivel){
	$nivel	= setNoMenorQueCero($nivel);
	$clave	= setNoMenorQueCero($clave);
	$sql	= "SELECT * FROM `general_niveles` WHERE `tipo_sistema`=$nivel AND `estatus`=1 ";
	$xQL	= new MQL();
	$xT		= new cGeneral_niveles();
	if($nivel > 0 AND $clave > 0){
		$rs	= $xQL->getDataRecord($sql);
		foreach ($rs as $rw){
			$idp		= $rw[$xT->IDGENERAL_NIVELES];
			$xPerm	= new cSystemPermisosObjeto($clave);
			if($xPerm->init() == true){
				
				$xPerm->addNegado($idp);
			}
		}
	}
	return "Listo Nivel $nivel en la clave $clave";
}
function jsaDenegarTodos($clave){
	$xPerm		= new cSystemPermissions();
	$clave		= setNoMenorQueCero($clave);
	$xQL		= new MQL();
	$xQL->setRawQuery("UPDATE `sistema_permisos` SET `denegado`='" . $xPerm->DEF_PERMISOS . "' WHERE `idsistema_permisos`=$clave");
	return "Todos..";
}
$jxc ->exportFunction('jsaDenegarNivel', array('idclave', 'idnegado'), "#idxaviso");
$jxc ->exportFunction('jsaDenegarTodos', array('idclave'), "#idxaviso");
$jxc ->process();
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

$observaciones= parametro("idobservaciones");


$form		= parametro("form", "", MQL_RAW);


$xHP->addJTableSupport();
$xHP->init();

$ByF		= ($form == "") ? "" : " AND (`accion` LIKE '" .  crc32($form) . "-f-" . "%') ";

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivpermisos",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `sistema_permisos` WHERE `estatus`=1 $ByF LIMIT 0,100");
//exit("SELECT * FROM `sistema_permisos` WHERE `estatus`=1 $ByF LIMIT 0,100");

$xHG->addList();
$xHG->setOrdenar();



$xHG->addKey("idsistema_permisos");
//$xHG->col("accion", "TR.ACCION", "10%");
if($form == ""){
	$xHG->col("nombre_objeto", "TR.SUJETO", "10%");
}
$xHG->col("descripcion", "TR.DESCRIPCION", "20%");
//$xHG->col("tipo_objeto", "TR.TIPO OBJETO", "10%");

$xHG->col("denegado", "TR.DENEGADO", "15%");

$rs	= $xQL->getDataRecord("SELECT `tipo_sistema` AS `clave`, MAX(`descripcion_del_nivel`) AS `tipo` FROM `general_niveles` WHERE `estatus`=1 GROUP BY `tipo_sistema` ");
foreach ($rs as $rw){
	$idx	= $rw["clave"];
	
	$nn		= $rw["tipo"];
	if($idx <= 6){
		$xHG->OButton("$nn", "jsAddNegados('+ data.record.idsistema_permisos +',$idx)", "$idx.png");
	}
}
$xHG->OButton("TR.TODOS", "jsAddNegadosTodos('+ data.record.idsistema_permisos +')", "done-all.png");
//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idsistema_permisos +')", "edit.png");
if(SAFE_ON_DEV == true){
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idsistema_permisos +')", "delete.png");
}



$xFRM->addHElem("<div id='iddivpermisos'></div>");

$xFRM->addJsCode( $xHG->getJs(true) );

$xFRM->OHidden("idnegado", 0);
$xFRM->OHidden("idclave", 0);
$xFRM->OHidden("idxaviso", "");

echo $xFRM->get();
?>
<script>
var xG			= new Gen();

function jsEdit(id){
	xG.w({url:"../frmsecurity/sistema-permisos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivpermisos});
}
function jsAdd(){
	xG.w({url:"../frmsecurity/sistema-permisos.new.frm.php?", tiny:true, callback: jsLGiddivpermisos});
}
function jsDel(id){
	xG.rmRecord({tabla:"sistema_permisos", id:id, callback:jsLGiddivpermisos});
}
function jsAddNegados(id, idnivel){
	//var idnegado = $("#idnegado").val();
	//var idclave = $("#idclave").val();
	$("#idclave").val(id);
	$("#idnegado").val(idnivel);
	//xG.confirmar({msg: "MSG_CONFIRMA_GUARDAR", callback : jsaDenegarNivel});
	jsaDenegarNivel();
}
function jsAddNegadosTodos(id){
	$("#idclave").val(id);
	
	xG.confirmar({msg: "MSG_CONFIRMA_GUARDAR", callback : jsaDenegarTodos});
}
function jsMsg(){
	$("#idclave").val(0);
	$("#idnegado").val(0);
	
	var idxaviso = $("#idxaviso").val(); 
	xG.alerta({msg: idxaviso});
}
</script>
<?php


$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
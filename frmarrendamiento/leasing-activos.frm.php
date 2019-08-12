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
$xHP		= new cHPage("TR.FLOTA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xAc		= new cLeasingActivos();
$xUser			= new cSystemUser(getUsuarioActual()); $xUser->init();
$xRuls			= new cReglaDeNegocio();
$originador		= 0;
$suborigen		= 0;
$EsAdmin		= false;
$NoUsarUsers	= $xRuls->getArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_NOUSERS);
$EsOriginador	= false;

if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		$originador	= $xOrg->getOriginador();
		$suborigen	= $xOrg->getSubOriginador();
		//$EsActivo	= $xOrg->getEsActivo();
		$EsAdmin	= $xOrg->getEsAdmin();
		if($xOrg->getEsAdmin() == true){
			$suborigen			= 0;
		}
		if($xOrg->getEsActivo() == false){
			$xHP->goToPageError(403);
		} else {
			$EsOriginador	= true;
		}
	}
}
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$todas		= parametro("todas", false, MQL_BOOL); $todas = parametro("todos", $todas, MQL_BOOL);


$idleasing	= parametro("idleasing",0 , MQL_INT );

$xHP->addJTableSupport();
$xHP->init();

$xFRM		= new cHForm("frmactivos", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$ById		= ($idleasing >0 ) ? " AND `leasing_activos`.`clave_leasing` = $idleasing " : "";
$xFRM->OHidden("idleasing", $idleasing);

$xFRM->addCerrar();

$ByStatus	= ($todas == true) ? "" : " AND (`leasing_activos`.`status`= " . $xAc->ESTADO_ACTIVO . ") ";

/* ===========		GRID JS		============*/


$xHG	= new cHGrid("iddivactivos",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `leasing_activos` WHERE `leasing_activos`.`clave_leasing` >0 $ById $ByStatus LIMIT 0,100");
$xHG->addList();
$xHG->addKey("idleasing_activos");

$xHG->col("idleasing_activos", "TR.ID", "5%");
$xHG->col("clave_leasing", "TR.IDLEASING", "5%");
//$xHG->col("persona", "TR.PERSONA", "10%");
$xHG->col("credito", "TR.CREDITO", "10%");
$xHG->col("placas", "TR.PLACAS", "15%");
$xHG->col("descripcion", "TR.DESCRIPCION", "50%");

$xHG->OColFunction("estatus", "TR.ESTATUSACTIVO", "8%", "jsRendEstatus");

$xHG->setOrdenar();

$xHG->setColSum("credito");

//$xHG->col("proveedor", "TR.PROVEEDOR", "10%");
/*
$xHG->col("fecha_compra", "TR.FECHA COMPRA", "10%");
$xHG->col("fecha_registro", "TR.FECHA REGISTRO", "10%");
$xHG->col("fecha_mtto", "TR.FECHA MTTO", "10%");
$xHG->col("fecha_seguro", "TR.FECHA SEGURO", "10%");
$xHG->col("tipo_activo", "TR.TIPO ACTIVO", "10%");
$xHG->col("tipo_seguro", "TR.TIPO SEGURO", "10%");
$xHG->col("tasa_depreciacion", "TR.TASA DEPRECIACION", "10%");
$xHG->col("valor_nominal", "TR.VALOR NOMINAL", "10%");
$xHG->col("serie", "TR.SERIE", "10%");
$xHG->col("factura", "TR.FACTURA", "10%");

$xHG->col("motor", "TR.MOTOR", "10%");
$xHG->col("marca", "TR.MARCA", "10%");
$xHG->col("color", "TR.COLOR", "10%");
*/
if($EsOriginador == false){
	$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
	
	$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idleasing_activos +')", "edit.png");
	
	$xHG->OButton("TR.BAJA", "jsBaja('+ data.record.idleasing_activos +')", "prohibition.png");
	
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idleasing_activos +')", "delete.png");

}

$xFRM->addHElem("<div id='iddivactivos'></div>");

if($todas == false){
	$xHG->OToolbar(SYS_TEXTO_TODAS, "jsGoTodas()", "grid/funnel.png");
} else {
	$xHG->OToolbar("TR.ESTATUSACTIVO", "jsGoNormal()", "grid/filter.png");
}



$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/leasing-activos.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivactivos});
}
function jsAdd(){
	var idleasing = $("#idleasing").val();
	xG.w({url:"../frmarrendamiento/leasing-activos.new.frm.php?idleasing=" + idleasing, tiny:true, callback: jsLGiddivactivos});
}
function jsDel(id){
	//data.record.idleasing_activos
	xG.rmRecord({tabla:"leasing_activos", id:id, callback:jsLGiddivactivos});
}
function jsBaja(id){
	var $row = $('#iddivactivos').jtable('getRowByKey', id);
	if ($row.length > 0){
        $row.each(function () {
			var record = $(this).data('record');
			if(record.status == 0){
				xG.confirmar({msg : "MSG_CONFIRMA_ACTIVACION", callback: function(){
					xG.save({tabla: "leasing_activos", id: id, nomsg:true, content: "status=1", callback: jsLGiddivactivos});
				}});
			} else {
				xG.w({url:"../frmarrendamiento/leasing-activos.baja.frm.php?clave=" + id, tiny:true, callback: jsLGiddivactivos});
			}
        });		
	}
}
function getActivos(){
	//var idfecha	= xF.get($("#idfechaactual").val());
	//var str 	= " getFechaByInt(`sistema_eliminados`.`tiempo`) = '" + idfecha + "' ";
	//str			= "&w="  + base64.encode(str);

	//$('#iddiveliminados').jtable('destroy');
	
	//jsLGiddiveliminados(str);	
}
function jsGoTodas(){
	var idleasing	= $("#idleasing").val();
	
	xG.go({url: "../frmarrendamiento/leasing-activos.frm.php?todas=true&idleasing=" + idleasing });
}
function jsGoNormal(){
	var idleasing	= $("#idleasing").val();
	
	xG.go({url: "../frmarrendamiento/leasing-activos.frm.php?idleasing=" + idleasing });
}
function jsRendEstatus(dd){
	
	if(dd.record.status == 0){
		return "<img class='x24' src='../images/busy.png' />";
	} else {
		return "<img class='x24' src='../images/check.png' />";
	}
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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
$xHP		= new cHPage("TR.LISTA DE COTIZADOR", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
//$xDic		= new cHDicccionarioDeTablas();
$xUser		= new cSystemUser(getUsuarioActual()); $xUser->init();
$xRuls		= new cReglaDeNegocio();

$originador	= 0;
$suborigen	= 0;
$EsAdmin	= false;

$NoUsarUsers= $xRuls->getArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_NOUSERS);

//$EsActivo	= false;
if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		$originador	= $xOrg->getOriginador();
		$suborigen	= $xOrg->getSubOriginador();
		//$EsActivo	= $xOrg->getEsActivo();
		//$EsAdmin	= $xOrg->getEsAdmin();
		if($xOrg->getEsAdmin() == true){
			$suborigen			= 0;
		}
		if($xOrg->getEsActivo() == false){
			$xHP->goToPageError(403);
		}
	}
}
//$jxc = new TinyAjax();
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

$observaciones= parametro("idobservaciones");

$xHP->addJTableSupport();
$xHP->init();


$xFRM	= new cHForm("frmoriginacion_leasing", "leasing-comisiones.frm.php?action=$action");
/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cOriginacion_leasing();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xFRM->addCerrar();

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivleasingcreditos",$xHP->getTitle());

$xHG->setSQL($xLi->getListadoDeLeasingSolicitudes($originador, $suborigen));



$xHG->addList();
$xHG->addKey("clave");
$xHG->col("clave", "TR.ID", "10%");
$xHG->ColFecha("fecha", "TR.FECHA", "10%");

if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		if($xOrg->getEsAdmin() == true){
			$xHG->col("nombre_suboriginador", "TR.SUBORIGINADOR", "10%");
		}
	}
} else {
	if($NoUsarUsers == false){
		$xHG->col("nombre_originador", "TR.ORIGINADOR", "10%");
		$xHG->col("nombre_suboriginador", "TR.SUBORIGINADOR", "10%");
	}
}


$xHG->col("cliente", "TR.CLIENTE", "25%");

$xHG->ColMoneda("precio_vehiculo", "TR.PRECIO", "10%");

$xHG->ColMoneda("monto_anticipo", "TR.ANTICIPO", "10%");

//$xHG->col("total_credito", "TR.CREDITO", "10%", true);
//$xHG->col("monto_directo", "TR.PAGO DIRECTO", "10%", true);

/*$xHG->col("monto_aliado", "TR.EQUIPOALIADO", "10%", true);
$xHG->col("monto_accesorios", "TR.ACCESORIOS", "10%", true);
$xHG->col("monto_tenencia", "TR.TENENCIA", "10%", true);
$xHG->col("monto_garantia", "TR.GARANTIA", "10%", true);
$xHG->col("monto_mtto", "TR.MTTO", "10%", true);*/

//$xHG->col("proceso", "TR.PROCESO", "10%", true);

$xHG->OColFunction("proceso", "TR.ETAPA", "15%", "jsQuePaso");
//Editar
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");



if($xUser->getEsOriginador() == false){
	$xHG->OButton("TR.ADMINISTRAR", "jsEdit2('+ data.record.clave +')", "unlocked.png");
} else {
	if($EsAdmin == true){
		//Cancelar
	} else {

	}
}



/*$xHG->col("oficial", "TR.EJECUTIVO", "10%");
$xHG->col("persona", "TR.PERSONA", "10%");
$xHG->col("credito", "TR.CREDITO", "10%");*/

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");

$xHG->OButton("TR.Archivo", "jsArchivo('+ data.record.clave +')", "archive.png");
if(MODO_DEBUG){
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
}
$xHG->setOrdenar();

$xFRM->addHElem("<div id='iddivleasingcreditos'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/cotizador.edit.frm.php?clave=" + id, tab:true, callback: jsReload});
}
function jsEdit2(id){
	xG.w({url:"../frmarrendamiento/cotizador.edit.frm.php?olvidar=true&clave=" + id, tab:true, callback: jsReload});
}
function jsArchivo(id){
	xG.recordInActive({preguntar:true, tabla: "originacion_leasing", id: id, callback: jsReload});
}

function jsAdd(){
	xG.w({url:"../frmarrendamiento/cotizador.frm.php?", tab:true, callback: jsReload});
}
function jsReload(){
	$("#iddivleasingcreditos").jtable('destroy');
	jsLGiddivleasingcreditos();
}
function jsDel(id){
	xG.rmRecord({tabla:"originacion_leasing", id:id, callback:jsReload});
}
function jsQuePaso(data){
	ch 			= "";
	var vStep	= entero(data.record.proceso);
	
	switch(vStep){
	case Configuracion.credito.etapas.registrado:
		ch = "<div class='progress'><span class='orange' style='width:1%'><span>Registrado</span></span></div>";
		break;
			
		case Configuracion.credito.etapas.atendido:
			ch = "<div class='progress'><span class='orange' style='width:20%'><span>Visto</span></span></div>";
			break;
		case Configuracion.credito.etapas.con_oficial:
			ch = "<div class='progress'><span class='orange' style='width:30%'><span>Ejecutivo</span></span></div>";
			break;
		case Configuracion.credito.etapas.con_persona:
			ch = "<div class='progress'><span class='blue' style='width:40%'><span>Persona</span></span></div>";
			break;
		case Configuracion.credito.etapas.con_credito:
			ch = "<div class='progress'><span class='green' style='width:50%'><span>Credito</span></span> </div>";
			break;
		case Configuracion.credito.etapas.solicitado:
			ch = "<div class='progress'><span class='green' style='width:60%'><span>Tramitado</span></span> </div>";
			break;
		case Configuracion.credito.etapas.autorizado:
			ch = "<div class='progress'><span class='green' style='width:80%'><span>Autorizado</span></span> </div>";
			break;
		case Configuracion.credito.etapas.vigente:
			ch = "<div class='progress'><span class='green' style='width:100%'><span>Otorgado</span></span> </div>";
			break;			
		default:
			
			break;			
	}
	
	
	//ch = "<ol class='rounded-list'>" + ch + "</ol>";
	//ch = "<div class='progress'><span class='red' style='with:30%'><span>105</span></span> </div>";
	return ch;
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
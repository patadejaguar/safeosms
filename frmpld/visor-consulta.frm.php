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
$xHP		= new cHPage("TR.VISOR CONSULTA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
//$xHP->setIncludeJQueryUI();
$xHP->init();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);


$xFRM		= new cHForm("frm", "./");
$xTxt		= new cHTextArea();

$xTb		= new cPersonas_consulta_lista();
$xTb->setData($xTb->query()->initByID($clave));
if($xTb->proveedor()->v() == "INTERNO"){
	$url	= $xTb->url()->v() . "&report=true";
	$xHP->goToPageX($url);
	exit;
}
//setError($xTb->contenido()->v());
$data		= base64_decode($xTb->contenido()->v());
$html		= "";
$xHT		= new cHTabla("lsresultado", "listado");
$djson		= json_decode($data, true);

unset($djson["primerapellido"]);
unset($djson["segundoapellido"]);
unset($djson["curp"]);
unset($djson["nombres"]);
unset($djson["tipo"]);

$xHT->addTH("Descripcion");
$xHT->addTH("Valor");

foreach ($djson as $cv => $cn){
	$xHT->initRow();
	$xHT->addTD($xFRM->l()->getT("TR.$cv"), " scope='col' ");
	$xHT->addTD($cn);
	
	$xHT->endRow();
}
$html		= $xHT->get();

$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();


$xTabs		= new cHTabs();
$xDiv1		= new cHDiv("tx1", "idhvals");

$xTabs->addTab("Resultado", $html );

$xTabs->addTab("Sin Formato", $xDiv1->get($data) );

$xFRM->addHTML( $xTabs->get() );


echo $xFRM->get();
?>
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Quicksand" />
    <link rel="stylesheet" type="text/css" href="../css/pretty-json.css" />
    <script type="text/javascript" src="../js/underscore-min.js" ></script>
    <script type="text/javascript" src="../js/backbone-min.js" ></script>
    <script type="text/javascript" src="../js/pretty-json-min.js" ></script>
    
<script>
$(document).ready(function() {
var el = {
        solovals: $('#idhvals'),
        hsolovals: $('#idhvals'),
        /*idtodo: $('#idhtodo'),
        idhtodo: $('#idhtodo')*/
    };
//function jsRender(){
	//$("#idtexto").val(session("var.serialize"));
    var json1 = el.solovals.html();
    //var json2 = el.idtodo.html();

    var data1;
    //var data2;
    try{ 
    	
        data1 = JSON.parse(json1);
        //data2 = JSON.parse(json2);
    } catch(e){
    	el.hsolovals.html(data1); 
    	//el.idhtodo.html(data2);
		alert('JSON Incorrecto');
		return;
	}

    var node = new PrettyJSON.view.Node({ 
        el:el.hsolovals,
        data: data1,
        dateFormat:"DD/MM/YYYY - HH24:MI:SS"
    });
    /*var node = new PrettyJSON.view.Node({ 
        el:el.idhtodo,
        data: data2,
        dateFormat:"DD/MM/YYYY - HH24:MI:SS"
    });   */ 
//}

});
</script>
<style>
	#idtexto {
		min-height: 400px;
	}
</style>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
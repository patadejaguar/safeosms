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
$xHP		= new cHPage("TR.VISOR JSON", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
//$xHP->setIncludeJQueryUI();
$xHP->init();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$tabla		= parametro("tabla", false, MQL_RAW);
$clave		= parametro("id", false, MQL_RAW);

$xFRM		= new cHForm("frm", "./");

$xTxt		= new cHTextArea();
$msg		= "";

$rs			= array();
$soloValores= array();
//AND MODO_DEBUG == true
if($tabla != false AND $clave != false ){
	$xObj	= new cSQLTabla($tabla);
	if( $xObj->obj() == null){
		$rs["message"]		= "ERROR\t para la Tabla $tabla y clave $clave\r\n";
		$rs["error"]		= true;
	} else {
		$obj	= $xObj->obj();
		$key	= $obj->getKey();
		//$obj	= new cSocios_general();
		$obj->setData( $obj->query()->initByID($clave) );
		$rs		= $obj->query()->getCampos();
		foreach ($rs as $vals => $cnt){
			$soloValores[$vals]	= $cnt["V"];
		}
	}
}

$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

$todo		= json_encode($rs);
$jsVals		= json_encode($soloValores);

$xTabs		= new cHTabs();
$xDiv1		= new cHDiv("tx1", "idhvals");
$xDiv2		= new cHDiv("tx1", "idhtodo");

$xTabs->addTab("Campos", $xDiv1->get($jsVals) );
$xTabs->addTab("Todo", $xDiv2->get($todo)  );
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
        idtodo: $('#idhtodo'),
        idhtodo: $('#idhtodo')
    };
//function jsRender(){
	//$("#idtexto").val(session("var.serialize"));
    var json1 = el.solovals.html();
    var json2 = el.idtodo.html();

    var data1;
    var data2;
    try{ 
    	
        data1 = JSON.parse(json1);
        data2 = JSON.parse(json2);
    } catch(e){
    	el.hsolovals.html(data1); 
    	el.idhtodo.html(data2);
		alert('not valid JSON');
		return;
	}

    var node = new PrettyJSON.view.Node({ 
        el:el.hsolovals,
        data: data1,
        dateFormat:"DD/MM/YYYY - HH24:MI:SS"
    });
    var node = new PrettyJSON.view.Node({ 
        el:el.idhtodo,
        data: data2,
        dateFormat:"DD/MM/YYYY - HH24:MI:SS"
    });    
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
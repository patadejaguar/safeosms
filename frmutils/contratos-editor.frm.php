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
$xHP			= new cHPage("TR.Editar FORMS_Y_DOCS");
$idcontrato 	= parametro("idcontrato", 0 , MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);
$tipopersona	= parametro("tipopersona",0, MQL_INT); $tipopersona = parametro("tipodepersona", $tipopersona, MQL_INT);

$xHP->addJsFile("../js/ckeditor/ckeditor.js");

$xFRM		= new cHForm("frmeditor", "contratos-editor.frm.php", false, "GET");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xFMT		= new cFormato(false);
$xLi		= new cSQLListas();

$xHP->addJTableSupport();
$jxc 		= new TinyAjax();
function jsBajaContrato($id, $nombre){
	$xForma		= new cFormato();
	if($xForma->init($id) == true){
		$xForma->setBaja();
	}
}
function jsActivarContrato($id, $nombre){
	$xForma		= new cFormato();
	if($xForma->init($id) == true){
		$xForma->setAlta();
	}
}

$jxc->exportFunction('jsActivarContrato', array('idcontrato'), "#idaviso");
$jxc->exportFunction('jsBajaContrato', array('idcontrato'), "#idaviso");

$jxc->process();

//ini_set("display_errors", "on");

$xHP->init("initComponents()");
$xFRM->setTitle($xHP->getTitle());


	$xFRM->addCerrar();
	//$xFRM->addDivSolo( $xSel->getListaDeFormatos("idcontrato")->get(false), "", "txmon" );
	//
	$xFRM->OHidden("idcontrato", $idcontrato);
	/* ===========		GRID JS		============*/
	$xLF	= new cFormatosDelSistema();
	
	$xHG	= new cHGrid("iddiv",$xHP->getTitle());
	
	//$q1		= $xHG->setSQL("SELECT * FROM `general_contratos` ORDER BY `estatus`,`titulo_del_contrato` LIMIT 0,20");
	//$xHG->setSQL("SELECT * FROM `general_contratos` WHERE `estatus`='alta' ORDER BY `titulo_del_contrato` LIMIT 0,20");
	
	$q1		= $xHG->setSQL($xLF->getSQL_Lista(false));
	$xHG->setSQL( $xLF->getSQL_Lista() );
	
	
	
	if($producto >0){
		$xProd	= new cProductoDeCredito($producto);
		if($xProd->init() == true){
		  $subtipo	= $xProd->getTipoEnSistema();
		  $tipo     = iDE_CREDITO;
		  //$xFMT	= new cFormato();
		  $q1		= $xHG->setSQL($xLF->getSQL_Lista(false, $tipo, $subtipo));
		  if($subtipo == 500){
		  	$subtipo	= 281;			//FiXME: Corregir por leasing
		  }
		  $xHG->setSQL( $xLF->getSQL_Lista(true, $tipo, $subtipo) );
		}
		
	}
	if($tipopersona > 0){
		$xTI	= new cPersonasTipoDeIngreso($tipopersona);
		if($xTI->init() == true){
			//$xTI->
			$tipo     = iDE_SOCIO;
			$q1		= $xHG->setSQL($xLF->getSQL_Lista(false, $tipo));
		}
	}
	$xHG->addList();
	$xHG->addKey("idgeneral_contratos");
	$xHG->col("idgeneral_contratos", "TR.CLAVE", "10%");
	$xHG->col("titulo_del_contrato", "TR.NOMBRE", "10%");
	$xHG->col("tipo_contrato", "TR.TIPO", "10%");
	$xHG->col("tags", "TR.TAGS", "10%");
	//$xHG->col("estatus", "TR.ESTATUS", "10%");
	
	//$xHG->col("texto_del_contrato", "TR.TEXTO DEL CONTRATO", "10%");
	
	$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
	$xHG->OToolbar("TR.VER TODOS", "jsVerTodos()", "grid/off.png");
	
	$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idgeneral_contratos +')", "edit.png");
	$xHG->OButton("TR.BAJA", "jsBaja('+ data.record.idgeneral_contratos +')", "minus.png");
	$xHG->OButton("TR.VER", "getForma('+ data.record.idgeneral_contratos +')", "view.png");
	
	if(MODO_DEBUG == true){
		$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idgeneral_contratos +')", "delete.png");
	}
	
	$xHG->setOrdenar();
	
	$xFRM->addHElem("<div id='iddiv'></div>");
	$xFRM->addAviso("", "idaviso");
	
	$xFRM->addJsCode( $xHG->getJs(true) );
	
	echo $xFRM->get();
	?>
	<script>
	var xG	= new Gen();
	var q1	= "<?php echo $q1; ?>";
	function jsEdit(id){
		//xG.w({url:"../frm/.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddiv});
		$("#idcontrato").val(id);
		//$("#id-frmeditor").trigger("submit");
		xG.w({url:"../frmutils/contratos-editor.edit.frm.php?idcontrato=" + id, tab:true, callback: jsLGiddiv});
	}
	function jsAdd(){
		xG.w({url:"../frmutils/contratos-editor.new.frm.php?", tiny:true, callback: jsLGiddiv});
	}
	function jsDel(id){
		xG.rmRecord({tabla:"general_contratos", id:id, callback:jsLGiddiv});
	}
	function jsBaja(id){
		$("#idcontrato").val(id);
		xG.confirmar({msg:"Confirma desactivar este Formato", callback:jsBajaContrato});
	}
	function jsAlta(id){
		$("#idcontrato").val(id);
		xG.confirmar({msg:"Confirma Activar este Formato", callback:jsActivarContrato});
	}
	function jsVerTodos(){
		var str		= "&q="  + q1;
		$('#iddiv').jtable('destroy');
		jsLGiddiv(str);		
	}
	</script>
	<?php
		
	


//echo "$datos_del_contrato[4] en contrato $idcontrato";


$jxc ->drawJavaScript(false, true);
?>
<script>
var xG	= new Gen();
	function jsAddText(){
		var txt = document.getElementById("idvariables").value;
		//var curSel = document.getSelection();
		InsertHTML(txt);
	}
	function setSelectSize(mSize){
		var mSelect = document.getElementById("idvariables");
			mSelect.removeAttribute("size");
			mSelect.setAttribute("size", mSize);
	}
	function InsertHTML(strText){
		CKEDITOR.instances['ckeditor'].insertText(strText);
	}
	function jsGuardar(){
		CKEDITOR.instances['ckeditor'].commands.save.exec();
	}	
	function initComponents(){
		$("#cke_1_contents").css("height", "600px");
	}
	function getForma(id){
		xG.w({ url : "../frmutils/forma.vista_previa.rpt.php?forma=" + id});
	}
</script>
<?php
$xHP->fin();
?>

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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  


$xHP->addJsFile("../js/jquery-hover-dropdown-box.js");
$xHP->addCSS("../css/jquery-hover-dropdown-box.css");
$xHP->init();

$xFRM		= new cHForm("frmcompromisosdetalles", "./");
$xFRM->addCerrar();
$xFRM->addJsInit("init();");
$xComp		= new cSeguimientoCompromisos($clave);
if( $xComp->init() == true){
	$xFRM->OHidden("idclave", $clave);
	//$xFRM->addCreditoComandos($xComp->getClaveDeCredito());
	$credito	= $xComp->getClaveDeCredito();
	$persona	= $xComp->getClaveDePersona();
	$xFRM->addHElem($xComp->getFicha(true, true) );
	//$xFRM->addDivSolo(, '<div id="example_inline_box"></div>');
	$xFRM->OButton("TR.Editar", "var xS=new SegGen();xS.setEditarCompromiso({clave:$clave})", $xFRM->ic()->EDITAR);
	$xFRM->OButton("TR.Cambiar estatus", "", $xFRM->ic()->AVISO, "idbtnestado");
	$xFRM->OButton("TR.NOTAS PROMESA_DE_VISITA", "var xS=new SegGen();xS.setAgregarNotaVisita($persona,$credito);", $xFRM->ic()->NOTA, "idbtnestado");
	//$xFRM->OTextArea("idnota", $xComp->getNota(), "TR.Nota");
	//agregar llamadas
	$xT			= new cTabla($xLi->getListadoDeNotas(false, $credito));
	$xFRM->addHElem($xT->Show("TR.Notas"));
}
$msg		= "";
//Agregar Nota

echo $xFRM->get();
?>
<script>
var xPer	= new PersGen();
var xSeg	= new SegGen();
/*		items: {
			"Item A": {
				color: "#e74c3c",
				inputType: "text",
				inputChecked: true,
				inputPlaceholder: "Example input"
			},
			"Item B": {
				color: "#f1c40f", 
				inputType: "checkbox",
				inputChecked: false
			},
			"Item C": {
				color: "#2ecc71",
				inputType: "checkbox",
				inputChecked: false
			},
			"Item D": {
				color: "#3498db",
				inputType: "checkbox",
				inputChecked: false
			},
			"Item E": {
				color: "#7f8c8d",
				inputType: "checkbox",
				inputChecked: true
			}
		},
		footer: {
			label: "+ New Item",
			inputPlaceholder: "Input item name",
			inputType: "text",
		},*/
		/*onTextInput: function(key, item, value){
		window.alert("onTextInput: " + key + " - " + value );
		if( key == 'footer' ) {
			console.log(this);
			this.getHoverDropdownBox().addItem( value, {
				color: "#34495e",
				type: "checkbox",
				selected: true
			});
		}
		footer: {

		},		
	}*/
function init(){
	$('#idbtnestado').appendHoverDropdownBox({
		items: {

			"vencido": {
				color: "#e74c3c",label:xG.lang("vencido")
			},
			"cancelado": {
				color: "#f1c40f",label:xG.lang("cancelado")
			},
			"efectuado": {
				color:"#2ecc71",label:xG.lang("efectuado")
			}
		},
		onLabelClick: function(key, item){
			//item.checked( !item.checked() );
			//xSeg.setLlamadaEstado({estado:key, });
			var xID	= $("#idclave").val();
			xSeg.setEstadoDeCompromiso({clave:xID, estado:key});
		},
		onChange: function(key, item, value){					
			//window.alert("onChange: " + key + " - " + value );
		}
	});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
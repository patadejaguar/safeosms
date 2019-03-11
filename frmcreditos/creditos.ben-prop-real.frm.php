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
$xHP		= new cHPage("TR.PROPIETARIO / BENEFICIARIO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);



$xHP->init();

$xFRM		= new cHForm("frmbenpropreal", "./"); $xSel	= new cHSelect(); $xTxt	= new cHText();
$xFRM->setNoAcordion();
$xFRM->setTitle($xHP->getTitle());

$msg		= "";
//$xFRM->addJsBasico();
if($credito > DEFAULT_CREDITO){
	$xCred		= new cCredito($credito);
	$xCred->init();
	$persona	= $xCred->getClaveDePersona();
	$xFRM->addSeccion("idprops", "TR.Propietarios Reales");
	
	$xTbl		= new cHTabla("idtblrels");$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText(); $xText->setDivClass(""); $xChk->setDivClass("");
	$xBtn		= new cHButton(); 
	$xUl		= new cHUl("idlistpr1", "ul", "tags green");
	$xUl->setTags("");
	$xUl->li($xBtn->getBasic("TR.Guadar", "jsGuardarPropietario()", $xBtn->ic()->GUARDAR, "idguardar1", false, true), "");
		
	$xTbl->initRow();
	$xTbl->addTD($xText->getDeNombreDePersona("idpersona1"));
	
	$xFRM->OHidden("idtipoderelacion1", PERSONAS_REL_PROP_REAL);
	$xTbl->addTD($xHSel->getListaDeTiposDeParentesco("idtipodeparentesco1")->get("")  );
	$xTbl->addRaw("<td class='toolbar-24'>". $xUl->get() . "</td>" );
	$xTbl->endRow();
	$xFRM->addHElem($xTbl->get());
		
	//propietarios reales
	$xFRM->endSeccion();
	$xFRM->addSeccion("idprovs", "TR.Proveedores de recursos");
	//proveedor de recursos.
	$xTbl		= new cHTabla("idtblprov");$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText(); $xText->setDivClass(""); $xChk->setDivClass("");
	$xBtn		= new cHButton(); 
	$xUl		= new cHUl("idlistpr", "ul", "tags blue");
	$xUl->setTags("");
	$xUl->li($xBtn->getBasic("TR.Guadar", "jsGuardarProveedor()", $xBtn->ic()->GUARDAR, "idguardar2", false, true), "");
	$xTbl->initRow();
	$xTbl->addTD($xText->getDeNombreDePersona("idpersona2"));
	$xFRM->OHidden("idtipoderelacion2", PERSONAS_REL_PROV_RECURSOS);
	$xTbl->addTD($xHSel->getListaDeTiposDeParentesco("idtipodeparentesco2")->get("")  );
	$xTbl->addRaw("<td class='toolbar-24'>". $xUl->get() . "</td>" );
	$xTbl->endRow();
	$xFRM->addHElem($xTbl->get());
		
	
	$xFRM->endSeccion();
	$xFRM->addSeccion("idlista", "TR.Lista de Personas");
	$xFRM->addHTML("<div id='ListaDeRelaciones'></div>");
	
	$xFRM->OHidden("idcredito", $credito);
	$xFRM->OHidden("idpersona", $persona);
	
	$xFRM->endSeccion();
	
} else {
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
}
$xHG	= new cHGrid("aml_alerts");
echo $xFRM->get();
//$jxc ->drawJavaScript(false, true);
?>
<link href="../css/jtable/lightcolor/orange/jtable.min.css" rel="stylesheet" type="text/css" />
<script src="../js/jtable/jquery.jtable.js" type="text/javascript"></script>

<script>
var xPer	= new PersGen();


$(document).ready(function () {
	var idxpersona		= "<?php echo setNoMenorQueCero($persona); ?>";
	var idxcredito		= "<?php echo setNoMenorQueCero($credito); ?>";
	
	//alert(session(ID_PERSONA));
    $('#ListaDeRelaciones').jtable({
        title: '',
        actions: {
            listAction: '../svc/referencias.svc.php?out=jtable&persona=' + idxpersona + "&documento=" + idxcredito

        },
        fields: {
            clave: {
                key: true,
                list: false
            },
            tipo_de_relacion : {
                title: 'Relacion',
                width: '20%'
            },
            nombre_completo: {
                title: 'Nombre',
                width: '30%'
            },
            ocupacion: {
                title: 'Ocupacion',
                width: '20%'
            },
            domicilio: {
                title: 'Domicilio',
                width: '30%'
            }            
        }
    });
    jsRefreshTable();
});

function jsGuardarProveedor(){
	var idpersona			= $("#idpersona").val();
	var iddocto				= $("#idcredito").val();
	var idrelacionado		= $("#idpersona2").val();
	var idtipoderelacion	= $("#idtipoderelacion2").val();
	var idtipodeparentesco	= $("#idtipodeparentesco2").val();
	var stat				= false;
	xPer.addRelacion({ persona : idpersona, relacionado : idrelacionado, tipo : idtipoderelacion, parentesco : idtipodeparentesco, depende : stat, documento: iddocto, callback : jsRefreshTable });
	$("#idpersona2").val(0);
}
function jsGuardarPropietario(){
	var idpersona			= $("#idpersona").val();
	var iddocto				= $("#idcredito").val();
	var idrelacionado		= $("#idpersona1").val();
	var idtipoderelacion	= $("#idtipoderelacion1").val();
	var idtipodeparentesco	= $("#idtipodeparentesco1").val();
	var stat				= false;
	xPer.addRelacion({ persona : idpersona, relacionado : idrelacionado, tipo : idtipoderelacion, parentesco : idtipodeparentesco, depende : stat, documento: iddocto,  callback : jsRefreshTable });
	$("#idpersona1").val(0);
}
function jsRefreshTable(){ 
	$('#ListaDeRelaciones').jtable('load');
}
function onCloseVentanaRelaciones(){ jsRefreshTable(); }

</script>
<?php
$xHP->fin();
?>
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
	include_once("../vendor/autoload.php");
	
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.CLIENTES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

/*function jsaImportPersona($id){
	
}
$jxc ->exportFunction('jsaImportPersona', array('idinterno'), "#idavisos");
$jxc ->process();*/

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
$forcesync		= parametro("sync", false, MQL_BOOL);



$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();


$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
$xFRM->OButton("TR.IMPORTAR", "jsEjecutarImport()", $xFRM->ic()->EJECUTAR);


//if($forcesync == true){
	
	//use  PHPOnCouch\CouchClient;
	//$client		= new PHPOnCouch\CouchClient("http://pruebas:pruebas@localhost:5984","safeosms");
	$xDB	= new cCouchDB();
	$xTT	= new cHTabla("", "listado");
	$sem	= 0;  
	
	$xDB->getCnn();
	
	$xPC	= new cCreditosPreclientes();
	$data 	= $xDB->getTablaNoSync("preclientes");
	
	$xFRM->addSeccion("idnclientes", "TR.IMPORTAR CLIENTES");
	
	$xTT->addTH("TR.NOMBRE");
	$xTT->addTH("TR.PRIMER_APELLIDO");
	$xTT->addTH("TR.SEGUNDO_APELLIDO");
	$xTT->addTH("TR.CORREO_ELECTRONICO");
	
	$xTT->addTH("TR.IMPORTAR VIVIENDA");
	$xTT->addTH("TR.IMPORTAR ACTIVIDAD");
	$xTT->addTH("TR.IMPORTAR CREDITO");
	$xTT->addTH("TR.OPCION");
	$xChk	= new cHCheckBox();
	$xImg	= new cHImg();
	
	foreach ($data as $obj){
		//
		$v	= $obj->value;
		//$xPC->add($v->primerapellido, $v->segundoapellido, $v->nombre, $v->telefono, $v->email, $v->pagos, $v->frecuencia, $v->monto);
		//$bid	= base64_encode($data)
		$idp	= $xDB->getCleanID($v->_id);
		
		if($sem == 1){
			$sem	= 0;
			$xTT->initRow("trOdd", " id='tr-$idp' ");
		} else {
			$xTT->initRow("", " id='tr-$idp' ");
			$sem	= 1;
		}
		
		
		$xTT->addTD($v->nombre);
		$xTT->addTD($v->primerapellido);
		$xTT->addTD($v->segundoapellido);
		$xTT->addTD($v->email);
		
		if(setNoMenorQueCero($v->vivienda_codigopostal)>0){
			$xTT->addTD($xImg->get24("check.png"), " class='success' ");
		} else {
			$xTT->addTD($xImg->get24("busy.png"), " class='error' ");
		}
		//$xTT->addTD($v->vivienda_codigopostal);
		if(setNoMenorQueCero($v->ae_ingresomensual)>0){
			$xTT->addTD($xImg->get24("check.png"), " class='success' ");
		} else {
			$xTT->addTD($xImg->get24("busy.png"), " class='error' ");
		}
		//$xTT->addTD(getFMoney($v->ae_ingresomensual));
		//$xTT->addTD(getFMoney($v->monto));
		if(setNoMenorQueCero($v->monto)>0){
			$xTT->addTD($xImg->get24("check.png"), " class='success' ");
		} else {
			$xTT->addTD($xImg->get24("busy.png"), " class='error' ");
		}
		$xChk->setDivClass("");
		
		$xChk->setOnClick("jsAlimentarLista('" . $v->_id . "', this, '$idp')");
		
		$xTT->addTD( $xChk->get("", ""), " class='toolbar-24' id='tool-$idp' " );
		
		$xTT->endRow();
		
		//$xTT->addTD();
		//var_dump($v);
		//print_r($v);
		//$properties = get_object_vars($v);
		//print_r($properties);
		//var_dump($properties);
		/*  
  public '_id' => string 'preclientes:patadejaguar@gmail.com:1516665558' (length=45)
  public '_rev' => string '47-b2e0dac1924946c5b1ded44904433c57' (length=35)
  public 'createtime' => int 1516742927
  public 'nombre' => string 'Luis' (length=4)
  public 'primerapellido' => string 'Balam' (length=5)
  public 'segundoapellido' => string 'Gonzalez' (length=8)
  public 'idpoblacional' => string 'BAGL810822HCCLNS04' (length=18)
  public 'email' => string 'patadejaguar@gmail.com' (length=22)
  public 'telefono' => string '9811098164' (length=10)
  public 'idtemp' => 
    object(stdClass)[26]
      public 'entidad1' => string '' (length=0)
      public 'entidad2' => string '' (length=0)
  public 'synctime' => int 0
  public 'user' => int 0
  public 'tabla' => string 'preclientes' (length=11)
  public 'vivienda_calle' => string 'Benito Juarez' (length=13)
  public 'vivienda_numero' => string '451' (length=3)
  public 'vivienda_referencia' => string 'Entre Pololo y Metros' (length=21)
  public 'vivienda_codigopostal' => int 97206
  public 'vivienda_coordinadas' => int 0
  public 'vivienda_colonia' => string 'Juarez' (length=6)
  public 'idtemp_relacionado' => string '' (length=0)
  public 'id_relacionado' => int 0
  
  public 'pagos' => int 24
  public 'frecuencia' => string '30' (length=2)
  public 'producto' => string '2012' (length=4)
  public 'monto' => int 45000
  public 'fecha' => int 0
  
  public 'ae_ingresomensual' => int 4500
  public 'ae_empresa' => string 'La empresa de Prueba' (length=20)
  public 'ae_descripcion' => string 'Actividad de Prueba' (length=19)
  public 'ae_fechaingreso' => string '2018-01-24T06:00:00.000Z' (length=24)
  public 'vivienda_coordenadas' => string '21.003895999999997,-89.6170257' (length=30)		
		*/
	}
	$xFRM->addHElem($xTT->get());
	
	$xFRM->endSeccion();
//}


echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var imps	= {};

function jsGoPanel(id){
	//xG.w({url: "../frmcreditos/creditos-preclientes.panel.frm.php?clave=" + id, tab:true});
}
function jsAlimentarLista(id, obj, idx){
	if (obj.checked == true) {
		imps[idx]	= id;
	} else {
		delete imps[idx];
	}
}
function jsEjecutarImport(){
	xG.confirmar({
		msg : "MSG_CONFIRMA_IMPORTAR", 
		callback : function(){
			for(idx in imps){
				var idmx	= imps[idx];
				//alert(idmx + " --- Imp --- " + idx);
				xG.spinInit();
				xG.svc({url: "app-sync.svc.php?action=mql.add&tipo=personas&clave=" + idmx,
					callback : function(res){
						xG.spinEnd();
						xG.alerta({msg: res.message});
						$("#tool-" + idx).html(res.persona);
						xG.markTR({src:"#tr-" + idx});
					}
				});
			}
		}
		});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
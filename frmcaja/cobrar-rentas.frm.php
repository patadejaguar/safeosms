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
$xHP		= new cHPage("TR.COBRO DE RENTA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
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
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");
$idletra		= parametro("idperiodo", 0, MQL_INT);

$xHP->init();

$xFRM		= new cHForm("frmcobrarrentas", "./", "frmcobrarrentas");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

if($credito <= DEFAULT_CREDITO){
	//$xFRM->addCreditBasico();
	//$xFRM->addSubmit();
	$xHP->goToPageX("../frmcreditos/buscar-creditos.frm.php?tipoensistema=" . SYS_PRODUCTO_ARREND . "&next=../frmcaja/cobrar-rentas.frm.php");
} else {
	$xFRM->addCerrar();
	
	
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		//llevar a cobro
		$xFRM->addHElem( $xCred->getFichaMini() );
		$xFRM->addFechaRecibo();
		$xFRM->OHidden("credito", $credito);
		
		
		if($action == SYS_NINGUNO){
			
			$xFRM->OHidden("idperiodo", 0);
			$xFRM->OHidden("monto", 0);
			$xFRM->OHidden("persona", $xCred->getClaveDePersona() );
			
			$xFRM->addCobroBasico();
			$xFRM->addObservaciones();
			
			$xDic->OTable()->setKeyField("periodo");
			$xDic->OTable()->setKey(0);
			$xDic->OTable()->setWithMetaData();
			
			$xDic->OTable()->OButton("TR.COBRO", "jsGoToForm(" . HP_REPLACE_ID . ")", $xFRM->ic()->CAJA);
			//Agregar Leasing
			$xFRM->addHElem( $xDic->getLeasingTablasDeRenta($xCred->getClaveDeCredito(), true, true) );
			$xFRM->setAction("../frmcaja/cobrar-rentas.frm.php?action=" . MQL_ADD, true);
			
		} else {
			//setLog(" $persona, $credito, $idletra ");
			
			$xParcs	= new cParcialidadDeCredito($persona, $credito, $idletra);
			if($xParcs->init() == true){
				$xFRM->addAvisoRegistroOK();
			} else {
				$xFRM->addAvisoRegistroError($xParcs->getMessages());
			}
		}
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>
var xG	= new Gen();

function jsGoToForm(idxx){
	var idx	= "#tr-leasing_rentas-" + idxx;
	var dd	= xG.getMetadata(idx);
	//console.log(dd);
	var idmonto		= dd.total;
	var montoLetra	= flotante(dd.total);
	var claveSocio	= $("#persona").val();
	var credito		= $("#credito").val();
	var parcialidad	= entero(dd.periodo);
	var tipoPago	= $("#idtipo_pago").val();
	var idnotas		= $("#idobservaciones").val();
	var banco		= 0;
	var fdeposit	= $("#idfechaactual").val();
	
	if(idmonto > 0){
		xG.confirmar({msg : "MSG_CONFIRMA_COBRO",
			title: "Total : " + getFMoney(idmonto) + " " + CNF.moneda.iso  + "",
			callback: function(){ 
				$("#idperiodo").val(dd.periodo);
				$("#monto").val(idmonto);
				//$("#frmcobrarrentas").submit();
				
				if(flotante(montoLetra) > 0 ){
					var url	= "../frmcaja/frmpagoprocesado.php?out=json&p=" + claveSocio + "|" + credito + "|" + parcialidad + "|" + montoLetra + "|plc|" + tipoPago + "|" + banco + "|" + fdeposit + "&procesar=automatico&notas=" + idnotas;
					xG.svc({
						url : url,
						callback : function(data){
							if (data.error == true){
								//xG.alerta({msg:data.message, nivel:"error"});
								xG.aviso({message: "La parcialidad " + parcialidad + " del Credito " +  credito + " no se cobra"});
								$("#options-" + idxx).parent().addClass("tr-error");
							} else {
								$("#options-" + idxx).parent().addClass("tr-pagar");
								var xHTML	= "<ul class=\"tags blue\"><li><a onclick=\"var xR=new RecGen();xR.formato(" + data.recibo + ");\"><i class=\"fa fa-print\"></i><span>Imprimir</span></a></li></ul>";
								$("#options-" + idxx).html(xHTML);
								xG.notificar({message: "MSG_TAREA_OK", clase: "ok"});
								//Eliminar cache
								session(credito +  "." + parcialidad, null);								
							}
						}
					});

				} else {
					xG.alerta({message: "La parcialidad " + parcialidad + " del Credito " +  credito + " no se cobra por Monto " + montoLetra, type:"warn"});
				}

				
			}
		});
	}
}
</script>
<?php
$xHP->fin();
?>
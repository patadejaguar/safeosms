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
$jxc 		= new TinyAjax();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa = parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);

$xFRM		= new cHForm("frm", "./");
$xCB		= new cHCobros();
$xHSel		= new cHSelect();
$xEmp		= new cEmpresas($empresa);
if($xEmp->init()== true){
	$xFRM->addHElem($xEmp->getFicha());
	$xT			= new cTabla($xLi->getListadoDeIncidenciasAhorro($empresa));
	$Tool		= "PHP::\"<input onchange='jsSumarAhorro(this)' value='\" . \$rw[\"ahorro\"] . \"' type='number' id='id-_REPLACE_ID_' class='mny' />\";";
	$xT->addEspTool($Tool);
	$xHP->init();
	$xT->setWidthTool("200px");
	$xT->setWithMetaData();
	$xT->setFootSum(array(
			2 => "ahorro"
	));
	
	$xFRM->OButton("TR.Guardar Ahorro", "jsConfirmaGuardarAhorro()", $xFRM->ic()->GUARDAR);
	$xFRM->OButton("TR.Cobranza del dia", "jsGetReportesEmitidos()", $xFRM->ic()->REPORTE);
	$xFRM->addCerrar();
	$xFRM->addFecha();
	$xFRM->addHElem( $xCB->get(false, "", "", false) );
	$xFRM->addHElem( $xHSel->getListaDeCuentasBancarias("", true)->get("TR.Banco de Deposito", true) );
	//$xFRM->addMonto();
	$xFRM->OHidden("idempresa", $empresa);
	$xFRM->OHidden("idsocio", $xEmp->getClaveDePersona());
	$xFRM->addObservaciones();
	$xFRM->addHTML($xT->Show());
}
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>
<script>
var xG		= new Gen();
var xCapt	= new CaptGen();
var xRec	= new RecGen();
var xBan	= new BanGen();
function jsConfirmaGuardarAhorro(){
	xG.confirmar({callback : jsGuardarAhorro, msg : "MSG_CONFIRM_SAVE"})
}
function jsGuardarAhorro(){
	var size		= $("#id-frm").find("input[class=mny]").size();
	var ff			= $("#idfechaactual").val();
	var fpago		= $("#idtipo_pago").val();
	var idbanco		= $("#idcodigodecuenta").val();
	var ixempresa	= $("#idempresa").val();
	var ixpersona	= $("#idsocio").val();
	var idnotas		= $("#idobservaciones").val();
	
	xG.spinInit();
	$("#id-frm").find("input[class=mny]").each(function(index){
		//index			= index+1;
		var idmonto		= flotante(this.value);
		var idx			= String(this.id).split("-");
		var idpersona	= entero(idx[1]);
		if(idmonto > 0 && idpersona > 0){
			xCapt.setNuevoDepositoVista({ persona : idpersona, monto : idmonto, forma_de_pago : fpago, cuenta_bancaria : idbanco, fecha : ff, empresa : ixempresa, observaciones : idnotas });
		} else {
			xG.alerta({ msg : "Monto omitido " + idmonto , type : "warn" });
		}
		
		if((index+1) == size){
			var mmsuma	= $("#idsum-ahorro").val();
			xBan.setNuevoDeposito({cuenta : idbanco, monto : mmsuma, persona : ixpersona, fecha : ff, observaciones : idnotas });
						
			setTimeout("setEndGuardado()", (size*SYS_RETARDO));
		}
	});
		
}
function setEndGuardado(){
	jsGetReportesEmitidos();
	xG.spinEnd();	
}
function jsGetReportesEmitidos(){
	var ixempresa	= $("#idempresa").val();
	var ff			= $("#idfechaactual").val();
	xRec.getReporteEmitidos({ empresa : ixempresa, desde : ff, hasta : ff });
}
function jsSumarAhorro(ixd){
	var size		= $("#id-frm").find("input[class=mny]").size();
	var suma		= 0;
	$("#id-frm").find("input[class=mny]").each(function(index){
		index		= index+1;
		suma		+= flotante(this.value);
		if(index >= size){
			xG.alerta({ msg : "Monto " + suma , info : "Numero " + index, type : "warn" });
			$("#sum-ahorro").html(getFMoney(suma));
			$("#idsum-ahorro").val(suma);
		}
	});
		
}
</script>

<?php
$xHP->fin();
?>
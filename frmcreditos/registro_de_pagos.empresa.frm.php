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
$xHP->setIncludes();

$jxc = new TinyAjax();
function jsaSavePago($empresa, $monto, $periodo, $periocidad, $idAnno, $idMes, $idDia){
	$xEmp		= new cEmpresas($empresa);
	$oficial	= getUsuarioActual();
	$fecha		= "$idAnno-$idMes-$idDia"; //fechasys();
	$xEmp->addOperacion($monto, $periodo, $periocidad, $fecha, -1, $oficial);
}
function jsaGetMontoDelPeriodo($empresa, $periodo, $periocidad){
	if(intval($periodo) > 0){
		$xEmp		= new cEmpresas($empresa);
		$xEmp->init();
		return $xEmp->getMontoDelPeriodo($periodo, $periocidad);
	}
}

function jsaGetEmpresa($empresa, $periocidad){
	$xEmp		= new cEmpresas($empresa);
	$xEmp->init();
	$periodo	= 0;
	$dias		= 24*60;
	$xEmp->init();
	$ctrl		= "<label for=\"idperiodo\">Periodo</label><input type=\"number\" id=\"idperiodo\" onchange=\"jsaGetMontoDelPeriodo()\" onblur=\"jsaGetMontoDelPeriodo()\" />";
	$xF		= new cFecha(0);
	//$observaciones	.= " -- $periodo";
	$xEmp		= new cEmpresas($empresa); $xEmp->init();
	$periodo	= ( intval($xEmp->getPeriodo()) == 0 ) ? $periodo : intval($xEmp->getPeriodo());
	
	//$observaciones	.= "$fecha";
	switch($periocidad){
	    case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
		//buscar lunes
		$xSel	= $xF->getSelectSemanas("idPeriodo", $periodo);
		$xSel->addEvent("jsaGetMontoDelPeriodo()", "onchange");
		$xSel->addEvent("jsaGetMontoDelPeriodo()", "onblur");
		$xSel->setEnclose(false);
		$ctrl	= $xSel->get("idPeriodo", "Periodo", $periodo);
		break;
	    case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
		$xSel	= $xF->getSelectQuincenas("idPeriodo", $periodo);
		$xSel->addEvent("jsaGetMontoDelPeriodo()", "onchange");
		$xSel->addEvent("jsaGetMontoDelPeriodo()", "onblur");
		$xSel->setEnclose(false);
		$ctrl	= $xSel->get("idPeriodo", "Periodo", $periodo);
		break;
	    case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
		
		break;
	    case CREDITO_TIPO_PERIOCIDAD_DECENAL:
		
		break;
	}
	return $ctrl;
}
$jxc ->exportFunction('jsaSavePago', array('idempresa', 'idMonto', 'idPeriodo', 'idperiocidadpagos', 'idelanno98', 'idelmes98', 'ideldia98'), "#iddatos_pago");
$jxc ->exportFunction('jsaGetEmpresa', array('idempresa', 'idperiocidadpagos'), "#divperiodo");
$jxc ->exportFunction('jsaGetMontoDelPeriodo', array('idempresa', 'idPeriodo', 'idperiocidadpagos'), "#idMonto");

$jxc ->process();

echo $xHP->getHeader();

$jsb	= new jsBasicForm("frm");


echo $xHP->setBodyinit();

//$oFRM		= new cHForm("frmTemplate", "./");

//echo $oFRM->get();

echo $xHP->setBodyEnd();
?>
<!-- HTML content -->
<!-- Start Formoid form-->
<form class="formoid-default" name="frm" id="frm" title="Registro de Fichas de Pago de Empresas" method="post">
	<div class="element-title" ><h2 class="title">Registro de Depositos</h2></div>
	<div class="element-select" >
		<label class="title">Fecha de Pago<span class="required">*</span></label>
		<?php
		$xF	= new cFecha(98);
		echo $xF->show(true, FECHA_TIPO_OPERATIVA);
		?>
	</div>
	<div class="element-select" ><label class="title">Empresa<span class="required">*</span></label>
		<?php
			$xEmp	= new cSocios_aeconomica_dependencias();
			$xSel	= new cSelect("idempresa", "idempresa", $xEmp->get() );
			$xSel->addEvent("onchange", "jsaGetEmpresa");
			
			echo $xSel->get();
		?>
	</div>
	<div class="element-select" >
		<label class="title">Periocidad<span class="required">*</span></label>
	<?php
	$sqlSc	= "SELECT
			    `creditos_periocidadpagos`.`idcreditos_periocidadpagos`,
			    `creditos_periocidadpagos`.`descripcion_periocidadpagos`
		    FROM
			    `creditos_periocidadpagos` `creditos_periocidadpagos`
		    WHERE
			    (`creditos_periocidadpagos`.`idcreditos_periocidadpagos` !=99) ";
	    $xTP 	= new cSelect("periocidadpagos", "idperiocidadpagos", $sqlSc);
	    $xTP->addEvent("onchange", "jsaGetEmpresa");
	    $xTP->setOptionSelect(SYS_NINGUNO);
	    $xTP->addEspOption(SYS_NINGUNO);
	    $xTP->SetEsSql();
	    $xTP->show(false);
	?>
	</div>
	<div class="element-select" id="divperiodo">
		<label class="title">Periodo</label>
		<input type="number" name="idPeriodo" id="idPeriodo" onchange="jsaGetMontoDelPeriodo()" onblur="jsaGetMontoDelPeriodo()" /></div>
	<div class="element-text" ><label class="title">Monto</label><input type="number" name="idMonto" id="idMonto" /></div>
	
	<div class="element-submit" ><input type="button" value="Guardar" onclick="jsSavePago()" />
	<input type="button" value="Estado de Cuenta" onclick="jsPrintEstadoCuenta()" /></div>

</form>

<!-- Stop Formoid form-->
<?php $jxc ->drawJavaScript(false, true); $jsb->show(); ?>
<script language="javascript">
	function jsSavePago(){
		var sip	= confirm("Desea Agregar el Pago a favor de la Empresa?");
		if (sip == true) {
			jsaSavePago();
			$('#frm').each (function(){
				this.reset();
			});
		}
	}
	function jsPrintEstadoCuenta(){
		var idemp	= $("#idempresa").val();
		var idper	= $("#idPeriodo").val();
		var perio	= $("#idperiocidadpagos").val();
		var wf		= new Gen();
		wf.w({
			url : "../rptempresas/empresas.movimientos.rpt.php?empresa=" + idemp + "&periodo=" + idper + "&periocidad=" + perio,
			
			});
	}
</script>
<?php
$xHP->end();
?>
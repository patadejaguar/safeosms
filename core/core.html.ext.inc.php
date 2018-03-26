<?php
use Enhance\Language;
use Respect\Validation\Exceptions\PrivateAbstractNestedException;
use Dompdf\Dompdf;
//use Enhance\Language;
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package commons
 * 
 */
	include_once("core.config.inc.php");
	include_once("entidad.datos.php");
	include_once("core.init.inc.php");
	include_once("core.deprecated.inc.php");
	
	include_once("core.db.inc.php");
	include_once("core.db.dic.php");
	include_once("core.lang.inc.php");
	include_once("core.fechas.inc.php");
	include_once("core.html.inc.php");
	
	@include_once("../libs/PHPExcel.php");
	
	@include_once("../libs/dompdf/autoload.inc.php");
	
	
class cHDicccionarioDeTablas {
	private $mLimitRecords		= 20;
	private $mItems				= 0;
	private $mOTable			= null;
	
	function __construct(){
		$this->mOTable	= new cTabla("");
	}
	function getCreditosPorAutorizar($fecha, $persona = false, $titulo = ""){
		$this->mItems	= 0;
		$xD				= new cFecha();
		$xL				= new cSQLListas();
		$ic				= new cHButton();
		$fecha 			= $xD->getFechaISO($fecha);
		$sql			= $xL->getListaDeCreditosEnProceso(EACP_PER_SOLICITUDES, CREDITO_ESTADO_SOLICITADO, false, false, false, false, false, false, $persona);
		$xT				= new cTabla($sql, 3);
		//$xT->setKeyField("numero_de_solicitud");
		if($persona>0){	$xT->setKey(1);}
		$xT->setEventKey("var xC=new CredGen(); xC.goToPanelControl");
		$xT->OButton("TR.Autorizar", "var xC=new CredGen(); xC.getFormaAutorizacion(" . HP_REPLACE_ID . ")", $ic->ic()->OK);
		$html			= $xT->Show($titulo);
		$this->mItems	= $xT->getRowCount();
		return $html;
	}
	function getCreditosPorMinistrar($fecha, $persona = false, $titulo = ""){
		$this->mItems	= 0;
		$xD				= new cFecha();
		$xL				= new cSQLListas();
		$fecha 			= $xD->getFechaISO($fecha);
		$ic				= new cHButton();
		$sql			= $xL->getListaDeCreditosEnProceso(EACP_PER_SOLICITUDES, CREDITO_ESTADO_AUTORIZADO, true, false, false, false, false, false, $persona);
		$xT				= new cTabla($sql, 3);
		//$xT->setKeyField("numero_de_solicitud");
		if($persona>0){	$xT->setKey(1);}
		$xT->setWithMetaData();
		$xT->setEventKey("var xC=new CredGen(); xC.goToPanelControl");
		$xT->OButton("TR.MINISTRAR", "var xC=new CredGen();xC.getFormaMinistracion(" . HP_REPLACE_ID . ")", $ic->ic()->DINERO);
		
		$xT->setFootSum(array(
				5 => "monto_solicitado",
				14 => "monto_autorizado" 
		));
		//$xT->OButton("TR.Panel", "var xC=new CredGen(); xC.goToPanelControl(" . HP_REPLACE_ID . ")", $ic->ic()->CONTROL);
		$html			= $xT->Show($titulo);
		$this->mItems	= $xT->getRowCount();
		return $html;
	}
	function getLlamadas($fecha_inicial = false, $fecha_final = false, $De = 0 , $efectuadas = false, $canceladas = false, $vencidas = false){
		$xLi					= new cSQLListas();
		$sql 	= $xLi->getListadoDeLlamadas(false, getUsuarioActual(), $fecha_inicial, $fecha_final, $efectuadas, $canceladas, $vencidas);

		$td		= "";
		$xT		= new cTabla($sql, 3);
		$xT->setKeyField("idseguimiento_llamadas");
		$xT->setKeyTable("seguimiento_llamadas");
		$xT->OButton("TR.Cancelar", "var xSeg=new SegGen(); xSeg.setLlamadaCancelada(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->CERRAR);
		$xT->OButton("TR.Descartar", "var xSeg=new SegGen(); xSeg.setLlamadaEfectuada(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->OK);
		$xT->OButton("TR.Notas", "var xSeg=new SegGen(); xSeg.setAgregarNotaLlamada(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->REPORTE);
		
		$xT->setWithMetaData();
		/*$rs 	= $xQL->getDataRecord($sql);
	
		foreach ($rs as $rw){
		$control	= $rw["control"];
		$socio		= $rw["codigo"];
		$nombre		= htmlentities($rw["nombre"]);
		$credito	= $rw["numero_solicitud"];
		$estatus	= $rw["estatus_llamada"];
		$grupo		= $rw["grupo_relacionado"];
		$hora		= $rw["hora_llamada"];
		$tel1		= $rw["telefono_uno"];
		$tel2		= $rw["telefono_dos"];
	
		$select		= "	<select id='ids-$control' name='s-$control' onchange=\"jsSetAction($control)\">
		<optgroup label='Acciones'>
		<option value='set-notes'>Agregar Resultados de la Llamada</option>
		<option value='set-vivienda'>Actualizar Datos de Vivienda</option>
		</optgroup>
		<optgroup label='Herramientas'>
		<option value='set-edit'>Editar Informacion de la LLamada</option>
		<option value='add-compromiso'>Agregar Compromiso</option>
		<option value='add-llamada'>Agregar Llamada</option>
		<option value='add-memo'>Agregar Memo</option>
		<option value='add-notif-1'>Agregar Notificacion 1a</option>
		<option value='add-notif-2'>Agregar Notificacion 2a</option>
		<option value='add-notif-3'>Agregar Notificacion 3a</option>
		<option value='add-notif-e'>Agregar Notificacion Extrajudicial</option>
		</optgroup>
		<optgroup label='Informacion'>
		<option value='info-moral'>Obtener Informacion Moral</option>
	
		<option value='info-llamadas'>Obtener Reporte de llamadas</option>
		<option value='info-compromisos'>Obtener Reporte de Compromisos</option>
		<option value='info-notificaciones'>Obtener Reporte de Notificaciones</option>
		<option value='info-creditos'>Obtener Estado de Cuenta de Creditos</option>
		</optgroup>
		</select>";
		switch ($estatus){
		case "vencido":
		$select		= "";
		break;
		case "efectuado":
		$select		= $rw["observaciones"];
		break;
		case "cancelado":
		$select		= $rw["observaciones"];
		break;
		default:
		break;
		}
		$td .= "	<tr id=\"tr-$control\">
		<td>$socio<input type='hidden' id='socio-$control' value='$socio' /><br />
		$credito<input type='hidden' id='credito-$control' value='$credito' />
		<!-- $control -->
		<input type='hidden' id='grupo-$control' value='$grupo' /></td>
		<td>$nombre<br />
		<a>" . $tel1 . " &nbsp;&nbsp;|&nbsp;&nbsp; " . $tel2 . "</a></td>
		<td class=\"$estatus\">$hora</td>
		<td id=\"td-$control\">
		$select
		</td>
	
		</tr>";
		}
	
		return "<table width='100%' id='tbl-id'>
		<tbody>
		<tr>
		<th width=\"10%\">
		Socio<br />
		Credito
		</th>
		<th width=\"50%\">
		Nombre<br />
		Telefono Fijo &nbsp;&nbsp;|&nbsp;&nbsp; Telefono Movil
		</th>
		<th>Hora</th>
		<th width=\"30%\">
		Herramientas
		</th>
		</tr>
		$td
		</tbody>
		</table>";*/
		return $xT->Show();
	}
	function getNumeroItems(){ return $this->mItems; }
	function getLeasingTablasDeRenta($credito, $simple = false, $SoloActivos = false){
		$xLi	= new cSQLListas();
		
		
		$sql	= $xLi->getListadoDeLeasingPlanCliente($credito, $SoloActivos);
		$this->mOTable->setSQL($sql);
		
		//$xTabla	= new cTabla($sql);
		$this->mOTable->setOmitidos("id");
		$this->mOTable->setOmitidos("idleasing");
		$this->mOTable->setOmitidos("credito");
		$this->mOTable->setOmitidos("pagos");
		
		$this->mOTable->setFootSum(array(
				2 => "deducible",
				3 => "nodeducible",
				4 => "iva",
				5 => "total"
		));
		
		$this->mOTable->setNoFilas("");
		
		$this->mOTable->setColTitle("fecha", "FECHA_DE PAGO");
		$this->mOTable->setColTitle("periodo", "PLANPERIODORENTA");
		
		if($simple == true){
			$this->mOTable->setOmitidos("deducible");
			$this->mOTable->setOmitidos("nodeducible");
			
			$this->mOTable->setColTitle("total", "PLANMONTORENTA");
			
			
			
			$this->mOTable->setOmitidos("iva");
			$this->mOTable->setFootSum(array(2=>"total"));
			
		}
		return $this->mOTable->Show();
	}
	function getPlanDePagosOriginal($idcredito){
		$fahorro	= (MODULO_CAPTACION_ACTIVADO == true) ? "`creditos_plan_de_pagos`.`ahorro`," : "";
		
		$sql	= "SELECT   `creditos_plan_de_pagos`.`plan_de_pago` AS `clave`,
         `creditos_plan_de_pagos`.`clave_de_credito` AS `credito`,
         `creditos_plan_de_pagos`.`numero_de_parcialidad` AS `parcialidad`,
         `creditos_plan_de_pagos`.`fecha_de_pago`,
         `creditos_plan_de_pagos`.`capital`,
         `creditos_plan_de_pagos`.`interes`,
         `creditos_plan_de_pagos`.`impuesto`,
         `creditos_plan_de_pagos`.`otros`,
         `operaciones_tipos`.`descripcion_operacion` AS `otros_cargos`,
                 
         $fahorro
		`creditos_plan_de_pagos`.`total_c_otros` AS `original`,
         `creditos_plan_de_pagos`.`penas`,
         `creditos_plan_de_pagos`.`gtoscbza`,
         `creditos_plan_de_pagos`.`mora`,
		`creditos_plan_de_pagos`.`iva_castigos` AS `iva_otros`,
         `creditos_plan_de_pagos`.`descuentos`,
		
		`creditos_plan_de_pagos`.`total_c_castigos` AS `neto`,
 		`creditos_plan_de_pagos`.`saldo_inverso`
		FROM     `creditos_plan_de_pagos` INNER JOIN `operaciones_tipos`  ON `creditos_plan_de_pagos`.`otros_codigo` = `operaciones_tipos`.`idoperaciones_tipos` 
		WHERE    ( `creditos_plan_de_pagos`.`clave_de_credito` = $idcredito )";
		$this->mOTable->setOmitidos("credito");
		
		$this->mOTable->setSQL($sql);
		return $this->mOTable->Show();
	}
	function OTable(){ return $this->mOTable; }
}

/**
 * SAFE chart is a implement from open_flash_chart
 *
 */
class SAFEChart{
	private $mValues	= array();
	private $mValues2	= false;
	private $mLabels	= array();
	private $mTitle		= "";
	private $mColor		= false;
	function __construct(){

	}
	function setValues($values, $values2 = false){
		$this->mValues		= $values;
		$this->mValues2	= $values2;
	}
	/**
	 * Agrega los Titulos de la tabla
	 * @param array $title
	 */
	function setTitle($title){
		$this->mTitle	= $title;
	}
	function setLabels($labels){
		$this->mLabels = $labels;
	}

	function ChartPIE(){

		$iduser	= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"];

		$data 	= $this->mValues;
		$label	= $this->mLabels;


		$g = new graph();
		//
		// PIE chart, 60% alpha
		//
		//$g->set_swf_path(vIMG_PATH . "/tmp/");
		$g->pie(60,'#505050','{font-size: 10px; color: #404040;');
		//
		// pass in two arrays, one of data, the other data labels
		//
		$g->pie_values( $data, $label );
		//
		// Colours for each slice, in this case some of the colours
		// will be re-used (3 colurs for 5 slices means the last two
		// slices will have colours colour[0] and colour[1]):
		//
		if ( $this->mColor == false ){
			$lim = sizeof($data);
			$colorInit		= hexdec("d01f3c");
			$this->mColor	= array();

			for ($i = 0; $i < $lim; $i++){

				$colorInit			+= floor($i * rand(-255,255));
				$this->mColor[]		= "#" . dechex($colorInit);
			}
		}
		$g->pie_slice_colours( $this->mColor );

		$g->set_tool_tip( '#val#%' );

		$g->title( $this->mTitle, '{font-size:14px; color: #d01f3c}' );

		$x = $g->render();
		return $this->setWriteFile($x);
	}

	function Chart3DBAR($LimiteMaximo = 100, $titulo ="", $titulo2 = ""){
		$data 	= $this->mValues;
		$label	= $this->mLabels;

		$g = new graph();
		$g->title( $this->mTitle, '{font-size:16px; color: #FFFFFF; margin: 5px; background-color: #505050; padding:5px; padding-left: 20px; padding-right: 20px;}' );

		//$g->set_data( $data_1 );
		//$g->bar_3D( 75, '#D54C78', '2006', 10 );

		//$g->set_data( $data_2 );
		//$g->bar_3D( 75, '#3334AD', '2007', 10 );
		//Crea el Bar Blue
		$bar_blue = new bar_3d( 75, '#3334AD' );
		$bar_blue->key( $titulo, 10);
		$bar_blue->data	= $this->mValues;

		$g->data_sets[] = $bar_blue;
		if ( is_array($this->mValues2) ){
			$bar_blue2 = new bar_3d( 75, '#ff0000' );
			$bar_blue2->key( $titulo2, 10);
			$bar_blue2->data	= $this->mValues2;
			$g->data_sets[] = $bar_blue2;
		}

		$g->set_x_axis_3d( 12 );

		$g->x_axis_colour( '#909090', '#ADB5C7' );

		$g->y_axis_colour( '#909090', '#ADB5C7' );

		$g->set_x_labels( $this->mLabels );

		$g->set_y_max( $LimiteMaximo );

		$g->y_label_steps( 5 );
		//$g->set_y_legend( 'Open Flash Chart', 12, '#736AFF' );
		$x = $g->render();

		return $this->setWriteFile($x);
	}
	function setWriteFile($x){
		$iduser			= $_SESSION["log_id"];
		$tmpKey			= md5(date("Ymdhsi") . $iduser. getRndKey());
		//Abre Otro, lo crea si no existe
		$mFILE			= vIMG_PATH . "/tmp/chart-". $tmpKey . ".dat";
		$URIFil			= fopen($mFILE, "a+");
		@chmod($mFILE, 0666);
		@fwrite($URIFil, $x);
		@fclose($URIFil);
		return $mFILE;
	}

}
class cPanelDeReportesContables {
	private $mOFRM				= null;
	function __construct($ConPeriodos = true, $ConCuentas = true){
		$this->mOFRM	= new cHForm("reportescontable", "", "reportescontable", "");
		$this->mOFRM->addCerrar();
		$this->mOFRM->setFieldsetClass("fieldform frmpanel");
		$this->mOFRM->OButton("TR.Obtener Reporte", "jsGetReporte()", "reporte", "cmdgetreporte");
		if($ConPeriodos == true){ $this->addPeriodoInicial(); }
		if($ConCuentas == true){ $this->addCuentaInicial(); }

	}
	function addPeriodoInicial(){
		$xSel	= new cHSelect();
		$this->mOFRM->addDivSolo($xSel->getListaDeAnnos("idejercicioinicial")->get(false), $xSel->getListaDeMeses("idperiodoinicial")->get(false), "tx24", "tx24");
	}
	function addPeriodoFinal(){
		$xSel	= new cHSelect();
		$this->mOFRM->addDivSolo($xSel->getListaDeAnnos("idejerciciofinal")->get(false), $xSel->getListaDeMeses("idperiodofinal")->get(false), "tx24", "tx24");
	}
	function addMoneda(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem($xSel->getListaDeMonedas()->get("TR.Moneda", true) );
	}
	function addCuentaInicial(){
		$xTxt	= new cHText();
		$this->mOFRM->addHElem( $xTxt->getDeCuentaContable("idcuentainicial", ZERO_EXO));
	}
	function addCuentaFinal(){
		$xTxt	= new cHText();
		$this->mOFRM->addHElem( $xTxt->getDeCuentaContable("idcuentafinal", ZERO_EXO));
	}
	function addTipoDeCuentas(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem($xSel->getListaDeTiposDeCuentasContables("", true, SYS_TODAS)->get(true) );
	}
	function addNivelesDeCuentas(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem( $xSel->getListaDeNivelesDeCuentasContables("idniveldecuenta", true, SYS_TODAS)->get(true) );
	}
	function addEstadoDeMovimiento(){
		$xSel	= new cHSelect();
		$this->mOFRM->addHElem( $xSel->getListaDeEstadoMvtosDeCuentasContables()->get(true));
	}
	function render(){ return $this->mOFRM->get(); }
	function addFechaInicial($fecha = false){
		$xF	= new cFecha(0, $fecha);
		$this->mOFRM->ODate("idfechainicial", $xF->getDiaInicial(), "TR.Fecha Inicial");
	}
	function addFechaFinal($fecha = false){
		$xF	= new cFecha(0, $fecha);
		$this->mOFRM->ODate("idfechafinal", $xF->getDiaFinal(), "TR.Fecha Final");
	}
	function OFRM(){ return $this->mOFRM; }
	function addRangosDeNumero($final = 9999, $inicial = 0){
		$this->OFRM()->OMoneda("idnumeroinicial", $inicial, "TR.Numero Inicial");
		$this->OFRM()->OMoneda("idnumerofinal", $final, "TR.Numero Final");
	}
	function addTipoDePolizas(){
		$xSel	= new cHSelect();
		$sel	= $xSel->getListaDeTiposDePolizas();
		$sel->addEspOption(SYS_TODAS);
		$sel->setOptionSelect(SYS_TODAS);
		$this->mOFRM->addHElem($sel->get(true) );
	}
	function addVistaDePolizas(){
		$xSel	= new cHSelect("idvistapoliza");
		$title	= $this->OFRM()->l()->getT("TR.ver Poliza");
		$xSel->addOptions(array(
				SYS_TODAS => $this->OFRM()->l()->getT("TR.Completa"),
				SYS_DEFAULT => $this->OFRM()->l()->getT("TR.POR_DEFECTO"),
		));
		$this->mOFRM->addHElem($xSel->get("", $title) );
		$xSel =null;
	}
	function addListaDeReportes($tipo = "", $id=""){
		$xSel	= new cHSelect();
		$xRP	= $xSel->getListaDeReportes($id, $tipo);
		$xRP->setNoMayus();
		$this->mOFRM->addHElem($xRP->get(true));
		$xSel 	= null;
		$xRP	= null;
	}
}

class cPanelDeReportes {
	private $mFechaInicial		= "";
	private $mFechaFinal		= "";
	private $mTiposSalida		= "";
	private $mSucursales		= "";

	private $mCreditosFrecPagos	= "";
	private $mCreditosEstatus	= "";
	private $mCreditosProductos	= "";
	private $mTipo				= "";
	private $mForceRecibos		= true;
	private $mForceOperaciones	= false;
	private $mForceCredito		= false;
	private $mForceCajeros		= false;
	private $mForceSucursal		= true;

	private $mFiltro			= "";
	private $mStruct			= "";
	private $mTitle				= "";
	private $mSelectReports		= "";
	private $mJsVars			= "function jsGetReporte(){\r\n";
	private $mURL				= "\"\"";
	private $mHtml				= "";
	private $mConFecha			= true;
	private $mFooterBar			= "";
	private $mOFRM				= null;
	private $mLblR				= "";
	private $mControls			= "";
	private $mConEmpresa		= true;
	private $mFechaInicialVal	= false;
	private $mArrOpts			= array();
	public $OPTS_NOUSUARIOS		= "NOUSUARIOS";
	public $OPTS_NOCAJEROS		= "NOCAJEROS";
	public $OPTS_CREDSNOPERI	= "CREDITOSNOPERIOCIDAD";
	public $OPTS_CREDSNOSTAT	= "CREDITOSNOESTADO";

	function __construct($tipo = iDE_CREDITO, $filtro = "", $addList = true){
		$this->mTipo	= $tipo;
		$this->mFiltro	= $filtro;
		$xF				= new cFecha();
		$this->mOFRM	= new cHForm("frmpanel");
		//$this->mOFRM->setFieldsetClass("fieldform frmpanel");
		$this->mOFRM->OButton("TR.Obtener Reporte", "jsGetReporte()", $this->mOFRM->ic()->REPORTE, "cmdgetreporte", "green2");
		//$this->mOFRM->setFieldsetClass("fieldform frmpanel");
		$SqlRpt			= "SELECT * FROM general_reports WHERE aplica='" . $this->mFiltro . "' AND `estatus`=1 ORDER BY `descripcion_reports`,`order_index` ASC ";
		$cSRpt			= new cSelect("idreporte", "idreporte", $SqlRpt );
		$cSRpt->setEsSql();
		$cSRpt->setNoMayus();
		$cSRpt->addEvent("onblur", "if(typeof jsBlurListaDeReportes !='undefined'){ jsBlurListaDeReportes(); }");
		$this->mJsVars	.= "var idreporte	= $('#idreporte').val();\r\n";
		$lbl			= $this->mOFRM->l()->getT("TR.Nombre del Reporte");
		$this->mLblR	= "<label for='idreports'>$lbl</label>";
		$this->mSelectReports		= $cSRpt->get("", false);
		if($addList == true){
			$this->mOFRM->addDivSolo($this->mLblR, $this->mSelectReports, "tx14", "tx34");
		}
		if($this->mTipo == iDE_BANCOS OR $this->mTipo == iDE_AML OR $this->mTipo == iDE_CONTABLE){
			$this->mConEmpresa		= false;
		}
		if($tipo == iDE_CPOLIZA OR $tipo == iDE_OPERACION OR $tipo == iDE_RECIBO){
				
		} else {
			$this->mFechaInicialVal	= $xF->getDiaInicial();
		}
	}
	function addListReports(){ $this->mOFRM->addDivSolo($this->mLblR, $this->mSelectReports, "tx14", "tx34");	}
	function setSinUsuarios(){ $this->mArrOpts[$this->OPTS_NOUSUARIOS]	= true; }
	function setSinCajeros(){ $this->mArrOpts[$this->OPTS_NOCAJEROS]	= true; }
	function addOpciones($opts){ $this->mArrOpts[$opts]	= true; }
	function setConRecibos($force = true){ $this->mForceRecibos	= $force; }
	function setTitle($title){ $this->mTitle	= $title; }
	function setConOperacion($force = true){ $this->mForceOperaciones	= $force; }
	function setConCreditos($force = true){ $this->mForceCredito	= $force; }
	function setConCajero($force = true){ $this->mForceCajeros = $force; }
	function setConFechas($fechas = false){ $this->mConFecha = $fechas; }
	function setConSucursal($force = false){$this->mForceSucursal = $force;}
	function addFooterBar($html = ""){ $this->mFooterBar .= $html; }
	function OFRM(){ return $this->mOFRM; }
	function addControl($html = "", $id="", $jsVar="", $checkbox = false){
		$this->addjsVars($id, $jsVar, $checkbox);
		$this->mHtml	.= $html;
	}
	function addTipoDeCuentaDeCaptacion(){
		$xSel			= new cHSelect();
		$xOb			= $xSel->getListaDeTipoDeCaptacion();
		$xOb->addEspOption(SYS_TODAS);
		$xOb->setOptionSelect(SYS_TODAS);
		$this->OFRM()->addHElem( $xOb->get(true)  );
		$this->mJsVars	.= "var idtipodecuenta	= $('#idtipodecuenta').val();\r\n";
		$this->mURL		.= " + \"&producto=\" + idtipodecuenta ";
		//$this->mURL		.= " + \"&dependencia=\" + idempresa ";
	}
	function addProductoDeCuentaDeCaptacion(){
		$xSel			= new cHSelect();
		$xOb			= $xSel->getListaDeProductosDeCaptacion();
		$xOb->addEspOption(SYS_TODAS);
		$xOb->setOptionSelect(SYS_TODAS);
		$this->OFRM()->addHElem( $xOb->get(true)  );
		$this->mJsVars	.= "var idproductocaptacion	= $('#idproductocaptacion').val();\r\n";
		$this->mURL		.= " + \"&subproducto=\" + idproductocaptacion ";
		//$this->mURL		.= " + \"&dependencia=\" + idempresa ";
	}
	function get(){
		$xBtn			= new cHButton();
		$xFRM			= $this->mOFRM;
		$xF				= new cFecha();
		
		$xFRM->setTitle( $this->mTitle );
		if($this->mTipo == iDE_CAPTACION){
			$this->addTipoDeCuentaDeCaptacion();
			$this->addProductoDeCuentaDeCaptacion();
		}
		if($this->mConFecha == true){
			$xFRM->addHElem( $this->addFechaInicial() );
			$xFRM->addHElem( $this->addFechaFinal() );
		}
		switch ($this->mTipo){
			case iDE_CONTABLE:
				break;
		}
		if($this->mTipo == iDE_USUARIO){ $this->addOficialDeCredito();	}
		if($this->mConEmpresa == true){ $this->addEmpresasConConvenio();}
		if($this->mTipo == iDE_AML){
			$this->mForceRecibos = false;
		} else {
			if(MULTISUCURSAL == true AND ($this->mForceSucursal == true) ){
				$this->addSucursales(true);
			}
		}
		if($this->mTipo == iDE_CREDITO OR $this->mForceCredito == true){
			$this->addCreditosProductos();
			//frecuencia
			if(!isset($this->mArrOpts[$this->OPTS_CREDSNOPERI])){
				$this->addCreditosPeriocidadDePago();
			}
			//estatus
			if(!isset($this->mArrOpts[$this->OPTS_CREDSNOSTAT])){
				$this->addCreditosEstados();
			}
		}
		if($this->mTipo == iDE_CAPTACION){
			//TODO: Considerar
		}//&base
		if($this->mTipo == iDE_BANCOS){
			$this->addListadDeCuentasBancarias();
			$this->addTiposDeOperacionesBancarias();
		}
		if($this->mTipo == iDE_RECIBO OR $this->mForceRecibos == true){
			$this->addTipoDePago();
			$this->addTiposDeRecibos();
		}
		if(($this->mTipo == iDE_RECIBO) OR ($this->mForceRecibos == true) OR ($this->mForceCajeros == true)){
			if(!isset($this->mArrOpts[$this->OPTS_NOCAJEROS])){
				$this->addCajeros();
			}
			if(!isset($this->mArrOpts[$this->OPTS_NOUSUARIOS])){
				//$this->addUsuarios();
			}
		}
		if( $this->mTipo == iDE_OPERACION OR $this->mForceOperaciones == true){
			$this->addTipoDeOperacion();
		}
		$xFRM->addHElem($this->mStruct);
		$xFRM->addHElem($this->mHtml);
		$xFRM->addHElem($this->addTiposDeSalida());

		if($this->mFooterBar != ""){
			$xFRM->addFooterBar( $this->mFooterBar );
		}
		//Button
		return $xFRM->get();
	}
	function addOficialDeCredito($addControl = true){
		$xHS			= new cHSelect();
		$xS 			= $xHS->getListaDeOficiales("idoficial");
		$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idoficial	= $('#idoficial').val();\r\n";
		$this->mURL		.= " + \"&f700=\" + idoficial ";
		$this->mURL		.= " + \"&oficial=\" + idoficial ";
		if($addControl == true){
			$this->mStruct	.=  $xS->get("TR.Oficial", true);
		}
		return $xS;
	}
	function addCreditosProductos(){
		$xHS			= new cHSelect();
		$xSel			= $xHS->getListaDeProductosDeCredito("idtipo_de_convenio");
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$v		= $xSel->get("TR.Producto de Credito", true) ;

		$this->mURL		.= " + \"&producto=\" + idproducto "; //"&producto=" + producto
		$this->mURL		.= " + \"&convenio=\" + idproducto ";
		$this->mURL		.= " + \"&f5=\" + idproducto ";

		$this->mJsVars	.= "var idproducto	= $('#idtipo_de_convenio').val();\r\n";

		$this->mStruct	.= $v ;
	}
	function addCreditosPeriocidadDePago(){
		$xHS			= new cHSelect();
		$xSel	= $xHS->getListaDePeriocidadDePago("idperiocidad");
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idperiocidad	= $('#idperiocidad').val();\r\n";
		$this->mURL		.= " + \"&f1=\" + idperiocidad ";
		$this->mURL		.= " + \"&periocidad=\" + idperiocidad ";
		$this->mURL		.= " + \"&frecuencia=\" + idperiocidad ";

		$v		= $xSel->get("TR.Periocidad de Pago", true);
		$this->mStruct	.= $v ;
	}
	function addTipoDePago(){
		$xTipo	= new cHCobros();
		$xTipo->setOptions("<option value='" . SYS_TODAS . "' selected='selected'>TODAS</option>");
		$xTipo->setSelectOpt(SYS_TODAS);
		$this->mJsVars	.= "var idtipodepago	= $('#idtipo_pago').val();\r\n";

		$this->mURL		.= " + \"&tipopago=\" + idtipodepago ";
		$this->mURL		.= " + \"&tipodepago=\" + idtipodepago ";
		$this->mURL		.= " + \"&pago=\" + idtipodepago ";
		$v				= $xTipo->get(false, "", "", false);

		$this->mStruct	.= $v;
		return $v;
	}
	function addjsVars($id	= "", $geteq = "", $checkbox = false){
		$this->mJsVars	.= ($checkbox == false) ? "var $id	= $('#$id').val();\r\n" : "var $id	= $('#$id').prop('checked');\r\n";
		//TODO: Agregar Cuentas
		$this->mURL		.= " + \"&$geteq=\" + $id ";
	}
	function addSucursales($close = false){
		$xUser		= new cSystemUser();
		if(OPERACION_LIBERAR_SUCURSALES == false AND $xUser->getEsCorporativo() == false){
			$xTxt	= new cHText();
			$this->mSucursales	= $xTxt->getHidden("idsucursal",0, $xUser->getSucursal());
		} else {
			$xHS		= new cHSelect();
			$xS 		= $xHS->getListaDeSucursales("idsucursal");
			
			
			$xS->addEspOption(SYS_TODAS);
			$xS->setOptionSelect(SYS_TODAS);
			$xS->SetEsSql();
			$xS->setNoMayus();
			
			if($close == false){
				$this->mSucursales	= $xS->show();
			} else {
				$this->mSucursales	= $xS->get("TR.Sucursal", true);
			}
		}
		$this->mJsVars	.= "var idsucursal	= $('#idsucursal').val();\r\n";
		$this->mURL		.= " + \"&sucursal=\" + idsucursal ";
		$this->mURL		.= " + \"&s=\" + idsucursal ";

		$this->mStruct	.=	$this->mSucursales;
		return $this->mSucursales;
	}
	function addFechaInicial(){
		$xF						= new cFecha(0);
		$xDate			= new cHDate(0, $this->mFechaInicialVal, TIPO_FECHA_OPERATIVA);
		$xDate->setEsDeReporte();
		$xDate->setDivClass("tx4 tx18 blue");
		$this->mJsVars	.= "var fechaInicial	= $('#idfecha-0').val();\r\n";

		$this->mURL		.= " + \"&on=\" + fechaInicial ";
		$this->mURL		.= " + \"&fechainicial=\" + fechaInicial ";
		$this->mURL		.= " + \"&fechaMX=\" + fechaInicial ";

		return $xDate->get("TR.Fecha_Inicial");
	}
	function addFechaFinal($titulo = ""){
		/*$xF						= new cFecha(1);
		 $this->mFechaInicial	= $xF->show(true, TIPO_FECHA_OPERATIVA);*/
		$titulo			= ($titulo == "") ? "TR.Fecha_Final" : $titulo;
		$xDate			= new cHDate(1, false, TIPO_FECHA_OPERATIVA);
		$xDate->setEsDeReporte();
		$xDate->setDivClass("tx4 tx18 blue");
		$this->mJsVars	.= "var fechaFinal	= $('#idfecha-1').val();\r\n";

		$this->mURL		.= " + \"&off=\" + fechaFinal ";
		$this->mURL		.= " + \"&fechafinal=\" + fechaFinal ";

		return $xDate->get($titulo);
	}
	function addTiposDeSalida(){
		//<option value=\"html\">Pagina Web(www)</option>
		$this->mTiposSalida = "<div class='tx4 tx18'><label>Exportar Reporte Como</label>
			<select name=\"idtipodesalida\" id=\"idtipodesalida\">
				<option value=\"" . SYS_DEFAULT . "\" selected>Por Defecto(xml)</option>
				<option value=\"pdf\">Archivo PDF</option>
				<option value=\"xls\">Excel</option>
				<option value=\"csv\">Archivo Delimitado por comas (cvs)</option>

				<option value=\"txt\">Archivo de Texto(txt)</option>


			</select></div> ";

		$this->mJsVars	.= "var idtiposalida	= $('#idtipodesalida').val();\r\n";
		$this->mURL		.= " + \"&out=\" + idtiposalida ";

		return $this->mTiposSalida;
	}
	function addListadDeCuentasBancarias(){
		$xHS	= new cHSelect();
		$xSel	= $xHS->getListaDeCuentasBancarias("idcuentabancaria");
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idcuentabancaria	= $('#idcuentabancaria').val();\r\n";
		//Agregar Cuentas
		$this->mURL		.= " + \"&cuenta=\" + idcuentabancaria ";
		$this->mURL		.= " + \"&cuentabancaria=\" + idcuentabancaria ";
		$this->mStruct	.= $xSel->get("TR.Numero de Cuenta", true);
	}
	function addTiposDeOperacionesBancarias(){
		$xHOp			= new cHSelect();
		$this->mJsVars	.= "var idoperacionbancaria	= $('#idtipooperacionbanco').val();\r\n";
		//TODO: Agregar parametros de operacion bancaria
		$this->mURL		.= " + \"&operacion=\" + idoperacionbancaria ";
		$xSel			= $xHOp->getListaDeTiposDeOperacionesBancarias();
		$xSel->addEspOption(SYS_TODAS, "Todas");
		$xSel->setOptionSelect(SYS_TODAS);
		$this->mStruct	.= $xSel->get(true);
	}
	function addEmpresasConConvenio($agregar = true){
		$xHS	= new cHSelect();
		$xSel	= $xHS->getListaDeEmpresas("idempresa");
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$v		= $xSel->get("TR.Empresa", true);
		$this->mJsVars	.= "var idempresa	= $('#idempresa').val();\r\n";

		$this->mURL		.= " + \"&empresa=\" + idempresa ";
		$this->mURL		.= " + \"&dependencia=\" + idempresa ";
		$this->mConEmpresa	= false;
		$this->mStruct		.= ($agregar == true) ? $v : "";
		return $v;
	}
	function addCreditosEstados(){
		$xHS	= new cHSelect();
		$xSel	= $xHS->getListaDeEstadosDeCredito("idestado");
		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$v		= $xSel->get("TR.Estado del Credito", true);
		$this->mJsVars	.= "var idestado	= $('#idestado').val();\r\n";

		$this->mURL		.= " + \"&f2=\" + idestado ";
		$this->mURL		.= " + \"&estado=\" + idestado ";

		$this->mStruct	.= $v;
	}
	function addUsuarios($addControl = true){

		$xHS	= new cHSelect();
		$sqlSc		= "SELECT `usuarios`.`idusuarios`, `usuarios`.`nombrecompleto` FROM `usuarios` `usuarios` ORDER BY `usuarios`.`estatus` DESC, `usuarios`.`nombrecompleto` ";
		$xS 		= new cSelect("idusuario", "idusuario", $sqlSc);
		$xS->setEsSql();
		$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idusuario	= $('#idusuario').val();\r\n";
		if(!isset($this->mArrOpts["NOCAJEROS"])){
			$this->mURL		.= " + \"&cajero=\" + idusuario ";
			$this->mURL		.= " + \"&f3=\" + idusuario ";
		}
		$this->mURL		.= " + \"&usuario=\" + idusuario ";
		$xS->setLabel("TR.USUARIO");
		if($addControl == true){
			$this->mStruct	.= $xS->get(true);
		}
		$this->mArrOpts["NOUSUARIOS"]	= true;
		return $xS;
	}
	function addCajeros($addControl = true, $id = ""){
		$xHS	= new cHSelect();
		$id		= ($id == "") ? "idcajero" : $id;
		//$xFRM->addHElem(  );
		$sqlSc		= "SELECT	`cajeros`.`id`,	`cajeros`.`nombre_completo` FROM `cajeros` ";
		$xS 		= new cSelect($id, $id, $sqlSc);
		$xS->setEsSql();
		$xS->addEspOption(SYS_TODAS);
		$xS->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idcajero	= $('#$id').val();\r\n";
		$this->mURL		.= " + \"&cajero=\" + idcajero ";

		if(!isset($this->mArrOpts["NOUSUARIOS"])){
			$this->mURL		.= " + \"&f3=\" + idcajero ";
			$this->mURL		.= " + \"&usuario=\" + idcajero ";
		}
		$xS->setLabel("TR.CAJERO");
		if($addControl == true){
			$this->mStruct	.= $xS->get(true);
		}
		$this->mArrOpts["NOCAJEROS"]	= true;
		return $xS;
	}
	function addTipoDeOperacion($base = false, $base2 = false){
		$base	= setNoMenorQueCero($base);
		if($base > 0){
			$xHSel	= new cHSelect();
			$xSel	= $xHSel->getListaDeOperacionesPorBase($base, "idtipo_de_operacion", $base2);
		} else {
			//$xTb	= new cOperaciones_tipos();
			$xSel	= new cSelect("idtipo_de_operacion", "idtipo_de_operacion","SELECT `idoperaciones_tipos`,`descripcion_operacion` FROM `operaciones_tipos` ORDER BY `descripcion_operacion`" );
			$xSel->setEsSql(true);
		}

		$xSel->addEspOption(SYS_TODAS);
		$xSel->setOptionSelect(SYS_TODAS);
		$this->mJsVars	.= "var idtipo_de_operacion	= $('#idtipo_de_operacion').val();\r\n";
		$this->mURL		.= " + \"&operacion=\" + idtipo_de_operacion ";

		$v		=  $xSel->get("TR.Tipo de Operacion", true);
		//TODO. Agregar indentificadores de tipo de operacion
		$this->mStruct	.= $v;
	}
	function getSelectReportes(){ return $this->mSelectReports; }
	function addHTML($html = ""){ $this->mHtml	.= $html; }
	function getJs($close	= true){
		$this->mJsVars	.= "";
		$this->mJsVars	.= "var g 		= new Gen();\r\n";
		$this->mJsVars	.= "var murl 	= idreporte + \"mx=true\" + " . $this->mURL . ";\r\n";
		if(MODO_DEBUG == true){
			$this->mJsVars	.= "console.log(murl);\r\n";
		}
		$this->mJsVars	.= "g.w({ url : murl }); \r\n}";
		return ($close == true) ? "<script>\r\n" . $this->mJsVars . "\r\n</script>" : $this->mJsVars;
	}
	function addCheckBox($title = "", $parametro = "", $checked = false){
		$xChk	= new cHCheckBox();
		$this->mJsVars	.= "var id$parametro	= $('#id$parametro').prop('checked');\r\n";

		$this->mURL		.= " + \"&$parametro=\" + id$parametro ";

		$this->mStruct	.= $xChk->get($title, "id$parametro", $checked);
	}
	function addTiposDeRecibos(){
		$xSel	=  new cHSelect();
		$ctrl	= $xSel->getListaDeTiposDeRecibos();
		$ctrl->addEspOption(SYS_TODAS, $this->OFRM()->l()->getT("TR.TODAS") );
		$ctrl->setOptionSelect(SYS_TODAS);
		$this->mStruct	.= $ctrl->get(true);
		$this->mJsVars	.= "var idtipoderecibo	= $('#idtipoderecibo').val();\r\n";
		$this->mURL		.= " + \"&tiporecibo=\" + idtipoderecibo ";
	}
	function addMunicipiosActivos(){
		$xUser		= new cSystemUser();
		if(OPERACION_LIBERAR_SUCURSALES == false AND $xUser->getEsCorporativo() == false){
			
		} else {
			$xSel		= new cHSelect();
			$xSelM		= $xSel->getListaDeMunicipiosAct();
			$xSelM->addEspOption(SYS_TODAS, SYS_TODAS);
			$xSelM->setOptionSelect(SYS_TODAS);
			
			//$this->mStruct	.= $ctrl->get(true);
			$this->mStruct	.= $xSelM->get(true);
			//$this->OFRM()->addHElem( $xSelM->get(true) );
			
			$this->mJsVars	.= "var idmunicipioactivo	= $('#idmunicipioactivo').val();\r\n";
			$this->mURL		.= " + \"&municipioactivo=\" + idmunicipioactivo ";
		}
	}
}

class cReportes {
	private $mBody		= "";
	private $mHeader	= "";
	private $mFooter	= "";
	private $mTitulo	= "";
	private $mOut		= "";
	private $mSenders	= array(); 
	private $mMessages	= "";
	private $mBodyMail	= "";
	private $mResponse	= false; 
	private $mFile		= ""; 
	private $mSQL		= "";
	private $mJS		= "";
	private	$mCSSList	= array(); 
	private $mIncluirH3	= false;
	private $mFooterBar	= "";
	private $mGrupo		= array();
	private $mOmitidos	= array();
	private $mSumas		= array();
	private $mConteo	= array();
	private $mSumaGrupo	= array();
	private	$mDataRep	= array();
	private $mFormats	= array();
	private $mKeyUniq	= "";
	private $mPreSQL	= ""; 
	public $FMT_FECHA	= "fmt.date";
	public $FMT_MONEDA	= "fmt.mny";
	private $mPDFPaper	= "letter";
	private $mPDFOrient		= "portrait";
	public $PDF_OHORIZONTAL = "landscape";
	public $PDF_OVERTICAL 	= "portrait";
	
	
	function __construct($titulo = ""){
		$xL				= new cLang();
		$this->mTitulo	= $xL->getT( $titulo );
		$this->mOut		= OUT_HTML;
	}
	function getTitle(){ return $this->mTitulo; }
	function setTitle($title, $incluir = false){ 
		$this->mTitulo 		= $title;
		$this->mIncluirH3 	= $incluir;
		if($this->mFile == "" AND $this->mTitulo != ""){
			$this->setFile($this->mTitulo);
		}
	}
	function getHInicial($titulo, $FechaInicial = "", $FechaFinal = "", $nombreusuario = ""){
		return $this->getEncabezado($titulo, $FechaInicial, $FechaFinal, $nombreusuario);
	}
	function getEncabezado($titulo = "", $FechaInicial = "", $FechaFinal = "", $usuario = ""){
		$xF	= new cFecha();
		//$FechaInicial	= $xF->getFechaCorta($FechaInicial);
		//$FechaFinal		= $xF->getFechaCorta($FechaFinal);
		$usuario		= ($usuario == "") ? elusuario( getUsuarioActual() ) : $usuario;
		$titulo			= ($titulo == "") ? $this->mTitulo : $titulo;
		$fi				= ($FechaInicial == "") ? "" : "<td>Fecha Inicial:</td><td>" . $xF->getFechaCorta($FechaInicial) . "</td>";
		$ff				= ($FechaFinal == "") ? "" : "<td>Fecha Final:</td><td>" . $xF->getFechaCorta($FechaFinal) . "</td>";
		$html	= ($this->mOut == OUT_EXCEL) ? "" : "<table>
		<thead>
			<tr>
				<th colspan=\"4\" class=\"title\">$titulo</th>
			</tr>
			<tr>
				<td width=\"20%\">Preparado por:</td>
				<td width=\"30%\">$usuario</td>
				<td width=\"20%\">Fecha de Elaboracion:</td>
				<td width=\"30%\">" . $xF->getFechaCorta(fechasys()) . "</td>
			</tr>
			<tr>
				$fi
				$ff
			</tr>
		</thead>
		</table>";
		return $html;
	}
	function getPie(){ return getRawFooter();	}
	function setBodyMail($txt){ $this->mBodyMail	.= $txt; }
	function addContent($html, $clean = false){ 
		$this->mBody	.= $html;
	}
	function addHeaderCNT($txt = ""){ $this->mHeader	.= $txt; }
	function addCSSFiles($css){ $this->mCSSList[] = $css; }
	function setOut($out = OUT_HTML){
		if($out == SYS_DEFAULT ){ $out = OUT_HTML; }
		
		$xHP		= new cHPage($this->getTitle(), HP_REPORT);
		$this->mOut	= $out;
		switch ($out){
			case OUT_EXCEL:
				//NADA
				break;
			case OUT_TXT:
				//NADA
				break;

			default:
				$xHP->setTitle($this->mTitulo);
				if($out !== OUT_DOC){
					$xHP->setDevice($out);
				}
				//setLog($out);
				foreach ($this->mCSSList as $key => $file){
					$xHP->addCSS($file);
				}
				$this->mHeader	= $xHP->getHeader() . $this->mHeader;
				//$this->mHeader	.= "<style>.logo{ margin-left: .5em; max-height: 5em; max-width: 5em;	margin-top: 0 !important; border-color: #808080; z-index: 100000 !important;}</style>";
				//$this->mHeader	.= $xHP->setBodyinit("javascript:window.print();");
				$this->mHeader	.= "<body>";
				$this->mFooter	.= "</body></html>";
				
				break;
		}
	}
	function setResponse($response = true){ $this->mResponse = $response;}
	function setSenders($arrSend){		$this->mSenders	= $arrSend;	}
	function setFile($file){ $this->mFile	= preg_replace("/[^A-Za-z0-9 ]/", '', $file); }
	function setSQL($sql){ $this->mSQL = $sql; }
	function setToPrint(){ $this->mJS .= "xRpt.print();"; }
	function setToPagination($init = 0){ $this->mJS .= "xRpt.setPagePagination($init);"; }
	function setPDFOrientacion($or){$this->mPDFOrient = $or; }
	/**
	 * @deprecated
	 * */
	function setPDFOrietacion($or){$this->mPDFOrient = $or; }
	function render($includeHeaders = false){
		$xOH		= new cHObject();
		$cnt		= "";
		$toMail		= (count($this->mSenders) >= 1) ? true : false;
		$body		= "";
		
		ini_set('xdebug.max_nesting_level', 100);
		
		
		if($this->mFile == ""){
			if($this->mTitulo != ""){
				$this->mFile	= $xOH->getTitulize($this->mTitulo);
			}
		}
		if($this->mOut !== OUT_RXML){
			//$this->mFile		= $xOH->getTitulize($this->mFile);
		}
		
		if($includeHeaders == true){
			$this->mHeader	.= getRawHeader(false, $this->mOut);
			$this->mFooter	= getRawFooter(false, $this->mOut) . $this->mFooter;
		}
		if($this->mIncluirH3 == true){
			$this->mHeader = $this->mHeader . "<h3 class='title'>" . $this->mTitulo . "</h3>";
		}
		switch($this->mOut){
			case OUT_EXCEL:
				if($this->mSQL != ""){
					$xls	= new cHExcel($this->mTitulo);
					$html	= $this->mHeader . $this->mBody . $this->mFooter;
					$xls->addContent($html);
					$cnt	= $xls->render(false, $this->mTitulo);					
				}				
				break;
			case OUT_DOC:
				$html	= $this->mHeader . $this->mBody . $this->mFooter;
				$title	= $xOH->getTitulize($this->mTitulo);
				$body	= ($this->mBodyMail == "") ? $title : $this->mBodyMail;
				
				$html	= str_replace("../css/", SAFE_HOST_URL . "css/", $html);
				$html	= str_replace("../js/", SAFE_HOST_URL . "js/", $html);
				$html	= str_replace("../images/", SAFE_HOST_URL . "images/", $html);
				$html 	= preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
				
				$xFS	= new cFileSystem();
				 $nn		= $xFS->cleanNombreArchivo($title, true);
				 $fspdf	= $xFS->setConvertToDocx($html, $nn);
				 if($fspdf !== ""){
				 	if(file_exists($fspdf)){
				 		header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
				 		header("Content-Disposition: attachment; filename=\"$nn.docx\"; ");
				 		readfile($fspdf);
				 	}
				 }
				
				break;
			
			case OUT_RXML:
				$arrPar		= array( "titulo" => $this->mTitulo	);
				$output		= SYS_DEFAULT;
				$oRpt 		= new PHPReportMaker();
				$oRpt->setParameters($arrPar);
				$oRpt->setDatabase(MY_DB_IN);
				$oRpt->setUser(RPT_USR_DB);
				$oRpt->setPassword(RPT_PWD_DB);
				$oRpt->setSQL($this->mSQL);
				$oRpt->setXML("../repository/". $this->mFile . ".xml");
				$oOut 		= $oRpt->createOutputPlugin("html");
				//$oOut->setClean(false);
				$oRpt->setOutputPlugin($oOut);
				//echo  $oRpt->run(true);exit;
				if($toMail == true){
					$html	= $oRpt->run(true);
					
					$title	= $xOH->getTitulize($this->mTitulo);
					$body	= ($this->mBodyMail == "") ? $title : $this->mBodyMail;
					
					
					$dompdf = new Dompdf();
					$dompdf->loadHtml($html);
					$dompdf->setPaper($this->mPDFPaper, $this->mPDFOrient);
					
					$dompdf->render();
					$this->mFile	= PATH_TMP . "" . $title . ".pdf";
					$output = $dompdf->output();
					file_put_contents($this->mFile, $output);
					$output			= null;
				} else {
					$oRpt->run();
				}
				break;
			case OUT_PDF:
				$html	= $this->mHeader . $this->mBody . $this->mFooter;
				$title	= $xOH->getTitulize($this->mTitulo);
				$body	= ($this->mBodyMail == "") ? $title : $this->mBodyMail;
				
				
				$xFS	= new cFileSystem();
				$nn		= $xFS->cleanNombreArchivo($title, true);
				$html	= str_replace("../css/", SAFE_HOST_URL . "css/", $html);
				$html	= str_replace("../js/", SAFE_HOST_URL . "js/", $html);
				$html	= str_replace("../images/", SAFE_HOST_URL . "images/", $html);
				
				$fspdf	= $xFS->setConvertToPDF($html, $nn);
				if($fspdf !== ""){
					header("Content-type: application/pdf");
					header("Content-Disposition: attachment; filename=\"$nn.pdf\"; ");
					readfile($fspdf);
				}
				//Nuevo DOM
				/*$dompdf = null;
				//Agregar Limite de Memoria
				try {
					
					
					$dompdf = new Dompdf();
					$dompdf->loadHtml($html);
					$dompdf->setPaper($this->mPDFPaper, $this->mPDFOrient);
					
					$dompdf->render();
					if($toMail == true){
						$this->mFile	= PATH_TMP . "" . $title . ".pdf";
						$output 		= $dompdf->output();
						file_put_contents($this->mFile, $output);
						$output			= null;
					} else {
						$this->mFile	= $title . ".pdf";
						# Enviamos el fichero PDF al navegador.
						$dompdf->stream($this->mFile);
					}					
				} catch (Exception $e) {
					$this->mMessages	.= "ERROR\tNo se genera el Archivo PDF\r\n";
				}*/		
				break;

			default:
				
				
				$cnt	= $this->mHeader . $this->mBody . $this->mFooter;
				
				
				if($toMail == true){
					$html	= $cnt;
					$title	= $xOH->getTitulize($this->mTitulo);
					
					
					$dompdf = new Dompdf();
					$dompdf->loadHtml($html);
					$dompdf->setPaper($this->mPDFPaper, $this->mPDFOrient);
					
					
					$dompdf->render();
					$body			= ($this->mBodyMail == "") ? $title : $this->mBodyMail;
					$this->mFile	= PATH_TMP . "" . $title . ".pdf";
					$output 		= $dompdf->output();
					file_put_contents($this->mFile, $output);
					$output			= null;
				} else {
					
					if($this->mOut == OUT_DOC){
						$this->mJS	= "";
					}
					$this->mJS	= ($this->mJS == "") ? "" : "<script>var xRpt = new RepGen();" . $this->mJS . "</script>";
					$footerbar	= (trim($this->mFooterBar) == "") ? "" : "<div class='footer-bar warning'>" . $this->mFooterBar . "</div>";
					$cnt		= $this->mHeader . $this->mBody . $this->mJS . $footerbar . $this->mFooter;
				}
				break;
				
		}
		if($toMail == true){
			$xMail		= new cNotificaciones();
			foreach ($this->mSenders as $idmail => $email){
				$this->mMessages	.= $xMail->sendMail($this->mTitulo, $body, $email, array( "path" => $this->mFile ));
			}

			if($this->mResponse == true){
				$rs		= array("message"  => $this->mMessages);
				$cnt	= json_encode($rs);
			}
		}
		return $cnt;
	}
	function setDataReplace($data){ $this->mDataRep = $data;}
	function addFooterBar($html){		$this->mFooterBar	.= $html;	}
	function setOmitir($campo){ $this->mOmitidos[$campo] = $campo; }
	function setGrupo($campo, $formato = ""){
		$this->mGrupo[$campo] = $campo;
		$this->setOmitir($campo); 
	}	
	function addCampoSuma($campo){ $this->mSumas[$campo]	= 0; }
	function addCampoContar($campo){ $this->mConteo[$campo]	= 0; }
	function addJsCode($js = ""){ $this->mJS .= $js;}
	function setKeyUnique($key){$this->mKeyUniq = $key; }
	function setPreSQL($sql){$this->mPreSQL = $sql; }
	function setConfig($idconf){
		$xQL	= new MQL();
		$D		= $xQL->getDataRow("SELECT * FROM `entidad_reportes_props` WHERE `idconfiguracion`='$idconf' LIMIT 0,1");
		if(isset($D["omitir"])){
			$omitidos	= explode(",", $D["omitir"]);
			foreach ($omitidos as $k => $c){
				$this->setOmitir($c);
			}
		}
	}
	function setFormato($campo, $formato){
		$this->mFormats[$campo]	= $formato;
	}
	function setProcessSQL(){
		$xQL	= new MQL();
		$xLng	= new cLang();
		$xF		= new cFecha();
		
		$xQL->setConTitulos();
		if($this->mPreSQL !== ""){
			$xQL->setRawQuery($this->mPreSQL);
		}
		$rs 	= $xQL->getDataRecord($this->mSQL);
		$tit	= $xQL->getTitulos();
		$regs	= $xQL->getNumberOfRows();
		
		$body	= "";
		$head	= "";
		$foot	= "";
		$idx	= 1;
		$cnt	= sizeof($tit);
		$format	= ($this->mOut == OUT_TXT) ? false : true;
		$tdcss	= " class='mny'";
		$xCant	= new cCantidad();
		$mLstF	= "";
		$OPTS	= array();
		$tit2	= $tit;
		foreach ($tit2 as $titles){
			$xFld		= new MQLCampo($titles);
			$idcampo	= $xFld->get();
			if(isset($this->mOmitidos[$idcampo])){
				unset($tit[$idcampo]);
			} else {
				$tit[$idcampo][SYS_NUMERO]	= false;
				$tit[$idcampo][SYS_FECHA]	= false;
				$tit[$idcampo][SYS_MONEDA]	= false;
				$tit[$idcampo]["GRUPO"]		= false;
				if($xFld->isNumber() == true){
					$tit[$idcampo][SYS_NUMERO]		= true;
				}
				if(isset($this->mFormats[$idcampo])){
					if($this->mFormats[$idcampo] == $this->FMT_FECHA){
						$tit[$idcampo][SYS_FECHA] 	= true;
					}
					if($this->mFormats[$idcampo] == $this->FMT_MONEDA){
						$tit[$idcampo][SYS_MONEDA] 	= true;
					}					
				}
				if( isset($this->mGrupo[$idcampo]) ){
					$tit[$idcampo]["GRUPO"]		= true;
				}
			}
		}
		$tit2	= null;
		
		foreach ($rs as $rw){
			//$body .= "<tr>";
			$txt	= "";
			$txtInit= "";
			$txtFin	= "";
			$cidx	= 1;
			$mKey	= (isset($rw[$this->mKeyUniq])) ? $rw[$this->mKeyUniq] : null;
			foreach ($tit as $campos){
				
				$idcampo	= $campos["N"];
				$mny		= $campos[SYS_MONEDA];
				$isDate		= $campos[SYS_FECHA];
				$valor		= $rw[$idcampo];
				
				if( isset($this->mGrupo[$idcampo]) ){
					$vgrupo	= $valor;
					
					if($this->mGrupo[$idcampo] != $vgrupo){
						$ncc		= $cnt -2;
						$trad		= $xLng->getT("TR." . str_replace("_", " ", $idcampo));
						$gInit		= "";
						$gcss		= " class='mny sumas'";
						$gtcss		= " class='ctitle'";
						for ($ix=1; $ix<=$cnt; $ix++){
							
							$gInit	.= (isset($this->mSumaGrupo[$ix])) ? "<td$gcss>" . $xCant->moneda($this->mSumaGrupo[$ix]) . "</td>" : "<td></td>";
						}
						$gInit		= ($gInit == "" OR $idx == 1) ? "" : "$gInit</tr>";
						$txtInit	= "$gInit<th colspan='2'$gtcss>$trad</th><th colspan='$ncc'$gtcss>" . $vgrupo . "</th></tr><tr>\r\n";
						$this->mSumaGrupo		= array();
					}
					$this->mGrupo[$idcampo]		= $valor;
				}
				if($campos[SYS_NUMERO] == true ){
					if(isset($this->mSumas[$idcampo])){
						$this->mSumas[$idcampo]	+= $valor;
						if(!isset($this->mSumaGrupo[$cidx])){ $this->mSumaGrupo[$cidx] = 0; }
						$this->mSumaGrupo[$cidx]	+= $valor; //sumarx x campo a grupo
						$mny			= true;
					}
				}
				if($isDate == true){
					$valor				= $xF->getFechaMX($valor, "/");
				}
				//if(!isset($this->mOmitidos[$idcampo])){
					if($idx == 1){ 
						$trad	= $xLng->getT("TR." . str_replace("_", " ", $idcampo));
						$head	.= "<th>". $trad . "</th>";
					}
					
					if($valor == HP_REPLACE_DATA AND $mKey !== null){
						$valor	= isset($this->mDataRep[$mKey]) ? str_replace(HP_REPLACE_DATA, $this->mDataRep[$mKey], $valor) : 0;
						if(isset($this->mSumas[$idcampo])){
							$this->mSumas[$idcampo]	+= $valor;
							if(!isset($this->mSumaGrupo[$cidx])){ $this->mSumaGrupo[$cidx] = 0; }
							$this->mSumaGrupo[$cidx]	+= $valor; //sumarx x campo a grupo
							$mny			= true;
						}
					}
					$txt		.= ($mny == true )? "<td$tdcss>" . $xCant->moneda($valor) . "</td>" : "<td>" . $valor . "</td>";
					if($idx == $regs){
						
						$gcss		= " class='mny sumas title'";
						if(isset($this->mSumas[$idcampo])){
							$foot	.= "<td$gcss>". $xCant->moneda($this->mSumas[$idcampo]) . "</td>";
						} else {
							if(isset($this->mConteo[$idcampo])){
								$foot	.= "<td$tdcss>" . $idx . "</td>";
							} else {
								$foot	.= "<td></td>";
							}
						}
					}
					$cidx++;
				//}
				
			}
			//ULTIMO
			if($idx == $regs){
				for ($ix=1; $ix<=$cnt; $ix++){
					$gcss		= " class='mny sumas'";
					$txtFin		.= (isset($this->mSumaGrupo[$ix])) ? "<td$gcss>" . $xCant->moneda($this->mSumaGrupo[$ix]) . "</td>" : "<td></td>";
				}
				$txtFin			= ($txtFin == "") ? "" : "</tr><tr>$txtFin";
			}
			$body .= "<tr>$txtInit$txt$txtFin</tr>";
			$idx++;
		}
		$mLstF	= ($mLstF == "") ? "" : "<tr>$mLstF</tr>";
		$body	.= $mLstF;
		$foot	= ($foot == "") ? "" : "<tfoot><tr>$foot</tr></tfoot>";
		
		$this->mSumas	= null;
		$this->mOmitidos= null;
		$this->mGrupo	= null;
		$this->mSumaGrupo=null;
		$this->mDataRep	= null;
				
		$this->addContent( "<table><thead><tr>$head</tr></thead><tbody>$body</tbody>$foot</table>");
		$body			= null;
		$head			= null;
		$foot			= null;
		$rs				= null;
	}
}

class cHExcelNew {
	private $mObj		= null;
	private $mTitle		= "";
	private $mCols		= array();
	private $mFile		= "";
	private $mHoja		= array();
	function __construct($title = ""){
		$this->mTitle	= $title;
		
		$this->mObj 		= new PHPExcel();
		$this->mObj->getProperties()->setCreator(SAFE_FIRM)
		->setLastModifiedBy(EACP_NAME)
		->setTitle($this->mTitle)
		->setSubject($this->mTitle)
		->setDescription($this->mTitle)
		->setKeywords("export")
		->setCategory("export");
		//Columnas
		$this->mCols[]	= "A";
		$this->mCols[]	= "B";
		$this->mCols[]	= "C";
		$this->mCols[]	= "D";
		$this->mCols[]	= "E";
		$this->mCols[]	= "F";
		$this->mCols[]	= "G";
		$this->mCols[]	= "H";
		$this->mCols[]	= "I";
		$this->mCols[]	= "J";
		$this->mCols[]	= "K";
		$this->mCols[]	= "L";
		$this->mCols[]	= "M";
		$this->mCols[]	= "N";
		$this->mCols[]	= "O";
		$this->mCols[]	= "P";
		$this->mCols[]	= "Q";
		$this->mCols[]	= "R";
		$this->mCols[]	= "S";
		$this->mCols[]	= "T";
		$this->mCols[]	= "U";
		$this->mCols[]	= "V";
		$this->mCols[]	= "W";
		$this->mCols[]	= "X";
		$this->mCols[]	= "Y";
		$this->mCols[]	= "Z";
		$this->mCols[]	= "AA";
		$this->mCols[]	= "AB";
		$this->mCols[]	= "AC";
		$this->mCols[]	= "AD";
		$this->mCols[]	= "AE";
		$this->mCols[]	= "AF";
		$this->mCols[]	= "AG";
		$this->mCols[]	= "AH";
		$this->mCols[]	= "AI";
		$this->mCols[]	= "AJ";
		$this->mCols[]	= "AK";
		$this->mCols[]	= "AL";
		$this->mCols[]	= "AM";
		$this->mCols[]	= "AN";
		//$this->mObj->setActiveSheetIndex(0);
		
	}
	function __destruct(){
		$this->mObj	= null;
	}
	function addArray($arr, $fila, $hoja = 0){
		if(!isset($this->mHoja[$hoja])){
			$this->mObj->createSheet($hoja);
			$this->mHoja[$hoja]	= $hoja;
		}
		$this->mObj->setActiveSheetIndex($hoja);
		$contar	= 0;
		foreach ($arr as $idx => $cnt){
			//$this->mObj->setActiveSheetIndex($hoja)->setCellValue($this->mCols[$idx] . $fila, "$cnt");
			$this->mObj->setActiveSheetIndex($hoja)->setCellValueExplicit($this->mCols[$contar] . $fila, "$cnt", "s"); //s = string
			$contar++;
		}
	}
	function setRenameSheet($id, $name){
		$this->mObj->setActiveSheetIndex($id);
		$this->mObj->getActiveSheet()->setTitle($name);
		
	}
	function setExportar($nombre = ""){
		$archivo		= PATH_TMP . $nombre . ".xlsx";
		$this->mFile	= $nombre;
		$objWriter 		= PHPExcel_IOFactory::createWriter($this->mObj, 'Excel2007');
		$objWriter->save($archivo);		
	}
	function getLinkDownload($label, $class = "button"){
		
		$xBtn	= new cHButton();
		$xLn	= new cLang();
		$label	= $xLn->getT($label);
		$ic		= $xBtn->setIcon( $xBtn->ic()->DESCARGAR );
		$class	= ($class == "") ? "" : " class=\"$class\" ";
		$str = "<a href=\"../utils/download.php?type=xlsx&download=" . $this->mFile . "&file=" . $this->mFile . "\" target=\"_blank\" $class>$ic $label</a>";
		return $str;
	}	
}
class cChart {
	private $mCNT	= array();
	public $MULTILABEL	= "chart.multilabel";
	public $BAR			= "chart.bar";
	public $PIE			= "chart.pie";
	private $mLabels	= array();
	private $mSeries	= array();
	private $mKey		= array();
	private $mLbl		= "";
	private $mSs		= "";
	private $mId		= "";
	private $mTipo		= "chart.bar";
	private $mTotal		= 0;
	private $mOnPercent	= false;
	private $OLang		= null;
	private $mHorizontal= false;
	private $mColumnW	= 50;
	private $mFuncCvt	= "";
	private $mAlto		= "300";
	
	function __construct($id = ""){$this->mId	= $id;	}
	private function TR($txt){if($this->OLang == null){ $this->OLang = new cLang(); } return $this->OLang->getT($txt); }
	function getDiv(){ return "<div id=\"". $this->mId . "\"></div>";	}
	function setOnPercent($p = true){ $this->mOnPercent = $p;}
	function setHorizontal($v = true){ $this->mHorizontal = $v; }
	function setTamanioCol($v){$this->mColumnW = $v;}
	function setFuncConvert($jsFunc){$this->mFuncCvt = $jsFunc; }
	function setAlto($v){$this->mAlto = $v; }
	function getJs(){
		$js		= "";
		$opFc	= ($this->mFuncCvt == "") ? "" : "convert:".$this->mFuncCvt;
		$opF2	= ($this->mFuncCvt == "") ? "" : "labelInterpolationFnc:function(value){return ".$this->mFuncCvt . "(value)}";
		$opsH	= ($this->mHorizontal == true) ? ",reverseData: true,  horizontalBars: true,  axisY: { offset: 100 }" : ", axisY: { $opF2 } ";
		$opsA	= ($this->mAlto == "") ? "" : ", height: ". $this->mAlto; 
		switch($this->mTipo){
			case $this->BAR:
				$js	= "var data = {labels:[" . $this->mLbl . "], series: [" . $this->mSs . " ] };
var options = { seriesBarDistance: 10, plugins: [ Chartist.plugins.ctBarLabels({ $opFc   }) ] $opsH $opsA };
new Chartist.Bar('#" . $this->mId . "', data, options).on('draw', function(data){ if(data.type === 'bar') {  data.element.attr({ style: 'stroke-width: " . $this->mColumnW . "px' }); } } 	);";
				break;
			case $this->MULTILABEL:
		$js	= "new Chartist.Bar('#" . $this->mId . "', { labels: [" . $this->mLbl . "], series: [" . $this->mSs . " ]}, {
			  seriesBarDistance: 10,
			  axisX: {   offset: 60
			  },
			  axisY: {
			    offset: 80,
			    labelInterpolationFnc: function(value) {
			      return value + ' CHF'
			    },
			    scaleMinSpace: 15
			  }
			});";
		break;
			default:
				$js = "new Chartist.Pie('#" . $this->mId . "', { series: [" .$this->mSs ."],labels: [" . $this->mLbl . "]}, {
  				donut: true,  donutWidth: 60,  startAngle: 270,  total: " . $this->mTotal . ",  showLabel: true});";
				break;
		}
		return $js;
	}
	function setProcess($tipo = ""){
		$tipo			= ($tipo == "") ? $this->BAR : $tipo;
		$this->mTipo	= $tipo;
		$this->mTotal	= setNoMenorQueCero($this->mTotal);
		$multiserie		= false;
		$nseries		= count($this->mLabels);
		$cseries		= 0;
		
		foreach ($this->mLabels as $k => $v){
			$this->mLbl	.= ($this->mLbl == "") ? "'$v'" : ",'$v'";
		}
		$maxWidth = max( array_map( 'count',  $this->mSeries ) );
		//setLog($maxWidth);
		foreach ($this->mSeries as $kx => $cnt){
			$sr			= "";
			$nitems		= 0;
			foreach ($cnt as $k => $v){
				$subitems	= "";
				if(is_array($v)){
					$vtmp	= "";
					foreach ($v as $arrK => $arrV){
						if($this->mOnPercent == true){
							$arrV		= $arrV / $this->mTotal;
							$arrV		= round(($arrV * 100),2);
						}
						$vtmp .= ($vtmp  == "") ? "$arrV" : ",$arrV";
					}
					
					$v		= (count($v) > 1) ? "[" .$vtmp . "]" : $vtmp;
				} else {
					if($this->mOnPercent == true){
						$v		= $v / $this->mTotal;
						$v		= round(($v * 100),2);
					}
				}
				$sr	.= ($sr == "") ? "$v" : ",$v";
				$nitems++;
				
			}
			
			if($this->mTipo == $this->PIE){
				$sr	= "$sr";
			} else {
				//setLog(" $kx => $sr ($nitems) ");
				if($nitems < $maxWidth){
					$nrellena 	= ($maxWidth-$nitems);
					$sr 	= $sr . str_repeat(",0", $nrellena);
				}
				
				$sr	= "[$sr]";
			}
			$this->mSs	.= ($this->mSs == "") ? "$sr" : ",$sr";
			
			$cseries++;
		}
		
	}
	/**
	 * @param string $sql	Cadena SQL
	 * @param string $data	Nombre del Campo de Datos
	 * @param string $key	Nombre de Campo en la Consulta SQL
	 */
	function addDataset($sql, $data, $key = ""){
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord($sql);
		$key	= ($key == "") ? $this->mKey : $key;
		$arrD	= array();
		foreach ($rs as $rw){
			$this->mLabels[$rw[$key]]	= $rw[$key];
			$arrD[$rw[$key]]			= $rw[$data];
			$this->mTotal				+=$rw[$data];
		}
		$rs								= null;
		$this->mSeries[]				= $arrD;
		$this->mKey						= $key;
	}
	function addData($valor, $texto){
		$texto					= $this->TR($texto);	
		$this->mLabels[$texto]	= $texto;
		$this->mTotal			+= $valor;
		if(isset($this->mSeries[$texto])){
			$tmp							= $this->mSeries[$texto];
			//setLog(count($this->mSeries[$texto]) . "    $texto");
			if(count($this->mSeries[$texto])<=1){
				$this->mSeries[$texto]					= array();
				$this->mSeries[$texto][]	= $tmp;
				//var_dump($this->mSeries[$texto]);
			}
			$this->mSeries[$texto][]		= array($texto=> $valor);
		} else {
			$this->mSeries[$texto]			= array($texto=> $valor);
		}
		
	}
}

class cHPersona {
	private $mClavePersona	= 1; 
	private $mD				= array();
	private $mNumCreds		= 0;
	private $mNumCtas		= 0;

	function __construct($persona = false){
		$this->mClavePersona	= setNoMenorQueCero($persona);
		if($this->mClavePersona > DEFAULT_SOCIO){$this->init();}
	}
	function init(){
		$xQL	= new MQL();
		$dd		= $xQL->getDataRow("SELECT * FROM `tmp_personas_estadisticas` WHERE `persona`=" . $this->mClavePersona . " LIMIT 0,1");
		if(isset($dd["creditos"])){
			$this->mNumCreds		= $dd["creditos"];
			$this->mNumCtas			= $dd["cuentas"];
		}
	}
	function getNotifNumCreds(){
		$html		= "";
		if($this->mNumCreds>0){
			$xNot	= new cHNotif();
			$html	= $xNot->getNoticon($this->mNumCreds,"", $xNot->ic()->CREDITO);
		}
		return $html;
	}
	function getNotifNumCtas(){
		$html		= "";
		if($this->mNumCtas>0){
			$xNot	= new cHNotif();
			$html	= $xNot->getNoticon($this->mNumCtas,"", $xNot->ic()->AHORRO);
		}
		return $html;
	}	
}

class cHTabs{
	protected $mArrTabs = array();
	private $mArrID		= array();
	protected $mWidth	= "100%";
	protected $mHeight	= "100%";
	protected $mId		= 0;
	function __construct($id = false){
		$this->mId	= ($id == false) ? "tab" : $id;
	}
	function setWidth($width){
		$this->mWidth	= $width;
	}
	function setHeight($height){
		$this->mHeight = $height;
	}
	function addTab($titulo, $contenido = "", $id = ""){
		$xL	= new cLang();
		//$titulo		= $xL->getT($titulo);

		if( isset($this->mArrTabs[$titulo]) ){
			$this->mArrTabs[$titulo] .= $contenido;
		} else {
			$this->mArrTabs[$titulo] = $contenido;
		}
		if($id != ""){
			$this->mArrID[$titulo]	= $id;
		}
	}
	function setContenido($tab, $contenido){

	}
	function get(){
		$strLi		= "";
		$strCont	= "";
		$ix			= 0;
		$mid		= $this->mId;
		$xLng		= new cLang();
		foreach ($this->mArrTabs as $clave => $valor){
			//Reemplazar espacios y poner a minusculas
			//$keyTab	= strtolower( str_replace(" ", "_", $clave) );
			$tabtit		= $xLng->getT($clave);
			$keyTab		= $ix;
			if(isset($this->mArrID[$clave])){
				$keyTab	= $this->mArrID[$clave];
			} else {
				$keyTab	= $mid-$keyTab;
			}
			$strLi		.= "<li><a href=\"#$keyTab\">$tabtit</a></li>";
			$strCont	.= "<div id=\"$keyTab\">$valor</div>\r\n";
			$ix++;
		}
		$strH	= "<div id=\"$mid\" style='min-height:  " . $this->mHeight . "; min-width:  " . $this->mWidth . ";'><ul>$strLi</ul> $strCont</div><script>setTimeout('go$mid()',1000); function go$mid(){ if(document.getElementById(\"$mid\")){ try{ $(\"#$mid\" ).tabs();} catch(e){}} }</script>";
		return $strH;
	}
	function getIdTab(){

	}
}
class cHImg {
	private $mIcon		= "icon.png";
	function __construct($icon = ""){ $this->mIcon = $icon; }
	function get16($icon = "", $snipt = ""){
		$icon		= ($icon == "") ? $this->mIcon : $icon;
		$icon		= (strpos($icon, "png") === false) ? "$icon.png" : $icon;
		return "<img src=\"../images/$icon\" $snipt class=\"x16\"/>";
	}
	function get24($icon = "", $snipt = ""){
		$icon		= ($icon == "") ? $this->mIcon : $icon;
		$icon		= (strpos($icon, "png") === false) ? "$icon.png" : $icon;
		return "<img src=\"../images/$icon\" $snipt class=\"x24\"/>";
	}

}
class cHMenuItem {
	private $mDatos		= array();
	public $CLAVE		= 0;
	public $TIPO		= "command";
	public $NOTA		= "";
	public $TITULO		= "";
	public $DESTINO		= "";
	public $ARCHIVO		= "";
	public $ICON		= "";
	public $PARENT		= 0;
	private $mEventPa	= "";
	private $mIsMobile	= false;
	
	function __construct($id, $datos = false){
		$xMen	= new cGeneral_menu();
		if(is_array($datos)){
			$xMen->setData($datos);
		} else {
				
			$xMen->setData( $xMen->query()->initByID($id) );
		}
		$this->CLAVE	= $xMen->idgeneral_menu()->v();
		$this->NOTA		= $xMen->menu_description()->v(OUT_TXT);
		$this->TIPO		= $xMen->menu_type()->v();
		$this->DESTINO	= $xMen->menu_destination()->v(OUT_TXT);
		$this->ARCHIVO	= $xMen->menu_file()->v(OUT_TXT);
		$this->ICON		= $xMen->menu_image()->v(OUT_TXT);
		$this->TITULO	= $xMen->menu_title()->v();
		$this->PARENT	= $xMen->menu_parent()->v();
		$this->mIsMobile= $_SESSION[SYS_CLIENT_MOB];
	}
	function setIsMobile($is){ $this->mIsMobile = $is; }
	function getLi($WithImages = false, $html = "", $extraTags = ""){
		$Clave		= $this->CLAVE;
		$Tipo		= $this->TIPO;
		$Titulo		= ucfirst($this->TITULO);
		$Imagen		= $this->ICON;
		$TipoDeDes	= $this->DESTINO;
		$Archivo	= $this->ARCHIVO;
		$Descrip	= $this->NOTA;
		$Descrip	= ($Descrip == "" OR $Descrip == "NO_DESCRIPTION") ? "": "title=\"$Descrip\"";
		$isMobile	= $this->mIsMobile;
		$xL			= new cLang();
			
		if( SAFE_LANG != "ES" AND SYS_TRADUCIR_MENUS == true){ $Titulo	= $xL->getT("TR.$Titulo");	}
			
		$mImagen		= "";
		
		$xBtn			= new cHButton();
		$mImagen		= $xBtn->setIcon($Imagen, "fa-lg", true);
		
		//$WithImages == true
		
		$mCmd			= $this->getTipoDestino($TipoDeDes);
		$id				= "";
		if(MODO_DEBUG == true AND $this->PARENT > 0){ $id = "<span>$Clave</span>"; }
		$mCmd			= " onclick=\"$mCmd('$Archivo', event)\"";
		$dKey			= " ";
		if($Tipo == "parent"){
			$dKey		= " data-key=\"$Clave\"";
			$mCmd		= "";
			if($isMobile == true){ $mCmd = " onclick=\"jsGetMenuChilds(this.id, event)\""; }
		}
		$menu			= "<li id=\"md_$Clave\"><a  id=\"amenu_$Clave\" $extraTags" . $dKey . $mCmd. ">$mImagen&nbsp;$Titulo&nbsp;$id</a>$html</li>\n";
		return $menu;
	}
	private function getTipoDestino($tipodestino){
		$destino		= "";
		switch ($tipodestino){
			case "principal":
				$destino	= "setInFrame";
				break;
			case "tiny":
				$destino	= "getNewTiny";
				break;
			default:
				$destino	= "getNewWindow";
				break;
		}
		return $destino;
	}
}
class cHGrid {
	private $mCampos			= array();
	private $mActions			= array();
	private $mToolbars			= array();
	private $mTitle				= "";
	private $mId				= "";
	private $mSQL				= "";
	private $mOLang				= null;
	private $mPaginacion		= true;
	private $mOrden				= false;
	private $mNoDefParam		= false;
	private $mSizeIcon			= "5%";
	
	
	function __construct($id, $title = ""){ $xlng	= new cLang(); $this->mId	= $id; $this->mTitle	= $xlng->getT($title); }
	function setSQL($sql){
		$arrch		= array('/\t+/', '/\s\s+/');
		$sql		= preg_replace($arrch, ' ', $sql);
		

		
		$this->mSQL = utf8_encode($sql);
		//setLog($this->mSQL);
		return base64_encode($this->mSQL);
	}
	private function OLang(){
		if($this->mOLang == null){
			$this->mOLang	= new cLang();
		}
		return $this->mOLang;
	}
	//listAction: '../svc/referencias.svc.php?out=jtable&persona=' + idxpersona + "&documento=" + idxcredito
	/**
	* @deprecated @since 2015.07.03
	* */
	function setListAction($url){ $this->mActions["listAction"] = $url; }
	function addList($url="../svc/datos.svc.php"){
		$sql		= $this->mSQL;
		//setLog($sql);
		$exsql		= base64_encode($sql);
		$pta		= strlen($exsql);
		if($this->mNoDefParam == false){
			$this->mActions["listAction"]	= $url ."?out=jtable&q=". $exsql;
		} else {
			$this->mActions["listAction"]	= $url;
		}
		
	}
	/**
	 * @deprecated @since 2015.07.04
	 * */
	function addElement($nombre, $titulo, $tamannio){
		/*tipo_de_relacion:{ title: 'Relacion', width: '20%'}*/
		$xlng	= new cLang();
		$titulo	= $xlng->getT($titulo);
		$this->mCampos[$nombre] = array ("title" => $titulo, "width" => $tamannio);
	}
	function col($nombre, $titulo, $zsize, $moneda = false){
		/*tipo_de_relacion:{ title: 'Relacion', width: '20%'}*/

		$titulo	= $this->OLang()->getT($titulo);
		if($moneda == true){
			$this->mCampos[$nombre] = array ("title" => $titulo, "width" => $zsize, "format" => "getFMoney(data.record.$nombre)");
		} else {
			$this->mCampos[$nombre] = array ("title" => $titulo, "width" => $zsize);
		}
		
	}
	function ColMoneda($nombre, $titulo, $zsize){
		$titulo	= $this->OLang()->getT($titulo);
		$this->mCampos[$nombre] = array ("title" => $titulo, "width" => $zsize, "format" => "getFMoney(data.record.$nombre)");
	}
	function ColFecha($nombre, $titulo, $zsize){
		$titulo	= $this->OLang()->getT($titulo);
		$this->mCampos[$nombre] = array ("title" => $titulo, "width" => $zsize, "type" => "date", "displayFormat" => "dd/mm/yy");
	}
	function OColFunction($nombre, $titulo, $zsize, $funcion = ""){
		/*tipo_de_relacion:{ title: 'Relacion', width: '20%'}*/
		$titulo	= $this->OLang()->getT($titulo);
		$this->mCampos[$nombre] = array ("title" => $titulo, "width" => $zsize, "function" => "$funcion(data)");
	}
	function setColSum($nombre){
		if( isset($this->mCampos[$nombre]) ){
			$this->mCampos[$nombre]["footer"] = "function(data){ var total = 0; $.each(data.records, function(index,record){ total += redondear(record.$nombre,2); }); return getFMoney(total); }";
		}
	}
	function OToolbar($titulo, $evento, $icono){
		$titulo	= $this->OLang()->getT($titulo);
		$this->mToolbars[$titulo] = array(
				"icon" => "../images/$icono",
				"text" => $titulo,
				"click"	=> $evento
		);
	}

	function OButton($titulo, $evento, $icono, $id = "", $dataRAW = false){
		$size 	= $this->mSizeIcon;
		//$xFRM->OButton("TR.CODIGO", "jsGetTable()", $xFRM->ic()->EJECUTAR);
		/*tipo_de_relacion:{ title: 'Relacion', width: '20%'}*/
		//$xBtn	= new cHButton();
		$nombre	= $this->OLang()->getT("TR.ACCIONES");

		$xImg	= new cHImg($icono);
		$id		= ($id == "") ? "id" . rand(0, 100) : $id;
		//$xBtn->setProperty("class", "jtable-command-button");
		$titulo	= $this->OLang()->getT($titulo);

		$btn = $xImg->get24($icono, " onclick=\"$evento;return false\" alt=\"$titulo\" title=\"$titulo\" class=\"jtable-command-button\" style=\"width:2em;height:2em\" ");
		//<i class=\"fa fa-user fa-lg\"></i>
		//$btn 	= "<button class=\"jtable-command-button\" onclick=\"$evento;return false\">$titulo</button>";
		//$btn 	= "<image src=\"../images/edit.png\" onclick=\"$evento;return false\" />";
		
		$titulo	= setLimpiarCadena($titulo);
		
		
		$this->mCampos[$titulo] = array (
				"title" => '', 
				"width" => $size, 
				"button" => $btn, 
				"listClass" => "jtable-command-column", 
				"sorting" => "false", 
				"edit" => "false", 
				"create" => "false");
		if($dataRAW == true){
			$this->mCampos[$titulo]["dataRaw"] = true;
		}
		//setLog($this->mCampos[$titulo]);
		/*if(isset($this->mCampos[$nombre])){
			$btn = $this->mCampos[$nombre]["display"] . $btn;
			$this->mCampos[$nombre] = array ("title" => $nombre, "width" => $zsize, "display" => $btn, "listClass" => "jtable-command-column");
			} else {

			$this->mCampos[$nombre] = array ("title" => $nombre, "width" => $zsize, "display" => $btn, "listClass" => "jtable-command-column");
			}*/
		/*MyButton: {
		 title: 'MyButton',
		 width: '40%',
		 display: function(data) {
		 return "<button type='button' onclick='alert(" + data.record.clave + ")'>create PDF</button>";
		 }
		 }*/
	}
	function addkey($nombre, $show = false){
		$DKey	= array("key" => "true");
		$DKey["list"]	= ($show == false) ? "false" : "true";
		if(setCadenaVal($show) != ""){	//si existe es titulo y enable
			$xlng	= new cLang();
			$DKey["list"]	= "true";
			$DKey["title"]	= $xlng->getT("$show");
			$DKey["width"]	= "10%";
		}
		$this->mCampos[$nombre] = $DKey;
	}
	function getJs($init = false, $enclose = false){
		$flds		= "";
		$tbars		= "";
		$sorting	= ($this->mOrden == true) ? " sorting : true, " : "";
		
		foreach ($this->mCampos as $campos => $items){
			$flds		.= ($flds == "") ? "$campos : {" : ",$campos : {";
			$isRaw		= false;
			if(isset($items["dataRaw"])){
				$isRaw	= $items["dataRaw"];
				unset($items["dataRaw"]);
				setLog($items);
			}
			foreach ($items as $props => $vals){
				switch($props){
					case "button":
						if($isRaw == true){
							$flds .= "display : function(data){ var dataRaw = base64.encode(JSON.stringify(data.record)); return '$vals'; },";
						} else {
							$flds .= "display : function(data){ return '$vals'; },";
						}
						break;
					case "format":
						$flds .= "display : function(data){ return $vals; },";
						break;
					case "function":
						$flds .= "display : function(data){ return $vals; },";
						break;
					default:
						$flds	.= ($vals == "true" OR $vals == "false" OR $props == "footer") ? "$props : $vals," : "$props : \"$vals\",";
						break;
				}
			}
			$flds	.= "}";
		}
		$acts	= "";
		foreach ($this->mActions as $act => $url){
			$acts	.= ($acts == "") ? "$act : '$url' + str" : ", $act : '$url' + str";
		}
		foreach ($this->mToolbars as $tt => $cnt){
			if(isset($cnt["click"])){
				$tbars .= ($tbars == "") ? "{ icon:'" . $cnt["icon"] . "', text: '" . $cnt["text"] . "', click:function(){ " . $cnt["click"] . "; } }" : ",{ icon:'" . $cnt["icon"] . "', text: '" . $cnt["text"] . "', click:function(){ " . $cnt["click"] . "; } }";
			}
		}
		if($tbars !== ""){
			$tbars	= ",\n\t\ttoolbar: { items: [$tbars] }";
		}
		$sinit		= ($init == false) ? "" : "jsLG" . $this->mId . "();";
		$pg			= ($this->mPaginacion == true) ? "pageSize:50, paging:true," : "";
		$str		= "";
		//$fn1		= "";
		//$fn2		= ($this->mFooterCallback == "") ? "" : ",\n\tfooterCallback:" . $this->mFooterCallback . "";
		/*sorting: true,*/
		$str		.= "$('#" . $this->mId . "').jtable({
        title: '" . $this->mTitle . "',$pg
        actions: { $acts }, selecting:true,$sorting tableId:'tbl_" . $this->mId . "',
        fields: { $flds }$tbars });\n $('#". $this->mId . "').jtable('load'); ";
		$str		= "$sinit\nfunction jsLG" . $this->mId . "(str){\nstr =(typeof str == 'undefined') ? '' : str;\n$str\n}";
		return ($enclose == false) ? $str: "<script>$str</script>";
	}
	function getJsHeaders(){
		//<script src="../js/jtable/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
		return '<link href="../css/jtable/lightcolor/orange/jtable.min.css" rel="stylesheet" type="text/css" /><script src="../js/jtable/jquery.jtable.js" type="text/javascript"></script>';
	}
	function getDiv(){ return "<div id='" . $this->mId  . "'></div>"; }
	function setNoPaginar(){ $this->mPaginacion= false; }
	function setOrdenar(){ $this->mOrden	= true;	}
	function setNoDefaultParam(){ $this->mNoDefParam = true; }
	function setSizeIcon($sz){ $this->mSizeIcon = $sz; }
}
class cHMenu {
	private $mType		= "html";
	private $mDevice	= "desktop";
	private $mID		= "jMenu";
	private $mIncImages	= false;
	private $mKeyEvent	= "";
	private $mFilter	= "";

	public $PARENT		= "parent";
	public $DESKTOP		= "desktop";
	
	private $mEnableLastmenu	= true;
	
	private $mIsMobile	= false;
	private $mDisCaptacion		= array(8000, 1030, 1020, 8050);
	private $mDisSeguimiento	= array(4000);
	private $mDisContable		= array(5000);
	private $mDisGrupo			= array(2010, 2008, 2030, 2011);
	private $mDisAML			= array(7000, 71000);
	private $mDisCredPers		= array(3010);
	private $mDisLeasing		= array(3040, 2064);
	private $mDisNomina			= array(2052, 1060, 18800);
	private $mDisAports			= array(1040, 1008);
	private $mDisTesofe			= array(9000);
	private $mDisables			= array();
	private $mNumDis			= 0;

	function __construct($type= OUT_HTML, $Device = "desktop"){
		$this->mDevice	= $Device;
		$this->mType	= $type;
		if(MODULO_CAPTACION_ACTIVADO == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisCaptacion);
		}
		if(MODULO_SEGUIMIENTO_ACTIVADO == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisSeguimiento);
		}
		if(MODULO_CONTABILIDAD_ACTIVADO == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisContable);
		}
		if(MODULO_AML_ACTIVADO == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisAML);
		}
		if(PERSONAS_CONTROLAR_POR_GRUPO == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisGrupo);
		}
		if(PERSONAS_CONTROLAR_POR_EMPRESA == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisNomina);
		}
		if(PERSONAS_CONTROLAR_POR_APORTS == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisAports);
		}
		if(CREDITO_CONTROLAR_POR_PERIODOS == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisCredPers);
		}
		if(MODULO_LEASING_ACTIVADO == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisLeasing);
		}
		if(MODULO_CAJA_ACTIVADO == false){
			$this->mDisables	= array_merge($this->mDisables, $this->mDisTesofe);
		}
		//if(CREDITO_CONTROLAR_POR_ORIGEN)
		$this->mNumDis	= count($this->mDisables);
		
		
	}
	function setIsMobile($mobile = true){ 
		$this->mIsMobile 		= $mobile; 
		$this->mIncImages 		= true;
		if($this->mEnableLastmenu == true){
			$this->mIsMobile	= false;
		}	
	}

	function setID($id){ $this->mID	= $id; }
	function setKeyEvent($evt){ $this->mKeyEvent = $evt; }
	function getAll($liTags = ""){

		$xL				= new cLang();
		$xQl			= new MQL();
		$xBtn			= new cHButton();
		$ConHijos 		= ($this->mIsMobile == false) ? true : false;
		$pUSRNivel 		= $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
		$menu			= "";
		//OR FIND_IN_SET('$pUSRNivel@ro', menu_rules)>0)
		$this->mFilter	= " AND FIND_IN_SET('$pUSRNivel@rw', menu_rules)>0 ";
		$filter			= $this->mFilter;
		$sql_TN1 		= "SELECT * FROM general_menu WHERE menu_parent=0 $filter AND menu_parent = 0 ORDER BY menu_order ";
		$mmenu			= null;
		$xCache			= new cCache();
		$idcache		= ($ConHijos == true) ? "menu.childs.$pUSRNivel"  : "menu.normal.$pUSRNivel";
		
		if($xCache->isReady() == true){
			$mmenu		= $xCache->get($idcache);
		}
		if($mmenu == null){
			$rs				= $xQl->getDataRecord($sql_TN1);
			foreach ($rs as $rw){
				$Clave		= $rw["idgeneral_menu"];
				$xItem		= new cHMenuItem($Clave, $rw);
				$mxItem		= "";
				$run		= true;
				//========== Checa si existen el Item en disables
				if($this->mNumDis > 0){
					if(in_array($Clave, $this->mDisables)){
						$run	= false;
					}
				}
				
				
				if($run == true){
					if($ConHijos == true){
						$mxItem		= $this->getItems($Clave);
						$mxItem		= (trim($mxItem) == "") ? "" : "<ul>" . $mxItem . "</ul>";
					}

					$menu		.= $xItem->getLi($this->mIncImages, $mxItem, $liTags);
				}
			}
			$mmenu			= "<ul id=\"" . $this->mID . "\">"  . $menu . "</ul>";
		}
		return $mmenu;
	}
	function getItems($parent = 0){
		$ConHijos 	= ($this->mIsMobile == false) ? true : false;
		$filter		= $this->mFilter;
		$xCache		= new cCache();
		$idx		= ($ConHijos == false) ? "mnu-ch-$parent-" . crc32($filter) : "mnu-sm-$parent-" . crc32($filter);
		$menu		= "";
		$menu		= $xCache->get($idx);
		
		if($menu === null){
			$sql		= "SELECT * FROM `general_menu` WHERE (`general_menu`.`menu_parent` =$parent) $filter ORDER BY menu_order";
			$xQL		= new MQL();
			$rs			= $xQL->getDataRecord($sql);
			foreach($rs as $rw){
				$Clave		= $rw["idgeneral_menu"];
				$run		= true;
				
				if($this->mNumDis > 0){
					if(in_array($Clave, $this->mDisables)){
						$run	= false;
					}
				}
				if($run == true){
					$xItem		= new cHMenuItem($Clave, $rw);
					$xItem->setIsMobile($this->mIsMobile);
					
					$subMenu	= "";
					if($this->mIsMobile == false){
						if( ($xItem->TIPO == $this->PARENT) AND ($ConHijos == true) ){
							$subMenu	= $this->getChilds($Clave);
							$subMenu	= (trim($subMenu) == "") ? "" : "<ul>$subMenu</ul>";
							//setLog($Clave);
						}
					}
					$menu		.= $xItem->getLi($this->mIncImages, $subMenu);
				}
			}
			$rs				= null;
			$xCache->set($idx, $menu);
		}
		return $menu;
	}
	function getChilds($MenuParent){
		$ConHijos 	= ($this->mIsMobile == false) ? true : false;
		$filter		= $this->mFilter;
		$ql			= new MQL();
		$sql		= "SELECT *	FROM `general_menu` WHERE (`general_menu`.`menu_parent` = $MenuParent) $filter  ORDER BY menu_order";
		$rs			= $ql->getDataRecord($sql);
		$childs		= "";
		foreach ($rs as $rw){
			$Clave		= $rw["idgeneral_menu"];
			$xItem		= new cHMenuItem($Clave, $rw);
			$xItem->setIsMobile($this->mIsMobile);
			$subMenu	= "";
			if( ($xItem->TIPO == $this->PARENT) AND ($ConHijos == true) ){

				$subMenu	= $this->getChilds($Clave);
				$subMenu		= (trim($subMenu) == "") ? "" : "<ul>$subMenu</ul>";
			}
			$childs		.= $xItem->getLi($this->mIncImages, $subMenu);
		}
		$rs				= null;
		return $childs;
	}
	function getSubChilds($MenuParent){
		$ConHijos 	= ($this->mIsMobile == false) ? true : false;
		$menu		= "";
		$ql			= new MQL();
		$filter		= $this->mFilter;
		$sql		= "SELECT * FROM `general_menu` WHERE (`general_menu`.`menu_parent` =$MenuParent) $filter ORDER BY menu_order";
		$rs			= $ql->getDataRecord($sql);
		foreach($rs as $rw){
			$Clave		= $rw["idgeneral_menu"];
			$xItem		= new cHMenuItem($Clave, $rw);
			$xItem->setIsMobile($this->mIsMobile);
			
			$subMenu	= "";
			if( ($xItem->TIPO == $this->PARENT) AND ($ConHijos == true) ){
				$subMenu	= $this->getSubChilds($Clave);
			}
			$subMenu		= (trim($subMenu) == "") ? "" : "<ul>$subMenu</ul>";
			$menu		.= $xItem->getLi($this->mIncImages, $subMenu);
		}
		$rs				= null;
		return $menu;
	}
	function add(){  }
	function setIncludeIcons(){ $this->mIncImages	= true; }
	function get(){	}
	function setDevice($Device){ $this->mDevice	= $Device;	}
}



class cHTablaDic {
	function __construct(){
		
	}
	function getHGuardarRelacion($idpersona, $callback='function(){}', $EsFisica = true){
		
		$xTbl		= new cHTabla("idtblrels");$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText(); $xText->setDivClass(""); $xChk->setDivClass("");
		$xBtn		= new cHButton();
		$xRuls		= new cReglaDeNegocio();
		$xRels		= new cPersonasRelaciones(false, false);
		
		$xUl		= new cHUl("idtools", "ul", "tags blue");
		
		$RelsSAct	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_RELS_SOLOACTIV);		//regla de negocio
		$depende	= ($EsFisica == true) ? "$('#dependiente').prop('checked')" : "false";
		
		$txtjs		= "var xP=new PersGen();xP.addRelacion({persona : $idpersona,relacionado:$('#idpersona').val(),tipo:$('#idtipoderelacion').val(),parentesco :  $('#idtipodeparentesco').val(), depende : $depende, callback : $callback });$('#idpersona').val(0);";
		$xUl->setTags("");
		$xUl->li($xBtn->getBasic("TR.Guardar", $txtjs, $xBtn->ic()->GUARDAR, "idguardar", false, true));
		$xTbl->initRow();
		$xTbl->addTD($xText->getDeNombreDePersona());
		$xTbl->addTD($xHSel->getListaDeTiposDeRelaciones2("", false, $EsFisica )->get("") );
		if($EsFisica == true){
			$xTbl->addTD($xHSel->getListaDeTiposDeParentesco()->get("")  );
			$xTbl->addTD($xChk->get("TR.es dependiente_economico", "dependiente") );
		} else {
			$xTbl->addTD( $xText->getHidden("idtipodeparentesco", $xRels->CONSANGUINIDAD_NINGUNA) );
		}
		$xTbl->addRaw("<td class='toolbar-24'>". $xUl->get() . "</td>" );
		$xTbl->endRow();

		return $xTbl->get();
	}
}

class cFormatosDelSistema {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "general_contratos";
	private $mTipo			= 0;
	private $mTags			= "";
	private $mEsArrend		= false;
	private $mEsTodas		= false;
	private $mEsPersonaM	= false;
	private $mEsPersonaF	= false;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cGeneral_contratos();
		$xTC		= new cCreditosDatosDeOrigen();
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave	= $data[$xT->getKey()];
			$this->mNombre	= $data[$xT->TITULO_DEL_CONTRATO];
			$this->mTipo	= $data[$xT->TIPO_CONTRATO];
			$this->mTags	= $data[$xT->TAGS];
			if(strpos($this->mTags, $xTC->ORIGEN_ARRENDAMIENTO) !== false){
				$this->mEsArrend	= true;
			}
			if(strpos($this->mTags, SYS_TODAS) !== false){
				$this->mEsTodas	= true;
			}
			if(strpos($this->mTags, "pm") !== false){
				$this->mEsPersonaM	= true;
			}
			if(strpos($this->mTags, "pf") !== false){
				$this->mEsPersonaF	= true;
			}
			$this->mObj		= $xT;
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function getTipo(){ return $this->mTipo; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function getEsArrendamiento(){ return $this->mEsArrend; }
	function getEsTodas(){ return $this->mEsTodas; }
	function getEsPersonaMoral(){ return $this->mEsPersonaM; }
	function getEsPersonaFisica(){ return $this->mEsPersonaF; }
	
	function add(){}
	function getSQL_Lista($Activos=true, $tipo = 0, $subtipo = 0, $figura = "", $tag1="", $tag2=""){
		$tipo	    = setNoMenorQueCero($tipo);
		$subtipo    = setNoMenorQueCero($subtipo);
		$ByActivos 	= ($Activos == true) ? " AND (`estatus`='alta') " : "";
		$ByTipo		= ($tipo>0) ? " AND (`tipo_contrato`=$tipo) " : "";
		$BySubtipo  = ($subtipo>0) ? " AND (`tags` LIKE '%$subtipo%' OR `tags` LIKE '%" . SYS_TODAS .  "%') " : "";
		$sql	="SELECT * FROM `general_contratos` WHERE `idgeneral_contratos` > 0 $ByTipo $BySubtipo $ByActivos ORDER BY `titulo_del_contrato`";
		//setLog($sql);
		return $sql;
	}
}
?>

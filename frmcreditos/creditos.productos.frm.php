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
    $theFile            = __FILE__;
    $permiso            = getSIPAKALPermissions($theFile);
    if($permiso === false){    header ("location:../404.php?i=999");    }
    $_SESSION["current_file"]    = addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.Editar PRODUCTO", HP_FORM);
$xT			= new cTipos();
$ql			= new MQL();
$dSN		= array("1"=>"SI", "0"=>"NO");
$msg		= "";
$jxc 		= new TinyAjax();

function jsaGetCuentas($cuenta){
	$xCta	= new cCuentaContableEsquema($cuenta);
	$sql 	= "SELECT numero, nombre FROM contable_catalogo WHERE numero LIKE '" . $xCta->CUENTARAW . "%' AND afectable=1  ORDER BY numero LIMIT 0,10";
	$ql		= new MQL();
	$rs		= $ql->getDataRecord($sql);
	$h		= "";
	foreach($rs as $rows){
		$xCta2	= new cCuentaContableEsquema($rows["numero"]);
		$h	.= "<option value=\"" . $rows["numero"] . "\">" . $xCta2->CUENTARAW . "-" . $rows["nombre"] . "</option>";
	}
	return $h;
}

$jxc ->exportFunction('jsaGetCuentas', array('idcuenta'), "#listadocuentas");
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO, MQL_RAW);
$opcion		= parametro("tema", SYS_NINGUNO, MQL_RAW);
$xHP->init();
//$clave = parametro("idcreditos_tipoconvenio", null, MQL_INT);
$clave 		= parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
$xTabla		= new cCreditos_tipoconvenio();


if($clave == null){
	$step		= MQL_ADD;
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}

if($action == MQL_ADD){
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);
		$xTabla->query()->insert()->save();
	}	
} else if($action == MQL_MOD){
	//iniciar
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){ 
		$xTabla->setData( $xTabla->query()->initByID($clave)); 
		$xTabla->setData($_REQUEST);
		$xTabla->query()->update()->save($clave);
		$opcion = "fin";
	}
} else {
	
}

$xFRM	= new cHForm("frmcreditos_tipoconvenio", "../frmcreditos/creditos.productos.frm.php?id=$clave&tema=$opcion&action=$step");
//setLog("../frmcreditos/creditos.productos.frm.php?action=$step");
//$xFRM->addSubmit();
$xFRM->OHidden("idcreditos_tipoconvenio", $xTabla->idcreditos_tipoconvenio()->v(), "TR.Clave");
$xFRM->OHidden("tipo_convenio", $xTabla->tipo_convenio()->v(), "TR.Clave");
$xTxt		= new cHText();
$xFRM->setTitle($xHP->getTitle() . " : " . $xTabla->descripcion_tipoconvenio()->v());
switch ($opcion){
	case "contablecapital":
		$xFRM->addGuardar();
		
		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_cartera_vigente", $xTabla->contable_cartera_vigente()->v(), false, CONTABLE_MAYOR_CARTERA_VIG, "TR.CARTERA VIGENTE NORMAL"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("capital_vigente_normal", $xTabla->capital_vigente_normal()->v(), false, CONTABLE_MAYOR_CARTERA_VIG, "TR.CARTERA VIGENTE NORMAL"));
		
		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_cartera_vencida", $xTabla->contable_cartera_vencida()->v(), false, CONTABLE_MAYOR_CARTERA_VENC, "TR.CARTERA VENCIDA NORMAL"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("capital_vencido_normal", $xTabla->capital_vencido_normal()->v(), false, CONTABLE_MAYOR_CARTERA_VENC, "TR.CARTERA VENCIDA NORMAL"));
		
		$xFRM->addHElem($xTxt->getDeCuentaContable("capital_vencido_reestructurado", $xTabla->capital_vencido_reestructurado()->v(), false, CONTABLE_MAYOR_CARTERA_REST_VENC, "TR.CARTERA REESTRUCTURADO VENCIDO"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("capital_vigente_reestructurado", $xTabla->capital_vigente_reestructurado()->v(), false, CONTABLE_MAYOR_CARTERA_REST, "TR.CARTERA REESTRUCTURADO NORMAL"));
		
		$xFRM->addHElem($xTxt->getDeCuentaContable("capital_vencido_renovado", $xTabla->capital_vencido_renovado()->v(), false, CONTABLE_MAYOR_CARTERA_REN_VENC, "TR.CARTERA RENOVADO VENCIDO"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("capital_vigente_renovado", $xTabla->capital_vigente_renovado()->v(), false, CONTABLE_MAYOR_CARTERA_REN, "TR.CARTERA RENOVADO NORMAL"));
		
		

		
		break;
	case "contableinteres":
		$xFRM->addGuardar();
		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_intereses_devengados", $xTabla->contable_intereses_devengados()->v(), false, CONTABLE_MAYOR_INT_DEV_VIG, "TR.Cuenta_contable Interes Vigente devengado"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("interes_vigente_normal", $xTabla->interes_vigente_normal()->v(), false, CONTABLE_MAYOR_INT_DEV_VIG, "TR.Cuenta_contable Interes Vigente devengado"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("interes_vigente_reestructurado", $xTabla->interes_vigente_reestructurado()->v(), false, CONTABLE_MAYOR_INT_DEV_VIG, "TR.CUENTA_CONTABLE para Interes Vigente Reestructurado"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("interes_vigente_renovado", $xTabla->interes_vigente_renovado()->v(), false, CONTABLE_MAYOR_INT_DEV_VIG, "TR.Cuenta_CONTABLE para Interes Vigente Renovados"));
		
		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_intereses_vencidos", $xTabla->contable_intereses_vencidos()->v(), false, CONTABLE_MAYOR_INT_DEV_VENC, "TR.Cuenta_CONTABLE para Interes Vencido devengado"));		
		$xFRM->addHElem($xTxt->getDeCuentaContable("interes_vencido_normal", $xTabla->interes_vencido_normal()->v(), false, CONTABLE_MAYOR_INT_DEV_VENC, "TR.Cuenta_CONTABLE para Interes Vencido devengado"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("interes_vencido_reestructurado", $xTabla->interes_vencido_reestructurado()->v(), false, CONTABLE_MAYOR_INT_DEV_VENC, "TR.Cuenta de Balance para Interes Vencido Reestructurado"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("interes_vencido_renovado", 	$xTabla->interes_vencido_renovado()->v(), false, CONTABLE_MAYOR_INT_DEV_VENC,  "TR.Cuenta de Balance para Interes Vencido devengado"));

		$xFRM->addHElem($xTxt->getDeCuentaContable("interes_cobrado", 	$xTabla->interes_cobrado()->v(), false, CONTABLE_MAYOR_INGRESO_INT, "TR.CUENTA_CONTABLE INTERES_NORMAL_COBRADO"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("moratorio_cobrado", $xTabla->moratorio_cobrado()->v(), false, CONTABLE_MAYOR_INGRESO_MORA, "TR.CUENTA_CONTABLE INTERES_MORA_COBRADO"));

		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_intereses_cobrados", $xTabla->contable_intereses_cobrados()->v(), false, CONTABLE_MAYOR_INGRESO_INT, "TR.CUENTA_CONTABLE para Intereses Normales Cobrados"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_intereses_moratorios", $xTabla->contable_intereses_moratorios()->v(), false, CONTABLE_MAYOR_INGRESO_MORA, "TR.CUENTA_CONTABLE para Intereses Moratorios Cobrados"));
		
		
		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_cartera_castigada", $xTabla->contable_cartera_castigada()->v(), false, CONTABLE_CLAVE_EGRESOS, "TR.CARTERA CASTIGADA"));
		$xFRM->addHElem($xTxt->getDeCuentaContable("contable_intereses_anticipados", $xTabla->contable_intereses_anticipados()->v(), false, CONTABLE_CLAVE_PASIVO, "TR.CUENTA_CONTABLE para Intereses Cobrado por Anticipado"));
		//$xFRM->addHElem( $xTxt->get("contable_intereses_anticipados", $xTabla->contable_intereses_anticipados()->v(), "TR.Cuenta_Contable para Intereses Cobrado por Anticipado"));
		break;		
	case "tasas":
		$xFRM->addGuardar();
		$xFRM->OTasa("interes_moratorio", $xTabla->interes_moratorio()->v(), "TR.Tasa anualizada de interes moratorio");
		$xFRM->OTasa("interes_normal", $xTabla->interes_normal()->v(), "TR.Tasa anualizada del interes normal");
		$d34 	= $ql->getArrayRecord("SELECT * FROM `creditos_tipo_de_calculo_de_interes`");
		$xFRM->OSelect("base_de_calculo_de_interes", $xTabla->base_de_calculo_de_interes()->v(), "TR.base de calculo de interes", $d34);

		//$xFRM->OTasa("porcentaje_ica", $xTabla->porcentaje_ica()->v(), "TR.TASA Cobrado por Anticipado");
		$xFRM->OHidden("porcentaje_ica", $xTabla->porcentaje_ica()->v());
		
		$xFRM->OTasa("porcentaje_otro_credito", $xTabla->porcentaje_otro_credito()->v(), "TR.Tasa para obtener otro credito");
		$xFRM->OTasa("porciento_garantia_liquida", $xTabla->porciento_garantia_liquida()->v(), "TR.Tasa garantia liquida");		
		if(MODULO_CAPTACION_ACTIVADO == true){
			$xFRM->OTasa("tasa_ahorro", $xTabla->tasa_ahorro()->v(), "TR.TASA de Ahorro");
		} else {
			$xFRM->OHidden("tasa_ahorro", $xTabla->tasa_ahorro()->v());
		}
		$xFRM->OTasa("tasa_iva", $xTabla->tasa_iva()->v(), "TR.Tasa de Impuesto_al_Consumo");
		$xFRM->OSelect("iva_incluido", $xTabla->iva_incluido()->v() , "TR.impuesto_al_consumo en la Tasa de Interes", $dSN);
		break;
	case "dias":
		$xFRM->addGuardar();
		$xFRM->OMoneda("dias_maximo", $xTabla->dias_maximo()->v(), "TR.Numero de Dias Maximo de Plazo");
		$xFRM->OMoneda("tolerancia_dias_no_pago", $xTabla->tolerancia_dias_no_pago()->v(), "TR.tolerancia de espera en dias por falta de pago");
		$xFRM->OMoneda("tolerancia_dias_primer_abono", $xTabla->tolerancia_dias_primer_abono()->v(), "TR.tolerancia dias para el primer abono");				
		break;
	case "cantidades":
		$xFRM->addGuardar();
			$xFRM->OMoneda("minimo_otorgable", $xTabla->minimo_otorgable()->v(), "TR.monto minimo", true);
			$xFRM->OMoneda("maximo_otorgable", $xTabla->maximo_otorgable()->v(), "TR.monto maximo", true);
						
			$xFRM->OMoneda("numero_creditos_maximo", $xTabla->numero_creditos_maximo()->v(), "TR.numero creditos maximo por persona");
			$xFRM->OMoneda("pagos_maximo", $xTabla->pagos_maximo()->v(), "TR.Numero de pagos maximo");
			$xFRM->OMoneda("numero_de_pagos_preferente", $xTabla->numero_de_pagos_preferente()->v(), "TR.numero de pagos preferente");
				
			$xFRM->OMoneda("fuente_de_fondeo_predeterminado", $xTabla->fuente_de_fondeo_predeterminado()->v(), "TR.Fuente de fondeo predeterminado");
			break;		
	case "garantias":
		$xFRM->addGuardar();
		$xFRM->OMoneda("numero_avales", $xTabla->numero_avales()->v(), "TR.numero avales");
		$xFRM->OMoneda("razon_garantia", $xTabla->razon_garantia()->v(), "TR.Razon de la garantia Fisica sobre el Credito");
		$arropts	= array("todas"=>"TODAS", "cuenta_inversion"=>"CUENTA INVERSION", "aportacion"=>"APORTACION", "prenda" => "PRENDARIA", "inmuebles" => "INMUEBLES");
		$xFRM->OSelect("tipo_de_garantia", $xTabla->tipo_de_garantia()->v() , "TR.tipo de garantia reales aceptadas", $arropts);
		$xFRM->OMoneda("creditos_mayores_a", $xTabla->creditos_mayores_a()->v(), "TR.Monto minimo para solicitar Garantias");
		break;
	case "comisiones":
		$xFRM->addGuardar();
		$xFRM->OSelect("aplica_gastos_notariales", $xTabla->aplica_gastos_notariales()->v() , "TR.se aplican gastos notariales", $dSN);
		$xFRM->OSelect("aplica_mora_por_cobranza", $xTabla->aplica_mora_por_cobranza()->v() , "TR.se aplican mora por cobranza", $dSN);
		$xFRM->OMoneda("comision_por_apertura", $xTabla->comision_por_apertura()->v(), "TR.Tasa de comision por apertura");
		$xFRM->OMoneda("monto_fondo_obligatorio", $xTabla->monto_fondo_obligatorio()->v(), "TR.Fondo de defuncion", true);
						
		break;
	case "permisos":
		$xFRM->addGuardar();
		$off = $xSel->getListaDeOficiales("oficial_seguimiento");
		$off->setOptionSelect($xTabla->oficial_seguimiento()->v());
		$xFRM->addHElem( $off->get("TR.oficial por defecto", true) );
				
		$d4 	= $ql->getArrayRecord("SELECT * FROM `creditos_tipo_de_autorizacion` ");
		$xFRM->OSelect("tipo_autorizacion", $xTabla->tipo_autorizacion()->v(), "TR.tipo de autorizacion", $d4);
		$xFRM->OMoneda("nivel_autorizacion_oficial", $xTabla->nivel_autorizacion_oficial()->v(), "TR.nivel autorizacion oficial");
		$xFRM->OMoneda("nivel_riesgo", $xTabla->nivel_riesgo()->v(), "TR.nivel riesgo por defecto");
				
		$d1 	= $ql->getArrayRecord("SELECT * FROM creditos_estatus");
		$xFRM->OSelect("estatus_predeterminado", $xTabla->estatus_predeterminado()->v() , "TR.Estado predeterminado", $d1);		
		$xFRM->OText("leyenda_docto_autorizacion", $xTabla->leyenda_docto_autorizacion()->v(), "TR.leyenda del Documento de autorizacion");
		break;
		
		case "codigo":
			$xFRM->addGuardar();
			$xFRM->OTextArea("code_valoracion_javascript", $xTabla->code_valoracion_javascript()->v(), "TR.code valoracion javascript");
			$xFRM->OTextArea("php_monto_maximo", $xTabla->php_monto_maximo()->v(), "TR.php monto maximo");
			$xFRM->OTextArea("valoracion_php", $xTabla->valoracion_php()->v(), "TR.valoracion php");
			$xFRM->OTextArea("pos_modificador_de_interes", $xTabla->pos_modificador_de_interes()->v(), "TR.pos modificador de interes");
			$xFRM->OTextArea("pre_modificador_de_autorizacion", $xTabla->pre_modificador_de_autorizacion()->v(), "TR.pre modificador de autorizacion");
			$xFRM->OTextArea("pre_modificador_de_interes", $xTabla->pre_modificador_de_interes()->v(), "TR.pre modificador de interes");
			$xFRM->OTextArea("pre_modificador_de_ministracion", $xTabla->pre_modificador_de_ministracion()->v(), "TR.pre modificador de ministracion");
			$xFRM->OTextArea("pre_modificador_de_solicitud", $xTabla->pre_modificador_de_solicitud()->v(), "TR.pre modificador de solicitud");
			$xFRM->OTextArea("pre_modificador_de_vencimiento", $xTabla->pre_modificador_de_vencimiento()->v(), "TR.pre modificador de vencimiento");			
			break;		
	default:
		$xFRM->addGuardar();
		$xFRM->OText("descripcion_tipoconvenio", $xTabla->descripcion_tipoconvenio()->v(), "TR.Nombre");
		$xFRM->OText("descripcion_completa", $xTabla->descripcion_completa()->v(), "TR.descripcion completa");
		$xFRM->OText("nombre_corto", $xTabla->nombre_corto()->v(), "TR.NOMBRE_CORTO");
		$xFRM->OSelect("estatus", $xTabla->estatus()->v() , "TR.Estado Actual del Producto", array("baja"=>"BAJA", "activo"=>"ACTIVO"));
		
		$xFRM->OSelect("tipo_de_convenio", $xTabla->tipo_de_convenio()->v() , "TR.TIPO_AGRUPACION", 			array("1"=>"INDIVIDUAL", "3"=>"GRUPAL"));
		$xFRM->OSelect("tipo_de_integracion", $xTabla->tipo_de_integracion()->v(), "TR.TIPO_AGRUPACION", 	array("1"=>"INDIVIDUAL", "3"=>"GRUPAL"));
		
		$d2 	= $ql->getArrayRecord("SELECT * FROM creditos_modalidades");
		$xFRM->OSelect("tipo_de_credito", $xTabla->tipo_de_credito()->v() , "TR.Clasificacion Legal", $d2);
			
		//		
		$xFRM->OMoneda("tipo_de_interes", $xTabla->tipo_de_interes()->v(), "TR.tipo de interes");
		$xFRM->OMoneda("perfil_de_interes", $xTabla->perfil_de_interes()->v(), "TR.perfil de interes");
		
		$d5 	= $ql->getArrayRecord("SELECT * FROM `creditos_periocidadpagos` ");
		$xFRM->OSelect("tipo_de_periocidad_preferente", $xTabla->tipo_de_periocidad_preferente()->v(), "TR.tipo de periocidad preferente", $d5);
		//$f1 	= array(CREDITO_PRODUCTO_NOMINA =>"NOMINA", CREDITO_PRODUCTO_INDIVIDUAL=>"INDIVIDUAL", CREDITO_PRODUCTO_GRUPOS => "GRUPO");
		$f1		= array( SYS_PRODUCTO_NOMINA => "NOMINA",  SYS_PRODUCTO_ARREND => "ARRENDAMIENTO", SYS_PRODUCTO_GRUPOS => "GRUPOS", SYS_PRODUCTO_INDIVIDUAL => "INDIVIDUAL", SYS_PRODUCTO_REVOLVENTES => "REVOLVENTES" );
		
		$xFRM->OSelect("tipo_en_sistema", $xTabla->tipo_en_sistema()->v(), "TR.tipo en sistema", $f1);
				
		$xFRM->OText("clave_de_tipo_de_producto", $xTabla->clave_de_tipo_de_producto()->v(), "TR.clave de tipo de producto en SIC");
		
		$xFRM->OText("path_del_contrato", $xTabla->path_del_contrato()->v(), "TR.URl del contrato");
		$xFRM->OMoneda("codigo_de_contrato", $xTabla->codigo_de_contrato()->v(), "TR.Numero de formato en el sistema");
		
	break;
	case "fin":
		$xFRM->addCerrar("", 2);
		$xFRM->addAvisoRegistroOK($msg);
	breaK;	
}

echo $xFRM->get();
?>
<script>
function jsKeyAction(evt, ctrl){
    evt=(evt) ? evt:event;
    var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var cta	= String(ctrl.value);
	if ((charCode >= 48 && charCode <= 57)||(charCode >= 96 && charCode <= 105)) {
		$("#idcuenta").val(ctrl.value);
		if (cta.length > 2) { jsaGetCuentas();	}
	}
}

</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
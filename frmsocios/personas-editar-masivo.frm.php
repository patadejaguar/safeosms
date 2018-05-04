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
$xHP		= new cHPage("TR.EDITAR PERSONAS", HP_FORM);
$xQL		= new MQL();
$cT			= new cTipos();
$xRuls		= new cReglaDeNegocio();
$msg		= "";
$jxc 		= new TinyAjax();
$xSel		= new cHSelect();

$persona	= (isset($_GET["i"])) ? $_GET["i"] : false;		//CLAVE
$f 			= (isset($_GET["f"])) ? $_GET["f"] : false;		//form dependiente
$buscarPor	= (isset($_GET["o"])) ? $_GET["o"] : PERSONAS_BUSCAR_POR;		//tipo de busqueda: n = nombre, c = curp, r = rfc, s = socio, nf = Nombre con FORM

$tipo_de_ingreso	= parametro("idtipodeingreso", 0, MQL_INT); $tipo_de_ingreso	= parametro("tipodeingreso", $tipo_de_ingreso, MQL_INT); $tipo_de_ingreso	= parametro("tipoingreso", $tipo_de_ingreso, MQL_INT);

$OtherEvent	= parametro("ev", "", MQL_RAW);	//Otro Evento Desatado
$OtherEvent	= parametro("callback", "", MQL_RAW);
$control	= parametro("control", "idsocio", MQL_RAW);

$tiny 		= (isset($_GET["tinybox"])) ? true : false;
$BuscarConID= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_BUSQUEDA_IDINT);		//regla de negocio
//si existe la forma y no existe condicionante
if($persona > DEFAULT_SOCIO ){
	$buscarPor	= "s";
}
$buscarPor	= ( $f != false ) ? "nc" : $buscarPor;
$persona	= ($buscarPor == "nc" AND $cT->cInt($persona) > 0) ? "" : $persona;

$arrLst	= array (
		"n"  => 0,
		"nc" => 1,
		"s"  => 2,
		"c"  => 3,
		"r"  => 4,
		"e"  => 5,
		"idi" => 6
);
//ESCRIBA_EL_TEXTO_A_BUSCAR
function fmt_string($string){
	if(strlen(trim($string)) > 4) {
		$string = substr(trim($string), 0, 4);
	} else {
		$string = trim($string);
	}
	return $string;
}
function jsaGetListadoDeEmpresas(){
	$gssql= "SELECT * FROM socios_aeconomica_dependencias";
	$cDE = new cSelect("idtextobusqueda", "idtextobusqueda", $gssql);
	$cDE->addEvent("onchange", "jsShowSocios");
	$cDE->setLabel("TR.NOMBRE EMPRESA");
	$cDE->setEsSql();
	$cDE->setOptionSelect(DEFAULT_EMPRESA);
	return $cDE->get("", false);
}
function jsaGetListadoDeProductos(){

	$gssql= "SELECT * FROM creditos_tipoconvenio ORDER BY `descripcion_tipoconvenio`";
	$cDE = new cSelect("idtextobusqueda", "idtextobusqueda", $gssql);
	$cDE->addEvent("onchange", "jsShowSocios");
	$cDE->setLabel("TR.NOMBRE DEL PRODUCTO");
	$cDE->setEsSql();

	$cDE->setOptionSelect(DEFAULT_TIPO_CONVENIO);
	return $cDE->get("", false);
}
function jsaShowSocios($texto, $tipo_de_busqueda, $todos = false, $idinterno = "", $tipoingreso = 0, $sucorigen=""){
	$strTbls			= "";
	$ByForm				= false;
	$MostrarGars		= true;
	$MostrarPartes		= true;
	$sqlL				= new cSQLListas();
	$xFil				= new cTiposLimpiadores();
	$xUser				= new cSystemUser();
	$xVals				= new cReglasDeValidacion();
	$WSoc				= "";
	$WPrel				= "";
	$extras				= "";
	$xIc				= new cHImg();
	$xT					= new cTipos();
	$todos				= $xT->cBool($todos);
	$w1					= ($todos == true) ? "" : " AND (tipoingreso != " . TIPO_INGRESO_SDN ." AND tipoingreso != " . TIPO_INGRESO_PEP ." AND tipoingreso != " . TIPO_INGRESO_USUARIO ." AND tipoingreso != " . FALLBACK_PERSONAS_TIPO_ING ." AND `codigo` != " . DEFAULT_SOCIO . ") AND (`socios_general`.`estatusactual`!=20) ";
	
	
	if($tipoingreso > 0){
		$w1				= " AND (tipoingreso=$tipoingreso) ";
	}
	if ( $tipo_de_busqueda == "nc" ){
		$ByForm			= true;
	}
	if(OPERACION_LIBERAR_SUCURSALES == false AND $xUser->getEsCorporativo() == false){
		$w1				.= " AND `socios_general`.`sucursal`='" . $xUser->getSucursal() . "' ";
	}
	//if(OPERACION_LIBERAR_SUCURSALES == false){
		$extras			= ", `socios_general`.`sucursal` ";
	//}
	if($xVals->sucursal($sucorigen) == true){
		$w1				.= " AND (`socios_general`.`sucursal`='$sucorigen') ";
	}
		
	$buscar				= ( ((!isset($texto)) OR (trim($texto) == "") OR ($texto == DEFAULT_SOCIO) OR ($texto == "0")) AND ($idinterno =="") ) ? false : true;
	if ($buscar == false) {
		$sqllike = $sqlL->getListadoDePersonasV2($w1, "0,50", $extras);
		$table_s = new cTabla($sqllike);
		$table_s->OCheckBox("jsAddCola(" . HP_REPLACE_ID . ", this)", "codigo");
		$table_s->setEventKey("var xP=new PersGen();xP.goToPanel");
		
		$strTbls .= $table_s->Show();
	} else {
		$texto 		= trim($texto);
		$texto		= strtoupper($texto);
		//$unico		= (substr(trim($texto), -1) == *)
		if($idinterno == ""){
			$marked		= false;
			switch ( $tipo_de_busqueda ){
				//CURP
				case "c":
					$str		= $xFil->cleanTextoBuscado($texto, 7);
					$WSoc		= " AND (curp LIKE '$str%') ";
					break;
					//IDInterno
				case "idi":
					$str		= $xFil->cleanTextoBuscado($texto);
					$WSoc		= " AND (`idinterna` LIKE '%$str%') ";
					break;
					//NUMERO DE SOCIO
				case "s":
					$str		= $xFil->cleanTextoBuscado($texto);
					$WSoc		= " AND (codigo LIKE '%$str%') ";
					break;
					//NUMERO DEempresa
				case "e":
					$WSoc		= " AND (dependencia = $texto) ";
					break;
					//DEAFULT:
				default:
					$items		= explode(" ", $texto,3);
					//setLog($texto);
					if(substr_count($texto, '*') == 1 AND  substr(trim($texto), -1) == "*"){
						$items	= array(0 => $xFil->cleanTextoBuscado($texto));
						//setLog($texto);
					}
					$nitems		= count($items);
					//balam gon lui
					if($nitems == 3){
						$b1			= $xFil->cleanTextoBuscado($items[0]);
						$b2			= $xFil->cleanTextoBuscado($items[1]);
						$b3			= $xFil->cleanTextoBuscado($items[2]);
						$WSoc		.= ($b1 != "") ? " AND (REPLACE(`apellidopaterno`, ' ', '-') LIKE '%$b1%') " : "";
						$WSoc		.= ($b2 != "") ? " AND (REPLACE(`apellidomaterno`, ' ', '-') LIKE '%$b2%') " : "";
						$WSoc		.= ($b3 != "") ? " AND (REPLACE(`nombrecompleto`, ' ', '-') LIKE '%$b3%') " : "";
							
					} else if ($nitems == 2){
						$b1			= $xFil->cleanTextoBuscado($items[0]);
						$b2			= $xFil->cleanTextoBuscado($items[1]);
						$WSoc		.= ($b1 != "") ? " AND (REPLACE(`apellidopaterno`, ' ', '-') LIKE '%$b1%') " : "";
						$WSoc		.= ($b2 != "") ? " AND (REPLACE(`apellidomaterno`, ' ', '-') LIKE '%$b2%') " : "";
							
					} else {
						$b1			= $xFil->cleanTextoBuscado($items[0]);
						$WSoc		.= " AND ((REPLACE(`apellidopaterno`, ' ', '-') LIKE '%$b1%') OR (REPLACE(`nombrecompleto`, ' ', '-') LIKE '%$b1%') )";
					}

						
					break;
			}
		} else {
			$str		= $xFil->cleanTextoBuscado($idinterno);
			$WSoc		.= " AND (`idinterna` LIKE '%$str%') ";
		}
		if($tipo_de_busqueda == "pp"){ //por credito
			$sqllike	= "SELECT
				
			`socios`.`codigo`,
			`socios`.`nombre`,
			`creditos_solicitud`.`numero_solicitud`        AS `credito`,
			`creditos_solicitud`.`pagos_autorizados`       AS `pagos`,
			(`creditos_solicitud`.`ultimo_periodo_afectado`+1) AS `periodo`,
			`creditos_solicitud`.`saldo_actual`            AS `saldo`,
			`creditos_solicitud`.`monto_parcialidad`       AS `parcialidad`
			FROM
			`creditos_solicitud` `creditos_solicitud`
			INNER JOIN `socios` `socios`
			ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo`
			WHERE
			(`creditos_solicitud`.`saldo_actual` >0.99)
			AND
			(`creditos_solicitud`.`tipo_convenio` =$texto) ORDER BY	`socios`.`nombre` ";
			$table_s = new cTabla($sqllike);

			$table_s->setRowCSS("credito", "center");
			$table_s->setKeyField("codigo");
			$table_s->setWithMetaData();
			$table_s->OCheckBox("jsAddCola(" . HP_REPLACE_ID . ", this)", "codigo");
			
			$strTbls .= $table_s->Show("TR.CREDITOS");
		} else {
			if($todos == true){$WSoc = " `socios_general`.`codigo` >0 $WSoc ";}
			$sqllike = $sqlL->getListadoDePersonasV2($w1 . $WSoc, "0,100", $extras);
			$table_s = new cTabla($sqllike);
			//$table_s->setEventKey("setSocio");
			$table_s->OCheckBox("jsAddCola(" . HP_REPLACE_ID . ", this)", "codigo");
			$table_s->setEventKey("var xP=new PersGen();xP.goToPanel");
			//$table_s->OButton("TR.PANEL", 'jsToPanel(_REPLACE_ID_)', $table_s->ODicIcons()->CONTROL );

			$strTbls .= $table_s->Show();
		}
		//setLog($sqllike);
	}
	return $strTbls;
}
function jsaSetSocioEnSession($socio){	getPersonaEnSession($socio); }


$jxc ->exportFunction('jsaShowSocios', array("idtextobusqueda", "idtipobusqueda","idtodo", "idinterna", "tipodeingreso", "idsucursalorigen"), "#divresultado");
$jxc ->exportFunction('jsaGetListadoDeEmpresas', array(""), "#idbusqueda");
$jxc ->exportFunction('jsaGetListadoDeProductos', array(""), "#idbusqueda");
$jxc ->exportFunction('jsaSetSocioEnSession', array("idsocio"));
$jxc ->process();

$xHP->init("initComponents()");


$xFRM		= new cHForm("elform", "frmbuscarsocio.php");
$xFRM->setTitle($xHP->getTitle());


$xFRM->setNoAcordion();
$xHSel		= new cHSelect();
$xTxt		= new cHText();
$xTxt2		= new cHText();
$xChk		= new cHCheckBox();
$xChk->setDivClass("");
$xFRM->OButton("TR.Buscar", "jsShowSocios()", $xFRM->ic()->BUSCAR);
$xFRM->addToolbar($xChk->get("TR.TODO", "idtodo", true));

$xFRM->OButton("TR.MODIFICAR BATCH", "jsEjecutarBatch()", $xFRM->ic()->EJECUTAR, "idmodbatch", "green");
$xFRM->OButton("TR.EXPORTAR", "jsEjecutarExportar()", $xFRM->ic()->EJECUTAR, "idexp", "blue2");

$xTxt->addEvent("jsGetPersonasByKey(this)", "onkeyup");$xTxt2->addEvent("jsGetPersonasByKey2(this)", "onkeyup");
$xTxt->setDivClass("");
$xHSel->addOptions(array(
		"n" 	=> "Nombre/Iniciales(Paterno Materno Nombre)",
		"nc"	=>$xFRM->getT("TR.Nombre desde un Formulario"),
		"e"		=>$xFRM->getT("TR.POR EMPRESA"),
		"idi"	=>$xFRM->getT("TR.IDINTERNO"),
		"s"		=>$xFRM->getT("TR.CLAVE_DE_PERSONA"),
		"c"		=>$xFRM->getT("TR.IDENTIFICACION_POBLACIONAL"),
		"r"		=>$xFRM->getT("TR.IDENTIFICACION_FISCAL"),
		"pp"	=>$xFRM->getT("TR.POR PRODUCTO CREDITO")
));
$xHSel->setDivClass("");
$xHSel->addEvent("jsMostrarOpciones()", "onchange");
$xFRM->OHidden("idsocio", $persona);
$xFRM->addSeccion("idopciones", "TR.BUSCAR PERSONAS");

if($BuscarConID == true){
	$xTxt2->setDivClass("");
	$lbl	= $xTxt2->getLabel("TR.IDINTERNO");
	$xFRM->addDivMedio($lbl, $xTxt2->getNormal("idinterna", "", ""), "tx12", "tx12");
	$xFRM->addDivMedio($xHSel->get("idtipobusqueda", "", $buscarPor), $xTxt->getNormal("idtextobusqueda", $persona, ""), "tx12", "tx12", array(2=>array("id"=>"idbusqueda")));
} else {
	$xFRM->OHidden("idinterna", "");
	$xFRM->addDivMedio($xHSel->get("idtipobusqueda", "Buscar por :", $buscarPor), $xTxt->getNormal("idtextobusqueda", $persona, "TR.Texto de busqueda"), "tx12", "tx12", array(2=>array("id"=>"idbusqueda")));
}

$xSelSuc	= $xSel->getListaDeSucursales("idsucursalorigen");
$xSelSuc->addVacio(true);
$xFRM->addHElem($xSelSuc->get(true));


$xFRM->ODate("idfechainicial", false, "TR.FECHA_INICIAL");
$xFRM->ODate("idfechafinal", false, "TR.FECHA_FINAL");

$xFRM->endSeccion();
//Opciones de cambios
$xFRM->addSeccion("idsec", "TR.OPCIONES DE CAMBIO");


$xSelSuc	= $xSel->getListaDeSucursales("idsucursal");
$xSelSuc->addEspOption(SYS_TODAS, "-");
$xSelSuc->setOptionSelect(SYS_TODAS);

$xFRM->addHElem($xSelSuc->get(true));

$xFRM->endSeccion();
//


$xFRM->addSeccion("idlistabusqueda", "TR.Resultado");
$xFRM->addHTML("<div id='divresultado'></div>");
$xFRM->endSeccion();

//Tipo de Ingreso falso

$xFRM->OHidden("tipodeingreso", $tipo_de_ingreso);

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>

var OListas	= {};

var mFecha	= "<?php echo fechasys(); ?>";
var xG		= new Gen();
var xP		= new PersGen();
var idsoc	= "<?php echo $control; ?>";
function jsGetPersonasByKey(msrc){
	var mstr	= new String( msrc.value );
	if (mstr.length >= 4){ jsShowSocios(); }  /*Busqueda por TXT*/
}
function jsGetPersonasByKey2(msrc){
	var mstr	= new String( msrc.value );
	if (mstr.length >= 2) { jsShowSocios();	} /*Busqueda por ID*/
}
function initComponents(){
	jsShowSocios();
	$("#idtextobusqueda").focus();
	$("#idtextobusqueda").select();
}
function jsShowSocios(){ jsaShowSocios(); }
function jsToPanel(idpersona){ var xP = new PersGen(); xP.goToPanel(idpersona); }

function jsMostrarOpciones() {
	if ($("#idtipobusqueda").val() == "e") {
		jsaGetListadoDeEmpresas();
	} else if ($("#idtipobusqueda").val() == "pp") {
		jsaGetListadoDeProductos();
	} else {
		$("#idbusqueda").html("<label for='idtextobusqueda'>Texto de Busqueda</label><input name='idtextobusqueda' id='idtextobusqueda' type='text' value='<?php echo $persona; ?>' onkeyup='jsGetPersonasByKey(this)' />");
	}
}





function jsAddCola(id, obj){
	console.log(id);
	if (obj.checked == true) {
		OListas[id] = id;
	} else {
		delete OListas[id];
	}
}

function jsSetUpdate(obj){
	var clave	= $(obj).attr("data-clave");
	var campo	= $(obj).attr("data-campo");
	var valor	= $(obj).val();
	var cnt		= campo + "=" + valor;
	setLog(cnt);
	xG.save({tabla:'operaciones_mvtos', id:clave, content: cnt})
	xG.markTR({src:obj});
}
function jsEjecutarBatch(){
	xG.confirmar({msg: "¿ Desea ejecutar la actualizacion masiva ?", callback: jsEjecutarBatchReady});
}
function jsEjecutarExportar(){
	xG.confirmar({msg: "¿ Desea ejecutar la actualizacion masiva ?", callback: jsEjecutarExportReady});
}
function jsEjecutarExportReady(){
	for (var itms in OListas) {
		var idpersona	= OListas[itms];
		xG.svc({ url : "../svc/pc.svc.php?cmd=EXPORT&persona=" + idpersona, 
			callback : function(data){
				xG.alerta({ msg: data.message });
			} });
	}
}
function jsEjecutarBatchReady(){
	var idsucursal	= $("#idsucursal").val();
	var cnt			= "";
	if(idsucursal !== "todas"){
		cnt = "sucursal=" + idsucursal;
	}
	for (var itms in OListas) {
		var idpersona	= OListas[itms];
		xG.save({tabla:'socios_general', id:idpersona, content: cnt})
	}
}
</script>
</html>
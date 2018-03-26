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
//=====================================================================================================
$xHP	= new cHPage("TR.Buscar Personas", HP_FORM);
$xQL	= new MQL();
$cT		= new cTipos();
$xRuls	= new cReglaDeNegocio();
$msg	= "";
$jxc 	= new TinyAjax();

$persona			= (isset($_GET["i"])) ? $_GET["i"] : false;		//CLAVE
$f 					= (isset($_GET["f"])) ? $_GET["f"] : false;		//form dependiente
$buscarPor			= (isset($_GET["o"])) ? $_GET["o"] : PERSONAS_BUSCAR_POR;		//tipo de busqueda: n = nombre, c = curp, r = rfc, s = socio, nf = Nombre con FORM

$nextstep			= parametro("next", "", MQL_RAW);

$tipo_de_ingreso	= parametro("idtipodeingreso", 0, MQL_INT); $tipo_de_ingreso	= parametro("tipodeingreso", $tipo_de_ingreso, MQL_INT); $tipo_de_ingreso	= parametro("tipoingreso", $tipo_de_ingreso, MQL_INT);

$OtherEvent			= parametro("ev", "", MQL_RAW);	//Otro Evento Desatado
$OtherEvent			= parametro("callback", $OtherEvent, MQL_RAW);

$control			= parametro("control", "idsocio", MQL_RAW);

$tiny 				= (isset($_GET["tinybox"])) ? true : false;
$BuscarConID		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_BUSQUEDA_IDINT);		//regla de negocio
/*if($OtherEvent != ""){
	$OtherEvent	= ($tiny == true) ? "window.parent.$OtherEvent;" : "opener.$OtherEvent;";
}*/
//si existe la forma y no existe condicionante
if($persona > DEFAULT_SOCIO ){
	$buscarPor	= "s";
}
$buscarPor	= ( $f != false ) ? "nc" : $buscarPor;
$persona	= ($buscarPor == "nc" AND $cT->cInt($persona) > 0) ? "" : $persona;
/*
 * id) IDInterna
 **/
$arrLst	= array (
		"n"  => 0,
		"nc" => 1,
		"s"  => 2,
		"c"  => 3,
		"r"  => 4,
		"e" => 5,
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
function jsaShowSocios($texto, $tipo_de_busqueda, $todos = false, $idinterno = "", $tipoingreso = 0){
	$strTbls			= "";
	$ByForm				= false;
	$MostrarGars		= true;
	$MostrarPartes		= true;
	$sqlL				= new cSQLListas();
	$xFil				= new cTiposLimpiadores();
	$xUser				= new cSystemUser();
	$WSoc				= "";
	$WPrel				= "";
	$extras				= "";
	$xIc				= new cHImg();
	$xT					= new cTipos();
	$todos				= $xT->cBool($todos);
	$w1					= ($todos == true) ? "" : " (tipoingreso != " . TIPO_INGRESO_SDN ." AND tipoingreso != " . TIPO_INGRESO_PEP ." AND tipoingreso != " . TIPO_INGRESO_USUARIO ." AND tipoingreso != " . FALLBACK_PERSONAS_TIPO_ING ." AND `codigo` != " . DEFAULT_SOCIO . ") AND (`socios_general`.`estatusactual`!=20) ";
	if($tipoingreso > 0){
		$w1				= " (tipoingreso=$tipoingreso) ";
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
	$buscar				= ( ((!isset($texto)) OR (trim($texto) == "") OR ($texto == DEFAULT_SOCIO) OR ($texto == "0")) AND ($idinterno =="") ) ? false : true;
	if ($buscar == false) {
		$sqllike = $sqlL->getListadoDeSocios($w1, "0,50", $extras);
		$table_s = new cTabla($sqllike);
		$table_s->setEventKey("setSocio");
		//$table_s->setRowCSS("codigo", "center");
		$table_s->addEspTool("\$xS=new cHPersona(_REPLACE_ID_);PHP:: \$xS->getNotifNumCreds();");
		if(MODULO_CAPTACION_ACTIVADO == true){
			$table_s->addEspTool("\$xS=new cHPersona(_REPLACE_ID_);PHP:: \$xS->getNotifNumCtas();");
		}
		$table_s->setWithMetaData();
		
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
				$table_s->setEventKey("setSocio");
				$table_s->setRowCSS("credito", "center");
				$table_s->setKeyField("codigo");
				$table_s->setWithMetaData();
				$strTbls .= $table_s->Show("TR.CREDITOS");
			} else {
				if($todos == true){$WSoc = " `socios_general`.`codigo` >0 $WSoc ";}
				$sqllike = $sqlL->getListadoDeSocios($w1 . $WSoc, "0,100", $extras);
				$table_s = new cTabla($sqllike);
				$table_s->setEventKey("setSocio");
				
				
				$table_s->OButton("TR.PANEL", 'jsToPanel(_REPLACE_ID_)', $table_s->ODicIcons()->CONTROL );
				$table_s->OButton("TR.Relacion", "jsGetRelaciones(" . HP_REPLACE_ID . ")", "fa-group");
				//Codigo inicial PHP:: Asignar valor
				$table_s->addEspTool("\$xS=new cHPersona(_REPLACE_ID_);PHP:: \$xS->getNotifNumCreds();");
				if(MODULO_CAPTACION_ACTIVADO == true){
					$table_s->addEspTool("\$xS=new cHPersona(_REPLACE_ID_);PHP:: \$xS->getNotifNumCtas();");
				}
				$table_s->setWithMetaData();
				
				$strTbls .= $table_s->Show();
			}
			//setLog($sqllike);
		}
	return $strTbls;
}
function jsaSetSocioEnSession($socio){	getPersonaEnSession($socio); }


$jxc ->exportFunction('jsaShowSocios', array("idtextobusqueda", "idtipobusqueda","idtodo", "idinterna", "tipodeingreso"), "#divresultado");
$jxc ->exportFunction('jsaGetListadoDeEmpresas', array(""), "#idbusqueda");
$jxc ->exportFunction('jsaGetListadoDeProductos', array(""), "#idbusqueda");
$jxc ->exportFunction('jsaSetSocioEnSession', array("idsocio"));
$jxc ->process();

$xHP->init("initComponents()");


$xFRM		= new cHForm("elform", "frmbuscarsocio.php");
//$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();
$xHSel		= new cHSelect();
$xTxt		= new cHText();
$xTxt2		= new cHText();
$xChk		= new cHCheckBox();
$xChk->setDivClass("");
$xFRM->OButton("TR.Buscar", "jsShowSocios()", $xFRM->ic()->BUSCAR);
$xFRM->addToolbar($xChk->get("TR.Todos", "idtodo"));
$xTxt->addEvent("jsGetPersonasByKey(this)", "onkeyup");$xTxt2->addEvent("jsGetPersonasByKey2(this)", "onkeyup");
$xTxt->setDivClass("");
$xHSel->addOptions(array(
		"n" => "Nombre/Iniciales(Paterno Materno Nombre)",
		"nc"=>$xFRM->getT("TR.Nombre desde un Formulario"),
		"e"=>$xFRM->getT("TR.POR EMPRESA"),
		"idi"=>$xFRM->getT("TR.IDINTERNO"),
		"s"=>$xFRM->getT("TR.CLAVE_DE_PERSONA"),
		"c"=>$xFRM->getT("TR.IDENTIFICACION_POBLACIONAL"),
		"r"=>$xFRM->getT("TR.IDENTIFICACION_FISCAL"),
		"pp"=>$xFRM->getT("TR.POR PRODUCTO CREDITO")
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
$xFRM->endSeccion();

$xFRM->addSeccion("idlistabusqueda", "TR.Resultado");
$xFRM->addHTML("<div id='divresultado'></div>");
$xFRM->endSeccion();

//Tipo de Ingreso falso

$xFRM->OHidden("tipodeingreso", $tipo_de_ingreso);

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script >
var mFecha	= "<?php echo fechasys(); ?>";
var xG		= new Gen();
var xP		= new PersGen();
var idsoc	= "<?php echo $control; ?>";
var next	= "<?php echo $nextstep; ?>";

function jsGetPersonasByKey(msrc){
	var mstr	= new String( msrc.value );
	if (mstr.length >= 4){ jsShowSocios(); }  /*Busqueda por TXT*/
}
function jsGetPersonasByKey2(msrc){
	var mstr	= new String( msrc.value );
	if (mstr.length >= 2) { jsShowSocios();	} /*Busqueda por ID*/
}
function setSocio(id){
	var msrc	= xG.winOrigen();
	var dd		= xG.getMetadata("#tr-socios_general-" + id);
	$("#idsocio").val(id);

	if(next == Configuracion.rutas.credito){
		xG.go({url: "../utils/frmscreditos_.php?next=panel&persona=" + id});
		return false;
	}
	if(next == Configuracion.rutas.panel){
		xG.go({url: "../frmsocios/socios.panel.frm.php?persona=" + id});
		return false;
	}
	
	jsaSetSocioEnSession();
	if(msrc == null){
		
	} else {
	<?php
		if($OtherEvent != ""){
			echo "if(msrc.$OtherEvent != \"undefined\"){ msrc.$OtherEvent(id); }";
		} else {
	?>		
		if(msrc.getElementById(idsoc)){
			oid			= msrc.getElementById(idsoc);
			oid.value	= id;
			oid.focus();
			oid.select();
			session(ID_PERSONA, id);
			
			
			if(msrc.getElementById("nombresocio")){
				var nn = $.trim(dd["nombres"] +  " " + dd["apellido_paterno"] + " " + dd["apellido_materno"]);
				msrc.getElementById("nombresocio").value = nn; 

			} else {
				if(typeof msrc.jsSetNombreSocio != "undefined"){ msrc.jsSetNombreSocio(); }
			}
			xG.close();		
		} else {
			//ir al panel de control
			xP.goToPanel(id, false);
		}
	<?php
		}
	?>
	}
}
function initComponents(){
	//document.getElementById("idtipobusqueda").options[<?php echo $arrLst[$buscarPor]; ?>].selected = true;
	
	jsShowSocios();
	$("#idtextobusqueda").focus();
	$("#idtextobusqueda").select();
}
function jsShowSocios(){ jsaShowSocios(); }
function jsMostrarOpciones() {
	if ($("#idtipobusqueda").val() == "e") {
		jsaGetListadoDeEmpresas();
	} else if ($("#idtipobusqueda").val() == "pp") {
		jsaGetListadoDeProductos();
	} else {
		$("#idbusqueda").html("<label for='idtextobusqueda'>Texto de Busqueda</label><input name='idtextobusqueda' id='idtextobusqueda' type='text' value='<?php echo $persona; ?>' onkeyup='jsGetPersonasByKey(this)' />");
	}
}
function jsGoRecibo(id) {
	//jsGoReciboDeCobranza(socio, credito, parcialidad, oargs)
	var mObj	= processMetaData("#tr-creditos_solicitud-" + id);
	//oargs
	mFecha		= prompt("Fecha", mFecha);
	if (mFecha) {
		jsGoReciboDeCobranza(mObj.codigo, mObj.credito, mObj.periodo, "&fecha=" + mFecha);
	}
	
}
function jsToPanel(idpersona){
	var xP	= new PersGen();
	xP.goToPanel(idpersona);
}
function jsGetRelaciones(idpersona){
	var xG	= new Gen();
	xG.w({ url : "../frmsocios/socios.relaciones.sigma.frm.php?persona=" + idpersona});
}
</script>
</html>

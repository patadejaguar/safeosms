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
$xHP	= new cHPage("TR.Buscar Personas");
$xQL	= new MQL();
$cT		= new cTipos();
$msg	= "";

$i 			= (isset($_GET["i"])) ? $_GET["i"] : false;		//CLAVE
$f 			= (isset($_GET["f"])) ? $_GET["f"] : false;		//form dependiente
$By			= (isset($_GET["o"])) ? $_GET["o"] : "n";		//tipo de busqueda: n = nombre, c = curp, r = rfc, s = socio, nf = Nombre con FORM

$OtherEvent	= parametro("ev", "", MQL_RAW);	//Otro Evento Desatado
$OtherEvent	= parametro("callback", "", MQL_RAW);
$control	= parametro("control", "idsocio", MQL_RAW);

$tiny 		= (isset($_GET["tinybox"])) ? true : false;
/*if($OtherEvent != ""){
	$OtherEvent	= ($tiny == true) ? "window.parent.$OtherEvent;" : "opener.$OtherEvent;";
}*/
//si existe la forma y no existe condicionante
if($cT->cInt($i) > DEFAULT_SOCIO ){ $By	= "s"; }
$By		= ( $f != false ) ? "nc" : $By;
$i		= ($By == "nc" AND $cT->cInt($i) > 0) ? "" : $i;
$arrLst	= array (
		"n"  => 0,
		"nc" => 1,
		"s"  => 2,
		"c"  => 3,
		"r"  => 4,
		"e" => 5 //por empresa
		);
	//ESCRIBA_EL_TEXTO_A_BUSCAR
function fmt_string($string){
	if(strlen(trim($string)) > 4) {
		$string = substr($string, 0, 4);
	} else {
		$string = trim($string);
	}
	return $string;
}
function jsaGetListadoDeEmpresas(){
	$gssql= "SELECT * FROM socios_aeconomica_dependencias";
	$cDE = new cSelect("id-texto_buscado", "id-texto_buscado", $gssql);
	$cDE->addEvent("onchange", "jsShowSocios");
	$cDE->setEsSql();
	$cDE->setOptionSelect(99);
	return $cDE->show();
}
function jsaGetListadoDeProductos(){
	$gssql= "SELECT * FROM creditos_tipoconvenio";
	$cDE = new cSelect("id-texto_buscado", "id-texto_buscado", $gssql);
	$cDE->addEvent("onchange", "jsShowSocios");
	$cDE->setEsSql();
	$cDE->setOptionSelect(99);
	return $cDE->show();
}
function jsShowSocios($texto, $tipo_de_busqueda){
	$strTbls			= "";
	$ByForm				= false;
	$MostrarGars		= true;
	$MostrarPartes		= true;
	$sqlL				= new cSQLListas();
	$WSoc				= " `socios_general`.`codigo` != " . DEFAULT_SOCIO . "";
	$WPrel				= "";
	$xIc				= new cHImg();

	if ( $tipo_de_busqueda == "nc" ){
		$ByForm			= true;
	}
	if ((!isset($texto)) OR (trim($texto) == "") OR ($texto == DEFAULT_SOCIO) OR ($texto == "0") ) {
		$sqllike = $sqlL->getListadoDeSocios(" tipoingreso != " . TIPO_INGRESO_SDN);
		$table_s = new cTabla($sqllike);
		$table_s->setEventKey("setSocio");
		$table_s->setRowCSS("codigo", "center");
		$table_s->addEspTool("\$xS=new cSocio(_REPLACE_ID_,true);\$D=\$xS->getTotalColocacionActual();PHP::(\$D[SYS_NUMERO]>0) ? \"<div class='noticon'><i class='fa fa-credit-card fa-lg'></i><span class='noticount'>\" . \$D[SYS_NUMERO] . \"</span></div>\":\"\";");
		$strTbls .= $table_s->Show("TR.ULTIMOS REGISTROS");
	} else {
		$texto 		= trim($texto);
		$texto		= strtoupper($texto);
		$completo	= explode(" ", $texto);

		$str		= fmt_string($completo[0]);

		$marked		= false;
		switch ( $tipo_de_busqueda ){
			//CURP
			case "c":
				$str		= trim( substr( $completo[0], 0, 7) );
				$WSoc		= " (curp LIKE '$str%') ";
			break;
		//NUMERO DE SOCIO
			case "s":
				$str		= trim( substr( $completo[0], 0, 6) );
				$WSoc		= " (codigo LIKE '$str%') ";
			break;
		//NUMERO DEempresa
			case "e":
				$WSoc		= " (dependencia = $texto) ";
			break;
			//DEAFULT:
			default:
				//balam gon lui
			if ( isset($completo[1]) ){
				$str		= fmt_string($completo[1]);
				$WSoc		.= ($str != "*") ? " AND (apellidomaterno LIKE '$str%') " : "";
			}
			if ( isset($completo[2]) ){
				$str		= fmt_string($completo[2]);
				$WSoc		.= ($str != "*") ? " AND (nombrecompleto LIKE '$str%') " : "";
			}
			if(isset($completo[0]) ){
				$str		= fmt_string($completo[0]);
				$WSoc		.= " AND ((apellidopaterno LIKE '$str%') OR (nombrecompleto LIKE '%$str%') )";
			}			
			break;
		}
			if($tipo_de_busqueda == "pp"){
				$sqllike	= "SELECT
					`creditos_solicitud`.`numero_solicitud`        AS `credito`,
					`socios`.`codigo`,
					`socios`.`nombre`,
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
				$table_s->setEventKey("jsGoRecibo");
				$table_s->setRowCSS("credito", "center");
				$table_s->setKeyField("credito");
				$table_s->setWithMetaData();
				$strTbls .= $table_s->Show("TR.CREDITOS");
			} else {
				$sqllike = $sqlL->getListadoDeSocios($WSoc . " AND tipoingreso != " . TIPO_INGRESO_SDN);
				$table_s = new cTabla($sqllike);
				$table_s->setEventKey("setSocio");
				$table_s->setRowCSS("codigo", "center");
				$table_s->addEspTool( $xIc->get24("settings.png", "onclick='jsToPanel(_REPLACE_ID_)'") );
				//$table_s->addSubQuery("SELECT CONCAT(`creditos_solicitud`.`numero_solicitud`, '|', `creditos_solicitud`.`numero_pagos`, '|',`creditos_solicitud`.`periocidad_de_pago`,'|',`creditos_solicitud`.`saldo_actual` ) FROM creditos_solicitud WHERE numero_socio = _REPLACE_ID_", 1);
				$table_s->addEspTool("\$xS=new cSocio(_REPLACE_ID_,true);\$D=\$xS->getTotalColocacionActual();PHP::(\$D[SYS_NUMERO]>0) ? \"<div class='noticon'><i class='fa fa-credit-card fa-lg'></i><span class='noticount'>\" . \$D[SYS_NUMERO] . \"</span></div>\":\"\";");
				$strTbls .= $table_s->Show("TR.PERSONAS QUE COINCIDEN CON LA BUSQUEDA");
			}
		}
	return $strTbls;
}
function jsaSetSocioEnSession($socio){	getPersonaEnSession($socio); }

$jxc = new TinyAjax();
$jxc ->exportFunction('jsShowSocios', array("id-texto_buscado", "id-TipoDeBusqueda"), "#dResults");
$jxc ->exportFunction('jsaGetListadoDeEmpresas', array(""), "#valorBusqueda");
$jxc ->exportFunction('jsaGetListadoDeProductos', array(""), "#valorBusqueda");
$jxc ->exportFunction('jsaSetSocioEnSession', array("idsocio"));
$jxc ->process();

$xHP->init("initComponents()");


$xFRM		= new cHForm("elform", "frmbuscarsocio.php");


//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();

//$xFRM->addSubmit();


//echo $xFRM->get();
?>
<!-- FORMULARIO BASICO -->
<fieldset>
	<legend>Buscar una Persona</legend>

<form name='elform' action='frmbuscarsocio.php' method='post'>
	<table>
		<tr>
			<th>Buscar Por:</th>
			<th><select name='TipoDeBusqueda' id='id-TipoDeBusqueda' onchange='jsMostrarOpciones()'>
				<option value="n">Nombre/Iniciales(Paterno Materno Nombre)</option>
				<option value="nc">Nombre desde un Formulario</option>
				<option value="s">Clave de Persona</option>
				<option value="c">CURP</option>
				<option value="r">RFC</option>
				<option value="e">POR EMPRESA</option>
				<option value="pp">POR PRODUCTO</option>
			</select></th>
			<td id="valorBusqueda"><input name='texto_buscado' id='id-texto_buscado' type='text'
				   value='<?php echo $i; ?>' size="50" maxlength="100"
				   onchange='jsShowSocios()' onkeyup='jsGetPersonasByKey(this)' /></td>
		</tr>
		<tr>
			<td />
			<td />
			<th>
				<a class='button' name='btsend' onClick='jsShowSocios();'>BUSCAR</a>
			</th>
		</tr>
	</table>
	<?php

	//$xLo		= new cLocal();
	//$arrBusq	= array("AP" => "balam",  "N" => "luis");
	//$model		= array("AP" => "ApellidoPaterno", "AM" => "ApellidoMaterno", "N" => "SuNombre");
	//var_dump($xLo->getListadoDePersonasBuscadas($arrBusq, $model));
	?>
<input type="hidden" id="idsocio">
</form>
	<div id="dResults"></div>
</fieldset>
</body>
<?php $jxc ->drawJavaScript(false, true); ?>
<script >
var mFecha			= "<?php echo fechasys(); ?>";
var xG				= new Gen();
var idsoc			= "<?php echo $control; ?>";
function jsGetPersonasByKey(msrc){
	var mstr	= new String( msrc.value );
	if (mstr.length > 4) {
		jsShowSocios();
	}
}
function setSocio(id){
	var msrc	= null;	
	$("#idsocio").val(id);
	jsaSetSocioEnSession();
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
	if(msrc == null){} else {
	<?php
	if($OtherEvent != ""){
		echo "if(msrc.$OtherEvent != \"undefined\"){ msrc.$OtherEvent(id); }";
	} else {
	?>		
		if(msrc.getElementById(idsoc)){
			oid			=  msrc.getElementById(idsoc);
			oid.value	= id;
			oid.focus();
			oid.select();
			if(typeof msrc.jsSetNombreSocio != "undefined"){ msrc.jsSetNombreSocio(); }
			xG.close();		
		}
	<?php
	}

		/*if($tiny == true) {
			echo "
			window.parent.document.$f.idsocio.value 	= id;
			window.parent.document.$f.idsocio.focus();
			window.parent.document.$f.idsocio.select();
			if(typeof window.parent.jsSetNombreSocio != \"undefined\"){
				window.parent.jsSetNombreSocio();
			}
			$OtherEvent
			window.parent.TINY.box.hide();
			";
		} else if( $f !== false ){
			echo "
			opener.document.$f.idsocio.value 	= id;
			opener.document.$f.idsocio.focus();
			opener.document.$f.idsocio.select();
			if(typeof opener.jsSetNombreSocio != \"undefined\"){
				opener.jsSetNombreSocio();
			}
			$OtherEvent
			window.close();
			";
		}*/
	?>
	}
}
function initComponents(){
	document.getElementById("id-TipoDeBusqueda").options[<?php echo $arrLst[$By]; ?>].selected = true;
	resizeMainWindow();
	jsShowSocios();
}
function resizeMainWindow(){
	var mWidth	= 800;
	var mHeight	= 800;
	window.resizeTo(mWidth, mHeight);
}
function jsMostrarOpciones() {
	if ($("#id-TipoDeBusqueda").val() == "e") {
		jsaGetListadoDeEmpresas();
	} else if ($("#id-TipoDeBusqueda").val() == "pp") {
		jsaGetListadoDeProductos();
	} else {
		$("#valorBusqueda").html("<input name='texto_buscado' id='id-texto_buscado' type='text' value='<?php echo $i; ?>' size='50' maxlength='100' onchange='jsShowSocios()' />");
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
</script>
</html>

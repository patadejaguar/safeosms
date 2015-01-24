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
$xHP				= new cHPage("TR.Catalogo Contable");
$jxc 				= new TinyAjax();
$oficial 			= elusuario($iduser);
$control			= parametro("c", "false");
$oLeft 				= parametro("l", 300, MQL_INT);
$oTop				= parametro("t", 300, MQL_INT);

function jsaShowCatalogo($codigo){
	//$idcta = substr($idcta, 1, -1);
	$sql = "SELECT numero, nombre FROM contable_catalogo WHERE numero LIKE '" . $codigo . "%' AND afectable=1  ORDER BY numero LIMIT 0,20";
	$rs = mysql_query($sql, cnnGeneral());
	$tds = "";
	$i = 1;
	while ($row = mysql_fetch_array($rs)) {
		$ctaformateada  = $row["numero"];
		$nombrecuenta = htmlentities($row["nombre"]);
		if($i ==2){
			$i = 1;
		} else {
			$i++;
		}

		$tds .= " \n
		<option value=\"$row[0]\" >$ctaformateada - $nombrecuenta</option>";
		
	}
	
	return "<select name=\"underCuenta\" id=\"idUnderCuenta\" size=\"10\"
			onclick=\"setCuenta(this.value);\"
			onblur=\"setCuenta(this.value);\" >
				$tds 
			</select>";
}
$jxc ->exportFunction('jsaShowCatalogo', array('idcodigo'), "#thisIsCatalog");	
$jxc ->process();

$xHP->init("initComponents()");
?>
<form name="frmListCatalog" method="post" action="">
<fieldset>
	<legend>Buscar en el Catalogo</legend>
	<table>
		<tbody>
		<tr>
			<td>Codigo de Cuenta</td>
			<td><input type='text' name='codigo' value='<?php echo CUENTA_DE_CUADRE; ?>' id="idcodigo" onkeyup="setActionPerKey(event)"  /></td>
		</tr>
		<tr>
			<th colspan="2" id="thisIsCatalog">
				
			</th>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
</body>
<?php $jxc ->drawJavaScript(false, true); ?>
<script  >
var mCtrl 		= "<?php echo $control ?>";
var mLeft 		= "<?php echo $oLeft ?>";
var mTop		= "<?php echo $oTop ?>";
var onSearch	= false;
function searchCatalog(){
	jsaShowCatalogo();
	document.getElementById("thisIsCatalog").innerHTML = "buscando ...";
	
}
function initComponents(){
	//obtiene el Val 
	if(mCtrl){
		document.getElementById("idcodigo").value = opener.document.getElementById(mCtrl).value;
	}	
		document.getElementById("idcodigo").focus();
		document.getElementById("idcodigo").select();	
	resizeMainWindow();
	window.moveTo(mLeft, mTop);
}
function resizeMainWindow(){
	var mWidth	= 640;
	var mHeight	= 280;
	window.resizeTo(mWidth, mHeight);
}
function setCuenta(sValue){
		document.getElementById("idcodigo").value = sValue;
		setTimeout("goToCode()", 300);
}
function setActionPerKey(evt){
	evt 		= (evt) ? evt : ((event) ? event : null);
	
	var mChar	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var mVal	= evt.target.value;

	switch (mChar){
		case 13:		//Entrar
			window.close();
			onSearch = false;
			if(mCtrl){
				opener.document.getElementById(mCtrl).value = mVal;
				opener.document.getElementById(mCtrl).focus();
				opener.document.getElementById(mCtrl).select();
			}
		//Seleccionar y Cerrar
			break;
		case 40:		//Key Down
			onSearch = false;
			document.getElementById("idUnderCuenta").focus();
			break;
		case 27:		//Escape
		//salir
			window.close();
			onSearch = false;
			if(mCtrl){
				opener.document.getElementById(mCtrl).focus();
				opener.document.getElementById(mCtrl).select();
			}
			break;
		default:
				searchCatalog();
				onSearch = true;
			break;
	}
}
function goToCode(){
		document.getElementById("idcodigo").focus();
		document.getElementById("idcodigo").select();	
}
</script>
</html>
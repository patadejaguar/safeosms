<?php
//Formulario avanzado de operaciones_recibostipo
	
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

	$o =  trim($_GET['i']);
	 settype($v_3193c, "integer"); 	 // Tipo de idoperaciones_recibostipo
 	 settype($v_97f45, "string"); 	 // Tipo de descripcion_recibostipo 
 	 settype($v_56429, "string"); 	 // Tipo de detalles_del_concepto 
 	 settype($v_a4db7, "double"); 	 // Tipo de subclasificacion 
 	 settype($v_9c6e0, "string"); 	 // Tipo de nombre_sublasificacion 
 	 settype($v_867a4, "integer"); 	 // Tipo de tipo_docto
 	 settype($v_5f5d9, "string"); 	 // Tipo de mostrar_en_corte 
 	 settype($v_5dd1e, "integer"); 	 // Tipo de tipo_poliza_generada
 
	 $v_3193c = trim($_POST['c_3193c']); 	 // Variable de idoperaciones_recibostipo 
 	 $v_97f45 = trim($_POST['c_97f45']); 	 // Variable de descripcion_recibostipo 
 	 $v_56429 = trim($_POST['c_56429']); 	 // Variable de detalles_del_concepto 
 	 $v_a4db7 = trim($_POST['c_a4db7']); 	 // Variable de subclasificacion 
 	 $v_9c6e0 = trim($_POST['c_9c6e0']); 	 // Variable de nombre_sublasificacion 
 	 $v_867a4 = trim($_POST['c_867a4']); 	 // Variable de tipo_docto 
 	 $v_5f5d9 = trim($_POST['c_5f5d9']); 	 // Variable de mostrar_en_corte 
 	 $v_5dd1e = trim($_POST['c_5dd1e']); 	 // Variable de tipo_poliza_generada 
 
	//Tiny Ajax en Accion
	require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
	$jxc = new TinyAjax();
	//Funcion que rellena los datos del Form
	function ff_2651169204622cb80ec4b28ac12285ac($filter, $form) {
	$n_type = gettype($filter);
		if ($n_type == "string") {
				$filter = "'$filter'";
		}
		$sql = "SELECT * FROM operaciones_recibostipo WHERE idoperaciones_recibostipo = $filter LIMIT 0,1";
		$rs = mysql_query($sql, cnnGeneral());
		$nfields = mysql_num_fields($rs)-1;

		$tab = new TinyAjaxBehavior();

		while($rw = mysql_fetch_array($rs)) {
				 	 	 $tab -> add(TabSetValue::getBehavior("i_3193c", $rw["idoperaciones_recibostipo"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_97f45", $rw["descripcion_recibostipo"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_56429", $rw["detalles_del_concepto"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_a4db7", $rw["subclasificacion"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_9c6e0", $rw["nombre_sublasificacion"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_867a4", $rw["tipo_docto"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_5f5d9", $rw["mostrar_en_corte"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_5dd1e", $rw["tipo_poliza_generada"])); 
 
		}
	return $tab -> getString();
	}
	function NextRecord($init, $form){
		//$init = $init + 1;
		$ifin = $init + 1;

		$sql = "SELECT * FROM operaciones_recibostipo LIMIT $init,$ifin";
		$rs = mysql_query($sql, cnnGeneral());
		$nfields = mysql_num_fields($rs)-1;

		$tab = new TinyAjaxBehavior();

		while($rw = mysql_fetch_array($rs)) {
				 	 	 $tab -> add(TabSetValue::getBehavior("i_3193c", $rw["idoperaciones_recibostipo"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_97f45", $rw["descripcion_recibostipo"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_56429", $rw["detalles_del_concepto"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_a4db7", $rw["subclasificacion"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_9c6e0", $rw["nombre_sublasificacion"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_867a4", $rw["tipo_docto"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_5f5d9", $rw["mostrar_en_corte"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_5dd1e", $rw["tipo_poliza_generada"])); 
 
			$tab -> add(TabSetValue::getBehavior("ifproperties", $ifin)); 

		}
	return $tab -> getString();

	}
	function BackRecord($init, $form){
		//$init = $init + 1;
		$ifin = $init - 1;
		if ($ifin>=0) {

			$sql = "SELECT * FROM operaciones_recibostipo LIMIT $ifin,$init";
			$rs = mysql_query($sql, cnnGeneral());
			$nfields = mysql_num_fields($rs)-1;

			$tab = new TinyAjaxBehavior();

			while($rw = mysql_fetch_array($rs)) {
					 	 $tab -> add(TabSetValue::getBehavior("i_3193c", $rw["idoperaciones_recibostipo"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_97f45", $rw["descripcion_recibostipo"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_56429", $rw["detalles_del_concepto"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_a4db7", $rw["subclasificacion"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_9c6e0", $rw["nombre_sublasificacion"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_867a4", $rw["tipo_docto"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_5f5d9", $rw["mostrar_en_corte"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_5dd1e", $rw["tipo_poliza_generada"])); 
 
				$tab -> add(TabSetValue::getBehavior("ifproperties", $ifin)); 

			}
			return $tab -> getString();
		} else {

		}
	}
	//funcion buscar un n Limitado de Registros
	function SearchRecord($filter) {

			$limit_find = 5;
			$n_type = gettype($filter);
			if ($n_type == "string") {
					$filter = "'%$filter%'";
			} else {
				$filter = "'%$filter%'";
			}
			$sql = "SELECT * FROM operaciones_recibostipo WHERE idoperaciones_recibostipo LIKE $filter LIMIT 0,$limit_find";
		$rs = mysql_query($sql, cnnGeneral());
		$tds = "";

		while ($row = mysql_fetch_array($rs)) {
			

			$tds = $tds . "<tr> 

					<td><strong onclick='accion_click(" . $row["idoperaciones_recibostipo"] . "); ff_2651169204622cb80ec4b28ac12285ac(); '>" . $row["idoperaciones_recibostipo"] . "</strong></td>
					<td>$row[1]</td> 

			</tr> 
 ";
		}
		@mysql_free_result($rs);
		return "<div id='i_lst'>
		<table border='1'> 
  $tds  </table>
		</div>
		";

	}
	function ClearSearch($similar) {
		return '';
	}

	$jxc ->exportFunction('ff_2651169204622cb80ec4b28ac12285ac', array('i_3193c', 'frm72ca3ee8'));
	$jxc ->exportFunction('SearchRecord', array('i_3193c'), '#mFind');
	$jxc ->exportFunction('ClearSearch', array('i_3193c'), '#mFind');
	$jxc ->exportFunction('BackRecord', array('ifproperties', 'frm72ca3ee8'));
	$jxc ->exportFunction('NextRecord', array('ifproperties', 'frm72ca3ee8'));
	$jxc ->process();
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>operaciones_recibostipo</title>
</head>
<?php $jxc ->drawJavaScript(false, true); ?>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
<?php
$values = explode("@", "0@@@@@@|1|0|@");
switch ($o) {
	case "o_e0df5f3dfd2650ae5be9993434e2b2c0":						//Insert
	//SQL INSERT
	$sql_insert = "INSERT INTO operaciones_recibostipo( idoperaciones_recibostipo, descripcion_recibostipo, detalles_del_concepto, subclasificacion, nombre_sublasificacion, tipo_docto, mostrar_en_corte, tipo_poliza_generada) VALUES( $v_3193c, '$v_97f45', '$v_56429', $v_a4db7, '$v_9c6e0', $v_867a4, '$v_5f5d9', $v_5dd1e)";
	my_query($sql_insert);
	echo "<html>
				<body onLoad='javascript:history.back();'>
				</body>
		</html>";


		break;
	case "o_3ac340832f29c11538fbe2d6f75e8bcc":						//update
	$sql_update = "UPDATE operaciones_recibostipo SET  idoperaciones_recibostipo=$v_3193c, descripcion_recibostipo='$v_97f45', detalles_del_concepto='$v_56429', subclasificacion=$v_a4db7, nombre_sublasificacion='$v_9c6e0', tipo_docto=$v_867a4, mostrar_en_corte='$v_5f5d9', tipo_poliza_generada=$v_5dd1e WHERE idoperaciones_recibostipo=$v_3193c ";
	my_query($sql_update);
	echo "<html>
				<body onLoad='javascript:history.back();'>
				</body>
		</html>";


		break;

	case "o_099af53f601532dbd31e0ea99ffdeb64":						//Delete
		$n_type = gettype($v_3193c);
		if ($n_type == "string") {
				$v_3193c = "'$v_3193c'";
		}
	$sql_delete = "DELETE FROM operaciones_recibostipo WHERE idoperaciones_recibostipo=$v_3193c ";
	my_query($sql_delete);
	echo "<html>
				<body onLoad='javascript:history.back();'>
				</body>
		</html>";

		break;

	default:
		?>


		<?php

		break;
}
?>
		<form name="frm72ca3ee8" action="f_72ca3ee8.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0" method="POST">
		
	<fieldset>
	<legend>[ Operaciones Recibostipo ]</legend>
	<table   >
		 	 <tr> 
 	 	 	 <td>Idoperaciones Recibostipo</td> 
 	 	 	 <td><input type="text" name="c_3193c" value="<?php echo $values[0] ; ?>" id="i_3193c" onkeypress='setCharAction(event);'  size="3" maxlength="3"  /><img src="../images/common/execute.png" onClick="mostraritem();" id="execmenugif" /><div id="mFind"></div></td> 
	 	 	 <td id="lbls">Descripcion Recibostipo</td> 
 	 	 	 <td id="txts"><input type="text" name="c_97f45" value="<?php echo $values[1] ; ?>" id="i_97f45" onkeypress='setCharAction(event);'  size="43" maxlength="43"  /></td> 
 	 	 </tr> 
	 	 <tr> 
 	 	 	 <td>Detalles Del Concepto</td> 
 	 	 	 <td><input type="text" name="c_56429" value="<?php echo $values[2] ; ?>" id="i_56429" onkeypress='setCharAction(event);'  size="98" maxlength="98"  /></td> 
	 	 	 <td id="lbls">subclasificacion</td> 
 	 	 	 <td id="txts"><input type="text" name="c_a4db7" value="<?php echo $values[3] ; ?>" id="i_a4db7" onkeypress='setCharAction(event);'  /></td> 
 	 	 </tr> 
	 	 <tr> 
 	 	 	 <td>Nombre Sublasificacion</td> 
 	 	 	 <td><input type="text" name="c_9c6e0" value="<?php echo $values[4] ; ?>" id="i_9c6e0" onkeypress='setCharAction(event);'  size="48" maxlength="48"  /></td> 
	 	 	 <td id="lbls">Tipo De Documento</td> 
 	 	 	 <td id="txts"><input type="text" name="c_867a4" value="<?php echo $values[5] ; ?>" id="i_867a4" onkeypress='setCharAction(event);'  size="3" maxlength="3"  /></td> 
 	 	 </tr> 
	 	 <tr> 
 	 	 	 <td>Mostrar En Corte</td> 
 	 	 	 <td><select name="c_5f5d9" id="i_5f5d9"> 
 <option value="1 ">Si</option> 
 <option value="0 ">No</option> 
  
 </select> 
 </td> 
	 	 	 <td id="lbls">Tipo Poliza Generada</td> 
 	 	 	 <td id="txts"><?php ctrl_select("SELECT  * FROM contable_polizasdiarios", " name='c_5dd1e' id='i_5dd1e' ",  "", "yes"); ?> </td> 
 	 	 </tr> 

	</table>
	</fieldset>
		<input type="hidden" id="ifproperties" value="0" />

		<div id="menuh">
			   <table border="3"   >
				  <tr> 
 <td onClick="SearchRecord(); ocultaritem();"><img src="../images/common/find.gif" width="16" height="16" />&nbsp;Buscar Registro</td> 
 </tr>
				  <tr> 
 <td onClick="ff_2651169204622cb80ec4b28ac12285ac(); ocultaritem();"><img src="../images/common/search.png" width="16" height="16" />&nbsp;Obtener Registro</td> 
 </tr>
				  <tr> 
 <td onClick="cmd(2); "><img src="../images/common/edit.png"  width="16" height="16" />&nbsp;Actualizar Registro</td> 
 </tr>
				  <tr> 
 <td onClick="cmd(1); "><img src="../images/common/save.gif" width="16" height="16" />&nbsp;Agregar Registro</td> 
 </tr>
				  <tr> 
 <td onClick="cmd(3); "><img src="../images/common/trash.png"  width="16" height="16" />&nbsp;Eliminar Registro</td> 
 </tr>
			   </table></div>
		<fieldset>
		<legend>[ Operaciones ]</legend>
				<!-- <input type="button" name="nuevo_registro" onclick="cmd(5);" value="NUEVO REGISTRO"/> -->
				<input type="button" name="guardar_registro" onclick="cmd(1);" value="AGREGAR REGISTRO"/>
				<input type="button" name="actualizar_registro" onclick="cmd(2);" value="ACTUALIZAR REGISTRO"/>
				<input type="button" name="eliminar_registro" onclick="cmd(3);" value="ELIMINAR REGISTRO"/>
		</fieldset>
		<div id="avisos"></div>
		</form>
<hr />
</body>
<script  >
var myfrm = document.frm72ca3ee8;
var onEdit = false;
function cmd(is) {
	switch(is) {
		case 1:		//Guardar Registro
		ocultaritem();
			document.frm72ca3ee8.action = "f_72ca3ee8.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0";
			document.frm72ca3ee8.submit();
		break;
		case 2:		//Actualizar Registro
		ocultaritem();
			document.frm72ca3ee8.action = "f_72ca3ee8.php?i=o_3ac340832f29c11538fbe2d6f75e8bcc";
			document.frm72ca3ee8.submit();
		break;
		case 3:		//Eliminar Registro
		ocultaritem();
			document.frm72ca3ee8.action = "f_72ca3ee8.php?i=o_099af53f601532dbd31e0ea99ffdeb64";
			document.frm72ca3ee8.submit();
		break;
		case 5:		//Limpiar Form
		ocultaritem();
			document.frm72ca3ee8.reset();
		break;
	}

}
function mostraritem(item){
	if(!item) {
		var item = "menuh";
	}
	var cmdBtn = document.getElementById("execmenugif");
	var dMnuFind = document.getElementById(item);

	var iPar = cmdBtn.offsetParent;
	var ePar = iPar.offsetParent.offsetTop;

	//posicionar el Menu
	oTop = parseInt(ePar) + 20;
	oLeft = parseInt(cmdBtn.offsetParent.offsetLeft) + 40;
	dMnuFind.style.top = oTop + "px";
	dMnuFind.style.left = oLeft + "px";

	document.getElementById(item).style.visibility = "visible";
	setTimeout("ocultaritem('" + item + "')", 3000);
}
function ocultaritem(item) {
	if(!item) {
		var item = "menuh";
	}
	if(item!="menuh") {
		document.getElementById(item).innerHTML = "";
	}
	document.getElementById(item).style.visibility = "hidden";
}
function the_action(mKye) {
	document.getElementById("i_3193c").value = mKye;
	ClearSearch();
}
function accion_click(NaNKey){
    //ejecutar una accion al hacer click
    document.getElementById('i_3193c').value = NaNKey;
	ocultaritem("i_lst");
}
function setCharAction(evt){
    evt=(evt) ? evt:event;
    var charCode = (evt.charCode) ? evt.charCode :
        ((evt.which) ? evt.which : evt.keyCode);
    if (charCode == 33) { //Page  Up
		BackRecord();
    } else if (charCode == 34) { //Page  Down
    	NextRecord();
    }
}
</script>
</html>
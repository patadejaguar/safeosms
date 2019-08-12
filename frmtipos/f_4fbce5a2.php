<?php
//Formulario avanzado de general_utilerias
	
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
		 settype($v_d213f, "integer"); 	 // Tipo de idgeneral_utilerias
 	 settype($v_50b4f, "string"); 	 // Tipo de nombre_utilerias 
 	 settype($v_3276e, "string"); 	 // Tipo de descripcion_utileria 
 	 settype($v_b41f7, "string"); 	 // Tipo de describe_param_1 
 	 settype($v_a0319, "string"); 	 // Tipo de describe_param_2 
 	 settype($v_2571b, "string"); 	 // Tipo de describe_param_3 
 	 settype($v_e33ef, "string"); 	 // Tipo de describe_init 
 	 settype($v_1b079, "string"); 	 // Tipo de describe_end 
 
		 $v_d213f = trim($_POST['c_d213f']); 	 // Variable de idgeneral_utilerias 
 	 $v_50b4f = trim($_POST['c_50b4f']); 	 // Variable de nombre_utilerias 
 	 $v_3276e = trim($_POST['c_3276e']); 	 // Variable de descripcion_utileria 
 	 $v_b41f7 = trim($_POST['c_b41f7']); 	 // Variable de describe_param_1 
 	 $v_a0319 = trim($_POST['c_a0319']); 	 // Variable de describe_param_2 
 	 $v_2571b = trim($_POST['c_2571b']); 	 // Variable de describe_param_3 
 	 $v_e33ef = trim($_POST['c_e33ef']); 	 // Variable de describe_init 
 	 $v_1b079 = trim($_POST['c_1b079']); 	 // Variable de describe_end 
 
	//Tiny Ajax en Accion
	require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
	$jxc = new TinyAjax();
	//Funcion que rellena los datos del Form
	function ff_2651169204622cb80ec4b28ac12285ac($filter, $form) {
	$n_type = gettype($filter);
		if ($n_type == "string") {
				$filter = "'$filter'";
		}
		$sql = "SELECT * FROM general_utilerias WHERE idgeneral_utilerias = $filter LIMIT 0,1";
		$rs = mysql_query($sql, cnnGeneral());
		$nfields = mysql_num_fields($rs)-1;

		$tab = new TinyAjaxBehavior();

		while($rw = mysql_fetch_array($rs)) {
				 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_d213f", $rw["idgeneral_utilerias"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_50b4f", $rw["nombre_utilerias"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_3276e", $rw["descripcion_utileria"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_b41f7", $rw["describe_param_1"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_a0319", $rw["describe_param_2"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_2571b", $rw["describe_param_3"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_e33ef", $rw["describe_init"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_1b079", $rw["describe_end"])); 
 
		}
	return $tab -> getString();
	}
	function NextRecord($init, $form){
		//$init = $init + 1;
		$ifin = $init + 1;

		$sql = "SELECT * FROM general_utilerias LIMIT $init,$ifin";
		$rs = mysql_query($sql, cnnGeneral());
		$nfields = mysql_num_fields($rs)-1;

		$tab = new TinyAjaxBehavior();

		while($rw = mysql_fetch_array($rs)) {
				 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_d213f", $rw["idgeneral_utilerias"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_50b4f", $rw["nombre_utilerias"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_3276e", $rw["descripcion_utileria"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_b41f7", $rw["describe_param_1"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_a0319", $rw["describe_param_2"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_2571b", $rw["describe_param_3"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_e33ef", $rw["describe_init"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_1b079", $rw["describe_end"])); 
 
			$tab -> add(TabSetValue::getBehavior("ifproperties", $ifin)); 

		}
	return $tab -> getString();

	}
	function BackRecord($init, $form){
		//$init = $init + 1;
		$ifin = $init - 1;
		if ($ifin>=0) {

			$sql = "SELECT * FROM general_utilerias LIMIT $ifin,$init";
			$rs = mysql_query($sql, cnnGeneral());
			$nfields = mysql_num_fields($rs)-1;

			$tab = new TinyAjaxBehavior();

			while($rw = mysql_fetch_array($rs)) {
					 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_d213f", $rw["idgeneral_utilerias"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_50b4f", $rw["nombre_utilerias"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_3276e", $rw["descripcion_utileria"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_b41f7", $rw["describe_param_1"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_a0319", $rw["describe_param_2"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_2571b", $rw["describe_param_3"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_e33ef", $rw["describe_init"])); 
 	 	 	 	 	 	 $tab -> add(TabSetValue::getBehavior("i_1b079", $rw["describe_end"])); 
 
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
			$sql = "SELECT * FROM general_utilerias WHERE idgeneral_utilerias LIKE $filter LIMIT 0,$limit_find";
		$rs = mysql_query($sql, cnnGeneral());
		$tds = "";

		while ($row = mysql_fetch_array($rs)) {
			

			$tds = $tds . "<tr> 

					<td><strong onclick='accion_click(" . $row["idgeneral_utilerias"] . "); ff_2651169204622cb80ec4b28ac12285ac(); '>" . $row["idgeneral_utilerias"] . "</strong></td>
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

	$jxc ->exportFunction('ff_2651169204622cb80ec4b28ac12285ac', array('i_d213f', 'frm4fbce5a2'));
	$jxc ->exportFunction('SearchRecord', array('i_d213f'), '#mFind');
	$jxc ->exportFunction('ClearSearch', array('i_d213f'), '#mFind');
	$jxc ->exportFunction('BackRecord', array('ifproperties', 'frm4fbce5a2'));
	$jxc ->exportFunction('NextRecord', array('ifproperties', 'frm4fbce5a2'));
	$jxc ->process();
	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>general_utilerias</title>
</head>
<?php $jxc ->drawJavaScript(false, true); ?>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
<?php
$values = explode("@", "0@@@@@@@");
switch ($o) {
	case "o_e0df5f3dfd2650ae5be9993434e2b2c0":						//Insert
	//SQL INSERT
	$sql_insert = "INSERT INTO general_utilerias( idgeneral_utilerias, nombre_utilerias, descripcion_utileria, describe_param_1, describe_param_2, describe_param_3, describe_init, describe_end) VALUES( $v_d213f, '$v_50b4f', '$v_3276e', '$v_b41f7', '$v_a0319', '$v_2571b', '$v_e33ef', '$v_1b079')";
	my_query($sql_insert);
	echo "<html>
				<body onLoad='javascript:history.back();'>
				</body>
		</html>";


		break;
	case "o_3ac340832f29c11538fbe2d6f75e8bcc":						//update
	$sql_update = "UPDATE general_utilerias SET  idgeneral_utilerias=$v_d213f, nombre_utilerias='$v_50b4f', descripcion_utileria='$v_3276e', describe_param_1='$v_b41f7', describe_param_2='$v_a0319', describe_param_3='$v_2571b', describe_init='$v_e33ef', describe_end='$v_1b079' WHERE idgeneral_utilerias=$v_d213f ";
	my_query($sql_update);
	echo "<html>
				<body onLoad='javascript:history.back();'>
				</body>
		</html>";


		break;

	case "o_099af53f601532dbd31e0ea99ffdeb64":						//Delete
		$n_type = gettype($v_d213f);
		if ($n_type == "string") {
				$v_d213f = "'$v_d213f'";
		}
	$sql_delete = "DELETE FROM general_utilerias WHERE idgeneral_utilerias=$v_d213f ";
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
		<form name="frm4fbce5a2" action="f_4fbce5a2.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0" method="POST">
		
	<fieldset>
	<legend>[ General Utilerias ]</legend>
	<table   >
		 	 <tr> 
 	 	 	 <td>Idgeneral Utilerias</td> 
 	 	 	 <td ><input type="text" name="c_d213f" value="<?php echo $values[0] ; ?>" id="i_d213f"  onkeypress='setCharAction(event);'   size="8" maxlength="8"  /><img src="../images/common/execute.png" onClick="mostraritem();" id="execmenugif" /><div id="mFind"></div></td> 
 	 	 <tr> 
 	 	 	 <td>Nombre Utilerias</td> 
 	 	 	 <td  colspan="3" ><textarea name='c_50b4f' id='i_50b4f' cols="50" rows="2" ><?php echo $values[1] ; ?></textarea></td> 
 </tr>	 	 <tr> 
 	 	 	 <td>Descripcion Utileria</td> 
 	 	 	 <td  colspan="3" ><textarea name='c_3276e' id='i_3276e' cols="50" rows="2" ><?php echo $values[2] ; ?></textarea></td> 
 </tr>	 	  
 	 	 	 <td>Descripcion Del Parametro 1</td> 
 	 	 	 <td ><input type="text" name="c_b41f7" value="<?php echo $values[3] ; ?>" id="i_b41f7"   size="43" maxlength="43"  /></td> 
 </tr>	 	 <tr> 
 	 	 	 <td>Descripcion Del Parametro 2</td> 
 	 	 	 <td ><input type="text" name="c_a0319" value="<?php echo $values[4] ; ?>" id="i_a0319"   size="43" maxlength="43"  /></td> 
 	 	  
 	 	 	 <td>Descripcion Del Parametro 3</td> 
 	 	 	 <td ><input type="text" name="c_2571b" value="<?php echo $values[5] ; ?>" id="i_2571b"   size="43" maxlength="43"  /></td> 
 </tr>	 	 <tr> 
 	 	 	 <td>Descripcion Del Inicio</td> 
 	 	 	 <td ><input type="text" name="c_e33ef" value="<?php echo $values[6] ; ?>" id="i_e33ef"   size="43" maxlength="43"  /></td> 
 	 	  
 	 	 	 <td>Descripcion Del Final</td> 
 	 	 	 <td ><input type="text" name="c_1b079" value="<?php echo $values[7] ; ?>" id="i_1b079"   size="43" maxlength="43"  /></td> 
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
var myfrm = document.frm4fbce5a2;
var onEdit = false;
function cmd(is) {
	switch(is) {
		case 1:		//Guardar Registro
		ocultaritem();
			document.frm4fbce5a2.action = "f_4fbce5a2.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0";
			document.frm4fbce5a2.submit();
		break;
		case 2:		//Actualizar Registro
		ocultaritem();
			document.frm4fbce5a2.action = "f_4fbce5a2.php?i=o_3ac340832f29c11538fbe2d6f75e8bcc";
			document.frm4fbce5a2.submit();
		break;
		case 3:		//Eliminar Registro
		ocultaritem();
			document.frm4fbce5a2.action = "f_4fbce5a2.php?i=o_099af53f601532dbd31e0ea99ffdeb64";
			document.frm4fbce5a2.submit();
		break;
		case 5:		//Limpiar Form
		ocultaritem();
			document.frm4fbce5a2.reset();
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
	document.getElementById("i_d213f").value = mKye;
	ClearSearch();
}
function accion_click(NaNKey){
    //ejecutar una accion al hacer click
    document.getElementById('i_d213f').value = NaNKey;
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
<?php
/**
*  @see        Formulario avanzado de general_formulas
*  @since    2007-11-13 15:35
*  @author    PHP Form Wizard V 0.75 - Balam Gonzalez Luis (2007)
**/

    
  
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
    $defaultAction        = "o_e0df5f3dfd2650ae5be9993434e2b2c0";
    $retrieveKey            = $_GET["x"];  
       settype($v_b62df, "string");      // Tipo de aplicado_a 
      settype($v_23088, "string");      // Tipo de code_type 
      settype($v_0f743, "string");      // Tipo de description_short 
      settype($v_41f88, "string"); // Tipo de estructura_de_la_formula 
 
       $v_b62df = trim($_POST['c_b62df']);      // Variable de aplicado_a 
      $v_23088 = trim($_POST['c_23088']);      // Variable de code_type 
      $v_0f743 = trim($_POST['c_0f743']);      // Variable de description_short 
      $v_41f88 = trim($_POST['c_41f88']);      // Variable de estructura_de_la_formula 
 
  //Tiny Ajax en Accion
  require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
  $jxc = new TinyAjax();
  //Funcion que rellena los datos del Form
  function jsGetRegistro($filter, $form) {
  $n_type = gettype($filter);
    if ($n_type == "string") {
        $filter = "'$filter'";
    }
    $sql = "SELECT * FROM general_formulas WHERE aplicado_a = $filter LIMIT 0,1";
    $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
    $nfields = mysql_num_fields($rs)-1;

    $tab = new TinyAjaxBehavior();

    while($rw = mysql_fetch_array($rs)) {
                                    $tab -> add(TabSetValue::getBehavior("i_b62df", $rw["aplicado_a"])); 
                               $tab -> add(TabSetValue::getBehavior("i_23088", $rw["code_type"])); 
                               $tab -> add(TabSetValue::getBehavior("i_0f743", $rw["description_short"])); 
                               $tab -> add(TabSetValue::getBehavior("i_41f88", $rw["estructura_de_la_formula"])); 
 
    }
  return $tab -> getString();
  }
  function NextRecord($init, $form){
    //$init = $init + 1;
    $ifin = $init + 1;

    $sql = "SELECT * FROM general_formulas LIMIT $init,$ifin";
    $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
    $nfields = mysql_num_fields($rs)-1;

    $tab = new TinyAjaxBehavior();

    while($rw = mysql_fetch_array($rs)) {
                                    $tab -> add(TabSetValue::getBehavior("i_b62df", $rw["aplicado_a"])); 
                               $tab -> add(TabSetValue::getBehavior("i_23088", $rw["code_type"])); 
                               $tab -> add(TabSetValue::getBehavior("i_0f743", $rw["description_short"])); 
                               $tab -> add(TabSetValue::getBehavior("i_41f88", $rw["estructura_de_la_formula"])); 
 
      $tab -> add(TabSetValue::getBehavior("ifproperties", $ifin)); 

    }
  return $tab -> getString();

  }
  function BackRecord($init, $form){
    //$init = $init + 1;
    $ifin = $init - 1;
    if ($ifin>=0) {

      $sql = "SELECT * FROM general_formulas LIMIT $ifin,$init";
      $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
      $nfields = mysql_num_fields($rs)-1;

      $tab = new TinyAjaxBehavior();

      while($rw = mysql_fetch_array($rs)) {
                                      $tab -> add(TabSetValue::getBehavior("i_b62df", $rw["aplicado_a"])); 
                               $tab -> add(TabSetValue::getBehavior("i_23088", $rw["code_type"])); 
                               $tab -> add(TabSetValue::getBehavior("i_0f743", $rw["description_short"])); 
                               $tab -> add(TabSetValue::getBehavior("i_41f88", $rw["estructura_de_la_formula"])); 
 
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
      $sql = "SELECT * FROM general_formulas WHERE aplicado_a LIKE $filter LIMIT 0,$limit_find";
    $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
    $tds = "";

    while ($row = mysql_fetch_array($rs)) {
     

      $tds = $tds . "<tr> 

          <th onclick='cmdClick(" . $row["aplicado_a"] . "); jsGetRegistro(); '>" . $row["aplicado_a"] . "</th>
          <td>$row[1]</td> 

      </tr> 
 ";
    }
    @mysql_free_result($rs);
    return "<div id='i_lst'>
    <table border='1'> 
  $tds 
 </table>
    </div>
    ";

  }
  function ClearSearch($similar) {
    return '';
  }

  $jxc ->exportFunction('jsGetRegistro', array('i_b62df', 'frm1879d025'));
  $jxc ->exportFunction('SearchRecord', array('i_b62df'), '#mFind');
  $jxc ->exportFunction('ClearSearch', array('i_b62df'), '#mFind');
  $jxc ->exportFunction('BackRecord', array('ifproperties', 'frm1879d025'));
  $jxc ->exportFunction('NextRecord', array('ifproperties', 'frm1879d025'));
  $jxc ->process();

    if(isset($retrieveKey)){
        $sql_get_rec = "SELECT  aplicado_a, code_type, description_short, estructura_de_la_formula
                        FROM general_formulas WHERE aplicado_a=$retrieveKey";
        $arrRs = obten_filas($sql_get_rec);
        $lim = sizeof($arrRs) -1;
            for($i=0; $i<=$lim;$i++){
                if($i ==0) {
                    $str .= "$arrRs[$i]";
                } else {
                    $str .= "@$arrRs[$i]";
                }
            }
            $values = explode("@", $str);
            $defaultAction = "o_3ac340832f29c11538fbe2d6f75e8bcc";
    } else {  
        $values = explode("@", "0@php@@");
    }
    


switch ($o) {
  case "o_e0df5f3dfd2650ae5be9993434e2b2c0":                        //Insert
  //SQL INSERT
  $sql_insert = "INSERT INTO general_formulas( aplicado_a, code_type, description_short, estructura_de_la_formula) VALUES( '$v_b62df', '$v_23088', '$v_0f743', '$v_41f88')";
  my_query($sql_insert);
  echo "<html>
        <body onLoad='javascript:history.back();'>
        </body>
    </html>";


    break;
  case "o_3ac340832f29c11538fbe2d6f75e8bcc":                        //update
  $sql_update = "UPDATE general_formulas SET  aplicado_a='$v_b62df', code_type='$v_23088', description_short='$v_0f743', estructura_de_la_formula='$v_41f88' WHERE aplicado_a='$v_b62df' ";
  my_query($sql_update);
  echo "<html>
        <body onLoad='javascript:history.back();'>
        </body>
    </html>";


    break;

  case "o_099af53f601532dbd31e0ea99ffdeb64":                        //Delete
    $n_type = gettype($v_b62df);
    if ($n_type == "string") {
        $v_b62df = "'$v_b62df'";
    }
  $sql_delete = "DELETE FROM general_formulas WHERE aplicado_a=$v_b62df ";
  my_query($sql_delete);
  echo "<html>
        <body onLoad='javascript:history.back();'>
        </body>
    </html>";

    break;

  default:
    //cargar Values

    break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>general_formulas</title>
</head>
<?php $jxc ->drawJavaScript(false, true); ?>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
    <form name="frm1879d025" action="f_1879d025.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0" method="POST">
    
  <fieldset>
  <legend>[ General Formulas ]</legend>
  <table   >
            <tr> 
                <td>Aplicado A</td> 
                <td ><input  type="text" name="c_b62df" value="<?php echo $values[0] ; ?>" id="i_b62df"  ondblclick="mostraritem();" onkeypress="setCharAction(event);" onblur=" jsGetRegistro();"   size="18" maxlength="18"  /><img src="../images/common/execute.png" id="execmenugif" style="visibility:hidden; width:0px; height:0px;" />
                <div id="mFind"></div></td> 
            
                <td>Code Type</td> 
                <td><select name="c_23088" id="i_23088"> 
 <option value="php ">php</option> 
 <option value="js ">js</option> 
 <option value="human ">human</option> 
 <option value="null ">null</option> 
  
 </select> 
 </td> 
 </tr>          <tr> 
                <td>Description Short</td> 
                <td colspan="3"><input  type="text" name="c_0f743" value="<?php echo $values[2] ; ?>" id="i_0f743"   size="58" maxlength="58"  /></td> 
    </tr>
    <tr>
                <td>Estructura De La Formula</td> 
                <td colspan="3"><textarea name="c_41f88" id="i_41f88" cols="40" rows="10" ><?php echo $values[3] ; ?></textarea></td>
 </tr>
  </table>
  
  </fieldset>
    <input type="hidden" id="ifproperties" value="0" />

    <div id="menuh">
         <table border="1"   >
          <tr> 
 <td onClick="SearchRecord(); HiItem();"><img src="../images/common/find.gif" width="16" height="16" />&nbsp;Buscar Registro</td> 
 </tr>
          <tr> 
 <td onClick="jsGetRegistro(); HiItem();"><img src="../images/common/search.png" width="16" height="16" />&nbsp;Obtener Registro</td> 
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
    <div id="mnuTools">
        <!-- <a name="cNuevo" id="idNew" onclick="cmd(5);">&nbsp;&nbsp;&nbsp;&nbsp;NUEVO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a> -->
        <a name="cGuardar" id="idSave" onclick="cmd(1);"/>&nbsp;&nbsp;&nbsp;AGREGAR&nbsp;&nbsp;&nbsp;&nbsp;</a>
        <a name="cActualizar" id="idUpdate" onclick="cmd(2);">&nbsp;&nbsp;ACTUALIZAR&nbsp;&nbsp;</a>
        <a name="cEliminar" id="idDelete" onclick="cmd(3);">&nbsp;&nbsp;&nbsp;ELIMINAR&nbsp;&nbsp;&nbsp;</a>
     </div>
    </fieldset>
    <div id="avisos"></div>
    </form>
<hr />
</body>
<script  >
var myfrm = document.frm1879d025;
var onEdit = false;
function cmd(is) {
  switch(is) {
    case 1:        //Guardar Registro
    ocultaritem();
      document.frm1879d025.action = "f_1879d025.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0";
      document.frm1879d025.submit();
    break;
    case 2:        //Actualizar Registro
    ocultaritem();
      document.frm1879d025.action = "f_1879d025.php?i=o_3ac340832f29c11538fbe2d6f75e8bcc";
      document.frm1879d025.submit();
    break;
    case 3:        //Eliminar Registro
    ocultaritem();
        var siDel = confirm("USTED HA PEDIDO ELIMINAR EL REGISTRO \n " +
                            "** DESEA CONFIRMA LA ELIMINACION? **");
            if(siDel){
                document.frm1879d025.action = "f_1879d025.php?i=o_099af53f601532dbd31e0ea99ffdeb64";
                  document.frm1879d025.submit();
            }
    break;
    case 5:        //Limpiar Form
    ocultaritem();
      document.frm1879d025.reset();
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
  document.getElementById("i_b62df").value = mKye;
  ClearSearch();
}
function cmdClick(NaNKey){
    //ejecutar una accion al hacer click
    document.getElementById('i_b62df').value = NaNKey;
  setDevNull('mFind');
}
function setCharAction(evt){
    evt=(evt) ? evt:event;
    var charCode = (evt.charCode) ? evt.charCode :
        ((evt.which) ? evt.which : evt.keyCode);
    switch(charCode){
        case 33:
            BackRecord();
        break;
        case 34:        //
            NextRecord();
        break;
        case 113:        //F2
            jsGetRegistro();
        break;
        case 27:        //Escape
            ocultaritem();
        break;
        default:
            return false;
        break;
    }
}
function HiItem(){
  ocultaritem();
  setTimeout("setDevNull('mFind')", 3000);
}
function setDevNull(mID){
  var oID = document.getElementById(mID);
  oID.innerHTML = "";
  oID.removeAttribute("class");
  oID.setAttribute("class", "devnull");
}
</script>
</html>
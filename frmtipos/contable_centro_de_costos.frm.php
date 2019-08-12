<?php
/**
*  @see        Formulario avanzado de contable_centrodecostos
*  @since    2008-01-13 23:30:36
*  @author    PHP Form XUL Wizard V 0.1.10 - Balam Gonzalez Luis (2008)
**/
//=====================================================================================================
    include_once("../core/go.login.inc.php");
    include_once("../core/core.error.inc.php");
    $permiso = getSIPAKALPermissions(__FILE__);
    if($permiso === false){
        saveError(999, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Acceso no permitido a :" . addslashes(__FILE__));
        header ("location:../404.php?i=999");
    }
    $iduser = $_SESSION["log_id"];
//=====================================================================================================

include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$pUSRNivel = $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
$oficial = elusuario($iduser);
$jxc = new TinyAjax();
//Valores de Control por default
$FActual    = date("Y-m-d");
$retrieveKey        = $_GET["x"];

$rw = explode("^", "0^");
//Genera Valores por el GET KEY recibido
if(isset($retrieveKey)){
    if(is_string($retrieveKey )){
        $retrieveKey = "'$retrieveKey'";
    }
    $sqlEXP = "SELECT $ajax_idcontable_centrodecostos, '$ajax_nombre_centrodecostos' FROM contable_centrodecostos WHERE idcontable_centrodecostos=$retrieveKey";
    $rw = obten_filas($sqlEXP);
}
function xul_update_record($ajax_idcontable_centrodecostos, $ajax_nombre_centrodecostos){
$msg = "";
        settype($ajax_idcontable_centrodecostos, "integer");
        settype($ajax_nombre_centrodecostos, "string");

    $strSQL_Update    = "UPDATE contable_centrodecostos SET
                        idcontable_centrodecostos=$ajax_idcontable_centrodecostos, nombre_centrodecostos='$ajax_nombre_centrodecostos'
                          WHERE idcontable_centrodecostos=$ajax_idcontable_centrodecostos";

      $action = my_query($strSQL_Update);

    if($action["stat"] == false){
        $msg = "Se Fallo al Actualizar el Registro";
    } else {
        $msg = "El Registro se Actualizo Exitosamente";
    }
    return $msg;
}

function xul_delete_record($ajax_idcontable_centrodecostos){
$msg = "";
    $strSQL_Delete    = "DELETE FROM contable_centrodecostos
                        WHERE idcontable_centrodecostos=$ajax_idcontable_centrodecostos";
    $action = my_query($strSQL_Delete);
    if($action["stat"] == false){
        $msg = "Se Fallo al Eliminar el Registro";
    } else {
        $msg = "El Registro se Elimino Exitosamente";
    }
    return $msg;
}
function xul_add_record($ajax_idcontable_centrodecostos, $ajax_nombre_centrodecostos){
$msg = "";
        settype($ajax_idcontable_centrodecostos, "integer");
        settype($ajax_nombre_centrodecostos, "string");

    $strSQL_Insert = "INSERT INTO contable_centrodecostos(idcontable_centrodecostos, nombre_centrodecostos)
                        VALUES ($ajax_idcontable_centrodecostos, '$ajax_nombre_centrodecostos')";
    $action = my_query($strSQL_Insert);
    if($action["stat"] == false){
        $msg = "Se Fallo al Agregar el Registro";
    } else {
        $msg = "El Registro se Agrego Exitosamente";
    }
    return $msg;
}
function xul_get_record($ajax_idcontable_centrodecostos){
    $strSQL_Select = "SELECT * FROM contable_centrodecostos
                        WHERE idcontable_centrodecostos=$ajax_idcontable_centrodecostos";
    $tab    = new TinyAjaxBehavior();
    $rw        = obten_filas($strSQL_Select);
        $tab -> add(TabSetValue::getBehavior("id-idcontable_centrodecostos", $rw["idcontable_centrodecostos"]));
             $tab -> add(TabSetValue::getBehavior("id-nombre_centrodecostos", $rw["nombre_centrodecostos"]));


    return $tab -> getString();
}
function xul_next_record($mark){
    $strSQL_Select = "SELECT * FROM contable_centrodecostos
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw        = obten_filas($strSQL_Select);
    $mark++;
        $tab -> add(TabSetValue::getBehavior("id-idcontable_centrodecostos", $rw["idcontable_centrodecostos"]));
             $tab -> add(TabSetValue::getBehavior("id-nombre_centrodecostos", $rw["nombre_centrodecostos"]));

            $tab -> add(TabSetValue::getBehavior("id-markRecord", $mark ));


    return $tab -> getString();
}
function xul_back_record($mark){
    if($mark < 0){
        $mark = 0;
    }
    $strSQL_Select = "SELECT * FROM contable_centrodecostos
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw        = obten_filas($strSQL_Select);
    $mark--;
        $tab -> add(TabSetValue::getBehavior("id-idcontable_centrodecostos", $rw["idcontable_centrodecostos"]));
             $tab -> add(TabSetValue::getBehavior("id-nombre_centrodecostos", $rw["nombre_centrodecostos"]));

            $tab -> add(TabSetValue::getBehavior("id-markRecord", $mark ));


    return $tab -> getString();
}

$jxc ->exportFunction('xul_update_record', array('id-idcontable_centrodecostos', 'id-nombre_centrodecostos'), "#id-messages");
$jxc ->exportFunction('xul_delete_record', array('id-idcontable_centrodecostos'), "#id-messages");
$jxc ->exportFunction('xul_add_record', array('id-idcontable_centrodecostos', 'id-nombre_centrodecostos'), "#id-messages");
$jxc ->exportFunction('xul_get_record', array('id-idcontable_centrodecostos'));
$jxc ->exportFunction('xul_next_record', array('id-markRecord'));
$jxc ->exportFunction('xul_back_record', array('id-markRecord'));
$jxc ->process();

header("Content-type: application/vnd.mozilla.xul+xml");
//header("Content-type: text/plain");


echo "<?xml version=\"1.0\"?>
<?xml-stylesheet href=\"chrome://global/skin/\" type=\"text/css\"?>
<!-- <?xml-stylesheet href=\"../css/xul.css\" type=\"text/css\"?> -->";
?>
<window
    id="index-main-window"
    title=""
     
     
    sizemode="maximized"
    onload="xul_local_disable(true);"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

    <vbox flex="2" id="vbToolbar"  maxheight="32px" height="32px">

    <toolbox>
          <toolbar id="cmd-toolbar">
            <toolbarbutton label="Agregar" image="../images/common/icon-new.png" oncommand="xul_local_new_record();" />
            <toolbarbutton label="Editar" image="../images/common/icon-edit.png" oncommand="xul_local_edit_record();" />
            <toolbarbutton label="Eliminar" image="../images/common/icon-delete.png" oncommand="xul_local_delete_record();" />
            <toolbarbutton label="Guardar" image="../images/common/icon-save.png" oncommand="xul_local_save_record();" />
            <toolbarbutton label="Buscar" image="../images/common/icon-find.png" oncommand="xul_find_record();" />

          <!-- </toolbar>
          <toolbar id="nav-toolbar"> -->
            <!--  <toolbarbutton label="Primero" image="../images/common/icon-first.png" oncommand="xul_first_record();" /> -->
            <toolbarbutton label="Anterior" image="../images/common/icon-previous.png" oncommand="xul_local_back();" />
            <toolbarbutton label="Siguiente" image="../images/common/icon-next.png" oncommand="xul_local_next();" />
            <!--  <toolbarbutton label="Ultimo" image="../images/common/icon-last.png" oncommand="xul_last_record();" />-->
            <label id="id-messages" />
          </toolbar>

    </toolbox>


    </vbox>

    <vbox flex="2" id="vbForm" maxheight="832px">
    <textbox id="id-markRecord" value="0" hidden="true" />

        <grid flex="1">
              <columns>
                <column flex="1" />
                <column flex="1" />
                <column flex="1" />
                <column flex="1" />
              </columns>
            <rows>

                <row>

                    <label value="Numero de Centro de Costo" control="id-idcontable_centrodecostos" />
                    <textbox id="id-idcontable_centrodecostos" value="<?php echo $rw[0]; ?>"  size="10" maxlength="10" />
                    <label value="Nombre del Centro de Costo" control="id-nombre_centrodecostos" />
                    <textbox id="id-nombre_centrodecostos" value="<?php echo $rw[1]; ?>"  size="45" maxlength="45" />
                </row>

              </rows>
        </grid>
    </vbox>
    <!-- <script src="../js/prototype.js"/> -->
    <?php
    $jxc ->drawJavaScript(false, true);
    ?>
    <script>
    var isEdit = false;
    function xul_local_save_record(){
        if(isEdit==false){
            xul_add_record();
            xul_local_disable(true);
        } else {
            xul_update_record();
            xul_local_disable(true);
        }
        isEdit = false;
    }
    function xul_find_record(){
        xul_get_record();
    }
    function xul_local_edit_record(){
        isEdit = true;
        xul_get_record();
        xul_local_disable(false);
    }
    function xul_local_new_record(){
    xul_local_clear();
      isEdit = false;
    }
    function xul_local_delete_record(){
        x = confirm("Confirme que desea eliminar el Registro Actual");
        if(x == true){
            xul_delete_record();
            xul_local_clear();
            xul_local_next();
        }
    }
    function xul_local_clear(){
    xul_local_disable(false);
                 document.getElementById("id-idcontable_centrodecostos").value = "<?php echo $rw[0]; ?>";
                 document.getElementById("id-nombre_centrodecostos").value = "<?php echo $rw[1]; ?>";

    }
    function xul_local_disable(jstat){
    if(jstat == true) {
                 document.getElementById("id-nombre_centrodecostos").setAttribute("disabled", jstat);

    } else {
                 document.getElementById("id-nombre_centrodecostos").removeAttribute("disabled");

    }

    }
    function xul_local_next(){
        xul_next_record();
        xul_local_disable(true);
        isEdit = false;
    }
    function xul_local_back(){
        xul_back_record();
        xul_local_disable(true);
        isEdit = false;
    }
    </script>
    </window>
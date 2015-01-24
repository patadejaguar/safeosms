<?php
/**
*  @see        Formulario para editar Entidades bancarios
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


$pUSRNivel = $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
$oficial = elusuario($iduser); 
/* Tiny Ajax Includes */
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
$jxc = new TinyAjax();
//Valores de Control por default
$FActual    = date("Y-m-d");
$retrieveKey        = $_GET["x"];

$rw = explode("^", "RFC^");
//Genera Valores por el GET KEY recibido
if(isset($retrieveKey)){
    if(is_string($retrieveKey )){
        $retrieveKey = "'$retrieveKey'";
    }
    $sqlEXP = "SELECT idbancos_entidades, rfc_de_la_entidad, nombre_de_la_entidad
                FROM bancos_entidades
                WHERE =$retrieveKey";
    $rw = obten_filas($sqlEXP);
}
function xul_update_record($jx_idbancos_entidades, $jx_rfc_de_la_entidad, $jx_nombre_de_la_entidad){
$msg = "";
        settype($jx_idbancos_entidades, "integer");     
        settype($jx_rfc_de_la_entidad, "string");     
        settype($jx_nombre_de_la_entidad, "string");     

    $strSQL_Update    = "UPDATE bancos_entidades SET
                        idbancos_entidades=$jx_idbancos_entidades, rfc_de_la_entidad='$jx_rfc_de_la_entidad', nombre_de_la_entidad='$jx_nombre_de_la_entidad'
                          WHERE =";

      $action = my_query($strSQL_Update);

    if($action["stat"] == false){
        $msg = "Se Fallo al Actualizar el Registro";
    } else {
        $msg = "El Registro se Actualizo Exitosamente";
    }
    return $msg;
}

function xul_delete_record(){
$msg = "";
    $strSQL_Delete    = "DELETE FROM bancos_entidades
                        WHERE =";
    $action = my_query($strSQL_Delete);
    if($action["stat"] == false){
        $msg = "Se Fallo al Eliminar el Registro";
    } else {
        $msg = "El Registro se Elimino Exitosamente";
    }
    return $msg;
}
function xul_add_record($jx_idbancos_entidades, $jx_rfc_de_la_entidad, $jx_nombre_de_la_entidad){
$msg = "";
        settype($jx_idbancos_entidades, "integer");     
        settype($jx_rfc_de_la_entidad, "string");     
        settype($jx_nombre_de_la_entidad, "string");     

    $strSQL_Insert = "INSERT INTO bancos_entidades(idbancos_entidades, rfc_de_la_entidad, nombre_de_la_entidad)
                        VALUES ($jx_idbancos_entidades, '$jx_rfc_de_la_entidad', '$jx_nombre_de_la_entidad')";
    $action = my_query($strSQL_Insert);
    if($action["stat"] == false){
        $msg = "Se Fallo al Agregar el Registro";
    } else {
        $msg = "El Registro se Agrego Exitosamente";
    }
    return $msg;
}
function xul_get_record(){
    $strSQL_Select = "SELECT * FROM bancos_entidades
                        WHERE =";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
        $tab->add(TabSetValue::getBehavior("id-idbancos_entidades", $rw["idbancos_entidades"])); 
             $tab->add(TabSetValue::getBehavior("id-rfc_de_la_entidad", $rw["rfc_de_la_entidad"])); 
             $tab->add(TabSetValue::getBehavior("id-nombre_de_la_entidad", $rw["nombre_de_la_entidad"])); 
     
    return $tab->getString();
}
function xul_next_record($mark){
    $strSQL_Select = "SELECT * FROM bancos_entidades
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
    $mark++;
        $tab->add(TabSetValue::getBehavior("id-idbancos_entidades", $rw["idbancos_entidades"])); 
             $tab->add(TabSetValue::getBehavior("id-rfc_de_la_entidad", $rw["rfc_de_la_entidad"])); 
             $tab->add(TabSetValue::getBehavior("id-nombre_de_la_entidad", $rw["nombre_de_la_entidad"])); 
     
            $tab->add(TabSetValue::getBehavior("id-markRecord", $mark )); 
    

    return $tab->getString();
}
function xul_back_record($mark){
    if($mark < 0){
        $mark = 0;
    }
    $strSQL_Select = "SELECT * FROM bancos_entidades
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
    $mark--;
        $tab->add(TabSetValue::getBehavior("id-idbancos_entidades", $rw["idbancos_entidades"])); 
             $tab->add(TabSetValue::getBehavior("id-rfc_de_la_entidad", $rw["rfc_de_la_entidad"])); 
             $tab->add(TabSetValue::getBehavior("id-nombre_de_la_entidad", $rw["nombre_de_la_entidad"])); 
     
            $tab->add(TabSetValue::getBehavior("id-markRecord", $mark )); 
    

    return $tab->getString();
}

$jxc ->exportFunction('xul_update_record', array('id-idbancos_entidades', 'id-rfc_de_la_entidad', 'id-nombre_de_la_entidad'), "#id-messages");
$jxc ->exportFunction('xul_delete_record', array('id-'), "#id-messages");
$jxc ->exportFunction('xul_add_record', array('id-idbancos_entidades', 'id-rfc_de_la_entidad', 'id-nombre_de_la_entidad'), "#id-messages");
$jxc ->exportFunction('xul_get_record', array('id-'));
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
 
                    <label value="Numero de Banco" control="id-idbancos_entidades" />
                    <textbox id="id-idbancos_entidades" value="<?php echo $rw[0]; ?>"  size="1" maxlength="1" />
                    <label value="RFC del Banco" control="id-rfc_de_la_entidad" />
                    <textbox id="id-rfc_de_la_entidad" value="<?php echo $rw[1]; ?>"  size="15" maxlength="15" />
                </row>
 
                <row>
 
                    <label value="Nombre del Banco" control="id-nombre_de_la_entidad" />
                    <textbox id="id-nombre_de_la_entidad" value="<?php echo $rw[2]; ?>"  size="4" maxlength="4" />
                </row>
 
            </rows>
        </grid>
    </vbox>
    <popup id="id-popup-messages" >
    <label id="id-messages" />
    </popup>
    <!-- <script src="../js/prototype.js"/> -->
    <?php
    $jxc ->drawJavaScript(false, true);
    ?>
<script>
    c_idbancos_entidades = document.getElementById("id-idbancos_entidades");
    c_rfc_de_la_entidad = document.getElementById("id-rfc_de_la_entidad");
    c_nombre_de_la_entidad = document.getElementById("id-nombre_de_la_entidad");

    var isEdit = false;
    function xul_local_save_record(){
        x = confirm("Confirme que desea ACTUALIZAR el Registro Actual");
        if(x == true){
            if(isEdit==false){
                xul_add_record();
                xul_local_disable(true);
            } else {
                xul_update_record();
                xul_local_disable(true);
            }
            isEdit = false;
            document.getElementById("id-popup-messages").showPopup();
        }
    }
    function xul_find_record(){
        var xVal = prompt("Clave del Registro\r\nque desea buscar:   ", 0);
        document.getElementById("id-").value = xVal;
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
                c_idbancos_entidades.value = "<?php echo $rw[0]; ?>";    
                c_rfc_de_la_entidad.value = "<?php echo $rw[1]; ?>";    
                c_nombre_de_la_entidad.value = "<?php echo $rw[2]; ?>";    

    }
    function xul_local_disable(jstat){
        if ( jstat == true ){
                c_idbancos_entidades.setAttribute("disabled", jstat);    
                c_rfc_de_la_entidad.setAttribute("disabled", jstat);    
                c_nombre_de_la_entidad.setAttribute("disabled", jstat);    

        } else {
                c_idbancos_entidades.removeAttribute("disabled");    
                c_rfc_de_la_entidad.removeAttribute("disabled");    
                c_nombre_de_la_entidad.removeAttribute("disabled");    

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
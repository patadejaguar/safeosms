<?php
/**
*  @see        Formulario avanzado de general_reports
*  @since    2010-05-20 11:31:38
*  @author    PHP Form XUL Wizard V 0.1.10 - Balam Gonzalez Luis (2008)
**/
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

include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
$pUSRNivel = $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
$oficial = elusuario($iduser); 
/* Tiny Ajax Includes */
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
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
    $sqlEXP = "SELECT idreport, descripcion_reports, aplica, idgeneral_reports, explicacion
                FROM general_reports
                WHERE =$retrieveKey";
    $rw = obten_filas($sqlEXP);
}
function xul_update_record($jx_idreport, $jx_descripcion_reports, $jx_aplica, $jx_idgeneral_reports, $jx_explicacion){
$msg = "";
        settype($jx_idreport, "integer");     
        settype($jx_descripcion_reports, "string");     
        settype($jx_aplica, "string");     
        settype($jx_idgeneral_reports, "string");     
        settype($jx_explicacion, "string");     

    $strSQL_Update    = "UPDATE general_reports SET
                        idreport=$jx_idreport, descripcion_reports='$jx_descripcion_reports', aplica='$jx_aplica', idgeneral_reports='$jx_idgeneral_reports', explicacion='$jx_explicacion'
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
    $strSQL_Delete    = "DELETE FROM general_reports
                        WHERE =";
    $action = my_query($strSQL_Delete);
    if($action["stat"] == false){
        $msg = "Se Fallo al Eliminar el Registro";
    } else {
        $msg = "El Registro se Elimino Exitosamente";
    }
    return $msg;
}
function xul_add_record($jx_idreport, $jx_descripcion_reports, $jx_aplica, $jx_idgeneral_reports, $jx_explicacion){
$msg = "";
        settype($jx_idreport, "integer");     
        settype($jx_descripcion_reports, "string");     
        settype($jx_aplica, "string");     
        settype($jx_idgeneral_reports, "string");     
        settype($jx_explicacion, "string");     

    $strSQL_Insert = "INSERT INTO general_reports(idreport, descripcion_reports, aplica, idgeneral_reports, explicacion)
                        VALUES ($jx_idreport, '$jx_descripcion_reports', '$jx_aplica', '$jx_idgeneral_reports', '$jx_explicacion')";
    $action = my_query($strSQL_Insert);
    if($action["stat"] == false){
        $msg = "Se Fallo al Agregar el Registro";
    } else {
        $msg = "El Registro se Agrego Exitosamente";
    }
    return $msg;
}
function xul_get_record(){
    $strSQL_Select = "SELECT * FROM general_reports
                        WHERE =";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
        $tab->add(TabSetValue::getBehavior("id-idreport", $rw["idreport"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_reports", $rw["descripcion_reports"])); 
             $tab->add(TabSetValue::getBehavior("id-aplica", $rw["aplica"])); 
             $tab->add(TabSetValue::getBehavior("id-idgeneral_reports", $rw["idgeneral_reports"])); 
             $tab->add(TabSetValue::getBehavior("id-explicacion", $rw["explicacion"])); 
     
    return $tab->getString();
}
function xul_next_record($mark){
    $strSQL_Select = "SELECT * FROM general_reports
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
    $mark++;
        $tab->add(TabSetValue::getBehavior("id-idreport", $rw["idreport"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_reports", $rw["descripcion_reports"])); 
             $tab->add(TabSetValue::getBehavior("id-aplica", $rw["aplica"])); 
             $tab->add(TabSetValue::getBehavior("id-idgeneral_reports", $rw["idgeneral_reports"])); 
             $tab->add(TabSetValue::getBehavior("id-explicacion", $rw["explicacion"])); 
     
            $tab->add(TabSetValue::getBehavior("id-markRecord", $mark )); 
    

    return $tab->getString();
}
function xul_back_record($mark){
    if($mark < 0){
        $mark = 0;
    }
    $strSQL_Select = "SELECT * FROM general_reports
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
    $mark--;
        $tab->add(TabSetValue::getBehavior("id-idreport", $rw["idreport"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_reports", $rw["descripcion_reports"])); 
             $tab->add(TabSetValue::getBehavior("id-aplica", $rw["aplica"])); 
             $tab->add(TabSetValue::getBehavior("id-idgeneral_reports", $rw["idgeneral_reports"])); 
             $tab->add(TabSetValue::getBehavior("id-explicacion", $rw["explicacion"])); 
     
            $tab->add(TabSetValue::getBehavior("id-markRecord", $mark )); 
    

    return $tab->getString();
}

$jxc ->exportFunction('xul_update_record', array('id-idreport', 'id-descripcion_reports', 'id-aplica', 'id-idgeneral_reports', 'id-explicacion'), "#id-messages");
$jxc ->exportFunction('xul_delete_record', array('id-'), "#id-messages");
$jxc ->exportFunction('xul_add_record', array('id-idreport', 'id-descripcion_reports', 'id-aplica', 'id-idgeneral_reports', 'id-explicacion'), "#id-messages");
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
 
                    <label value="Codigo" control="id-idreport" />
                    <textbox id="id-idreport" value="<?php echo $rw[0]; ?>"  size="1" maxlength="1" />
                    <label value="Nombre" control="id-descripcion_reports" />
                    <textbox id="id-descripcion_reports" value="<?php echo $rw[1]; ?>"  size="20" maxlength="20" />
                </row>
 
                <row>
 
                    <label value="Aplicacion" control="id-aplica" />
                    <textbox id="id-aplica" value="<?php echo $rw[2]; ?>"  size="3" maxlength="3" />
                    <label value="Archivo" control="id-idgeneral_reports" />
                    <textbox id="id-idgeneral_reports" value="<?php echo $rw[3]; ?>"  size="10" maxlength="10" />
                </row>
 
                <row>
 
                    <label value="Descripcion" control="id-explicacion" />
                    <textbox id="id-explicacion" value="<?php echo $rw[4]; ?>"  size="0" maxlength="0" />
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
    c_idreport = document.getElementById("id-idreport");
    c_descripcion_reports = document.getElementById("id-descripcion_reports");
    c_aplica = document.getElementById("id-aplica");
    c_idgeneral_reports = document.getElementById("id-idgeneral_reports");
    c_explicacion = document.getElementById("id-explicacion");

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
                c_idreport.value = "<?php echo $rw[0]; ?>";    
                c_descripcion_reports.value = "<?php echo $rw[1]; ?>";    
                c_aplica.value = "<?php echo $rw[2]; ?>";    
                c_idgeneral_reports.value = "<?php echo $rw[3]; ?>";    
                c_explicacion.value = "<?php echo $rw[4]; ?>";    

    }
    function xul_local_disable(jstat){
        if ( jstat == true ){
                c_idreport.setAttribute("disabled", jstat);    
                c_descripcion_reports.setAttribute("disabled", jstat);    
                c_aplica.setAttribute("disabled", jstat);    
                c_idgeneral_reports.setAttribute("disabled", jstat);    
                c_explicacion.setAttribute("disabled", jstat);    

        } else {
                c_idreport.removeAttribute("disabled");    
                c_descripcion_reports.removeAttribute("disabled");    
                c_aplica.removeAttribute("disabled");    
                c_idgeneral_reports.removeAttribute("disabled");    
                c_explicacion.removeAttribute("disabled");    

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
<?php
/**
*  @see        Formulario avanzado de general_colonias
*  @since    2009-02-25 00:36:17
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
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$pUSRNivel          = $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
$oficial            = elusuario($iduser);

$maxColonia         = mifila("SELECT (MAX(idgeneral_colonia) + 1) AS 'ultimo' FROM general_colonias", "ultimo");

$RemoteAction       = "xul_local_disable(true)";

$action             = $_GET["e"];
    if ( isset($action) ){
        $RemoteAction   = "$action()";
    }
$jxc                = new TinyAjax();
//Valores de Control por default
$FActual            = date("Y-m-d");
$retrieveKey        = $_GET["x"];

$rw = explode("^", "$maxColonia^" . DEFAULT_CODIGO_POSTAL . "^ANOTE_EL_NOMBRE_COMPLETO^Colonia^" . DEFAULT_NOMBRE_LOCALIDAD . "^" . DEFAULT_NOMBRE_MUNICIPIO . "^" . DEFAULT_NOMBRE_ESTADO . "^$FActual^4^1^" . getSucursal());

//Genera Valores por el GET KEY recibido
if(isset($retrieveKey)){
    if(is_string($retrieveKey )){
        $retrieveKey = "'$retrieveKey'";
    }
    $sqlEXP = "SELECT idgeneral_colonia, codigo_postal, nombre_colonia, tipo_colonia, ciudad_colonia, municipio_colonia, estado_colonia, fecha_de_revision, codigo_de_estado, codigo_de_municipio, sucursal
                FROM general_colonias
                WHERE idgeneral_colonia=$retrieveKey";
    $rw = obten_filas($sqlEXP);
}
function xul_update_record($ajax_idgeneral_colonia, $ajax_codigo_postal, $ajax_nombre_colonia, $ajax_tipo_colonia, $ajax_ciudad_colonia, $ajax_municipio_colonia, $ajax_estado_colonia, $ajax_fecha_de_revision, $ajax_codigo_de_estado, $ajax_codigo_de_municipio, $ajax_sucursal){
$msg = "";
        settype($ajax_idgeneral_colonia, "integer");
        settype($ajax_codigo_postal, "integer");
        settype($ajax_nombre_colonia, "string");
        settype($ajax_tipo_colonia, "string");
        settype($ajax_ciudad_colonia, "string");
        settype($ajax_municipio_colonia, "string");
        settype($ajax_estado_colonia, "string");
        settype($ajax_fecha_de_revision, "string");
        settype($ajax_codigo_de_estado, "integer");
        settype($ajax_codigo_de_municipio, "integer");
        settype($ajax_sucursal, "string");

    $strSQL_Update    = "UPDATE general_colonias SET
                        idgeneral_colonia=$ajax_idgeneral_colonia, codigo_postal=$ajax_codigo_postal, nombre_colonia='$ajax_nombre_colonia', tipo_colonia='$ajax_tipo_colonia', ciudad_colonia='$ajax_ciudad_colonia', municipio_colonia='$ajax_municipio_colonia', estado_colonia='$ajax_estado_colonia', fecha_de_revision='$ajax_fecha_de_revision', codigo_de_estado=$ajax_codigo_de_estado, codigo_de_municipio=$ajax_codigo_de_municipio, sucursal='$ajax_sucursal'
                          WHERE idgeneral_colonia=$ajax_idgeneral_colonia";

      $action = my_query($strSQL_Update);

    if($action["stat"] == false){
        $msg = "Se Fallo al Actualizar el Registro";
    } else {
        $msg = "El Registro se Actualizo Exitosamente";
    }
    return $msg;
}

function xul_delete_record($ajax_idgeneral_colonia){
$msg = "";
    $strSQL_Delete    = "DELETE FROM general_colonias
                        WHERE idgeneral_colonia=$ajax_idgeneral_colonia";
    $action = my_query($strSQL_Delete);
    if($action["stat"] == false){
        $msg = "Se Fallo al Eliminar el Registro";
    } else {
        $msg = "El Registro se Elimino Exitosamente";
    }
    return $msg;
}
function xul_add_record($ajax_idgeneral_colonia, $ajax_codigo_postal, $ajax_nombre_colonia, $ajax_tipo_colonia, $ajax_ciudad_colonia, $ajax_municipio_colonia, $ajax_estado_colonia, $ajax_fecha_de_revision, $ajax_codigo_de_estado, $ajax_codigo_de_municipio, $ajax_sucursal){
$msg = "";
        settype($ajax_idgeneral_colonia, "integer");
        settype($ajax_codigo_postal, "integer");
        settype($ajax_nombre_colonia, "string");
        settype($ajax_tipo_colonia, "string");
        settype($ajax_ciudad_colonia, "string");
        settype($ajax_municipio_colonia, "string");
        settype($ajax_estado_colonia, "string");
        settype($ajax_fecha_de_revision, "string");
        settype($ajax_codigo_de_estado, "integer");
        settype($ajax_codigo_de_municipio, "integer");
        settype($ajax_sucursal, "string");

    $strSQL_Insert = "INSERT INTO general_colonias(idgeneral_colonia, codigo_postal, nombre_colonia, tipo_colonia, ciudad_colonia, municipio_colonia, estado_colonia, fecha_de_revision, codigo_de_estado, codigo_de_municipio, sucursal)
                        VALUES ($ajax_idgeneral_colonia, $ajax_codigo_postal, '$ajax_nombre_colonia', '$ajax_tipo_colonia', '$ajax_ciudad_colonia', '$ajax_municipio_colonia', '$ajax_estado_colonia', '$ajax_fecha_de_revision', $ajax_codigo_de_estado, $ajax_codigo_de_municipio, '$ajax_sucursal')";
    $action = my_query($strSQL_Insert);
    if($action["stat"] == false){
        $msg = "Se Fallo al Agregar el Registro";
    } else {
        $msg = "El Registro se Agrego Exitosamente";
    }
    return $msg;
}
function xul_get_record($ajax_idgeneral_colonia){
    $strSQL_Select = "SELECT * FROM general_colonias
                        WHERE idgeneral_colonia=$ajax_idgeneral_colonia";
    $tab    = new TinyAjaxBehavior();
    $rw        = obten_filas($strSQL_Select);
        $tab -> add(TabSetValue::getBehavior("id-idgeneral_colonia", $rw["idgeneral_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_postal", $rw["codigo_postal"]));
             $tab -> add(TabSetValue::getBehavior("id-nombre_colonia", $rw["nombre_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-tipo_colonia", $rw["tipo_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-ciudad_colonia", $rw["ciudad_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-municipio_colonia", $rw["municipio_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-estado_colonia", $rw["estado_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-fecha_de_revision", $rw["fecha_de_revision"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_de_estado", $rw["codigo_de_estado"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_de_municipio", $rw["codigo_de_municipio"]));
             $tab -> add(TabSetValue::getBehavior("id-sucursal", $rw["sucursal"]));


    return $tab -> getString();
}
function xul_next_record($mark){
    $strSQL_Select = "SELECT * FROM general_colonias
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw        = obten_filas($strSQL_Select);
    $mark++;
        $tab -> add(TabSetValue::getBehavior("id-idgeneral_colonia", $rw["idgeneral_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_postal", $rw["codigo_postal"]));
             $tab -> add(TabSetValue::getBehavior("id-nombre_colonia", $rw["nombre_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-tipo_colonia", $rw["tipo_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-ciudad_colonia", $rw["ciudad_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-municipio_colonia", $rw["municipio_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-estado_colonia", $rw["estado_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-fecha_de_revision", $rw["fecha_de_revision"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_de_estado", $rw["codigo_de_estado"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_de_municipio", $rw["codigo_de_municipio"]));
             $tab -> add(TabSetValue::getBehavior("id-sucursal", $rw["sucursal"]));

            $tab -> add(TabSetValue::getBehavior("id-markRecord", $mark ));


    return $tab -> getString();
}
function xul_back_record($mark){
    if($mark < 0){
        $mark = 0;
    }
    $strSQL_Select = "SELECT * FROM general_colonias
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw        = obten_filas($strSQL_Select);
    $mark--;
        $tab -> add(TabSetValue::getBehavior("id-idgeneral_colonia", $rw["idgeneral_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_postal", $rw["codigo_postal"]));
             $tab -> add(TabSetValue::getBehavior("id-nombre_colonia", $rw["nombre_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-tipo_colonia", $rw["tipo_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-ciudad_colonia", $rw["ciudad_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-municipio_colonia", $rw["municipio_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-estado_colonia", $rw["estado_colonia"]));
             $tab -> add(TabSetValue::getBehavior("id-fecha_de_revision", $rw["fecha_de_revision"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_de_estado", $rw["codigo_de_estado"]));
             $tab -> add(TabSetValue::getBehavior("id-codigo_de_municipio", $rw["codigo_de_municipio"]));
             $tab -> add(TabSetValue::getBehavior("id-sucursal", $rw["sucursal"]));

            $tab -> add(TabSetValue::getBehavior("id-markRecord", $mark ));


    return $tab -> getString();
}

$jxc ->exportFunction('xul_update_record', array('id-idgeneral_colonia', 'id-codigo_postal', 'id-nombre_colonia', 'id-tipo_colonia', 'id-ciudad_colonia', 'id-municipio_colonia', 'id-estado_colonia', 'id-fecha_de_revision', 'id-codigo_de_estado', 'id-codigo_de_municipio', 'id-sucursal'), "#id-messages");
$jxc ->exportFunction('xul_delete_record', array('id-idgeneral_colonia'), "#id-messages");
$jxc ->exportFunction('xul_add_record', array('id-idgeneral_colonia', 'id-codigo_postal', 'id-nombre_colonia', 'id-tipo_colonia', 'id-ciudad_colonia', 'id-municipio_colonia', 'id-estado_colonia', 'id-fecha_de_revision', 'id-codigo_de_estado', 'id-codigo_de_municipio', 'id-sucursal'), "#id-messages");
$jxc ->exportFunction('xul_get_record', array('id-idgeneral_colonia'));
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
    height="640"
    width="480"
    sizemode="normal"
    onload="<?php echo $RemoteAction; ?>"
    xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">

    <vbox flex="2" id="vbToolbar"  maxheight="32px" height="32px">

    <toolbox>
          <toolbar id="cmd-toolbar">
            <toolbarbutton label="Agregar" image="../images/common/icon-new.png" oncommand="xul_local_new_record();" />
            <toolbarbutton label="Editar" image="../images/common/icon-edit.png" oncommand="xul_local_edit_record();" />
            <toolbarbutton label="Eliminar" image="../images/common/icon-delete.png" oncommand="xul_local_delete_record();" />
            <toolbarbutton label="Guardar" image="../images/common/icon-save.png" oncommand="xul_local_save_record();" />
            <toolbarbutton label="Buscar" image="../images/common/icon-find.png" oncommand="xul_find_record();" />

            <toolbarbutton label="Anterior" image="../images/common/icon-previous.png" oncommand="xul_local_back();" />
            <toolbarbutton label="Siguiente" image="../images/common/icon-next.png" oncommand="xul_local_next();" />

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

                    <label value="Codigo" control="id-idgeneral_colonia" />
                    <textbox id="id-idgeneral_colonia" value="<?php echo $rw[0]; ?>"  size="10" maxlength="10" />
                    <label value="Codigo Postal" control="id-codigo_postal" />
                    <textbox id="id-codigo_postal" value="<?php echo $rw[1]; ?>"  size="10" maxlength="10" />
                </row>

                <row>

                    <label value="Nombre" control="id-nombre_colonia" />
                    <textbox id="id-nombre_colonia" value="<?php echo $rw[2]; ?>"  size="50" maxlength="100" />
                    <label value="Tipo" control="id-tipo_colonia" />
                     <menulist label="" id="id-tipo_colonia">
                        <menupopup>
                            <menuitem label="Colonia" value ="Colonia" selected="true" />
                            <menuitem label="Barrio" value ="Barrio" />
                            <menuitem label="Ejido" value ="Ejido" />
                            <menuitem label="Unidad habitacional" value ="Unidad habitacional" />
                            <menuitem label="Pueblo" value ="Pueblo" />
                            <menuitem label="Ampliacion" value ="Ampliacion" />
                            <menuitem label="Rancho o rancheria" value ="Rancho o rancheria" />
                            <menuitem label="Congregacion" value ="Congregacion" />
                            <menuitem label="Fraccionamiento" value ="Fraccionamiento" />
                            <menuitem label="Residencial" value ="Residencial" />
                            <menuitem label="Fabrica o industria" value ="Fabrica o industria" />
                        </menupopup>
			</menulist>
                </row>

                <row>

                    <label value="Ciudad" control="id-ciudad_colonia" />
                    <textbox id="id-ciudad_colonia" value="<?php echo $rw[4]; ?>" />
                    <label value="Municipio" control="id-municipio_colonia" />
                    <textbox id="id-municipio_colonia" value="<?php echo $rw[5]; ?>"  />
                </row>

                <row>

                    <label value="Estado" control="id-estado_colonia" />
                    <textbox id="id-estado_colonia" value="<?php echo $rw[6]; ?>"  size="50" maxlength="80" />
                    <label value="Fecha de Revision" control="id-fecha_de_revision" />
                    <textbox id="id-fecha_de_revision" value="<?php echo $rw[7]; ?>"  size="10" maxlength="10" />
                </row>

                <row>

                    <label value="Codigo de Estado" control="id-codigo_de_estado" />
                    <textbox id="id-codigo_de_estado" value="<?php echo $rw[8]; ?>"  size="4" maxlength="4" />
                    <label value="Codigo de Municipio" control="id-codigo_de_municipio" />
                    <textbox id="id-codigo_de_municipio" value="<?php echo $rw[9]; ?>"  size="4" maxlength="4" />
                </row>

                <row>

                    <label value="Sucursal" control="id-sucursal" />

              <?php
                  $s_sucursal    = "SELECT * FROM general_sucursales";
                  $x_sucursal = new cSelect("c-sucursal", "id-sucursal", $s_sucursal);
                  $x_sucursal ->setEsSQL();
                  $x_sucursal ->setOptionSelect(getSucursal());
                  $x_sucursal ->setPut("xul-menu");
                  $x_sucursal ->show(false);
              ?>
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
    var isEdit = false;
    function xul_local_save_record(){
        x = confirm("Confirme que desea GUARDAR el Registro Actual");
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
        document.getElementById("id-idgeneral_colonia").value = xVal;
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
                 document.getElementById("id-idgeneral_colonia").value = "<?php echo $rw[0]; ?>";
                 document.getElementById("id-codigo_postal").value = "<?php echo $rw[1]; ?>";
                 document.getElementById("id-nombre_colonia").value = "<?php echo $rw[2]; ?>";
                 document.getElementById("id-tipo_colonia").value = "<?php echo $rw[3]; ?>";
                 document.getElementById("id-ciudad_colonia").value = "<?php echo $rw[4]; ?>";
                 document.getElementById("id-municipio_colonia").value = "<?php echo $rw[5]; ?>";
                 document.getElementById("id-estado_colonia").value = "<?php echo $rw[6]; ?>";
                 document.getElementById("id-fecha_de_revision").value = "<?php echo $rw[7]; ?>";
                 document.getElementById("id-codigo_de_estado").value = "<?php echo $rw[8]; ?>";
                 document.getElementById("id-codigo_de_municipio").value = "<?php echo $rw[9]; ?>";
                 document.getElementById("id-sucursal").value = "<?php echo $rw[10]; ?>";

    }
    function xul_local_disable(jstat){
    if(jstat == true) {
                 document.getElementById("id-codigo_postal").setAttribute("disabled", jstat);
                 document.getElementById("id-nombre_colonia").setAttribute("disabled", jstat);
                 document.getElementById("id-tipo_colonia").setAttribute("disabled", jstat);
                 document.getElementById("id-ciudad_colonia").setAttribute("disabled", jstat);
                 document.getElementById("id-municipio_colonia").setAttribute("disabled", jstat);
                 document.getElementById("id-estado_colonia").setAttribute("disabled", jstat);
                 document.getElementById("id-fecha_de_revision").setAttribute("disabled", jstat);
                 document.getElementById("id-codigo_de_estado").setAttribute("disabled", jstat);
                 document.getElementById("id-codigo_de_municipio").setAttribute("disabled", jstat);
                 document.getElementById("id-sucursal").setAttribute("disabled", jstat);

    } else {
                 document.getElementById("id-codigo_postal").removeAttribute("disabled");
                 document.getElementById("id-nombre_colonia").removeAttribute("disabled");
                 document.getElementById("id-tipo_colonia").removeAttribute("disabled");
                 document.getElementById("id-ciudad_colonia").removeAttribute("disabled");
                 document.getElementById("id-municipio_colonia").removeAttribute("disabled");
                 document.getElementById("id-estado_colonia").removeAttribute("disabled");
                 document.getElementById("id-fecha_de_revision").removeAttribute("disabled");
                 document.getElementById("id-codigo_de_estado").removeAttribute("disabled");
                 document.getElementById("id-codigo_de_municipio").removeAttribute("disabled");
                 document.getElementById("id-sucursal").removeAttribute("disabled");

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


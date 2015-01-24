<?php
/**
*  @see        Formulario avanzado de captacion_subproductos
*  @since    2010-06-02 22:46:12
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
$pUSRNivel 	= $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
$oficial 	= elusuario($iduser); 
/* Tiny Ajax Includes */
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
$jxc = new TinyAjax();
//Valores de Control por default
$FActual    = date("Y-m-d");
$retrieveKey        = $_GET["x"];

$rw = explode("^", "0^10^^^$FActual^$FActual^AL_FIN_DE_MES^CUENTA^^^^^^^0^");
//Genera Valores por el GET KEY recibido
if(isset($retrieveKey)){
    if(is_string($retrieveKey )){
        $retrieveKey = "'$retrieveKey'";
    }
    $sqlEXP = "SELECT idcaptacion_subproductos, tipo_de_cuenta, descripcion_subproductos, descripcion_completa, fecha_alta, fecha_baja, metodo_de_abono_de_interes, destino_del_interes, algoritmo_de_premio, algoritmo_de_tasa_incremental, algoritmo_modificador_del_interes, contable_movimientos, contable_gastos_por_intereses, contable_intereses_por_pagar, contable_cuentas_castigadas, nombre_del_contrato
                FROM captacion_subproductos
                WHERE idcaptacion_subproductos=$retrieveKey";
    $rw = obten_filas($sqlEXP);
}
function xul_update_record($jx_idcaptacion_subproductos, $jx_tipo_de_cuenta, $jx_descripcion_subproductos, $jx_descripcion_completa, $jx_fecha_alta, $jx_fecha_baja, $jx_metodo_de_abono_de_interes, $jx_destino_del_interes, $jx_algoritmo_de_premio, $jx_algoritmo_de_tasa_incremental, $jx_algoritmo_modificador_del_interes, $jx_contable_movimientos, $jx_contable_gastos_por_intereses, $jx_contable_intereses_por_pagar, $jx_contable_cuentas_castigadas, $jx_nombre_del_contrato){
$msg = "";
        settype($jx_idcaptacion_subproductos, "integer");     
        settype($jx_tipo_de_cuenta, "integer");     
        settype($jx_descripcion_subproductos, "string");     
        settype($jx_descripcion_completa, "string");     
        settype($jx_fecha_alta, "string");     
        settype($jx_fecha_baja, "string");     
        settype($jx_metodo_de_abono_de_interes, "string");     
        settype($jx_destino_del_interes, "string");     
        settype($jx_algoritmo_de_premio, "string");     
        settype($jx_algoritmo_de_tasa_incremental, "string");     
        settype($jx_algoritmo_modificador_del_interes, "string");     
        settype($jx_contable_movimientos, "string");     
        settype($jx_contable_gastos_por_intereses, "string");     
        settype($jx_contable_intereses_por_pagar, "string");     
        settype($jx_contable_cuentas_castigadas, "string");     
        settype($jx_nombre_del_contrato, "string");     

    $strSQL_Update    = "UPDATE captacion_subproductos SET
                        idcaptacion_subproductos=$jx_idcaptacion_subproductos, tipo_de_cuenta=$jx_tipo_de_cuenta, descripcion_subproductos='$jx_descripcion_subproductos', descripcion_completa='$jx_descripcion_completa', fecha_alta='$jx_fecha_alta', fecha_baja='$jx_fecha_baja', metodo_de_abono_de_interes='$jx_metodo_de_abono_de_interes', destino_del_interes='$jx_destino_del_interes', algoritmo_de_premio='$jx_algoritmo_de_premio', algoritmo_de_tasa_incremental='$jx_algoritmo_de_tasa_incremental', algoritmo_modificador_del_interes='$jx_algoritmo_modificador_del_interes', contable_movimientos='$jx_contable_movimientos', contable_gastos_por_intereses='$jx_contable_gastos_por_intereses', contable_intereses_por_pagar='$jx_contable_intereses_por_pagar', contable_cuentas_castigadas='$jx_contable_cuentas_castigadas', nombre_del_contrato='$jx_nombre_del_contrato'
                          WHERE idcaptacion_subproductos=$jx_idcaptacion_subproductos";

      $action = my_query($strSQL_Update);

    if($action["stat"] == false){
        $msg = "Se Fallo al Actualizar el Registro";
    } else {
        $msg = "El Registro se Actualizo Exitosamente";
    }
    return $msg;
}

function xul_delete_record($jx_idcaptacion_subproductos){
$msg = "";
    $strSQL_Delete    = "DELETE FROM captacion_subproductos
                        WHERE idcaptacion_subproductos=$jx_idcaptacion_subproductos";
    $action = my_query($strSQL_Delete);
    if($action["stat"] == false){
        $msg = "Se Fallo al Eliminar el Registro";
    } else {
        $msg = "El Registro se Elimino Exitosamente";
    }
    return $msg;
}
function xul_add_record($jx_idcaptacion_subproductos, $jx_tipo_de_cuenta, $jx_descripcion_subproductos, $jx_descripcion_completa, $jx_fecha_alta, $jx_fecha_baja, $jx_metodo_de_abono_de_interes, $jx_destino_del_interes, $jx_algoritmo_de_premio, $jx_algoritmo_de_tasa_incremental, $jx_algoritmo_modificador_del_interes, $jx_contable_movimientos, $jx_contable_gastos_por_intereses, $jx_contable_intereses_por_pagar, $jx_contable_cuentas_castigadas, $jx_nombre_del_contrato){
$msg = "";
        settype($jx_idcaptacion_subproductos, "integer");     
        settype($jx_tipo_de_cuenta, "integer");     
        settype($jx_descripcion_subproductos, "string");     
        settype($jx_descripcion_completa, "string");     
        settype($jx_fecha_alta, "string");     
        settype($jx_fecha_baja, "string");     
        settype($jx_metodo_de_abono_de_interes, "string");     
        settype($jx_destino_del_interes, "string");     
        settype($jx_algoritmo_de_premio, "string");     
        settype($jx_algoritmo_de_tasa_incremental, "string");     
        settype($jx_algoritmo_modificador_del_interes, "string");     
        settype($jx_contable_movimientos, "string");     
        settype($jx_contable_gastos_por_intereses, "string");     
        settype($jx_contable_intereses_por_pagar, "string");     
        settype($jx_contable_cuentas_castigadas, "string");     
        settype($jx_nombre_del_contrato, "string");     

    $strSQL_Insert = "INSERT INTO captacion_subproductos(idcaptacion_subproductos, tipo_de_cuenta, descripcion_subproductos, descripcion_completa, fecha_alta, fecha_baja, metodo_de_abono_de_interes, destino_del_interes, algoritmo_de_premio, algoritmo_de_tasa_incremental, algoritmo_modificador_del_interes, contable_movimientos, contable_gastos_por_intereses, contable_intereses_por_pagar, contable_cuentas_castigadas, nombre_del_contrato)
                        VALUES ($jx_idcaptacion_subproductos, $jx_tipo_de_cuenta, '$jx_descripcion_subproductos', '$jx_descripcion_completa', '$jx_fecha_alta', '$jx_fecha_baja', '$jx_metodo_de_abono_de_interes', '$jx_destino_del_interes', '$jx_algoritmo_de_premio', '$jx_algoritmo_de_tasa_incremental', '$jx_algoritmo_modificador_del_interes', '$jx_contable_movimientos', '$jx_contable_gastos_por_intereses', '$jx_contable_intereses_por_pagar', '$jx_contable_cuentas_castigadas', '$jx_nombre_del_contrato')";
    $action = my_query($strSQL_Insert);
    if($action["stat"] == false){
        $msg = "Se Fallo al Agregar el Registro";
    } else {
        $msg = "El Registro se Agrego Exitosamente";
    }
    return $msg;
}
function xul_get_record($jx_idcaptacion_subproductos){
    $strSQL_Select = "SELECT * FROM captacion_subproductos
                        WHERE idcaptacion_subproductos=$jx_idcaptacion_subproductos";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
        $tab->add(TabSetValue::getBehavior("id-idcaptacion_subproductos", $rw["idcaptacion_subproductos"])); 
             $tab->add(TabSetValue::getBehavior("id-tipo_de_cuenta", $rw["tipo_de_cuenta"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_subproductos", $rw["descripcion_subproductos"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_completa", $rw["descripcion_completa"])); 
             $tab->add(TabSetValue::getBehavior("id-fecha_alta", $rw["fecha_alta"])); 
             $tab->add(TabSetValue::getBehavior("id-fecha_baja", $rw["fecha_baja"])); 
             $tab->add(TabSetValue::getBehavior("id-metodo_de_abono_de_interes", $rw["metodo_de_abono_de_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-destino_del_interes", $rw["destino_del_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_de_premio", $rw["algoritmo_de_premio"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_de_tasa_incremental", $rw["algoritmo_de_tasa_incremental"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_modificador_del_interes", $rw["algoritmo_modificador_del_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_movimientos", $rw["contable_movimientos"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_gastos_por_intereses", $rw["contable_gastos_por_intereses"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_intereses_por_pagar", $rw["contable_intereses_por_pagar"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_cuentas_castigadas", $rw["contable_cuentas_castigadas"])); 
             $tab->add(TabSetValue::getBehavior("id-nombre_del_contrato", $rw["nombre_del_contrato"])); 
     
    return $tab->getString();
}
function xul_next_record($mark){
    $strSQL_Select = "SELECT * FROM captacion_subproductos
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
    $mark++;
        $tab->add(TabSetValue::getBehavior("id-idcaptacion_subproductos", $rw["idcaptacion_subproductos"])); 
             $tab->add(TabSetValue::getBehavior("id-tipo_de_cuenta", $rw["tipo_de_cuenta"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_subproductos", $rw["descripcion_subproductos"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_completa", $rw["descripcion_completa"])); 
             $tab->add(TabSetValue::getBehavior("id-fecha_alta", $rw["fecha_alta"])); 
             $tab->add(TabSetValue::getBehavior("id-fecha_baja", $rw["fecha_baja"])); 
             $tab->add(TabSetValue::getBehavior("id-metodo_de_abono_de_interes", $rw["metodo_de_abono_de_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-destino_del_interes", $rw["destino_del_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_de_premio", $rw["algoritmo_de_premio"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_de_tasa_incremental", $rw["algoritmo_de_tasa_incremental"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_modificador_del_interes", $rw["algoritmo_modificador_del_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_movimientos", $rw["contable_movimientos"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_gastos_por_intereses", $rw["contable_gastos_por_intereses"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_intereses_por_pagar", $rw["contable_intereses_por_pagar"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_cuentas_castigadas", $rw["contable_cuentas_castigadas"])); 
             $tab->add(TabSetValue::getBehavior("id-nombre_del_contrato", $rw["nombre_del_contrato"])); 
     
            $tab->add(TabSetValue::getBehavior("id-markRecord", $mark )); 
    

    return $tab->getString();
}
function xul_back_record($mark){
    if($mark < 0){
        $mark = 0;
    }
    $strSQL_Select = "SELECT * FROM captacion_subproductos
                          LIMIT $mark, 1";
    $tab    = new TinyAjaxBehavior();
    $rw    = obten_filas($strSQL_Select);
    $mark--;
        $tab->add(TabSetValue::getBehavior("id-idcaptacion_subproductos", $rw["idcaptacion_subproductos"])); 
             $tab->add(TabSetValue::getBehavior("id-tipo_de_cuenta", $rw["tipo_de_cuenta"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_subproductos", $rw["descripcion_subproductos"])); 
             $tab->add(TabSetValue::getBehavior("id-descripcion_completa", $rw["descripcion_completa"])); 
             $tab->add(TabSetValue::getBehavior("id-fecha_alta", $rw["fecha_alta"])); 
             $tab->add(TabSetValue::getBehavior("id-fecha_baja", $rw["fecha_baja"])); 
             $tab->add(TabSetValue::getBehavior("id-metodo_de_abono_de_interes", $rw["metodo_de_abono_de_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-destino_del_interes", $rw["destino_del_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_de_premio", $rw["algoritmo_de_premio"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_de_tasa_incremental", $rw["algoritmo_de_tasa_incremental"])); 
             $tab->add(TabSetValue::getBehavior("id-algoritmo_modificador_del_interes", $rw["algoritmo_modificador_del_interes"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_movimientos", $rw["contable_movimientos"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_gastos_por_intereses", $rw["contable_gastos_por_intereses"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_intereses_por_pagar", $rw["contable_intereses_por_pagar"])); 
             $tab->add(TabSetValue::getBehavior("id-contable_cuentas_castigadas", $rw["contable_cuentas_castigadas"])); 
             $tab->add(TabSetValue::getBehavior("id-nombre_del_contrato", $rw["nombre_del_contrato"])); 
     
            $tab->add(TabSetValue::getBehavior("id-markRecord", $mark )); 
    

    return $tab->getString();
}

$jxc ->exportFunction('xul_update_record', array('id-idcaptacion_subproductos', 'id-tipo_de_cuenta', 'id-descripcion_subproductos', 'id-descripcion_completa', 'id-fecha_alta', 'id-fecha_baja', 'id-metodo_de_abono_de_interes', 'id-destino_del_interes', 'id-algoritmo_de_premio', 'id-algoritmo_de_tasa_incremental', 'id-algoritmo_modificador_del_interes', 'id-contable_movimientos', 'id-contable_gastos_por_intereses', 'id-contable_intereses_por_pagar', 'id-contable_cuentas_castigadas', 'id-nombre_del_contrato'), "#id-messages");
$jxc ->exportFunction('xul_delete_record', array('id-idcaptacion_subproductos'), "#id-messages");
$jxc ->exportFunction('xul_add_record', array('id-idcaptacion_subproductos', 'id-tipo_de_cuenta', 'id-descripcion_subproductos', 'id-descripcion_completa', 'id-fecha_alta', 'id-fecha_baja', 'id-metodo_de_abono_de_interes', 'id-destino_del_interes', 'id-algoritmo_de_premio', 'id-algoritmo_de_tasa_incremental', 'id-algoritmo_modificador_del_interes', 'id-contable_movimientos', 'id-contable_gastos_por_intereses', 'id-contable_intereses_por_pagar', 'id-contable_cuentas_castigadas', 'id-nombre_del_contrato'), "#id-messages");
$jxc ->exportFunction('xul_get_record', array('id-idcaptacion_subproductos'));
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
 
                    <label value="Codigo de Producto" control="id-idcaptacion_subproductos" />
                    <textbox id="id-idcaptacion_subproductos" value="<?php echo $rw[0]; ?>"  size="10" maxlength="10" />
                    <label value="Tipo de cuenta" control="id-tipo_de_cuenta" />
		              <?php
		                  $s_tipo_de_cuenta    = "SELECT * FROM captacion_cuentastipos";
		                  $x_tipo_de_cuenta = new cSelect("c-tipo_de_cuenta", "id-tipo_de_cuenta", $s_tipo_de_cuenta);
		                  $x_tipo_de_cuenta ->setEsSQL();
		                  $x_tipo_de_cuenta ->setOptionSelect($rw[1]);
		                  $x_tipo_de_cuenta ->setPut("xul-menu");
		                  $x_tipo_de_cuenta ->show(false);
		              ?>
                </row>
 
                <row>
 
                    <label value="Nombre" control="id-descripcion_subproductos" />
                    <textbox id="id-descripcion_subproductos" value="<?php echo $rw[2]; ?>"  size="45" maxlength="45" />
                    <label value="Descripcion" control="id-descripcion_completa" />
                    <textbox id="id-descripcion_completa" value="<?php echo $rw[3]; ?>"  size="80" maxlength="200" />
                </row>
 
            </rows>
        </grid>
<tabbox >
  <tabs>
    <tab label="Fechas"/>
    <tab label="Datos del Interes"/>
    <tab label="Datos Contables"/>
    <tab label="Algotimos"/>
    <tab label="Datos del Contrato"/>
  </tabs>
  <tabpanels>
    <tabpanel id="t-fechas" orient="vertical">
                    <label value="Fecha de alta" control="id-fecha_alta" />
                    <textbox id="id-fecha_alta" value="<?php echo $rw[4]; ?>"  size="10" maxlength="10" />
                    <label value="Fecha de baja" control="id-fecha_baja" />
                    <textbox id="id-fecha_baja" value="<?php echo $rw[5]; ?>"  size="10" maxlength="10" />
    </tabpanel>
    <tabpanel id="t-interes" orient="vertical">
                    <label value="Metodo de abono de interes" control="id-metodo_de_abono_de_interes" />
                    <menulist label="" id="id-metodo_de_abono_de_interes">
                        <menupopup>
                            <menuitem label="AL FIN DE MES" value ="AL_FIN_DE_MES" />
                            <menuitem label="AL VENCIMIENTO" value ="AL_VENCIMIENTO" />
                        </menupopup>
                    </menulist>
                    <label value="Destino del interes" control="id-destino_del_interes" />
                    <menulist label="" id="id-destino_del_interes">
                        <menupopup>
                            <menuitem label="CUENTA" value ="CUENTA" />
                            <menuitem label="NUEVA" value ="NUEVA" />
                            <menuitem label="CUENTA INTERESES" value ="CUENTA_INTERESES" />
                        </menupopup>
                    </menulist>        
        </tabpanel>
    <tabpanel id="t-contable" orient="vertical">
                    <label value="Cuenta Contable para Mvtos" control="id-contable_movimientos" />
                    <textbox id="id-contable_movimientos" value="<?php echo $rw[11]; ?>"  size="20" maxlength="20" />
                    <label value="Cuenta Contable para Gastos por Intereses" control="id-contable_gastos_por_intereses" />
                    <textbox id="id-contable_gastos_por_intereses" value="<?php echo $rw[12]; ?>"  size="20" maxlength="20" />
                    <label value="Cuenta Contable para Intereses" control="id-contable_intereses_por_pagar" />
                    <textbox id="id-contable_intereses_por_pagar" value="<?php echo $rw[13]; ?>"  size="20" maxlength="20" />
                    <label value="Cuenta Contable para Castigos" control="id-contable_cuentas_castigadas" />
                    <textbox id="id-contable_cuentas_castigadas" value="<?php echo $rw[14]; ?>"  size="20" maxlength="20" />
    </tabpanel>
    <tabpanel id="t-algoritmos" orient="vertical">
                    <label value="Algoritmo de premio" control="id-algoritmo_de_premio" />
                    <textbox id="id-algoritmo_de_premio" value="<?php echo $rw[8]; ?>"  size="80" maxlength="100"  multiline='true' />
                    <label value="Algoritmo de tasa incremental" control="id-algoritmo_de_tasa_incremental" />
                    <textbox id="id-algoritmo_de_tasa_incremental" value="<?php echo $rw[9]; ?>"  size="80" maxlength="100"  multiline='true' />
                    <label value="Algoritmo modificador del interes" control="id-algoritmo_modificador_del_interes"  />
                    <textbox id="id-algoritmo_modificador_del_interes" value="<?php echo $rw[10]; ?>"  size="80" maxlength="100"  multiline='true' />

    </tabpanel>
    <tabpanel id="t-contrato" orient="vertical">
                    <label value="Nombre del contrato" control="id-nombre_del_contrato" />
                    <textbox id="id-nombre_del_contrato" value="<?php echo $rw[15]; ?>"  size="80" maxlength="100" />
    </tabpanel>
    </tabpanels>
  </tabbox>
    </vbox>
    <popup id="id-popup-messages" >
    <label id="id-messages" />
    </popup>
    <!-- <script src="../js/prototype.js"/> -->
    <?php
    $jxc ->drawJavaScript(false, true);
    ?>
<script>
    c_idcaptacion_subproductos = document.getElementById("id-idcaptacion_subproductos");
    c_tipo_de_cuenta = document.getElementById("id-tipo_de_cuenta");
    c_descripcion_subproductos = document.getElementById("id-descripcion_subproductos");
    c_descripcion_completa = document.getElementById("id-descripcion_completa");
    c_fecha_alta = document.getElementById("id-fecha_alta");
    c_fecha_baja = document.getElementById("id-fecha_baja");
    c_metodo_de_abono_de_interes = document.getElementById("id-metodo_de_abono_de_interes");
    c_destino_del_interes = document.getElementById("id-destino_del_interes");
    c_algoritmo_de_premio = document.getElementById("id-algoritmo_de_premio");
    c_algoritmo_de_tasa_incremental = document.getElementById("id-algoritmo_de_tasa_incremental");
    c_algoritmo_modificador_del_interes = document.getElementById("id-algoritmo_modificador_del_interes");
    c_contable_movimientos = document.getElementById("id-contable_movimientos");
    c_contable_gastos_por_intereses = document.getElementById("id-contable_gastos_por_intereses");
    c_contable_intereses_por_pagar = document.getElementById("id-contable_intereses_por_pagar");
    c_contable_cuentas_castigadas = document.getElementById("id-contable_cuentas_castigadas");
    c_nombre_del_contrato = document.getElementById("id-nombre_del_contrato");

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
        document.getElementById("id-idcaptacion_subproductos").value = xVal;
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
                c_idcaptacion_subproductos.value = "<?php echo $rw[0]; ?>";    
                c_tipo_de_cuenta.value = "<?php echo $rw[1]; ?>";    
                c_descripcion_subproductos.value = "<?php echo $rw[2]; ?>";    
                c_descripcion_completa.value = "<?php echo $rw[3]; ?>";    
                c_fecha_alta.value = "<?php echo $rw[4]; ?>";    
                c_fecha_baja.value = "<?php echo $rw[5]; ?>";    
                c_metodo_de_abono_de_interes.value = "<?php echo $rw[6]; ?>";    
                c_destino_del_interes.value = "<?php echo $rw[7]; ?>";    
                c_algoritmo_de_premio.value = "<?php echo $rw[8]; ?>";    
                c_algoritmo_de_tasa_incremental.value = "<?php echo $rw[9]; ?>";    
                c_algoritmo_modificador_del_interes.value = "<?php echo $rw[10]; ?>";    
                c_contable_movimientos.value = "<?php echo $rw[11]; ?>";    
                c_contable_gastos_por_intereses.value = "<?php echo $rw[12]; ?>";    
                c_contable_intereses_por_pagar.value = "<?php echo $rw[13]; ?>";    
                c_contable_cuentas_castigadas.value = "<?php echo $rw[14]; ?>";    
                c_nombre_del_contrato.value = "<?php echo $rw[15]; ?>";    

    }
    function xul_local_disable(jstat){
        if ( jstat == true ){
                c_tipo_de_cuenta.setAttribute("disabled", jstat);    
                c_descripcion_subproductos.setAttribute("disabled", jstat);    
                c_descripcion_completa.setAttribute("disabled", jstat);    
                c_fecha_alta.setAttribute("disabled", jstat);    
                c_fecha_baja.setAttribute("disabled", jstat);    
                c_metodo_de_abono_de_interes.setAttribute("disabled", jstat);    
                c_destino_del_interes.setAttribute("disabled", jstat);    
                c_algoritmo_de_premio.setAttribute("disabled", jstat);    
                c_algoritmo_de_tasa_incremental.setAttribute("disabled", jstat);    
                c_algoritmo_modificador_del_interes.setAttribute("disabled", jstat);    
                c_contable_movimientos.setAttribute("disabled", jstat);    
                c_contable_gastos_por_intereses.setAttribute("disabled", jstat);    
                c_contable_intereses_por_pagar.setAttribute("disabled", jstat);    
                c_contable_cuentas_castigadas.setAttribute("disabled", jstat);    
                c_nombre_del_contrato.setAttribute("disabled", jstat);    

        } else {
                c_tipo_de_cuenta.removeAttribute("disabled");    
                c_descripcion_subproductos.removeAttribute("disabled");    
                c_descripcion_completa.removeAttribute("disabled");    
                c_fecha_alta.removeAttribute("disabled");    
                c_fecha_baja.removeAttribute("disabled");    
                c_metodo_de_abono_de_interes.removeAttribute("disabled");    
                c_destino_del_interes.removeAttribute("disabled");    
                c_algoritmo_de_premio.removeAttribute("disabled");    
                c_algoritmo_de_tasa_incremental.removeAttribute("disabled");    
                c_algoritmo_modificador_del_interes.removeAttribute("disabled");    
                c_contable_movimientos.removeAttribute("disabled");    
                c_contable_gastos_por_intereses.removeAttribute("disabled");    
                c_contable_intereses_por_pagar.removeAttribute("disabled");    
                c_contable_cuentas_castigadas.removeAttribute("disabled");    
                c_nombre_del_contrato.removeAttribute("disabled");    

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
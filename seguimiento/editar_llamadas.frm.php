<?php
/**
*  @see        Formulario avanzado de seguimiento_llamadas
*  @since    2008-05-10 23:32
*  @author    PHP Form Wizard V 0.75 - Balam Gonzalez Luis (2007)
**/
//=====================================================================================================
  include_once("../core/go.login.inc.php");
  include_once("../core/core.error.inc.php");
  $nivelmin = 2;
  $permiso = getSIPAKALPermissions(__FILE__);
  if($permiso === false){
    saveError(2, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Acceso no permitido a :" . addslashes(__FILE__));
    header ("location:404.php?i=999");
  }
  $iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Agregar llamadas");

$eacp 		= EACP_CLAVE;
$sucursal	= getSucursal();
$fecha		= fechasys();

$idNew		= $_GET["n"];

  	$o 		=  trim($_GET['i']);
    $defaultAction        	= "o_e0df5f3dfd2650ae5be9993434e2b2c0";
    $retrieveKey            = $_GET["x"];
      settype($v_idseguimiento_llamadas, "integer");      // Tipo de idseguimiento_llamadas
      settype($v_numero_socio, "integer");      // Tipo de numero_socio
      settype($v_numero_solicitud, "integer");      // Tipo de numero_solicitud
      settype($v_deuda_total, "double");      // Tipo de deuda_total
      settype($v_telefono_uno, "string");      // Tipo de telefono_uno
      settype($v_telefono_dos, "string");      // Tipo de telefono_dos
      settype($v_fecha_llamada, "string"); // Tipo de fecha_llamada
      settype($v_hora_llamada, "string"); // Tipo de hora_llamada
      settype($v_observaciones, "string");      // Tipo de observaciones
      settype($v_estatus_llamada, "string");      // Tipo de estatus_llamada
      settype($v_oficial_a_cargo, "integer");      // Tipo de oficial_a_cargo



      $v_idseguimiento_llamadas 	= parametro('c_idseguimiento_llamadas');      // Variable de idseguimiento_llamadas
      $v_numero_socio 				= parametro('idsocio');      // Variable de numero_socio
      $v_numero_solicitud			= parametro('idsolicitud');      // Variable de numero_solicitud
      $v_deuda_total 				= parametro('c_deuda_total');      // Variable de deuda_total
      $v_telefono_uno 				= parametro('c_telefono_uno');      // Variable de telefono_uno
      $v_telefono_dos 				= parametro('c_telefono_dos');      // Variable de telefono_dos
      $v_fecha_llamada 				= parametro('c_fecha_llamada');      // Variable de fecha_llamada
      $v_hora_llamada 				= parametro('c_hora_llamada');      // Variable de hora_llamada
      $v_observaciones 				= parametro('c_observaciones');      // Variable de observaciones
      $v_estatus_llamada 			= parametro('c_estatus_llamada');      // Variable de estatus_llamada
      $v_oficial_a_cargo 			= parametro('c_oficial_a_cargo');      // Variable de oficial_a_cargo
      $v_sucursal 					= getSucursal();      // Variable de sucursal
      $v_eacp 						= EACP_CLAVE;      // Variable de eacp

  //Tiny Ajax en Accion
  require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
  $jxc = new TinyAjax();
  //Funcion que rellena los datos del Form
  function jsGetRegistro($filter, $form) {
  $n_type = gettype($filter);
    if ($n_type == "string") {
        $filter = "'$filter'";
    }
    $sql = "SELECT * FROM seguimiento_llamadas WHERE idseguimiento_llamadas = $filter LIMIT 0,1";
    $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
    $nfields = mysql_num_fields($rs)-1;

    $tab = new TinyAjaxBehavior();

    while($rw = mysql_fetch_array($rs)) {
                               $tab -> add(TabSetValue::getBehavior("i_idseguimiento_llamadas", $rw["idseguimiento_llamadas"]));
                               $tab -> add(TabSetValue::getBehavior("idsocio", $rw["numero_socio"]));
                               $tab -> add(TabSetValue::getBehavior("idsolicitud", $rw["numero_solicitud"]));
                               $tab -> add(TabSetValue::getBehavior("i_deuda_total", $rw["deuda_total"]));
                               $tab -> add(TabSetValue::getBehavior("i_telefono_uno", $rw["telefono_uno"]));
                               $tab -> add(TabSetValue::getBehavior("i_telefono_dos", $rw["telefono_dos"]));
                               $tab -> add(TabSetValue::getBehavior("i_fecha_llamada", $rw["fecha_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_hora_llamada", $rw["hora_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_observaciones", $rw["observaciones"]));
                               $tab -> add(TabSetValue::getBehavior("i_estatus_llamada", $rw["estatus_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_oficial_a_cargo", $rw["oficial_a_cargo"]));
                               $tab -> add(TabSetValue::getBehavior("i_sucursal", $rw["sucursal"]));
                               $tab -> add(TabSetValue::getBehavior("i_eacp", $rw["eacp"]));

    }
  return $tab -> getString();
  }
  function NextRecord($init, $form){
    //$init = $init + 1;
    $ifin = $init + 1;

    $sql = "SELECT * FROM seguimiento_llamadas LIMIT $init,$ifin";
    $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
    $nfields = mysql_num_fields($rs)-1;

    $tab = new TinyAjaxBehavior();

    while($rw = mysql_fetch_array($rs)) {
                                    $tab -> add(TabSetValue::getBehavior("i_idseguimiento_llamadas", $rw["idseguimiento_llamadas"]));
                               $tab -> add(TabSetValue::getBehavior("idsocio", $rw["numero_socio"]));
                               $tab -> add(TabSetValue::getBehavior("idsolicitud", $rw["numero_solicitud"]));
                               $tab -> add(TabSetValue::getBehavior("i_deuda_total", $rw["deuda_total"]));
                               $tab -> add(TabSetValue::getBehavior("i_telefono_uno", $rw["telefono_uno"]));
                               $tab -> add(TabSetValue::getBehavior("i_telefono_dos", $rw["telefono_dos"]));
                               $tab -> add(TabSetValue::getBehavior("i_fecha_llamada", $rw["fecha_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_hora_llamada", $rw["hora_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_observaciones", $rw["observaciones"]));
                               $tab -> add(TabSetValue::getBehavior("i_estatus_llamada", $rw["estatus_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_oficial_a_cargo", $rw["oficial_a_cargo"]));
                               $tab -> add(TabSetValue::getBehavior("i_sucursal", $rw["sucursal"]));
                               $tab -> add(TabSetValue::getBehavior("i_eacp", $rw["eacp"]));

      $tab -> add(TabSetValue::getBehavior("ifproperties", $ifin));

    }
  return $tab -> getString();

  }
  function BackRecord($init, $form){
    //$init = $init + 1;
    $ifin = $init - 1;
    if ($ifin>=0) {

      $sql = "SELECT * FROM seguimiento_llamadas LIMIT $ifin,$init";
      $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
      $nfields = mysql_num_fields($rs)-1;

      $tab = new TinyAjaxBehavior();

      while($rw = mysql_fetch_array($rs)) {
                                      $tab -> add(TabSetValue::getBehavior("i_idseguimiento_llamadas", $rw["idseguimiento_llamadas"]));
                               $tab -> add(TabSetValue::getBehavior("idsocio", $rw["numero_socio"]));
                               $tab -> add(TabSetValue::getBehavior("idsolicitud", $rw["numero_solicitud"]));
                               $tab -> add(TabSetValue::getBehavior("i_deuda_total", $rw["deuda_total"]));
                               $tab -> add(TabSetValue::getBehavior("i_telefono_uno", $rw["telefono_uno"]));
                               $tab -> add(TabSetValue::getBehavior("i_telefono_dos", $rw["telefono_dos"]));
                               $tab -> add(TabSetValue::getBehavior("i_fecha_llamada", $rw["fecha_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_hora_llamada", $rw["hora_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_observaciones", $rw["observaciones"]));
                               $tab -> add(TabSetValue::getBehavior("i_estatus_llamada", $rw["estatus_llamada"]));
                               $tab -> add(TabSetValue::getBehavior("i_oficial_a_cargo", $rw["oficial_a_cargo"]));
                               $tab -> add(TabSetValue::getBehavior("i_sucursal", $rw["sucursal"]));
                               $tab -> add(TabSetValue::getBehavior("i_eacp", $rw["eacp"]));

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
      $sql = "SELECT * FROM seguimiento_llamadas WHERE idseguimiento_llamadas LIKE $filter LIMIT 0,$limit_find";
    $rs = mysql_query($sql, cnnGeneral());
        if(!$rs){
            saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sql);
        }
    $tds = "";

    while ($row = mysql_fetch_array($rs)) {
 

      $tds = $tds . "<tr>

          <th onclick='cmdClick(" . $row["idseguimiento_llamadas"] . "); jsGetRegistro(); '>" . $row["idseguimiento_llamadas"] . "</th>
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

  function jsGetNombreSocio($codigo){
  	if ($codigo != 0 && $codigo != ""){
  		return getNombreSocio($codigo);
	}
  }
  function jsGetDescSolicitud($solicitud){
  	if ($solicitud != 0 && $solicitud != ""){
			$xCred			= new cCredito($solicitud);
			$xCred->init();
			$description	= $xCred->getShortDescription();
  	}
  }

  $jxc ->exportFunction('jsGetRegistro', array('i_idseguimiento_llamadas', 'editar_llamadas'));
  $jxc ->exportFunction('SearchRecord', array('i_idseguimiento_llamadas'), '#mFind');
  $jxc ->exportFunction('ClearSearch', array('i_idseguimiento_llamadas'), '#mFind');
  $jxc ->exportFunction('BackRecord', array('ifproperties', 'editar_llamadas'));
  $jxc ->exportFunction('NextRecord', array('ifproperties', 'editar_llamadas'));

  $jxc ->exportFunction('jsGetNombreSocio', array('idsocio'), '#i_nombre_socio');
  $jxc ->exportFunction('jsGetDescSolicitud', array('idsolicitud'), '#i_desc_solicitud');

  $jxc ->process();

    if(isset($retrieveKey)){
        $sql_get_rec = "SELECT  idseguimiento_llamadas, numero_socio, numero_solicitud, deuda_total, telefono_uno,
						telefono_dos, fecha_llamada, hora_llamada, observaciones, estatus_llamada, oficial_a_cargo, sucursal, eacp
                        FROM seguimiento_llamadas WHERE idseguimiento_llamadas=$retrieveKey";
        $arrRs 		= obten_filas($sql_get_rec);
        $lim 		= sizeof($arrRs) -1;
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
    	if (isset($idNew)) {
    		$DCred		= explode(STD_LITERAL_DIVISOR, $idNew);
    		$socio		= $DCred[1];
    		$credito	= $DCred[2];
    		//Inicializar el socio y el credito
			$xSocio		= new cSocio($socio);
			$xSocio->init();
			$DTel		= $xSocio->getTelefonos();
			$xCred		= new cCredito($credito);
			$xCred->init();

    		//TODO: Agregar Telefonos desde el New
    		$telefono1	= $DTel[0];
    		$telefono2	= $DTel[1];
    		$deudaTotal	= $xCred->getSaldoActual();

    		$values = explode(STD_LITERAL_DIVISOR, "$idNew@$deudaTotal@$telefono1@$telefono2@$fecha@08:00@@pendiente@$iduser@$sucursal@$eacp");
    	} else {
    		$values = explode("@", "0@1@1@0.00@0@0@$fecha@08:00@@pendiente@$iduser@$sucursal@$eacp");
    	}
    }



switch ($o) {
  case "o_e0df5f3dfd2650ae5be9993434e2b2c0":                        //Insert
  //SQL INSERT
  $sql_insert = "INSERT INTO seguimiento_llamadas(numero_socio, numero_solicitud, deuda_total, telefono_uno, telefono_dos, fecha_llamada, hora_llamada, observaciones, estatus_llamada, oficial_a_cargo, sucursal, eacp)
  				VALUES($v_numero_socio, $v_numero_solicitud, $v_deuda_total, '$v_telefono_uno', '$v_telefono_dos', '$v_fecha_llamada', '$v_hora_llamada', '$v_observaciones', '$v_estatus_llamada', $v_oficial_a_cargo, '$v_sucursal', '$v_eacp')";
  my_query($sql_insert);
  echo "<html>
        <body onLoad='javascript:history.back();'>
        </body>
    </html>";


    break;
  case "o_3ac340832f29c11538fbe2d6f75e8bcc":                        //update
  $sql_update = "UPDATE seguimiento_llamadas SET  idseguimiento_llamadas=$v_idseguimiento_llamadas, numero_socio=$v_numero_socio, numero_solicitud=$v_numero_solicitud, deuda_total=$v_deuda_total, telefono_uno='$v_telefono_uno', telefono_dos='$v_telefono_dos', fecha_llamada='$v_fecha_llamada', hora_llamada='$v_hora_llamada', observaciones='$v_observaciones', estatus_llamada='$v_estatus_llamada', oficial_a_cargo=$v_oficial_a_cargo, sucursal='$v_sucursal', eacp='$v_eacp' WHERE idseguimiento_llamadas=$v_idseguimiento_llamadas ";
  my_query($sql_update);
  echo "<html>
        <body onLoad='javascript:history.back();'>
        </body>
    </html>";


    break;

  case "o_099af53f601532dbd31e0ea99ffdeb64":                        //Delete
    $n_type = gettype($v_idseguimiento_llamadas);
    if ($n_type == "string") {
        $v_idseguimiento_llamadas = "'$v_idseguimiento_llamadas'";
    }
  $sql_delete = "DELETE FROM seguimiento_llamadas WHERE idseguimiento_llamadas=$v_idseguimiento_llamadas ";
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

$xHP->init("initComponents();");

?>

    <form name="editar_llamadas" action="editar_llamadas.frm.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0" method="POST">

  <fieldset>
  <legend>[ Agregar / Editar el registro de Llamadas Telefonicas ]</legend>
  <table     >
  			<tr>
  				<td>Clave de Persona</td>
  				<td><input type="text" name="idsocio" value="<?php echo $values[1] ; ?>" id="idsocio" onchange="envsoc()" onblur="envsoc()" size='12' class='mny' />
  				<?php echo CTRL_GOSOCIO; ?></td>
				<td colspan='2'><input name='nombresocio' type='text' disabled value='' size="40" /></td>
			</tr>
			<tr>
				<td>Num. de Solicitud</td>
				<td><input type="text" name="idsolicitud" value="<?php echo $values[2] ; ?>" id="idsolicitud" onchange="envsol()" onblur="envsol()" size='12' class='mny' />
				<?php echo CTRL_GOCREDIT; ?></td>
				<td colspan='2'><input name='nombresolicitud' type='text' disabled  value='' size="40" /></td>
  			</tr>
 			<tr>

                <td>Fecha</td>
                <td>
                  <input type="text" name="c_fecha_llamada" value="<?php echo $values[6] ; ?>" id="i_fecha_llamada" size='12'  />
                  <?php echo GO_CALENDAR("i_fecha_llamada"); ?></td>

                <td>Hora</td>
                <td><?php
                	$xFecha = new cFecha();
                	echo $xFecha->getSelectHour(false, "c_hora_llamada", "i_hora_llamada");
                ?></td>
        	</tr>
            <tr>
                <td>Deuda Total</td>
                <td><input  class="imny"  type="text" name="c_deuda_total" value="<?php echo $values[3] ; ?>" id="i_deuda_total"   size="12" maxlength="23"  /></td>
                <td>Estatus Llamada</td>
                <td><select name="c_estatus_llamada" id="i_estatus_llamada">
 						<option value="efectuado">Efectuada</option>
						<option value="cancelado">Cancelada</option>
 						<option value="pendiente" selected>pendiente</option>
 						<option value="vencido">vencida</option>
					</select>
 				</td>
 			</tr>
 			<tr>
                <td>Telefono<br />(Primera Opcion)</td>
                <td><input  type="text" name="c_telefono_uno" value="<?php echo $values[4] ; ?>" id="i_telefono_uno"  size='14' class='mny' maxlength="28"  /></td>

                <td>Telefono<br />(Segunda Opcion)</td>
                <td><input  type="text" name="c_telefono_dos" value="<?php echo $values[5] ; ?>" id="i_telefono_dos"  size='14' class='mny'  maxlength="28"  /></td>
 			</tr>

            <tr>
                <td>Observaciones</td>
                <td  colspan="3" ><textarea name='c_observaciones' id='i_observaciones' cols="50" rows="1" ><?php echo $values[8] ; ?></textarea></td>
            </tr>
            <tr>

                <td>Oficial Asignado</td>
                <td colspan='2'><?php
                	$xSel = new cSelect("c_oficial_a_cargo", "i_oficial_a_cargo",
                					"SELECT id, nombre_completo FROM oficiales WHERE estatus='activo' " );
                	$xSel->setEsSql();
                	$xSel->setOptionSelect($values[10]);
                	$xSel->show(false);
	       			?></td>
            </tr>
  </table>

 	<input type="hidden" name="c_idseguimiento_llamadas" value="<?php echo $values[0] ; ?>" id="i_idseguimiento_llamadas" />
 	<input type="hidden" name="c_sucursal" value="<?php echo getSucursal() ; ?>" id="i_sucursal" />
 	<input type="hidden" name="c_eacp" value="<?php echo EACP_CLAVE; ?>" id="i_eacp" />

  </fieldset>

    <fieldset>
    <legend>[ Operaciones ]</legend>
    <div id="mnuTools">
        <a name="cGuardar" id="idSave" onclick="cmd(1);" class="button">&nbsp;&nbsp;&nbsp;AGREGAR&nbsp;&nbsp;&nbsp;&nbsp;</a>
        <a name="cActualizar" id="idUpdate" onclick="cmd(2);" class="button">&nbsp;&nbsp;ACTUALIZAR&nbsp;&nbsp;</a>
        <a name="cEliminar" id="idDelete" onclick="cmd(3);" class="button">&nbsp;&nbsp;&nbsp;ELIMINAR&nbsp;&nbsp;&nbsp;</a>

     </div>
    </fieldset>



    <div id="avisos"></div>
    </form>
<hr />
</body>
<?php $jxc ->drawJavaScript(false, true);

jsbasic("editar_llamadas","",".");
?>
<script  >
var myfrm 	= document.editar_llamadas;
var onEdit 	= false;

var MINUTE 	= 60 * 1000;
var HOUR 	= 60 * MINUTE;
var DAY 	= 24 * HOUR;
var WEEK 	= 7  * DAY;

/** Funciones Normales */

function cmd(is) {
  switch(is) {
    case 1:        //Guardar Registro
    ocultaritem();
      document.editar_llamadas.action = "editar_llamadas.frm.php?i=o_e0df5f3dfd2650ae5be9993434e2b2c0";
      document.editar_llamadas.submit();
    break;
    case 2:        //Actualizar Registro
    ocultaritem();
      document.editar_llamadas.action = "editar_llamadas.frm.php?i=o_3ac340832f29c11538fbe2d6f75e8bcc";
      document.editar_llamadas.submit();
    break;
    case 3:        //Eliminar Registro
    ocultaritem();
        var siDel = confirm("USTED HA PEDIDO ELIMINAR EL REGISTRO \n " +
                            "** DESEA CONFIRMA LA ELIMINACION? **");
            if(siDel){
                document.editar_llamadas.action = "editar_llamadas.frm.php?i=o_099af53f601532dbd31e0ea99ffdeb64";
                  document.editar_llamadas.submit();
            }
    break;
    case 5:        //Limpiar Form
    ocultaritem();
      document.editar_llamadas.reset();
    break;
  }

}
function mostraritem(item){
}
function ocultaritem(item) {

}
function the_action(mKye) {
  document.getElementById("i_idseguimiento_llamadas").value = mKye;
  ClearSearch();
}
function cmdClick(NaNKey){
    //ejecutar una accion al hacer click
    document.getElementById('i_idseguimiento_llamadas').value = NaNKey;
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
function initComponents(){
	resizeMainWindow();
	//showFlatCalendar();
}
function resizeMainWindow(){
	var mWidth	= 640;
	var mHeight	= 480;
	window.resizeTo(mWidth, mHeight);
}
</script>
</html>
<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 * 		-
 *		-
 */
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
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.captacion.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.operaciones.inc.php");
//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$socio			= $persona;
$cuenta			= ( isset( $_GET["c"] ) ) ? $_GET["c"] : false;
$msg			= ( isset( $_GET[SYS_MSG] ) ) ? $_GET[SYS_MSG] : false;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
?>
<body>

<fieldset>
	<legend>|&nbsp;&nbsp;&nbsp;&nbsp;OPERACIONES EN ACCIONES&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
<form name="OperacionesEnAcciones" action="acciones.frm.php?c=1" method="GET">
	<table    >
		<tbody>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='s' value='<?php echo $socio; ?>' onchange="jsSetNombreSocio();" id="idsocio" size='12' class='mny' /><?php echo CTRL_GOSOCIO; ?></td>
			<td>Nombre Completo</td>
			<td><input disabled name='nombresocio' type='text' value='' size="50"></td>
		</tr>
		<tr>
			<td>Numero de Cuenta</td>
			<td><input type='text' name='c' value='<?php echo $cuenta;?>' onchange="envcta(20)" onblur="envcta(20)" id="idcuenta"  size='15' class='mny' />
			<?php echo CTRL_GOCUENTAS_I; ?></td>
			<td>Descripcion Corta</td>
			<td><input disabled name='nombrecuenta' type='text' value='' size="50"></td>
		</tr>
                <tr>
			<th colspan='2'><input type='button' value='Compra de Acciones' onclick="setGoForm('compra')" /></th>
			<th colspan='2'><input type='button' value='Venta de Acciones' onclick="setGoForm('venta')" /></th>
                </tr>
		</tbody>
	</table>
	<p class='aviso'><?php
	if ( $msg != false ){
		echo $msg;
	}
	?></p>
</form>
</fieldset>
</body>
<?php
$jsb	= new jsBasicForm("OperacionesEnAcciones", iDE_CAPTACION);
//$jsb->setIncludeCaptacion(true);
$jsb->setInputProp("codigo_de_cuenta", "name", "c" );
$jsb->setInputProp("codigo_de_socio", "name", "s" );
$jsb->setTypeCaptacion(20);
$jsb->setSubproducto(70);
$jsb->show();
?>
<script  >
function setGoForm(mAction){
	if ( mAction =='venta' ){
		jsWorkForm.action	= 'venta.acciones.frm.php';
	} else {
		jsWorkForm.action	= 'compra.acciones.frm.php';
	}
	jsWorkForm.submit();
}
</script>
</html>

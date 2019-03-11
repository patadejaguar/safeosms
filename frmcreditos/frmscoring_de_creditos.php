<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
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
$xHP		= new cHPage("TR.CREDITSCORING", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frmscoringcreds", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());




if($credito <= DEFAULT_CREDITO){
	//$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

exit;
?>
<?php
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
$xHP		= new cHPage("TR.Credit Scoring", HP_FORM);

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
//$persona 	= parametro("i");

$xHP->init();
?>
<fieldset>
<legend>Credit Scoring V 0.9</legend>
<?php
	if( $persona != DEFAULT_SOCIO ){
		$action = $_GET["a"];
		$cFSoc	= new cFicha(iDE_SOCIO, $persona);
		$cFSoc->setTableWidth();
		$cFSoc->show();

		if ( isset($action) AND $action == "1"){
			//Capturar el resultado del Scoring
			$xTyp = new cTipos();
			
		} else  {

?>
<form name="credit_scoring" method="post" action="frmscoring_de_creditos.php?a=1&i=<?php echo $persona; ?>">
<fieldset>
<legend>Cuestionario Complemetario para Integrar el Scoring</legend>
<table>
<tbody>
	<tr>
		<th>Pregunta</th>
		<th>Respuesta</th>
	</tr>
	<!-- ORGANIZACIOn -->
	<tr>
		<td>Lleva Registros sobre sus Operaciones; Por ejemplo algun Flujo de Eefectivo, 
		Cortes de Caja, Relacion entre Ventas y Gastos?</td>
		<th>
			<?php 
				echo cBoolSelect("ask-01");
			?>
		</th>
	</tr>
	<tr>
		<td>Que Tipo de Registros lleva?</td>
		<th>
			<select name="ask-01-b" id="id-ask-01-b" >
				<option value="10">Contabilidad Formal</option>
				<option value="3">Solo Cortes de Caja o Registros de Ingresos</option>
				<option value="5" >Solo Comparacion de Gastos Contra Ventas</option>
				<option value="0" selected>Ninguno</option>
			</select>
			
		</th>		
	</tr>

	
	<!-- CLIENTES -->

	<tr>
		<td>Puede Identificar sus clientes?</td>
		<th>
			<?php 
				echo cBoolSelect("ask-02");
				
			?>
		</th>		
	</tr>
	<tr>
		<td>De Donde Provienen la Mayoria de sus Ingresos por Ventas?</td>
		<th>
			<select name="ask-02-b" id="id-ask-02-b">
				<option value="5">Del Publico en General</option>
				<option value="2">De Unos Cuantos Clientes</option>
				<option value="5">De Varios Clientes</option>
				<option value="0" selected>No Identificado</option>
			</select>
		</th>
	</tr>
	
	<!-- PROVEEDORES -->

	<tr>
		<td>Puede Identificar proveedores?</td>
		<th>
			<?php 
				echo cBoolSelect("ask-03");
				
			?>
		</th>		
	</tr>
	<tr>
		<td>�Cuantos Proveedores Tiene?</td>
		<th>
			<select name="ask-03-b" id="id-ask-03-b">
				<option value="2">De 01 a 05</option>
				<option value="5">De 06 a 10</option>
				<option value="10">Mas de 10</option>
				<option value="0">No Identificado</option>
			</select>
		</th>
	</tr>

	<tr>
		<td>�Que Tama�o tienen sus Principales Proveedores?</td>
		<th>
			<select name="ask-03-c" id="id-ask-03-c">
				<option value="10">Empresas Grandes</option>
				<option value="5">Empresas Fijas</option>
				<option value="2">Empresas Peque�as</option>
				<option value="0">No Identificado</option>
			</select>
		</th>
	</tr>
	
	<!-- FUERZA LABORAL -->

	<tr>
		<td>�Cuantos Empleados Tiene?</td>
		<th>
			<select name="ask-04" id="id-ask-04">
				<option value="0">Ninguno</option>
				<option value="2">De 01 a 05</option>
				<option value="5">De 06 a 10</option>
				<option value="10">Mas de 10</option>
			</select>
		</th>
	</tr>

	<tr>
		<td>�Cual es la Situacion Patronal Actual de sus Empleados?</td>
		<th>
			<select name="ask-04-b" id="id-ask-04-b">
				<option value="5">Empresa Familiar</option>
				<option value="10">Todos Inscritos en el IMSS</option>
				<option value="2">Menos de la Mitad Inscritos en el IMSS</option>
				<option value="4">Mas de la Mitad Inscritos en el IMSS</option>
			</select>
		</th>
	</tr>
	
	<tr>
		<td>�Aceptaria una Visita para Verificar sus Datos?</td>
		<th>
			<?php 
				echo cBoolSelect("ask-05");
				
			?>
		</th>
	</tr>
	
</tbody>
</table>
</fieldset>
</form>
<?php
		}
	} else {
	?>
	<form name="frm_credit_scoring" method="post" action="frmscoring_de_creditos.php">
	<table  >
		<tbody>
			<tr>
				<td>Clave de Persona</td>
				<td><input type='text' name='idsocio' value='' onchange="envsoc();" /><?php echo CTRL_GOSOCIO; ?></td>
				<td>Nombre Completo</td>
				<td><input name='nombresocio' type='text' disabled value='' size="60" /></td>
			</tr>
			<tr>
				<td colspan="4"><input type="submit" /></td>
			</tr>
		</tbody>	
	</table>
	</form>
	<?php
}

jsbasic("frm_credit_scoring", "", ".");
?>
</fieldset>
</body>
<script  >
function jsNoVerDocumentacion(){
	if (parseInt(document.getElementById("id-ask-01").value) == 0){
		document.getElementById("id-ask-01-b").style.visible = "hidden";
		document.getElementById("id-ask-02").focus();
	} else {
		document.getElementById("id-ask-01-b").style.visible = "visible";
	}
}
</script>
</html>

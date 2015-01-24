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
$xHP		= new cHPage("TR.Reporte de Catalogo de Cuentas", HP_FORM);

echo $xHP->getHeader();

echo jsBasicContable("frmreports");
$xBtn		= new cHButton("");
$xChk		= new cHCheckBox();

echo $xHP->setBodyinit("initComponents();");


?>
<form name="frmreports" method="post" action="" class="formoid-default">
<table>
	<tbody>
    <tr>
		<td><fieldset>
			<legend>Tipo de Cuentas</legend>
			Tipo de Cuentas :<br />
			<?php 	$cCta = new cSelect("tipocuentas", "", "contable_catalogotipos");
					//$cCta->addEspOption("algunas", "Algunas");
					$cCta->addEspOption(SYS_TODAS);
					//$cCta->addEspOption("cuadre", "Cuadre");
					$cCta->setOptionSelect(SYS_TODAS);
					$cCta->show(false);
			 ?><br />
	<!-- comparativo Real vs Presupuesto -->
	<!-- en Presentacion Especial UDIS/Pesos/Dolares -->
		</fieldset>
		</td>
		</tr>
		<tr>
			<td><fieldset>
				<legend>Rango de Niveles</legend>
				<select name="rangoNiveles" id="idrangoNiveles">
					<option value="1">Titulo</option>
					<option value="2">SubTitulo</option>
					<option value="3">Mayor</option>
					<option value="99">Personalizar</option>
					<option value="0">Todas</option>
				</select>
			</fieldset></td>
		</tr>
		<tr>
    		<td>
			<fieldset>
				<legend>Otras opciones</legend>
				<?php 
					echo $xChk->get("TR.Solo Afectables", "soloAfectables");
				?>
				
				<p><input type="checkbox" name="QueAcumulenA" />Que Acumulen a :</p>
				<p><input type='text' name='ci' value='<?php echo ZERO_EXO; ?>' id="idci" size="35" onchange="getCuentaFmt('idci');" /></p>
				<?php 
					echo $xChk->get("TR.Mostrar Todos", "mostrarTodos");
				?>				
				<!--  <p><input type="checkbox" name="mMostrarTodos" />Mostrar Todas las Cuentas(Es Tardado)</p> -->
		</fieldset>
      </td>
    </tr>

    <tr>
    	<td colspan="2">
			<table style=""
 				     >
			<tbody>
				<tr>

					<td><?php echo $xBtn->getBasic("Aceptar", "cmdTAceptar(event);", "aceptar"); ?></td>
					<td><?php echo $xBtn->getSalir(); ?></td>
				</tr>
			</tbody>
			</table>
    	</td>
    </tr>
	</tbody>
</table>
</form>
<?php 
echo $xHP->setBodyEnd();
?>
<script  >
var xG		= new Gen();
function cmdTEliminar(evt){
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function cmdTNuevo(evt){
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function cmdTAceptar(evt){
	openReport();
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function cmdTImprimir(evt){
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function imgAsClicked(sId){

}
/**
**********************************
*/
var wfrm = document.frmreports;

function openReport() {
		//control de opciones
		var vf71 = 0;		//Cuenta Especial
		var vf70 = 0;		//Estatus
		var vf72 = 0;		//Convenio
		var vf73 = 0;		//Fechas
		
		if(wfrm.QueAcumulenA.checked){
			vf71 = 1;
		}
		var mTodas		= $("#mostrarTodos").prop('checked');//(wfrm.mMostrarTodos.checked) ? 1 : 0;
		var mAffect		= $("#soloAfectables").prop('checked');
		//
		vfor = document.getElementById("idci").value;
		vto = 0;	//document.getElementById("idcf").value;
		vf1 = document.getElementById("idtipocuentas").value;
		vf2 = document.getElementById("idrangoNiveles").value;
		vf3 = 0;
		vOut = 0;
		fi = 0;
		ff = 0;

		var urlrpt = "rpt_reporte_del_catalogo.php?" + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' +
						vto + '&out=' + vOut + '&f1=' + vf1 + '&f2=' + vf2 + '&f71=' + vf71 + '&mostrar=' + mTodas + "&afectables=" + mAffect;
		xG.w({ url: urlrpt, h : 800, w: 600 });
}
function initComponents(){
}
</script>
<?php
$xHP->end(); 
?>

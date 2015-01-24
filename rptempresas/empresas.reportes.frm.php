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
$xHP		= new cHPage("Reportes por Empresas", HP_FORM);

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->getHeader();

$jsb	= new jsBasicForm("mfrm", iDE_CAPTACION);
$jsb->show();
//$jxc ->drawJavaScript(false, true);
echo $xHP->setBodyinit();

$oFRM		= new cHForm("frmTemplate", "./", "mfrm", "","formoid-default");
$oFRM->setTitle("TR.Reportes por Empresas");

echo $xHP->setBodyEnd();

$xReports	= new cPanelDeReportes(iDE_CREDITO, "empresas");
$xReports->get();

$rpts	= new cGeneral_reports();
$sel3	= $xReports->getSelectReportes();  //$rpts->query()->html()->select( $rpts->descripcion_reports()->get(), " aplica='empresas' " );


$emp	= new cSocios_aeconomica_dependencias();
$sel 	= $emp->query()->html()->select($emp->descripcion_dependencia()->get());

$sel->addOptions(array("todas" => "TODAS"));

$per	= new cCreditos_periocidadpagos();
$sel4 	= $per->query()->html()->select($per->descripcion_periocidadpagos()->get());
$sel4->addOptions(array("todas" => "TODAS"));

$pdto	= new cCreditos_tipoconvenio();
$sel2	= $pdto->query()->html()->select($pdto->descripcion_tipoconvenio()->get() );
$sel2->addOptions(array("todas" => "TODAS"));


$base	= new cEacp_config_bases_de_integracion();
$selB	= $base->query()->html()->select($base->descripcion()->get());
//
$xSel	= new cHSelect();
$xSel->addOptions(array(
		"chart" => "Grafico",
		"default" => "Normal",
		OUT_EXCEL => "Compatible con Excel"
		));
$xSel->setDefault(SYS_DEFAULT);

$xF1	= new cHDate(0);
$xF2	= new cHDate(1);
//estado
$xBtn	= new cHButton("submit", "Ejecutar", "");
$xCbza	= new cHCobros();

$oFRM->addHElem( $sel->get("idempresa", "Empresa") );

$oFRM->addHElem( $sel3 );

$oFRM->addHElem( $sel2->get("idproducto", "Producto", "todas") );
$oFRM->addHElem( $sel4->get("idperiocidad", "Periocidad", "todas") );
$oFRM->addHElem( $xCbza->get() );

$oFRM->addHElem($selB->get("idbase", "Base de Reporte"));
$oFRM->addHElem($xSel->get("idout", "Formato de Salida") );
		
$oFRM->addHElem( $xF1->get("Fecha Inicial") );
$oFRM->addHElem( $xF2->get("Fecha Final") );

$F	= new cFecha();
/*
$F->setFechaPorQuincena(17);
$FI	= $F->getDayName();
$FF	= $F->get();
*/

$oFRM->addToolbar($xBtn->getEjecutar("jsGetReporte();", "", "", true ) );

//http://localhost/rptcreditos/empresas.movimientos.rpt.php?empresa=101&periodo=20&periocidad=7
echo $oFRM->get();
?>
<!-- HTML content -->

<script>
    function jsGetReporte(){
	var empresa	= $("#idempresa").val();
	var f1		= $("#idfecha-0").val();
	var f2		= $("#idfecha-1").val();
	var producto	= $("#idproducto").val();
	var reporte	= $("#idreporte").val();
	var periocidad	= $("#idperiocidad").val();
	var mBase		= $("#idbase").val();
	var out			= $("#idout").val();
	var tpago		= $("#idtipo_pago").val();
	
	var g 		= new Gen();
	g.w({
	    url : reporte + "empresa=" + empresa + "&producto=" + producto + "&on=" + f1 + "&off=" + f2 + "&periocidad=" + periocidad + "&base=" + mBase + "&out=" + out + "&pago=" + tpago + "&mx=true"
	});
    }
</script>
<?php
$xHP->end();
?>
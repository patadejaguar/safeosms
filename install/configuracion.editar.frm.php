<?php
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

$oficial 	= elusuario($iduser);
$tipo	 	= parametro("cmenu", false, MQL_RAW);
$tipo	 	= parametro("tipo", $tipo, MQL_RAW);

$txtBuscar	= parametro("idbuscar", "", MQL_RAW);
$txtBuscar	= parametro("buscar", $txtBuscar, MQL_RAW);

$tipo		= strtolower($tipo);

$xHP		= new cHPage("TR.Editar Configuracion del Sistema", HP_FORM);
$xHP->addJTableSupport();
$xHP->init();


//if($parent == false AND $txtBuscar == ""){
	$w			= ($tipo == "") ?  "" : " AND(`tipo`='$tipo') ";
	

	$xFRM		= new cHForm("frmeditar", "configuracion.editar.frm.php");
	$xFRM->setTitle($xHP->getTitle());
	
	$sqlMost 	= "SELECT tipo, CONCAT('(' , COUNT(nombre_del_parametro), ') ', tipo ) AS 'conceptos'
					    FROM entidad_configuracion WHERE `estatus`=1 $w
					GROUP BY tipo
					ORDER BY tipo ";
	$cSel 		= new cSelect("cmenu", "cmenu", $sqlMost);
	$cSel->setEsSql();
	$cSel->addEspOption(SYS_TODAS);
	$cSel->setOptionSelect(SYS_TODAS);
	$cSel->addEvent("onchange", "setFiltrar()");
	
	if($tipo == ""){
		$xFRM->addHElem( $cSel->get("TR.Parametro", true) );
	} else {
		$xFRM->OHidden("cmenu", $tipo);
	}
	$xFRM->OText("idbuscar", "", "TR.Buscar Texto");
	//$xFRM->setValidacion("idbuscar", "setFiltrar");
	
	//$xFRM->addSubmit();
	$xFRM->addCerrar();
	$xFRM->OButton("TR.FILTRO", "setFiltrar()", $xFRM->ic()->FILTRO, "", "yellow");
	
	//Grid
	$xHG	= new cHGrid("iddiv",$xHP->getTitle());
	
	$xHG->setSQL("SELECT * FROM `entidad_configuracion` WHERE `estatus`=1 $w");
	$xHG->addList();
	$xHG->setOrdenar();
	
	$xHG->col("tipo", "TR.TIPO", "10%");
	$xHG->addKey("nombre_del_parametro");
	if($xFRM->getEnDesarrollo() == true){
		$xHG->col("nombre_del_parametro", "TR.NOMBRE", "20%");
	}
	$xHG->col("descripcion_del_parametro", "TR.DESCRIPCION", "30%");
	$xHG->col("valor_del_parametro", "TR.VALOR", "20%");
	
	//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
	$xHG->OButton("TR.EDITAR", "jsEdit(\''+ data.record.nombre_del_parametro +'\')", "edit.png");
	$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.clave_de_control +')", "undone.png");
	
	//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.nombre_del_parametro +')", "delete.png");
	$xFRM->addHElem("<div id='iddiv'></div>");
	$xFRM->addJsCode( $xHG->getJs(true) );
	echo $xFRM->get();
	?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../install/entidad-configuracion.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddiv});
}
function jsAdd(){
	//xG.w({url:"../frm/.new.frm.php?", tiny:true, callback: jsLGiddiv});
}
function jsDel(id){
	//xG.rmRecord({tabla:"entidad_configuracion", id:id, callback:jsLGiddiv});
}
function jsDeact(id){
    xG.recordInActive({tabla:"aml_alerts", id:id, callback:jsLGiddiv, preguntar:true });
}
function setFiltrar(){
	var tipo	= String($("#cmenu").val()).toLowerCase();
	var txtb	= $("#idbuscar").val();
	
	var str		= "";
	
	if(tipo !== "todas"){
		str		= " AND (`entidad_configuracion`.`tipo`='" + tipo + "') ";
	}
	
	if($.trim(txtb) !== ""){
		str		= (str === "") ? " AND (`entidad_configuracion`.`nombre_del_parametro` LIKE '%"  + txtb +  "%' OR `entidad_configuracion`.`valor_del_parametro` LIKE '%" + txtb + "%') " : str + " AND (`entidad_configuracion`.`nombre_del_parametro` LIKE '%"  + txtb +  "%' OR `entidad_configuracion`.`valor_del_parametro` LIKE '%" + txtb + "%') ";
	}
	
	if($.trim(str) !== ""){
		str			= "&w="  + base64.encode(str);
		$('#iddiv').jtable('destroy');
		jsLGiddiv(str);	
	}

	return true;
}

</script>
<?php

//} else {
/*		$filtro1			= "";
		$filtro2			= "";
		
		$filtro1			= ($parent != SYS_TODAS AND $parent != false AND $parent != "") ? " tipo = '$parent' " : "";

		if ( $txtBuscar !=  "" ){
			$filtro2		.= (trim($filtro1) == "") ? "" : " AND "; 
			$filtro2		.= " ( nombre_del_parametro LIKE '%$txtBuscar%' OR descripcion_del_parametro LIKE '%$txtBuscar%' ) ";
		}
		$xHP->setNoDefaultCSS();
		echo $xHP->getHeader(true);
		//setLog("$filtro1 $filtro2");
		echo '<body onmouseup="SetMouseDown(false);" >';
                        // Define your grid
                        $_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
                        //,menu_type
                        $_SESSION["grid"]->SetSqlSelect('nombre_del_parametro,
							UCASE(nombre_del_parametro) AS "parametro",
							descripcion_del_parametro,
							valor_del_parametro ',
							'entidad_configuracion', trim("$filtro1 $filtro2"));
						$_SESSION["grid"]->SetUniqueDatabaseColumn("nombre_del_parametro", false);
						$_SESSION["grid"]->SetTitleName("Editar Configuracion de la Entidad de la Seccion " . strtoupper($parent) );
						
						// End definition

						$_SESSION["grid"]->SetDatabaseColumnWidth("parametro",400);
						$_SESSION["grid"]->SetDatabaseColumnName("parametro", "Parametro");
						$_SESSION["grid"]->SetDatabaseColumnEditable("parametro", false);
						
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("descripcion_del_parametror",400);
						$_SESSION["grid"]->SetDatabaseColumnName("descripcion_del_parametro", "Descripcion");
						$_SESSION["grid"]->SetDatabaseColumnEditable("descripcion_del_parametro", false);
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("valor_del_parametror",250);
						$_SESSION["grid"]->SetDatabaseColumnName("valor_del_parametro", "Valor");
												
						$_SESSION["grid"]->SetMaxRowsEachPage(40);
						$_SESSION["grid"]->PrintGrid(MODE_EDIT);

						//Create the grid.*/
//}
$xHP->fin();
?>
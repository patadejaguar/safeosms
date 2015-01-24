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
$xHP		= new cHPage("TR.Editar estructura", HP_FORM);

$oficial	= elusuario($iduser);
$table 		=parametro("ctable", false, MQL_RAW);
$actualizar	= parametro("forzar", false, MQL_BOOL);

if($actualizar == true){
	$table		= strtolower($table);
	$xUtil		= new cTableStructure($table);
	$command	= ($xUtil->getNumeroDeCampos() > 0) ? SYS_CERO : SYS_UNO;
	$xUtil->setStructureTableByDemand($command); //0 update
}

if ($table == false){
	$xHP->init();	
	$xFRM		= new cHForm("frmeditstructure", "frm_edit_structure.php");
	$xBtn		= new cHButton();
	$xTxt		= new cHText();
	$xDate		= new cHDate();
	$xChk		= new cHCheckBox();
	//$xSel		= new cHSelect();
	$xFRM->setTitle("TR.Editar Estructura del Sistema");
	
	$xSel		= new cSelect("ctable", "ctable", "SHOW TABLES IN " . MY_DB_IN );
	$xSel->setEsSql();
	
	$xFRM->addHElem( $xSel->get("TR.Nombre de la Tabla", true) );
	$xFRM->addHElem( $xChk->get("TR.Actualizar", "forzar") );
	
	$xFRM->addSubmit();
	$xFRM->addToolbar( $xBtn->getBasic("TR.Respaldo", "jsGetBackup()", "respaldo", "idgetrespaldo", false) );
	
	echo $xFRM->get();
	?>
<script>
	var xg		= new Gen();
	function jsGetBackup(){
	    var url			= "http://localhost/utils/download.php?tabla=general_structure";
	    xg.w({ url : url, w : 800, h : 600 });
	}	
</script>
	<?php 
}	else	{
	$table		= strtolower($table);
	$xHP		= new cHPage("TR.Editar estructura", HP_GRID);
	$xHP->setNoDefaultCSS();
	$xHP->init();
                        // Define your grid
                        $_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
                        $_SESSION["grid"]->SetSqlSelect('index_struct, campo, valor, tipo, longitud, descripcion, titulo, control, sql_select, order_index', 'general_structure', " tabla='$table' ");
						$_SESSION["grid"]->SetUniqueDatabaseColumn("index_struct", false);
						$_SESSION["grid"]->SetTitleName("Editar Estructura de $table");

						// End definition
						$_SESSION["grid"]->SetDatabaseColumnName("longitud", "Tama&ntilde;o");
						
						//$_SESSION["grid"]->SetDatabaseColumnName(
						
						$_SESSION["grid"]->SetDatabaseColumnName("order_index", "Orden");
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("Tama&ntilde;o", 3);
						$_SESSION["grid"]->SetDatabaseColumnWidth("Orden", 3);
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("tipo", 10);
						
						$_SESSION["grid"]->SetDatabaseColumnWidth("control", 12);
						$_SESSION["grid"]->SetEditModeAdd(false);
						
						$_SESSION["grid"]->SetMaxRowsEachPage(30);
						$_SESSION["grid"]->PrintGrid(MODE_EDIT);

						//Create the grid.

}
$xHP->fin();
?>
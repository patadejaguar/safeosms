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
$xHP		= new cHPage("TR.CODIGO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();


$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$idgrid		= parametro("idgrid", "", MQL_RAW);
$tabla		= parametro("tabla", "", MQL_RAW);
$nombreForm	= parametro("nombreforma", "", MQL_RAW);
$nombreFile	= parametro("nombrefile", "", MQL_RAW);
$menuid		= parametro("menu",0, MQL_INT);
$menuparent	= parametro("menuparent",9999, MQL_INT);
$idtit		= parametro("title", "", MQL_RAW);
$idruta		= parametro("ruta", "", MQL_RAW);
$idsql		= parametro("idsql", "", MQL_RAW);
$prefijoObj	= parametro("prefijo", "", MQL_RAW);

$menuid		= ($menuid <=0 ) ? "NULL" : $menuid;



	$mEquivalencias	= array(
			"INT" 		=> "int",
			"TINYINT" 	=> "int",
			"SMALLINT" 	=> "int",
			"MEDIUMINT" => "int",
			"BIGINT" 	=> "int",
			"YEAR" 		=> "int",
			"TIMESTAMP" => "int",

			"FLOAT" 	=> "float",
			"DOUBLE" 	=> "float",
			"DECIMAL" 	=> "float",

			"VARCHAR" 	=> "string",
			"CHAR" 		=> "string",
			"TEXT" 		=> "string",
			"LONGTEXT" 	=> "string",
			"TINYTEXT" 	=> "string",
			"MEDIUMTEXT" => "string",
			"DATE" 		=> "string",
			"DATETIME" 	=> "string",
			"TIME" 		=> "string",
			"ENUM"		=> "string",

			"BINARY" 	=> "string",
			"BLOB" 		=> "string",
			"MEDIUMBLOB" 		=> "string"
	);


//$tabla				= ( isset($_GET["tabla"]) ) ? $_GET["tabla"] : "";
$create				= ( isset($_GET["create"]) ) ? true : false;

$cmdForms			= ( isset($_GET["forms"]) ) ? true : false;
$cmdReplace			= ( isset($_GET["sql"]) ) ? true : false;

//========================= Testea si la DB Existe
$isSQL				= false;

if(strlen($idsql) >32 ){
	
	$idsql		= base64_decode($idsql);
	
	$nuevatabla	= "tmp_" . crc32($idsql);
	
	$res	= $xQL->setRawQuery("CREATE TEMPORARY TABLE IF NOT EXISTS $nuevatabla AS $idsql");
	if($res === false){
			exit ("No se pudo crear la tabla $nuevatabla con el SQL[ $idsql ]");
	} else {
		$tabla		= $nuevatabla;
		$isSQL		= true;
	}
	
}

$strcode			= ""; //$strcode	.=
$codeclass			= "";
$codemenu			= "";

$estructura			= "";
$estructura2		= "";

$txtReplace			= "";

$forma				= "";
$formahead			= "";
$receivers			= array();
$controles			= array();
$loader				= "";
//grid
$nombreFile2		= ($nombreFile == "") ? "$tabla.frm.php" : "$nombreFile.frm.php";
$nombreForm			= ($nombreForm == "") ? "frm$tabla" : $nombreForm;
$gridF				= "";
$CODE_CLASS_1		= "";
$CODE_CLASS_2		= "";
$CODE_CLASS_3		= "";

$FRM_CLASS_1		= "";
$FRM_CLASS_2		= "";


if($tabla != ""){
		$NTable			= $tabla;
		$msg			= "";
		$descriptors	= "";

		$all			= "";
		$pKey			= "";
		$pgrid			= "";
		$cgrid			= "";
		
		$estructura		= "/*\tORM: Tabla:\t$tabla\t-\tGenerado:\t[" . date("d/n/Y H:i") . "]\t*/\n";
		$estructura		.= "class c" . ucfirst($tabla). " {\n";
		$estructura2	.= "class cT" . ucfirst($tabla). " {\n\tfunction __construct(){}\n";
		
		$funciones		= "";
		$variables		= "";
		$variables2		= "";
		
		$primary		= "\tfunction __construct(\$campos = false){ if(is_array(\$campos)){ \$this->mCampos = \$campos; } }\n\tfunction get(){ return \"$tabla\";}\n";
		$nuevos			= "";
		$formahead		.= "\$xFRM\t= new cHForm(\"$nombreForm\", \"$nombreFile2?action=\$action\");\n";
			//ahora a grabar
				$sql_fields = "SHOW FIELDS IN $NTable";
				//$cnn		= mysqli_connect(MQL_SERVER, MQL_USER, MQL_PASS, MQL_DB);
				$rs_fields 	=  $xQL->getRecordset($sql_fields);//$cnn->query($sql_fields);
				$i			= 0;
				while($rowf = $rs_fields->fetch_array()){ //mysqli_fetch_array($rs_fields)
					$nombre				= $rowf[0];
					$valor 				= $rowf[4];
					$titulo				= ucfirst(str_replace("_", " ", $nombre));
					
					$atype 				= explode(" ", $rowf[1]);
					$atype 				= $atype[0];
					$atype 				= str_replace(")", "", $atype);
					$atype 				= str_replace("(", "@", $atype);
					$iType 				= explode("@", $atype);
					$field_type			= $iType[0];
					$field_long 		= (isset($iType[1]) ) ? $iType[1] : "0";
					$ctrl				= "text";
					$isKey				= false;
					switch ($field_type){
						case "enum":
							$valor 		= str_replace(",", "", $field_long);
							$valor 		= str_replace("''", "'", $valor);
							$valor 		= str_replace("'", "|", $valor);
							$field_long = 0;
							$ctrl 		= "select";
							break;
						default:
							//pocsionamiento de float enteros+fracciones + divisor
							if(strpos($field_long, ",") > 0){
								$field_long = explode(",", $field_long);
								$field_long = $field_long[0] + $field_long[0] + 1 ;
							}
							if($field_type == "int"||$field_type == "float"||$field_type == "bigint"){
								$ctrl		= ($field_type == "bigint" && stristr($nombre, "fecha") !== false) ? "date" : "number";
							}
							break;
					}
					$field_type				= strtoupper($field_type);
					
					//si el key es si
					if($rowf[3] == "PRI"){
						$primary	.= "\tfunction getKey(){ return \"$nombre\";}\n";
						$loader		= ($loader == "") ? $nombre : $loader;
						$isKey		= true;
						
					}
					if($field_long > 100||$field_type == "MEDIUMTEXT"||$field_type == "LONGTEXT"||$field_type == "TEXT"){
						$ctrl 		= "textarea";
					}
					
					$all			.= ($all == "") ? "\"" . $nombre . "\"" : ",\"$nombre\"";
					//$estructura		.= "";
					$variables		.= ($variables == "") ? "\"$nombre\" => array(\"N\"=>\"$nombre\",\"T\"=>\"$field_type\",\"V\"=>\"$valor\",\"L\"=>$field_long)" : ",\"$nombre\" => array(\"N\"=>\"$nombre\",\"T\"=>\"$field_type\",\"V\"=>\"$valor\",\"L\"=>$field_long)";
					$variables2		.= ($variables2 == "") ? "\"$nombre\" => array(\"N\"=>\"$nombre\",\"T\"=>\"$field_type\",\"V\"=>\"$valor\",\"L\"=>$field_long,\"S\"=>false)" : ",\"$nombre\" => array(\"N\"=>\"$nombre\",\"T\"=>\"$field_type\",\"V\"=>\"$valor\",\"L\"=>$field_long,\"S\"=>false)";
					
					
					$funciones		.= "\tfunction $nombre(\$v = false){ if(\$v !== false){\$this->mCampos[\"$nombre\"][\"V\"] =  \$v; } return new MQLCampo(\$this->mCampos[\"$nombre\"]);}\n";
					$nuevos			.= "\"$nombre\" => \"$valor\", ";
					
					
					
					if($create == true){
						//TODO: TErminar proceso de agregacion de estructura
					}
					$descriptors	.= ($descriptors == "") ? "\tpublic \$" . strtoupper($nombre) . " = \"$nombre\"; ": "public \$" . strtoupper($nombre) . " = \"$nombre\"; ";
					
					//INSERT INTO general_structure(tabla, campo, valor, tipo, longitud, descripcion, titulo, control, sql_select, orientacion, order_index, script_field, help_text, tab_num, css_class, input_events)
					$tipo_origen		= $mEquivalencias[  $field_type ];
					$idF				= "$nombre";
					$tituloF			= str_replace("_", " ", $nombre);
					$pgrid				.= "\t\"$nombre\" => \"$tituloF,true,$field_long\",\r\n";
					$cgrid				.= ($cgrid == "") ? $nombre : ",$nombre";
					$ictrl				= $ctrl;
					switch($tipo_origen){
						case "float":
								$valor				= "\$xTabla->$nombre" . "()->v()";
								$controles[$nombre] = "\$xFRM->OMoneda(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
								//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_FLOAT);\n";
								break;
						case "int":
								$valor				= "\$xTabla->$nombre" . "()->v()";
								$controles[$nombre] = "\$xFRM->OEntero(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
								//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_INT);\n";
								break;
						default:
								if($ctrl == "date"){
										$valor				= "\$xTabla->$nombre" . "()->v()";
										$controles[$nombre] = "\$xFRM->ODate(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
										//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_STRING);\n";
										$ctrl				= "text";
								} else if($ctrl == "select"){
										$arrV	= explode("|", $valor);
										$valor				= "\$xTabla->$nombre" . "()->v()";
										$opts 	= "";
										foreach($arrV as $k => $v){
												if(trim($v) != ""){
														$tval	= strtoupper( str_replace("_", " ", $v) );
														$opts	.= ($opts == "") ? "\"$v\"=>\"$tval\"" : ", \"$v\"=>\"$tval\"";
												}
										}
										$controles[$nombre] = "\$xFRM->OSelect(\"$idF\", $valor , \"TR." . strtoupper( $tituloF) . "\", array($opts));\n";
										//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_RAW);\n";
								} else if($ctrl == "textarea"){
										$valor				= "\$xTabla->$nombre" . "()->v()";
										$controles[$nombre] = "\$xFRM->OText(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
										$ctrl				= "textarea";
										//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_STRING);\n";
								} else {
										$valor				= "\$xTabla->$nombre" . "()->v()";
										$controles[$nombre] = "\$xFRM->OText_13(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
										//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_STRING);\n";
								}
								break;
						
					}
					if($isKey == true){
						$controles[$nombre] = "\$xFRM->OHidden(\"$idF\", $valor);\n";
						$gridF				.= "\$xHG" . $prefijoObj . "->addKey(\"$nombre\");\r\n";
					} else {
						$gridF		.= "\$xHG" . $prefijoObj . "->col(\"$nombre\", \"TR." . strtoupper( $tituloF) . "\", \"10%\");\r\n";
					}
					$ivalor			= "";
					$txtReplace		.= ($txtReplace == "") ? "('$tabla', '$nombre', '$ivalor', '$field_type', $field_long, 'TR.". strtoupper( $tituloF) ."', '$ictrl') \r\n": ",('$tabla', '$nombre', '$ivalor', '$field_type', $field_long, 'TR.". strtoupper( $tituloF) ."', '$ictrl') \r\n";
					//Purgar Datos de la Sucursal
					if($nombre == "sucursal" OR $nombre == "idusuario" OR $nombre == "eacp"){
						$controles[$nombre] = "\$xFRM->OHidden(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
					}
					
					$CODE_CLASS_3	.= "\$xTabla->$nombre" . "();\r\n";
					
					$i++;
				}
			@mysqli_free_result($rs_fields);
			
			$txtReplace					= "INSERT INTO general_structure(tabla, campo, valor, tipo, longitud, titulo, control) VALUES $txtReplace";
			//Agregar los campos
			$estructura					.= "\tprivate \$mCampos	= array($variables);\n";
			$estructura2				.= "\tprivate \$mCampos	= array($variables2);\n";
			$estructura2				.= $descriptors . "\r\n";
			
			$estructura					.= $descriptors . "\r\n";
			
			$estructura					.= $primary;
			
			
			$estructura					.= $funciones;
			$estructura					.= "\tfunction query(){ return new MQL(\$this->get(), \$this->mCampos, \$this->getKey());	}\n";
			$estructura					.= "\tfunction setData(\$datos){ \$mql	= new MQL(\$this->get(), \$this->mCampos, \$this->getKey()); \$this->mCampos = \$mql->setData(\$datos); }\n";
			$estructura					.= "\n}\n";
			
			$estructura2				.= "\n}\n";
			
			//header("Content-type: text/plain");
			//$strcode	.=  $estructura;
			$CODE_CLASS_1				= $estructura;
			
			//
			//$strcode	.= "\n\n\n\n\n";
			//$strcode	.=  $estructura2;
			//$strcode	.= "\n\n\n\n\n";
			$FRM_CLASS_1	.= "/* ===========\t\tFORMULARIO EDICION \t\t============*/\n";
			//ksort($controles);
			//ksort($receivers);
			//echo "\$clave\t\t= parametro(\"$loader\", null, MQL_INT);\n";
			//echo "\$xTabla\t\t= new c" . ucfirst($tabla). "();\nif(\$clave != null){\$xTabla->setData( \$xTabla->query()->initByID(\$clave));}\n\$xTabla->setData(\$_REQUEST);\n";
			$FRM_CLASS_1	.= "\$xTabla\t\t= new c" . ucfirst($tabla). "();\n\$xTabla->setData( \$xTabla->query()->initByID(\$clave));\n";
			//echo "if(\$action == MQL_ADD){\n} else if(\$action == MQL_MOD){}\n\n\n";
			//\$clave\t\t= parametro(\"id\", null, MQL_INT);
			$FRM_CLASS_1	.= "$formahead\r\n\$xFRM->setTitle(\$xHP->getTitle());\r\n\$xSel\t\t= new cHSelect();
			
";
/*
 if(\$clave == null){
	\$step		= MQL_ADD;
	\$clave		= \$xTabla->query()->getLastID() + 1;
	\$xTabla->$loader(\$clave);
} else {
	\$step		= MQL_MOD;
	if(\$clave != null){\$xTabla->setData( \$xTabla->query()->initByID(\$clave));}
} */
/*if(\$step == MQL_MOD){ \$xFRM->addGuardar(); } else { \$xFRM->addSubmit(); }
 \$clave 		= parametro(\$xTabla->getKey(), null, MQL_INT);

 if( (\$action == MQL_ADD OR \$action == MQL_MOD) AND (\$clave != null) ){
 \$xTabla->setData( \$xTabla->query()->initByID(\$clave));
 \$xTabla->setData(\$_REQUEST);

 if(\$action == MQL_ADD){
 \$xTabla->query()->insert()->save();
 } else {
 \$xTabla->query()->update()->save(\$clave);
 }
 \$xFRM->addAvisoRegistroOK();
 }*/

			/*foreach($receivers as $id => $cnt){
				echo $cnt;
			}*/
			
			
			
			$idgrid		= ($idgrid == "") ? $tabla : trim($idgrid);
			echo $forma;
			foreach($controles as $id => $cnt){
				$FRM_CLASS_1	.= $cnt;
			}
			$FRM_CLASS_1	.= "\n\$xFRM->addCRUD(\$xTabla->get(), true);\n\$xFRM->addCRUDSave(\$xTabla->get(), \$clave, true);\necho \$xFRM->get();";
	
	//echo "\n\n\n\n/* ===========\t\tGRID OLD\t\t============*/\n";
	//echo $pgrid;
	//echo "\n\$mGridSQL			= \"$cgrid\";";
			$FRM_CLASS_2	.= "/* ===========\t\tGRID JS\t\t============*/\n";
			$FRM_CLASS_2	.= "\n\$xHG" . $prefijoObj . "\t= new cHGrid(\"$idgrid\",\$xHP->getTitle());\r\n";
			$gen_sql1	= "\"SELECT * FROM `$tabla` LIMIT 0,100\"";
			if($isSQL == true){
				$gen_sql1	= "\"$idsql\"";
			}
			$FRM_CLASS_2	.= "\n\$xHG" . $prefijoObj . "->setSQL($gen_sql1);\r\n\$xHG" . $prefijoObj . "->addList();\r\n\$xHG" . $prefijoObj . "->setOrdenar();\r\n";	
			$FRM_CLASS_2	.= $gridF;
	
			$FRM_CLASS_2	.= "\n\$xHG" . $prefijoObj . "->OToolbar(\"TR.AGREGAR\", \"jsAdd" . $prefijoObj . "()\", \"grid/add.png\");\r\n\$xHG" . $prefijoObj . "->OButton(\"TR.EDITAR\", \"jsEdit" . $prefijoObj . "('+ data.record.$loader +')\", \"edit.png\");\r\n\$xHG" . $prefijoObj . "->OButton(\"TR.ELIMINAR\", \"jsDel" . $prefijoObj . "('+ data.record.$loader +')\", \"delete.png\");\r\n\$xHG" . $prefijoObj . "->OButton(\"TR.BAJA\", \"jsDeact" . $prefijoObj . "('+ data.record.$loader +')\", \"undone.png\");
\$xFRM->addHElem(\"<div id='". $idgrid . "'></div>\");\r\n\$xFRM->addJsCode( \$xHG" . $prefijoObj . "->getJs(true) );\r\necho \$xFRM->get();\r\n?>
\r\n<script>
var xG	= new Gen();
function jsEdit" . $prefijoObj . "(id){
	xG.w({url:\"../$idruta/$nombreFile.edit.frm.php?clave=\" + id, tiny:true, callback: jsLG$idgrid});
}
function jsAdd" . $prefijoObj . "(){
	xG.w({url:\"../$idruta/$nombreFile.new.frm.php?\", tiny:true, callback: jsLG$idgrid});
}
function jsDel" . $prefijoObj . "(id){
	xG.rmRecord({tabla:\"$tabla\", id:id, callback:jsLG$idgrid });
}
function jsDeact" . $prefijoObj . "(id){
	xG.recordInActive({tabla:\"$tabla\", id:id, callback:jsLG$idgrid, preguntar:true });
}
</script>
<?php
	";
	
			$codemenu	.= "/* ===========\t\tStructure\t\t============*/\n\r\n\r\n";
			$codemenu	.= $txtReplace;
	
	//Menu
	$mmnu	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ($menuid, $menuparent, '$idtit', '$idruta/$nombreFile2', '$idtit', 'fa-list-alt', 'command', '$menuid', '$menuid', 'true') ";
	
	$codemenu	.= "\n\n\n\n/* ===========\t\tMenu\t\t============*/\n\r\n\r\n";
	$codemenu	.= $mmnu;
}





$xFRM->addSeccion("idfrmphp-0", "Class Code");
$code0	= highlight_string($CODE_CLASS_1, true);
$xFRM->addHElem($code0);
$xFRM->endSeccion();


/*$xFRM->addSeccion("idtphp2", "Clase 2");
$xFRM->addHElem("<code id='php-code-2' class=\"php\">" . nl2br($CODE_CLASS_2) . "</code>");
$xFRM->endSeccion();*/



$xFRM->addSeccion("idfrmphp-1", "Form Class 1");
$code1	= highlight_string($FRM_CLASS_1, true);
$xFRM->addHElem($code1);
$xFRM->endSeccion();

$xFRM->addSeccion("idfrmphp-2", "Form Class 2");
$code2	= highlight_string($FRM_CLASS_2, true);
$xFRM->addHElem($code2);
$xFRM->endSeccion();


$xFRM->addSeccion("idfrmphp-2b", "Set Form Class 3");
$code3	= highlight_string($CODE_CLASS_3, true);
$xFRM->addHElem($code3);
$xFRM->endSeccion();

$xFRM->addSeccion("idtsql", "sql");
$xFRM->addHElem("<code id='sql-code' class=\"sql\">" . nl2br($codemenu) . "</code>");
$xFRM->endSeccion();


echo $xFRM->get();





?>
<!-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script> -->

<script>
/*$(document).ready(function() {
	  $('code').each(function(i, block) {
	    hljs.highlightBlock(block);
	  });
});*/
</script>
<style>
.fieldform code span { text-align: left; }
.formoid-default .formoid-section { text-align: left; }
</style>
<?php


$xHP->fin();
//Ahora de puro SQL
?>

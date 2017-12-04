<?php
include_once("../core/core.config.inc.php");
include_once("../core/core.db.inc.php");
include_once("../core/core.init.inc.php");
$idgrid		= parametro("idgrid", "", MQL_RAW);
$tabla		= parametro("tabla", "", MQL_RAW);
$nombreForm	= parametro("nombreforma", "", MQL_RAW);
$nombreFile	= parametro("nombrefile", "", MQL_RAW);
$menuid		= parametro("menu",0, MQL_INT);
$menuparent	= parametro("menuparent",9999, MQL_INT);
$idtit		= parametro("title", "", MQL_RAW);
$idruta		= parametro("ruta", "", MQL_RAW);
$menuid		= ($menuid <=0 ) ? "NULL" : $menuid;
/*	ORM: Tabla:	general_structure	-	Generado:	[08/1/2014 10:36]	*/
/*class cGeneral_structure {
	private $mCampos	= array(
			"index_struct" => array("N"=>"index_struct","T"=>"INT","V"=>"","L"=>11),
			"tabla" => array("N"=>"tabla","T"=>"VARCHAR","V"=>"","L"=>100),
			"campo" => array("N"=>"campo","T"=>"VARCHAR","V"=>"","L"=>100),
			"valor" => array("N"=>"valor","T"=>"VARCHAR","V"=>"","L"=>250),
			"tipo" => array("N"=>"tipo","T"=>"VARCHAR","V"=>"","L"=>20),
			"longitud" => array("N"=>"longitud","T"=>"INT","V"=>"0","L"=>4),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>200),
			"titulo" => array("N"=>"titulo","T"=>"VARCHAR","V"=>"","L"=>100),
			"control" => array("N"=>"control","T"=>"ENUM","V"=>"|text|textarea|select|hidden|","L"=>0),
			"sql_select" => array("N"=>"sql_select","T"=>"TINYTEXT","V"=>"","L"=>0),
			"orientacion" => array("N"=>"orientacion","T"=>"ENUM","V"=>"|izquierda|derecha|","L"=>0),
			"order_index" => array("N"=>"order_index","T"=>"INT","V"=>"0","L"=>10),
			"script_field" => array("N"=>"script_field","T"=>"TEXT","V"=>"","L"=>0),
			"help_text" => array("N"=>"help_text","T"=>"TEXT","V"=>"","L"=>0),
			"tab_num" => array("N"=>"tab_num","T"=>"VARCHAR","V"=>"","L"=>25),
			"css_class" => array("N"=>"css_class","T"=>"VARCHAR","V"=>"normalfield","L"=>20),
			"input_events" => array("N"=>"input_events","T"=>"VARCHAR","V"=>"","L"=>120),

	);
	function __construct(){}
	function get(){ return "general_structure";}
	function getKey(){ return "index_struct";}
	function index_struct($v=false){
		if($v!==false){$this->mCampos["index_struct"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["index_struct"]);
	}
	function tabla($v=false){
		if($v!==false){$this->mCampos["tabla"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tabla"]);
	}
	function campo($v=false){
		if($v!==false){$this->mCampos["campo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["campo"]);
	}
	function valor($v=false){
		if($v!==false){$this->mCampos["valor"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["valor"]);
	}
	function tipo($v=false){
		if($v!==false){$this->mCampos["tipo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo"]);
	}
	function longitud($v=false){
		if($v!==false){$this->mCampos["longitud"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["longitud"]);
	}
	function descripcion($v=false){
		if($v!==false){$this->mCampos["descripcion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["descripcion"]);
	}
	function titulo($v=false){
		if($v!==false){$this->mCampos["titulo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["titulo"]);
	}
	function control($v=false){
		if($v!==false){$this->mCampos["control"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["control"]);
	}
	function sql_select($v=false){
		if($v!==false){$this->mCampos["sql_select"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sql_select"]);
	}
	function orientacion($v=false){
		if($v!==false){$this->mCampos["orientacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["orientacion"]);
	}
	function order_index($v=false){
		if($v!==false){$this->mCampos["order_index"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["order_index"]);
	}
	function script_field($v=false){
		if($v!==false){$this->mCampos["script_field"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["script_field"]);
	}
	function help_text($v=false){
		if($v!==false){$this->mCampos["help_text"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["help_text"]);
	}
	function tab_num($v=false){
		if($v!==false){$this->mCampos["tab_num"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tab_num"]);
	}
	function css_class($v=false){
		if($v!==false){$this->mCampos["css_class"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["css_class"]);
	}
	function input_events($v=false){
		if($v!==false){$this->mCampos["input_events"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["input_events"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}*/


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
				$cnn		= mysqli_connect(MQL_SERVER, MQL_USER, MQL_PASS, MQL_DB);
				$rs_fields 	= $cnn->query($sql_fields);
				$i			= 0;
				while($rowf = mysqli_fetch_array($rs_fields)){
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
					$descriptors	.= "\tpublic \$" . strtoupper($nombre) . "\t= \"$nombre\";\r\n";
					
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
								$controles[$nombre] = "\$xFRM->OMoneda(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
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
										$controles[$nombre] = "\$xFRM->OTextArea(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
										$ctrl				= "textarea";
										//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_STRING);\n";
								} else {
										$valor				= "\$xTabla->$nombre" . "()->v()";
										$controles[$nombre] = "\$xFRM->OText(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
										//$receivers[$nombre]	= "\$$idF\t=parametro(\"$idF\", \"$valor\", MQL_STRING);\n";
								}
								break;
						
					}
					if($isKey == true){
						$controles[$nombre] = "\$xFRM->OHidden(\"$idF\", $valor);\n";
						$gridF				.= "\$xHG->addKey(\"$nombre\");\r\n";
					} else {
						$gridF		.= "\$xHG->col(\"$nombre\", \"TR." . strtoupper( $tituloF) . "\", \"10%\");\r\n";
					}
					$ivalor			= "";
					$txtReplace		.= ($txtReplace == "") ? "('$tabla', '$nombre', '$ivalor', '$field_type', $field_long, 'TR.". strtoupper( $tituloF) ."', '$ictrl') \r\n": ",('$tabla', '$nombre', '$ivalor', '$field_type', $field_long, 'TR.". strtoupper( $tituloF) ."', '$ictrl') \r\n";
					//Purgar Datos de la Sucursal
					if($nombre == "sucursal" OR $nombre == "idusuario" OR $nombre == "eacp"){
						$controles[$nombre] = "\$xFRM->OHidden(\"$idF\", $valor, \"TR." . strtoupper( $tituloF) . "\");\n";
					}
					$i++;
				}
			@mysqli_free_result($rs_fields);
			
			$txtReplace					= "INSERT INTO general_structure(tabla, campo, valor, tipo, longitud, titulo, control) VALUES $txtReplace";
			//Agregar los campos
			$estructura					.= "\tprivate \$mCampos	= array($variables);\n";
			$estructura2				.= "\tprivate \$mCampos	= array($variables2);\n";
			$estructura2				.= $descriptors;
			
			$estructura					.= $descriptors;
			
			$estructura					.= $primary;
			
			
			$estructura					.= $funciones;
			$estructura					.= "\tfunction query(){ return new MQL(\$this->get(), \$this->mCampos, \$this->getKey());	}\n";
			$estructura					.= "\tfunction setData(\$datos){ \$mql	= new MQL(\$this->get(), \$this->mCampos, \$this->getKey()); \$this->mCampos = \$mql->setData(\$datos); }\n";
			$estructura					.= "\n}\n";
			
			$estructura2				.= "\n}\n";
			
			header("Content-type: text/plain");
			echo  $estructura;
			echo "\n\n\n\n\n";
			echo  $estructura2;
			echo "\n\n\n\n\n";
			echo "\n\n\n\n/* ===========\t\tFORMULARIO EDICION \t\t============*/\n";
			//ksort($controles);
			//ksort($receivers);
			//echo "\$clave\t\t= parametro(\"$loader\", null, MQL_INT);\n";
			//echo "\$xTabla\t\t= new c" . ucfirst($tabla). "();\nif(\$clave != null){\$xTabla->setData( \$xTabla->query()->initByID(\$clave));}\n\$xTabla->setData(\$_REQUEST);\n";
			echo "\$xTabla\t\t= new c" . ucfirst($tabla). "();\n\$xTabla->setData( \$xTabla->query()->initByID(\$clave));\n";
			//echo "if(\$action == MQL_ADD){\n} else if(\$action == MQL_MOD){}\n\n\n";
			//\$clave\t\t= parametro(\"id\", null, MQL_INT);
			echo "$formahead
			\$xFRM->setTitle(\$xHP->getTitle());
			\$xSel\t\t= new cHSelect();
			
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
$idgrid		= ($idgrid == "") ? $tabla : $idgrid;
			echo $forma;
			foreach($controles as $id => $cnt){
				echo $cnt;
			}
	echo "\n\$xFRM->addCRUD(\$xTabla->get(), true);\n\$xFRM->addCRUDSave(\$xTabla->get(), \$clave, true);\necho \$xFRM->get();";
	
	//echo "\n\n\n\n/* ===========\t\tGRID OLD\t\t============*/\n";
	//echo $pgrid;
	//echo "\n\$mGridSQL			= \"$cgrid\";";
	echo "\n\n\n\n/* ===========\t\tGRID JS\t\t============*/\n";
	echo "\n\$xHG\t= new cHGrid(\"$idgrid\",\$xHP->getTitle());\r\n";
	echo "\n\$xHG->setSQL(\"SELECT * FROM `$tabla` LIMIT 0,100\");\r\n\$xHG->addList();\r\n";	
	echo $gridF;
	
	echo "\n\$xHG->OToolbar(\"TR.AGREGAR\", \"jsAdd()\", \"grid/add.png\");\r\n\$xHG->OButton(\"TR.EDITAR\", \"jsEdit('+ data.record.$loader +')\", \"edit.png\");\r\n\$xHG->OButton(\"TR.ELIMINAR\", \"jsDel('+ data.record.$loader +')\", \"delete.png\");
\$xFRM->addHElem(\"<div id='$idgrid'></div>\");\r\n\$xFRM->addJsCode( \$xHG->getJs(true) );\r\necho \$xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:\"../$idruta/$nombreFile.edit.frm.php?clave=\" + id, tiny:true, callback: jsLG$idgrid});
}
function jsAdd(){
	xG.w({url:\"../$idruta/$nombreFile.new.frm.php?\", tiny:true, callback: jsLG$idgrid});
}
function jsDel(id){
	xG.rmRecord({tabla:\"$tabla\", id:id, callback:jsLG$idgrid});
}
</script>
<?php
	";
	
	echo "\n\n\n\n/* ===========\t\tStructure\t\t============*/\n\r\n\r\n";
	echo $txtReplace;
	
	//Menu
	$mmnu	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ($menuid, $menuparent, '$idtit', '$idruta/$nombreFile2', '$idtit', 'fa-money', 'command', '$menuid', '$menuid', 'true') ";
	
	echo "\n\n\n\n/* ===========\t\tMenu\t\t============*/\n\r\n\r\n";
	echo $mmnu;
}
//Ahora de puro SQL
?>

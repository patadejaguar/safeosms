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
$xHP		= new cHPage("", HP_FORM);

$oficial 	= elusuario($iduser);
//$wonner		= $iduser;
$tabla 		= parametro("t", "", MQL_RAW);// ( isset($_GET["t"]) ) ? $_GET["t"] : "";
$tabla 		= parametro("tabla", $tabla, MQL_RAW);
$filtro 	= ( isset($_GET["f"]) ) ? $_GET["f"] : "";
$comando	= ( isset($_GET["cmd"]) ) ? $_GET["cmd"] : SYS_DEFAULT;

$clave		= parametro("id", null, MQL_RAW);
$clave		= parametro("clave", $clave, MQL_RAW);
//necesarios tabla clave
$encode		= (isset($_REQUEST["enc"])) ? $_REQUEST["enc"] : false;
$options	= (isset($_REQUEST["opts"])) ? $_REQUEST["opts"] : "";

$frm		= "frm8db7028bdcdf054882ab54f644a9d36b";
$xRef		= new MQL();
$xDBTip		= $xRef->getTipos();
$xLng		= new cLang();

if($comando == SYS_DEFAULT){
	echo $xHP->getHeader(true);
	echo $xHP->setBodyinit("initComponents()");
	$jsVars			= "";
	$location		= "$frm.php?t=$tabla&f=$filtro&enc=$encode&cmd=$comando";
	$xTxt			= new cHText();
	$xBtn			= new cHButton();
	$original		= "";
	if($tabla != "" AND ($filtro != "" OR $clave != null)){
		$xFRM		= new cHForm($frm, $location, $frm, SYS_GET );
		//$DFiltro	= explode("=", $filtro);
		$conTitulo	= false;
		$xData		= new cSAFETabla($tabla);
		$mObj		= $xData->obj();
		
		if($mObj == null){
			$xFRM->addAviso(MSG_NO_PARAM_VALID);
			$xFRM->addHTML("<script>var g = new Gen(); g.close(); </script>");
		} else {
			$filtro		= ($filtro == "") ? $mObj->getKey() . "=" . $clave . "" : $filtro;
			$data		= $mObj->query()->getRow(" $filtro ");
			$Boone		= new cGeneral_structure();
			$datos		= $Boone->query()->select()->exec(" `general_structure`.`tabla` ='$tabla' ", " `general_structure`.`order_index`, `general_structure`.`tipo`");
			//$original	= json_encode($datos);
			$hidden		= "";
			//var_dump($datos);
			foreach($datos as $clave => $valores){
				$Boone->setData($valores);
				$id	= "_" . $Boone->campo()->v();
				$valor	= $data[$Boone->campo()->v()];
				$titulo	= $xLng->getT( $Boone->titulo()->v() );
				$equiva	= $xDBTip[ strtoupper($Boone->tipo()->v()) ];
				$control= $Boone->control()->v();
				$xTxt->setClearEvents();
				if( $Boone->input_events()->v() != "" ){
					$scr	= explode(";", $Boone->input_events()->v());
					foreach($scr as $k => $v){
						if($v != ""){
							$props	= explode("=", $v);
							//var_dump($props);
							//echo $props[0] ."===". $props[1] . "\n";
							$xTxt->addEvent($props[1], $props[0]);
						}
					}
				}
				$proc	= true;
				$jsVars	.= "\t$(\"#$id\").val();\n";
				//options RAW
				if($options == "raw"){
					if($control == "hidden"){
						if( trim($Boone->sql_select()->v()) != "" AND trim($Boone->sql_select()->v()) != "NA" ){
							$control	= "select";
							
						} else {
							$control	= "text";
						}
					}
				}
				if($Boone->valor()->v() == "primary_key"){
					$hidden		.= "<input type = \"hidden\" value=\"$valor\" name=\"$id\" id=\"$id\" />";
					$proc		= false;
					if($conTitulo == false){
						$xFRM->setTitle("$titulo : $valor");
						$conTitulo	= true;
					}
				}
				if($control == "hidden" AND $proc == true){
					$hidden	.= "<input type = \"hidden\" value=\"$valor\" name=\"$id\" id=\"$id\" />";
					$proc	= false;
				}
				if($control == "select" AND $proc == true){
					$xHSel		= new cHSelect($id);
					if($Boone->tipo()->v() == "enum"){
						$div	= "|";
						
						$Bdata	= explode($div, $Boone->valor()->v());
						$Cdata	= array();
						foreach($Bdata as $clave => $valor ){
							if( trim($valor) != ""){
								if(strpos($valor, "@") !== false){
									$DD		= explode("@", $valor); 
									$Cdata[$DD[0]]	= strtoupper($DD[1]);
								} else {
									$Cdata[$valor]	= strtoupper($valor);
								}
							}
						}
						$xHSel->addOptions( $Cdata ); unset($Bdata);
					} else {
						$xHSel->setSQL( $Boone->sql_select()->v() );
					}
					$xHSel->setEnclose(true);
					$xFRM->addHElem($xHSel->get($id, $titulo, $valor));
					$proc	= false;
				}
				if( $proc == true ){
	
					//controles normales
					if($Boone->tipo()->v() == "date"){
						$xHDate	= new cHDate($Boone->order_index()->v());
						$xHDate->setID($id);
						$xFRM->addHElem($xHDate->get($titulo, $valor));
					} else {
						//moneda
						switch( $equiva ){
							case "float":
								$xFRM->addHElem($xTxt->getDeMoneda($id, $titulo, $valor ));
							break;
							case "int":
								$xFRM->addHElem($xTxt->getDeMoneda($id, $titulo, $valor ));
								break;
							default :
								$xFRM->addHElem($xTxt->getNormal($id, $valor, $titulo));
								break;
						}
					}
					//numero
					
				}
			}
			$xFRM->addSubmit("", "jsGuardarCambios()");
			$xFRM->addToolbar($xBtn->getBasic("Edicion avanzada", "jsGoAvanzada()", "ejecutar", "idraw", false ) );
			
			$xFRM->addAviso("");
			//$xFRM->addFootElement( $Boone->query()->getLog() );
			$xFRM->addFootElement($hidden);
			
		}
		echo $xFRM->get();
	} else {
		//print close error
	}
	echo "<script>var WGen	= new Gen();
	$jsVars
	function jsGuardarCambios(){
		WGen.pajax({
			url : '$frm.php',
			form : $('#$frm'),
			extra : '&t=$tabla&f=$filtro&enc=$encode&cmd=" . SYS_AUTOMATICO . "',
			callback : function(o){ if(o.resultado == true){ alert('Registro Guardado!'); WGen.close(); } else { alert('Error al Guardar!\\n' + o.messages); } } ,
			result : 'json'
		});
	}
	function jsGoAvanzada(){ window.location ='$location&opts=raw'; }
	function initComponents(){  }
	</script>";
	?>
	<script>
		/* Funciones de Operaciones */
		function jsCambiarValores(obj) {
			$("#_afectacion_cobranza").val(obj.value);
			$("#_afectacion_estadistica").val(obj.value);
			$("#_afectacion_contable").val(obj.value);
		}
	</script>
	<?php
	echo $xHP->setBodyEnd();
	echo $xHP->end();
} else {
	header('Content-Type: application/json');
	//json
	$msg		= "";
	if($comando == SYS_AUTOMATICO){
		$xF	= new cFecha();
		if($tabla != "" AND $filtro != ""){
			$rs		= false;
			$vars		= $_REQUEST;
			//var_dump($vars);
			//$DFiltro	= explode("=", $filtro);
			$xData		= new cSAFETabla($tabla);
			$q		= $xData->obj()->query();
			$datos		= $q->getRow($filtro);
			$q->setData($datos);
			$campos		= $q->getCampos();
			$actualizar	= false;

			$sqlor		= "SELECT * FROM $tabla WHERE $filtro";
			$filas		= obten_filas($sqlor);
			$cadena		= json_encode($filas);
			$xErr		= new cCoreLog(); $xErr->add("$oficial Editar $tabla como $filtro.\n ORIGINAL:\n $cadena"); $xErr->guardar( $xErr->OCat()->EDICION_RAW );
						
			foreach($vars as $k => $v){
				$campo			= substr($k, 1);
				if(isset($campos[$campo])){
					if( $campos[$campo]["V"]  == $v){
						$msg		.= "OMITIR_ACTUALIZAR: $campo, ";
					} else {
					if($campos[$campo]["T"] == "DATE"){
						$v	= $xF->getFechaISO($v);
					}
					$msg		.= "ACTUALIZAR : $campo DE " .$campos[$campo]["V"] . " A $v,";
					$campos[$campo]["V"]	= $v;
					//echo "" . $campos[$campo]["V"] . " === "  . $v . "\n";
					$actualizar	= true;
					}
				} else {
					$msg		.= "OMITIR: $campo,";
					//echo "ERROR : $campo;";
				}
			}
			
			$q->setCampos($campos);
			//$q->update()->get($filtro);
			if($actualizar == true){
				$qry	= $q->update();
				$rs	= $qry->save($filtro);
				if(MODO_DEBUG == true){ $msg	.=	$qry->getMessages(OUT_TXT); }
			} else {
				$msg	.= "ACTUALIZACION OMITIDA";
			}
			
			echo json_encode(array("messages" => $msg, "resultado" => $rs));
			//var_dump($campos);
		}
	} else {
		echo json_encode(array("messages" => "COMANDO INVALIDO", "resultado" => false));
	}
}
?>
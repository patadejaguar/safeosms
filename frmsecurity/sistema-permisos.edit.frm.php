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
$xHP		= new cHPage("TR.EDITAR PERMISOS", HP_FORM);
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

$xHP->addJTagSupport();


$xHP->init();

$jsTags		= "";

$xPerms		= new cSystemPermissions();
$xNiv		= new cSystemPerfiles();


$arrN		= array();
$rs			= $xNiv->initNiveles();
foreach ($rs as $rw){
	$id		= $rw["idgeneral_niveles"];
	$lbl	= $rw["descripcion_del_nivel"];
	$arrN[$id]	= $lbl;
	$jsTags	.= ($jsTags == "") ?  " \"$lbl -N- $id\"" : ", \"$lbl -N- $id\"";
}


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cSistema_permisos();
$xTabla->setData( $xTabla->query()->initByID($clave));

$xFRM	= new cHForm("frmpermisos", "sistema-permisos.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xSel->addOptions( array("FORM" => "FORMULARIO", "TABLE" => "TABLA" ) );	

$xFRM->OHidden("idsistema_permisos", $xTabla->idsistema_permisos()->v());
$xFRM->ODisabled("accion", $xTabla->accion()->v(), "TR.ACCION");




$arrDen 	= $xPerms->setTraducir($xTabla->denegado()->v());
$lsItems	= "";



foreach ($arrDen as $idx => $nn){
	$idx	= $xNiv->getTraducir($nn);
	
	if(isset($arrN[$idx])){
		$lsItems	.= "<li>" . $arrN[$idx] . " -N- $idx</li>";
	}
}

if($clave > 0){
	$xFRM->ODisabled_13("tipo_objeto", $xTabla->tipo_objeto()->v(), "TR.TIPO");
	$xFRM->ODisabled_13("nombre_objeto", $xTabla->nombre_objeto()->v(), "TR.NOMBRE");
} else {
	$xFRM->OText_13("tipo_objeto", $xTabla->tipo_objeto()->v(), "TR.TIPO");
	$xFRM->OText_13("nombre_objeto", $xTabla->nombre_objeto()->v(), "TR.NOMBRE");
}



$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.DESCRIPCION");
$xFRM->OHidden("denegado", $xTabla->denegado()->v(), "TR.DENEGADO");

$xFRM->addHElem("<div class=\"solo\"><label>DENEGADO</label><ul id=\"tags\">$lsItems</ul></div>");

$xFRM->addJsInit("jsTags();");

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();

?>
<script>

function jsTags(){
	
	$("#tags").tagit({
	    availableTags: [<?php echo $jsTags; ?>],
	    afterTagAdded: function(event, ui) {
	        // do something special
	        var mtag	= ui.tag[0].innerText;
	        var DTag	= String(mtag).split(" -N- ");
	    	var idnivel	= entero(DTag[1]);
	    	if(idnivel > 0){
		    	var oPerm	= $("#denegado").val();
		    	var dPerm	= String(idnivel).concat("@rw");
		    	setLog("Agregar " + dPerm);
		    	//Eliminar si existe
		    	if(oPerm == ""){
		    		$("#denegado").val(dPerm);
		    	} else {
			    	if(dPerm == oPerm){
				    	//No hacer nada
			    	} else {
				    	//
				    	var nTag	= String(",").concat(dPerm);
				    	var nTags	= String(oPerm).replace(/nTag/, "");
				    	nTags		= String(nTags).concat(nTag);
				    	$("#denegado").val(nTags);
			    	}
		    	}
	    	}
	    },
		afterTagRemoved : function(event, ui){
	        // do something special
	        var mtag	= ui.tag[0].innerText;
	        var DTag	= String(mtag).split(" -N- ");
	    	var idnivel	= entero(DTag[1]);
	    	var oPerm	= $("#denegado").val();
	    	var dPerm	= String(idnivel).concat("@rw");
	    	
	    	if(idnivel > 0){
		    	var txt	= String(oPerm);
	    		if(dPerm == oPerm){
		    		var txt		= "";//eliminar si solo hay un individuo
	    		} else {
	    			var txt		= "";
	    			//console.log(oPerm);
		    		var DPerms	= String(oPerm).split(",");
					for (idx in DPerms){
						var str	= DPerms[idx];
						//console.log(str);
						if(str == dPerm){
							console.log(dPerm + "-- -- " + str);
						} else if (str === ""){
							console.log("Vacio:" + str);
						} else {
							if(txt === ""){
								txt = str;
							} else {
								txt = String(txt).concat("," , str);
							}
						}
					}

			    			    				    		
	    		}
		    	//
	    		$("#denegado").val(txt);
	    		setLog("Permisos en " + txt);
	    	}			
		} 
	});
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
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

$xPage		= new cHPage(HP_FORM, 	 "Admitir Socios");
echo $xPage->getHeader();
?>
<body>
<fieldset>
	<legend>SOCIO(S) NO ADMITIDO(S)</legend>
<form name="frmAdmitir" action="frmadmitirsocios.php" method="post">


<?php

	$sqlSNA = "SELECT
	`socios_general`.`codigo`,
	`socios_general`.`nombrecompleto` AS 'nombre',
	`socios_general`.`apellidopaterno` AS 'apellido_paterno',
	`socios_general`.`apellidomaterno` AS 'apellido_materno',
	`socios_general`.`fechaentrevista` AS 'fecha_de_entrevista',
	`socios_general`.`sucursal` 
FROM
	`socios_general` `socios_general` 
WHERE
	(`socios_general`.`estatusactual` =99)
ORDER BY
	`socios_general`.`fechaentrevista` DESC
LIMIT 0,20	";

	$tSoc = new cTabla($sqlSNA);
	$tSoc->setWidth();
	$tSoc->addEspTool("<input type=\"checkbox\"  id=\"chk" . STD_LITERAL_DIVISOR . "_REPLACE_ID_\" />");
	$tSoc->setTdClassByType();	
	$tSoc->Show("", false);
?>
<input type="button" name="sendmme" value="GUARDAR AUTORIZACION" onClick="jsSetAdmision();" />
</form>
</fieldset>

</body>
<script language='javascript' src='../js/jsrsClient.js'></script>
<script  >

var Frm 					= document.frmAdmitir;
var jsrCommon				= "../js/general.common.js.php";
var divLiteral				= "<?php echo STD_LITERAL_DIVISOR; ?>";
var jsrsContextMaxPool 		= 300;

function jsSetAdmision(){
	  	var isLims 			= Frm.elements.length - 1;

  		for(i=0; i<=isLims; i++){
			var mTyp 	= Frm.elements[i].getAttribute("type");
			var mID 	= Frm.elements[i].getAttribute("id");
			var mVal	= Frm.elements[i].checked;

			//Verificar si es mayor a cero o no nulo
			if ( (mID!=null) && (mID.indexOf("chk@")!= -1) && (mTyp == "checkbox") && (mVal == true) ){
				//Despedazar el ID para obtener el denominador comun
				var aID	= mID.split(divLiteral);
				jsrsExecute(jsrCommon, jsEchoMsg, "Common_52d87bf9711abf3a850de1dc12a14895", aID[1] );
  			}

  		}
	Frm.submit();
}
function  jsEchoMsg(){
	
}
</script>
</html>

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

$xHP			= new cHPage("TR.Listado de Creditos");
$xQL			= new MQL();

$persona 		= (isset($_GET["i"])) ? $_GET["i"] : DEFAULT_SOCIO;
$persona 		= (isset($_GET["socio"])) ? $_GET["socio"] : $persona;
$persona 		= (isset($_GET["persona"])) ? $_GET["persona"] : $persona;
$persona 		= (isset($_GET["socio"])) ? $_GET["socio"] : $persona;

$f 				= (isset($_GET["f"])) ? $_GET["f"] : false;
$ctrl 			= (isset($_GET["control"])) ? $_GET["control"] : "idsolicitud";

$a 				= (isset($_GET["a"])) ? $_GET["a"] : "";
$estado			= parametro("estado", SYS_TODAS, MQL_INT); $estado = parametro("tipo", $estado, MQL_INT);

$OtherEvent		= (isset($_GET["ev"])) ? $_GET["ev"]: "";	//Otro Evento Desatado
$tiny 			= (isset($_GET["tinybox"])) ? true : false;

$EventoCred		= parametro("evento", SYS_NINGUNO);
$EventoCred		= strtolower($EventoCred);
$nextstep		= parametro("next", "", MQL_RAW);

$slimit 	= "";
if($a == ""){
	$slimit = " LIMIT 0,20";
}

//echo $xHP->getHeader();

$lsql		= new cSQLListas();
$xEvt		= new cCreditosEventos();
$xCache		= new cCache();
$arr		= array();
switch($EventoCred){
	default:
		$sql		= $lsql->getListadoDeCreditos($persona, true, $estado, false, "", true);
		$arr[7]		= "saldo";
		break;
	case $xEvt->PAGO:
		$mSQL		= "UPDATE `creditos_solicitud`, `creditos_letras_pendientes_rt` SET `creditos_solicitud`.`fecha_de_proximo_pago`=`creditos_letras_pendientes_rt`.`fecha_de_pago` 
						WHERE `creditos_solicitud`.`numero_solicitud`=`creditos_letras_pendientes_rt`.`docto_afectado` AND `creditos_solicitud`.`numero_socio`=$persona 
						AND `creditos_letras_pendientes_rt`.`socio_afectado`=$persona AND `creditos_solicitud`.`saldo_actual`> " . TOLERANCIA_SALDOS;
		//$xQL->setRawQuery($mSQL);
		$sql		= $lsql->getListadoDeCreditosParaPagos($persona, false, $estado, false, "", true);
		break;
}


$xFRM		= new cHForm("frmlistacreditos");
$xFRM->setTitle($xHP->getTitle());

if($nextstep !== ""){
	if($persona> DEFAULT_SOCIO){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
		}
	}
}

$xT			= new cTabla($sql);
$xT->setFootSum($arr);
$xT->setEventKey("setCredito");

$xFRM->addHTML( $xT->Show($xHP->getTitle()) );
//$xFRM->addHTML("<code>$sql</code>");
$xFRM->addSubmit("TR.aceptar", "jsEnd()");

//echo $xHP->setBodyinit();
$xHP->init();

echo $xFRM->get();


?>
<script>
var msrc	= null;
var next	= "<?php echo $nextstep; ?>";
var xG		= new Gen();

function setCredito(id){
	var mopts	= {};
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
	if(next == Configuracion.rutas.panel){
		xG.go({url: "../frmcreditos/creditos.panel.frm.php?credito=" + id});
		return false;
	}
<?php
		
			echo "
			if(msrc == null){} else {
				if(msrc.getElementById('$ctrl')){
					var rmt	= msrc.getElementById('$ctrl');
					rmt.value 	= id;
					rmt.focus();
					rmt.select();
					if(typeof msrc.jsGetDescCredito != \"undefined\"){ msrc.jsGetDescCredito();	}
				}
			}";
			if( $OtherEvent != ""){
					echo "if(msrc == null){} else { msrc.$OtherEvent;}";
			} 
		
?>
	xG.close();
}
</script>
<?php $xHP->fin(); ?>

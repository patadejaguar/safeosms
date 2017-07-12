<?php
/**
 * Modulo
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
$xHP		= new cHPage("", HP_SERVICE);
//$xQL		= new MQL();
//$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");
$letra			= parametro("letra", false, MQL_INT);

$xSVC			= new MQLService($action, "");

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";


header('Content-type: application/json');

/*
$options = array(
  'http' => array(
    'method'  => 'POST',
    'content' => json_encode( $data ),
    'header'=>  "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n"
    )
);

$context  = stream_context_create( $options );
$result = file_get_contents( $url, false, $context );
$response = json_decode( $result );
*/

/*
INSERT INTO `risks` (
`id`, `status`, `subject`, `reference_id`, `regulation`, `control_number`, `location`, `source`, 
`category`, `team`, `technology`, `owner`, `manager`, `assessment`, `notes`, 
`submission_date`, `last_update`, `review_date`, `mitigation_id`, `mgmt_review`, `project_id`, `close_id`, `submitted_by`) VALUES

(2,	'New',	'Riesgo de Prueba',	'999',	2,	'1451',	1,	1,	5,	5,	1,	1,	1,	'Prueba',	'Nada que ver',	'2017-04-01 03:59:48',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	0,	0,	0,	NULL,	1);

INSERT INTO `risk_scoring_history` (`id`, `risk_id`, `calculated_risk`, `last_update`) VALUES

(2,	2,	3.6,	'2017-03-31 21:59:48');

 */

$url 			= "http://localhost/api/risk/new/";



$xAlert		= new cAMLAlertas($clave);
if($xAlert->init() == true){
	
}


$sqlL1	= "INSERT INTO `risks` (`id`, `status`, `subject`, `reference_id`, `regulation`, `control_number`, `location`, `source`, `category`, `team`, `technology`,
 `owner`, `manager`, `assessment`, `notes`, `submission_date`, `last_update`, `review_date`, `mitigation_id`, `mgmt_review`, `project_id`, `close_id`, `submitted_by`) VALUES ";
$sqlL2	= "INSERT INTO `risk_scoring_history` (`id`, `risk_id`, `calculated_risk`, `last_update`) VALUES ";

$sqlL3	= "INSERT INTO `risk_scoring` (`id`, `scoring_method`, `calculated_risk`, `CLASSIC_likelihood`, `CLASSIC_impact`, `CVSS_AccessVector`, `CVSS_AccessComplexity`, `CVSS_Authentication`, `CVSS_ConfImpact`, `CVSS_IntegImpact`, `CVSS_AvailImpact`, `CVSS_Exploitability`, `CVSS_RemediationLevel`, `CVSS_ReportConfidence`, `CVSS_CollateralDamagePotential`, `CVSS_TargetDistribution`, `CVSS_ConfidentialityRequirement`, `CVSS_IntegrityRequirement`, `CVSS_AvailabilityRequirement`, `DREAD_DamagePotential`, `DREAD_Reproducibility`, `DREAD_Exploitability`, `DREAD_AffectedUsers`, `DREAD_Discoverability`, `OWASP_SkillLevel`, `OWASP_Motive`, `OWASP_Opportunity`, `OWASP_Size`, `OWASP_EaseOfDiscovery`, `OWASP_EaseOfExploit`, `OWASP_Awareness`, `OWASP_IntrusionDetection`, `OWASP_LossOfConfidentiality`, `OWASP_LossOfIntegrity`, `OWASP_LossOfAvailability`, `OWASP_LossOfAccountability`, `OWASP_FinancialDamage`, `OWASP_ReputationDamage`, `OWASP_NonCompliance`, `OWASP_PrivacyViolation`, `Custom`) VALUES ";

//print_r($params);

$estatus	= "New";
$describe	= $xAlert->getDescripcion();
$regulation	= 9;//PLD
$location	= 2;//Corporativo
$origen		= 2;//1.- Gente; 2.- Proceso; 3.- Sistema; 4.- Externo
$categoria	= 91; //Prevencion AML
$team		= 91;//Oficial de cumplimiento
$tech		= 91;//Sistema Externo
$propietario= 1;//1 = Admin
$manager	= 1;//1 = Admin
$equipo		= "";//Assests
$notas		= $xAlert->getMensajes();
$fecha_crea	= date("Y-m-d H:m:s", strtotime($xAlert->getFechaRegistro()));
$enviadopor	= 1;//Admin
$nivel		= 3.6;

if($xAlert->getEsEnviadoRMS() == false){


	$cnn 		= new mysqli( "localhost", "simplerisk", "simplerisk", "simplerisk");
	$cnn->set_charset("utf8");
	
	$rs1		= $cnn->query("SELECT COUNT(*) AS 'items' FROM `risks` WHERE `id`=$clave");
	if(!$rs1){
		$rs["message"] = "La base de Datos no está disponible";
	} else {
		$row	= $rs1->fetch_assoc();
		$contar	= setNoMenorQueCero($row["items"]);
		if($contar > 0){
			$rs["message"] = "El registro ya existe";
		} else {
			$cnn->query("$sqlL1 ($clave,'$estatus','$describe','$clave', $regulation, '$clave',	$location,	$origen, $categoria, $team,	$tech,
			$propietario,	$manager,	'$equipo',	'$notas',	'$fecha_crea',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	0,	0,	0,	NULL,	$enviadopor)");
			
			$cnn->query("$sqlL2 (NULL, $clave, $nivel, '$fecha_crea')");
			
			$cnn->query("$sqlL3  ('$clave', '1', '$nivel', '4', '3', 'N', 'L', 'N', 'C', 'C', 'C', 'ND', 'ND', 'ND', 'ND', 'ND', 'ND', 'ND', 'ND', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10', '10')");
			
			$xAlert->setEnviadoRMS();
		
			$rs["message"] = "El registro de riesgos se ha efectuado";
		}
		
		$rs["error"]	= false;
	
	}
} else {
	$rs["error"]	= false;
	$rs["message"] = "El registro no se puede volver a enviar";
}

/*
$content 	= json_encode($arrDatos);

$curl 		= curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response 	= curl_exec($curl);*/

//$status 		= curl_getinfo($curl, CURLINFO_HTTP_CODE);
//if ( $status != 201 ) {
	//die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
//}

/*curl_close($curl);

$response 		= json_decode($json_response, true);

setLog($json_response);*/
//setLog($response);
echo json_encode($rs);


?>
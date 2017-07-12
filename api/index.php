<?php

// Kickstart the framework
$f3=require('../libs/fatfree/lib/base.php');

$f3->set("RAW", true);

$f3->route("POST /risk/new/*", function ($f3, $args){
	
	
	
	//echo $f3->get("PARAMS.iduser");
	//echo "ruta 24";
	/*
	 INSERT INTO `risks` (
	 `id`, `status`, `subject`, `reference_id`, `regulation`, `control_number`, `location`, `source`,
	 `category`, `team`, `technology`, `owner`, `manager`, `assessment`, `notes`,
	 `submission_date`, `last_update`, `review_date`, `mitigation_id`, `mgmt_review`, `project_id`, `close_id`, `submitted_by`) VALUES
	 
	 (2,	'New',	'Riesgo de Prueba',	'999',	2,	'1451',	1,	1,	5,	5,	1,	1,	1,	'Prueba',	'Nada que ver',	'2017-04-01 03:59:48',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	0,	0,	0,	NULL,	1);
	 
	 
	 
	 ;
	 
	 */
	$db		= new DB\SQL(
			'mysql:host=localhost;port=3306;dbname=simplerisk',
			'simplerisk',
			'simplerisk'
			);
	$sqlL1	= "INSERT INTO `risks` (`id`, `status`, `subject`, `reference_id`, `regulation`, `control_number`, `location`, `source`, `category`, `team`, `technology`, `owner`, `manager`, `assessment`, `notes`, `submission_date`, `last_update`, `review_date`, `mitigation_id`, `mgmt_review`, `project_id`, `close_id`, `submitted_by`) VALUES ";
	$sqlL2	= "INSERT INTO `risk_scoring_history` (`id`, `risk_id`, `calculated_risk`, `last_update`) VALUES ";
	//print_r($params);
	
	$estatus	= "New";
	$describe	= "";
	
	//header('Content-type: application/json');
	
	
	
	//syslog(E_WARNING, print_r($f3->get("BODY"), true));
	//$db->exec("$sqlL1 (2,	'$estatus',	'$describe',	'999',	2,	'1451',	1,	1,	5,	5,	1,	1,	1,	'Prueba',	'Nada que ver',	'2017-04-01 03:59:48',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	0,	0,	0,	NULL,	1)");
	//$db->exec("$sqlL2 (2,	2,	3.6,	'2017-03-31 21:59:48')");
});


//

$f3->route("GET /hola/test/@iduser", function ($f3){
	echo $f3->get("PARAMS.iduser");
	//echo "ruta 24";
});

	
	
	
$f3->route("GET /", function ($f3){
	
	// MySql settings
	/*$f3->set('DB', new DB\SQL(
			'mysql:host=localhost;port=3306;dbname=simplerisk',
			'simplerisk',
			'simplerisk'
			));*/
	
	$db		= new DB\SQL(
			'mysql:host=localhost;port=3306;dbname=simplerisk',
			'simplerisk',
			'simplerisk'
			);
	
	$table = new DB\SQL\Mapper($db, 'assets');
	$table->load(array('id=?', '1'));
	$result = $table->created;
	
	echo $result;
	
});

$f3->run();

?>
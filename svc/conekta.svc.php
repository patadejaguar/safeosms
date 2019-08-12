<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
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
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
$xInit->cors();
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();

$tabla		= parametro("tabla", false, MQL_RAW);
$clave		= parametro("id", false, MQL_RAW);

$rs			= array();

header('Content-type: application/json');
echo json_encode($rs);
//setLog(json_encode($rs));
//setLog($sql);

$data = json_decode(file_get_contents('php://input'), true);

print_r($data);


echo $data["operacion"];

setLog($data);

//{"id":"51cef9faf23668b1f4000001","created_at":1558974719,"livemode":true,"type":"ping","data":{"object":[],"previous_attributes":[]}}


echo file_get_contents('php://input');

/*{
  "data": {
    "object": {
      "id": "588258fbedbb6e85e7000f95",
      "livemode": false,
      "created_at": 1484937467,
      "currency": "MXN",
      "payment_method": {
        "service_name": "OxxoPay",
        "object": "cash_payment",
        "type": "oxxo",
        "expires_at": 1487548800,
        "store_name": "OXXO",
        "reference": "93345678901234"
      },
      "details": {
        "name": "Fulanito Pérez",
        "phone": "+5218181818181",
        "email": "fulanito@conekta.com",
        "line_items": [{
            "name": "Tacos",
            "unit_price": 1000,
            "quantity": 12
        }],
        "shipping_contact": {
           "phone": "5555555555",
           "receiver": "Bruce Wayne",
           "address": {
             "street1": "Calle 123 int 2 Col. Chida",
             "city": "Cuahutemoc",
             "state": "Ciudad de Mexico",
             "country": "MX",
             "postal_code": "06100",
             "residential": true
           }
         },
        "object": "details"
      },
      "object": "charge",
      "status": "paid",
      "amount": 13500,
      "paid_at": 1484937498,
      "fee": 1421,
      "customer_id": "",
      "order_id": "ord_2fshhd1RAEnB5zUfG",
    },
    "previous_attributes": {
      "status": "pending_payment"
    }
  },
  "livemode": false,
  "webhook_status": "successful",
  "webhook_logs": [
    {
      "id": "webhl_2fshi2CmCGqx4p6go",
      "url": "<a href="http://www.example.com"">www.example.com"</a>,
      "failed_attempts": 0,
      "last_http_response_status": 200,
      "object": "webhook_log",
      "last_attempted_at": 1484937503
    }
  ],
  "id": "5882591b5906e7819c0007f1",
  "object": "event",
  "type": "charge.paid",
  "created_at": 1484937499
}
*/
?>
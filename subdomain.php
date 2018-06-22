<?php
$url 		= $_SERVER['SERVER_NAME'];
$parsedUrl 	= parse_url($url);
$host 		= explode('.', $url);// $parsedUrl['host']);

$subdomain 	= $host[0];

header("Location: https://" . $subdomain . ".microfinanzas.net/");
?>
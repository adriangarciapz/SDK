<?php

require_once __DIR__ . '/../vendor/autoload.php';

use UDT\SDK\SDK;

$sdk = new SDK("eruceSemuserp_redirect", "eruceSemuserp_redirect");

$requestJSON = file_get_contents("php://input");
$response = $sdk->handlePayload($requestJSON);

$response["code"] = isset($response["code"]) ? $response["code"] : 500;
header("HTTP/1.0 " . $response["code"]);
header("Content-type: application/json; charset=utf-8");

echo json_encode($response);
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use UDT\SDK\SDK;

$sdk = new SDK("eruceSemuserp_redirect", "eruceSemuserp_redirect");

$response = $sdk->handlePayload();
echo $response;
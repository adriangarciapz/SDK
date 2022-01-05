<?php

namespace HelloWorld;

class CancelPayment {

  public static function cancel($pid) {

    $url = 'localhost:8081/api/v1/superapp/payment_' . $pid . '/cancellations';
    $payloadString = "{\n    \"paymentId\": \"payment_$pid\",\n    \"requestId\": \"x\"\n}";

    $appKey = "eruceSemuserp_redirect";
    $appToken = "eruceSemuserp_redirect";

    $cancelResponse = Utils::request($url, $payloadString, $appKey, $appToken);
    if (!(intval($cancelResponse->cancellationId) > 0)) {
	    echo "Wrong cancel response";
    }
  }
}

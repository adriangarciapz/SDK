<?php

namespace HelloWorld;

class CancelPayment {

  public static function cancel($pid) {

    $orderPendingAmount = 0.0;
    //$orderStatus = SUPERAPP_ORDER_STATE_CANCELLED;
    $orderStatus = "cancelled";

    $url = 'localhost:8081/api/v1/superapp/payment_' . $pid . '/cancellations';
    $payloadString = "{\n    \"paymentId\": \"payment_$pid\",\n    \"requestId\": \"x\"\n}";

    // key and token
    $appKey = "eruceSemuserp_redirect";
    $appToken = "eruceSemuserp_redirect";

    $cancelResponse = $this->request($url, $payloadString, $appKey, $appToken);
    if (!(intval($cancelResponse->cancellationId) > 0)) {
	echo "Wrong cancel response";
    }

    // reload and check
    //$order = superAppQueriesOrderGetById($order->id);
    //$this->verifyOrder($order, $orderStatus, $orderPendingAmount, $orderPaidAmount, $orderRefundedAmount);

    //return true; // kill the testing, everything was fine

  }
}

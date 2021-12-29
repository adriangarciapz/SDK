<?php

namespace HelloWorld;

class CreatePayment {

  public static function create() {

    $result = null;

    $userId = 4002187;

    $orderStatus = null;
    $orderPendingAmount = 0.0;
    $orderPaidAmount = 0.0;
    $orderRefundedAmount = 0.0;

    // process id
    $pid = date("Ymd") . '_' . rand(1, 10000);

    // key and token
    $appKey = "eruceSemuserp_redirect";
    $appToken = "eruceSemuserp_redirect";

    // create merchant
    $merchantId = 1;

    $paymentAmount = 44.5;
    $extraChargeAmount = 0.0;
    $orderPendingAmount = $paymentAmount;
    $orderStatus = 'pending';


    $url = 'localhost:8081/api/v1/superapp/payments';

    $payloadString = "{\n    \"reference\": \"reference_".$pid."\",\n    \"orderId\": \"x\",\n    \"transactionId\": \"transaction_".$pid."_01\",\n    \"paymentId\": \"payment_".$pid."\",\n    \"paymentMethod\": \"undostres\",\n    \"paymentMethodCustomCode\": null,\n    \"merchantName\": \"unit_test_".$pid."\",\n    \"value\": ".$paymentAmount.",\n    \"currency\": \"MXN\",\n    \"installments\": 3,\n    \"deviceFingerprint\": \"12ade389087fe\",\n    \"card\": {\n        \"holder\": \"John Doe\",\n        \"number\": \"4682185088924788\",\n        \"csc\": \"021\",\n        \"expiration\": {\n            \"month\": \"06\",\n            \"year\": \"2029\"\n        }\n    },\n    \"miniCart\": {\n        \"shippingValue\": 11.44,\n        \"taxValue\": 10.01,\n        \"buyer\": {\n            \"id\": \"2647866\",\n            \"firstName\": \"John\",\n            \"lastName\": \"Doe\",\n            \"document\": \"01234567890\",\n            \"documentType\": \"CPF\",\n            \"email\": \"john.doe@example.com\",\n            \"phone\": \"+5521987654321\"\n        },\n        \"shippingAddress\": {\n            \"country\": \"BRA\",\n            \"street\": \"Praia de Botafogo St.\",\n            \"number\": \"300\",\n            \"complement\": \"3rd Floor\",\n            \"neighborhood\": \"Botafogo\",\n            \"postalCode\": \"22250040\",\n            \"city\": \"Rio de Janeiro\",\n            \"state\": \"RJ\"\n        },\n        \"billingAddress\": {\n            \"country\": \"BRA\",\n            \"street\": \"Brigadeiro Faria Lima Avenue\",\n            \"number\": \"4440\",\n            \"complement\": \"10th Floor\",\n            \"neighborhood\": \"Itaim Bibi\",\n            \"postalCode\": \"04538132\",\n            \"city\": \"São Paulo\",\n            \"state\": \"SP\"\n        },\n        \"items\": [\n            {\n                \"id\": \"132981\",\n                \"name\": \"My First Product\",\n                \"price\": 2134.90,\n                \"quantity\": 2,\n                \"discount\": 5.00\n            },\n            {\n                \"id\": \"123242\",\n                \"name\": \"My Second Product\",\n                \"price\": 21.98,\n                \"quantity\": 1,\n                \"discount\": 1.00\n            }\n        ]\n    },\n    \"url\": \"https://admin.mystore.example.com/orders/v32478982\",\n    \"callbackUrl\": \"https://api.example.com/some-path/to-notify/status-changes?an=mystore\",\n    \"returnUrl\": \"https://mystore.example.com/checkout/order/v32478982\"\n}";
    $createPaymentResponse = CreatePayment::request($url, $payloadString, $appKey, $appToken);

    echo $createPaymentResponse->status;
  }

  public static function request($url, $payloadString, $appKey, $appToken) {
    $serverResponse = null;
    $curl = null;
    try {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>$payloadString,
            CURLOPT_HTTPHEADER => array(
                "superappkey" . ": " . $appKey,
                "superapptoken" . ": " . $appToken,
                "Content-Type: application/json",
                "Accept: application/json"
            ),
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $serverResponse = json_decode($resp);
    } catch (Exception $e) {
        if ($curl) {
	    echo "Curl error\n";
            echo curl_error($curl);
        }
	echo "Curl error\n";
        echo $e->getMessage();
        echo $e->getTraceAsString();
    }
    return $serverResponse;
  }
}

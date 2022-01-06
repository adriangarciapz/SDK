<?php

namespace HelloWorld;

class CreatePayment {

  const SUPERAPP_PAYMENTS_URL = "localhost:8081/api/v1/superapp/payments";
  const APP_KEY   = "eruceSemuserp_redirect";
  const APP_TOKEN = "eruceSemuserp_redirect";
  const STAGE_URL_STR = "undostres://home?stage=superAppPaymentIntent&url=";
  const PAYMENT_STR = "payment.php?d=";
  const MERCHANT_STR = "&m=";
  const DECRYPT_KEY = "313233343536373839306162636465664A4B4C4D4E4F5A6A6B6C6D6E6F707172";

  public static function create($pid) {

    $paymentAmount = 1;

    $payloadString = "{\n    \"reference\": \"reference_".$pid."\",\n    \"orderId\": \"x\",\n    \"transactionId\": \"transaction_".$pid."_01\",\n    \"paymentId\": \"payment_".$pid."\",\n    \"paymentMethod\": \"undostres\",\n    \"paymentMethodCustomCode\": null,\n    \"merchantName\": \"unit_test_".$pid."\",\n    \"value\": ".$paymentAmount.",\n    \"currency\": \"MXN\",\n    \"installments\": 3,\n    \"deviceFingerprint\": \"12ade389087fe\",\n    \"card\": {\n        \"holder\": \"John Doe\",\n        \"number\": \"4682185088924788\",\n        \"csc\": \"021\",\n        \"expiration\": {\n            \"month\": \"06\",\n            \"year\": \"2029\"\n        }\n    },\n    \"miniCart\": {\n        \"shippingValue\": 11.44,\n        \"taxValue\": 10.01,\n        \"buyer\": {\n            \"id\": \"2647866\",\n            \"firstName\": \"John\",\n            \"lastName\": \"Doe\",\n            \"document\": \"01234567890\",\n            \"documentType\": \"CPF\",\n            \"email\": \"john.doe@example.com\",\n            \"phone\": \"+5521987654321\"\n        },\n        \"shippingAddress\": {\n            \"country\": \"BRA\",\n            \"street\": \"Praia de Botafogo St.\",\n            \"number\": \"300\",\n            \"complement\": \"3rd Floor\",\n            \"neighborhood\": \"Botafogo\",\n            \"postalCode\": \"22250040\",\n            \"city\": \"Rio de Janeiro\",\n            \"state\": \"RJ\"\n        },\n        \"billingAddress\": {\n            \"country\": \"BRA\",\n            \"street\": \"Brigadeiro Faria Lima Avenue\",\n            \"number\": \"4440\",\n            \"complement\": \"10th Floor\",\n            \"neighborhood\": \"Itaim Bibi\",\n            \"postalCode\": \"04538132\",\n            \"city\": \"São Paulo\",\n            \"state\": \"SP\"\n        },\n        \"items\": [\n            {\n                \"id\": \"132981\",\n                \"name\": \"My First Product\",\n                \"price\": 2134.90,\n                \"quantity\": 2,\n                \"discount\": 5.00\n            },\n            {\n                \"id\": \"123242\",\n                \"name\": \"My Second Product\",\n                \"price\": 21.98,\n                \"quantity\": 1,\n                \"discount\": 1.00\n            }\n        ]\n    },\n    \"url\": \"https://admin.mystore.example.com/orders/v32478982\",\n    \"callbackUrl\": \"https://api.example.com/some-path/to-notify/status-changes?an=mystore\",\n    \"returnUrl\": \"https://mystore.example.com/checkout/order/v32478982\"\n}";
    $response = Utils::request(self::SUPERAPP_PAYMENTS_URL, $payloadString, self::APP_KEY, self::APP_TOKEN);

    $sstageUrlStrLen = strlen(self::STAGE_URL_STR);
    $response->paymentUrl = substr($response->paymentUrl, $sstageUrlStrLen);
    $response->paymentUrl = self::STAGE_URL_STR . urldecode($response->paymentUrl);

    $paymentStrLen = strlen(self::PAYMENT_STR);
    $paymentStrPos = strpos($response->paymentUrl, self::PAYMENT_STR);

    $publicRootStr = substr($response->paymentUrl, 0, $paymentStrPos);
    $publicRootStrLen = strlen($publicRootStr);

    $response->paymentUrl = substr($response->paymentUrl, $publicRootStrLen + $paymentStrLen);

    $merchantStrLen = strlen(self::MERCHANT_STR);
    $merchantStrPos = strpos($response->paymentUrl, self::MERCHANT_STR);

    $encryptedDataStr = substr($response->paymentUrl, 0, $merchantStrPos);
    $encryptedDataStrLen = strlen($encryptedDataStr);

    $decryptedData = Utils::decrypt(rawurldecode($encryptedDataStr), self::DECRYPT_KEY);

    $merchantId = rawurldecode(substr($response->paymentUrl, $encryptedDataStrLen + $merchantStrLen));

    echo $publicRootStr . "\n";
    echo $decryptedData["returnUrl"] . "\n";
    echo $merchantId . "\n";
  }

}

<?php

namespace UDT\Payment;

use UDT\Utils;

class Payment {

  const SUPERAPP_PAYMENTS_URL = "localhost:8081/api/v1/superapp/payments";
  const APP_KEY   = "eruceSemuserp_redirect";
  const APP_TOKEN = "eruceSemuserp_redirect";
  const STAGE_URL_STR = "undostres://home?stage=superAppPaymentIntent&url=";
  const PAYMENT_STR = "payment.php?d=";
  const MERCHANT_STR = "&m=";
  const DECRYPT_KEY = "313233343536373839306162636465664A4B4C4D4E4F5A6A6B6C6D6E6F707172";

  public static function create($pid) {

    $paymentAmount = 1;

    $payload = [
      "reference" => "reference_$pid",
      "orderId" => "x",
      "transactionId" => "transaction_$pid",
      "paymentId" => "payment_$pid",
      "paymentMethod" => "undostres",
      "paymentMethodCustomCode" => null,
      "merchantName" => "unit_test_$pid",
      "value" => $paymentAmount,
      "currency" => "MXN",
      "installments" => 3,
      "deviceFingerprint" => "12ade389087fe",
      "card" => [
        "holder" => "John Doe",
        "number" => "4682185088924788",
        "csc" => "021",
        "expiration" => [
            "month" => "06",
            "year" => "2029"
          ]
      ],
      "miniCart" => [
        "shippingValue" => 11.44,
        "taxValue" => 10.01,
        "buyer" => [
          "id" => "2647866",
          "firstName" => "John",
          "lastName" => "Doe",
          "document" => "01234567890",
          "documentType" => "CPF",
          "email" => "john.doe@example.com",
          "phone" => "+5521987654321"
        ],
        "shippingAddress" => [
          "country" => "BRA",
          "street" => "Praia de Botafogo St.",
          "number" => "300",
          "complement" => "3rd Floor",
          "neighborhood" => "Botafogo",
          "postalCode" => "22250040",
          "city" => "Rio de Janeiro",
          "state" => "RJ"
        ],
        "billingAddress" => [
          "country" => "BRA",
          "street" => "Brigadeiro Faria Lima Avenue",
          "number" => "4440",
          "complement" => "10th Floor",
          "neighborhood" => "Itaim Bibi",
          "postalCode" => "04538132",
          "city" => "São Paulo",
          "state" => "SP"
        ],
        "items" => [[
          "id" => "132981",
          "name" => "My First Product",
          "price" => 2134.90,
          "quantity" => 2,
          "discount" => 5.00
        ], [
          "id" => "123242",
          "name" => "My Second Product",
          "price" => 21.98,
          "quantity" => 1,
          "discount" => 1.00
          ]]
      ],
      "url" => "https =>//admin.mystore.example.com/orders/v32478982",
      "callbackUrl" => "https://api.example.com/some-path/to-notify/status-changes?an=mystore",
      "returnUrl" => "https://mystore.example.com/checkout/order/v32478982"
    ];

    $payloadString = json_encode($payload);
    $response = Utils::request(self::SUPERAPP_PAYMENTS_URL, $payloadString, self::APP_KEY, self::APP_TOKEN);

    if ($response == null) {
      echo "Response is null\n";
      return;
    }

    echo "Is a valid URL? : " . (self::isValidUrl($response->paymentUrl) ? "Yes" : "No") . "\n";

    self::extractDataForRequest($response->paymentUrl);
  }

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

  public static function isValidUrl($url) {
    $url = filter_var($url, FILTER_SANITIZE_URL);
    if (!str_starts_with($url, "undostres")){
      return false;
    }
    return filter_var($url, FILTER_VALIDATE_URL);
  }

  public static function extractDataForRequest($url) {
    $stageUrlStrLen = strlen(self::STAGE_URL_STR);
    $url = substr($url, $stageUrlStrLen);
    $url = self::STAGE_URL_STR . urldecode($url);

    $paymentStrLen = strlen(self::PAYMENT_STR);
    $paymentStrPos = strpos($url, self::PAYMENT_STR);

    $publicRootStr = substr($url, 0, $paymentStrPos);
    $publicRootStrLen = strlen($publicRootStr);

    $url = substr($url, $publicRootStrLen + $paymentStrLen);

    $merchantStrLen = strlen(self::MERCHANT_STR);
    $merchantStrPos = strpos($url, self::MERCHANT_STR);

    $encryptedDataStr = substr($url, 0, $merchantStrPos);
    $encryptedDataStrLen = strlen($encryptedDataStr);

    $decryptedData = Utils::decrypt(rawurldecode($encryptedDataStr), self::DECRYPT_KEY);

    $merchantId = rawurldecode(substr($url, $encryptedDataStrLen + $merchantStrLen));

    echo $publicRootStr . "\n";
    echo $decryptedData["returnUrl"] . "\n";
    echo $merchantId . "\n";
  }

}

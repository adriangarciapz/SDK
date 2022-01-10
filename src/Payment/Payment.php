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

  private $payloadObj;
  private $payloadStr;

  public function setPayload($payload) {
    $this->payloadObj = $payload;
    $this->payloadJSON = json_encode($payload);

    if (json_last_error() != JSON_ERROR_NONE)
      throw new \InvalidArgumentException("The payload is not JSON encodable :: " . json_last_error_msg());
  }

  public function getPayload() {
    return $this->payloadObj;
  }

  public function exec() {
    if (!isset($this->payloadJSON))
      throw new \Exception("Payload not set");

    return Utils::request(self::SUPERAPP_PAYMENTS_URL, $this->payloadJSON, self::APP_KEY, self::APP_TOKEN);
  }

  public function cancel($pid) {

    $url = 'localhost:8081/api/v1/superapp/payment_' . $pid . '/cancellations';
    $payloadStr = "{\n    \"paymentId\": \"payment_$pid\",\n    \"requestId\": \"x\"\n}";

    $appKey = "eruceSemuserp_redirect";
    $appToken = "eruceSemuserp_redirect";

    $cancelResponse = Utils::request($url, $payloadStr, $appKey, $appToken);
    if (!(intval($cancelResponse->cancellationId) > 0)) {
	    echo "Wrong cancel response";
    }
  }

  public static function decodePaymentUrl($url) {
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

    $decryptedData = Utils::decryptPaymentData(rawurldecode($encryptedDataStr), self::DECRYPT_KEY);

    $merchantId = rawurldecode(substr($url, $encryptedDataStrLen + $merchantStrLen));

    return [
      "publicRoot" => $publicRootStr,
      "data" => $decryptedData,
      "merchantId" => $merchantId
    ];
  }

}

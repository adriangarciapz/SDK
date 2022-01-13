<?php

namespace UDT\Payment;

use UDT\Utils;

class Payment {

  const HOST = "localhost:8081";
  const CREATE_URL = "/api/v1/superapp/payments";
  const APP_KEY   = "eruceSemuserp_redirect";
  const APP_TOKEN = "eruceSemuserp_redirect";
  const STAGE_URL_STR = "undostres://home?stage=superAppPaymentIntent&url=";
  const PAYMENT_STR = "payment.php?d=";
  const MERCHANT_STR = "&m=";
  const DECRYPT_KEY = "313233343536373839306162636465664A4B4C4D4E4F5A6A6B6C6D6E6F707172";

  private $CREATE_ENDPOINT = self::HOST . self::CREATE_URL;
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

  public function requestPayment() {
    if (!isset($this->payloadJSON))
      throw new \Exception("Payload not set");

    $response = Utils::request($this->CREATE_ENDPOINT, $this->payloadJSON, self::APP_KEY, self::APP_TOKEN);
    // Utils::validateResponse($response, "SuperappCreatePaymentResponse.json");

    return $response;
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

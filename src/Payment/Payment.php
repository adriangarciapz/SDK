<?php

namespace UDT\Payment;

use UDT\Utils;

class Payment {

  const CREATE_URL = "/api/v1/superapp/payments";

  private $createEndpoint;
  private $appKey;
  private $appToken;
  private $payloadObj;
  private $payloadStr;
  
  public function __construct($host, $appKey, $appToken) {
    $this->createEndpoint = $host . self::CREATE_URL;
    $this->appKey         = $appKey;
    $this->appToken       = $appToken;
  }

  public function setPayload($payload) {
    $this->payloadObj = $payload;
    $this->payloadJSON = json_encode($payload);

    if (json_last_error() != JSON_ERROR_NONE)
      throw new \InvalidArgumentException(
        "The payload is not JSON encodable :: " . json_last_error_msg(),
        400);
  }

  public function getPayload() {
    return $this->payloadObj;
  }

  public function requestPayment() {
    if (!isset($this->payloadJSON))
      throw new \Exception("Payload not set", 400);

    $response = Utils::request($this->createEndpoint, $this->payloadJSON, $this->appKey, $this->appToken);
    Utils::validateResponse($response, "SuperappCreatePaymentResponse.json");

    return $response;
  }

}

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
    $this->payloadJSON = Utils::encodePayload($payload);
  }

  public function requestPayment() {
    if (!isset($this->payloadJSON))
      throw new \Exception("Payload not set", 400);

    $response = Utils::request($this->createEndpoint, $this->payloadJSON, $this->appKey, $this->appToken);
    Utils::validateResponse($response, "SuperappCreatePaymentResponse.json");

    return $response;
  }

}

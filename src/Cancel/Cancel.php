<?php

namespace UDT\Cancel;

use UDT\Utils;

class Cancel {

  const CANCEL_URL = "/api/v1/superapp/{paymentId}/cancellations";

  private $cancelEndpoint;
  private $appKey;
  private $appToken;
  private $paymentId;
  private $requestId;
  private $payloadJSON;

  public function __construct($host, $appKey, $appToken, $paymentId) {
    $this->cancelEndpoint = $host . self::CANCEL_URL;
    $this->appKey        = $appKey;
    $this->appToken      = $appToken;
    $this->paymentId     = $paymentId;
    $this->requestId     = $paymentId . date("YmdHisu");

    $payload = [
      "paymentId"     => $this->paymentId,
      "requestId"     => $this->requestId
    ];

    $this->payloadJSON = Utils::encodePayload($payload);

    $this->cancelEndpoint = $this->createCancelUrl($host, $paymentId);
  }

  public function createCancelUrl($host, $paymentId) {
    $subject = $host . self::CANCEL_URL;
    return str_replace("{paymentId}", $paymentId, $subject);
  }

  public function requestCancel() {
    if (!isset($this->payloadJSON))
      throw new \Exception("Payload not set");

    if (strpos($this->cancelEndpoint, "{paymentId}") !== false)
      throw new \Exception("paymentId not set in URL");

    $response = Utils::request($this->cancelEndpoint, $this->payloadJSON, $this->appKey, $this->appToken);
    Utils::validateData($response, "SuperappCancelPaymentResponse.json", 500);

    return $response;
  }

}

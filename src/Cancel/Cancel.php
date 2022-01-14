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

    $this->createCancelUrl($host, $paymentId);
  }

  public function createCancelUrl($host, $paymentId) {
    $this->cancelEndpoint = $host . self::CANCEL_URL;
    $this->cancelEndpoint = str_replace("{paymentId}", $paymentId, $this->cancelEndpoint);
  }

  public function requestCancel() {
    if (!isset($this->payloadJSON))
      throw new \Exception("Payload not set");

    if (strpos($this->cancelEndpoint, "{paymentId}") !== false)
      throw new \Exception("paymentId not set in URL");

    return Utils::request($this->cancelEndpoint, $this->payloadJSON, $this->appKey, $this->appToken);
  }

}

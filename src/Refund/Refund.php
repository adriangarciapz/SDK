<?php

namespace UDT\Refund;

use UDT\Utils;

class Refund {

  const REFUND_URL = "/api/v1/superapp/{paymentId}/refunds";

  private $refundEndpoint;
  private $paymentId;
  private $transactionId;
  private $value;
  private $requestId;
  private $payloadJSON;

  public function __construct($host, $appKey, $appToken, $paymentId, $transactionId, $value) {
    $this->refundEndpoint = $host . self::REFUND_URL;
    $this->appKey        = $appKey;
    $this->appToken      = $appToken;
    $this->paymentId     = $paymentId;
    $this->transactionId = $transactionId;
    $this->value         = $value;
    $this->requestId     = $paymentId . date("YmdHisu");

    $payload = [
      "paymentId"     => $this->paymentId,
      "transactionId" => $this->transactionId,
      "value"         => $this->value,
      "requestId"     => $this->requestId
    ];

    $this->createPayloadJSON($payload);

    $this->createRefundUrl($host, $paymentId);
  }

  public function getPaymentId() {
    return $this->paymentId;
  }

  public function setPaymentId($paymentId) {
    $this->paymentId = $paymentId;
  }

  public function getTransactionId() {
    return $this->transactionId;
  }

  public function setTransactionId($transactionId) {
    $this->transactionId = $transactionId;
  }

  public function getValue() {
    return $this->value;
  }

  public function setValue($value) {
    $this->value = $value;
  }

  public function getRequestId() {
    return $this->requestId;
  }

  public function getPayloadJSON() {
    return $this->payloadJSON;
  }

  public function createRefundUrl($host, $paymentId) {
    $this->refundEndpoint = $host . self::REFUND_URL;
    $this->refundEndpoint = str_replace("{paymentId}", $paymentId, $this->refundEndpoint);
  }

  public function createPayloadJSON($payload) {
    $this->payloadJSON = json_encode($payload);

    if (json_last_error() != JSON_ERROR_NONE)
      throw new \InvalidArgumentException("The payload is not JSON encodable :: " . json_last_error_msg());
  }

  public function requestRefund() {
    if (!isset($this->payloadJSON))
      throw new \Exception("Payload not set");

    if (strpos($this->refundEndpoint, "{paymentId}") !== false)
      throw new \Exception("paymentId not set in URL");

    return Utils::request($this->refundEndpoint, $this->payloadJSON, $this->appKey, $this->appToken);
  }

}

<?php

namespace UDT\SDK;

use UDT\Payment\Payment;
use UDT\Refund\Refund;

class SDK {

  public function __construct($appKey, $appToken) {
    if (isset($_ENV["DEV"]) && $_ENV["DEV"] == true)
      $this->host = "localhost:8081";
    else
      $this->host = "test.undostres.com.mx";
    
    $this->appKey   = $appKey;
    $this->appToken = $appToken;
  }

  public function handlePayload() {
    $requestJSON = file_get_contents("php://input");

    $body = json_decode($requestJSON, true);
    
    if(isset($body["payment"]))
      return $this->createPayment($body["payment"]);

    else if(isset($body["cancel"]))
      return $this->createRefund($body["cancel"]);
  }

  public function createPayment($paymentData) {
    $payment = new Payment($this->host, $this->appKey, $this->appToken);

    try {
        $payment->setPayload($paymentData);
        $response = $payment->requestPayment();

        $queryParams = [];
        parse_str(parse_url($response->paymentUrl)['query'], $queryParams);
        return json_encode($queryParams);

    } catch (Exception $e) {
        return json_encode($e->getMessage());
    }
  }

  public function createRefund($refundData) {
    try {
      $refund = new Refund(
        $this->host,
        $this->appKey,
        $this->appToken,
        $refundData["paymentId"],
        $refundData["transactionId"],
        round(floatval($refundData["value"]), 2)
      );

      return json_encode($refund->requestRefund());
    }
    catch (Exception $e) {
      return json_encode($e->getMessage());
    }
  }
}

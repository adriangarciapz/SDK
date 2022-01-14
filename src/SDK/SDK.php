<?php

namespace UDT\SDK;

use UDT\Payment\Payment;
use UDT\Cancel\Cancel;

class SDK {

  public function __construct($appKey, $appToken, $env = "dev") {
    $this->appKey   = $appKey;
    $this->appToken = $appToken;
    $this->env      = $env;

    $this->host = self::getHost($env);
  }

  public function setEnv($env) {
    $this->env = $env;
    $this->host = self::getHost($env);
  }

  public function getEnv() {
    return $this->env;
  }

  private static function getHost($env) {
    switch ($env) {
      case "dev":
        return "localhost:8081";
      default:
        return "test.undostres.com.mx";
    }
  }

  public function handlePayload() {
    $requestJSON = file_get_contents("php://input");

    $response = [
      "code" => 400,
      "body" => ["status" => "error"]
      ];

    $body = json_decode($requestJSON, true);

    try {
      if(isset($body["payment"]))
        $response = $this->createPayment($body["payment"]);

      else if(isset($body["cancel"]))
        $response = $this->createCancel($body["cancel"]);
    }
    catch (\Exception $e) {
      $response = [
        "code" => $e->getCode(),
        "body" => [
          "status" => "error",
          "error" => $e->getMessage()
        ]
      ];
    }
    finally {
      return $response;
    }
  }

  public function createPayment($paymentData) {
    $payment = new Payment($this->host, $this->appKey, $this->appToken);

    $payment->setPayload($paymentData);
    $response = $payment->requestPayment();

    $queryParams = [];
    parse_str(parse_url($response->paymentUrl)['query'], $queryParams);

    $response = [
      "code" => 200,
      "body" => [
        "status" => "success",
        "queryParams" => $queryParams
        ]
      ];
    return $response;
  }

  public function createCancel($cancelData) {
    try {
      $cancel = new Cancel(
        $this->host,
        $this->appKey,
        $this->appToken,
        $cancelData["paymentId"]
      );

      return json_encode($cancel->requestCancel());
    }
    catch (\Exception $e) {
      return json_encode($e->getMessage());
    }
  }
}

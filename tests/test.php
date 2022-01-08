<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use UDT\Payment\Payment;
use UDT\Utils;

$pid = date("Ymd") . '_' . rand(1, 10000);

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

$payment = new Payment();
$payment->setPayload($payload);
$response = $payment->exec();

echo "Is a valid URL? : " . (Utils::isValidUrl($response->paymentUrl) ? "Yes" : "No") . "\n";

$decodedUrl = Payment::decodePaymentUrl($response->paymentUrl);
var_dump($decodedUrl);

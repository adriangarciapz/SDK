<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use HelloWorld\CreatePayment;
use HelloWorld\CancelPayment;

$pid = date("Ymd") . '_' . rand(1, 10000);
echo CreatePayment::create($pid);
//echo CancelPayment::cancel($pid);

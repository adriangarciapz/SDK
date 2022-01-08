<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use UDT\Payment\Payment;

$pid = date("Ymd") . '_' . rand(1, 10000);
echo Payment::create($pid);
//echo CancelPayment::cancel($pid);

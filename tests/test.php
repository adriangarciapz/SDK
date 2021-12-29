<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use HelloWorld\CreatePayment;

echo CreatePayment::create();

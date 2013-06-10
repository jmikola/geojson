<?php

if (!file_exists($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

require_once $autoloadFile;

<?php
require_once __DIR__ . '/vendor/autoload.php';

use Quickstrap\ConsoleKernel;
use Quickstrap\Application;

$app = new Application(new ConsoleKernel());

$app->run();

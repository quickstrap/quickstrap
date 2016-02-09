<?php
/** @var ClassLoader $autoloader */
use Composer\Autoload\ClassLoader;
use QuickStrap\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->run();
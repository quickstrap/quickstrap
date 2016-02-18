<?php
/** @var ClassLoader $autoloader */
use Composer\Autoload\ClassLoader;
use QuickStrap\Application;

require_once __DIR__ . '/../vendor/autoload.php';

ini_alter('memory_limit', -1);

$application = new Application();
$application->run();
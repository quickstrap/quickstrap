<?php
/** @var ClassLoader $autoloader */
use Composer\Autoload\ClassLoader;
use QuickStrap\Commands\TestSuites\PhpUnitCommand;
use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->add(new PhpUnitCommand());
$application->run();
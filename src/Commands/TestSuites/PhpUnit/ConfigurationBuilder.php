<?php


namespace QuickStrap\Commands\TestSuites\PhpUnit;


use FluidXml\Core\FluidContext;
use FluidXml\FluidXml;

class ConfigurationBuilder
{
    /** @var  fluidxml */
    private $xml;
    /** @var  FluidContext */
    private $testsuites;
    /**
     * ConfigurationBuilder constructor.
     */
    public function __construct()
    {
        $this->xml = new fluidxml('phpunit');
        $this->xml->namespace('xsi','http://www.w3.org/2001/XMLSchema-instance');
        $this->xml->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->xml->setAttribute('xsi:noNamespaceSchemaLocation', 'http://schema.phpunit.de/4.0/phpunit.xsd');
    }

    public function bootstrap($path) {
        $this->xml->setAttribute('bootstrap', $path);
        return $this;
    }

    public function doNotBackupGlobals()
    {
        $this->xml->setAttribute('backupGlobals', 'false');
        return $this;
    }

    public function backupStaticAttributes()
    {
        $this->xml->setAttribute('backupStaticAttributes', 'true');
        return $this;
    }

    public function cacheTokens()
    {
        $this->xml->setAttribute('cacheTokens', 'true');
        return $this;
    }

    public function colors()
    {
        $this->xml->setAttribute('colors', 'true');
        return $this;
    }

    public function doNotConvertErrorsToExceptions()
    {
        $this->xml->setAttribute('convertErrorsToExceptions', 'false');
        return $this;
    }

    public function doNotConvertNoticesToExceptions()
    {
        $this->xml->setAttribute('convertNoticesToExceptions', 'false');
        return $this;
    }

    public function doNotConvertWarningsToExceptions()
    {
        $this->xml->setAttribute('convertWarningsToExceptions', 'false');
        return $this;
    }

    public function forceCoversAnnotation()
    {
        $this->xml->setAttribute('forceCoversAnnotation', 'true');
        return $this;
    }

    public function mapTestClassNameToCoveredClassName()
    {
        $this->xml->setAttribute('mapTestClassNameToCoveredClassName', 'true');
        return $this;
    }

    public function processIsolation()
    {
        $this->xml->setAttribute('processIsolation', 'true');
        return $this;
    }

    public function stopOnError()
    {
        $this->xml->setAttribute('stopOnError', 'true');
        return $this;
    }

    public function stopOnFailure()
    {
        $this->xml->setAttribute('stopOnFailure', 'true');
        return $this;
    }

    public function stopOnIncomplete()
    {
        $this->xml->setAttribute('stopOnIncomplete', 'true');
        return $this;
    }

    public function stopOnSkipped()
    {
        $this->xml->setAttribute('stopOnSkipped', 'true');
        return $this;
    }

    public function stopOnRisky()
    {
        $this->xml->setAttribute('stopOnRisky', 'true');
        return $this;
    }

    public function timeoutForSmallTests($timeout = 1)
    {
        $this->xml->setAttribute('timeoutForSmallTests', $timeout);
        return $this;
    }

    public function timeoutForMediumTests($timeout = 10)
    {
        $this->xml->setAttribute('timeoutForMediumTests', $timeout);
        return $this;
    }

    public function timeoutForLargeTests($timeout = 60)
    {
        $this->xml->setAttribute('timeoutForLargeTests', $timeout);
        return $this;
    }

    public function verbose()
    {
        $this->xml->setAttribute('verbose', 'true');
        return $this;
    }

    public function addTestSuite($name, $directory)
    {
        if (! $this->testsuites) {
            $this->testsuites = $this->xml->add('testsuites', true);
        }

        $this->testsuites->add('testsuite', true)
            ->attr('name', $name)
            ->add('directory', $directory);

        return $this;
    }

    public function __toString()
    {
        return $this->xml->xml();
    }
}
<?php


namespace QuickStrapUnit\Commands\TestSuites\PhpUnit;


use FluidXml\FluidXml;
use QuickStrap\Commands\TestSuites\PhpUnit\ConfigurationBuilder;

class ConfigurationBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ConfigurationBuilder */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new ConfigurationBuilder();
    }

    public function test_configures_root_tag_with_namespace()
    {
        $xml = FluidXml::load($this->sut->__toString());
        self::assertEquals('http://www.w3.org/2001/XMLSchema-instance',
            $xml->query('/phpunit')[0]->getAttribute('xmlns:xsi'));

        self::assertEquals('http://schema.phpunit.de/4.0/phpunit.xsd',
            $xml->query('/phpunit')[0]->getAttribute('xsi:noNamespaceSchemaLocation'));
    }

    public function test_configures_bootstrap_path()
    {
        $xml = FluidXml::load($this->sut
            ->bootstrap($path = 'path/to/bootstrap.php')
            ->__toString());

        self::assertEquals($path, $xml->query('/phpunit/@bootstrap')[0]->value);
    }

    public function test_configures_backup_globals_to_off()
    {
        $xml = FluidXml::load($this->sut
            ->doNotBackupGlobals()
            ->__toString());

        self::assertEquals('false', $xml->query('/phpunit/@backupGlobals')[0]->value);
    }
    
    public function test_configures_backup_static_attributes()
    {
        $xml = FluidXml::load($this->sut
            ->backupStaticAttributes()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@backupStaticAttributes')[0]->value);
    }
    
    public function test_configures_caches_tokens()
    {
        $xml = FluidXml::load($this->sut
            ->cacheTokens()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@cacheTokens')[0]->value);
    }
    
    public function test_configures_colors()
    {
        $xml = FluidXml::load($this->sut
            ->colors()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@colors')[0]->value);
    }
    
    public function test_configures_convert_errors_to_exceptions_as_off()
    {
        $xml = FluidXml::load($this->sut
            ->doNotConvertErrorsToExceptions()
            ->__toString());

        self::assertEquals('false', $xml->query('/phpunit/@convertErrorsToExceptions')[0]->value);
    }
    
    public function test_configures_convert_notices_to_exceptions_as_off()
    {
        $xml = FluidXml::load($this->sut
            ->doNotConvertNoticesToExceptions()
            ->__toString());

        self::assertEquals('false', $xml->query('/phpunit/@convertNoticesToExceptions')[0]->value);
    }
    
    public function test_configures_convert_warnings_to_exceptions_as_off()
    {
        $xml = FluidXml::load($this->sut
            ->doNotConvertWarningsToExceptions()
            ->__toString());

        self::assertEquals('false', $xml->query('/phpunit/@convertWarningsToExceptions')[0]->value);
    }
    
    public function test_configures_force_covers_annotation()
    {
        $xml = FluidXml::load($this->sut
            ->forceCoversAnnotation()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@forceCoversAnnotation')[0]->value);
    }
    
    public function test_configures_maps_test_class_name_to_covered_class_name()
    {
        $xml = FluidXml::load($this->sut
            ->mapTestClassNameToCoveredClassName()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@mapTestClassNameToCoveredClassName')[0]->value);
    }
    
    public function test_configures_process_isolation()
    {
        $xml = FluidXml::load($this->sut
            ->processIsolation()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@processIsolation')[0]->value);
    }
    
    public function test_configures_stops_on_errors()
    {
        $xml = FluidXml::load($this->sut
            ->stopOnError()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@stopOnError')[0]->value);
    }
    
    public function test_configures_stop_on_failure()
    {
        $xml = FluidXml::load($this->sut
            ->stopOnFailure()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@stopOnFailure')[0]->value);
    }

    public function test_configures_stop_on_incomplete()
    {
        $xml = FluidXml::load($this->sut
            ->stopOnIncomplete()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@stopOnIncomplete')[0]->value);
    }

    public function test_configures_stop_on_skipped()
    {
        $xml = FluidXml::load($this->sut
            ->stopOnSkipped()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@stopOnSkipped')[0]->value);
    }

    public function test_configures_stop_on_risky()
    {
        $xml = FluidXml::load($this->sut
            ->stopOnRisky()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@stopOnRisky')[0]->value);
    }

    public function test_configures_timeout_for_small_tests()
    {
        $xml = FluidXml::load($this->sut
            ->timeoutForSmallTests($timeout=1)
            ->__toString());

        self::assertEquals($timeout, $xml->query('/phpunit/@timeoutForSmallTests')[0]->value);
    }

    public function test_configures_timeout_for_medium_tests()
    {
        $xml = FluidXml::load($this->sut
            ->timeoutForMediumTests($timeout=12)
            ->__toString());

        self::assertEquals($timeout, $xml->query('/phpunit/@timeoutForMediumTests')[0]->value);

    }

    public function test_configures_timeout_for_large_tests()
    {
        $xml = FluidXml::load($this->sut
            ->timeoutForLargeTests($timeout=123)
            ->__toString());

        self::assertEquals($timeout, $xml->query('/phpunit/@timeoutForLargeTests')[0]->value);
    }

    public function test_configures_verbose()
    {
        $xml = FluidXml::load($this->sut
            ->verbose()
            ->__toString());

        self::assertEquals('true', $xml->query('/phpunit/@verbose')[0]->value);
    }

    public function test_adds_testsuites()
    {
        $xml = FluidXml::load($this->sut
            ->addTestSuite('foo', 'path/to/foo')
            ->addTestSuite('bar', 'path/to/bar')
            ->__toString());

        $testsuites = $xml->query('/phpunit/testsuites/testsuite');
        self::assertCount(2, $testsuites);
        self::assertEquals('foo', $testsuites[0]->getAttribute('name'));
        self::assertEquals('bar', $testsuites[1]->getAttribute('name'));

        $directories = $testsuites->query('//directory');
        self::assertCount(2, $directories);
        self::assertEquals('path/to/foo', $directories[0]->nodeValue);
        self::assertEquals('path/to/bar', $directories[1]->nodeValue);
    }
}

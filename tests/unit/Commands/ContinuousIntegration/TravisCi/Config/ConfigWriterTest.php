<?php

namespace QuickStrapUnit\Commands\ContinuousIntegration\TravisCi\Config;

use org\bovigo\vfs\vfsStream;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\Config;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigWriter;
use Symfony\Component\Yaml\Yaml;

class ConfigWriterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  string */
    private $configFile;
    
    /** @var ConfigWriter */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        vfsStream::setup('application_path')
            ->addChild($configFile = vfsStream::newFile('.travis.yml'));
        
        $this->configFile = $configFile->url();

        $this->sut = new ConfigWriter();
    }
    
    public function test_can_write_language_to_yml()
    {
        $config = new Config('php');

        $actual = $this->sut->toYml($config);

        self::assertEquals("language: php\n", $actual);
    }
    
    public function test_can_write_php_versions_to_yml()
    {
        $config = new Config('php');
        $config->setPhp(['7.0', '5.6']);

        $actual = $this->sut->toYml($config);

        self::assertEquals(<<<YML
language: php
php:
    - '7.0'
    - '5.6'

YML
, $actual);
    }
    
    public function test_can_write_before_script_to_yml()
    {
        $config = new Config('php');
        $config->setBeforeScript(['composer self-update', 'composer install']);

        $actual = $this->sut->toYml($config);

        self::assertEquals(<<<YML
language: php
before_script:
    - 'composer self-update'
    - 'composer install'

YML
, $actual);
    }
    
    public function test_can_write_script_to_yml()
    {
        $config = new Config('php');
        $config->setScript(['vendor/bin/phpunit', 'vendor/bin/behat']);

        $actual = $this->sut->toYml($config);

        self::assertEquals(<<<YML
language: php
script:
    - vendor/bin/phpunit
    - vendor/bin/behat

YML
, $actual);
    }
    
    public function test_can_allow_failures_to_yml()
    {
        $config = new Config('php');
        $config->getMatrix()->setAllowFailures('php', ['7.0', '5.6']);

        $actual = $this->sut->toYml($config);

        self::assertEquals(<<<YML
language: php
matrix:
    allow_failures:
        - { php: '7.0' }
        - { php: '5.6' }

YML
, $actual);
    }
    
    public function test_write_file_with_language()
    {
        $config = new Config('php');

        $bytesWritten = $this->sut->toYmlFile($config, $this->configFile);

        self::assertGreaterThan(0, $bytesWritten);

        $actual = Yaml::parse(file_get_contents($this->configFile));
        
        self::assertArrayKeyHasValue($actual, 'language', 'php');
    }

    /**
     * @param mixed[] $array
     * @param mixed $key
     * @param mixed $value
     */
    private static function assertArrayKeyHasValue($array, $key, $value)
    {
        self::assertArrayHasKey($key, $array);
        self::assertEquals($value, $array[$key]);
    }
}

<?php

namespace QuickStrapUnit\Commands\ContinuousIntegration\TravisCi\Config\Config;

use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\Config;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\Matrix;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new Config();
    }
    
    public function test_a_default_language_is_provided()
    {
        self::assertEquals('php', $this->sut->getLanguage());
    }
    
    public function test_language_can_be_set()
    {
        $this->sut->setLanguage($expected = 'java');
        
        self::assertEquals($expected, $this->sut->getLanguage());
    }
    
    public function test_php_version_can_be_set()
    {
        self::assertCount(0, $this->sut->getPhp());
        
        $this->sut->setPhp([$expected = '7.0']);
        
        self::assertCount(1, $this->sut->getPhp());
        self::assertContains($expected, $this->sut->getPhp());
        
        $this->sut->setPhp([$expected = '5.6']);
        
        self::assertCount(1, $this->sut->getPhp());
        self::assertContains($expected, $this->sut->getPhp());
    }
    
    public function test_adding_a_php_version_appends_to_list_of_versions()
    {
        self::assertCount(0, $this->sut->getPhp());

        $this->sut->addPhp($expected = '7.0');

        self::assertCount(1, $this->sut->getPhp());
        self::assertContains($expected, $this->sut->getPhp());

        $this->sut->addPhp($expected = '5.6');

        self::assertCount(2, $this->sut->getPhp());
        self::assertContains($expected, $this->sut->getPhp());
    }
    
    public function test_duplicate_php_version_are_ignored()
    {
        self::assertCount(0, $this->sut->getPhp());

        $this->sut->setPhp([$expected = '7.0']);

        self::assertCount(1, $this->sut->getPhp());
        self::assertContains($expected, $this->sut->getPhp());
        
        $this->sut->addPhp($expected);
        
        self::assertCount(1, $this->sut->getPhp());
        self::assertContains($expected, $this->sut->getPhp());
    }
    
    public function test_can_set_before_script()
    {
        self::assertCount(0, $this->sut->getBeforeScript());

        $this->sut->setBeforeScript([$expected = 'composer self-update']);

        self::assertCount(1, $this->sut->getBeforeScript());
        self::assertContains($expected, $this->sut->getBeforeScript());

        $this->sut->setBeforeScript([$expected = 'composer install']);

        self::assertCount(1, $this->sut->getBeforeScript());
        self::assertContains($expected, $this->sut->getBeforeScript());
    }
    
    public function test_added_before_script_is_appended_to_list()
    {
        self::assertCount(0, $this->sut->getBeforeScript());

        $this->sut->addBeforeScript($expected = 'composer self-update');

        self::assertCount(1, $this->sut->getBeforeScript());
        self::assertContains($expected, $this->sut->getBeforeScript());

        $this->sut->addBeforeScript($expected = 'composer install');

        self::assertCount(2, $this->sut->getBeforeScript());
        self::assertContains($expected, $this->sut->getBeforeScript());
    }
    
    public function test_can_set_script()
    {
        self::assertCount(0, $this->sut->getScript());

        $this->sut->setScript([$expected = 'vendor/bin/phpunit']);

        self::assertCount(1, $this->sut->getScript());
        self::assertContains($expected, $this->sut->getScript());

        $this->sut->setScript([$expected = 'vendor/bin/behat']);

        self::assertCount(1, $this->sut->getScript());
        self::assertContains($expected, $this->sut->getScript());
    }
    
    public function test_added_scripts_are_appended_to_list()
    {
        self::assertCount(0, $this->sut->getScript());

        $this->sut->addScript($expected = 'vendor/bin/phpunit');

        self::assertCount(1, $this->sut->getScript());
        self::assertContains($expected, $this->sut->getScript());

        $this->sut->addScript($expected = 'vendor/bin/behat');

        self::assertCount(2, $this->sut->getScript());
        self::assertContains($expected, $this->sut->getScript());
    }
    
    public function test_can_set_matrix()
    {
        self::assertInstanceOf(Matrix::class, $this->sut->getMatrix());
        
        $this->sut->setMatrix($expected = new Matrix());
        
        self::assertSame($expected, $this->sut->getMatrix());
    }
}

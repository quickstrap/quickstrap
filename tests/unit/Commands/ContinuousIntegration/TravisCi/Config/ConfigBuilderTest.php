<?php

namespace QuickStrapUnit\Commands\ContinuousIntegration\TravisCi\Config;

use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\Config;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigBuilder;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiCommand;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiQuestionHelper;
use QuickStrap\Helpers\Composer\PackageHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject|TravisCiQuestionHelper */
    private $questionHelper;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|PackageHelper */
    private $packageHelper;

    /** @var  \PHPUnit_Framework_MockObject_MockObject|InputInterface */
    private $input;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|OutputInterface */
    private $output;
    
    /** @var ConfigBuilder */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->input = $this->getMock(InputInterface::class);
        $this->output = $this->getMock(OutputInterface::class);
        $this->packageHelper = $this->getMock(PackageHelper::class);
        $this->questionHelper = $this->getMock(TravisCiQuestionHelper::class, [], [], '', false);

        $this->sut = new ConfigBuilder($this->questionHelper, $this->packageHelper);
    }

    public function test_can_create_config_with_default_answers_from_input()
    {
        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertInstanceOf(Config::class, $actual);
    }

    public function test_a_default_language_is_provided()
    {
        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertContains('php', $actual->getLanguage());
    }

    public function test_selected_php_versions_are_added_to_config()
    {
        $this->questionHelper->method('askWhichPhpVersionsToBuild')
            ->willReturn(['7.0', '5.6']);

        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertContains('7.0', $actual->getPhp());
        self::assertContains('5.6', $actual->getPhp());
    }
    
    public function test_selected_builds_allowed_to_fail_are_added_to_config()
    {
        $this->questionHelper->method('askWhichPhpVersionsToBuild')
            ->willReturn(['7.0', '5.6']);
        
        $this->questionHelper->method('askWhichBuildsAreAllowedToFail')
            ->willReturn(['5.6']);

        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertCount(1, $allowFailures = $actual->getMatrix()->getAllowFailures());
        self::assertArrayKeyHasValue($allowFailures[0], 'php', '5.6');
    }
    
    public function test_composer_install_is_added_to_config()
    {
        $this->questionHelper->method('confirmComposerInstall')
            ->willReturn(true);

        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertContains('composer install --no-interaction', $actual->getBeforeScript());
    }

    public function test_composer_self_update_is_added_to_config()
    {
        $this->questionHelper->method('confirmComposerInstall')
            ->willReturn(true);

        $this->questionHelper->method('confirmComposerSelfUpdate')
            ->willReturn(true);

        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertContains('composer self-update', $actual->getBeforeScript());
    }

    public function test_phpunit_command_added_to_config_if_phpunit_is_installed()
    {
        $this->packageHelper->method('hasPackage')
            ->willReturnMap([
                ['phpunit/phpunit', null, $this->input, $this->output, true],
            ]);
        
        $this->questionHelper->method('confirmRunPhpUnit')
            ->willReturn(true);

        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertContains('vendor/bin/phpunit --coverage-text', $actual->getScript());
    }

    public function test_behat_command_added_to_config_if_phpunit_is_installed()
    {
        $this->packageHelper->method('hasPackage')
            ->willReturnMap([
                ['behat/behat', null, $this->input, $this->output, true],
            ]);

        $this->questionHelper->method('confirmRunBehat')
            ->willReturn(true);

        $actual = $this->sut->createConfig($this->input, $this->output);

        self::assertContains('vendor/bin/behat', $actual->getScript());
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

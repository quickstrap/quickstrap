<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi\Config;

use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiQuestionHelper;
use QuickStrap\Helpers\Composer\PackageHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigBuilder
{
    /** @var  string[] */
    private static $allowedPhpVersions = [
        '7.0',
        '5.6',
        '5.5',
        '5.4',
        '5.3',
        '5.2',
        'hhvm'
    ];

    /** @var  TravisCiQuestionHelper */
    private $questionHelper;

    /** @var PackageHelper */
    private $packageHelper;

    /**
     * ConfigurationBuilder constructor.
     * @param TravisCiQuestionHelper $questionHelper
     * @param PackageHelper $packageHelper
     */
    public function __construct(TravisCiQuestionHelper $questionHelper, PackageHelper $packageHelper)
    {
        $this->questionHelper = $questionHelper;
        $this->packageHelper = $packageHelper;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Config
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function createConfig(InputInterface $input, OutputInterface $output)
    {
        $config = new Config('php');

        // Configure the PHP versions to build.
        $versions = $this->questionHelper->askWhichPhpVersionsToBuild($input, $output, self::$allowedPhpVersions);
        if (is_array($versions)) {
            $config->setPhp($versions);

            // Configure the builds that are allowed to fail for particular PHP versions.
            $allowedToFail = $this->questionHelper->askWhichBuildsAreAllowedToFail($input, $output, $versions);
            if (is_array($allowedToFail)) {
                $config->getMatrix()->setAllowFailures('php', $allowedToFail);
            }
        }

        // Configure installation of dependencies via Composer, and Composer self-update.
        $composerInstall = $this->questionHelper->confirmComposerInstall($input, $output);
        if ($composerInstall) {
            $composerSelfUpdate = $this->questionHelper->confirmComposerSelfUpdate($input, $output);
            if ($composerSelfUpdate) {
                $config->addBeforeScript('composer self-update');
            }

            $config->addBeforeScript('composer install --no-interaction');
        }

        // Configure execution of PHPUnit.
        if ($this->packageHelper->hasPackage('phpunit/phpunit', null, $input, $output) && 
            $this->questionHelper->confirmRunPhpUnit($input, $output)) {
            // TODO Get bin filename from package helper.
            $config->addScript('vendor/bin/phpunit --coverage-text');
        }

        // Configure execution of Behat.
        if ($this->packageHelper->hasPackage('behat/behat', null, $input, $output) &&
            $this->questionHelper->confirmRunBehat($input, $output)) {
            // TODO Get bin filename from package helper.
            $config->addScript('vendor/bin/behat');
        }
        
        return $config;
    }
}

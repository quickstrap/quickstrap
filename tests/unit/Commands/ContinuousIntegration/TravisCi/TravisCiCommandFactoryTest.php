<?php

namespace QuickStrapUnit\Commands\ContinuousIntegration\TravisCi;

use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiCommand;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiCommandFactory;
use QuickStrap\Helpers\Composer\PackageHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

class TravisCiCommandFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_command_can_be_created()
    {
        $actual = TravisCiCommandFactory::createCommand(new HelperSet([
            'question' => $this->getMock(QuestionHelper::class),
            'package' => $this->getMock(PackageHelper::class),
        ]));
        
        self::assertInstanceOf(TravisCiCommand::class, $actual);
    }
}

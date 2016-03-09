<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi;

use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigBuilder;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigWriter;
use QuickStrap\Helpers\Composer\PackageHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

class TravisCiCommandFactory
{
    /**
     * @param HelperSet $helpers
     * @return TravisCiCommand
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public static function createCommand(HelperSet $helpers)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $helpers->get('question');
        
        $travisCiQuestionHelper = new TravisCiQuestionHelper($questionHelper);

        /** @var PackageHelper $packageHelper */
        $packageHelper = $helpers->get('package');
        
        $configBuilder = new ConfigBuilder($travisCiQuestionHelper, $packageHelper);

        $configWriter = new ConfigWriter();

        return new TravisCiCommand($travisCiQuestionHelper, $configBuilder, $configWriter);
    }
}

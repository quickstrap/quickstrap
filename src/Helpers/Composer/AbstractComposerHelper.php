<?php


namespace QuickStrap\Helpers\Composer;


use Composer\Command\Command;
use Composer\Command\InitCommand;
use Composer\Command\RequireCommand;
use Composer\Console\Application as ComposerApplication;
use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractComposerHelper extends Helper
{
    protected function execute(InputInterface $input, OutputInterface $output, Command $command)
    {
        $command->setIO(new ConsoleIO($input, $output, $this->getHelperSet()));

        $application = new ComposerApplication();
        $application->add($command);
        $application->setAutoExit(false);

        return $application->run($input, $output);
    }
}
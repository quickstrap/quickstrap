<?php


namespace QuickStrap\Helpers\Composer;


use Composer\Command\Command;
use Composer\Console\Application as ComposerApplication;
use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractComposerHelper extends Helper
{
    /** @var  ComposerApplication */
    private $application;

    /**
     * AbstractComposerHelper constructor.
     * @param ComposerApplication $application
     */
    public function __construct(ComposerApplication $application = null)
    {
        $this->application = $application ?: new ComposerApplication;
    }


    protected function execute(InputInterface $input, OutputInterface $output, Command $command)
    {
        $command->setIO(new ConsoleIO($input, $output, $this->getHelperSet()));

        $application = $this->application;
        $application->add($command);
        $application->setAutoExit(false);

        return $application->run($input, $output);
    }
}
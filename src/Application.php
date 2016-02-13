<?php


namespace QuickStrap;

use Composer\Command\InitCommand;
use Composer\Command\RequireCommand;
use Composer\Command\ShowCommand;
use QuickStrap\Commands\TestSuites\PhpUnit\PhpUnitCommand;
use QuickStrap\Helpers\Composer\InitHelper;
use QuickStrap\Helpers\Composer\PackageHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use QuickStrap\Helpers\PathHelper;
use QuickStrap\Subscribers\ComposerSetupSubscriber;
use QuickStrap\Subscribers\CwdSubscriber;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends SymfonyApplication
{
    /** @var  EventDispatcher */
    private $eventDispatcher;

    public function getDispatcher()
    {
        return $this->eventDispatcher;
    }


    /**
     * Application constructor.
     */
    public function __construct()
    {
        parent::__construct('quickstrap', '1.0');

        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addSubscriber(new ComposerSetupSubscriber);
        $this->eventDispatcher->addSubscriber(new CwdSubscriber);

        $this->setDispatcher($this->eventDispatcher);

        $this->getDefinition()->addOption(
            new InputOption('project-path',
                null,
                InputOption::VALUE_OPTIONAL,
                'The path to the project',
                getcwd())
        );
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new InitCommand();
        $defaultCommands[] = new RequireCommand();
        $defaultCommands[] = new ShowCommand();

        // test suite commands
        $defaultCommands[] = new PhpUnitCommand();

        return $defaultCommands;
    }

    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        $helperSet->set(new InitHelper());
        $helperSet->set(new RequireHelper());
        $helperSet->set(new PackageHelper());
        $helperSet->set(new PathHelper());

        return $helperSet;
    }

    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        $this->getHelperSet()->setCommand($command);

        return parent::doRunCommand($command, $input, $output);
    }


}
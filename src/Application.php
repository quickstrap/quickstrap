<?php


namespace QuickStrap;

use Composer\Command\CreateProjectCommand;
use Composer\Command\InitCommand;
use Composer\Command\RequireCommand;
use QuickStrap\Commands\TestSuites\PhpUnitCommand;
use QuickStrap\Subscribers\ComposerSetupSubscriber;
use QuickStrap\Subscribers\CwdSubscriber;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputOption;
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

        // give app ability to initialize composer
        $defaultCommands[] = new InitCommand();
        // give app ability to require composer packages
        $defaultCommands[] = new RequireCommand();

        // test suite commands
        $defaultCommands[] = new PhpUnitCommand();

        return $defaultCommands;
    }

}
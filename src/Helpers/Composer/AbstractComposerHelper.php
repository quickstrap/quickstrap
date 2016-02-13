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

    public function initComposer(OutputInterface $output)
    {
        $input = new ArgvInput([
            'composer', 'init'
        ]);
        $input->setInteractive(true);

        /** @var InitCommand $command */
        $command = $this->getHelperSet()->getCommand()->getApplication()->find('init');

        return $this->execute($input, $output, $command);
    }

    public function requirePackage(OutputInterface $output, $package, $version = null, $dev = true)
    {
        $packageArg = sprintf("%s%s", $package, ($version != null ? ':'.$version : null));

        $args = [
            'composer', 'require'
        ];

        if ($dev) {
            $args[] = '--dev';
        }

        $args[] = $packageArg;

        $input = new ArgvInput($args);
        $input->setInteractive(true);

        /** @var RequireCommand $command */
        $command = $this->getHelperSet()->getCommand()->getApplication()->find('require');
        $command->setIO(new ConsoleIO($input, $output, $this->getHelperSet()));

        $application = new ComposerApplication();
        $application->add($command);
        $application->setAutoExit(false);

        return $application->run($input, $output);
    }
}
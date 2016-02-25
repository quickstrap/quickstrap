<?php


namespace QuickStrap\Commands\TestSuites\Behat;


use QuickStrap\Helpers\Composer\PackageHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class BehatCommand extends Command
{
    /** @var  ProcessFactory */
    private $processFactory;

    public function __construct(ProcessFactory $processFactory = null)
    {
        parent::__construct();
        $this->processFactory = $processFactory ?: new ProcessFactory();
    }


    protected function configure()
    {
        parent::configure();

        $this->setName('testsuites:behat')
            ->setDescription('bootstrap behat');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PackageHelper $packageHelper */
        $packageHelper = $this->getHelper('package');

        if($packageHelper->hasPackage('behat/behat', null, $input, $output)) {
            $version = $packageHelper->getPackageVersion('behat/behat', $input, $output);
            $output->writeln(sprintf("Found %s:%s, skipping Behat installation", 'behat/behat', $version));
            return 0;
        }

        $questionHelper = $this->getHelper('question');

        /** @var RequireHelper $requireHelper */
        $requireHelper = $this->getHelper('composer require');

        $question = new Question('What package version of behat do you want to install? [latest]: ', 'latest');
        $version = $questionHelper->ask($input, $output, $question);

        $status = $requireHelper->requirePackage(
            $output,
            'behat/behat',
            ($version == 'latest' ? '' : $version),
            true);

        if ($status !== 0) {
            return $status;
        }

        $process = $this->processFactory->create();
        try {
            $process->mustRun(function($type, $buffer) use($output) {
                $output->write($buffer);
            });
            return 0;
        } catch (\Exception $e) {
            $output->write($e->getMessage());
            return $process->getExitCode();
        }
    }
}
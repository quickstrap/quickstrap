<?php


namespace QuickStrap\Commands\TestSuites;


use QuickStrap\Commands\TestSuites\PhpUnit\ConfigurationFactory;
use QuickStrap\Helpers\Composer\PackageHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class PhpUnitCommand extends Command
{
    protected function configure()
    {
        parent::configure();

        $this->setName('testsuites:phpunit')
            ->setDescription('bootstrap phpunit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');

        $status = $this->installPhpUnit($input, $output, $questionHelper);
        if($status) {
            return $status;
        }

        return $this->bootstrapPhpUnit($input, $output, $questionHelper);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     * @return int|null
     */
    private function installPhpUnit(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper
    )
    {
        /** @var PackageHelper $packageHelper */
        $packageHelper = $this->getHelper('package');
        if ($packageHelper->hasPackage('phpunit/phpunit', null, $input, $output)) {
            $version = $packageHelper->getPackageVersion('phpunit/phpunit', $input, $output);
            $output->writeln(sprintf("Found %s:%s, skipping PHPUnit installation", 'phpunit/phpunit', $version));
            return 0;
        }

        /** @var RequireHelper $requireHelper */
        $requireHelper = $this->getHelper('composer require');

        $question = new Question('What package version of phpunit do you want to install? [latest]: ', 'latest');
        $version = $helper->ask($input, $output, $question);

        $status = $requireHelper->requirePackage(
            $output,
            'phpunit/phpunit',
            ($version == 'latest' ? '' : ':' . $version),
            true);

        return $status;
    }

    private function bootstrapPhpUnit(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        $configPath = sprintf("%s%s%s", getcwd(), DIRECTORY_SEPARATOR, 'phpunit.xml');
        if(file_exists($configPath)) {
            if(! $helper->ask($input,
                $output,
                new ConfirmationQuestion(
                    sprintf("%s already exists, do you want to overwrite it? [yes]: ", $configPath))) ) {
                return 0;
            }
            unlink($configPath);
        }

        $factory = new ConfigurationFactory();
        $phpUnitXml = $factory->create($input, $output, $helper);

        file_put_contents($configPath, $phpUnitXml);
        $output->writeln(sprintf("Wrote phpunit config to %s", $configPath));
        $output->writeln("You may now run tests by executing `vending/bin/phpunit`");
        return 0;
    }


}
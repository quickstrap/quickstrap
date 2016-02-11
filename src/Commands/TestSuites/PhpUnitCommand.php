<?php


namespace QuickStrap\Commands\TestSuites;


use QuickStrap\Commands\TestSuites\PhpUnit\ConfigurationFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

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
        $helper = $this->getHelper('question');

        $status = $this->installPhpUnit($input, $output, $helper);
        if($status) {
            return $status;
        }

        return $this->bootstrapPhpUnit($input, $output, $helper);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     * @return int|null
     */
    private function installPhpUnit(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        // TODO this doesnt work, use composer to check for package
        if(class_exists('\PHPUnit_Framework_TestCase')) {
            $output->writeln("PHPUnit detected, skipping installation.");
            return 0;
        }

        $question = new Question('What package version of phpunit do you want to install? [latest]: ', 'latest');
        $version = $helper->ask($input, $output, $question);

        $command = sprintf("composer require --dev phpunit/phpunit%s", ($version == 'latest' ? '' : ':' . $version));
        $process = new Process($command);
        $process->setTimeout(300);

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
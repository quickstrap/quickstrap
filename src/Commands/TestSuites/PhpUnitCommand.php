<?php


namespace QuickStrap\Commands\TestSuites;


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
        $question = new Question('What relative path should test files be created in? [tests/unit]: ', 'tests/unit');
        $testFolder = ltrim($helper->ask($input, $output, $question), DIRECTORY_SEPARATOR);

        $path = sprintf("%s%s%s", getcwd(), DIRECTORY_SEPARATOR, $testFolder);
        if(file_exists($path)) {
            if(! $helper->ask($input,
                $output,
                new ConfirmationQuestion(sprintf("%s already exists, are you sure you want to continue?", $path))) ) {
                return 0;
            }
        } else {
            mkdir($path, 0777, true);
        }

        $output->writeln(sprintf("Configuring test folder to %s", $testFolder));

$xml = <<<XML
<phpunit
     xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:noNamespaceSchemaLocation="http://schema.phpunit.de/5.2/phpunit.xsd"
     bootstrap="vendor/autoload.php">
    <testsuites>
      <testsuite name="unit">
        <directory>$testFolder</directory>
      </testsuite>
    </testsuites>
</phpunit>
XML;

        $configPath = sprintf("%s%s%s", getcwd(), DIRECTORY_SEPARATOR, 'phpunit.xml');
        if(file_exists($configPath)) {
            if(! $helper->ask($input,
                $output,
                new ConfirmationQuestion(
                    sprintf("%s already exists, do you want to overwrite it? [Y|n]: ", $configPath))) ) {
                return 0;
            }
            unlink($configPath);
        }

        file_put_contents($configPath, $xml);
        $output->writeln(sprintf("Wrote phpunit config to %s", $configPath));
        $output->writeln("You may now run tests by executing vending/bin/phpunit");
        return 0;
    }


}
<?php


namespace QuickStrap\Commands\TestSuites\PhpUnit;


use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion as Confirm;
use Symfony\Component\Console\Question\Question;

class ConfigurationFactory
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     * @param ConfigurationBuilder|null $builder
     * @return string
     */
    public function create(
        InputInterface $input, 
        OutputInterface $output, 
        QuestionHelper $helper, 
        ConfigurationBuilder $builder = null)
    {
        if(! $builder) {
            $builder = new ConfigurationBuilder();
        }

        $bootstrap = $helper->ask($input, $output,
            new Question('relative path to bootstrap file? [vendor/autoload.php]: ', 'vendor/autoload.php'));
        $builder->bootstrap($bootstrap);
        
        if (!$helper->ask($input, $output, new Confirm('backup globals? [yes]: '))) {
            $builder->doNotBackupGlobals();
        }

        if ($helper->ask($input, $output, new Confirm('backup static attributes? [no]: ', false))) {
            $builder->backupStaticAttributes();
        }

        if ($helper->ask($input, $output, new Confirm('cache tokens? [no]: ', false))) {
            $builder->cacheTokens();
        }

        if ($helper->ask($input, $output, new Confirm('output in color? [no]: ', false))) {
            $builder->colors();
        }

        if (!$helper->ask($input, $output, new Confirm('convert errors to exceptions? [yes]: '))) {
            $builder->doNotConvertErrorsToExceptions();
        }

        if (!$helper->ask($input, $output, new Confirm('convert notices to exceptions? [yes]: '))) {
            $builder->doNotConvertNoticesToExceptions();
        }

        if (!$helper->ask($input, $output, new Confirm('convert warnings to exceptions? [yes]: '))) {
            $builder->doNotConvertWarningsToExceptions();
        }

        if ($helper->ask($input, $output, new Confirm('force @covers Annotation? [no]: ', false))) {
            $builder->forceCoversAnnotation();
        }

        if ($helper->ask($input, $output, new Confirm('map test class name to covered class name? [no]: ', false))) {
            $builder->mapTestClassNameToCoveredClassName();
        }

        if ($helper->ask($input, $output, new Confirm('run tests in process isolation? [no]: ', false))) {
            $builder->processIsolation();
        }

        if ($helper->ask($input, $output, new Confirm('stop on error? [no]: ', false))) {
            $builder->stopOnError();
        }

        if ($helper->ask($input, $output, new Confirm('stop on failure? [no]: ', false))) {
            $builder->stopOnFailure();
        }

        if ($helper->ask($input, $output, new Confirm('stop on incomplete? [no]: ', false))) {
            $builder->stopOnIncomplete();
        }

        if ($helper->ask($input, $output, new Confirm('stop on skipped? [no]: ', false))) {
            $builder->stopOnSkipped();
        }

        if ($helper->ask($input, $output, new Confirm('stop on risky? [no]: ', false))) {
            $builder->stopOnRisky();
        }

        if (($timeout = intval($helper->ask($input, $output, new Question('timeout for small tests (seconds)? [1]', 1)))) != 1) {
            $builder->timeoutForSmallTests($timeout);
        }

        if (($timeout = intval($helper->ask($input, $output, new Question('timeout for medium tests (seconds)? [10]', 10)))) != 10) {
            $builder->timeoutForMediumTests($timeout);
        }

        if (($timeout = intval($helper->ask($input, $output, new Question('timeout for large tests (seconds)? [60]', 60)))) != 60) {
            $builder->timeoutForLargeTests($timeout);
        }

        if ($helper->ask($input, $output, new Confirm('verbose? [no]: ', false))) {
            $builder->verbose();
        }

        while($helper->ask($input, $output, new Confirm('add test suite? [yes]: '))) {
            $name = $helper->ask($input, $output, new Question('name of test suite? [unit]: ', 'unit'));
            $directory = $helper->ask($input, $output,
                new Question('relative path to test suite directory? [tests/unit]: ', 'tests/unit'));
            $builder->addTestSuite($name, $directory);
        }
        
        return $builder->__toString();
    }
}
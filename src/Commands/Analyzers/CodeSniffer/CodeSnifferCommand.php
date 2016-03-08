<?php


namespace QuickStrap\Commands\Analyzers\CodeSniffer;


use QuickStrap\Helpers\Composer\PackageHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CodeSnifferCommand extends Command
{
    const PACKAGE_NAME = 'squizlabs/php_codesniffer';

    protected function configure()
    {
        parent::configure();

        $this->setName('analyzers:codesniffer')
            ->setDescription('bootstrap PHP CodeSniffer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PackageHelper $packageHelper */
        $packageHelper = $this->getHelper('package');

        if($packageHelper->hasPackage(static::PACKAGE_NAME, null, $input, $output)) {
            $version = $packageHelper->getPackageVersion(static::PACKAGE_NAME, $input, $output);
            $output->writeln(sprintf("Found %s:%s, skipping CodeSniffer installation", static::PACKAGE_NAME, $version));
            return 0;
        }

        $questionHelper = $this->getHelper('question');

        /** @var RequireHelper $requireHelper */
        $requireHelper = $this->getHelper('composer require');

        $question = new Question('What package version of CodeSniffer do you want to install? [latest]: ', 'latest');
        $version = $questionHelper->ask($input, $output, $question);

        $status = $requireHelper->requirePackage(
            $output,
            static::PACKAGE_NAME,
            ($version == 'latest' ? '' : $version),
            true);

        // todo configure phing task
        return $status;
    }
}
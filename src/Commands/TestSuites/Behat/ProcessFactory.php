<?php


namespace QuickStrap\Commands\TestSuites\Behat;


use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class ProcessFactory
{
    /**
     * @return Process
     */
    public function create()
    {
        $processBuilder = new ProcessBuilder();
        $processBuilder->setPrefix('vendor/bin/behat');
        $processBuilder->setArguments(['--init']);
        return $processBuilder->getProcess();
    }
}
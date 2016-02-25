<?php


namespace QuickStrapUnit\Commands\TestSuites\Behat;


use QuickStrap\Commands\TestSuites\Behat\ProcessFactory;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_returns_a_behat_init_process()
    {
        $factory = new ProcessFactory();
        $process = $factory->create();
        static::assertInstanceOf(Process::class, $process);
        static::assertEquals('vendor/bin/behat --init', $process->getCommandLine());
    }
}

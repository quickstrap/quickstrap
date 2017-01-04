<?php
/**
 * Created by PhpStorm.
 * User: Jeremy
 * Date: 8/20/2016
 * Time: 10:38 AM
 */

namespace Quickstrap;


use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;


class Application extends SymfonyApplication
{
    /** @var  Kernel */
    private $kernel;
    /** @var  bool */
    private $commandsRegistered;

    /**
     * PharCli constructor.
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        parent::__construct('Quickstrap', '0.1.0');

        $this->kernel = $kernel;
    }

    public function get($name)
    {
        $this->registerCommands();

        return parent::get($name);
    }

    public function all($namespace = null)
    {
        $this->registerCommands();

        return parent::all($namespace);
    }

    protected function getDefaultCommands()
    {
        $default = parent::getDefaultCommands();

        $composerCommands = [

        ];

        $default = array_merge($default, $composerCommands);

        return $default;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->kernel->boot();

        $container = $this->kernel->getContainer();

        foreach($this->all() as $command) {
            if ($command instanceof ContainerAwareInterface) {
                $command->setContainer($container);
            }
        }

        $returnCode = parent::doRun($input, $output);

        $this->kernel->shutdown();

        return $returnCode;
    }

    protected function registerCommands()
    {
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->kernel->boot();

        $container = $this->kernel->getContainer();

        foreach ($this->kernel->getBundles() as $bundle) {
            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($this);
            }
        }

        if ($container->hasParameter('console.command.ids')) {
            foreach ($container->getParameter('console.command.ids') as $id) {
                $this->add($container->get($id));
            }
        }
    }
}
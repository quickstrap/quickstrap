<?php

namespace QuickStrap\Commands\ContinuousIntegration\TravisCi;

use Contrib\Component\File\Path;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigBuilder;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigWriter;
use QuickStrap\Helpers\PathHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TravisCiCommand extends Command
{
    /** @var  ConfigBuilder */
    private $configBuilder;
    
    /** @var  ConfigWriter */
    private $configWriter;
    
    /** @var TravisCiQuestionHelper */
    private $questionHelper;

    /**
     * TravisCiCommand constructor.
     * @param TravisCiQuestionHelper $questionHelper
     * @param ConfigBuilder|null $configBuilder
     * @param ConfigWriter $configWriter
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(
        TravisCiQuestionHelper $questionHelper,
        ConfigBuilder $configBuilder,
        ConfigWriter $configWriter
    ) {
        parent::__construct('ci:travis-ci');
        
        $this->configBuilder = $configBuilder;
        $this->configWriter = $configWriter;
        $this->questionHelper = $questionHelper;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('ci:travis-ci');
        
        $this->setDescription('create travis-ci config');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PathHelper $pathHelper */
        $pathHelper = $this->getHelper('path');
        
        $configPath = $pathHelper->getPath('.travis.yml');

        if(file_exists($configPath)) {
            $shouldOverwrite = $this->questionHelper->confirmOverwriteFile(
                $input,
                $output,
                $configPath
            );

            if (!$shouldOverwrite) {
                return 0;
            }
        }
        
        $config = $this->configBuilder->createConfig($input, $output);

        $this->configWriter->toYmlFile($config, $configPath);
        
        return 0;
    }
}

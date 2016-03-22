<?php

namespace QuickStrapUnit\Commands\ContinuousIntegration\TravisCi;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\Config;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigBuilder;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\ConfigWriter;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiCommand;
use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiQuestionHelper;
use QuickStrap\Helpers\PathHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class TravisCiCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject|ConfigBuilder */
    private $configBuilder;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|ConfigWriter */
    private $configWriter;
    
    /** @var  string */
    private $configPath;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $packageHelper;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $pathHelper;
    
    /** @var  vfsStreamDirectory */
    private $applicationPath;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|TravisCiQuestionHelper */
    private $questionHelper;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|InputInterface */
    private $input;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|OutputInterface */
    private $output;
    
    /** @var TravisCiCommand */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->applicationPath = vfsStream::setup('application_path');
        $this->input = $this->getMock(InputInterface::class);
        $this->output = $this->getMock(OutputInterface::class);
        $this->pathHelper = $this->getMock(PathHelper::class);
        $this->questionHelper = $this->getMock(TravisCiQuestionHelper::class, [], [], '', false);
        $this->configBuilder = $this->getMock(ConfigBuilder::class, [], [], '', false);
        $this->configWriter = new ConfigWriter();
        
        $this->configPath = sprintf('%s/.travis.yml', $this->applicationPath->url());
        
        $this->pathHelper->method('getPath')
            ->willReturn($this->configPath);
        
        $this->sut = new TravisCiCommand($this->questionHelper, $this->configBuilder, $this->configWriter);
        $this->sut->setHelperSet(new HelperSet([
            'path' => $this->pathHelper,
        ]));
    }
    
    public function test_write_a_new_file_if_config_does_not_already_exist()
    {
        self::assertFileNotExists($this->configPath);
        
        $this->configBuilder->method('createConfig')
            ->willReturn(new Config());
        
        $actual = $this->sut->run($this->input, $this->output);
        
        self::assertEquals(0, $actual);
        
        self::assertFileExists($this->configPath);
    }
    
    public function test_write_to_existing_file_when_file_already_exists_and_user_confirms_overwrite()
    {
        file_put_contents($this->configPath, $expected = 'StuffAndThings');
        
        $this->questionHelper->method('confirmOverwriteFile')
            ->willReturn(true);
        
        $this->configBuilder->method('createConfig')
            ->willReturn(new Config());

        $actual = $this->sut->run($this->input, $this->output);

        self::assertEquals(0, $actual);

        self::assertFileExists($this->configPath);
        self::assertNotEquals($expected, file_get_contents($this->configPath));
    }
    
    public function test_do_not_write_file_when_file_already_exists_and_user_cancels_overwrite()
    {
        file_put_contents($this->configPath, $expected = 'StuffAndThings');

        $this->questionHelper->method('confirmOverwriteFile')
            ->willReturn(false);

        $this->configBuilder->method('createConfig')
            ->willReturn(new Config());

        $actual = $this->sut->run($this->input, $this->output);

        self::assertEquals(0, $actual);

        self::assertFileExists($this->configPath);
        self::assertEquals($expected, file_get_contents($this->configPath));
    }
}

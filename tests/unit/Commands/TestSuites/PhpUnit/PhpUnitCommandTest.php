<?php


namespace QuickStrapUnit\Commands\TestSuites\PhpUnit;

use Mockery;
use Mockery\MockInterface;
use org\bovigo\vfs\vfsStream;
use QuickStrap\Commands\TestSuites\PhpUnit\ConfigurationFactory;
use QuickStrap\Commands\TestSuites\PhpUnit\PhpUnitCommand;
use QuickStrap\Helpers\Composer\PackageHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use QuickStrap\Helpers\PathHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PhpUnitCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PhpUnitCommand */
    private $sut;
    /** @var  QuestionHelper|MockInterface */
    private $questionMock;
    /** @var  PackageHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $packageMock;
    /** @var  RequireHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $requireMock;
    /** @var  PathHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $pathMock;
    /** @var  InputInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $inputMock;
    /** @var  OutputInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $outputMock;
    /** @var  ConfigurationFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $factoryMock;
    /** @var  string */
    private $configPath;

    protected function setUp()
    {
        parent::setUp();

        $this->inputMock = static::getMock(InputInterface::class);
        $this->outputMock = static::getMock(OutputInterface::class);

        $this->packageMock = static::getMock(PackageHelper::class);
        $this->requireMock = static::getMock(RequireHelper::class);
        $this->pathMock = static::getMock(PathHelper::class);
        $this->questionMock = Mockery::mock(QuestionHelper::class)
            ->makePartial();

        $root = vfsStream::setup('projectDir');
        $this->configPath = $root->url() . '/phpunit.xml';

        $this->pathMock->expects(static::any())
            ->method('getPath')
            ->with('phpunit.xml')
            ->willReturn($this->configPath);

        $this->factoryMock = static::getMockBuilder(ConfigurationFactory::class)
            ->disableProxyingToOriginalMethods()
            ->getMock();

        $helperSet = new HelperSet();
        $helperSet->set($this->questionMock, 'question');
        $helperSet->set($this->packageMock, 'package');
        $helperSet->set($this->requireMock, 'composer require');
        $helperSet->set($this->pathMock, 'path');

        $this->sut = new PhpUnitCommand($this->factoryMock);
        $this->sut->setHelperSet($helperSet);
    }

    public function test_it_will_install_phpunit_if_not_already_present()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with('phpunit/phpunit', null, $this->inputMock, $this->outputMock)
            ->willReturn(false);

        $this->requireMock->expects(static::once())
            ->method('requirePackage')
            ->with($this->outputMock, 'phpunit/phpunit', null, true)
            ->willReturn(0);

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_not_install_phpunit_if_already_present()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with('phpunit/phpunit', null, $this->inputMock, $this->outputMock)
            ->willReturn(true);

        $this->requireMock->expects(static::never())
            ->method('requirePackage');

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_generate_phpunit_configuration_if_not_already_present()
    {
        $this->factoryMock->expects(static::any())
            ->method('create')
            ->with($this->inputMock, $this->outputMock, $this->questionMock)
            ->willReturn($config = '<phpunit/>');

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
        static::assertEquals($config, file_get_contents($this->configPath));
    }

    public function test_it_will_generate_phpunit_configuration_after_overwrite_confirmation()
    {
        file_put_contents($this->configPath, '<phpunit>old</phpunit>');

        $this->questionMock
            ->shouldReceive('ask')
            ->once()
            ->with(
                $this->inputMock,
                $this->outputMock,
                Mockery::on(function(Question $question){
                    if (strpos($question->getQuestion(), 'already exists, do you want to overwrite it?') !== false) {
                        return true;
                    }
                    return false;
                })
            )->andReturn(true);

        $this->factoryMock->expects(static::any())
            ->method('create')
            ->with($this->inputMock, $this->outputMock, $this->questionMock)
            ->willReturn($config = '<phpunit/>');

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
        static::assertEquals($config, file_get_contents($this->configPath));
    }

    public function test_it_will_not_generate_phpunit_configuration_after_overwrite_confirmation()
    {
        file_put_contents($this->configPath, '<phpunit>old</phpunit>');

        $this->factoryMock->expects(static::any())
            ->method('create')
            ->with($this->inputMock, $this->outputMock, $this->questionMock)
            ->willReturn($config = '<phpunit/>');

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
        static::assertEquals($config, file_get_contents($this->configPath));
    }
}

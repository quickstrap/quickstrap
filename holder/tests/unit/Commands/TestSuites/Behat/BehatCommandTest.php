<?php


namespace QuickStrapUnit\Commands\TestSuites\Behat;


use Mockery;
use Mockery\MockInterface;
use QuickStrap\Commands\TestSuites\Behat\BehatCommand;
use QuickStrap\Commands\TestSuites\Behat\ProcessFactory;
use QuickStrap\Helpers\Composer\PackageHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

class BehatCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var  BehatCommand */
    private $sut;
    /** @var  QuestionHelper|MockInterface */
    private $questionMock;
    /** @var  PackageHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $packageMock;
    /** @var  RequireHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $requireMock;
    /** @var  InputInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $inputMock;
    /** @var  OutputInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $outputMock;
    /** @var  ProcessFactory|\PHPUnit_Framework_MockObject_MockObject */
    private $factoryMock;
    /** @var  Process|\PHPUnit_Framework_MockObject_MockObject */
    private $processMock;

    protected function setUp()
    {
        parent::setUp();

        $this->inputMock = static::getMock(InputInterface::class);
        $this->outputMock = static::getMock(OutputInterface::class);

        $this->packageMock = static::getMock(PackageHelper::class);
        $this->requireMock = static::getMock(RequireHelper::class);
        $this->questionMock = Mockery::mock(QuestionHelper::class)
            ->makePartial();

        $this->processMock = static::getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factoryMock = static::getMockBuilder(ProcessFactory::class)
            ->disableProxyingToOriginalMethods()
            ->getMock();
        $this->factoryMock->expects(static::any())
            ->method('create')
            ->willReturn($this->processMock);

        $helperSet = new HelperSet();
        $helperSet->set($this->questionMock, 'question');
        $helperSet->set($this->packageMock, 'package');
        $helperSet->set($this->requireMock, 'composer require');

        $this->sut = new BehatCommand($this->factoryMock);
        $this->sut->setHelperSet($helperSet);
    }


    public function test_it_will_install_behat_if_not_present()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with('behat/behat', null, $this->inputMock, $this->outputMock)
            ->willReturn(false);

        $this->requireMock->expects(static::once())
            ->method('requirePackage')
            ->with($this->outputMock, 'behat/behat', null, true)
            ->willReturn(0);

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_install_specified_version_of_behat_if_not_present()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with('behat/behat', null, $this->inputMock, $this->outputMock)
            ->willReturn(false);

        $this->questionMock
            ->shouldReceive('ask')
            ->once()
            ->with(
                $this->inputMock,
                $this->outputMock,
                Mockery::on(function(Question $question){
                    if (strpos($question->getQuestion(), 'What package version of behat do you want to install? [latest]: ') !== false) {
                        return true;
                    }
                    return false;
                })
            )->andReturn($version = '>=2.5');

        $this->requireMock->expects(static::once())
            ->method('requirePackage')
            ->with($this->outputMock, 'behat/behat', $version, true)
            ->willReturn(0);

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_not_install_behat_if_already_present()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with('behat/behat', null, $this->inputMock, $this->outputMock)
            ->willReturn(true);

        $this->requireMock->expects(static::never())
            ->method('requirePackage');

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_initialize_behat()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with('behat/behat', null, $this->inputMock, $this->outputMock)
            ->willReturn(false);

        $this->requireMock->expects(static::any())
            ->method('requirePackage')
            ->willReturn(0);

        $this->processMock->expects(static::once())
            ->method('mustRun')
            ->willReturn(0);

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_not_try_to_initialize_behat_if_installation_fails()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with('behat/behat', null, $this->inputMock, $this->outputMock)
            ->willReturn(false);

        $this->requireMock->expects(static::any())
            ->method('requirePackage')
            ->willReturn($status = 7);

        $this->processMock->expects(static::never())
            ->method('mustRun');

        static::assertEquals($status, $this->sut->run($this->inputMock, $this->outputMock));
    }
}

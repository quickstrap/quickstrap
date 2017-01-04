<?php


namespace QuickStrapUnit\Commands\Analyzers\CodeSniffer;


use Mockery;
use Mockery\MockInterface;
use QuickStrap\Commands\Analyzers\CodeSniffer\CodeSnifferCommand;
use QuickStrap\Helpers\Composer\PackageHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CodeSnifferCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var  CodeSnifferCommand */
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


    protected function setUp()
    {
        parent::setUp();

        $this->inputMock = static::getMock(InputInterface::class);
        $this->outputMock = static::getMock(OutputInterface::class);

        $this->packageMock = static::getMock(PackageHelper::class);
        $this->requireMock = static::getMock(RequireHelper::class);
        $this->questionMock = Mockery::mock(QuestionHelper::class)
            ->makePartial();



        $helperSet = new HelperSet();
        $helperSet->set($this->questionMock, 'question');
        $helperSet->set($this->packageMock, 'package');
        $helperSet->set($this->requireMock, 'composer require');

        $this->sut = new CodeSnifferCommand();
        $this->sut->setHelperSet($helperSet);
    }

    public function test_it_will_not_install_code_sniffer_if_already_present()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with(CodeSnifferCommand::PACKAGE_NAME, null, $this->inputMock, $this->outputMock)
            ->willReturn(true);

        $this->requireMock->expects(static::never())
            ->method('requirePackage');

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_install_code_sniffer_if_not_present()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with(CodeSnifferCommand::PACKAGE_NAME, null, $this->inputMock, $this->outputMock)
            ->willReturn(false);

        $this->requireMock->expects(static::once())
            ->method('requirePackage')
            ->with($this->outputMock, CodeSnifferCommand::PACKAGE_NAME, '', true)
            ->willReturn(0);

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));
    }

    public function test_it_will_install_code_sniffer_with_specified_version()
    {
        $this->packageMock->expects(static::any())
            ->method('hasPackage')
            ->with(CodeSnifferCommand::PACKAGE_NAME, null, $this->inputMock, $this->outputMock)
            ->willReturn(false);

        $this->questionMock
            ->shouldReceive('ask')
            ->once()
            ->with(
                $this->inputMock,
                $this->outputMock,
                Mockery::on(function(Question $question){
                    if (strpos($question->getQuestion(), 'What package version of CodeSniffer do you want to install? [latest]: ') !== false) {
                        return true;
                    }
                    return false;
                })
            )->andReturn($version = 'v1.0');

        $this->requireMock->expects(static::once())
            ->method('requirePackage')
            ->with($this->outputMock, CodeSnifferCommand::PACKAGE_NAME, $version, true)
            ->willReturn(0);

        static::assertEquals(0, $this->sut->run($this->inputMock, $this->outputMock));

    }
}

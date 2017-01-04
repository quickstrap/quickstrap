<?php


namespace QuickStrapUnit\Helpers\Composer;


use Composer\Console\Application;
use QuickStrap\Helpers\Composer\AbstractComposerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;

abstract class AbstractComposerHelperTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var  AbstractComposerHelper */
    protected $sut;
    /** @var  Application|\PHPUnit_Framework_MockObject_MockObject */
    protected $application;

    abstract protected function getSut();

    protected function setUp()
    {
        parent::setUp();

        $this->application = static::getMock(Application::class);

        $this->sut = $this->getSut();

        $command = static::getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command->expects(static::any())
            ->method('getApplication')
            ->willReturn($this->application);

        $command->expects(static::any())
            ->method('getName')
            ->willReturn('mock command');

        $helperSet = new HelperSet();
        $helperSet->setCommand($command);
        $this->sut->setHelperSet($helperSet);
        $this->application->setHelperSet($helperSet);
    }

}

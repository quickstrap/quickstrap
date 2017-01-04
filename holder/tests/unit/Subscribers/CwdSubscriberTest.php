<?php

namespace QuickStrapUnit\Subscribers;

use QuickStrap\Helpers\PathHelper;
use QuickStrap\Subscribers\CwdSubscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class CwdSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var  EventDispatcher */
    private $dispatcher;
    /** @var  CwdSubscriber */
    private $sut;
    /** @var  string */
    private $projectPath;
    /** @var Command|\PHPUnit_Framework_MockObject_MockObject $command */
    private $command;
    /** @var InputInterface|\PHPUnit_Framework_MockObject_MockObject $input */
    private $input;
    /** @var OutputInterface $output */
    private $output;
    /** @var  PathHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $pathHelper;
    protected function setUp()
    {
        parent::setUp();

        $this->dispatcher = new EventDispatcher();

        $this->sut = new CwdSubscriber();
        $this->dispatcher->addSubscriber($this->sut);

        $this->projectPath = sys_get_temp_dir();

        $this->pathHelper = $this->getMock(PathHelper::class);

        $this->command = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command->expects(static::any())
            ->method('getHelper')
            ->with('path')
            ->willReturn($this->pathHelper);

        $this->input = $input = $this->getMock(InputInterface::class);
        $input->expects(static::any())
            ->method('getOption')
            ->willReturn($this->projectPath);

        $this->output = $this->getMock(OutputInterface::class);
    }

    public function test_working_dir_changes_with_command_and_terminate_events()
    {
        $oldCwd = getcwd();

        $this->pathHelper->expects(static::once())
            ->method('setProjectPath')
            ->with($this->projectPath);

        $commandEvent = new ConsoleCommandEvent(
            $this->command,
            $this->input,
            $this->output
        );

        $this->dispatcher->dispatch(ConsoleEvents::COMMAND, $commandEvent);

        static::assertEquals($this->projectPath, getcwd());

        $terminateEvent = new ConsoleTerminateEvent(
            $this->command,
            $this->input,
            $this->output,
            0
        );

        $this->dispatcher->dispatch(ConsoleEvents::TERMINATE, $terminateEvent);

        static::assertEquals($oldCwd, getcwd());

    }
}

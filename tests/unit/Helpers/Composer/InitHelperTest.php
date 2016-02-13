<?php


namespace QuickStrapUnit\Helpers\Composer;


use Composer\Command\InitCommand;
use QuickStrap\Helpers\Composer\AbstractComposerHelper;
use QuickStrap\Helpers\Composer\InitHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class InitHelperTest extends AbstractComposerHelperTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function test_init_composer_executes_command_and_returns_exist_status()
    {
        /** @var OutputInterface $output */
        $output = static::getMock(OutputInterface::class);

        $this->application->expects(static::any())
            ->method('find')
            ->with('init')
            ->willReturn($this->getMockBuilder(InitCommand::class)
                ->disableOriginalConstructor()
                ->getMock()
            );

        $this->application->expects(static::once())
            ->method('run')
            ->with(
                static::isInstanceOf(ArgvInput::class),
                $output
            )->willReturn($status = 3);

        static::assertEquals($status, $this->sut->initComposer($output));
    }

    /** @return AbstractComposerHelper */
    protected function getSut()
    {
        return new InitHelper($this->application);
    }
}

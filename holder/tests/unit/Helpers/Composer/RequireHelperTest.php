<?php


namespace QuickStrapUnit\Helpers\Composer;


use Composer\Command\RequireCommand;
use QuickStrap\Helpers\Composer\AbstractComposerHelper;
use QuickStrap\Helpers\Composer\RequireHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class RequireHelperTest extends AbstractComposerHelperTestCase
{

    /** @return AbstractComposerHelper */
    protected function getSut()
    {
        return new RequireHelper($this->application);
    }

    public function test_require_package_executes_command_and_returns_exit_status()
    {
        /** @var OutputInterface $output */
        $output = static::getMock(OutputInterface::class);

        $package = 'my/package';
        $version = '^5.0';
        $dev = true;

        $this->application->expects(static::any())
            ->method('find')
            ->with('require')
            ->willReturn($this->getMockBuilder(RequireCommand::class)
                ->disableOriginalConstructor()
                ->getMock()
            );

        $this->application->expects(static::once())
            ->method('run')
            ->with(
                static::isInstanceOf(ArgvInput::class),
                $output
            )->willReturn($status = 3);

        static::assertEquals(
            $status,
            $this->sut->requirePackage(
                $output,
                $package,
                $version,
                $dev
            ));
    }
}

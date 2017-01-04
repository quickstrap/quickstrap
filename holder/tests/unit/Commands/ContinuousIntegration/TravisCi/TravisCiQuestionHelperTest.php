<?php

namespace QuickStrapUnit\Commands\ContinuousIntegration\TravisCi;

use QuickStrap\Commands\ContinuousIntegration\TravisCi\TravisCiQuestionHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TravisCiQuestionHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject|QuestionHelper */
    private $questionHelper;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|InputInterface */
    private $input;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject|OutputInterface */
    private $output;
    
    /** @var TravisCiQuestionHelper */
    private $sut;

    protected function setUp()
    {
        parent::setUp();
        
        $this->questionHelper = $this->getMock(QuestionHelper::class);
        $this->input = $this->getMock(InputInterface::class);
        $this->output = $this->getMock(OutputInterface::class);

        $this->sut = new TravisCiQuestionHelper($this->questionHelper);
    }
    
    public function test_confirm_overwrite_config_file()
    {
        $this->questionHelper->method('ask')->willReturn($expected = 'no');
        
        $actual = $this->sut->confirmOverwriteFile(
            $this->input,
            $this->output,
            '.travis.yml'
        );
        
        self::assertEquals($expected, $actual);
    }
    
    public function test_ask_which_php_version_to_build()
    {
        $this->questionHelper->method('ask')->willReturn($expected = '7.0');
        
        $actual = $this->sut->askWhichPhpVersionToBuild($this->input, $this->output, ['7.0']);
        
        self::assertEquals($expected, $actual);
    }
    
    public function test_ask_which_php_version_is_allowed_to_fail()
    {
        $this->questionHelper->method('ask')->willReturn($expected = '7.0');
        
        $actual = $this->sut->askWhichBuildIsAllowedToFail($this->input, $this->output, ['7.0']);
        
        self::assertEquals($expected, $actual);
    }
    
    public function test_confirm_composer_self_update()
    {
        $this->questionHelper->method('ask')->willReturn($expected = 'y');
        
        $actual = $this->sut->confirmComposerSelfUpdate($this->input, $this->output);
        
        self::assertEquals($expected, $actual);
    }
    
    public function test_confirm_composer_install()
    {
        $this->questionHelper->method('ask')->willReturn($expected = 'y');
        
        $actual = $this->sut->confirmComposerInstall($this->input, $this->output);
        
        self::assertEquals($expected, $actual);
    }
    
    public function test_confirm_run_phpunit()
    {
        $this->questionHelper->method('ask')->willReturn($expected = 'y');
        
        $actual = $this->sut->confirmRunPhpUnit($this->input, $this->output);
        
        self::assertEquals($expected, $actual);
    }
    
    public function test_confirm_run_behat()
    {
        $this->questionHelper->method('ask')->willReturn($expected = 'y');
        
        $actual = $this->sut->confirmRunBehat($this->input, $this->output);
        
        self::assertEquals($expected, $actual);
    }
    
    public function test_loop_until_empty_answer_on_which_php_versions_to_build()
    {
        $this->questionHelper->method('ask')
            ->willReturnOnConsecutiveCalls('7.0', '5.6', null);
        
        $actual = $this->sut->askWhichPhpVersionsToBuild($this->input, $this->output, [
            '7.0',
            '5.6',
            '5.5',
        ]);
        
        self::assertInternalType('array', $actual);
        self::assertContains('7.0', $actual);
        self::assertContains('5.6', $actual);
    }
    
    public function test_loop_until_valid_answer_on_which_php_versions_to_build()
    {
        $this->questionHelper->method('ask')
            ->willReturnOnConsecutiveCalls('6.0', 'asdf', '7.0', null);
        
        $actual = $this->sut->askWhichPhpVersionsToBuild($this->input, $this->output, [
            '7.0',
            '5.6',
            '5.5',
        ]);
        
        self::assertInternalType('array', $actual);
        self::assertContains('7.0', $actual);
    }
    
    public function test_loop_until_empty_answer_on_which_builds_can_fail()
    {
        $this->questionHelper->method('ask')
            ->willReturnOnConsecutiveCalls('7.0', '5.6', null);
        
        $actual = $this->sut->askWhichBuildsAreAllowedToFail($this->input, $this->output, [
            '7.0',
            '5.6',
            '5.5',
        ]);
        
        self::assertInternalType('array', $actual);
        self::assertContains('7.0', $actual);
        self::assertContains('5.6', $actual);
    }
    
    public function test_loop_until_valid_answer_on_which_builds_can_fail()
    {
        $this->questionHelper->method('ask')
            ->willReturnOnConsecutiveCalls('6.0', 'asdf', '7.0', null);
        
        $actual = $this->sut->askWhichBuildsAreAllowedToFail($this->input, $this->output, [
            '7.0',
            '5.6',
            '5.5',
        ]);
        
        self::assertInternalType('array', $actual);
        self::assertContains('7.0', $actual);
    }
}

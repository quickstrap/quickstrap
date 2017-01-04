<?php


namespace QuickStrapUnit\Commands\TestSuites\PhpUnit;


use FluidXml\FluidXml;
use Mockery;
use Mockery\MockInterface;
use QuickStrap\Commands\TestSuites\PhpUnit\ConfigurationFactory;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  InputInterface */
    private $mockInput;
    /** @var  OutputInterface */
    private $mockOutput;
    /** @var  QuestionHelper|MockInterface */
    private $mockHelper;
    /** @var  ConfigurationFactory */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->mockInput = $this->getMock(InputInterface::class);
        $this->mockOutput= $this->getMock(OutputInterface::class);
        $this->mockHelper = Mockery::mock(QuestionHelper::class);

        $this->sut = new ConfigurationFactory();
    }

    /**
     * @param string $question
     * @param mixed $answer
     */
    private function mock_question_helper($question, $answer)
    {
        $this->mockHelper->shouldReceive('ask')
            ->with($this->mockInput, $this->mockOutput, Mockery::on(function(Question $questionArg) use ($question) {
                return $questionArg->getQuestion() == $question;
            }))
            ->andReturn($answer, null);
    }

    /**
     * @return string
     */
    private function call_create_on_sut()
    {
        // low priority catchall
        $this->mockHelper->shouldReceive('ask')
            ->withAnyArgs()
            ->andReturn();


        return $this->sut->create($this->mockInput, $this->mockOutput, $this->mockHelper);
    }

    /**
     * @dataProvider data_provider
     * @param array $questionsAndAnswers
     * @param string $xpath
     * @param int $expectedCount
     */
    public function test_factory_question(array $questionsAndAnswers, $xpath, $expectedCount)
    {
        foreach ($questionsAndAnswers as $question => $answer) {
            $this->mock_question_helper($question, $answer);
        }

        $xml = $this->call_create_on_sut();

        $fluidXml = FluidXml::load($xml);
        $results = $fluidXml->query($xpath);
        self::assertCount($expectedCount, $results);
    }

    public function data_provider()
    {
        return [
            'bootstrap' => [
                ['relative path to bootstrap file? [vendor/autoload.php]: ' => 'expected/path'],
                "/phpunit[@bootstrap='expected/path']",
                1
            ],
            'backup globals on' => [
                ['backup globals? [yes]: ' => true],
                "/phpunit[@backupGlobals]",
                0
            ],
            'backup globals off' => [
                ['backup globals? [yes]: ' => false],
                "/phpunit[@backupGlobals='false']",
                1
            ],
            'backup static attributes off' => [
                ['backup static attributes? [no]: ' => false],
                "/phpunit[@backupStaticAttributes]",
                0
            ],
            'backup static attributes on' => [
                ['backup static attributes? [no]: ' => true],
                "/phpunit[@backupStaticAttributes='true']",
                1
            ],
            'cache tokens off' => [
                ['cache tokens? [no]: ' => false],
                "/phpunit[@cacheTokens]",
                0
            ],
            'cache tokens on' => [
                ['cache tokens? [no]: ' => true],
                "/phpunit[@cacheTokens='true']",
                1
            ],
            'colors off' => [
                ['output in color? [no]: ' => false],
                "/phpunit[@colors]",
                0
            ],
            'colors on' => [
                ['output in color? [no]: ' => true],
                "/phpunit[@colors='true']",
                1
            ],
            'convert errors to exceptions on' => [
                ['convert errors to exceptions? [yes]: ' => true],
                "/phpunit[@convertErrorsToExceptions]",
                0
            ],
            'convert errors to exceptions off' => [
                ['convert errors to exceptions? [yes]: ' => false],
                "/phpunit[@convertErrorsToExceptions='false']",
                1
            ],
            'convert notices to exceptions on' => [
                ['convert notices to exceptions? [yes]: ' => true],
                "/phpunit[@convertNoticesToExceptions]",
                0
            ],
            'convert notices to exceptions off' => [
                ['convert notices to exceptions? [yes]: ' => false],
                "/phpunit[@convertNoticesToExceptions='false']",
                1
            ],
            'convert warnings to exceptions on' => [
                ['convert warnings to exceptions? [yes]: ' => true],
                "/phpunit[@convertWarningsToExceptions]",
                0
            ],
            'convert warnings to exceptions off' => [
                ['convert warnings to exceptions? [yes]: ' => false],
                "/phpunit[@convertWarningsToExceptions='false']",
                1
            ],
            'force @covers annotation off' => [
                ['force @covers Annotation? [no]: ' => false],
                "/phpunit[@forceCoversAnnotation]",
                0
            ],
            'force @covers annotation on' => [
                ['force @covers Annotation? [no]: ' => true],
                "/phpunit[@forceCoversAnnotation='true']",
                1
            ],
            'map test class name to covered class name off' => [
                ['map test class name to covered class name? [no]: ' => false],
                "/phpunit[@mapTestClassNameToCoveredClassName]",
                0
            ],
            'map test class name to covered class name on' => [
                ['map test class name to covered class name? [no]: ' => true],
                "/phpunit[@mapTestClassNameToCoveredClassName='true']",
                1
            ],
            'run tests in process isolation off' => [
                ['run tests in process isolation? [no]: ' => false],
                "/phpunit[@processIsolation]",
                0
            ],
            'run tests in process isolation on' => [
                ['run tests in process isolation? [no]: ' => true],
                "/phpunit[@processIsolation='true']",
                1
            ],
            'stop on error off' => [
                ['stop on error? [no]: ' => false],
                "/phpunit[@stopOnError]",
                0
            ],
            'stop on error on' => [
                ['stop on error? [no]: ' => true],
                "/phpunit[@stopOnError='true']",
                1
            ],
            'stop on failure off' => [
                ['stop on failure? [no]: ' => false],
                "/phpunit[@stopOnFailure]",
                0
            ],
            'stop on failure on' => [
                ['stop on failure? [no]: ' => true],
                "/phpunit[@stopOnFailure='true']",
                1
            ],
            'stop on incomplete off' => [
                ['stop on incomplete? [no]: ' => false],
                "/phpunit[@stopOnIncomplete]",
                0
            ],
            'stop on incomplete on' => [
                ['stop on incomplete? [no]: ' => true],
                "/phpunit[@stopOnIncomplete='true']",
                1
            ],
            'stop on skipped off' => [
                ['stop on skipped? [no]: ' => false],
                "/phpunit[@stopOnSkipped]",
                0
            ],
            'stop on skipped on' => [
                ['stop on skipped? [no]: ' => true],
                "/phpunit[@stopOnSkipped='true']",
                1
            ],
            'stop on risky off' => [
                ['stop on risky? [no]: ' => false],
                "/phpunit[@stopOnRisky]",
                0
            ],
            'stop on risky on' => [
                ['stop on risky? [no]: ' => true],
                "/phpunit[@stopOnRisky='true']",
                1
            ],
            'timeout for small tests default' => [
                ['timeout for small tests (seconds)? [1]' => 1],
                "/phpunit[@timeoutForSmallTests]",
                0
            ],
            'timeout for small tests custom' => [
                ['timeout for small tests (seconds)? [1]' => 10],
                "/phpunit[@timeoutForSmallTests='10']",
                1
            ],
            'timeout for medium tests default' => [
                ['timeout for medium tests (seconds)? [10]' => 10],
                "/phpunit[@timeoutForMediumTests]",
                0
            ],
            'timeout for medium tests custom' => [
                ['timeout for medium tests (seconds)? [10]' => 100],
                "/phpunit[@timeoutForMediumTests='100']",
                1
            ],
            'timeout for large tests default' => [
                ['timeout for large tests (seconds)? [60]' => 60],
                "/phpunit[@timeoutForLargeTests]",
                0
            ],
            'timeout for large tests custom' => [
                ['timeout for large tests (seconds)? [60]' => 100],
                "/phpunit[@timeoutForLargeTests='100']",
                1
            ],
            'verbose off' => [
                ['verbose? [no]: ' => false],
                "/phpunit[@verbose]",
                0
            ],
            'verbose on' => [
                ['verbose? [no]: ' => true],
                "/phpunit[@verbose='true']",
                1
            ],
            'no tests suites' => [
                ['add test suite? [yes]: ' => false],
                "/phpunit/testsuites",
                0
            ],
            'with test suite' => [
                [
                    'add test suite? [yes]: ' => true,
                    'name of test suite? [unit]: ' => 'my test suite',
                    'relative path to test suite directory? [tests/unit]: ' => 'path/to/my/tests',
                ],
                "/phpunit/testsuites/testsuite[@name='my test suite']/directory[text()='path/to/my/tests']",
                1
            ],
        ];
    }
}

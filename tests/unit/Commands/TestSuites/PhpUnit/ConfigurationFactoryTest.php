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
            ->andReturn($answer);
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
        ];
        /**
         * convertNoticesToExceptions
         * convertWarningsToExceptions
         * forceCoversAnnotation
         * mapTestClassNameToCoveredClassName
         * processIsolation
         * stopOnError
         * stopOnFailure
         * stopOnIncomplete
         * stopOnSkipped
         * stopOnRisky
         * timeoutForSmallTests
         * timeoutForMediumTests
         * timeoutForLargeTests
         * verbose
         */
    }
}

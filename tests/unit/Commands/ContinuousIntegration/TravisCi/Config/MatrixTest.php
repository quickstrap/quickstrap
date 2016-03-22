<?php

namespace QuickStrapUnit\Commands\ContinuousIntegration\TravisCi\Config\Config;

use QuickStrap\Commands\ContinuousIntegration\TravisCi\Config\Matrix;

class MatrixTest extends \PHPUnit_Framework_TestCase
{
    /** @var Matrix */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->sut = new Matrix();
    }

    public function test_builds_allowed_to_fail_can_be_set()
    {
        self::assertCount(0, $this->sut->getAllowFailures());

        $this->sut->setAllowFailures('php', ['7.0']);

        self::assertCount(1, $allowFailures = $this->sut->getAllowFailures());
        self::assertArrayKeyHasValue($allowFailures[0], 'php', '7.0');

        $this->sut->setAllowFailures('php', [$build = '5.6']);

        self::assertCount(1, $allowFailures = $this->sut->getAllowFailures());
        self::assertArrayKeyHasValue($allowFailures[0], 'php', '5.6');
    }

    public function test_builds_allowed_to_fail_can_be_appended_to_list()
    {
        self::assertCount(0, $this->sut->getAllowFailures());

        $this->sut->addAllowFailure('php', '7.0');

        self::assertCount(1, $allowFailures = $this->sut->getAllowFailures());
        self::assertArrayKeyHasValue($allowFailures[0], 'php', '7.0');

        $this->sut->addAllowFailure('php', '5.6');

        self::assertCount(2, $allowFailures = $this->sut->getAllowFailures());
        self::assertArrayKeyHasValue($allowFailures[0], 'php', '7.0');
        self::assertArrayKeyHasValue($allowFailures[1], 'php', '5.6');
    }

    public function test_ignore_duplicate_build_allowed_to_fail()
    {
        self::assertCount(0, $this->sut->getAllowFailures());

        $this->sut->setAllowFailures('php', ['7.0']);

        self::assertCount(1, $allowFailures = $this->sut->getAllowFailures());
        self::assertArrayKeyHasValue($allowFailures[0], 'php', '7.0');

        $this->sut->addAllowFailure('php', '7.0');

        self::assertCount(1, $this->sut->getAllowFailures());
        self::assertArrayKeyHasValue($allowFailures[0], 'php', '7.0');
    }

    /**
     * @param mixed[] $array
     * @param mixed $key
     * @param mixed $value
     */
    private static function assertArrayKeyHasValue($array, $key, $value)
    {
        self::assertArrayHasKey($key, $array);
        self::assertEquals($value, $array[$key]);
    }
}

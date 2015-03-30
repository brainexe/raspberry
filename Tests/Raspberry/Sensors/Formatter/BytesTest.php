<?php

namespace Tests\Raspberry\Sensors\Formatter;

use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Sensors\Formatter\Bytes;

/**
 * @covers Raspberry\Sensors\Formatter\Bytes
 */
class BytesTest extends TestCase
{

    /**
     * @var Bytes
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Bytes();
    }

    /**
     * @dataProvider provideValues
     * @param mixed $value
     * @param string $expected
     */
    public function testFormatValue($value, $expected)
    {
        $actual = $this->subject->formatValue($value);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider provideEspeak
     * @param string $value
     * @param string $expected
     */
    public function testGetEspeakText($value, $expected)
    {
        $actual = $this->subject->getEspeakText($value);

        $this->assertEquals($expected, $actual);
    }

    public function testGetType()
    {
        $actualResult = $this->subject->getType();
        $this->assertEquals(Bytes::TYPE, $actualResult);
    }

    /**
     * @return array[]
     */
    public function provideEspeak()
    {
        return [
            [1000000, "1MB"],
            [1234000, "1MB"],
        ];
    }

    /**
     * @return array[]
     */
    public function provideValues()
    {

        return [
            [1000000, "1MB"],
            [1234000, "1MB"],
        ];
    }
}

<?php

namespace Tests\Homie\Sensors\Sensors;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\BMP085Barometer;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\BMP085Barometer
 */
class BMP085BarometerTest extends TestCase
{

    /**
     * @var BMP085Barometer
     */
    private $subject;

    /**
     * @var ClientInterface|MockObject
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->getMock(ClientInterface::class);

        $this->subject = new BMP085Barometer($this->client);
    }

    public function testIsSupported()
    {
        $parameter = 'not_exiting_file';
        $output    = new DummyOutput();
        $actual    = $this->subject->isSupported($parameter, $output);
        $this->assertFalse($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}
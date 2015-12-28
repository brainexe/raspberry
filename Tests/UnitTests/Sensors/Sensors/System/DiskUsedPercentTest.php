<?php

namespace Tests\Homie\Sensors\Sensors\System;

use Homie\Client\ClientInterface;
use Homie\Sensors\Definition;
use Homie\Sensors\Sensors\System\DiskUsedPercent;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @covers Homie\Sensors\Sensors\System\DiskUsedPercent
 */
class DiskUsedPercentTest extends TestCase
{

    /**
     * @var DiskUsedPercent
     */
    private $subject;

    /**
     * @var MockObject|ClientInterface
     */
    private $client;

    public function setUp()
    {
        $this->client = $this->getMock(ClientInterface::class);
        $this->subject = new DiskUsedPercent($this->client);
    }

    public function testGetSensorType()
    {
        $actual = $this->subject->getSensorType();
        $this->assertEquals(DiskUsedPercent::TYPE, $actual);
    }

    public function testGetValueInvalid()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn('ads');

        $sensor = new SensorVO();
        $actual = $this->subject->getValue($sensor);

        $this->assertNull($actual);
    }

    public function testGetValue()
    {
        $this->client
            ->expects($this->once())
            ->method('executeWithReturn')
            ->willReturn('foo bar 12%');

        $sensor = new SensorVO();
        $actual = $this->subject->getValue($sensor);

        $this->assertEquals(12, $actual);
    }

    public function testIsSupported()
    {
        $sensor = new SensorVO();

        $output = new DummyOutput();
        $actual = $this->subject->isSupported($sensor, $output);
        $this->assertTrue($actual);
    }

    public function testGetDefinition()
    {
        $actual = $this->subject->getDefinition();
        $this->assertInstanceOf(Definition::class, $actual);
    }
}

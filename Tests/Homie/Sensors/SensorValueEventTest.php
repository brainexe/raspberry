<?php

namespace Tests\Homie\Sensors;

use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\SensorVO;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Sensors\SensorValueEvent
 */
class SensorValueEventTest extends TestCase
{

    public function testProperties()
    {
        $sensorVo       = new SensorVO();
        /** @var Sensor $sensor */
        $sensor         = $this->getMock(Sensor::class);
        $value          = 'value';
        $valueFormatted = 'valueFormatted';
        $timestamp      = 'valueFormatted';

        $subject = new SensorValueEvent($sensorVo, $sensor, $value, $valueFormatted, $timestamp);

        $this->assertEquals($sensor, $subject->sensor);
        $this->assertEquals($sensorVo, $subject->sensorVo);
        $this->assertEquals($value, $subject->value);
        $this->assertEquals($valueFormatted, $subject->valueFormatted);
        $this->assertEquals($timestamp, $subject->timestamp);
    }
}

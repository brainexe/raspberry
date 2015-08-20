<?php

namespace Tests\Homie\Sensors;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;

/**
 * @covers Homie\Sensors\Builder
 */
class BuilderTest extends TestCase
{

    /**
     * @var Builder
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Builder();
    }

    public function testBuildSensorVOFromArray()
    {
        $array = [
            'sensorId' => $sensorId = 41,
            'name' => $name = 'name',
            'description' => $description = 'description',
            'interval' => $interval = 60,
            'node' => $node = 22,
            'pin' => $pin = 'pin',
            'color' => $color = 'red',
            'type' => $type = 'type',
            'lastValue' => $lastValue = 'lastValue',
            'lastValueTimestamp' => $lastValueTimestamp = 10000
        ];

        $actual = $this->subject->buildFromArray($array);

        $expected                     = new SensorVO();
        $expected->sensorId           = $sensorId;
        $expected->name               = $name;
        $expected->description        = $description;
        $expected->interval           = $interval;
        $expected->node               = $node;
        $expected->pin                = $pin;
        $expected->type               = $type;
        $expected->color              = $color;
        $expected->lastValue          = $lastValue;
        $expected->lastValueTimestamp = $lastValueTimestamp;

        $this->assertEquals($expected, $actual);
    }
}

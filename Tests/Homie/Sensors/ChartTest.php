<?php

namespace Tests\Homie\Sensors;

use Homie\Sensors\Chart;
use PHPUnit_Framework_TestCase as TestCase;

class ChartTest extends TestCase
{

    /**
     * @var Chart
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Chart();
    }

    public function testFormatJsonData()
    {
        $sensors = [
            [
                'sensorId' => $sensorId1 = 1212,
                'name' => $sensorName = 'name',
                'description' => $sensorDescription = 'description',
                'pin' => $sensorPin = 'pin',
                'type' => 'mockType',
                'color' => 'colorful'
            ],
            [
                'sensorId' => $sensorId2 = 1213,
                'name' => 'name2',
                'description' => 'description2',
                'pin' => 'pin2',
                'type' => 'mockType2',
                'color' => 'colorful'
            ],
            [
                'sensorId' => 'sensor_id_2',
            ]
        ];

        $sensorValues = [
            $sensorId1 => [
                $timestamp = 1212 => $sensorValue = 1200
            ],
            $sensorId2 => [
                1 => 2
            ]
        ];

        $actualResult = $this->subject->formatJsonData($sensors, $sensorValues);

        $expectedResult = [
            [
                'sensor_id' => $sensorId1,
                'name' => $sensorName,
                'description' => $sensorDescription,
                'color' => 'colorful',
                'type' => 'mockType',
                'data' => [
                    [
                         'x' => $timestamp,
                         'y' => $sensorValue
                    ]
                ]
            ],
            [
                'sensor_id' => $sensorId2,
                'name' => 'name2',
                'description' => 'description2',
                'color' => 'colorful',
                'type' => 'mockType2',
                'data' => [
                    [
                        'x' => 1,
                        'y' => 2
                    ]
                ]
            ]
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testFormatEmpty()
    {
        $actualResult = $this->subject->formatJsonData([], []);

        $expectedResult = [];

        $this->assertEquals($expectedResult, $actualResult);
    }
}

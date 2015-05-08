<?php

namespace Tests\Homie\Sensors;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\Time;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorValuesGateway;

/**
 * @covers Homie\Sensors\SensorValuesGateway
 */
class SensorValuesGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var SensorValuesGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->time  = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new SensorValuesGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setTime($this->time);
    }

    public function testAddValue()
    {
        $sensorId = 10;
        $value    = 100;
        $now      = 10000;

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('ZADD')
            ->with("sensor_values:$sensorId", $now, "$now-$value");

        $this->redis
            ->expects($this->once())
            ->method('HMSET')
            ->with(SensorGateway::REDIS_SENSOR_PREFIX . $sensorId, [
                'lastValue' => $value,
                'lastValueTimestamp' => $now
            ]);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $this->subject->addValue($sensorId, $value);
    }

    public function testGetSensorValues()
    {
        $sensorId = 10;
        $from     = 300;
        $now      = 1000;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $redisResult = [
            "701-100",
            "702-101",
        ];
        $this->redis
            ->expects($this->once())
            ->method('ZRANGEBYSCORE')
            ->with("sensor_values:$sensorId", 700, $now)
            ->willReturn($redisResult);

        $actualResult = $this->subject->getSensorValues($sensorId, $from);

        $expectedResult = [
            701 => 100,
            702 => 101,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDeleteOldValues()
    {
        $sensorId = 10;
        $days = 1;
        $now = 86410;
        $deletedPercent = 80;

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $oldValues = [
            "701-100",
            "702-101",
        ];

        $this->redis
            ->expects($this->at(0))
            ->method('ZRANGEBYSCORE')
            ->with("sensor_values:$sensorId", 0, 10)
            ->willReturn($oldValues);

        $this->redis
            ->expects($this->at(1))
            ->method('ZREM')
            ->with("sensor_values:$sensorId", "701-100");

        $this->redis
            ->expects($this->at(2))
            ->method('ZREM')
            ->with("sensor_values:$sensorId", "702-101");

        $actualResult = $this->subject->deleteOldValues($sensorId, $days, $deletedPercent);

        $this->assertEquals(2, $actualResult);
    }
}

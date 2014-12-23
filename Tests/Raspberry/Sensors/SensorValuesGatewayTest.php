<?php

namespace Tests\Raspberry\Sensors\SensorValuesGateway;

use BrainExe\Core\Redis\Redis;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorValuesGateway;

/**
 * @Covers Raspberry\Sensors\SensorValuesGateway
 */
class SensorValuesGatewayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorValuesGateway
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new SensorValuesGateway();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setTime($this->mockTime);
    }

    public function testAddValue()
    {
        $sensorId = 10;
        $value     = 100;
        $now       = 10000;

        $this->mockRedis
        ->expects($this->once())
        ->method('multi')
        ->willReturn($this->mockRedis);

        $this->mockRedis
        ->expects($this->once())
        ->method('ZADD')
        ->with("sensor_values:$sensorId", $now, "$now-$value");

        $this->mockRedis
        ->expects($this->once())
        ->method('HMSET')
        ->with(SensorGateway::REDIS_SENSOR_PREFIX . $sensorId, [
        'last_value' => $value,
        'last_value_timestamp' => $now
        ]);

        $this->mockRedis
        ->expects($this->once())
        ->method('exec');

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->willReturn($now);

        $this->subject->addValue($sensorId, $value);
    }

    public function testGetSensorValues()
    {
        $sensorId = 10;
        $from = 300;
        $now = 1000;

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->willReturn($now);

        $redis_result = [
        "701-100",
        "702-101",
        ];
        $this->mockRedis
        ->expects($this->once())
        ->method('ZRANGEBYSCORE')
        ->with("sensor_values:$sensorId", 700, $now)
        ->willReturn($redis_result);

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
        $deleted_percent = 80;

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->willReturn($now);

        $old_values = [
        "701-100",
        "702-101",
        ];

        $this->mockRedis
        ->expects($this->at(0))
        ->method('ZRANGEBYSCORE')
        ->with("sensor_values:$sensorId", 0, 10)
        ->willReturn($old_values);

        $this->mockRedis
        ->expects($this->at(1))
        ->method('ZREM')
        ->with("sensor_values:$sensorId", "701-100");

        $this->mockRedis
        ->expects($this->at(2))
        ->method('ZREM')
        ->with("sensor_values:$sensorId", "702-101");

        $actualResult = $this->subject->deleteOldValues($sensorId, $days, $deleted_percent);

        $this->assertEquals(2, $actualResult);

    }
}

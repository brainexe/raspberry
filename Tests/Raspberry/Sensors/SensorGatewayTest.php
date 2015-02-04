<?php

namespace Tests\Raspberry\Sensors\SensorGateway;


use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\SensorGateway;
use Raspberry\Sensors\SensorVO;

/**
 * @Covers Raspberry\Sensors\SensorGateway
 */
class SensorGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var SensorGateway
     */
    private $subject;

    /**
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();

        $this->subject = new SensorGateway();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetSensors()
    {
        $sensorIds = [
            $sensorId = 10
        ];

        $result = ['result'];

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("sensor:$sensorId");

        $this->mockRedis
            ->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $actualResult = $this->subject->getSensors();

        $this->assertEquals($result, $actualResult);
    }

    public function testGetSensorsForNode()
    {
        $node = 1;
        $sensorIds = [
        $sensorId = 10
        ];

        $result = [
        [
            'node' => 100
        ],
        [
            'node' => $node
        ]
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("sensor:$sensorId");

        $this->mockRedis
            ->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $actualResult = $this->subject->getSensorsForNode($node);

        $expectedResult = [
        1 => [
            'node' => $node
        ]
        ];
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetSensorIds()
    {
        $sensorIds = [
            10
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('sMembers')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $actualResult = $this->subject->getSensorIds();

        $this->assertEquals($sensorIds, $actualResult);
    }

    public function testAddSensor()
    {
        $sensorVo = new SensorVO();
        $sensorIds = [
            10
        ];

        $newSensorId = 11;

        $sensorData = (array)$sensorVo;
        $sensorData['id'] = $newSensorId;
        $sensorData['last_value'] = 0;
        $sensorData['last_value_timestamp'] = 0;

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HMSET')
            ->with("sensor:$newSensorId");

        $this->mockRedis
            ->expects($this->once())
            ->method('sAdd')
            ->with(SensorGateway::SENSOR_IDS, $newSensorId);

        $this->mockRedis
            ->expects($this->once())
            ->method('execute');

        $actualResult = $this->subject->addSensor($sensorVo);

        $this->assertEquals($newSensorId, $actualResult);
    }

    public function testGetSensor()
    {
        $sensorId = 10;
        $sensor = ['sensor'];

        $this->mockRedis
            ->expects($this->once())
            ->method('hGetAll')
            ->with("sensor:$sensorId")
            ->willReturn($sensor);

        $actualResult = $this->subject->getSensor($sensorId);

        $this->assertEquals($sensor, $actualResult);
    }

    public function testDeleteSensor()
    {
        $sensorId = 10;

        $this->mockRedis
            ->expects($this->at(0))
            ->method('del')
            ->with("sensor:$sensorId");

        $this->mockRedis
            ->expects($this->at(1))
            ->method('sRem')
            ->with(SensorGateway::SENSOR_IDS, $sensorId);

        $this->mockRedis
            ->expects($this->at(2))
            ->method('del')
            ->with("sensor_values:$sensorId");

        $this->subject->deleteSensor($sensorId);
    }
}

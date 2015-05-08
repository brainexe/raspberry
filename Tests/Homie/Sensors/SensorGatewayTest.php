<?php

namespace Tests\Homie\Sensors;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorVO;

/**
 * @covers Homie\Sensors\SensorGateway
 */
class SensorGatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var SensorGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->redis       = $this->getRedisMock();
        $this->idGenerator = $this->getMock(IdGenerator::class);

        $this->subject = new SensorGateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testGetSensors()
    {
        $sensorIds = [
            $sensorId = 10
        ];

        $result = ['result'];

        $this->redis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("sensor:$sensorId");

        $this->redis
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

        $this->redis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("sensor:$sensorId");

        $this->redis
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

        $this->redis
            ->expects($this->once())
            ->method('sMembers')
            ->with(SensorGateway::SENSOR_IDS)
            ->willReturn($sensorIds);

        $actualResult = $this->subject->getSensorIds();

        $this->assertEquals($sensorIds, $actualResult);
    }

    public function testAddSensor()
    {
        $sensorVo    = new SensorVO();
        $newSensorId = 11880;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateRandomNumericId')
            ->willReturn($newSensorId);

        $sensorVo->sensorId = $newSensorId;
        $sensorVo->lastValueTimestamp = 0;
        $sensorVo->lastValue = 0;

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('HMSET')
            ->with("sensor:$newSensorId");

        $this->redis
            ->expects($this->once())
            ->method('sAdd')
            ->with(SensorGateway::SENSOR_IDS, $newSensorId);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $actualResult = $this->subject->addSensor($sensorVo);

        $this->assertEquals($newSensorId, $actualResult);
    }

    public function testGetSensor()
    {
        $sensorId = 10;
        $sensor = ['sensor'];

        $this->redis
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

        $this->redis
            ->expects($this->at(0))
            ->method('del')
            ->with("sensor:$sensorId");

        $this->redis
            ->expects($this->at(1))
            ->method('sRem')
            ->with(SensorGateway::SENSOR_IDS, $sensorId);

        $this->redis
            ->expects($this->at(2))
            ->method('del')
            ->with("sensor_values:$sensorId");

        $this->subject->deleteSensor($sensorId);
    }
}

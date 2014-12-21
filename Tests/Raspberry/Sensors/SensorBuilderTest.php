<?php

namespace Raspberry\Tests\Sensors;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Sensors\SensorBuilder;
use Raspberry\Sensors\Sensors\SensorInterface;

class SensorBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SensorBuilder
     */
    private $subject;


    public function setUp()
    {
        $this->subject = new SensorBuilder();
    }

    public function testGetSensors()
    {
        /** @var SensorInterface|MockObject $sensor_mock */
        $sensor_mock = $this->getMock(SensorInterface::class);
        $sensor_type = 'sensor_123';

        $this->subject->addSensor($sensor_type, $sensor_mock);
        $actualResult = $this->subject->getSensors();

        $this->assertEquals([$sensor_type => $sensor_mock], $actualResult);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid sensor type: sensor_123
     */
    public function testBuildInvalid()
    {
        $sensor_type = 'sensor_123';

        $this->subject->build($sensor_type);
    }

    public function testBuildValid()
    {
        /** @var SensorInterface|MockObject $sensor_mock */
        $sensor_mock = $this->getMock('Raspberry\Sensors\Sensors\SensorInterface');
        $sensor_type = 'sensor_123';

        $this->subject->addSensor($sensor_type, $sensor_mock);

        $actualResult = $this->subject->build($sensor_type);

        $this->assertEquals($sensor_mock, $actualResult);
    }
}

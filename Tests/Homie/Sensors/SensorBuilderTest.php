<?php

namespace Homie\Tests\Sensors;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;

class SensorBuilderTest extends TestCase
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
        /** @var Sensor|MockObject $sensorMock */
        $sensorMock = $this->getMock(Sensor::class);
        $sensorType = 'sensor_123';
        $definition = new Definition();

        $sensorMock
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $this->subject->addSensor($sensorType, $sensorMock);
        $actualResult = $this->subject->getSensors();

        $this->assertEquals([$sensorType => $sensorMock], $actualResult);
    }

    public function testGetDefinition()
    {
        /** @var Sensor|MockObject $sensorMock */
        $sensorMock = $this->getMock(Sensor::class);
        $sensorType = 'sensor_123';

        $definition = new Definition();

        $sensorMock
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $this->subject->addSensor($sensorType, $sensorMock);
        $actualResult = $this->subject->getDefinition($sensorType);

        $this->assertEquals($definition, $actualResult);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid sensor type: invalid
     */
    public function testGetDefinitionWithInvalid()
    {
        $this->subject->getDefinition('invalid');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid sensor type: sensor_123
     */
    public function testBuildInvalid()
    {
        $sensorType = 'sensor_123';

        $this->subject->build($sensorType);
    }

    public function testBuildValid()
    {
        /** @var Sensor|MockObject $sensorMock */
        $sensorMock = $this->getMock(Sensor::class);
        $sensorType = 'sensor_123';

        $definition = new Definition();

        $sensorMock
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $this->subject->addSensor($sensorType, $sensorMock);

        $actualResult = $this->subject->build($sensorType);

        $this->assertEquals($sensorMock, $actualResult);
    }

    public function testGetFormatter()
    {
        $type = 'mockType';

        /** @var Formatter $formatter */
        $formatter = $this->getMock(Formatter::class);

        $this->subject->addFormatter($type, $formatter);

        $actual = $this->subject->getFormatter($type);

        $this->assertEquals($formatter, $actual);
    }

    public function testGetFormatterDefault()
    {
        $type = 'mockType';

        /** @var Formatter $formatter */
        $formatter = $this->getMock(Formatter::class);
        $this->subject->addFormatter(None::TYPE, $formatter);

        $actual = $this->subject->getFormatter($type);

        $this->assertEquals($formatter, $actual);
    }

    public function testGetFormatterFromSensor()
    {
        $type = 'mockType';

        /** @var Sensor|MockObject $sensorMock */
        $sensorMock = $this->getMock(Sensor::class);

        $definition = new Definition();
        $definition->formatter = 'newFormatter';
        $sensorMock
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        /** @var Formatter $formatter */
        $formatter = $this->getMock(Formatter::class);
        $this->subject->addFormatter('newFormatter', $formatter);

        $this->subject->addSensor($type, $sensorMock);

        /** @var Formatter $formatter */
        $formatter = $this->getMock(Formatter::class);

        $this->subject->addFormatter($type, $formatter);

        $actual = $this->subject->getFormatter($type);

        $this->assertEquals($formatter, $actual);
    }
}

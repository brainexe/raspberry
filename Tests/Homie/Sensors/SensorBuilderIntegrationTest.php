<?php

namespace Tests\Homie\Sensors;

use Exception;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use PHPUnit_Framework_TestCase as TestCase;

class SensorBuilderIntegrationTest extends TestCase
{

    public function testSensorType()
    {
        global $dic;

        /** @var SensorBuilder $builder */
        $builder = $dic->get('SensorBuilder');

        $sensorTypes = [];
        foreach ($builder->getSensors() as $sensor) {
            $sensorType = $sensor->getSensorType();
            $this->assertNotEmpty($sensorType);
            $this->assertInternalType('string', $sensorType);

            if (isset($sensorTypes[$sensorType])) {
                throw new Exception(sprintf('Sensor type %s is duplicated'));
            }

            $sensorTypes[$sensorType] = true;
        }
    }

    /**
     * @return array[]
     */
    public function providerSensors()
    {
        global $dic;
        $builder = $dic->get('SensorBuilder');

        return array_map(function (Sensor $sensor) {
            return [$sensor];
        }, $builder->getSensors());
    }
}

<?php

namespace Homie\Sensors\Sensors\Temperature;

use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Temperature;
use Homie\Sensors\Sensors\AbstractDHT11;

/**
 * @Sensor("Sensor.DHT11.Temperature")
 */
class DHT11 extends AbstractDHT11
{

    const TYPE = 'temperature.dht11';

    /**
     * @param integer $parameter
     * @return double
     */
    public function getValue($parameter)
    {
        $output = $this->getContent($parameter);

        if (!preg_match('/Temperature = ([\d.]+)/', $output, $matches)) {
            return null;
        }

        return $this->round($matches[1], 0.01);
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('Temperature (DTH)');
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        return $definition;
    }
}

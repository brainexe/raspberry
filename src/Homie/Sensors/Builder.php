<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service("Sensor.VOBuilder", public=false)
 */
class Builder
{

    /**
     * @param array $data
     * @return SensorVO
     */
    public function buildFromArray(array $data)
    {
        return $this->build(
            $data['sensorId'],
            $data['name'],
            $data['description'],
            $data['interval'],
            $data['node'],
            $data['pin'],
            $data['type'],
            $data['color'],
            $data['lastValue'],
            $data['lastValueTimestamp']
        );
    }

    /**
     * @param int $sensorId
     * @param string $name
     * @param string $description
     * @param int $interval
     * @param int $node
     * @param string $pin
     * @param string $type
     * @param string $color
     * @param float $lastValue
     * @param int $lastValueTimestamp
     * @return SensorVO
     */
    public function build(
        $sensorId,
        $name,
        $description,
        $interval,
        $node,
        $pin,
        $type,
        $color,
        $lastValue = null,
        $lastValueTimestamp = null
    ) {
        $sensor = new SensorVO();

        $sensor->sensorId           = (int)$sensorId;
        $sensor->name               = $name;
        $sensor->description        = $description;
        $sensor->interval           = (int)$interval;
        $sensor->node               = (int)$node;
        $sensor->pin                = $pin;
        $sensor->type               = $type;
        $sensor->color              = $color;
        $sensor->lastValue          = $lastValue;
        $sensor->lastValueTimestamp = (int)$lastValueTimestamp;

        return $sensor;
    }
}

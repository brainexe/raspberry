<?php

namespace Homie\Dashboard\Widgets;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Application\UserException;
use Homie\Dashbaord\Annotation\Widget;
use Homie\Dashboard\AbstractWidget;
use Homie\Sensors\SensorGateway;

/**
 * @Widget
 */
class SensorInput extends AbstractWidget
{

    const TYPE = 'sensor_input';

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @Inject("@SensorGateway")
     * @param SensorGateway $gateway
     */
    public function __construct(SensorGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return WidgetMetadataVo
     */
    public function getMetadata()
    {
        $sensors = [];
        foreach ($this->gateway->getSensors() as $sensor) {
            $sensors[$sensor['sensorId']] = $sensor['name'];
        }

        $metadata = new WidgetMetadataVo(
            $this->getId(),
            gettext('Input sensor value'),
            gettext('Displays the current value of a given sensor'),
            [
                'sensor_id' => [
                    'name'   => gettext('Sensor'),
                    'values' => $sensors,
                    'type'   => 'single_select'
                ]
            ]
        );

        return $metadata
            ->addTitle()
            ->setSize(4, 3);
    }
}

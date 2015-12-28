<?php

namespace Homie\Sensors\GetValue;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\Core\Traits\TimeTrait;
use Exception;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorValueEvent;
use Homie\Sensors\SensorValuesGateway;
use Homie\Sensors\SensorVO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Sensor.GetValue.Listener")
 */
class Listener implements EventSubscriberInterface
{

    use TimeTrait;
    use EventDispatcherTrait;
    use LoggerTrait;

    /**
     * @var SensorBuilder
     */
    private $builder;

    /**
     * @var SensorValuesGateway
     */
    private $gateway;

    /**
     * @Inject({
     *      "@SensorBuilder",
     *      "@SensorValuesGateway"
     * })
     * @param SensorBuilder $builder
     * @param SensorValuesGateway $valuesGateway
     */
    public function __construct(
        SensorBuilder $builder,
        SensorValuesGateway $valuesGateway
    ) {
        $this->builder = $builder;
        $this->gateway = $valuesGateway;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Event::NAME => 'handle'
        ];
    }

    /**
     * @param Event $event
     */
    public function handle(Event $event)
    {
        $sensorVo = $event->getSensorVo();
        $sensor   = $this->builder->build($sensorVo->type);

        $value = $this->getValue($sensor, $sensorVo);
        if ($value === null) {
            $event = new SensorValueEvent(
                SensorValueEvent::ERROR,
                $sensorVo,
                $sensor,
                $value,
                null,
                $this->now()
            );
            $this->dispatcher->dispatchEvent($event);
            return;
        }

        $this->gateway->addValue($sensorVo, $value);

        $formatter      = $this->builder->getFormatter($sensorVo->formatter);
        $formattedValue = $formatter->formatValue($value);
        $event = new SensorValueEvent(
            SensorValueEvent::VALUE,
            $sensorVo,
            $sensor,
            $value,
            $formattedValue,
            $this->now()
        );
        $this->dispatcher->dispatchEvent($event);
    }

    /**
     * @param Sensor $sensor
     * @param SensorVO $sensorVo
     * @return float|null
     */
    private function getValue(Sensor $sensor, $sensorVo)
    {
        try {
            return $sensor->getValue($sensorVo);
        } catch (Exception $e) {
            $this->error('Error while fetching sensor value:' . $e->getMessage());
            return null;
        }
    }
}

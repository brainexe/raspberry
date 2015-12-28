<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\Sensors\Aggregate\Aggregated;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Custom.HamsterWheel")
 */
class HamsterWheel extends AbstractSensor implements Parameterized
{

    const TYPE = 'custom.hamsterwheel';

    /**
     * @var Aggregated
     */
    private $aggregated;

    /**
     * @Inject({"@Sensor.Sensor.Aggregated.Aggregated"})
     * @param Aggregated $aggregated
     */
    public function __construct(
        Aggregated $aggregated
    ) {
        $this->aggregated = $aggregated;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        // todo multiply by range/extend
        return (int)$this->aggregated->getCurrent($sensor->parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor, OutputInterface $output)
    {
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}

<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\CompilerPass\Annotation\Sensor;
use Raspberry\Sensors\Definition;
use Raspberry\Sensors\Formatter\Load as Formatter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Load")
 */
class Load extends AbstractSensor
{

    const TYPE = 'load';

    /**
     * {@inheritdoc}
     */
    public function getValue($parameter)
    {
        unset($parameter);

        return sys_getloadavg()[0];
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition = new Definition();
        $definition->name = _('Load');
        $definition->type = Definition::TYPE_LOAD;
        $definition->formatter = Formatter::TYPE;

        return $definition;
    }
}
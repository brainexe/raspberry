<?php

namespace Homie\Sensors\Sensors;

use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Percentage;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Annotations\Annotations\Inject;

/**
 * @Sensor("Sensor.DiskUsedPercent")
 */
class DiskUsedPercent extends AbstractSensor
{

    const TYPE = 'disk_used_percent';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@HomieClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }


    /**
     * {@inheritdoc}
     */
    public function getValue($parameter)
    {
        $content = $this->client->executeWithReturn('df .');

        if (preg_match('/\s(\d+)%/', $content, $matches)) {
            return $matches[1];
        }

        return null;
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
        $definition            = new Definition();
        $definition->name      = gettext('Disk Used (%)');
        $definition->type      = Definition::TYPE_DISK;
        $definition->formatter = Percentage::TYPE;

        return $definition;
    }
}

<?php

namespace Homie\Sensors\Sensors\Brightness;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Sensors\AbstractSensor;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Brightness.Webcam")
 */
class Webcam extends AbstractSensor
{

    const TYPE = 'brightness.webcam';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject({"@HomieClient"})
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param integer $path
     * @return double
     */
    public function getValue($path)
    {
        $tmpFile = tempnam('/tmp', self::TYPE);
        $command = sprintf(
            "fswebcam %s; convert %s  -colorspace gray  -resize 1x1  txt:-; rm %s ",
            $tmpFile,
            $tmpFile,
            $tmpFile
        );

        $result = $this->client->executeWithReturn($command);

        if (!preg_match('/gray\((\d+)\)/', $result, $matches)) {
            return 0;
        }

        return $this->round($matches[1], 0.1);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        // todo check if camera is connected
        // todo check if fswebcam/imagemagic is installed
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('Brightness');
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;
        $definition->neededPackages = [
            'fswebcam',
            'imagemagick'
        ];

        return $definition;
    }
}

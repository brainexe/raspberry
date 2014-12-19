<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Core\Util\FileSystem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Service(public=false, tags={{"name" = "sensor"}})
 */
class TemperatureDS18 implements SensorInterface
{

    use TemperatureSensorTrait;

    const TYPE = 'temp_ds18';
    const PIN_FILE = '/sys/bus/w1/devices/%s/w1_slave';
    const BUS_DIR = '/sys/bus/w1/devices';

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @inject("@FileSystem")
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->fileSystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getSensorType()
    {
        return self::TYPE;
    }

    /**
     * @param integer $pin
     * @return double
     */
    public function getValue($pin)
    {
        $path = sprintf(self::PIN_FILE, $pin);

        if (!$this->fileSystem->exists($path)) {
            return null;
        }

        $content = $this->fileSystem->fileGetContents($path);

        if (strpos($content, 'YES') === false) {
         // invalid response :(
            return null;
        }

        $matches = null;
        if (!preg_match('/t=([\-\d]+)$/', $content, $matches)) {
            return null;
        }

        $temperature = $matches[1] / 1000;

        $invalid_temperatures = [0.0, 85.0];
        if (in_array($temperature, $invalid_temperatures)) {
            return null;
        }

        return $temperature;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(OutputInterface $output)
    {
        $bus_system = '/sys/bus/w1/devices';

        if (!$this->fileSystem->exists($bus_system)) {
            $output->writeln(sprintf('<error>%s: w1 bus not exists: %s</error>', self::getSensorType(), $bus_system));
            return false;
        }

        return true;
    }
}

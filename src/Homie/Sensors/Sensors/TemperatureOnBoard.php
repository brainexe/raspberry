<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use Homie\Sensors\CompilerPass\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Temperature;
use Homie\Sensors\Interfaces\Searchable;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Temperature.OnBoard")
 */
class TemperatureOnBoard extends AbstractSensor implements Searchable
{

    const TYPE = 'temperature_onboard';

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var Glob
     */
    private $glob;

    /**
     * @Inject({"@FileSystem", "@Glob"})
     * @param FileSystem $filesystem
     * @param Glob $glob
     */
    public function __construct(Filesystem $filesystem, Glob $glob)
    {
        $this->fileSystem = $filesystem;
        $this->glob       = $glob;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($parameter)
    {
        $content = $this->fileSystem->fileGetContents($parameter);

        return $content / 1000;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        if (!$this->fileSystem->exists($parameter)) {
            $output->writeln(
                sprintf(
                    '<error>%s: Thermal zone file does not exist: %s</error>',
                    self::TYPE,
                    $parameter
                )
            );
            return false;
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function search()
    {
        return array_filter($this->glob->execGlob('/sys/class/thermal/*/temp'), function($file) {
            return strpos($file, 'cooling') === false;
        });
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = _('Temp. Onboard');
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        return $definition;
    }
}

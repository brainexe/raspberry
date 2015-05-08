<?php

namespace Homie\Sensors\Command;

use BrainExe\Annotations\Annotations\Inject;
use Exception;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Interfaces\Sensor;
use Homie\Sensors\SensorBuilder;
use Homie\Sensors\SensorGateway;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Command.Sensor.Crawl")
 */
class Crawl extends Command
{

    /**
     * @var SensorBuilder
     */
    private $builder;

    /**
     * @var array[]
     */
    private $sensorsRaw;

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sensor:crawl')
            ->setDescription('Search for all sensors');
    }

    /**
     * @Inject({"@SensorGateway", "@SensorBuilder"})
     * @param SensorGateway $gateway
     * @param SensorBuilder $builder
     */
    public function __construct(SensorGateway $gateway, SensorBuilder $builder)
    {
        $this->gateway = $gateway;
        $this->builder = $builder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->sensorsRaw = $this->gateway->getSensors();

        $sensors = $this->builder->getSensors();
        foreach ($sensors as $sensor) {
            $output->writeln(sprintf("Handling <info>%s</info>...", $sensor->getSensorType()));
            if ($sensor instanceof Searchable) {
                $output->writeln("Searching...");

                $parameters = $sensor->search();
                if (empty($parameters)) {
                    $output->writeln(
                        sprintf(
                            "<error>No valid sensor found for %s...</error>",
                            $sensor->getSensorType()
                        )
                    );
                }

                foreach ($parameters as $parameter) {
                    $this->addSensor($input, $output, $sensor, $parameter);
                }
            } elseif (!$sensor instanceof Parameterized) {
                $this->addSensor($input, $output, $sensor, null);
            } else {
                $output->writeln('Not searchable.');
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param Sensor $sensor
     * @param string|null $parameter
     */
    private function addSensor(
        InputInterface $input,
        OutputInterface $output,
        Sensor $sensor,
        $parameter
    ) {
        $type = $sensor->getSensorType();

        if ($this->hasSensor($type, $parameter)) {
            $output->writeln(
                sprintf(
                    'Sensor "<info>%s</info>" with parameter "%s" already exists',
                    $type,
                    $parameter
                )
            );

            return;
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelperSet()->get('question');

        $question = new ConfirmationQuestion(
            sprintf(
                'Do you want to add sensor "<info>%s</info>" with parameter "%s" (y/n)',
                $type,
                $parameter
            )
        );

        if ($helper->ask($input, $output, $question)) {
            $arrayInput = new ArrayInput([
                'command' => 'sensor:add',
                'type' => $type,
                'parameter' => $parameter
            ]);
            $this->getApplication()->run($arrayInput, $output);
        };
    }

    /**
     * @param string $type
     * @param string $parameter
     * @return bool
     */
    private function hasSensor($type, $parameter)
    {
        foreach ($this->sensorsRaw as $sensor) {
            if ($sensor['type'] !== $type) {
                continue;
            }

            if (empty($parameter) || $parameter == $sensor['pin']) {
                return true;
            }
        }

        return false;
    }
}

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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Command.Sensor.Add")
 */
class Add extends Command
{

    /**
     * @var SensorGateway
     */
    private $gateway;

    /**
     * @var SensorBuilder
     */
    private $builder;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var QuestionHelper
     */
    private $helper;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sensor:add')
            ->setDescription('Add a new Sensor')
            ->addArgument('type', InputArgument::OPTIONAL)
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addArgument('parameter', InputArgument::OPTIONAL)
            ->addArgument('description', InputArgument::OPTIONAL)
            ->addArgument('node', InputArgument::OPTIONAL)
            ->addArgument('interval', InputArgument::OPTIONAL);
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
        $this->input  = $input;
        $this->output = $output;
        $this->helper = $this->getHelperSet()->get('question');

        $sensor      = $this->getSensor();
        $parameter   = $this->getParameter($sensor);
        $name        = $this->getSensorName($sensor);
        $description = $this->getSensorDescription();
        $interval    = $this->getInterval();
        $node        = $this->getNode();
        $type        = $sensor->getSensorType();

        // get test value
        $testValue = $sensor->getValue($parameter);
        if ($testValue !== null) {
            $formatter = $this->builder->getFormatter($type);
            $output->writeln(
                sprintf('<info>Sensor value: %s</info>', $formatter->formatValue($testValue))
            );
        } else {
            $output->writeln('<error>Sensor returned invalid data.</error>');
            $this->askForTermination();
        }

        $sensorVo              = new SensorVO();
        $sensorVo->name        = $name;
        $sensorVo->type        = $type;
        $sensorVo->description = $description;
        $sensorVo->pin         = $parameter;
        $sensorVo->interval    = $interval;
        $sensorVo->node        = $node;
        $sensorVo->color       = '#' . substr(md5($name), 0, 6);

        $this->gateway->addSensor($sensorVo);
    }

    /**
     * @throws Exception
     */
    private function askForTermination()
    {
        $question = new ConfirmationQuestion('Abort adding this sensor? (y/n)');
        if ($this->helper->ask($this->input, $this->output, $question)) {
            throw new Exception('Terminated');
        }
    }

    /**
     * @param Sensor $sensor
     * @return string
     * @throws Exception
     */
    public function getParameter(Sensor $sensor)
    {
        if (!$sensor instanceof Parameterized) {
            return null;
        }

        if ($this->input->getArgument('parameter')) {
            return $this->input->getArgument('parameter');
        }

        if ($sensor instanceof Searchable) {
            $possible = $sensor->search();
            if (!$possible) {
                throw new Exception('No possible sensor found');
            }
            $question  = new ChoiceQuestion("Parameter", $possible);
            $parameter = $this->helper->ask($this->input, $this->output, $question);
        } else {
            $parameter = $this->helper->ask(
                $this->input,
                $this->output,
                new Question("Parameter?\n")
            );
        }

        /** @var Sensor $sensor */
        if (!$sensor->isSupported($parameter, $this->output)) {
            $this->output->writeln('<error>Sensor is not supported</error>');
            throw new Exception(sprintf('Parameter "%s" is not supported', $parameter));
        }

        $this->output->writeln('<info>Sensor is supported</info>');

        return $parameter;
    }

    /**
     * @return Sensor
     */
    protected function getSensor()
    {
        $sensors = $this->builder->getSensors();

        if ($this->input->getArgument('type')) {
            $type = $this->input->getArgument('type');
        } else {
            $sensorTypes = array_keys($sensors);

            $question = new ChoiceQuestion("Sensor Type", $sensorTypes);
            $type     = $this->helper->ask($this->input, $this->output, $question);
        }

        $sensor = $sensors[$type];

        return $sensor;
    }

    /**
     * @param Sensor $sensor
     * @return string
     */
    protected function getSensorName(Sensor $sensor)
    {
        if ($this->input->getArgument('name')) {
            return $this->input->getArgument('name');
        }

        $default = ucfirst($sensor->getSensorType());

        $name = $this->helper->ask(
            $this->input,
            $this->output,
            new Question(sprintf("Sensor name? (default: %s)\n", $default), $sensor->getSensorType())
        );

        return $name;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function setInputOutput(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * @return int
     */
    protected function getInterval()
    {
        if ($this->input->getArgument('interval')) {
            return (int)$this->input->getArgument('interval');
        }

        return (int)$this->helper->ask(
            $this->input,
            $this->output,
            new Question("Interval in minutes (default: 5)\n", 5)
        );
    }

    /**
     * @return int
     */
    protected function getNode()
    {
        if ($this->input->getArgument('node')) {
            return (int)$this->input->getArgument('node');
        }

        return (int)$this->helper->ask(
            $this->input,
            $this->output,
            new Question("Node? (only for advanced users needed)\n")
        );
    }

    /**
     * @return string
     */
    protected function getSensorDescription()
    {
        if ($this->input->getArgument('description')) {
            return $this->input->getArgument('description');
        }

        $description = $this->helper->ask(
            $this->input,
            $this->output,
            new Question("Description (optional)?\n")
        );

        return $description;
    }
}

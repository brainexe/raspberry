<?php

namespace Homie\Sensors\Command;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Sensors\Sensors\Aggregate\AggregateEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Command.Sensor.Aggregated")
 */
class Aggregated extends Command
{
    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sensor:aggregate')
            ->setDescription('Adds a value for an aggregated sensor')
            ->addArgument('identifier', InputArgument::REQUIRED)
            ->addArgument('value', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identifier = $input->getArgument('identifier');
        $value      = $input->getArgument('value');

        $event = new AggregateEvent($identifier, $value);
        $this->dispatchEvent($event);
    }
}

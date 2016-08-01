<?php

namespace Homie\Sensors\Annotation;

use BrainExe\Annotations\Builder\ServiceDefinition;
use Homie\Sensors\CompilerPass\Sensor as CompilerPass;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Definition;

class SensorBuilder extends ServiceDefinition
{
    /**
     * {@inheritdoc}
     */
    public function build(ReflectionClass $reflectionClass, $annotation)
    {
        /** @var Definition $definition */
        list($serviceId, $definition) = parent::build($reflectionClass, $annotation);

        $definition->setPublic(true);
        $definition->addTag(CompilerPass::TAG);
        $definition->setShared(false);

        return ['__' . $serviceId, $definition];
    }
}

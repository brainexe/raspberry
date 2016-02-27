<?php

namespace Homie\Expression\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use BrainExe\Core\Annotations\CompilerPass as CompilerPassAnnotation;

/**
 * @CompilerPassAnnotation
 */
class RegisterProvider implements CompilerPassInterface
{

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $dispatcher = $container->getDefinition('EventDispatcher');
        $dispatcher->addMethodCall('addCatchall', [new Reference('Expression.Listener')]);

        $language   = $container->getDefinition('Expression.Language');
        $serviceIds = $container->findTaggedServiceIds('expression_language');
        foreach (array_keys($serviceIds) as $serviceId) {
            $language->addMethodCall('registerProvider', [new Reference($serviceId)]);
        }
    }
}

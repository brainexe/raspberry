<?php

namespace Raspberry\DIC;

use Raspberry\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ControllerCompilerPass implements CompilerPassInterface {

	public function process(ContainerBuilder $container) {
		$definition = $container->getDefinition('Application');

		$taggedServices = $container->findTaggedServiceIds('controller');
		foreach ($taggedServices as $id => $attributes) {
			/** @var ControllerInterface $service */
			$service = $container->get($id);

			$definition->addMethodCall('mount', [$service->getPath(), new Reference($id)]);
		}
	}
}
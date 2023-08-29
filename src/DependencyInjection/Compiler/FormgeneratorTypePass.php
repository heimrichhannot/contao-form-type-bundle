<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\DependencyInjection\Compiler;

use HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType\FormgeneratorTypeCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormgeneratorTypePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FormgeneratorTypeCollection::class)) {
            return;
        }

        $definition = $container->findDefinition(FormgeneratorTypeCollection::class);

        $taggedServices = $container->findTaggedServiceIds('huh.formgenerator_type');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addType', [$container->getDefinition($id)]);
        }
    }
}
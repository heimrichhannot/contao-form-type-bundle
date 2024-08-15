<?php

namespace HeimrichHannot\FormTypeBundle\DependencyInjection\Compiler;

use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FormTypeCollection::class)) {
            return;
        }

        $definition = $container->findDefinition(FormTypeCollection::class);

        $taggedServices = $container->findTaggedServiceIds('huh.form_type');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addType', [$container->getDefinition($id)]);
        }
    }
}
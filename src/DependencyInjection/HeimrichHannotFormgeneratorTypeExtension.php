<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\DependencyInjection;

use HeimrichHannot\FormgeneratorTypeBundle\FormgeneratorType\FormgeneratorTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HeimrichHannotFormgeneratorTypeExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(FormgeneratorTypeInterface::class)
            ->addTag('huh.formgenerator_type');

    }


}
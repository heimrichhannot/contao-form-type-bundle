<?php

namespace HeimrichHannot\FormTypeBundle\DependencyInjection;

use HeimrichHannot\FormTypeBundle\FormType\FormTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class HeimrichHannotFormTypeExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(FormTypeInterface::class)
            ->addTag('huh.form_type');

    }


}
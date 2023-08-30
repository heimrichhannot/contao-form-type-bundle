<?php

namespace HeimrichHannot\FormgeneratorTypeBundle;

use HeimrichHannot\FormgeneratorTypeBundle\DependencyInjection\Compiler\FormgeneratorTypePass;
use HeimrichHannot\FormgeneratorTypeBundle\DependencyInjection\HeimrichHannotFormgeneratorTypeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotFormgeneratorTypeBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new HeimrichHannotFormgeneratorTypeExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormgeneratorTypePass());
    }
}
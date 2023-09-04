<?php

namespace HeimrichHannot\FormTypeBundle;

use HeimrichHannot\FormTypeBundle\DependencyInjection\Compiler\FormTypePass;
use HeimrichHannot\FormTypeBundle\DependencyInjection\HeimrichHannotFormTypeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotFormTypeBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new HeimrichHannotFormTypeExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FormTypePass());
    }
}
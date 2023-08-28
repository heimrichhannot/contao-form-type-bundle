<?php

namespace HeimrichHannot\FormgeneratorTypeBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use HeimrichHannot\FormgeneratorTypeBundle\HeimrichHannotFormgeneratorTypeBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(HeimrichHannotFormgeneratorTypeBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
        ];
    }
}
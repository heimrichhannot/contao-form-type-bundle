<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$dca = &$GLOBALS['TL_DCA']['tl_form'];

PaletteManipulator::create()
    ->addLegend('formType_legend', 'title_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('formType', 'formType_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_form');

$dca['fields']['formType'] = [
    'inputType' => 'select',
    'reference' => &$GLOBALS['TL_LANG']['tl_form']['FORMTYPE'],
    'filter' => true,
    'eval' => [
        'includeBlankOption' => true,
        'submitOnChange' => true,
    ],
    'sql' => "varchar(32) NOT NULL default ''",
];
$dca['fields']['allowEdit'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w50',
    ],
    'sql' => "char(1) NOT NULL default ''",
];
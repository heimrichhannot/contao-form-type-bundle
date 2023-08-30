<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$dca = &$GLOBALS['TL_DCA']['tl_form'];

PaletteManipulator::create()
    ->addLegend('formgeneratorType_legend', 'title_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField('formgeneratorType', 'formgeneratorType_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_form');

$dca['fields']['formgeneratorType'] = [
    'inputType' => 'select',
    'eval' => [
        'includeBlankOption' => true,
        'submitOnChange' => true,
    ],
    'sql' => "varchar(32) NOT NULL default ''",
];
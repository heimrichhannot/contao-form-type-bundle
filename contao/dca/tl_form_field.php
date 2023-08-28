<?php
$dca = &$GLOBALS['TL_DCA']['tl_form_field'];

$dca['fields']['formgeneratorType'] = [
    'inputType' => 'select',
    'eval' => [
        'includeBlankOption' => true,
    ]
];
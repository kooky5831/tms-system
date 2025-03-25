<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'TMS',
	'display_mode'          => 'fullpage',
	'tempDir'               => storage_path('app/public/temp'),
    'margin_top'            => '5',
    'margin_right'          => '5',
    'margin_bottom'         => '5',
    'margin_left'           => '5',
    'font_path'             => base_path('resources/fonts/'),
    'font_data'             => [
        'firasansfont' => [
            'R'  => 'FiraSans-Regular.ttf',    // regular font
            'B'  => 'FiraSans-Bold.ttf',       // optional: bold font
            'I'  => 'FiraSans-Italic.ttf',     // optional: italic font
            'BI' => 'FiraSans-BoldItalic.ttf' // optional: bold-italic font
            //'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
            //'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
        ]
    ]
];

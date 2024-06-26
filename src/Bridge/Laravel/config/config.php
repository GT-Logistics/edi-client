<?php

return [
    'transport' => 'ftp',
    'standard' => 'x12',
    'ftp' => [
        'host' => env('EDI_FTP_HOST', ''),
        'port' => (int) env('EDI_FTP_PORT', 21),
        'username' => env('EDI_FTP_USERNAME', ''),
        'password' => env('EDI_FTP_PASSWORD', ''),
        'input_dir' => env('EDI_FTP_INPUT_DIR', ''),
        'output_dir' => env('EDI_FTP_OUTPUT_DIR', ''),
        'ssl' => (bool) env('EDI_FTP_SSL', false),
    ],
    'x12' => [
        'element-delimiter' => env('EDI_X12_ELEMENT_DELIMITER', '*'),
        'segment-delimiter' => env('EDI_X12_SEGMENT_DELIMITER', '~'),
    ],
];

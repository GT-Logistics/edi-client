<?php

return [
    'transport' => 'ftp',
    'standard' => 'X12',
    'ftp' => [
        'host' => env('EDI_FTP_HOST', ''),
        'port' => (int) env('EDI_FTP_PORT', 21),
        'username' => env('EDI_FTP_USERNAME', ''),
        'password' => env('EDI_FTP_PASSWORD', ''),
        'input_dir' => env('EDI_FTP_INPUT_DIR', ''),
        'output_dir' => env('EDI_FTP_OUTPUT_DIR', ''),
        'ssl' => (bool) env('EDI_FTP_SSL', false),
    ],
];

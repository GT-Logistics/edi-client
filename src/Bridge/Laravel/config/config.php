<?php

return [
    'transport' => env('EDI_TRANSPORT', 'ftp'),
    'standard' => env('EDI_STANDARD', 'x12'),
    'ftp' => [
        'host' => env('EDI_FTP_HOST', ''),
        'port' => (int) env('EDI_FTP_PORT', 21),
        'username' => env('EDI_FTP_USERNAME', ''),
        'password' => env('EDI_FTP_PASSWORD', ''),
        'input_dir' => env('EDI_FTP_INPUT_DIR', ''),
        'output_dir' => env('EDI_FTP_OUTPUT_DIR', ''),
        'ssl' => (bool) env('EDI_FTP_SSL', false),
    ],
    'sftp' => [
        'host' => env('EDI_SFTP_HOST', ''),
        'port' => (int) env('EDI_SFTP_PORT', 22),
        'username' => env('EDI_SFTP_USERNAME', ''),
        'password' => env('EDI_SFTP_PASSWORD', ''),
        'input_dir' => env('EDI_SFTP_INPUT_DIR', ''),
        'output_dir' => env('EDI_SFTP_OUTPUT_DIR', ''),
    ],
    'x12' => [
        'element-delimiter' => env('EDI_X12_ELEMENT_DELIMITER', '*'),
        'segment-delimiter' => env('EDI_X12_SEGMENT_DELIMITER', '~'),
    ],
];

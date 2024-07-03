<?php

declare(strict_types=1);
/*
 * Copyright (C) 2024 GT+ Logistics.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
 * USA
 */

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
    'sftp' => [
        'host' => env('EDI_SFTP_HOST', ''),
        'port' => (int) env('EDI_SFTP_PORT', 21),
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

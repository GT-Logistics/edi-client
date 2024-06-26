<?php
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

declare(strict_types=1);

namespace Gtlogistics\EdiClient\Transport;

use Gtlogistics\EdiClient\Exception\TransportException;
use Safe\Exceptions\FtpException;

use function Safe\ftp_connect;
use function Safe\ftp_login;
use function Safe\ftp_pasv;
use function Safe\ftp_ssl_connect;

class FtpTransportFactory
{
    public function build(
        string $host,
        int $port,
        string $username,
        string $password,
        string $inputDir,
        string $outputDir,
        bool $useSsl = false,
    ): FtpTransport {
        if (!extension_loaded('ftp')) {
            throw new \RuntimeException('You must have the FTP extension for PHP, please enable it in the php.ini file');
        }

        try {
            $connection = !$useSsl ? ftp_connect($host, $port) : ftp_ssl_connect($host, $port);

            ftp_login($connection, $username, $password);
            ftp_pasv($connection, true);

            return new FtpTransport(
                $connection,
                $inputDir,
                $outputDir,
            );
        } catch (FtpException $e) {
            throw new TransportException("Could not connect to FTP server in $host:$port", 0, $e);
        }
    }
}

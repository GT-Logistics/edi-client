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

namespace Gtlogistics\EdiClient\Transport\Ftp;

use Gtlogistics\EdiClient\Exception\TransportException;
use Gtlogistics\EdiClient\Transport\LazyTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Safe\Exceptions\FtpException;

use function Safe\ftp_connect;
use function Safe\ftp_pasv;
use function Safe\ftp_ssl_connect;

class FtpTransportFactory
{
    private FtpAuthenticatorInterface $authenticator;

    public function __construct()
    {
        if (!extension_loaded('ftp')) {
            throw new \RuntimeException('You must have the FTP extension for PHP, please enable it in the php.ini file');
        }

        $this->withAnonymousAuthentication();
    }

    public function withAnonymousAuthentication(): self
    {
        return $this->withAuthenticator(new FtpAnonymousAuthenticator());
    }

    public function withPasswordAuthentication(string $username, string $password): self
    {
        return $this->withAuthenticator(new FtpPasswordAuthenticator($username, $password));
    }

    public function withAuthenticator(FtpAuthenticatorInterface $authenticator): self
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    public function build(
        string $host,
        int $port,
        string $inputDir,
        string $outputDir,
        bool $useSsl = false,
    ): TransportInterface {
        return new LazyTransport(fn () => $this->buildTransport($host, $port, $inputDir, $outputDir, $useSsl));
    }

    private function buildTransport(
        string $host,
        int $port,
        string $inputDir,
        string $outputDir,
        bool $useSsl = false,
    ): TransportInterface {
        try {
            $connection = !$useSsl ? ftp_connect($host, $port) : ftp_ssl_connect($host, $port);

            $this->authenticator->authenticate($connection);
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

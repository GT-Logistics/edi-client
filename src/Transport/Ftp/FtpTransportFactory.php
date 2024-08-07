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

class FtpTransportFactory
{
    private FtpAuthenticatorInterface $authenticator;

    private bool $passive;

    public function __construct()
    {
        if (!extension_loaded('ftp')) {
            throw new \RuntimeException('You must have the FTP extension for PHP, please enable it in the php.ini file');
        }

        $this->authenticator = new FtpAnonymousAuthenticator();
        $this->passive = true;
    }

    public function withPassive(): static
    {
        $cloned = clone $this;
        $cloned->passive = true;

        return $cloned;
    }

    public function withActive(): static
    {
        $cloned = clone $this;
        $cloned->passive = false;

        return $cloned;
    }

    public function withAnonymousAuthentication(): static
    {
        return $this->withAuthenticator(new FtpAnonymousAuthenticator());
    }

    public function withPasswordAuthentication(string $username, string $password): static
    {
        return $this->withAuthenticator(new FtpPasswordAuthenticator($username, $password));
    }

    public function withAuthenticator(FtpAuthenticatorInterface $authenticator): static
    {
        $cloned = clone $this;
        $cloned->authenticator = $authenticator;

        return $cloned;
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
            $connection = new FtpConnection($host, $port, $useSsl);
            $this->authenticator->authenticate($connection);
            $connection->pasv($this->passive);

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

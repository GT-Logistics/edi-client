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

namespace Gtlogistics\EdiClient\Transport\Sftp;

use Gtlogistics\EdiClient\Exception\TransportException;
use Gtlogistics\EdiClient\Transport\LazyTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Safe\Exceptions\Ssh2Exception;

use function Safe\ssh2_connect;

class SftpTransportFactory
{
    private SftpAuthenticatorInterface $authenticator;

    public function __construct()
    {
        $this->withAnonymousAuthentication();
    }

    public function withAnonymousAuthentication(): self
    {
        $this->authenticator = new SftpAnonymousAuthenticator();

        return $this;
    }

    public function withNoneAuthentication(string $username): self
    {
        $this->authenticator = new SftpNoneAuthenticator($username);

        return $this;
    }

    public function withPasswordAuthentication(string $username, string $password): self
    {
        $this->authenticator = new SftpPasswordAuthenticator($username, $password);

        return $this;
    }

    public function withCustomAuthentication(SftpAuthenticatorInterface $authenticator): self
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    public function build(
        string $host,
        int $port,
        string $inputDir,
        string $outputDir,
    ): TransportInterface {
        if (!extension_loaded('ssh2')) {
            throw new \RuntimeException('You must have the SSH2 extension for PHP, please enable it in the php.ini file');
        }

        return new LazyTransport(fn () => $this->buildTransport($host, $port, $inputDir, $outputDir));
    }

    private function buildTransport(
        string $host,
        int $port,
        string $inputDir,
        string $outputDir,
    ): TransportInterface {
        try {
            $sshConnection = ssh2_connect($host, $port);
            $this->authenticator->authenticate($sshConnection);

            return new SftpTransport(
                $sshConnection,
                $inputDir,
                $outputDir,
            );
        } catch (Ssh2Exception $e) {
            throw new TransportException("Could not connect to SFTP server in $host:$port", 0, $e);
        }
    }
}

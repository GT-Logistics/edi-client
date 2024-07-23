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

namespace Gtlogistics\EdiClient\Transport\Ftp;

use FTP\Connection;
use Safe\Exceptions\FtpException;
use Webmozart\Assert\Assert;

use function Safe\ftp_alloc;
use function Safe\ftp_append;
use function Safe\ftp_cdup;
use function Safe\ftp_chdir;
use function Safe\ftp_chmod;
use function Safe\ftp_close;
use function Safe\ftp_connect;
use function Safe\ftp_delete;
use function Safe\ftp_fget;
use function Safe\ftp_fput;
use function Safe\ftp_get;
use function Safe\ftp_login;
use function Safe\ftp_mkdir;
use function Safe\ftp_mlsd;
use function Safe\ftp_nb_put;
use function Safe\ftp_nlist;
use function Safe\ftp_pasv;
use function Safe\ftp_put;
use function Safe\ftp_pwd;
use function Safe\ftp_raw;
use function Safe\ftp_rename;
use function Safe\ftp_rmdir;
use function Safe\ftp_site;
use function Safe\ftp_ssl_connect;
use function Safe\ftp_systype;

/**
 * Wrapper object to ftp_* functions.
 *
 * @internal
 */
class FtpConnection
{
    private ?Connection $connection;

    public function __construct(string $host, int $port, bool $useSsl = false)
    {
        $connection = !$useSsl ? ftp_connect($host, $port) : ftp_ssl_connect($host, $port);

        $this->connection = $connection;
    }

    /**
     * @throws FtpException
     */
    public function alloc(int $size, ?string &$response = null): void
    {
        Assert::notNull($this->connection);

        ftp_alloc($this->connection, $size, $response);
    }

    /**
     * @throws FtpException
     */
    public function append(string $remote_filename, string $local_filename, int $mode = FTP_BINARY): void
    {
        Assert::notNull($this->connection);

        ftp_append($this->connection, $remote_filename, $local_filename, $mode);
    }

    /**
     * @throws FtpException
     */
    public function cdup(): void
    {
        Assert::notNull($this->connection);

        ftp_cdup($this->connection);
    }

    /**
     * @throws FtpException
     */
    public function chdir(string $directory): void
    {
        Assert::notNull($this->connection);

        ftp_chdir($this->connection, $directory);
    }

    /**
     * @throws FtpException
     */
    public function chmod(int $permissions, string $filename): int
    {
        Assert::notNull($this->connection);

        return ftp_chmod($this->connection, $permissions, $filename);
    }

    /**
     * @throws FtpException
     */
    public function close(): void
    {
        Assert::notNull($this->connection);

        ftp_close($this->connection);
    }

    /**
     * @throws FtpException
     */
    public function delete(string $filename): void
    {
        Assert::notNull($this->connection);

        ftp_delete($this->connection, $filename);
    }

    /**
     * @param resource $stream
     *
     * @throws FtpException
     */
    public function fget($stream, string $remote_filename, int $mode = FTP_BINARY, int $offset = 0): void
    {
        Assert::notNull($this->connection);

        ftp_fget($this->connection, $stream, $remote_filename, $mode, $offset);
    }

    /**
     * @param resource $stream
     *
     * @throws FtpException
     */
    public function fput(string $remote_filename, $stream, int $mode = FTP_BINARY, int $offset = 0): void
    {
        Assert::notNull($this->connection);

        ftp_fput($this->connection, $remote_filename, $stream, $mode, $offset);
    }

    /**
     * @throws FtpException
     */
    public function get(string $local_filename, string $remote_filename, int $mode = FTP_BINARY, int $offset = 0): void
    {
        Assert::notNull($this->connection);

        ftp_get($this->connection, $local_filename, $remote_filename, $mode, $offset);
    }

    /**
     * @throws FtpException
     */
    public function login(string $username, string $password): void
    {
        Assert::notNull($this->connection);

        ftp_login($this->connection, $username, $password);
    }

    /**
     * @throws FtpException
     */
    public function mkdir(string $directory): string
    {
        Assert::notNull($this->connection);

        return ftp_mkdir($this->connection, $directory);
    }

    /**
     * @return array<string, string>[]
     *
     * @throws FtpException
     */
    public function mlsd(string $directory): array
    {
        Assert::notNull($this->connection);

        return ftp_mlsd($this->connection, $directory);
    }

    /**
     * @throws FtpException
     */
    public function nb_put(string $remote_filename, string $local_filename, int $mode = FTP_BINARY, int $offset = 0): int
    {
        Assert::notNull($this->connection);

        return ftp_nb_put($this->connection, $remote_filename, $local_filename, $mode = FTP_BINARY, $offset = 0);
    }

    /**
     * @return string[]
     *
     * @throws FtpException
     */
    public function nlist(string $directory): array
    {
        Assert::notNull($this->connection);

        return ftp_nlist($this->connection, $directory);
    }

    /**
     * @throws FtpException
     */
    public function pasv(bool $enable): void
    {
        Assert::notNull($this->connection);

        ftp_pasv($this->connection, $enable);
    }

    /**
     * @throws FtpException
     */
    public function put(string $remote_filename, string $local_filename, int $mode = FTP_BINARY, int $offset = 0): void
    {
        Assert::notNull($this->connection);

        ftp_put($this->connection, $remote_filename, $local_filename, $mode, $offset);
    }

    /**
     * @throws FtpException
     */
    public function pwd(): string
    {
        Assert::notNull($this->connection);

        return ftp_pwd($this->connection);
    }

    /**
     * @return string[]
     *
     * @throws FtpException
     */
    public function raw(string $command): array
    {
        Assert::notNull($this->connection);

        return ftp_raw($this->connection, $command);
    }

    /**
     * @throws FtpException
     */
    public function rename(string $from, string $to): void
    {
        Assert::notNull($this->connection);

        ftp_rename($this->connection, $from, $to);
    }

    /**
     * @throws FtpException
     */
    public function rmdir(string $directory): void
    {
        Assert::notNull($this->connection);

        ftp_rmdir($this->connection, $directory);
    }

    /**
     * @throws FtpException
     */
    public function site(string $command): void
    {
        Assert::notNull($this->connection);

        ftp_site($this->connection, $command);
    }

    /**
     * @throws FtpException
     */
    public function systype(): string
    {
        Assert::notNull($this->connection);

        return ftp_systype($this->connection);
    }

    public function __destruct()
    {
        if (isset($this->connection)) {
            return;
        }

        try {
            $this->close();
        } catch (FtpException) {
        }
        $this->connection = null;
    }
}

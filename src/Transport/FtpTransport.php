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
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\FtpException;
use Safe\Exceptions\StreamException;
use Webmozart\Assert\Assert;

use function Safe\fopen;
use function Safe\ftp_close;
use function Safe\ftp_fget;
use function Safe\ftp_fput;
use function Safe\ftp_nlist;
use function Safe\fwrite;
use function Safe\stream_get_contents;

class FtpTransport implements TransportInterface
{
    /**
     * @var resource|null
     */
    private $connection;

    private string $inputDir;

    private string $outputDir;

    /**
     * @param resource $connection
     */
    public function __construct(
        $connection,
        string $inputDir,
        string $outputDir,
    ) {
        if (!extension_loaded('ftp')) {
            throw new \RuntimeException('You must have the FTP extension for PHP, please enable it in the php.ini file');
        }

        $this->connection = $connection;
        $this->inputDir = $this->normalizeDirPath($inputDir);
        $this->outputDir = $this->normalizeDirPath($outputDir);
    }

    public function getFileNames(): array
    {
        Assert::notNull($this->connection);
        try {
            $files = [];
            foreach (ftp_nlist($this->connection, $this->inputDir) as $file) {
                $file = $this->normalizeFilePath($file);
                if (in_array($file, ['.', '..'])) {
                    continue;
                }

                $files[] = $file;
            }

            return $files;
        } catch (FtpException $e) {
            throw new TransportException("Could not list files from folder $this->inputDir", 0, $e);
        }
    }

    public function getFileContents(string $filename): string
    {
        Assert::notNull($this->connection);
        try {
            $stream = fopen('php://memory', 'rb+');

            ftp_fget($this->connection, $stream, $this->inputDir . $filename);
            fseek($stream, 0);

            return stream_get_contents($stream);
        } catch (FtpException|FilesystemException|StreamException $e) {
            throw new TransportException("Could not read file $filename", 0, $e);
        }
    }

    public function putFileContents(string $filename, string $data): void
    {
        Assert::notNull($this->connection);
        try {
            $stream = fopen('php://memory', 'rb+');

            fwrite($stream, $data);
            fseek($stream, 0);

            ftp_fput($this->connection, $this->outputDir . $filename, $stream);
        } catch (FtpException|FilesystemException $e) {
            throw new TransportException("Could not write the file $filename in the folder $this->outputDir, check if exists", 0, $e);
        }
    }

    public function __destruct()
    {
        if (!$this->connection) {
            return;
        }

        try {
            ftp_close($this->connection);

            $this->connection = null;
        } catch (FtpException) {
        }
    }

    private function normalizeDirPath(string $dirPath): string
    {
        return rtrim($dirPath, '/\\') . '/';
    }

    private function normalizeFilePath(string $filePath): string
    {
        return str_replace($this->inputDir, '', $filePath);
    }
}

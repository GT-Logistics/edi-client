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
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Gtlogistics\EdiClient\Util\PathUtils;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\FtpException;
use Safe\Exceptions\StreamException;

use function Safe\fopen;
use function Safe\fwrite;
use function Safe\stream_get_contents;

class FtpTransport implements TransportInterface
{
    private FtpConnection $connection;

    private string $inputDir;

    private string $outputDir;

    public function __construct(
        FtpConnection $connection,
        string $inputDir,
        string $outputDir,
    ) {
        $this->connection = $connection;
        $this->inputDir = PathUtils::normalizeDirPath($inputDir);
        $this->outputDir = PathUtils::normalizeDirPath($outputDir);
    }

    public function getFileNames(): array
    {
        try {
            $files = [];
            foreach ($this->connection->nlist($this->inputDir) as $file) {
                $file = PathUtils::normalizeFilePath($this->inputDir, $file);
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
        try {
            $stream = fopen('php://memory', 'rb+');

            $this->connection->fget($stream, $this->inputDir . $filename);
            fseek($stream, 0);

            return stream_get_contents($stream);
        } catch (FtpException|FilesystemException|StreamException $e) {
            throw new TransportException("Could not read file $filename", 0, $e);
        }
    }

    public function putFileContents(string $filename, string $data): void
    {
        try {
            $stream = fopen('php://memory', 'rb+');

            fwrite($stream, $data);
            fseek($stream, 0);

            $this->connection->fput($this->outputDir . $filename, $stream);
        } catch (FtpException|FilesystemException $e) {
            throw new TransportException("Could not write the file $filename in the folder $this->outputDir, check if exists", 0, $e);
        }
    }
}

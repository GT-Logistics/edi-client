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

namespace Gtlogistics\EdiClient\Transport\Sftp;

use Gtlogistics\EdiClient\Exception\TransportException;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Gtlogistics\EdiClient\Utils\CustomAssert;
use Gtlogistics\EdiClient\Utils\PathUtils;
use Safe\Exceptions\DirException;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\Ssh2Exception;
use Webmozart\Assert\Assert;

use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\opendir;
use function Safe\ssh2_disconnect;
use function Safe\ssh2_sftp;

class SftpTransport implements TransportInterface
{
    /**
     * @var resource|null
     */
    private $sshConnection;

    /**
     * @var resource|null
     */
    private $sftpConnection;

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
        CustomAssert::ssh2Resource($connection);

        $this->sshConnection = $connection;
        $this->sftpConnection = ssh2_sftp($connection);
        $this->inputDir = PathUtils::normalizeDirPath($inputDir);
        $this->outputDir = PathUtils::normalizeDirPath($outputDir);
    }

    public function getFileNames(): array
    {
        Assert::notNull($this->sshConnection);
        Assert::notNull($this->sftpConnection);
        try {
            $files = [];

            $directory = opendir("ssh2.sftp://$this->sftpConnection/$this->inputDir");
            while (($file = readdir($directory)) !== false) {
                $file = PathUtils::normalizeFilePath($this->inputDir, $file);
                if (in_array($file, ['.', '..'])) {
                    continue;
                }

                $files[] = $file;
            }
            closedir($directory);

            return $files;
        } catch (DirException $e) {
            throw new TransportException("Could not list files from folder $this->inputDir", 0, $e);
        }
    }

    public function getFileContents(string $filename): string
    {
        Assert::notNull($this->sshConnection);
        Assert::notNull($this->sftpConnection);
        try {
            return file_get_contents("ssh2.sftp://$this->sftpConnection/$filename");
        } catch (FilesystemException $e) {
            throw new TransportException("Could not read file $filename", 0, $e);
        }
    }

    public function putFileContents(string $filename, string $data): void
    {
        Assert::notNull($this->sshConnection);
        Assert::notNull($this->sftpConnection);
        try {
            file_put_contents("ssh2.sftp://$this->sftpConnection/$filename", $data);
        } catch (FilesystemException $e) {
            throw new TransportException("Could not write the file $filename in the folder $this->outputDir, check if exists", 0, $e);
        }
    }

    public function __destruct()
    {
        if ($this->sftpConnection !== null) {
            try {
                ssh2_disconnect($this->sftpConnection);
            } catch (Ssh2Exception) {
            }

            $this->sftpConnection = null;
        }
        if ($this->sshConnection !== null) {
            try {
                ssh2_disconnect($this->sshConnection);
            } catch (Ssh2Exception) {
            }

            $this->sshConnection = null;
        }
    }
}

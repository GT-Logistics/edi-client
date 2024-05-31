<?php

namespace Gtlogistics\EdiClient\Transport;

use Gtlogistics\EdiClient\Exception\TransportException;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\FtpException;
use Safe\Exceptions\StreamException;

use function \Safe\sprintf;
use function \Safe\fopen;
use function \Safe\fwrite;
use function \Safe\ftp_nlist;
use function \Safe\ftp_fget;
use function \Safe\ftp_chdir;
use function \Safe\ftp_fput;
use function \Safe\ftp_close;
use function \Safe\stream_get_contents;

class FtpTransport implements TransportInterface
{
    /**
     * @var resource
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
        string $outputDir
    ) {
        if (!extension_loaded('ftp')) {
            throw new \RuntimeException('You must have the FTP extension for PHP, please enable it in the php.ini file');
        }

        $this->connection = $connection;
        $this->inputDir = $inputDir;
        $this->outputDir = $outputDir;
    }

    public function getFiles(): array
    {
        try {
            $filenames = ftp_nlist($this->connection, $this->inputDir);

            return array_map(
                fn(string $filename) => new FtpFile($this, $this->inputDir . '/' . $filename),
                $filenames
            );
        } catch (FtpException $e) {
            throw new TransportException(sprintf('Could not list files from folder %s', $this->inputDir), 0, $e);
        }
    }


    /**
     * @internal
     */
    public function getFileContent(string $filename): string
    {
        try {
            $stream = fopen('php://memory', 'rb+');

            ftp_fget($this->connection, $stream, $filename);
            fseek($stream, 0);

            return stream_get_contents($stream);
        } catch (FtpException|FilesystemException|StreamException $e) {
            throw new TransportException(sprintf('Could not read file %s', $filename), 0, $e);
        }
    }

    public function writeFile(FileInterface $file): void
    {
        try {
            ftp_chdir($this->connection, $this->outputDir);

            $stream = fopen('php://memory', 'rb+');
            fwrite($stream, $file->getContent());
            fseek($stream, 0);

            ftp_fput($this->connection, $this->outputDir . '/' . $file->getName(), $stream);
        } catch (FtpException|FilesystemException $e) {
            throw new TransportException(sprintf('Could not write the file %s in the folder %s, check if exists', $file->getName(), $this->outputDir), 0, $e);
        }
    }

    public function __destruct()
    {
        try {
            ftp_close($this->connection);
        } catch (FtpException $e) {
        }
    }
}

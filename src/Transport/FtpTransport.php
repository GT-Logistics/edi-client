<?php

namespace Gtlogistics\EdiClient\Transport;

use FTP\Connection;
use Gtlogistics\EdiClient\Exception\TransportException;

class FtpTransport implements TransportInterface
{
    /**
     * @var Connection
     */
    private $connection;

    private string $inputDir;

    private string $outputDir;

    /**
     * @param Connection $connection
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

    /**
     * @throws TransportException
     */
    public static function build(
        string $host,
        int $port,
        string $username,
        string $password,
        string $inputDir,
        string $outputDir,
        bool $useSsl = false
    ): self {
        $connection = !$useSsl ? ftp_connect($host, $port) : ftp_ssl_connect($host, $port);

        if (!$connection) {
            throw new TransportException(sprintf('Could not connect to FTP server in %s:%s', $host, $port));
        }

        if(!ftp_pasv($connection, true)) {
            throw new TransportException(sprintf('The FTP server at %s:%s could not support PASSIVE mode', $host, $port));
        }

        if(!@ftp_login($connection, $username, $password)) {
            throw new TransportException(sprintf('Could not login to FTP server in %s:%s', $host, $port));
        }

        return new self(
            $connection,
            $inputDir,
            $outputDir,
        );
    }

    public function getFiles(): array
    {
        if(($filenames = ftp_nlist($this->connection, $this->inputDir)) === false) {
            throw new TransportException(sprintf('Could not list files from folder %s', $this->inputDir));
        }

        return array_map(
            fn(string $filename) => new FtpFile($this, $this->inputDir . '/' . $filename),
            $filenames
        );
    }


    /**
     * @internal
     */
    public function getFileContent(string $filename): string
    {
        /** @var resource $stream */
        $stream = fopen('php://memory', 'rb+');
        ftp_fget($this->connection, $stream, $filename);
        fseek($stream, 0);

        if (($data = stream_get_contents($stream)) === false) {
            throw new TransportException(sprintf('Could not read file %s', $filename));
        }

        return $data;
    }

    public function writeFile(FileInterface $file): void
    {
        if(@ftp_chdir($this->connection, $this->outputDir)) {
            throw new TransportException(sprintf('Could not change to the folder %s, check if exists', $this->outputDir));
        }

        /** @var resource $stream */
        $stream = fopen('php://memory', 'rb+');
        fwrite($stream, $file->getContent());
        fseek($stream, 0);

        if (!ftp_fput($this->connection, $this->outputDir . '/' . $file->getName(), $stream)) {
            throw new TransportException(sprintf('Could not write the file %s in the folder %s, check if exists', $file->getName(), $this->outputDir));
        }
    }

    public function __destruct()
    {
        ftp_close($this->connection);
    }
}

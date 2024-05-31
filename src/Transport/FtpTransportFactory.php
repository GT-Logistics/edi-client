<?php

namespace Gtlogistics\EdiClient\Transport;

use Gtlogistics\EdiClient\Exception\TransportException;
use Safe\Exceptions\FtpException;

use function \Safe\ftp_connect;
use function \Safe\ftp_ssl_connect;
use function \Safe\ftp_pasv;
use function \Safe\ftp_login;

class FtpTransportFactory
{
    public function build(
        string $host,
        int $port,
        string $username,
        string $password,
        string $inputDir,
        string $outputDir,
        bool $useSsl = false
    ): FtpTransport {
        if (!extension_loaded('ftp')) {
            throw new \RuntimeException('You must have the FTP extension for PHP, please enable it in the php.ini file');
        }

        try {
            $connection = !$useSsl ? ftp_connect($host, $port) : ftp_ssl_connect($host, $port);

            ftp_pasv($connection, true);
            ftp_login($connection, $username, $password);

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

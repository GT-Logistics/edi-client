<?php

namespace Gtlogistics\EdiClient\Transport;

class FtpFile implements FileInterface
{
    private FtpTransport $transport;

    private string $filename;

    public function __construct(FtpTransport $transport, string $filename)
    {
        $this->transport = $transport;
        $this->filename = $filename;
    }

    public function getName(): string
    {
        return basename($this->filename);
    }

    public function getContentType(): string
    {
        return 'application/octet-stream';
    }

    public function getContent(): string
    {
        return $this->transport->getFileContent($this->filename);
    }
}

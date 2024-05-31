<?php

namespace Gtlogistics\EdiClient\Transport;

use Gtlogistics\EdiClient\Exception\TransportException;

interface TransportInterface
{
    /**
     * @return string[]
     *
     * @throws TransportException
     */
    public function getFileNames(): array;

    /**
     * @throws TransportException
     */
    public function getFileContents(string $filename): string;

    /**
     * @throws TransportException
     */
    public function putFileContents(string $filename, string $data): void;
}

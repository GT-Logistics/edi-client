<?php

namespace Gtlogistics\EdiClient\Transport;

interface TransportInterface
{
    /**
     * @return FileInterface[]
     */
    public function getFiles(): array;

    public function writeFile(FileInterface $file): void;
}

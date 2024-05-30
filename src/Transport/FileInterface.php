<?php

namespace Gtlogistics\EdiClient\Transport;

interface FileInterface
{
    public function getName(): string;

    public function getContentType(): string;

    public function getContent(): string;
}

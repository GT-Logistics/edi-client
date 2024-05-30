<?php

namespace Gtlogistics\EdiClient\Serializer;

use Gtlogistics\EdiClient\Model\EdiInterface;

interface SerializerInterface
{
    public function serialize(EdiInterface $edi): string;

    public function deserialize(string $content): EdiInterface;
}

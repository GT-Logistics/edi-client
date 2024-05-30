<?php

namespace Gtlogistics\EdiClient;

use Gtlogistics\EdiClient\Model\EdiInterface;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Transport\TransportInterface;

final class EdiClient
{
    private TransportInterface $transport;

    private SerializerInterface $serializer;

    public function __construct(
        TransportInterface $transport,
        SerializerInterface $serializer
    ) {
        $this->transport = $transport;
        $this->serializer = $serializer;
    }

    public function send(EdiInterface $edi)
    {
        $this->transport->writeFile();
    }
}

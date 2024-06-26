<?php

namespace Gtlogistics\EdiClient;

use Gtlogistics\EdiClient\Model\EdiInterface;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Transport\TransportInterface;

/**
 * @template T
 */
final class EdiClient
{
    /**
     * @param SerializerInterface<T> $serializer
     */
    public function __construct(
        private readonly TransportInterface $transport,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @param T $edi
     */
    public function send(string $filename, mixed $edi): void
    {
        $this->transport->putFileContents($filename, $this->serializer->serialize($edi));
    }

    /**
     * @return T
     */
    public function receive(string $filename): mixed
    {
        return $this->serializer->deserialize($this->transport->getFileContents($filename));
    }

    /**
     * @return string[]
     */
    public function files(): array
    {
        return $this->transport->getFileNames();
    }
}

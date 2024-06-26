<?php

namespace Gtlogistics\EdiClient\Serializer;

/**
 * @template T
 */
interface SerializerInterface
{
    /**
     * @param T $edi
     */
    public function serialize(mixed $edi): string;

    /**
     * @return T
     */
    public function deserialize(string $content): mixed;
}

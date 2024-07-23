<?php

declare(strict_types=1);

/*
 * Copyright (C) 2024 GT+ Logistics.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301
 * USA
 */

namespace Gtlogistics\EdiClient;

use Gtlogistics\EdiClient\Serializer\AnsiX12Serializer;
use Gtlogistics\EdiClient\Serializer\NullSerializer;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Transport\NullTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Gtlogistics\EdiX12\Edi;
use Gtlogistics\EdiX12\Model\ReleaseInterface;

/**
 * @template T
 */
final class EdiClientFactory
{
    private TransportInterface $transport;

    /**
     * @var SerializerInterface<T>
     */
    private SerializerInterface $serializer;

    public function __construct()
    {
        $this->transport = new NullTransport();
        $this->serializer = new NullSerializer();
    }

    /**
     * @return EdiClientFactory<T>
     */
    public function withNullTransport(): self
    {
        return $this->withTransport(new NullTransport());
    }

    /**
     * @return EdiClientFactory<T>
     */
    public function withTransport(TransportInterface $transport): self
    {
        $cloned = clone $this;
        $cloned->transport = $transport;

        return $cloned;
    }

    /**
     * @return EdiClientFactory<mixed>
     */
    public function withNullSerializer(): self
    {
        return $this->withSerializer(new NullSerializer());
    }

    /**
     * @param ReleaseInterface[] $releases
     *
     * @return EdiClientFactory<Edi>
     */
    public function withX12Serializer(
        array $releases,
        string $elementDelimiter,
        string $segmentDelimiter,
    ): self {
        return $this->withSerializer(new AnsiX12Serializer($releases, $elementDelimiter, $segmentDelimiter));
    }

    /**
     * @template TSerialize
     *
     * @param SerializerInterface<TSerialize> $serializer
     *
     * @return EdiClientFactory<TSerialize>
     */
    public function withSerializer(SerializerInterface $serializer): self
    {
        /** @var EdiClientFactory<TSerialize> $cloned */
        $cloned = clone $this;
        $cloned->serializer = $serializer;

        return $cloned;
    }

    /**
     * @return EdiClient<T>
     */
    public function build(): EdiClient
    {
        return new EdiClient($this->transport, $this->serializer);
    }
}

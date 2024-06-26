<?php
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

declare(strict_types=1);

namespace Gtlogistics\EdiClient;

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

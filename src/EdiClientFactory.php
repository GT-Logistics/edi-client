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
use Gtlogistics\EdiClient\Transport\FtpTransportFactory;
use Gtlogistics\EdiClient\Transport\NullTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Gtlogistics\EdiX12\Model\ReleaseInterface;

final class EdiClientFactory
{
    private TransportInterface $transport;

    private SerializerInterface $serializer;

    public function __construct()
    {
        $this
            ->withNullTransport()
            ->withNullSerializer()
        ;
    }

    public function withNullTransport(): self
    {
        $this->transport = new NullTransport();

        return $this;
    }

    public function withFtpTransport(
        string $host,
        int $port,
        string $username,
        string $password,
        string $inputDir,
        string $outputDir,
        bool $useSsl = false,
    ): self {
        $this->transport = (new FtpTransportFactory())->build($host, $port, $username, $password, $inputDir, $outputDir, $useSsl);

        return $this;
    }

    public function withNullSerializer(): self
    {
        $this->serializer = new NullSerializer();

        return $this;
    }

    /**
     * @param ReleaseInterface[] $releases
     */
    public function withX12Serializer(
        array $releases,
        string $elementDelimiter,
        string $segmentDelimiter,
    ): self {
        $this->serializer = new AnsiX12Serializer($releases, $elementDelimiter, $segmentDelimiter);

        return $this;
    }

    public function build(): EdiClient
    {
        return new EdiClient($this->transport, $this->serializer);
    }
}

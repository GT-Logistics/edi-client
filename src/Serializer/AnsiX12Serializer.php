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

namespace Gtlogistics\EdiClient\Serializer;

use Gtlogistics\EdiX12\Edi;
use Gtlogistics\EdiX12\Model\ReleaseInterface;
use Gtlogistics\EdiX12\Parser\X12Parser;
use Gtlogistics\EdiX12\Serializer\X12Serializer;
use Webmozart\Assert\Assert;

/**
 * @implements SerializerInterface<Edi>
 */
class AnsiX12Serializer implements SerializerInterface
{
    private X12Serializer $serializer;

    private X12Parser $parser;

    /**
     * @param ReleaseInterface[] $releases
     */
    public function __construct(
        array $releases,
        string $elementDelimiter,
        string $segmentDelimiter,
    ) {
        if (!class_exists(X12Serializer::class)) {
            throw new \RuntimeException('Can not detect an X12 Serializer, please execute "composer require gtlogistics/edi-x12"');
        }

        $this->serializer = new X12Serializer($elementDelimiter, $segmentDelimiter);
        $this->parser = new X12Parser($releases);
    }

    public function serialize(mixed $edi): string
    {
        Assert::isInstanceOf($edi, Edi::class);

        return $this->serializer->serialize($edi);
    }

    public function deserialize(string $content): mixed
    {
        return $this->parser->parse($content);
    }
}

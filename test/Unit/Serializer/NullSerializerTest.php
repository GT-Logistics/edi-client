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

namespace Gtlogistics\EdiClient\Test\Unit\Serializer;

use Gtlogistics\EdiClient\Serializer\NullSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullSerializer::class)]
class NullSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $serializer = new NullSerializer();
        $serializedData = $serializer->serialize(new \stdClass());

        $this->assertSame('', $serializedData);
    }

    public function testDeserialize(): void
    {
        $serializer = new NullSerializer();
        $deserializedData = $serializer->deserialize('test');

        $this->assertNull($deserializedData);
    }
}

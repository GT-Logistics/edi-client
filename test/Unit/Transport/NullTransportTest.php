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

namespace Gtlogistics\EdiClient\Test\Unit\Transport;

use Gtlogistics\EdiClient\Transport\NullTransport;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullTransport::class)]
class NullTransportTest extends TestCase
{
    public function testGetFilenames(): void
    {
        $transport = new NullTransport();
        $fileNames = $transport->getFileNames();

        $this->assertCount(0, $fileNames);
    }

    public function testGetFileContents(): void
    {
        $transport = new NullTransport();
        $contents = $transport->getFileContents('test.edi');

        $this->assertSame('', $contents);
    }

    public function testPutFileContents(): void
    {
        $this->expectNotToPerformAssertions();

        $transport = new NullTransport();
        $transport->putFileContents('test.edi', 'test');
    }
}

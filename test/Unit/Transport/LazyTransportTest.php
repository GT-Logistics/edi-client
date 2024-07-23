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

use Gtlogistics\EdiClient\Transport\LazyTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LazyTransport::class)]
class LazyTransportTest extends TestCase
{
    public function testOnlyOneInvocation(): void
    {
        $mockTransport = $this->createMock(TransportInterface::class);
        $mockTransport->expects($this->exactly(3))
            ->method('putFileContents')
        ;

        $mockFactory = $this->createMock(StubFactory::class);
        $mockFactory->expects($this->once())
            ->method('__invoke')
            ->willReturn($mockTransport)
        ;

        $transport = new LazyTransport($mockFactory(...));
        $transport->putFileContents('test1.edi', 'Test');
        $transport->putFileContents('test2.edi', 'Test');
        $transport->putFileContents('test3.edi', 'Test');
    }

    public function testGetFileNames(): void
    {
        $mockTransport = $this->createMock(TransportInterface::class);
        $mockTransport->expects($this->once())
            ->method('getFileNames')
            ->willReturn(['test1.edi', 'test2.edi'])
        ;

        $transport = new LazyTransport(static fn () => $mockTransport);
        $fileNames = $transport->getFileNames();

        $this->assertCount(2, $fileNames);
        $this->assertEquals('test1.edi', $fileNames[0]);
        $this->assertEquals('test2.edi', $fileNames[1]);
    }

    public function testGetFileContents(): void
    {
        $mockTransport = $this->createMock(TransportInterface::class);
        $mockTransport->expects($this->once())
            ->method('getFileContents')
            ->with('test1.edi')
            ->willReturn('Test')
        ;

        $transport = new LazyTransport(static fn () => $mockTransport);
        $contents = $transport->getFileContents('test1.edi');

        $this->assertSame('Test', $contents);
    }

    public function testPutFileContents(): void
    {
        $mockTransport = $this->createMock(TransportInterface::class);
        $mockTransport->expects($this->once())
            ->method('putFileContents')
            ->with('test1.edi', 'Test')
        ;

        $transport = new LazyTransport(static fn () => $mockTransport);
        $transport->putFileContents('test1.edi', 'Test');
    }
}

class StubFactory
{
    public function __invoke()
    {
    }
}

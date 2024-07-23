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

namespace Gtlogistics\EdiClient\Test\Unit\Transport\Ftp;

use Gtlogistics\EdiClient\Test\Constraint\IsStreamContentIdentical;
use Gtlogistics\EdiClient\Transport\Ftp\FtpConnection;
use Gtlogistics\EdiClient\Transport\Ftp\FtpTransport;
use Gtlogistics\EdiClient\Util\PathUtils;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;

use function Safe\fwrite;

#[CoversClass(FtpTransport::class)]
#[UsesClass(FtpConnection::class)]
#[UsesClass(PathUtils::class)]
class FtpTransportTest extends TestCase
{
    public function testGetFileNames(): void
    {
        $mock = $this->createMock(FtpConnection::class);
        $mock->expects($this->once())
            ->method('nlist')
            ->willReturn(['.', '..', '/input/test1.edi', '/input/test2.edi', '/input/test3.edi'])
        ;

        $transport = new FtpTransport($mock, '/input', '/output');
        $fileNames = $transport->getFileNames();

        $this->assertCount(3, $fileNames);
        $this->assertSame('test1.edi', $fileNames[0]);
        $this->assertSame('test2.edi', $fileNames[1]);
        $this->assertSame('test3.edi', $fileNames[2]);
    }

    public function testGetFileContents(): void
    {
        $mock = $this->createMock(FtpConnection::class);
        $mock->expects($this->once())
            ->method('fget')
            ->with(new IsType(IsType::TYPE_RESOURCE), '/input/test1.edi')
            ->willReturnCallback(static fn ($stream) => fwrite($stream, 'Test'))
        ;

        $transport = new FtpTransport($mock, '/input', '/output');
        $contents = $transport->getFileContents('test1.edi');

        $this->assertSame('Test', $contents);
    }

    public function testPutFileContents(): void
    {
        $mock = $this->createMock(FtpConnection::class);
        $mock->expects($this->once())
            ->method('fput')
            ->with('/output/test1.edi', new IsStreamContentIdentical('Test'))
        ;

        $transport = new FtpTransport($mock, '/input', '/output');
        $transport->putFileContents('test1.edi', 'Test');
    }
}

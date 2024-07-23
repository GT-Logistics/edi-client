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

use Gtlogistics\EdiClient\Transport\Ftp\FtpConnection;
use Gtlogistics\EdiClient\Transport\Ftp\FtpPasswordAuthenticator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FtpPasswordAuthenticator::class)]
#[UsesClass(FtpConnection::class)]
class FtpPasswordAuthenticatorTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $mock = $this->createMock(FtpConnection::class);
        $mock->expects($this->once())
            ->method('login')
            ->with('test', '1234')
        ;

        $authenticator = new FtpPasswordAuthenticator('test', '1234');
        $authenticator->authenticate($mock);
    }
}

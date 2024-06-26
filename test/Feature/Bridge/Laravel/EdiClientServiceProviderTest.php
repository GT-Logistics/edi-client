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

namespace Gtlogistics\EdiClient\Test\Feature\Bridge\Laravel;

use Gtlogistics\EdiClient\Bridge\Laravel\EdiClientServiceProvider;
use Gtlogistics\EdiClient\EdiClient;
use Gtlogistics\EdiClient\Serializer\AnsiX12Serializer;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Transport\FtpTransport;
use Gtlogistics\EdiClient\Transport\FtpTransportFactory;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(EdiClientServiceProvider::class)]
class EdiClientServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EdiClientServiceProvider::class,
        ];
    }

    public function testDefaultRegister(): void
    {
        $this->mock(FtpTransportFactory::class)
            ->expects('build')->once()
            ->withArgs(['example.org', 2121, 'user', 'pass', '/to', '/from', false])
            ->andReturn($this->mock(FtpTransport::class))
        ;

        $transport = $this->app->make(TransportInterface::class);
        self::assertInstanceOf(FtpTransport::class, $transport);

        $serializer = $this->app->make(SerializerInterface::class);
        self::assertInstanceOf(AnsiX12Serializer::class, $serializer);

        $ediClient = $this->app->make(EdiClient::class);
        self::assertInstanceOf(EdiClient::class, $ediClient);
    }
}

<?php
/*
 * Copyright (c) 2023 GT Logistics.
 */

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
            ->andReturn($this->createMock(FtpTransport::class));

        $transport = $this->app->make(TransportInterface::class);
        self::assertInstanceOf(FtpTransport::class, $transport);

        $serializer = $this->app->make(SerializerInterface::class);
        self::assertInstanceOf(AnsiX12Serializer::class, $serializer);

        $ediClient = $this->app->make(EdiClient::class);
        self::assertInstanceOf(EdiClient::class, $ediClient);
    }
}

<?php
/*
 * Copyright (c) 2023 GT Logistics.
 */

namespace Gtlogistics\EdiClient\Test\Feature\Bridge\Laravel;

use Gtlogistics\EdiClient\Bridge\Laravel\EdiClientServiceProvider;
use Gtlogistics\EdiClient\EdiClient;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Serializer\X12\AnsiX12Serializer;
use Gtlogistics\EdiClient\Transport\FtpTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Orchestra\Testbench\TestCase;

class EdiClientServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EdiClientServiceProvider::class,
        ];
    }

    public function testRegister(): void
    {
        $this->mock(FtpTransport::class);
        $this->mock(AnsiX12Serializer::class);

        self::assertSame('ftp', config('edi.transport'));
        self::assertSame('X12', config('edi.standard'));
        self::assertSame('example.org', config('edi.ftp.host'));
        self::assertSame(2121, config('edi.ftp.port'));
        self::assertSame('user', config('edi.ftp.username'));
        self::assertSame('pass', config('edi.ftp.password'));
        self::assertSame('/to', config('edi.ftp.input_dir'));
        self::assertSame('/from', config('edi.ftp.output_dir'));
        self::assertFalse(config('edi.ftp.ssl'));

        $transport = $this->app->make(TransportInterface::class);
        self::assertInstanceOf(FtpTransport::class, $transport);

        $serializer = $this->app->make(SerializerInterface::class);
        self::assertInstanceOf(AnsiX12Serializer::class, $serializer);

        $ediClient = $this->app->make(EdiClient::class);
        self::assertInstanceOf(EdiClient::class, $ediClient);
    }
}

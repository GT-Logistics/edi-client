<?php
/*
 * Copyright (c) 2023 GT Logistics.
 */

namespace Gtlogistics\EdiClient\Bridge\Laravel;

use Gtlogistics\EdiClient\EdiClient;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Serializer\X12\AnsiX12Serializer;
use Gtlogistics\EdiClient\Transport\FtpTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class EdiClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('extensiv.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'edi');

        // Register transports
        $this->app->singleton(FtpTransport::class, static fn() => FtpTransport::build(
            config('edi.ftp.host'),
            config('edi.ftp.port'),
            config('edi.ftp.username'),
            config('edi.ftp.password'),
            config('edi.ftp.input_dir'),
            config('edi.ftp.output_dir'),
            config('edi.ftp.ssl'),
        ));

        // Register serializers
        $this->app->singleton(AnsiX12Serializer::class);

        // Register services
        $this->app->singleton(TransportInterface::class, static function (Application $app) {
            $transport = config('edi.transport');

            if ($transport === 'ftp') {
                return $app->make(FtpTransport::class);
            }

            throw new \Exception(sprintf('The transport %s is not supported. Supported values are \'ftp\'', $transport));
        });
        $this->app->singleton(SerializerInterface::class, static function (Application $app) {
            $standard = config('edi.standard');

            if ($standard === 'X12') {
                return $app->make(AnsiX12Serializer::class);
            }

            throw new \Exception(sprintf('The standard %s is not supported. Supported values are \'X12\'', $standard));
        });
        $this->app->singleton(EdiClient::class);
    }
}

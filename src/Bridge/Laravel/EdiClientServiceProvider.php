<?php
/*
 * Copyright (c) 2023 GT Logistics.
 */

namespace Gtlogistics\EdiClient\Bridge\Laravel;

use Gtlogistics\EdiClient\EdiClient;
use Gtlogistics\EdiClient\Serializer\AnsiX12Serializer;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Transport\FtpTransport;
use Gtlogistics\EdiClient\Transport\FtpTransportFactory;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class EdiClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('edi.php'),
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
        $this->app->singleton(FtpTransportFactory::class);
        $this->app->singleton(FtpTransport::class, static fn(Application $app) => $app->make(FtpTransportFactory::class)->build(
            config('edi.ftp.host'),
            config('edi.ftp.port'),
            config('edi.ftp.username'),
            config('edi.ftp.password'),
            config('edi.ftp.input_dir'),
            config('edi.ftp.output_dir'),
            config('edi.ftp.ssl'),
        ));

        // Register serializers
        $this->app->singleton(AnsiX12Serializer::class, static fn (Application $app) => new AnsiX12Serializer(
            iterator_to_array($app->tagged('edi.x12.releases')),
            config('edi.x12.element-delimiter'),
            config('edi.x12.segment-delimiter'),
        ));

        // Register services
        $this->app->singleton(TransportInterface::class, static function (Application $app) {
            $transport = config('edi.transport');

            if ($transport === 'ftp') {
                return $app->make(FtpTransport::class);
            }

            throw new \InvalidArgumentException(sprintf('The transport %s is not supported. Supported values are \'ftp\'', $transport));
        });
        $this->app->singleton(SerializerInterface::class, static function (Application $app) {
            $standard = config('edi.standard');

            if ($standard === 'x12') {
                return $app->make(AnsiX12Serializer::class);
            }

            throw new \InvalidArgumentException(sprintf('The standard %s is not supported. Supported values are \'x12\'', $standard));
        });
        $this->app->singleton(EdiClient::class);
    }
}

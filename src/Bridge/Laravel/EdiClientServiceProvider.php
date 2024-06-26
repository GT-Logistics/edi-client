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

namespace Gtlogistics\EdiClient\Bridge\Laravel;

use Gtlogistics\EdiClient\EdiClient;
use Gtlogistics\EdiClient\EdiClientFactory;
use Gtlogistics\EdiClient\Serializer\AnsiX12Serializer;
use Gtlogistics\EdiClient\Serializer\NullSerializer;
use Gtlogistics\EdiClient\Serializer\SerializerInterface;
use Gtlogistics\EdiClient\Transport\FtpTransportFactory;
use Gtlogistics\EdiClient\Transport\NullTransport;
use Gtlogistics\EdiClient\Transport\TransportInterface;
use Gtlogistics\X12Parser\Model\ReleaseInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Webmozart\Assert\Assert;

final class EdiClientServiceProvider extends ServiceProvider
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
        $this->app->singleton('edi.transport.null', NullTransport::class);
        $this->app->singleton('edi.transport.ftp', function (Application $app): TransportInterface {
            $host = config('edi.ftp.host');
            $port = config('edi.ftp.port');
            $username = config('edi.ftp.username');
            $password = config('edi.ftp.password');
            $inputDir = config('edi.ftp.input_dir');
            $outputDir = config('edi.ftp.output_dir');
            $isSsl = config('edi.ftp.ssl');

            Assert::string($host);
            Assert::integer($port);
            Assert::string($username);
            Assert::string($password);
            Assert::string($inputDir);
            Assert::string($outputDir);
            Assert::boolean($isSsl);

            return $app->make(FtpTransportFactory::class)
                ->build($host, $port, $username, $password, $inputDir, $outputDir, $isSsl)
            ;
        });

        // Register serializers
        $this->app->singleton('edi.serializer.null', NullSerializer::class);
        $this->app->singleton('edi.serializer.x12', static function (Application $app): SerializerInterface {
            $releases = iterator_to_array($app->tagged('edi.x12.releases'));
            $elementDelimiter = config('edi.x12.element-delimiter');
            $segmentDelimiter = config('edi.x12.segment-delimiter');

            Assert::allIsInstanceOf($releases, ReleaseInterface::class);
            Assert::string($elementDelimiter);
            Assert::string($segmentDelimiter);

            return new AnsiX12Serializer($releases, $elementDelimiter, $segmentDelimiter);
        });

        // Register services
        $this->app->singleton(TransportInterface::class, static function (Application $app) {
            $transport = config('edi.transport');

            Assert::string($transport);
            Assert::inArray($transport, ['null', 'ftp']);

            return $app->make("edi.transport.$transport");
        });
        $this->app->singleton(SerializerInterface::class, static function (Application $app) {
            $standard = config('edi.standard');

            Assert::string($standard);
            Assert::inArray($standard, ['null', 'x12']);

            return $app->make("edi.serializer.$standard");
        });
        $this->app->singleton(EdiClientFactory::class);
        $this->app->singleton(EdiClient::class);
    }
}

<?php

declare(strict_types=1);

namespace AndyDefer\LaravelFeatures\Tests;

use AndyDefer\LaravelFeatures\LaravelFeaturesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class IntegrationTestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->runMigrations();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelFeaturesServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function runMigrations(): void
    {
        $migrationPaths = [
            // Modules
            __DIR__.'/../src/Modules/Addresses/migrations',
            __DIR__.'/../src/Modules/Comments/migrations',
            __DIR__.'/../src/Modules/Likes/migrations',
            __DIR__.'/../src/Modules/Notifications/migrations',
            __DIR__.'/../src/Modules/Ratings/migrations',
            // Otps
            __DIR__.'/../src/Modules/Otps/migrations',
            // Totps
            __DIR__.'/../src/Modules/Totps/migrations',
            // Fixtures (test models)
            __DIR__.'/Fixtures/database/migrations',
        ];

        foreach ($migrationPaths as $path) {
            if (is_dir($path)) {
                $this->loadMigrationsFrom($path);
            }
        }
    }
}

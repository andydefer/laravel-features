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
        // Charger les migrations du package
        $packageMigrationPath = __DIR__ . '/../src/Addresses/migrations';
        if (is_dir($packageMigrationPath)) {
            $this->loadMigrationsFrom($packageMigrationPath);
        }

        // Charger les migrations des fixtures (pour les modèles de test)
        $fixtureMigrationPath = __DIR__ . '/Fixtures/migrations';
        if (is_dir($fixtureMigrationPath)) {
            $this->loadMigrationsFrom($fixtureMigrationPath);
        }
    }
}

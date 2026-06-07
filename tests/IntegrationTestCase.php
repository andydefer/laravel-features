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
        // Charger les migrations du package Addresses
        $addressesMigrationPath = __DIR__.'/../src/Addresses/migrations';
        if (is_dir($addressesMigrationPath)) {
            $this->loadMigrationsFrom($addressesMigrationPath);
        }

        // Charger les migrations du package Likes
        $likesMigrationPath = __DIR__.'/../src/Likes/migrations';
        if (is_dir($likesMigrationPath)) {
            $this->loadMigrationsFrom($likesMigrationPath);
        }

        // Charger les migrations des fixtures (pour les modèles de test)
        $fixtureMigrationPath = __DIR__.'/Fixtures/migrations';
        if (is_dir($fixtureMigrationPath)) {
            $this->loadMigrationsFrom($fixtureMigrationPath);
        }
    }
}

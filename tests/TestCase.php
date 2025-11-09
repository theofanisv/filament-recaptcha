<?php

namespace Theograms\FilamentRecaptcha\Tests;

use Theograms\FilamentRecaptcha\ReCaptchaServiceProvider;
use Filament\FilamentServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            FilamentServiceProvider::class,
            ReCaptchaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('filament-recaptcha.site_key', 'test_site_key');
        config()->set('filament-recaptcha.secret_key', 'test_secret_key');
    }
}

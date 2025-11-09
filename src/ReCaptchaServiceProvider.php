<?php

namespace Theograms\FilamentRecaptcha;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReCaptchaServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-recaptcha';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('theofanisv/filament-recaptcha');
            });
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('google-recaptcha', 'https://www.google.com/recaptcha/api.js'),
        ], 'theofanisv/'.static::$name);
    }
}

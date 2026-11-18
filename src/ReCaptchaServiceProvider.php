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
            // The field loads the script itself where it is shown (x-load-js in its view), so Filament must not
            // print it on every page of every panel as well.
            Js::make('google-recaptcha', 'https://www.google.com/recaptcha/api.js')->loadedOnRequest(),
        ], 'theofanisv/'.static::$name);
    }
}

<?php

use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;

it('can instantiate component', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component)->toBeInstanceOf(ReCaptcha::class);
});

it('has default theme of light', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component->getTheme())->toBe('light');
});

it('can set theme to dark', function () {
    $component = ReCaptcha::make('recaptcha')
        ->theme('dark');

    expect($component->getTheme())->toBe('dark');
});

it('has default size of normal', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component->getSize())->toBe('normal');
});

it('can set size to compact', function () {
    $component = ReCaptcha::make('recaptcha')
        ->size('compact');

    expect($component->getSize())->toBe('compact');
});

it('retrieves site key from config', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component->getSiteKey())->toBe('test_site_key');
});

it('is required by default', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component->isRequired())->toBeTrue();
});

it('is dehydrated by default', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component->isDehydrated())->toBeTrue();
});

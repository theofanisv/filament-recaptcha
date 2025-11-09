<?php

use Theograms\FilamentRecaptcha\Forms\Components\ReCaptcha;

it('can set invisible mode', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible();

    expect($component->isInvisible())->toBeTrue();
    expect($component->getType())->toBe('invisible');
});

it('can set checkbox mode explicitly', function () {
    $component = ReCaptcha::make('recaptcha')
        ->checkbox();

    expect($component->isInvisible())->toBeFalse();
    expect($component->getType())->toBe('checkbox');
});

it('defaults to checkbox mode', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component->isInvisible())->toBeFalse();
    expect($component->getType())->toBe('checkbox');
});

it('can set badge position for invisible mode', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible()
        ->badge('bottomleft');

    expect($component->getBadge())->toBe('bottomleft');
});

it('has default badge position of bottomright', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible();

    expect($component->getBadge())->toBe('bottomright');
});

it('can set form id for invisible mode', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible()
        ->formId('my-form');

    expect($component->getFormId())->toBe('my-form');
});

it('form id is null by default', function () {
    $component = ReCaptcha::make('recaptcha');

    expect($component->getFormId())->toBeNull();
});

it('can chain invisible mode with other configurations', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible()
        ->badge('inline')
        ->formId('contact-form');

    expect($component->isInvisible())->toBeTrue();
    expect($component->getBadge())->toBe('inline');
    expect($component->getFormId())->toBe('contact-form');
});

it('invisible mode still has site key', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible();

    expect($component->getSiteKey())->toBe('test_site_key');
});

it('invisible mode is still required by default', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible();

    expect($component->isRequired())->toBeTrue();
});

it('invisible mode is still dehydrated by default', function () {
    $component = ReCaptcha::make('recaptcha')
        ->invisible();

    expect($component->isDehydrated())->toBeTrue();
});
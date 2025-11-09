<?php

use Theograms\FilamentRecaptcha\Rules\ReCaptchaRule;
use Illuminate\Support\Facades\Http;

it('passes validation with valid token', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => true,
            'challenge_ts' => now()->toIso8601String(),
            'hostname' => 'localhost',
        ], 200)
    ]);

    $rule = new ReCaptchaRule();
    $passes = $rule->passes('recaptcha', 'valid-token');

    expect($passes)->toBeTrue();
});

it('fails validation with invalid token', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ], 200)
    ]);

    $rule = new ReCaptchaRule();
    $passes = $rule->passes('recaptcha', 'invalid-token');

    expect($passes)->toBeFalse();
});

it('fails validation with empty token', function () {
    $rule = new ReCaptchaRule();
    $passes = $rule->passes('recaptcha', '');

    expect($passes)->toBeFalse();
});

it('fails validation when API request fails', function () {
    Http::fake([
        'www.google.com/recaptcha/api/siteverify' => Http::response([], 500)
    ]);

    $rule = new ReCaptchaRule();
    $passes = $rule->passes('recaptcha', 'some-token');

    expect($passes)->toBeFalse();
});

it('returns appropriate error message', function () {
    $rule = new ReCaptchaRule();
    $rule->passes('recaptcha', '');

    expect($rule->message())->toBeString();
});
